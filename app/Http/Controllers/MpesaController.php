<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessMpesaReconciliation;
use App\Jobs\ProcessTransaction;
use App\Jobs\SaveMpesaTransaction;
use App\Models\MpesaAccessToken;
use App\Models\UnknownDonation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MpesaController extends Controller
{
    //Transaction Confirmation Url
    public function confirmationUrl(Request $request): \Illuminate\Http\JsonResponse
    {
        $content = $request->all();

        if (env('LOG_MPESA_CALLBACK')) {
            Log::info(json_encode($content));
        }

        ProcessTransaction::dispatch([
            'channel' => 'mpesa',
            'content' => $content,
            'ip' => $request->ip()
        ])->onQueue('process-transaction')->onConnection('beanstalkd-worker001')->delay(now()->addSeconds(3));

        return response()->json([
            'status'=> true,
            'message' => 'C2B Transaction received'
        ]);
    }

    //transaction check callback
    public function transactionCheck(Request $request): \Illuminate\Http\JsonResponse
    {
        $content = $request->all();

        if (env('LOG_MPESA_TRANSACTION_CHECK_CALLBACK')) {
            Log::info(json_encode($content));
        }

        SaveMpesaTransaction::dispatch([
            'content' => $content,
            'ip' => $request->ip()
        ])->onQueue('save-mpesa-transaction')->onConnection('beanstalkd-worker001');

        return response()->json([
            'status'=> true,
            'message' => 'Transaction check callback received'
        ]);
    }

    //transaction check timeout
    public function transactionCheckTimeout(Request $request): \Illuminate\Http\JsonResponse
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
    public function stKPush(Request $request): bool|string
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
            'PartyA' => $request->msisdn,
            'PartyB' => config('mpesa.business_shortcode'),
            'PhoneNumber' =>  $request->msisdn,
            'CallBackURL' => url('api/v1/c2b/stk/transaction/callback'),
            'AccountReference' => $request->account_no,
            'TransactionDesc' => "DonBosco MSSC Payment"
        ];
        $data_string = json_encode($curl_post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        return curl_exec($curl);
    }

    //stk callback url
    public function stkCallback(Request $request): void
    {
        $content = $request->all();
        Log::channel('stklog')->info($content['Body']);
    }

    // Lipa na C2B M-PESA password
    public function lipaNaMpesaC2bPasswordActivity(): string
    {
        $lipa_time = Carbon::rawParse('now')->format('YmdHms');
        $passkey = config('mpesa.c2b_passkey');
        $BusinessShortCode = config('mpesa.business_shortcode');
        $timestamp = $lipa_time;
        return base64_encode($BusinessShortCode.$passkey.$timestamp);
    }


    // manage groups page
    public function managePage(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('unknown-donations');
    }

    // load unknown donations
    public function loadUnknownDonations(Request $request)
    {
        return UnknownDonation::filter($request)->paginate(20);
    }

    // upload m-pesa statement
    public function uploadMpesaStatement(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'uploaded_file' => ['required', 'mimes:xls,xlsx', 'max:2048']
        ]);

        $extension = $request->file('uploaded_file')->extension();
        $file_path = Storage::disk()->putFileAs('mpesa_statements', $request->file('uploaded_file'), time().'.'.$extension, 'public');

        ProcessMpesaReconciliation::dispatch([
            'file_path' => Storage::url($file_path)
        ])->onQueue('process-mpesa-reconciliation')->onConnection('beanstalkd-worker001');

        return response()->json([
            'status' => true,
            'message' => 'M-pesa Statement Uploaded Successfully'
        ]);
    }
}
