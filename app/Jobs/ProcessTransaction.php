<?php

namespace App\Jobs;

use App\Models\Account;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\MpesaAccessToken;
use App\Models\PendingMpesaDonation;
use App\Models\Pledge;
use App\Models\UnknownDonation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class ProcessTransaction implements ShouldQueue
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
        $channel = $this->data['channel'];
        if ($channel == 'mpesa'){
            //store the transaction in an unknown transaction table
            PendingMpesaDonation::create([
                'trans_id' => $this->data['content']['TransID'],
                'trans_time' => Carbon::parse($this->data['content']['TransTime']),
                'amount' => $this->data['content']['TransAmount'],
                'msisdn' => $this->data['content']['MSISDN'],
                'name' => $this->data['content']['FirstName'] .' '. $this->data['content']['LastName'],
                'account_no' => $this->data['content']['BillRefNumber'],
                'business_short_code' => $this->data['content']['BusinessShortCode'],
                'ip' => $this->data['ip']
            ]);

            if (env('APP_ENV') == 'production'){
                //perform m-pesa transaction check
                $result = MpesaAccessToken::where('type', 'c2b')->first();
                //m-pesa transaction check
                $ch = curl_init('https://api.safaricom.co.ke/mpesa/transactionstatus/v1/query');
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' .$result->token,
                    'Content-Type: application/json'
                ]);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                    'Initiator' => config('mpesa.c2b_initiator_name'),
                    'SecurityCredential' => getMpesaSecurityCredential(config('mpesa.c2b_password'), false),
