<?php

namespace App\Console\Commands;

use App\Models\MpesaAccessToken;
use Illuminate\Console\Command;

class RefreshMpesaAccessTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refresh-mpesa-access-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh Mpesa Access Token';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $consumer_key = config('mpesa.c2b_consumer_key');
        $consumer_secret = config('mpesa.c2b_consumer_secret');
        $credentials = base64_encode($consumer_key . ":" . $consumer_secret);
        $url = "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Basic " . $credentials));
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
        $access_token = json_decode($curl_response);
        $formatted_access_token = $access_token->access_token;

        $data = MpesaAccessToken::where('type', 'c2b')->first();
        if ($data){
            $data->update([
                'token' => $formatted_access_token,
            ]);
        } else{
            MpesaAccessToken::create([
                'type' => 'c2b',
                'token' => $formatted_access_token,
            ]);
        }
    }
}
