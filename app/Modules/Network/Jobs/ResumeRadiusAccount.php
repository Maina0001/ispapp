<?php

namespace Modules\Network\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Customer\Models\Customer;
use Modules\Network\Services\RadiusManager;

class ResumeRadiusAccount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ?int $tenant_id;

    public function __construct(public Customer $customer)
    {
        $this->tenant_id = $customer->tenant_id;
    }

    public function handle(RadiusManager $radiusManager): void
    {
        $radiusManager->resumeRadiusAccount($this->customer->radiusAccount);
    }
}