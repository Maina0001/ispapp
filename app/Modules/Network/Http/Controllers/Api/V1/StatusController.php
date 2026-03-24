<?php

namespace App\Modules\Network\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Modules\Network\Models\RadiusAccount;

class StatusController extends Controller
{
    public function checkStatus(Request $request): JsonResponse
    {
        // 1. Validate inputs (Clean up MAC format just in case)
        $mac = $request->get('mac');
        if (!$mac) {
            return response()->json(['ready' => false, 'error' => 'MAC required'], 400);
        }

        // 2. Multi-Tenant Safety
        // Since this is a public endpoint, we should ensure the check 
        // respects the tenant_id if provided (e.g., from a subdomain or request)
        $query = RadiusAccount::where('username', $mac)
            ->where('attribute', 'MikroTik-Rate-Limit');

        // If your tenant context isn't automatically set by middleware for this route:
        if ($request->has('tid')) {
            $query->where('tenant_id', $request->get('tid'));
        }

        $isProvisioned = $query->exists();

        // 3. Return response
        return response()->json([
            'ready' => $isProvisioned,
            'message' => $isProvisioned ? 'Account active' : 'Provisioning in progress...',
            // We use the router IP provided by the MikroTik redirect (passed from JS)
            'login_url' => "http://" . $request->get('router_ip', '10.0.0.1') . "/login",
        ]);
    }
}