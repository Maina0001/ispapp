@extends('customer::portal.layout')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[70vh] p-6 text-center">
    <div id="status-icon" class="w-24 h-24 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-4xl mb-6">
        <i class="fas fa-check"></i>
    </div>

    <h2 id="status-title" class="text-2xl font-bold mb-2">Payment Received!</h2>
    <p id="status-msg" class="text-gray-500 max-w-sm">
        We've received your payment. We are currently activating your device on the network.
    </p>
    
    <div id="loader" class="mt-8">
        <div class="w-12 h-12 border-4 border-orange-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
    </div>

    {{-- Manual fallback button (Hidden by default) --}}
    <button id="retry-btn" onclick="window.location.reload()" 
            class="hidden mt-8 px-6 py-2 bg-orange-500 text-white rounded-lg font-bold shadow-lg">
        Try Connecting Again
    </button>

    {{-- Hidden form for MikroTik Login --}}
    <form id="hs-login-form" method="POST" action="">
        <input type="hidden" name="username" id="hs-user">
        <input type="hidden" name="password" id="hs-pass">
        {{-- 'dst' tells MikroTik where to go after login --}}
        <input type="hidden" name="dst" value="https://google.com">
    </form>
</div>

<script>
    const config = {
        mac: localStorage.getItem('hs_mac'),
        loginLink: localStorage.getItem('hs_link-login-only'),
        attempts: 0,
        maxAttempts: 20 // Approx 1 minute
    };

    async function checkStatus() {
        try {
            // Use the status endpoint we created in the Network module
            const response = await fetch(`/api/v1/network/status?mac=${config.mac}`);
            const data = await response.json();

            if (data.ready) {
                // UPDATE UI
                document.getElementById('status-msg').innerText = "Account active! Redirecting to internet...";
                
                // PREPARE & SUBMIT FORM
                const form = document.getElementById('hs-login-form');
                form.action = config.loginLink;
                document.getElementById('hs-user').value = config.mac; // MAC-as-Username
                document.getElementById('hs-pass').value = config.mac; // MAC-as-Password
                form.submit();
            } else {
                config.attempts++;
                
                if (config.attempts < config.maxAttempts) {
                    setTimeout(checkStatus, 3000);
                } else {
                    handleTimeout();
                }
            }
        } catch (error) {
            console.error("Polling Error:", error);
            setTimeout(checkStatus, 5000); // Retry after longer interval on network error
        }
    }

    function handleTimeout() {
        document.getElementById('loader').classList.add('hidden');
        document.getElementById('status-icon').className = "w-24 h-24 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center text-4xl mb-6";
        document.getElementById('status-icon').innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
        document.getElementById('status-title').innerText = "Almost there!";
        document.getElementById('status-msg').innerText = "Activation is taking a bit longer. Please ensure you are still connected to the Wi-Fi and click the button below.";
        document.getElementById('retry-btn').classList.remove('hidden');
    }

    // Start Polling
    if (config.mac && config.loginLink) {
        checkStatus();
    } else {
        document.getElementById('status-msg').innerText = "Error: Session information missing. Please return to the portal home.";
    }
</script>
@endsection