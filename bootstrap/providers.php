<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Core\Providers\CoreServiceProvider::class,
    App\Providers\HorizonServiceProvider::class,
    App\Providers\Modules\Billing\Providers\BillingServiceProvider::class,
    App\Providers\Modules\Customer\Providers\CustomerServiceProvider::class,
    App\Providers\Modules\Network\Providers\NetworkServiceProvider::class,
    App\Providers\Modules\Payments\Providers\PaymentsServiceProvider::class,
    App\Providers\Modules\Reporting\Providers\ReportingServiceProvider::class,
    App\Providers\ModuleEventServiceProvider::class,
    
];
