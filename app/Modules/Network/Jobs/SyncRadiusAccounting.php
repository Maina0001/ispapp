<?php

namespace Modules\Network\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Network\Services\ProvisioningService;

class SyncRadiusAccounting implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public ?int $tenant_id = null) {}

    public function handle(ProvisioningService $provisioningService): void
    {
        $provisioningService->syncAccountingData();
    }
}