@extends('customer::layouts.portal')

@section('content')
    <div id="status-area">
        <div class="loader"></div>
        <h3>Processing Payment</h3>
        <p>Check your phone for the M-Pesa PIN prompt.</p>
        <p><small>Checking status: <span id="timer">0</span>s</small></p>
    </div>

    <script>
        const checkoutId = "{{ request('checkout_id') }}";
        let seconds = 0;

        const poller = setInterval(async () => {
            seconds += 3;
            document.getElementById('timer').innerText = seconds;

            try {
                const response = await fetch(`/api/v1/customer/payment-status/${checkoutId}`);
                const data = await response.json();

                if (data.status === 'completed') {
                    clearInterval(poller);
                    document.getElementById('status-area').innerHTML = `
                        <h2 style="color: green;">✔ Success!</h2>
                        <p>You are now connected to the internet.</p>
                        <a href="http://google.com" class="btn">Start Browsing</a>
                    `;
                    // Note: In real Mikrotik, redirect to the 'hs_login' link here
                } else if (data.status === 'failed') {
                    clearInterval(poller);
                    alert("Payment Failed. Please try again.");
                    window.location.href = "/portal/home";
                }
            } catch (e) { console.error("Poll error", e); }
        }, 3000);
    </script>
@endsection