import Utils from './utils.js';

const Voucher = {
    async activate(code) {
        const response = await fetch('/api/v1/hotspot/voucher', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                code: code,
                mac: Utils.get('mac')
            })
        });

        const result = await response.json();
        if (result.success) {
            window.location.href = '/portal/success';
        } else {
            Utils.notify(result.message || "Invalid Voucher Code", "error");
        }
    }
};

export default Voucher;