<?php

namespace Modules\Customer\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Customer\Services\OnboardingService;
use Modules\Network\Models\ServicePlan;
use Modules\Network\Services\ProvisioningService;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class PortalController extends Controller
{
    public function __construct(
        protected OnboardingService $onboardingService,
        protected ProvisioningService $networkService
    ) {}

    /**
     * Display the Portal Landing Page.
     * Captures MAC/IP from MikroTik and identifies the device.
     */
    public function home(Request $request): View
    {
        $mac = $request->query('mac');
        $ip = $request->query('ip');

        // Decoupled: Silently onboard/find the customer record via MAC
        $customer = $mac ? $this->onboardingService->onboardByMac($mac) : null;

        // Check for Free Trial eligibility via the Service (Encapsulated logic)
        $isEligibleForTrial = $customer ? $this->onboardingService->checkTrialEligibility($customer) : false;

        return view('customer::portal.home', [
            'mac' => $mac,
            'ip' => $ip,
            'customer' => $customer,
            'isEligible' => $isEligibleForTrial
        ]);
    }

    /**
     * Display Plan Catalogue.
     * Replaced 'BandwidthProfile' with 'ServicePlan' for consistency.
     */
    public function plans(): View
    {
        // ServicePlans are automatically filtered by tenant_id in the BaseModel
        $plans = ServicePlan::where('is_public', true)
            ->orderBy('price', 'asc')
            ->get();

        return view('customer::portal.plans', compact('plans'));
    }

    /**
     * Activate the daily free trial.
     */
    public function activateFreeTrial(Request $request)
    {
        $mac = $request->input('mac');
        $customer = $this->onboardingService->onboardByMac($mac);

        if (!$this->onboardingService->checkTrialEligibility($customer)) {
            return redirect()->route('portal.error')->with('message', 'Trial limit reached.');
        }

        // Use the OnboardingService to handle the logic of "Trial" activation
        // This calculates expiry and triggers the Network Driver
        $this->onboardingService->activateTrial($customer);

        return redirect()->route('portal.success', ['type' => 'trial']);
    }

    /**
     * Reconnect User.
     * Handles logic for users who still have active time.
     */
    public function reconnect(Request $request): JsonResponse
    {
        $mac = $request->input('mac');
        
        // Decoupled: Ask the Network Module if this MAC has an active session
        $session = $this->networkService->getActiveSession($mac);

        if ($session) {
            return response()->json([
                'success' => true,
                'username' => $session['username'],
                'password' => $session['password'],
                'login_url' => $request->session()->get('hs_link_login')
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No active session found.'], 404);
    }

    public function success(Request $request): View
    {
        return view('customer::portal.success', [
            'checkout_id' => $request->query('checkout_id')
        ]);
    }

    public function voucher(): View
    {
        return view('customer::portal.voucher');
    }

    public function error(): View
    {
        return view('customer::portal.error');
    }
}