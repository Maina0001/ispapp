<?php

namespace Modules\Network\Listeners;

use Modules\Network\Events\UsageThresholdExceeded;
use Modules\Network\Services\BandwidthManager;
use Illuminate\Contracts\Queue\ShouldQueue;

class ThrottleBandwidth implements ShouldQueue
{
    public string $queue = 'network';

    public function __construct(
        protected BandwidthManager $bandwidthManager
    ) {}

    public function handle(UsageThresholdExceeded $event): void
    {
        // Orchestrate: Apply the throttled profile to the RADIUS account
        $this->bandwidthManager->applyFupThrottling($event->radiusAccount);
    }
}