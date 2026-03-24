<?php

namespace Modules\Network\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Network\Models\RadiusAccount;
use Modules\Network\Services\BandwidthManager;

class UpdateBandwidthUsage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ?int $tenant_id;

    public function __construct(public RadiusAccount $account)
    {
        $this->tenant_id = $account->tenant_id;
    }

    public function handle(BandwidthManager $bandwidthManager): void
    {
        $bandwidthManager->updateBandwidthProfile($this->account);
    }
}