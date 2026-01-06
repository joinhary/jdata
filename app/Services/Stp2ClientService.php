<?php

namespace App\Services;

use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\Log;

class Stp2ClientService
{
    public static function fetch(string $past, string $run): array
    {
        $url = 'http://stp2.hoicongchungviencantho.org/get-data-for-backup'
            . '?timePast=' . urlencode($past)
            . '&timeRun=' . urlencode($run)
            . '&token=aboqor';

        Log::info('[SYNC_STP2] Fetching', ['url' => $url]);

        $response = Curl::to($url)
            ->withTimeout(60)
            ->get();

        if (!$response) {
            Log::warning('[SYNC_STP2] Empty response');
            return [];
        }

        $json = json_decode($response, true);

        return $json['data'] ?? [];
    }
}
