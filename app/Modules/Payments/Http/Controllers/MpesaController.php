<?php

namespace Modules\Payments\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Gathuku\Mpesa\Facades\Mpesa;
use Modules\Network\Models\ServicePlan;
use App\Models\Transaction;
use App\Events\PaymentSuccessful;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    public function stkPush(Request $request)
    {
        $request->validate(['plan_id' => 'required', 'phone' => 'required']);
        
        $plan = ServicePlan::findOrFail($request->plan_id);
        $phone = $this->formatPhoneNumber($request->phone);

        $stkResponse = Mpesa::stkpush($phone, $plan->price, 'ISP_BILLING', "Plan: ".$plan->name);
        $response = json_decode($stkResponse);

        if (isset($response->ResponseCode) && $response->ResponseCode == "0") {
            Transaction::create([
                'checkout_request_id' => $response->CheckoutRequestID,
                'plan_id' => $plan->id,
                'phone' => $phone,
                'status' => 'PENDING',
                'amount' => $plan->price,
                'mac_address' => $request->mac // Store this for the MikroTik listener
            ]);

            return response()->json([
                'success' => true,
                'checkout_id' => $response->CheckoutRequestID,
                'message' => 'STK Push sent. Please check your phone.'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'M-Pesa Service Error']);
    }

    public function callback(Request $request)
    {
        $data = json_decode($request->getContent());
        $resultCode = $data->Body->stkCallback->ResultCode;
        $checkoutId = $data->Body->stkCallback->CheckoutRequestID;

        $transaction = Transaction::where('checkout_request_id', $checkoutId)->first();

        if ($transaction) {
            if ($resultCode == 0) {
                $transaction->update(['status' => 'COMPLETED']);
                
                // PLUG & PLAY: Fire event for MikroTik to listen to
                event(new PaymentSuccessful($transaction));
                
                Log::info("Payment Successful: $checkoutId");
            } else {
                $transaction->update(['status' => 'FAILED']);
                Log::warning("Payment Failed: $checkoutId Code: $resultCode");
            }
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    }

    private function formatPhoneNumber($number)
    {
        $cleaned = preg_replace('/\D/', '', $number);
        return "254" . substr($cleaned, -9);
    }
}
