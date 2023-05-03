<?php

namespace App\Http\Controllers;

use App\Models\MpesaAccessToken;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    //Transaction Confirmation Url
    public function confirmationUrl(Request $request)
    {
        $content = $request->all();

        if (env('LOG_MPESA_CALLBACK')) {
            Log::info(json_encode($content));
        }

        \App\Jobs\ProcessTransaction::dispatch([
            'channel' => 'mpesa',
            'content' => $content,
            'ip' => $request->ip()
        ])->onQueue('process-transaction')->onConnection('beanstalkd-worker001')->delay(now()->addSeconds(3));

        return response()->json([
            'status'=> true,
            'message' => 'C2b Transaction received'
        ]);
    }

    //transaction check callback
    public function transactionCheck(Request $request)
    {
        $content = $request->all();

        if (env('LOG_MPESA_TRANSACTION_CHECK_CALLBACK')) {
            Log::info(json_encode($content));
        }

        \App\Jobs\SaveMpesaTransaction::dispatch([
            'content' => $content,
            'ip' => $request->ip()
        ])->onQueue('save-mpesa-transaction')->onConnection('beanstalkd-worker001');

        return response()->json([
            'status'=> true,
            'message' => 'Transaction check callback received'
        ]);
    }

    //transaction check timeout
    public function transactionCheckTimeout(Request $request)
    {
        $content = $request->all();

        Log::info(json_encode($content));

        return response()->json([
            'status'=> true,
            'message' => 'Transaction check timeout received'
        ]);
    }

    //J-son Response to M-pesa API feedback - Success or Failure
    public function createValidationResponse($result_code, $result_description): Response
    {
        $result=json_encode(["ResultCode"=>$result_code, "ResultDesc"=>$result_description]);
        $response = new Response();
        $response->headers->set("Content-Type","application/json; charset=utf-8");
        $response->setContent($result);
        return $response;
    }

    //Safaricom will only call your validation if you have requested by writing an official letter to them
    public function mpesaValidation(): Response
    {
        $result_code = "0";
        $result_description = "Accepted validation request.";
        return $this->createValidationResponse($result_code, $result_description);
    }

    // Lipa na C2B M-PESA password
    public function lipaNaMpesaC2bPassword(): string
    {
        $lipa_time = Carbon::rawParse('now')->format('YmdHms');
        $passkey = config('mpesa.c2b_passkey');
        $BusinessShortCode = config('mpesa.business_shortcode');
        $timestamp =$lipa_time;
        return base64_encode($BusinessShortCode.$passkey.$timestamp);
    }

    //initiate stk push
    public function stKPush(Request $request)
    {
        $data = MpesaAccessToken::where('type', 'c2b')->first();
        $url = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$data->token));
        $curl_post_data = [
            'BusinessShortCode' => config('mpesa.business_shortcode'),
            'Password' => $this->lipaNaMpesaC2bPasswordActivity(),
            'Timestamp' => Carbon::rawParse('now')->format('YmdHms'),
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $request->amount,
            'PartyA' => $request->phone,
            'PartyB' => config('mpesa.business_shortcode'),
            'PhoneNumber' =>  $request->phone,
            'CallBackURL' => url('api/v1/c2b/stk/transaction/callback'),
            'AccountReference' => $request->account_number,
            'TransactionDesc' => "Haba Payment"
        ];
        $data_string = json_encode($curl_post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        return curl_exec($curl);
    }

    //stk callback url
    public function stkCallback(Request $request)
    {
        $content = $request->all();
        Log::channel('stklog')->info($content['Body']);
    }
}
