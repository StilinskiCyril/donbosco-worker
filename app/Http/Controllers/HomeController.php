<?php

namespace App\Http\Controllers;

use App\Jobs\MpesaStkPush;
use App\Jobs\SendSms;
use App\Models\Account;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\Pledge;
use App\Models\Project;
use App\Models\Stat;
use App\Models\Treasurer;
use App\Rules\ValidateMsisdn;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

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
        // return results based on roles
        if ($request->user()->hasRole('admin')) {
            $donations_today = Stat::whereDate('created_at', Carbon::today())->sum('amount');
            $donations_this_month = Stat::whereMonth('created_at', Carbon::now()->format('m'))->sum('amount');
            $total_donations = Stat::sum('amount');
            $donations_summary = Stat::selectRaw('SUM(amount) as monthly_sum')
                ->whereYear('created_at', Carbon::now()->format('Y'))
                ->groupBy('created_at')
                ->orderBy('created_at', 'ASC')
                ->pluck('monthly_sum')
                ->toArray();
            $projects_target = Project::sum('target_amount');
            $balance = $projects_target - $total_donations;
            $bitwise_revenue_share = (4.5/100) * $total_donations;
        } elseif($request->user()->hasRole('treasurer')) {
            $account_ids = Treasurer::where('user_id', $request->user()->id)->pluck('account_id');
            $donations_today = Stat::whereDate('created_at', Carbon::today())->whereIn('account_id', $account_ids)->sum('amount');
            $donations_this_month = Stat::whereMonth('created_at', Carbon::now()->format('m'))->whereIn('account_id', $account_ids)->sum('amount');
            $total_donations = Stat::whereIn('account_id', $account_ids)->sum('amount');
            $donations_summary = Stat::selectRaw('SUM(amount) as monthly_sum')
                ->whereYear('created_at', Carbon::now()->format('Y'))
                ->whereIn('account_id', $account_ids)
                ->groupBy('created_at')
                ->orderBy('created_at', 'ASC')
                ->pluck('monthly_sum')
                ->toArray();
            $accounts_target = Account::whereIn('id', $account_ids)->sum('target_amount');
            $balance = $accounts_target - $total_donations;
            $bitwise_revenue_share = (4.5/100) * $total_donations;
        } else{
            $donations_today = Donation::whereDate('created_at', Carbon::today())->where('msisdn', $request->user()->msisdn)->sum('amount');
            $donations_this_month = Donation::whereMonth('created_at', Carbon::now()->format('m'))->where('msisdn', $request->user()->msisdn)->sum('amount');
            $total_donations = Donation::where('msisdn', $request->user()->msisdn)->sum('amount');
            $donations_summary = Donation::selectRaw('SUM(amount) as monthly_sum')
                ->whereYear('created_at', Carbon::now()->format('Y'))
                ->where('msisdn', $request->user()->msisdn)
                ->groupBy('created_at')
                ->orderBy('created_at', 'ASC')
                ->pluck('monthly_sum')
                ->toArray();
            $projects_target = Project::sum('target_amount');
            $balance = $projects_target - $total_donations;
            $bitwise_revenue_share = (4.5/100) * $total_donations;
        }
        return response()->json([
            'donations_today' => number_format($donations_today, 2),
            'donations_this_month' => number_format($donations_this_month, 2),
            'active_projects' => Project::all()->count(),
            'total_donations' => number_format($total_donations, 2),
            'donations_summary' => $donations_summary,
            'labels' => ['Total Donations', 'Balance', 'Bitwise Revenue Share'],
            'series' => [$total_donations, $balance, $bitwise_revenue_share]
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

    /**
     * @throws \Throwable
     */
    public function donateNow(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'payment_mode' => ['required', 'string', Rule::in(['mpesa', 'paypal', 'card'])],
            'target_channel' => ['required', 'string', Rule::in(['other', 'pledge'])],
            'msisdn' => [$request->input('payment_mode') == 'mpesa' ? 'required' : 'nullable', 'string', new ValidateMsisdn(false, true)],
            'account_no' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:5']
        ]);

        if ($request->input('target_channel') == 'pledge'){
            $formatted_account_no = 'PL-'.$request->input('account_no');
        } else {
            $formatted_account_no = $request->input('account_no');
        }

        if ($request->input('payment_mode') == 'mpesa'){
            MpesaStkPush::dispatch([
                'amount' => $request->input('amount'),
                'msisdn' => $request->input('msisdn'),
                'account_no' => $formatted_account_no
            ])->onQueue('mpesa-stk-push')->onConnection('beanstalkd-worker001');

            return response()->json([
                'status' => true,
                'type' => 'text',
                'message' => 'Safaricom M-pesa Stk Prompt Sent To '.$request->input('msisdn'). '. Enter M-pesa Pin On Your Phone To Make Your Donation'
            ]);
        } elseif ($request->input('payment_mode' == 'paypal')){
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();
            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => route('paypal.success-transaction'),
                    "cancel_url" => route('paypal.cancel-transaction'),
                ],
                "purchase_units" => [
                    0 => [
                        "reference_id" => $formatted_account_no,
                        "amount" => [
                            "currency_code" => "KES",
                            "value" => number_format($request->input('amount'), 2)
                        ]
                    ]
                ]
            ]);

            if (isset($response['id']) && $response['id'] != null) {
                // redirect to approve href
                foreach ($response['links'] as $links) {
                    if ($links['rel'] == 'approve') {
                        return response()->json([
                            'status' => true,
                            'type' => 'url',
                            'message' => $links['href']
                        ]);
                    }
                }
                return response()->json([
                    'status' => false,
                    'type' => 'text',
                    'message' => 'Something went wrong...'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'type' => 'text',
                    'message' => $response['message'] ?? 'Something went wrong.'
                ]);
            }
        } else {
            return response()->json([
                'status' => true,
                'type' => 'text',
                'message' => 'Card Payment Integration Coming Soon...'
            ]);
        }
    }

    // send sms page
    public function sendSmsPage(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
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
