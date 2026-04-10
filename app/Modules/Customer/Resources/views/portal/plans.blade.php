@extends('customer::layouts.portal')

@section('content')
    <h3>Select a Package</h3>
    <div id="plans-container">
        @foreach($plans as $plan)
            <div class="plan-item" onclick="selectPlan('{{ $plan->id }}', '{{ $plan->price }}')">
                <strong>{{ $plan->name }}</strong><br>
                <small>{{ $plan->bandwidth_limit }} speed</small><br>
                <span style="color: var(--primary); font-weight:bold;">KES {{ $plan->price }}</span>
            </div>
        @endforeach
    </div>

    <button id="pay-btn" class="btn" style="display:none;" onclick="initiatePayment()">Pay with M-Pesa</button>

    <script>
        let selectedPlan = null;
        const mac = "{{ request('mac') }}";
        const phone = "{{ request('phone') }}";

        function selectPlan(id, price) {
            selectedPlan = id;
            document.querySelectorAll('.plan-item').forEach(el => el.style.background = '#fff');
            event.currentTarget.style.background = '#f0f7ff';
            document.getElementById('pay-btn').style.display = 'block';
            document.getElementById('pay-btn').innerText = `Pay KES ${price} via M-Pesa`;
        }

        async function initiatePayment() {
            const btn = document.getElementById('pay-btn');
            btn.disabled = true;
            btn.innerText = "Initiating...";

            const response = await fetch('/api/v1/customer/onboard', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ mac, phone, plan_id: selectedPlan })
            });

            const data = await response.json();
            if(data.success) {
                window.location.href = `/portal/success?checkout_id=${data.checkout_id}`;
            } else {
                alert(data.message);
                btn.disabled = false;
                btn.innerText = "Try Again";
            }
        }
    </script>
@endsection