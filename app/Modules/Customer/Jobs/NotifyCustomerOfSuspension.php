<?php

namespace Modules\Customer\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Customer\Models\Customer;
use Modules\Customer\Services\NotificationService;

class NotifyCustomerOfSuspension implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ?int $tenant_id;

    /**
     * Create a new job instance.
     */
    public function __construct(public Customer $customer)
    {
        $this->tenant_id = $customer->tenant_id;
    }

    /**
     * Execute the job.
     */
    public function handle(NotificationService $notificationService): void
    {
        // Future Multi-tenant Note: Resolve tenant-specific SMS templates here
        $notificationService->sendSuspensionNotification($this->customer);
    }
}