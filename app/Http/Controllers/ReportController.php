<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Donation;
use App\Models\Expense;
use App\Models\MpesaTransaction;
use App\Models\Pledge;
use App\Models\Project;
use App\Models\Stat;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // account donations manage page
    public function accountDonationsManagePage()
    {
        return view('account-donations-report');
    }

    // account donations report

    public function accountDonationsReport(Request $request, Project $project)
    {
        return $project->accounts()->filter($request)->get()->map(function($account) {
            $amount_donated = Donation::where('account_no', $account->account_no)->sum('amount');
            $project_target_amount = $account->project->target_amount;
            $account_target_amount = $account->target_amount;
            return [
                'uuid' => $account->uuid,
                'name' => $account->name,
                'account_no' => $account->account_no,
                'target_amount' => number_format($account_target_amount, 2),
                'project_name' => $account->project->name,
                'project_target' => number_format($project_target_amount, 2),
                'amount_donated' => number_format($amount_donated, 2),
                'balance' => number_format($account_target_amount - $amount_donated, 2),
                'donations_against_account_target' => ($amount_donated/$account_target_amount) * 100,
                'donations_against_project_target' => ($amount_donated/$project_target_amount) * 100
            ];
        });
    }

    // project donations manage page
    public function projectDonationsManagePage()
    {
        return view('project-donations-report');
    }

    // project donations report

    public function projectDonationsReport(Request $request)
    {
        return Project::filter($request)->get()->map(function($project) {
            $amount_donated = Donation::sum('amount');
            $project_target_amount = $project->target_amount;
            return [
                'uuid' => $project->uuid,
                'name' => $project->name,
                'target_amount' => number_format($project_target_amount, 2),
                'amount_donated' => number_format($amount_donated, 2),
                'balance' => number_format($project_target_amount - $amount_donated, 2),
                'donations_against_project_target' => ($amount_donated/$project_target_amount) * 100
            ];
        });
    }

    // all donations manage page
    public function allDonationsManagePage()
    {
        return view('all-donations-report');
    }

    // all donations report
    public function allDonationsReport(Request $request)
    {
        $request->validate([
            'project_uuid' => ['nullable', 'string'],
            'account_uuid' => ['nullable', 'string'],
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date']
        ]);
        return Donation::filter($request)->paginate(20);
    }

    // all fund distribution manage page
    public function fundDistributionManagePage()
    {
        return view('fund-distribution-report');
    }

    // fund distribution report
    public function fundDistributionReport(Request $request)
    {
        $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date']
        ]);

        $start_date = Carbon::parse($request->input('start'))->startOfDay();
        $end_date = Carbon::parse($request->input('end'))->endOfDay();

        $total_collected = Stat::whereBetween('created_at', [$start_date, $end_date])->sum('net');
        $charges = Stat::whereBetween('created_at', [$start_date, $end_date])->sum('charges');
        $net_collected = $total_collected - $charges;
        $bitwise_revenue_share = (1.5/100) * $net_collected;
        $expenses = Expense::whereBetween('date', [$start_date, $end_date])->sum('amount');
        $net_amount = $net_collected - $bitwise_revenue_share;

        return response()->json([
            'total_collected' => number_format($total_collected, 2),
            'charges' => number_format($charges, 2),
            'net_collected' => number_format($net_collected, 2),
            'bitwise_revenue_share' => number_format($bitwise_revenue_share, 2),
            'expenses' => number_format($expenses, 2),
            'net_amount' => number_format($net_amount, 2),
            'start' => $request->input('start'),
            'end' => $request->input('end')
        ]);
    }

    // pledge donations manage page
    public function pledgeDonationsManagePage()
    {
        return view('pledge-donations-report');
    }

    // pledge donations report
    public function pledgeDonationsReport(Request $request)
    {
        $request->validate([
            'frequency' => ['nullable', 'numeric'],
            'payment_status' => ['nullable', 'numeric'],
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date']
        ]);

        $pledges = Pledge::filter($request)->paginate(10);
        $pledges->getCollection()->transform(function($pledge) {
            $account_nos = Account::where('account_no', $pledge->account_no)->pluck('id');
            $amount_donated = Stat::whereIn('account_no', $account_nos)->sum('amount');
            $pledge_target_amount = $pledge->target_amount;
            if ($pledge->frequency == 0){
                $frequency = 'Once';
            } elseif ($pledge->frequency == 1){
                $frequency = 'Daily';
            } elseif ($pledge->frequency == 2){
                $frequency = 'Weekly';
            } else {
                $frequency = 'Monthly';
            }
            return [
                'uuid' => $pledge->uuid,
                'name' => $pledge->name,
                'msisdn' => $pledge->msisdn,
                'email' => $pledge->email,
                'target_amount' => number_format($pledge_target_amount, 2),
                'target_date' => $pledge->target_date,
                'frequency' => $frequency,
                'frequency_amount' => number_format($pledge->frequency_amount, 2),
                'last_alert_time' => $pledge->last_alert_time,
                'account_no' => $pledge->account_no,
                'amount_donated' => number_format($amount_donated, 2),
                'balance' => number_format($pledge_target_amount - $amount_donated, 2),
                'payment_status' => $pledge->payment_status
            ];
        });
        return $pledges;
    }

    // pledge donations
    public function pledgeDonations(Pledge $pledge)
    {
        $donations = $pledge->donations()->paginate(20);
        return view('pledge-donations', compact('donations', 'pledge'));
    }
}
