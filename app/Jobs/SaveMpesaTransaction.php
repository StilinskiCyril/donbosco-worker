<?php

namespace App\Jobs;

use App\Models\Account;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\PendingMpesaDonation;
use App\Models\UnknownDonation;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Response;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class SaveMpesaTransaction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $ip = $this->data['ip'];

        //TODO whitelist with mpesa ip addresses
        $paymentResultCode = $this->data['content']['Result']['ResultCode'];

        //check if the transaction was successful
        if($paymentResultCode == 0){
            //check if trans id exists in pending mpesa transactions
            $result = PendingMpesaDonation::where('trans_id', $this->data['content']['Result']['ResultParameters']['ResultParameter'][12]['Value'])
                ->where('status', false)->first();

            if ($result){
                $senderDetails = $this->data['content']['Result']['ResultParameters']['ResultParameter'][0]['Value'];

                $name = explode('-', $senderDetails)[1];
                $msisdn = explode('-', $senderDetails)[0];

                /**
                 * TODO format the phone depending on response from m-pesa. Use 254 format
                 */
                $phoneUtil = PhoneNumberUtil::getInstance();
                $kenyaNumberProto = $phoneUtil->parse($msisdn, "KE");
                $isValid = $phoneUtil->isValidNumber($kenyaNumberProto);
                if ($isValid) {
                    $new_msisdn = $phoneUtil->format($kenyaNumberProto, PhoneNumberFormat::E164);
                    $formatted_msisdn = substr($new_msisdn, 1);
                } else {
                    $formatted_msisdn = $msisdn;
                }

                $account_no = 'MSSC';
//                $account_no = $result->account_no;
                $trans_id = $this->data['content']['Result']['ResultParameters']['ResultParameter'][12]['Value'];
                $amount = $this->data['content']['Result']['ResultParameters']['ResultParameter'][10]['Value'];
                $trans_time = Carbon::parse($this->data['content']['Result']['ResultParameters']['ResultParameter'][9]['Value'])->toDateTimeString();
                $business_short_code = $result->business_short_code;
                $third_party_trans_id = $result->third_party_trans_id;
                $account = Account::where('account_no', $account_no)->first();

                //calculate charges and net
                $charges = match ($amount) {
                    $amount >= 101 and $amount <= 999 => 23,
                    $amount >= 1000 and $amount <= 2499 => 34,
                    $amount >= 2500 and $amount <= 4999 => 56,
                    $amount >= 5000 and $amount <= 9999 => 85,
                    $amount >= 10000 and $amount <= 34999 => 112,
                    $amount >= 35000 and $amount <= 49999 => 202,
                    $amount >= 50000 and $amount <= 150000 => 210,
                    default => 0,
                };

                $net = $amount - $charges;

                //check if the account number entered by user matches any account
                if ($account){
                    //save the transaction
                    Donation::create([
                        'channel' => 'mpesa',
                        'trans_id' => $trans_id,
                        'trans_time' => $trans_time,
                        'amount' => $amount,
                        'business_short_code' => $business_short_code,
                        'account_no' => $account_no,
                        'third_party_trans_id' => $third_party_trans_id,
                        'msisdn' => $formatted_msisdn,
                        'name' => $name,
                        'ip' => $ip,
                        'charges' => $charges,
                        'net' => $net
                    ]);
                    //extract the user info
                    $donors = Donor::where('msisdn', $formatted_msisdn)->first();
                    if (!$donors){
                        Donor::create([
                            'name' => $name,
                            'msisdn' => $formatted_msisdn,
                            'account_no' => $account_no
                        ]);
                    }
                    $my_total_contributions = Donation::where('account_no', $account_no)
                        ->where('msisdn', $formatted_msisdn)->sum('amount');
                    $total_account_contributions = Donation::where('account_no', $account_no)->sum('amount');
                    //send first time donors a message
                    $donations = Donation::where('msisdn', $formatted_msisdn)->count();
                    if ($donations < 2){
                        $msg_to_contributor_on_first_deposit = "Thank you {$name} for your donation. If you would like to be a regular donor, click on the link below to register. https://sdb-mssc.donboscoshrine.com";
                        \App\Jobs\SendSms::dispatch([
                            'to' => $formatted_msisdn,
                            'message' => $msg_to_contributor_on_first_deposit
                        ])->onQueue('send-sms')->onConnection('beanstalkd-worker001');
                    }
                    //send donor message
                    donorMessageResponse($account_no, $formatted_msisdn, $name, $amount, $my_total_contributions, $total_account_contributions);
                    //send treasurer message
                    treasurerMessageResponse($account_no, $formatted_msisdn, $name, $amount, $my_total_contributions, $total_account_contributions);
                } else {
                    $result->unknownDonations()->create([
                        'channel' => 'mpesa',
                        'msisdn' => $formatted_msisdn,
                        'amount' => $amount
                    ]);
                    \App\Jobs\SendSms::dispatch([
                        'to' => $formatted_msisdn,
                        'message' => "$name, your donation of KES $amount was received but it doesn't seem to match any account. A reconciliation will be done shortly."
                    ])->onQueue('send-sms')->onConnection('beanstalkd-worker001');
                }
                //update that the transaction has been verified to be from m-pesa
                $result->update([
                    'resolved' => 1
                ]);
            } else {
                Log::error('Transaction ID not found');
            }
        } else {
            Log::error('Transaction validation failed');
        }

        //Responding to the confirmation request
        $response = new Response();
        $response->headers->set("Content-Type","text/xml; charset=utf-8");
        $response->setContent(json_encode(["C2BPaymentConfirmationResult"=>"Success"]));
        return $response;
    }
}
