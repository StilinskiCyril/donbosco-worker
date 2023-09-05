<?php

namespace App\Http\Controllers;

use App\Jobs\SendSms;
use App\Models\Account;
use App\Models\Treasurer;
use App\Models\User;
use App\Rules\ValidateMsisdn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
            'msisdn' => ['required', 'string', new ValidateMsisdn(false, false)],
            'email' => ['required', 'email', 'max:255']
        ]);

        if ($request->input('msisdn') == $request->user()->msisdn || $request->input('email') == $request->user()->email){
            return response()->json([
                'status' => false,
                'message' => 'Sorry, you can\'t add yourself as a treasurer'
            ]);
        }

        // check if user exists
        $existing_user = User::where('msisdn', $request->input('msisdn'))
            ->Orwhere('email', $request->input('email'))->first();
        if (!$existing_user){
            $password = generatePin();
            $user = User::create([
                'name' => $request->input('name'),
                'msisdn' => $request->input('msisdn'),
                'msisdn_verified_at' => now(),
                'email' => $request->input('email'),
                'email_verified_at' => now(),
                'password' => Hash::make($password)
            ]);
            $user->assignRole('treasurer');
            $new_user_id = $user->id;

            Treasurer::create([
                'user_id' => $new_user_id,
                'account_id' => $account->id
            ]);

            SendSms::dispatch([
                'to' => $user->msisdn,
                'message' => "You have been added as a treasurer to the fundraiser account ($account->name). Your treasurer email is {$request->input('email')} and your temporary password is $password. Log into your account at ".env('APP_URL')
            ])->onQueue('send-sms')->onConnection('beanstalkd-worker001');
        } else {
            $new_user_id = $existing_user->id;

            $treasurer = Treasurer::where('user_id', $new_user_id)->where('account_id', $account->id)->first();
            if (!$treasurer){
                Treasurer::create([
                    'user_id' => $new_user_id,
                    'account_id' => $account->id
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => "Treasurer {$request->input('name')} already added for account $account->name"
                ]);
            }

            if (!$existing_user->hasRole('treasurer')){
                $existing_user->assignRole('treasurer');
            }

            SendSms::dispatch([
                'to' => $existing_user->msisdn,
                'message' => "You have been added as a treasurer to the fundraiser account ($account->name). Your treasurer email is {$request->input('email')} and your temporary password is $password. Log into your account at ".env('APP_URL')
            ])->onQueue('send-sms')->onConnection('beanstalkd-worker001');
        }

        return response()->json([
            'status' => true,
            'message' => 'Treasurer Created Successfully'
        ]);
    }

    // update treasurer
    public function update(Request $request, Treasurer $treasurer, User $user, Account $account)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'msisdn' => ['required', 'string', new ValidateMsisdn(false), Rule::unique('users')->ignore($user->uuid, 'uuid')],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->uuid, 'uuid')],
        ]);

        $user->update([
            'name' => $request->input('name'),
            'msisdn' => $request->input('msisdn'),
            'email' => $request->input('email')
        ]);

        $treasurer->update([
            'account_id' => $account->id
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Treasurer Updated Successfully'
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
