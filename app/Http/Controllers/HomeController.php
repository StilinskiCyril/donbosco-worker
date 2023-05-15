<?php

namespace App\Http\Controllers;

use App\Jobs\SendSms;
use App\Models\Donor;
use App\Models\Pledge;
use App\Rules\ValidateMsisdn;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HomeController extends Controller
{
   // dashboard page
    public function dashboardPage()
    {
        return view('dashboard');
    }

    // load stats
    public function loadStats(Request $request)
    {
        // TODO return results based on roles
        return response()->json([
            'donations_today' => 20000,
            'donations_this_month' => 268040,
            'active_projects' => 2,
            'total_donations' => 14050800,
            'donations_summary' => [76, 85, 101, 98, 87, 105, 91, 114, 94, 45, 87, 36],
            'labels' => ['Total Donations', 'Balance', 'Administration Fees'],
            'series' => [10000, 3000, 5000],
        ]);
    }

    // landing page
    public function landingPage()
    {
        return view('landing');
    }

    // donate page
    public function donatePage()
    {
        return view('donate');
    }

    // donate now
    public function donateNow(Request $request)
    {
        $request->validate([
            'payment_mode' => ['required', 'string', Rule::in(['mpesa', 'paypal', 'card'])],
            'msisdn' => [$request->input('payment_mode') == 'mpesa' ? 'required' : 'nullable', 'string', new ValidateMsisdn(false, true)],
            'amount' => ['required', 'numeric', 'min:5']
        ]);

        if ($request->input('payment_mode') == 'mpesa'){
            Stk
            return response()->json([
                'status' => true,
                'message' => 'Safaricom M-pesa Stk Prompt Sent To '.$request->input('msisdn'). '. Enter M-pesa Pin On Your Phone To Make Your Donation'
            ]);
        } elseif ($request->input('payment_mode' == 'paypal')){

        } else {
            return response()->json([
                'status' => true,
                'message' => 'Card Payment Integration Coming Soon...'
            ]);
        }
    }

    // send sms page
    public function sendSmsPage()
    {
        return view('send-sms');
    }

    public function sendSms(Request $request)
    {
        $request->validate([
            'channel' => ['required', 'string', Rule::in(['specific-user', 'pledged-users', 'all-donors'])],
            'msisdn' => ['nullable', 'string', new ValidateMsisdn(false)],
            'message' => ['required', 'string', 'max:640']
        ]);

        if ($request->input('channel') == 'specific-user'){
            SendSms::dispatch([
                'to' => $request->input('msisdn'),
                'message' => $request->input('message')
            ])->onQueue('send-sms')->onConnection('beanstalkd-worker001');
        } elseif ($request->input('channel') == 'pledged-users'){
            $pledges = Pledge::all();
            foreach ($pledges as $pledge){
                SendSms::dispatch([
                    'to' => $pledge->msisdn,
                    'message' => $request->input('message')
                ])->onQueue('send-sms')->onConnection('beanstalkd-worker001');
            }
        } else {
            $donors = Donor::whereNotNull('msisdn')->get();
            foreach ($donors as $donor){
                SendSms::dispatch([
                    'to' => $donor->msisdn,
                    'message' => $request->input('message')
                ])->onQueue('send-sms')->onConnection('beanstalkd-worker001');
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'SMS Sent Successfully'
        ]);
    }
}
