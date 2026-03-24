import Utils from './utils';
import Plans from './plans';
import Mpesa from './mpesa';
import { FreeTrial } from './freeTrial';
import { Reconnect } from './reconnect';

// Initialize the portal context
document.addEventListener('DOMContentLoaded', () => {
    console.log("Portal Engine Loaded");
    
    // Capture MikroTik params from URL
    const params = ['mac', 'ip', 'ssid', 'link-login-only'];
    params.forEach(p => {
        const val = new URLSearchParams(window.location.search).get(p);
        if (val) Utils.store(p, val);
    });

    // Auto-fetch plans if the container exists
    if (document.getElementById('plans-container')) {
        Plans.fetchAll();
    }
});

// Expose functions to the global window object for HTML button access
window.openPaymentModal = (id, price, name) => Mpesa.initiate(id, price, name);
window.submitPayment = () => Mpesa.submit();
window.activateFreeTrial = () => FreeTrial.activate();
window.checkLastSession = () => Reconnect.checkSession();