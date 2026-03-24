<?php

namespace Modules\Network\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Billing\Events\SubscriptionCreated;
use Modules\Billing\Events\InvoicePaid;
use Modules\Customer\Events\CustomerSuspended;
use Modules\Network\Events\UsageThresholdExceeded;
use Modules\Network\Listeners\ProvisionNetworkAccess;
use Modules\Network\Listeners\DisableNetworkAccess;
use Modules\Network\Listeners\RestoreNetworkAccess;
use Modules\Network\Listeners\ThrottleBandwidth;

class NetworkEventServiceProvider extends ServiceProvider
{
    protected $listen = [
        SubscriptionCreated::class => [
            ProvisionNetworkAccess::class,
        ],
        CustomerSuspended::class => [
            DisableNetworkAccess::class,
        ],
        InvoicePaid::class => [
            RestoreNetworkAccess::class,
        ],
        UsageThresholdExceeded::class => [
            ThrottleBandwidth::class,
        ],
        PaymentReceived::class => [
            ProvisionInternetAccess::class,
    ],
    ];
}