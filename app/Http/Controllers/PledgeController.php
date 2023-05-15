<?php

namespace App\Http\Controllers;

use App\Jobs\SendSms;
use App\Models\Pledge;
use App\Models\User;
use App\Rules\ValidateMsisdn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PledgeController extends Controller
{
    // create pledge
    public function create(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'msisdn' => ['required', new ValidateMsisdn(true, false, 'Pledge')],
            'email' => ['required', 'email', 'unique:pledges'],
            'account_no' => ['required', 'string'],
            'target_amount' => ['required', 'numeric', 'min:50'],
            'frequency_amount' => ['required', 'numeric', 'min:10', 'max:'.$request->input('target_amount')],
            'frequency' => ['required', 'string', Rule::in(['0', '1', '2', '3'])],
            'day_of_the_week' => [$request->input('frequency') == 2 ? 'required' : 'nullable', 'string'],
            'notification_day' => [$request->input('frequency') == 3 ? 'required' : 'nullable', 'string'],
            'target_date' => ['required', 'date', 'after:today']
        ]);

        Pledge::create([
            'name' => $request->input('name'),
            'msisdn' => $request->input('msisdn'),
            'email' => $request->input('email'),
            'account_no' => 'PL-'.$request->input('account_no'),
            'target_amount' => $request->input('target_amount'),
            'frequency_amount' => $request->input('frequency_amount'),
            'frequency' => $request->input('frequency'),
            'day_of_the_week' => $request->input('day_of_the_week'),
            'notification_day' => $request->input('notification_day'),
            'target_date' => $request->input('target_date')
        ]);

        //create user account
        $existing_user = User::where('msisdn', $request->input('msisdn'))->first();

        if (!$existing_user){
            $password = generatePin();
            $user = User::create([
                'name' => $request->input('name'),
                'msisdn' => $request->input('msisdn'),
                'email' => $request->input('email'),
                'password' => Hash::make($password),
            ]);

            $user->assignRole('user');

            $app_name = env('APP_NAME');
            SendSms::dispatch([
                'to' => $request->input('msisdn'),
                'message' => "{$request->input('name')}, your {$app_name} account credentials are; \nEmail : {$request->input('email')} \nPassword : {$password} \nLog into your account at https://sdb-mssc.donboscoshrine.com/login"
            ])->onQueue('send-sms')->onConnection('beanstalkd-worker001');
        }

        return response()->json([
            'status' => true,
            'message' => 'Pledge Applied Successfully'
        ]);
    }
}
