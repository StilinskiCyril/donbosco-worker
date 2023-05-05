<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Donor;
use App\Models\User;
use App\Rules\ValidateMsisdn;
use Illuminate\Http\Request;

class DonorsController extends Controller
{
    // manage donors page
    public function managePage(Request $request)
    {
        return view('donors');
    }

    // load donors
    public function load(Request $request)
    {
        $request->validate([
            'name' => ['nullable', 'string'],
            'msisdn' => ['nullable', 'string', new ValidateMsisdn(false, 'Donor', 'msisdn', 'msisdn')],
            'account_no' => ['nullable', 'string'],
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date']
        ]);
        if ($request->user()->hasRole('super-admin') || $request->user()->hasRole('admin')) {
            return Donor::filter($request)->paginate(20);
        } else{
            $account_ids = $request->user()->treasurer()->pluck('account_id');
            $account_nos = Account::whereIn('id', $account_ids)->pluck('account_no');
            return Donor::whereIn('account_no', $account_nos)->filter($request)->paginate(20);
        }
    }
}
