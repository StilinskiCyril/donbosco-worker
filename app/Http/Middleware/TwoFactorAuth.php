<?php

namespace App\Http\Middleware;

use App\Jobs\SendSms;
use App\Models\Otp;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user->two_factor_auth){
            if (!Session::has('user_2fa')){
                if (!Session::has('user_2fa_otp_sent')){
                    // send OTP and redirect to OTP page
                    if (env('APP_ENV') == 'production'){
                        $new_otp = generatePin();
                        //otp to expire after 5 minutes
                        $expire_at = now()->addMinutes(5);

                        Otp::updateOrCreate(
                            ['user_id' => $user->id],
                            ['otp' => Hash::make($new_otp), 'expire_at' => $expire_at]
                        );

                        $message = 'Your account login OTP is '.$new_otp.' and will expiry at '.$expire_at;

                        SendSms::dispatch([
                            'to' => $user->msisdn,
                            'message' => $message
                        ])->onQueue('send-sms')->onConnection('beanstalkd-worker001');
                    }

                    Session::put('user_2fa_otp_sent', $user->id);
                    session()->flash('success', 'OTP sent to your phone. Check your inbox');
                } else {
                    session()->flash('error', 'OTP already sent. Check your inbox');
                }
                return redirect()->route('otp.two-factor-auth-page');
            }
            return $next($request);
        }
        return $next($request);
    }
}
