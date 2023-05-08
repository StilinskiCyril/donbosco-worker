<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Donation;
use App\Models\Expense;
use App\Models\MpesaTransaction;
use App\Models\Project;
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
            'start' => ['required', 'date'],
            'end' => ['required', 'date']
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

        $total_collected = Donation::whereBetween('created_at', [$start_date, $end_date])->sum('amount');
        $charges = Donation::whereBetween('created_at', [$start_date, $end_date])->sum('charges');
        $net_collected = $total_collected - $charges;
        $bitwise_revenue_share = (1.5/100) * $net_collected;
        $expenses = Expense::whereBetween('date', [$start_date, $end_date])->sum('amount');
        $net_amount = $net_collected - $bitwise_revenue_share;

        return response()->json([
            'total_collected' => $total_collected,
            'charges' => $charges,
            'net_collected' => $net_collected,
            'bitwise_revenue_share' => $bitwise_revenue_share,
            'expenses' => $expenses,
            'net_amount' => $net_amount,
            'from' => $request->input('start'),
            'to' => $request->input('end')
        ]);
    }
}
