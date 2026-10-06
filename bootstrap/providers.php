<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\HorizonServiceProvider::class,


    Modules\Billing\Providers\BillingServiceProvider::class,
    Modules\Customer\Providers\CustomerEventServiceProvider::class,
    Modules\Customer\Providers\CustomerServiceProvider::class,
    Modules\Network\Providers\NetworkEventServiceProvider::class,
    Modules\Network\Providers\NetworkServiceProvider::class,
    Modules\Payments\Providers\PaymentsServiceProvider::class,
    Modules\Payments\Providers\PaymentEventServiceProvider::class,
   
    Modules\Reporting\Providers\ReportingServiceProvider::class,
];
