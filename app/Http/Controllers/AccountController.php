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
            'account_number' => ['required', 'string', 'unique:accounts'],
            'target_amount' => ['required', 'numeric'],
            'target_date' => ['required', 'date', 'after:today'],
            'message_to_contributor' => ['required', 'string'],
            'message_to_account_admin' => ['required', 'string']
        ]);

        Account::create([
            'name' => $request->input('name'),
            'project_id' => $project->id,
            'description' => $request->input('description'),
            'account_number' => $request->input('account_number'),
            'target_amount' => $request->input('target_amount'),
            'target_date' => $request->input('target_date'),
            'message_to_contributor' => $request->input('message_to_contributor'),
            'message_to_account_admin' => $request->input('message_to_account_admin')
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Account Created Successfully'
        ]);
    }

    // load accounts
    public function load(Request $request)
    {
        return Account::filter($request)->paginate(20);
    }

    // update account
    public function update(Request $request, Account $account)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'account_number' => ['required', 'string', Rule::unique('accounts')->ignore($account->id)],
            'target_amount' => ['required', 'numeric'],
            'target_date' => ['required', 'date', 'after:today'],
            'message_to_contributor' => ['required'],
            'message_to_account_admin' => ['required']
        ]);

        $account->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'target_amount' => $request->input('target_amount'),
            'target_date' => $request->input('target_date'),
            'message_to_contributor' => $request->input('message_to_contributor'),
            'message_to_account_admin' => $request->input('message_to_account_admin')
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
            'account_number' => ['required', 'string', Rule::unique('accounts')->ignore($account->id)],
        ]);

        Account::create([
            'name' => $account->name,
            'project_id' => $account->project_id,
            'description' => $account->description,
            'account_number' => $account->account_number,
            'target_amount' => $account->target_amount,
            'target_date' => $account->target_date,
            'message_to_contributor' => $account->message_to_contributor,
            'message_to_account_admin' => $account->message_to_account_admin
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Account Created Successfully'
        ]);
    }
}
