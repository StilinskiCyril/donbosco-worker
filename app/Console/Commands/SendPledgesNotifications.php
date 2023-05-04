<?php

namespace App\Console\Commands;

use App\Models\Pledge;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class SendPledgesNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-pledges-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Pledge Notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pledges = Pledge::all();

        foreach ($pledges as $pledge){
            $hash = Hash::make('skrillex');
            $url = url('stk-push-form?hash='.$hash.'&amount='.$pledge->frequency_amount.'&account_no='.$pledge->account_no);
            if (Carbon::now()->format('Y-m-d') < Carbon::parse($pledge->end_date)->format('Y-m-d')){
                if ($pledge->frequency == 0 & $pledge->alerted == 0){
                    \App\Jobs\SendSms::dispatch([
                        'to' => $pledge->msisdn,
                        'message' => 'Hi ' .$pledge->name. ', we wish to remind you of your pledge of KES '. number_format($pledge->target_amount) .'. Kindly use Paybill 4069077 Account MSSC or click here to contribute online ' . $url . '. Be blessed.'
                    ])->onQueue('send-sms')->onConnection('beanstalkd-worker001');

                    $pledge->update([
                        'alerted' => 1,
                        'last_alert_time' => now()
                    ]);

//                    stkPush($pledge->frequency_amount, $pledge->phone, $pledge->account_number);

                } elseif ($pledge->frequency == 1 & Carbon::parse($pledge->last_alert_time)->format('Y-m-d') < Carbon::now()->format('Y-m-d')){
                    \App\Jobs\SendSms::dispatch([
                        'to' => $pledge->msisdn,
                        'message' => 'Hi ' .$pledge->name. ', we wish to remind you of your pledge of KES '. number_format($pledge->target_amount) .'. Kindly use Paybill 4069077 Account MSSC or click here to contribute online ' . $url . '. Be blessed.'
                    ])->onQueue('send-sms')->onConnection('beanstalkd-worker001');

                    $pledge->update([
                        'alerted' => 1,
                        'last_alert_time' => now()
                    ]);

                    //stkPush($pledge->frequency_amount, $pledge->phone, $pledge->account_number);

                } elseif ($pledge->frequency == 2 & $pledge->day_of_the_week == Carbon::now()->englishDayOfWeek){
                    \App\Jobs\SendSms::dispatch([
                        'to' => $pledge->msisdn,
                        'message' => 'Hi ' .$pledge->name. ', we wish to remind you of your pledge of KES '. number_format($pledge->target_amount) .'. Kindly use Paybill 4069077 Account MSSC or click here to contribute online ' . $url . '. Be blessed.'
                    ])->onQueue('send-sms')->onConnection('beanstalkd-worker001');

                    $pledge->update([
                        'alerted' => 1,
                        'last_alert_time' => now()
                    ]);

                    //stkPush($pledge->frequency_amount, $pledge->phone, $pledge->account_number);

                } elseif ($pledge->frequency == 3 & $pledge->date == Carbon::now()->day){
                    \App\Jobs\SendSms::dispatch([
                        'to' => $pledge->msisdn,
                        'message' => 'Hi ' .$pledge->name. ', we wish to remind you of your pledge of KES '. number_format($pledge->target_amount) .'. Kindly use Paybill 4069077 Account MSSC or click here to contribute online ' . $url . '. Be blessed.'
                    ])->onQueue('send-sms')->onConnection('beanstalkd-worker001');

                    $pledge->update([
                        'alerted' => 1,
                        'last_alert_time' => now()
                    ]);

                    //stkPush($pledge->frequency_amount, $pledge->phone, $pledge->account_number);

                }
            }
        }
        return 0;
    }
}