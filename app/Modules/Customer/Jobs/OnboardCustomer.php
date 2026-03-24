<?php

namespace Modules\Customer\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Customer\Models\Customer;
use Modules\Customer\Services\OnboardingService;

class OnboardCustomer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int|null The tenant context for this background task.
     */
    public ?int $tenant_id;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Customer $customer,
        public array $subscriptionData
    ) {
        $this->tenant_id = $customer->tenant_id;
    }

    /**
     * Execute the job.
     */
    public function handle(OnboardingService $onboardingService): void
    {
        /** * Future Multi-tenant Note: 
         * app(TenantContext::class)->setTenantId($this->tenant_id);
         */
        
        $onboardingService->onboardCustomer($this->customer, $this->subscriptionData);
    }
}