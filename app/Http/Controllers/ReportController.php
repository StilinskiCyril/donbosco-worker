<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Donation;
use App\Models\Project;
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
}
