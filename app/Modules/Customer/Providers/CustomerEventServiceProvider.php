<?php

namespace Modules\Customer\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Customer\Events\CustomerRegistered;
use Modules\Customer\Events\CustomerUpdated;
use Modules\Customer\Events\CustomerSuspended;
use Modules\Customer\Events\CustomerReactivated;
use Modules\Customer\Listeners\SendWelcomeNotification;
use Modules\Customer\Listeners\SyncCustomerToRadius;
use Modules\Customer\Listeners\NotifySuspension;

class CustomerEventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the Customer Module.
     */
    protected $listen = [
        CustomerRegistered::class => [
            SendWelcomeNotification::class,
        ],
        CustomerUpdated::class => [
            SyncCustomerToRadius::class,
        ],
        CustomerSuspended::class => [
            NotifySuspension::class,
        ],
        CustomerReactivated::class => [
            // Example: NotifyRestoration::class
        ],
        
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
    }
}