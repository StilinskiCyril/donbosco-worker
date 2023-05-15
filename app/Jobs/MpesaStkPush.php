<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class MpesaStkPush implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $client = new Client();
            $response = $client->request('POST', url('api/v1/c2b/stk/push'), [
                'form_params' => [
                    'amount' => $this->data['amount'],
                    'msisdn' => $this->data['msisdn'],
                    'account_no' => $this->data['account_no']
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ]);
            Log::channel('stklog')->info($response->getBody());
        } catch (\Exception $e){
            Log::channel('stklog')->info($e->getMessage());
        }
    }
}
