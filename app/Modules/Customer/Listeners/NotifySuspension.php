<?php

namespace Modules\Customer\Listeners;

use Modules\Customer\Events\CustomerSuspended;
use Modules\Customer\Services\NotificationService;

class NotifySuspension
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(CustomerSuspended $event): void
    {
        // Orchestrate: Delegate to service to handle channel selection (SMS/Email)
        $this->notificationService->sendSuspensionNotification($event->customer);
    }
}