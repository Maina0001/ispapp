<?php

namespace Modules\Customer\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Payments\Services\MpesaService;
use Modules\Network\Models\RadiusAccount;

class HotspotApiController extends Controller
{
    public function checkPaymentStatus($checkoutId)
    {
        // The frontend success.blade.php polls this
        $isProvisioned = RadiusAccount::where('username', request('mac'))->exists();

        return response()->json([
            'active' => $isProvisioned,
            'username' => request('mac'),
            'password' => request('mac'),
        ]);
    }

    public function initiateStkPush(Request $request, MpesaService $mpesa)
    {
        $request->validate([
            'phone' => 'required|numeric',
            'plan_id' => 'required|exists:bandwidth_profiles,id',
            'mac' => 'required'
        ]);

        return $mpesa->stkPush(
            $request->phone, 
            $request->plan_id, 
            $request->mac
        );
    }
}