<?php

namespace App\Console\Commands;

use App\Models\Pledge;
use Illuminate\Console\Command;

class ResetPledgeMpesaStkCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-pledge-mpesa-stk-count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset Pledge Mpesa Stk Count';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Pledge::query()->update([
            'stk_count_daily' => 0
        ]);
        return 0;
    }
}
