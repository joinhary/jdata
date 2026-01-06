<?php

namespace App\Services;

use Illuminate\Support\Carbon;

class SyncConfigService
{
    private static string $path = 'json/local.json';

    public static function load(): array
    {
        $file = storage_path(self::$path);

        if (!file_exists($file)) {
            return [
                'timePast' => now()->subMinutes(10)->format('Y-m-d H:i:s'),
                'timeRun'  => now()->format('Y-m-d H:i:s'),
            ];
        }

        return json_decode(file_get_contents($file), true) ?? [];
    }

    public static function updateAfterRun(): void
    {
        $now = now();

        $data = [
            'timeRun'  => $now->format('Y-m-d H:i:s'),
            'timePast' => $now->subMinutes(10)->format('Y-m-d H:i:s'),
        ];

        file_put_contents(
            storage_path(self::$path),
            json_encode($data, JSON_UNESCAPED_UNICODE)
        );
    }
}
