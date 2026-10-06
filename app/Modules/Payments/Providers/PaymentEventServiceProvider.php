<?php

namespace Modules\Payments\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Payments\Events\MpesaTransactionCompleted;
use Modules\Payments\Events\MpesaTransactionFailed;
use Modules\Payments\Listeners\CreatePaymentFromMpesaTransaction;
use Modules\Notifications\Listeners\NotifyCustomerMpesaFailed;
use Modules\Billing\Events\SubscriptionCreated;


class PaymentEventServiceProvider extends ServiceProvider
{
    protected $listen = [

        MpesaTransactionCompleted::class => [
            CreatePaymentFromMpesaTransaction::class,
        ],
        MpesaTransactionFailed::class => [
            NotifyCustomerMpesaFailed::class,
        ],
      
    ];
}