<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    // manage accounts
    public function managePage(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('accounts');
    }

    // create account
    public function create(Request $request, Project $project)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'account_no' => ['required', 'string', 'unique:accounts'],
            'target_amount' => ['required', 'numeric', 'max:'.$project->target_amount],
            'target_date' => ['required', 'date', 'after:today', 'before:'.$project->target_date],
            'message_to_donor' => ['required', 'string'],
            'message_to_treasurer' => ['required', 'string']
        ]);

        //validate message string
        $message_to_donor = $request->input('message_to_donor');
        $message_to_treasurer = $request->input('message_to_treasurer');

        if (!str_contains($message_to_donor, '[1]') OR !str_contains($message_to_donor, '[2]') OR !str_contains($message_to_donor, '[3]') OR !str_contains($message_to_donor, '[7]')) {
            return response()->json([
                'status' => false,
                'message' => 'Incorrect message syntax for "Message To Donor". Kindly follow instructions on the message template draft'
            ]);
        }

        if (!str_contains($message_to_treasurer, '[1]') OR !str_contains($message_to_treasurer, '[2]') OR !str_contains($message_to_treasurer, '[3]') OR !str_contains($message_to_treasurer, '[5]') OR !str_contains($message_to_treasurer, '[7]')) {
            return response()->json([
                'status' => false,
                'message' => 'Incorrect message syntax for "Message To Treasurer". Kindly follow instructions on the message template draft'
            ]);
        }

        Account::create([
            'name' => $request->input('name'),
            'project_id' => $project->id,
            'description' => $request->input('description'),
            'account_no' => $request->input('account_no'),
            'target_amount' => $request->input('target_amount'),
            'target_date' => $request->input('target_date'),
            'message_to_donor' => $message_to_donor,
            'message_to_treasurer' => $message_to_treasurer
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Account Created Successfully'
        ]);
    }

    // load accounts
    public function load(Request $request)
    {
        return Account::with(['project'])->filter($request)->paginate(20);
    }

    // load accounts without projects
    public function loadWithoutProject(Request $request)
    {
        return Account::filter($request)->paginate(20);
    }

    // update account
    public function update(Request $request, Account $account, Project $project)
    {
        $request->validate([
            'name' => ['required', 'string', Rule::unique('accounts')->ignore($account->uuid, 'uuid')],
            'description' => ['required', 'string'],
            'account_no' => ['required', 'string', Rule::unique('accounts')->ignore($account->uuid, 'uuid')],
            'target_amount' => ['required', 'numeric', 'max:'.$project->target_amount],
            'target_date' => ['required', 'date', 'after:today', 'before:'.$project->target_date],
            'message_to_donor' => ['required', 'string'],
            'message_to_treasurer' => ['required', 'string']
        ]);

        //validate message string
        $message_to_donor = $request->input('message_to_donor');
        $message_to_treasurer = $request->input('message_to_treasurer');

        if (!str_contains($message_to_donor, '[1]') OR !str_contains($message_to_donor, '[2]') OR !str_contains($message_to_donor, '[3]') OR !str_contains($message_to_donor, '[7]')) {
            return response()->json([
                'status' => false,
                'message' => 'Incorrect message syntax for "Message To Donor". Kindly follow instructions on the message template draft'
            ]);
        }

        if (!str_contains($message_to_treasurer, '[1]') OR !str_contains($message_to_treasurer, '[2]') OR !str_contains($message_to_treasurer, '[3]') OR !str_contains($message_to_treasurer, '[5]') OR !str_contains($message_to_treasurer, '[7]')) {
            return response()->json([
                'status' => false,
                'message' => 'Incorrect message syntax for "Message To Treasurer". Kindly follow instructions on the message template draft'
            ]);
        }

        $account->update([
            'name' => $request->input('name'),
            'project_id' => $project->id,
            'description' => $request->input('description'),
            'account_no' => $request->input('account_no'),
            'target_amount' => $request->input('target_amount'),
            'target_date' => $request->input('target_date'),
            'message_to_donor' => $message_to_donor,
            'message_to_treasurer' => $message_to_treasurer
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Account Updated Successfully'
        ]);
    }

    // create existing account
    public function createExisting(Request $request, Account $account)
    {
        $request->validate([
            'name' => ['required', 'string', 'unique:accounts'],
            'account_no' => ['required', 'string', 'unique:accounts'],
        ]);

        Account::create([
            'name' => $request->input('name'),
            'project_id' => $account->project_id,
            'description' => $account->description,
            'account_no' => $request->input('account_no'),
            'target_amount' => $account->target_amount,
            'target_date' => $account->target_date,
            'message_to_donor' => $account->message_to_donor,
            'message_to_treasurer' => $account->message_to_treasurer
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Account Created Successfully'
        ]);
    }
}