//                'SecurityCredential' => config('mpesa.c2b_security_credential'),
                    'CommandID' => 'TransactionStatusQuery',
                    'TransactionID' => $this->data['content']['TransID'],
                    'PartyA' => config('mpesa.business_shortcode'),
                    'IdentifierType' => 4,
                    'ResultURL' => url('api/v1/c2b/transaction-check/callback'),
                    'QueueTimeOutURL' =>  url('api/v1/c2b/transaction-check/timeout'),
                    'Remarks' => 'Transaction Check',
                    'Occassion' => 'Transaction Check'
                ]));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($ch);
                curl_close($ch);
                echo $response;
            } else {
                //simulate m-pesa transaction check for local development testing
                try {
                    $client = new Client();
                    $payload = array (
                        'Result' =>
                            array (
                                'ResultType' => 0,
                                'ResultCode' => 0,
                                'ResultDesc' => 'The service request is processed successfully.',
                                'OriginatorConversationID' => '46626-34535220-1',
                                'ConversationID' => 'AG_20220508_2020104175099c96e910',
                                'TransactionID' => 'QE80000000',
                                'ResultParameters' =>
                                    array (
                                        'ResultParameter' =>
                                            array (
                                                0 =>
                                                    array (
                                                        'Key' => 'DebitPartyName',
                                                        'Value' => '254705799644 - CYRIL AGUVASU',
                                                    ),
                                                1 =>
                                                    array (
                                                        'Key' => 'CreditPartyName',
                                                        'Value' => '4077985 - BITWISE DIGITAL SOLUTIONS LTD 3',
                                                    ),
                                                2 =>
                                                    array (
                                                        'Key' => 'OriginatorConversationID',
                                                    ),
                                                3 =>
                                                    array (
                                                        'Key' => 'InitiatedTime',
                                                        'Value' => 20220508203801,
                                                    ),
                                                4 =>
                                                    array (
                                                        'Key' => 'CreditPartyCharges',
                                                    ),
                                                5 =>
                                                    array (
                                                        'Key' => 'DebitAccountType',
                                                        'Value' => 'MMF Account For Customer',
                                                    ),
                                                6 =>
                                                    array (
                                                        'Key' => 'TransactionReason',
                                                    ),
                                                7 =>
                                                    array (
                                                        'Key' => 'ReasonType',
                                                        'Value' => 'Pay Utility with OD via STK',
                                                    ),
                                                8 =>
                                                    array (
                                                        'Key' => 'TransactionStatus',
                                                        'Value' => 'Completed',
                                                    ),
                                                9 =>
                                                    array (
                                                        'Key' => 'FinalisedTime',
                                                        'Value' => 20220508203801,
                                                    ),
                                                10 =>
                                                    array (
                                                        'Key' => 'Amount',
                                                        'Value' => $this->data['content']['TransAmount'],
                                                    ),
                                                11 =>
                                                    array (
                                                        'Key' => 'ConversationID',
                                                    ),
                                                12 =>
                                                    array (
                                                        'Key' => 'ReceiptNo',
                                                        'Value' => $this->data['content']['TransID'],
                                                    ),
                                            ),
                                    ),
                                'ReferenceData' =>
                                    array (
                                        'ReferenceItem' =>
                                            array (
                                                'Key' => 'Occasion',
                                            ),
                                    ),
                            ),
                    );
                    $response = $client->request('POST', route('mpesa.transaction-check'), [
                        'json' => $payload,
                        'headers' => [
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json',
                        ],
                    ]);
                    $response_data = $response->getBody()->getContents();
                    Log::info('FAKE LOCAL TRANSACTION CHECK CALLBACK '.$response_data);
                    $formatted_response = json_decode($response_data);
                } catch (\Exception $e){
                }
            }
        } elseif ($channel == 'paypal') {
            // process paypal payment
            $trans_id = $this->data['content']['id'];
            $name = $this->data['content']['payer']['name']['given_name'] .' '.$this->data['content']['payer']['name']['surname'];
            $email = $this->data['content']['payer']['email'];
            $account_no = $this->data['content']['purchase_units'][0]['reference_id'];
            $gross_amount = $this->data['content']['purchase_units'][0]['payments']['captures'][0]['seller_receivable_breakdown']['gross_amount']['value'];
            $paypal_fee = $this->data['content']['purchase_units'][0]['payments']['captures'][0]['seller_receivable_breakdown']['paypal_fee']['value'];
            $net_amount = $this->data['content']['purchase_units'][0]['payments']['captures'][0]['seller_receivable_breakdown']['net_amount']['value'];
            $trans_time = $this->data['content']['purchase_units'][0]['payments']['captures'][0]['create_time'];

            // extract the user info
            $donors = Donor::where('email', $email)->first();
            if (!$donors){
                Donor::create([
                    'name' => $name,
                    'email' => $email,
                    'account_no' => $account_no
                ]);
            }

            /**
             * Prioritize Pledge Donations
             */
            $pledge = Pledge::where('account_no', $account_no)->first();
            if ($pledge){
                Donation::create([
                    'channel' => 'paypal',
                    'trans_id' => $trans_id,
                    'trans_time' => $trans_time,
                    'amount' => $gross_amount,
                    'business_short_code' => null,
                    'account_no' => $account_no,
                    'third_party_trans_id' => null,
                    'msisdn' => null,
                    'name' => $name,
                    'ip' => $this->data['ip'],
                    'charges' => $paypal_fee,
                    'net' => $gross_amount - $paypal_fee
                ]);
                $my_total_contributions = Donation::where('account_no', $account_no)
                    ->where('email', $email)->sum('amount');
                $total_account_contributions = Donation::where('account_no', $account_no)->sum('amount');
                //send donor message
                donorMessageResponse($this->data['channel'], $account_no, null, $email, $name, $gross_amount, $my_total_contributions, $total_account_contributions);
                //send treasurer message
                treasurerMessageResponse($this->data['channel'], $account_no, null, $email, $name, $gross_amount, $my_total_contributions, $total_account_contributions);
            } else {
                /**
                 * Account Donations
                 */
                $account = Account::where('account_no', $account_no)->first();
                if ($account){
                    Donation::create([
                        'channel' => 'paypal',
                        'trans_id' => $trans_id,
                        'trans_time' => $trans_time,
                        'amount' => $gross_amount,
                        'business_short_code' => null,
                        'account_no' => $account_no,
                        'third_party_trans_id' => null,
                        'msisdn' => null,
                        'name' => $name,
                        'ip' => $this->data['ip'],
                        'charges' => $paypal_fee,
                        'net' => $gross_amount - $paypal_fee
                    ]);
                    $my_total_contributions = Donation::where('account_no', $account_no)
                        ->where('email', $email)->sum('amount');
                    $total_account_contributions = Donation::where('account_no', $account_no)->sum('amount');
                    //send first time donors an email
                    $donations = Donation::where('email', $email)->count();
                    if ($donations < 2){
                        // TODO send email
                        Log::channel('slack')->info("Thank you {$name} for your donation. If you would like to be a regular donor, click on the link below to register. https://sdb-mssc.donboscoshrine.com");
                    }
                    //send donor message
                    donorMessageResponse($this->data['channel'], $account_no, null, $email, $name, $gross_amount, $my_total_contributions, $total_account_contributions);
                    //send treasurer message
                    treasurerMessageResponse($this->data['channel'], $account_no, null, $email, $name, $gross_amount, $my_total_contributions, $total_account_contributions);
                } else {
                    UnknownDonation::create([
                        'channel' => 'paypal',
                        'trans_id' => $trans_id,
                        'trans_time' => $trans_time,
                        'amount' => $net_amount,
                        'email' => $email,
                        'name' => $name,
                        'account_no' => $account_no
                    ]);
                    // TODO send email
                    Log::channel('slack')->info("$name, your donation of KES $gross_amount was received but it doesn't seem to match any account. A reconciliation will be done shortly.");
                }
            }
        } elseif ($channel == 'card') {
            // process card payment (stripe)
        }
        //TODO etc etc add more payment options
    }
}
