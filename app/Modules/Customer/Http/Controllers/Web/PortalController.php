<?php

namespace Modules\Customer\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Network\Models\BandwidthProfile;
use Modules\Network\Services\RadiusManager;
use Modules\Network\Models\RadiusAccounting;

class PortalController extends Controller
{
    /**
     * Display the Portal Landing Page
     * Captures MAC/IP from MikroTik and identifies the device.
     */
    public function home(Request $request)
    {
        // Capture context from router redirect parameters
        $mac = $request->query('mac');
        $ip = $request->query('ip');

        // Check for Free Trial eligibility (7:00 AM -> 9:00 AM)
        $isEligibleForTrial = $this->checkTrialEligibility($mac);

        return view('customer::portal.home', [
            'mac' => $mac,
            'ip' => $ip,
            'isEligible' => $isEligibleForTrial
        ]);
    }

    /**
     * Display Plan Catalogue
     */
    public function plans()
    {
        // Only fetch profiles marked as 'public' for the Hotspot
        $plans = BandwidthProfile::where('is_public', true)
            ->orderBy('price', 'asc')
            ->get();

        return view('customer::portal.plans', compact('plans'));
    }

    /**
     * Voucher Entry View
     */
    public function voucher()
    {
        return view('customer::portal.voucher');
    }

    /**
     * Post-Payment Success Page
     * JS on this page will poll the payment status
     */
    public function success(Request $request)
    {
        return view('customer::portal.success', [
            'checkout_id' => $request->query('checkout_id')
        ]);
    }

    /**
     * Payment or Authentication Error View
     */
    public function error()
    {
        return view('customer::portal.error');
    }

    /**
     * Reconnect User
     * Handles logic for users with an active RADIUS session who were disconnected.
     */
    public function reconnect(Request $request)
    {
        $mac = $request->input('mac');
        
        // Check if device has a valid, non-expired session in RADIUS
        $session = RadiusManager::getActiveSessionByMac($mac);

        if ($session) {
            return response()->json([
                'success' => true,
                'username' => $session->username,
                'password' => $session->value, // Cleartext-Password
                'login_url' => $request->session()->get('hs_link_login')
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No active session found.'], 404);
    }

    /**
     * Activate the 30-minute daily free trial
     */
    public function activateFreeTrial(Request $request)
    {
        $mac = $request->input('mac');

        if (!$this->checkTrialEligibility($mac)) {
            return back()->with('error', 'Trial currently unavailable or already used.');
        }

        // Trigger Provisioning (Network Module)
        // Usually creates a temporary RADIUS account for 30 mins
        RadiusManager::provisionTemporaryAccess($mac, $duration = 30);

        return redirect()->route('portal.success', ['type' => 'trial']);
    }

    /**
     * Internal Logic for Trial Windows
     */
    private function checkTrialEligibility($mac): bool
    {
        $now = now();
        $start = now()->setTime(7, 0);
        $end = now()->setTime(9, 0);

        if (!$now->between($start, $end)) {
            return false;
        }

        // Check if MAC has already used a trial in the last 24 hours
        return !RadiusAccounting::where('callingstationid', $mac)
            ->where('is_trial', true)
            ->where('acctstarttime', '>=', now()->startOfDay())
            ->exists();
    }
}