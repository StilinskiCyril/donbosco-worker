<?php

namespace App\Http\Controllers;

use App\Jobs\SendSms;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class OtpController extends Controller
{
    // login otp page
    public function loginOtpPage()
    {
        return view('login-otp');
    }

    // send login otp
    public function send(Request $request)
    {
        $new_otp = generatePin();
        //otp to expire after 5 minutes
        $otp_expiry = now()->addMinutes(5);
        $otp = Otp::where('user_id', $request->user()->id)->first();

        if ($otp){
            $time_difference = now()->diffInMinutes($otp->updated_at);
            if ($time_difference < 5) {
                return response()->json([
                    'status' => false,
                    'type' => 'error',
                    'message' => 'OTP has not expired yet. Try again after 5 minutes.'
                ]);
            }

            $otp->update([
                'otp' => Hash::make($new_otp),
                'expire_at' => $otp_expiry,
            ]);
        } else {
            Otp::create([
                'user_id' => $request->user()->id,
                'otp' => Hash::make($new_otp),
                'expire_at' => $otp_expiry,
            ]);
        }

        $message = 'Your account login OTP is '.$new_otp.' and will expiry at '.$otp_expiry;

        SendSms::dispatch([
            'to' => $request->user()->msisdn,
            'message' => $message
        ])->onQueue('send-sms')->onConnection('beanstalkd-worker001');

        return response()->json([
            'status' => true,
            'type' => 'success',
            'message' => 'OTP sent to your phone successfully. Check your inbox.'
        ]);
    }

    // verify login otp
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'numeric']
        ]);

        $otp_to_verify = $request->input('otp');
        $otp = Otp::where('user_id', $request->user()->id)->first();
        if ($otp){
            if (Hash::check($otp_to_verify, $otp->otp)){
                if ($otp->expire_at < now()){
                    return response()->json([
                        'status' => false,
                        'message' => 'Your OTP has expired'
                    ]);
                }

                Session::put('user_2fa', $request->user()->id);

                return response()->json([
                    'status' => true,
                    'message' => 'Your account login request has been authenticated successfully. You will be redirected to the Dashboard shortly'
                ]);
            }
        }

        return response()->json([
            'status' => false,
            'message' => 'Invalid OTP'
        ]);
    }
}
