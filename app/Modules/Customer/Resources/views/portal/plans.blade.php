@extends('customer::portal.layout')

@section('content')
<div class="p-4 space-y-6">
    
    {{-- 1. Free Trial Component --}}
    @if($showFreeTrial)
    <div class="bg-gradient-to-r from-orange-500 to-red-600 rounded-xl p-5 text-white shadow-lg">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="font-bold text-lg">Daily Free Internet</h2>
                <p class="text-sm opacity-90">Valid 6 AM - 8 AM daily</p>
            </div>
            <i class="fas fa-gift text-2xl opacity-50"></i>
        </div>
        <button onclick="activateFreeTrial()" class="mt-4 w-full bg-white text-orange-600 py-2 rounded-lg font-bold shadow-md">
            ACTIVATE 20 MINS FREE
        </button>
    </div>
    @endif

    {{-- 2. Plan Catalogue --}}
    <div class="grid grid-cols-1 gap-4">
        <h3 class="font-bold text-gray-600 uppercase text-xs tracking-widest px-1">Internet Plans</h3>
        @foreach($plans as $plan)
        <div class="bg-white rounded-xl shadow-sm p-4 flex items-center border border-gray-200">
            <div class="duration-circle {{ $plan->color_class ?? 'bg-orange-100 text-orange-600' }}">
                {{ $plan->duration_label }}
            </div>
            <div class="ml-4 flex-grow">
                <h4 class="font-extrabold text-gray-800">{{ $plan->name }}</h4>
                <p class="text-sm text-gray-500">Ksh {{ number_format($plan->price) }} • 1 Device</p>
            </div>
            <button onclick="openPaymentModal({{ $plan->id }}, {{ $plan->price }}, '{{ $plan->name }}')" 
                    class="bg-orange-500 text-white px-5 py-2 rounded-full font-bold text-sm">
                BUY
            </button>
        </div>
        @endforeach
    </div>

    {{-- 3. Voucher & Reconnect Links --}}
    <div class="grid grid-cols-2 gap-4">
        <a href="{{ route('portal.voucher') }}" class="bg-white p-4 rounded-xl shadow-sm border text-center">
            <i class="fas fa-ticket text-orange-500 mb-2"></i>
            <span class="block text-xs font-bold text-gray-600 uppercase">Use Voucher</span>
        </a>
        <button onclick="checkLastSession()" class="bg-white p-4 rounded-xl shadow-sm border text-center">
            <i class="fas fa-rotate text-blue-500 mb-2"></i>
            <span class="block text-xs font-bold text-gray-600 uppercase">Reconnect</span>
        </button>
    </div>
</div>

{{-- Payment Modal --}}
<div id="payModal" class="hidden fixed inset-0 bg-black/60 z-[100] flex items-end sm:items-center justify-center p-4">
    <div class="bg-white w-full max-w-sm rounded-t-3xl sm:rounded-3xl p-6 shadow-2xl transition-all">
        <h3 class="text-xl font-bold mb-1">Buy <span id="modal-plan-name"></span></h3>
        <p class="text-gray-500 text-sm mb-6">Enter M-Pesa number to pay <span class="font-bold text-black" id="modal-plan-price"></span></p>
        
        <input type="tel" id="phone_number" placeholder="0712345678" 
               class="w-full text-2xl font-bold tracking-widest p-4 border-2 border-orange-100 rounded-2xl focus:border-orange-500 outline-none mb-6">
        
        <button id="pay-btn" onclick="submitPayment()" class="w-full bg-green-600 text-white py-4 rounded-2xl font-bold text-lg shadow-lg">
            PAY VIA M-PESA
        </button>
        <button onclick="closeModal()" class="w-full mt-2 py-2 text-gray-400 font-medium">Cancel</button>
    </div>
</div>

<script>
    let selectedPlan = null;

    function openPaymentModal(id, price, name) {
        selectedPlan = id;
        document.getElementById('modal-plan-name').innerText = name;
        document.getElementById('modal-plan-price').innerText = 'Ksh ' + price;
        document.getElementById('payModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('payModal').classList.add('hidden');
    }

    async function submitPayment() {
        const phone = document.getElementById('phone_number').value;
        const btn = document.getElementById('pay-btn');
        btn.disabled = true;
        btn.innerText = "Processing...";

        const response = await fetch('/api/v1/mpesa/stkpush', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                plan_id: selectedPlan,
                phone: phone,
                mac_address: localStorage.getItem('hs_mac')
            })
        });

        if(response.ok) {
            window.location.href = "{{ route('portal.success') }}";
        } else {
            window.location.href = "{{ route('portal.error') }}";
        }
    }
</script>
@endsection