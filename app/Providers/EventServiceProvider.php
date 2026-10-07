<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // Global event mappings go here. Keep module-specific mappings
        // in the module's own EventServiceProvider.
    ];

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}