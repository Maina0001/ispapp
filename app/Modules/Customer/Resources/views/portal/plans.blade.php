@extends('customer::layouts.portal')

@section('content')
<style>
    /* Modal Styles */
    .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center; }
    .modal-content { background: white; padding: 25px; border-radius: 10px; width: 90%; max-width: 350px; text-align: center; color: black; }
    .input-field { width: 100%; padding: 10px; margin: 15px 0; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;}
</style>

<div id="plans-container">
    @foreach($plans as $plan)
        <div class="plan-row">
            <div class="plan-info">
                <h4>Sh{{ number_format($plan->price, 0) }} = {{ $plan->name }}</h4>
            </div>
            <button class="buy-btn" onclick="openPaymentModal('{{ $plan->id }}', '{{ $plan->price }}')">BUY</button>
        </div>
    @endforeach
</div>

<div id="phoneModal" class="modal">
    <div class="modal-content">
        <h3>Enter M-Pesa Number</h3>
        <input type="tel" id="mpesaPhone" class="input-field" placeholder="e.g. 0712345678">
        <button id="submitBtn" class="buy-btn" style="width:100%;" onclick="submitPayment()">PAY NOW</button>
        <button onclick="closeModal()" style="margin-top:15px; background:none; border:none; color:gray; cursor:pointer;">Cancel</button>
    </div>
</div>

<script>
    // Use an IIFE (Immediately Invoked Function Expression) to prevent global scope errors
    (function() {
        // Unique variable names to avoid collisions
        window.activePlanId = null;

        window.openPaymentModal = function(id, price) {
            window.activePlanId = id;
            document.getElementById('phoneModal').style.display = 'flex';
        }

        window.closeModal = function() {
            document.getElementById('phoneModal').style.display = 'none';
        }

        window.submitPayment = async function() {
            const phone = document.getElementById('mpesaPhone').value;
            const btn = document.getElementById('submitBtn');
            
            if (!phone || phone.length < 9) {
                alert("Please enter a valid phone number");
                return;
            }

            btn.innerText = "Processing...";
            btn.disabled = true;

            try {
                const response = await fetch('/api/v1/payments/stk-push', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ plan_id: window.activePlanId, phone: phone })
                });

                const data = await response.json();
                
                if(data.success) {
                    alert("Check your phone for the M-Pesa prompt!");
                    closeModal();
                } else {
                    alert("Error: " + data.message);
                }
            } catch (e) {
                alert("Connection error. Ensure you are connected to the portal.");
            }
            
            btn.innerText = "PAY NOW";
            btn.disabled = false;
        }
    })();
</script>
@endsection
