<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewHorizon', function (User $user) {
            // Option 1: Check for an 'is_admin' boolean column on the users table.
            return $user->is_admin;

            // Option 2: Check for a specific role (e.g., using a permissions package).
            // return $user->hasRole('admin');

            // Option 3: Allow a specific list of emails (good for a small team).
            // return in_array($user->email, [
            //     'admin@your-isp.com',
            //     'devops@your-isp.com',
            // ]);
        });
    }
}