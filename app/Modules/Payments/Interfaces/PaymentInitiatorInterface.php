<?php

namespace Modules\Payments\Interfaces;

use Modules\Customer\Models\Customer;
use Modules\Network\Models\ServicePlan;

interface PaymentInitiatorInterface
{
    /**
     * Trigger the push to the customer's device.
     */
    public function initiatePush(Customer $customer, ServicePlan $plan): array;
}