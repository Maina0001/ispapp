<?php

namespace Modules\Payments\Jobs;

use App\Core\Jobs\BaseJob; // Use the BaseJob for automatic context
use Modules\Payments\Services\MpesaService;

class ProcessMpesaCallback extends BaseJob
{
    /**
     * No need for Manual Traits (Queueable, etc.) as BaseJob has them.
     */
    public function __construct(
        public array $callbackData,
        ?int $tenant_id = null
    ) {
        // Pass the tenant_id to the parent constructor to register it for the worker
        parent::__construct($tenant_id);
    }

    public function handle(MpesaService $mpesaService): void
    {
        /**
         * The 'RestoreTenantContext' middleware has already run at this point.
         * Any database query inside $mpesaService->processCallback() 
         * will automatically be scoped to the correct ISP.
         */
        $mpesaService->processCallback($this->callbackData);
    }
}