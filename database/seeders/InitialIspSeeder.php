<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Network\Models\ServicePlan;
use App\Models\Tenant;

class InitialIspSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       // 1. Create the Main ISP (The Tenant)
     $tenant = Tenant::create([
            'name' => 'Kariuki Highspeed Services',
            'slug' => 'kariuki-isp',
            'api_key' => Str::random(32),
            'is_active' => true,
        ]);

        // 2. Create Service Plans linked to this Tenant
        $plans = [
            [
                'tenant_id' => $tenant->id,
                'name' => '1 Hour Super',
                'price' => 20.00,
                'duration_minutes' => 60,
                'bandwidth_limit' => '2M/2M', // MikroTik format
                'is_public' => true,
            ],
            [
                'tenant_id' => $tenant->id,
                'name' => '24 Hour Unlimited',
                'price' => 50.00,
                'duration_minutes' => 1440,
                'bandwidth_limit' => '5M/5M',
                'is_public' => true,
            ],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Weekly Blazing',
                'price' => 350.00,
                'duration_minutes' => 10080,
                'bandwidth_limit' => '10M/10M',
                'is_public' => true,
            ],
        ];

        foreach ($plans as $plan) {
            ServicePlan::create($plan);
        }
    }
}
