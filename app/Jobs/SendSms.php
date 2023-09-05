<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SendSms implements ShouldQueue
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
        //send the message
//        try {
//            $client = new Client();
//            $response = $client->request('POST', 'https://api.mobilesasa.com/v1/send/message', [
//                'form_params' => [
//                    'senderID' => config('mobilesasa.senderID'),
//                    'phone' => $this->data['to'],
//                    'message' => $this->data['message']
//                ],
//                'headers' => [
//                    'Accept' => 'application/json',
//                    'Content-Type' => 'application/x-www-form-urlencoded',
//                    'Authorization' => 'Bearer ' . config('mobilesasa.bearer_token'),
//                ],
//            ]);
//            $response_data = $response->getBody()->getContents();
//            Log::info($response_data);
//            $formatted_response = json_decode($response_data);
//        } catch (\Exception $e){
//            Log::info($e->getMessage());
//        }
    }
}
