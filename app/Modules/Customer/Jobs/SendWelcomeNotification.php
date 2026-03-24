<?php

namespace Modules\Customer\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Customer\Models\Customer;
use Modules\Customer\Services\NotificationService;

class SendWelcomeNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ?int $tenant_id;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    public function __construct(public Customer $customer)
    {
        $this->tenant_id = $customer->tenant_id;
    }

    public function handle(NotificationService $notificationService): void
    {
        $notificationService->sendWelcomeNotification($this->customer);
    }
}