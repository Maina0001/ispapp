@extends('customer::portal.layout')

@section('content')
<div class="p-6">
    <a href="{{ route('portal.home') }}" class="text-orange-600 font-bold mb-8 block">
        <i class="fas fa-arrow-left"></i> Back to Plans
    </a>

    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <h2 class="text-2xl font-black mb-2">Voucher Login</h2>
        <p class="text-gray-500 text-sm mb-6">Enter the code from your printed voucher or SMS.</p>

        <form action="/api/v1/hotspot/voucher" method="POST" class="space-y-6">
            <input type="text" name="code" placeholder="XXXX-XXXX" 
                   class="w-full text-3xl text-center uppercase font-mono tracking-widest p-4 border-2 border-gray-100 rounded-xl focus:border-orange-500 outline-none">
            
            <button type="submit" class="w-full bg-orange-500 text-white py-4 rounded-xl font-bold text-lg">
                ACTIVATE VOUCHER
            </button>
        </form>
    </div>
</div>
@endsection