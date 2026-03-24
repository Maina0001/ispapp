import Utils from './utils.js';

const Mpesa = {
    async triggerSTK(planId, phone) {
        try {
            const response = await fetch('/api/v1/payments/stk-push', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    plan_id: planId,
                    phone: phone,
                    mac: Utils.get('mac'),
                    tenant_id: Utils.getParam('tenant_id') // For multi-tenancy
                })
            });
            return await response.json();
        } catch (error) {
            console.error("STK Push Failed", error);
            return { success: false };
        }
    },

    async pollStatus(checkoutId) {
        const interval = setInterval(async () => {
            const res = await fetch(`/api/v1/payments/query/${checkoutId}`);
            const data = await res.json();

            if (data.status === 'COMPLETED') {
                clearInterval(interval);
                window.location.href = '/portal/success';
            } else if (data.status === 'FAILED') {
                clearInterval(interval);
                Utils.notify("Payment failed or cancelled.", "error");
            }
        }, 3000);
    }
};

export default Mpesa;