<?php

use App\Models\Account;
use App\Models\Project;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

//send contributor confirmation message
function contributorMessageActivity($BillRefNumber, $MSISDN, $FirstName, $LastName, $TransAmount, $my_total_contributions, $total_account_contributions){
    //compose message to send to user
    $msg_to_contributor = '';
    $project_name = '';

    $accounts = Account::whereAccountNumber($BillRefNumber)->get();

    if (count($accounts) < 1){
        \App\Jobs\SendSms::dispatch([
            'to' => $MSISDN,
            'msg' => "$FirstName $LastName, your donation of $TransAmount was well received and will be addressed to the respective account"
        ])->onQueue('send-sms');
    }

    foreach ($accounts as $account){
        $msg_to_contributor = $account->message_to_contributor;
        $project_name = $account->project->name;
    }

    $search  = array('[1]', '[2]', '[3]', '[7]');
    $replace = array($FirstName .' '. $LastName, number_format($TransAmount), number_format($my_total_contributions), 'Marian Shrine Spirituality Centre, Don Bosco Upperhill');

    $new_msg =  str_replace($search, $replace, $msg_to_contributor);
//    Log::info($new_msg);

    \App\Jobs\SendSms::dispatch([
        'to' => $MSISDN,
        'msg' => $new_msg
    ])->onQueue('send-sms');
}

//send account admin confirmation message
function accountAdminMessageActivity($ReceiptNo, $BillRefNumber, $MSISDN, $FirstName, $LastName, $TransAmount, $my_total_contributions, $total_account_contributions){
    $msg_to_account_admin = '';
    $project_name = '';

    $accounts = Account::whereAccountNumber($BillRefNumber)->get();

    if (count($accounts) < 1){
        \App\Jobs\SendSms::dispatch([
            'to' => config('haba.admin_phone'),
            'msg' => "$FirstName $LastName has donated $TransAmount to an unspecified account number. The m-pesa transaction receipt no is $ReceiptNo"
        ])->onQueue('send-sms');
        exit();
    }

    foreach ($accounts as $account){
        $msg_to_account_admin = $account->message_to_account_admin;
        $project_name = $account->project->name;
    }

    $search  = array('[1]', '[2]', '[3]', '[5]', '[7]');
    $replace = array($FirstName . ' ' . $LastName, number_format($TransAmount), number_format($my_total_contributions), number_format($total_account_contributions), 'Marian Shrine Spirituality Centre, Don Bosco Upperhill');

    $new_msg =  str_replace($search, $replace, $msg_to_account_admin);

    $account_id = Account::whereAccountNumber($BillRefNumber)->value('id');
    $account_admins = \App\Models\AccountAdmin::whereAccountId($account_id)->get();

    if (count($account_admins) < 1){
        $phone = '254705799644';
        \App\Jobs\SendSms::dispatch([
            'to' => $phone,
            'msg' => $new_msg
        ])->onQueue('send-sms');
    }

    //calculate bitwise income
    $projects =  Project::with('accounts')->where('target_date', '>', now())->get();
    $sum = 0;
    foreach ($projects as $project){
        foreach ($project->accounts as $account){
            //$amount = \App\Models\MpesaTransaction::where('BillRefNumber', $account->account_number)->sum('TransAmount');
            $amount = \App\Models\MpesaTransaction::sum('TransAmount');
            $sum += $amount;
        }
    }

    $new_sum = (4.5/100) * $sum;

    Log::channel('slack')->info("$new_msg\n The new bitwise income is $new_sum");

    foreach ($account_admins as $account_admin){
        if ($account_admin->phone == '254700510737'){
            \App\Jobs\SendSms::dispatch([
                'to' => $account_admin->phone,
                'msg' => $new_msg
            ])->onQueue('send-sms');
        }
    }
}

//validate phone
function validatePhone($phone): array
{
    $phoneUtil = PhoneNumberUtil::getInstance();
    try {
        $kenyaNumberProto = $phoneUtil->parse($phone, "KE");
        $isValid = $phoneUtil->isValidNumber($kenyaNumberProto);

        if ($isValid) {
            $phone = $phoneUtil->format($kenyaNumberProto, PhoneNumberFormat::E164);
            return [
                'isValid' => $isValid,
                'msisdn' => substr($phone, 1)
            ];
        }

        return [
            'isValid' => $isValid,
            'phone' => $phone
        ];

    } catch (NumberParseException $e) {
        return [
            'isValid' => false,
            'phone' => $phone
        ];
    }
}

//generate account admin password/pin
function generatePassword($digits = 6): string
{
    $i = 0; //counter
    $pin = ""; //our default pin is blank.
    while($i < $digits){
        //generate a random number between 0 and 9.
        $pin .= mt_rand(0, 9);
        $i++;
    }
    return $pin;
}

//generate mobile sasa token
function mobileSasaToken(){
    try {
        $client = new Client();
        $response = $client->request('POST', 'https://account.mobilesasa.com/oauth/token', [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_secret' => 'd8b5xiBcLwBthixcyfZri9N3waFUhXvthwu65kyo',
                'client_id' => '3c6fa820-7ec9-11eb-bd7a-b9635b94b3ba'
            ],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ]);

        return json_decode($response->getBody()->getContents())->access_token;
    } catch (\Exception $e){

    }
}

//stk push
function stkPush($stkAmount, $stkPhone, $account_number){
    try {
        $client = new Client();
        $response = $client->request('POST', url('api/v1/stk/push'), [
            'form_params' => [
                'amount' => $stkAmount,
                'phone' => $stkPhone,
                'account_number' => $account_number,
            ],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ]);

        Log::channel('stklog')->info($response->getBody());
    } catch (\Exception $e){
        Log::channel('stklog')->info($e->getMessage());
    }
}
