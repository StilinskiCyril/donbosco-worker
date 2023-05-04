<?php

use App\Models\Account;
use App\Models\Project;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

//send donor confirmation message
if (!function_exists('donorMessageResponse')){
    function donorMessageResponse($account_no, $msisdn, $name, $amount, $my_total_contributions, $total_account_contributions){
        $msg_to_donor = '';
        $project_name = '';

        $accounts = Account::where('account_no', $account_no)->get();

        foreach ($accounts as $account){
            $msg_to_donor = $account->message_to_donor;
            $project_name = $account->project->name;
        }

        $search  = array('[1]', '[2]', '[3]', '[7]');
        $replace = array($name, number_format($amount), number_format($my_total_contributions), 'Marian Shrine Spirituality Centre, Don Bosco Upperhill');

        $new_msg =  str_replace($search, $replace, $msg_to_donor);

        \App\Jobs\SendSms::dispatch([
            'to' => $msisdn,
            'message' => $new_msg
        ])->onQueue('send-sms')->onConnection('beanstalkd-worker001');
    }
}

//send treasurer confirmation message
if (!function_exists('treasurerMessageResponse')){
    function treasurerMessageResponse($account_no, $msisdn, $name, $amount, $my_total_contributions, $total_account_contributions){
        $msg_to_treasurer = '';
        $project_name = '';

        $accounts = Account::where('account_no', $account_no)->get();

        foreach ($accounts as $account){
            $msg_to_treasurer = $account->message_to_treasurer;
            $project_name = $account->project->name;
        }

        $search  = array('[1]', '[2]', '[3]', '[5]', '[7]');
        $replace = array($name, number_format($amount), number_format($my_total_contributions), number_format($total_account_contributions), 'Marian Shrine Spirituality Centre, Don Bosco Upperhill');

        $new_msg =  str_replace($search, $replace, $msg_to_treasurer);

        $account = Account::where('account_no', $account_no)->first();
        if ($account){
            $treasurers = $account->treasurers()->get();
            foreach ($treasurers as $treasurer){
                \App\Jobs\SendSms::dispatch([
                    'to' => $treasurer->user->msisdn,
                    'message' => $new_msg
                ])->onQueue('send-sms')->onConnection('beanstalkd-worker001');
            }

        }

        //calculate bitwise income
        $projects =  Project::with(['accounts'])->where('target_date', '>', now())->get();
        $sum = 0;
        foreach ($projects as $project){
            foreach ($project->accounts as $account){
                $amount = \App\Models\Donation::sum('amount');
                $sum += $amount;
            }
        }

        $new_sum = (4.5/100) * $sum;

        Log::channel('slack')->info("$new_msg\n The new bitwise income is $new_sum");
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

//generate random int
function generatePin($digits = 6): string
{
    $i = 0;
    $pin = "";
    while($i < $digits){
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

//get m-pesa security credential
if (!function_exists('getMpesaSecurityCredential')) {
    function getMpesaSecurityCredential($password, $devMode = true)
    {
        if (Cache::has('encrypted_mpesa_security_credential')) {
            return decrypt(Cache::get('encrypted_mpesa_security_credential'));
        }

        $key = file_get_contents(storage_path('mpesa_certificates/prod_cert.cer.txt'));

        if ($devMode){
            $key = file_get_contents(storage_path('mpesa_certificates/sandbox_cert.cer.txt'));
        }

        openssl_public_encrypt($password, $encrypted, $key, OPENSSL_PKCS1_PADDING);

        $encryptedCredentials = base64_encode($encrypted);

        Cache::forever('encrypted_mpesa_security_credential', encrypt($encryptedCredentials));

        return $encryptedCredentials;
    }
}
