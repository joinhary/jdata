<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\Stp2ClientService;
use App\Services\SyncConfigService;
use App\Services\SuuTraSyncService;

class SyncBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $queue = 'sync_backup';

    public function handle(): void
    {
        Log::info('[SYNC_STP2] Job started');

        $config = SyncConfigService::load();
        $timeRun  = now()->format('Y-m-d H:i:s');
        $timePast = $config['timePast'];

        $items = Stp2ClientService::fetch($timePast, $timeRun);

        if (empty($items)) {
            Log::info('[SYNC_STP2] No data');
            return;
        }

        foreach ($items as $item) {
            SuuTraSyncService::sync($item);
        }

        SyncConfigService::updateAfterRun();

        Log::info('[SYNC_STP2] Job completed', [
            'count' => count($items),
            'from'  => $timePast,
            'to'    => $timeRun
        ]);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('[SYNC_STP2] Job failed', [
            'message' => $e->getMessage(),
            'trace'   => $e->getTraceAsString(),
        ]);
    }
}
