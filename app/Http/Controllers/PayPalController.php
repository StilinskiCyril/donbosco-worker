<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessTransaction;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalController extends Controller
{
    public function successTransactionActivity(Request $request): \Illuminate\Http\RedirectResponse
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            ProcessTransaction::dispatch([
                'channel' => 'paypal',
                'content' => $response,
                'ip' => $request->ip()
            ])->onQueue('process-paypal-transaction')->onConnection('beanstalkd-worker001');

            session()->flash('success', 'Paypal Payment Was Successful');
        } else {
            session()->flash('error', $response['message'] ?? 'Something went wrong.');
        }
        return redirect()->route('home.donate-page');
    }

    public function cancelTransactionActivity(): \Illuminate\Http\RedirectResponse
    {
        session()->flash('error', 'PayPal Payment Was Cancelled');
        return redirect()->route('home.donate-page');
    }
}
