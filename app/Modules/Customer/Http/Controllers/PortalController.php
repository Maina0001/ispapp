<?php

namespace Modules\Customer\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Modules\Network\Models\ServicePlan;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    /**
     * Display the Landing Page
     */
    public function index(Request $request)
    {
        // For now, we fetch the first ISP we seeded. 
        // Later, we will use 'tenant.resolve' to pick the right one automatically.
        $tenant = Tenant::first();

        if (!$tenant) {
            return "Error: No ISP/Tenant found in database. Please run the seeder.";
        }

        return view('customer::portal.index', compact('tenant'));
    }

    /**
     * Show the Catalogue of Service Plans
     */
    public function plans()
    {
        $tenant = Tenant::first();
        
        // Fetch public plans for this ISP
        $plans = ServicePlan::where('tenant_id', $tenant->id)
                            ->where('is_public', true)
                            ->get();

        return view('customer::portal.plans', compact('tenant', 'plans'));
    }

    /**
     * Show Voucher Entry Form
     */
    public function voucher()
    {
        return view('customer::portal.voucher');
    }

    /**
     * Success Page (Post-Payment)
     */
    public function success()
    {
        return view('customer::portal.success');
    }

    /**
     * Error Page
     */
    public function error()
    {
        return view('customer::portal.error');
    }
}
