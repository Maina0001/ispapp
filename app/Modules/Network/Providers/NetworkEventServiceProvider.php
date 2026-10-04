<?php

namespace Modules\Network\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Billing\Events\InvoicePaid;
use Modules\Billing\Events\SubscriptionCreated;
use Modules\Customer\Events\CustomerSuspended;
use Modules\Network\Events\UsageThresholdExceeded;
use Modules\Network\Listeners\DisableNetworkAccess;
use Modules\Network\Listeners\ProvisionInternetAccess;
use Modules\Network\Listeners\ProvisionNetworkAccess;
use Modules\Network\Listeners\RestoreNetworkAccess;
use Modules\Network\Listeners\ThrottleBandwidth;
use Modules\Payments\Events\PaymentReceived;

class NetworkEventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // Customer paid → create/extend Subscription → fire SubscriptionCreated
        PaymentReceived::class => [
            ProvisionInternetAccess::class,
        ],

        // Subscription exists → provision hardware via the driver
        SubscriptionCreated::class => [
            ProvisionNetworkAccess::class,
        ],

        // Customer suspended for non-payment → disable RADIUS + kick session
        CustomerSuspended::class => [
            DisableNetworkAccess::class,
        ],

        // Invoice settled → restore access if no other overdue invoices
        InvoicePaid::class => [
            RestoreNetworkAccess::class,
        ],

        // Data usage threshold crossed → apply FUP throttling
        UsageThresholdExceeded::class => [
            ThrottleBandwidth::class,
        ],
    ];
}