// resources/js/portal/freeTrial.js
export const FreeTrial = {
    async activate() {
        const res = await fetch('/api/v1/hotspot/free-trial', {
            method: 'POST',
            body: JSON.stringify({ mac: localStorage.getItem('hs_mac') })
        });
        if(res.ok) window.location.href = '/portal/success';
    }
};

// resources/js/portal/reconnect.js
export const Reconnect = {
    async checkSession() {
        const mac = localStorage.getItem('hs_mac');
        const res = await fetch(`/api/v1/hotspot/check-session?mac=${mac}`);
        const data = await res.json();
        
        if (data.is_active) {
            // Logic to auto-submit the MikroTik login form
            document.getElementById('hs-login-form').submit();
        } else {
            alert("No active session found for this device.");
        }
    }
};