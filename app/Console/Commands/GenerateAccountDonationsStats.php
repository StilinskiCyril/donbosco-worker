<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Donation;
use App\Models\Stat;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class GenerateAccountDonationsStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-account-donations-stats {day?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Account Donation Stats';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $yesterday = today()->subDay();
        $yesterday = $this->argument('day') ?: $yesterday;

        try {
            $yesterday = Carbon::parse($yesterday);
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
            return 0;
        }

        $from_time = $yesterday->startOfDay()->toDateTimeString();
        $to_time = $yesterday->endOfDay()->toDateTimeString();
        $date = $yesterday->toDateString();

        $accounts = Account::all();
        foreach ($accounts as $account) {
            $amount = Donation::where('account_no', $account->account_no)->whereBetween('trans_time', [$from_time, $to_time])->sum('amount');
            $charges = Donation::where('account_no', $account->account_no)->whereBetween('trans_time', [$from_time, $to_time])->sum('charges');
            $net = Donation::where('account_no', $account->account_no)->whereBetween('trans_time', [$from_time, $to_time])->sum('net');
            Stat::updateOrCreate([
                'account_id' => $account->id,
                'date' => $date
            ], [
                'amount' => $amount,
                'charges' => $charges,
                'net' => $net
            ]);
        }
    }
}
