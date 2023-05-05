<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Treasurer;
use App\Models\User;
use App\Rules\ValidateMsisdn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TreasurerController extends Controller
{
    // manage treasurers page
    public function managePage(Request $request)
    {
        return view('treasurers');
    }

    // create treasurer
    public function create(Request $request, Account $account)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'msisdn' => ['required', 'string', new ValidateMsisdn(true, 'User', 'msisdn', 'msisdn')],
            'email' => ['required', 'email', 'unique:users'],
        ]);

        $password = generatePin();
        Log::info($password);
        $user = User::create([
            'name' => $request->input('name'),
            'msisdn' => $request->input('msisdn'),
            'msisdn_verified_at' => now(),
            'email' => $request->input('email'),
            'email_verified_at' => now(),
            'password' => Hash::make($password),
            'remember_token' => Str::random(10),
        ]);

        $user->assignRole('treasurer');

        Treasurer::create([
            'user_id' => $user->id,
            'account_id' => $account->id
        ]);

        \App\Jobs\SendSms::dispatch([
            'to' => $request->input('msisdn'),
            'message' => "{$request->input('name')}, Your treasurer email is {$request->input('email')} and your temporary password is $password. Log into your account at https://sdb-mssc.donboscoshrine.com"
        ])->onQueue('send-sms')->onConnection('beanstalkd-worker001');

        return response()->json([
            'status' => true,
            'message' => 'Treasurer Created Successfully'
        ]);
    }

    // load treasurers
    public function load(Request $request)
    {
        return Treasurer::with(['account' => function($q){
            $q->with(['project']);
        }, 'user'])->filter($request)->paginate(20);
    }
}
