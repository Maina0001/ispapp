<?php

namespace Modules\Customer\Listeners;

use Modules\Customer\Events\CustomerRegistered;
use Modules\Customer\Jobs\SendWelcomeNotification as SendWelcomeJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWelcomeNotification implements ShouldQueue
{
    /**
     * The name of the queue the job should be sent to.
     */
    public string $queue = 'notifications';

    /**
     * Handle the event.
     */
    public function handle(CustomerRegistered $event): void
    {
        // Orchestrate: Dispatch the job to handle external API latency
        SendWelcomeJob::dispatch($event->customer)
            ->onQueue($this->queue);
    }
}