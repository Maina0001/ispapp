/**
 * Captive Portal Auto-Login Handler
 */
const HotspotActivation = {
    // Configuration from Data Attributes or Global Vars
    settings: {
        mac: document.body.dataset.userMac,
        routerIp: document.body.dataset.nasIp || '10.0.0.1',
        endpoint: '/api/v1/network/status',
        interval: 2500, // 2.5 seconds
        maxAttempts: 24, // 1 minute total timeout
    },

    attempts: 0,

    init() {
        console.log("Checking activation status for: " + this.settings.mac);
        this.poll();
    },

    poll() {
        const url = `${this.settings.endpoint}?mac=${this.settings.mac}&router_ip=${this.settings.routerIp}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                this.attempts++;

                if (data.ready) {
                    this.updateUI("Payment Verified! Connecting you to the internet...");
                    this.triggerMikrotikLogin(data.login_url);
                } else if (this.attempts < this.settings.maxAttempts) {
                    setTimeout(() => this.poll(), this.settings.interval);
                } else {
                    this.handleTimeout();
                }
            })
            .catch(err => {
                console.error("Status check failed", err);
                setTimeout(() => this.poll(), this.settings.interval);
            });
    },

    updateUI(msg) {
        const statusEl = document.getElementById('status-text');
        if (statusEl) statusEl.innerText = msg;
    },

    triggerMikrotikLogin(loginUrl) {
        // Create a hidden form to POST credentials to the MikroTik Router
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = loginUrl;

        // For Hotspots: Username and Password are both the MAC Address
        const fields = {
            'username': this.settings.mac,
            'password': this.settings.mac,
            'dst': 'http://www.google.com' // Redirect after login
        };

        for (const [name, value] of Object.entries(fields)) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            form.appendChild(input);
        }

        document.body.appendChild(form);
        form.submit();
    },

    handleTimeout() {
        this.updateUI("Activation is taking longer than expected.");
        const retryBtn = document.getElementById('retry-btn');
        if (retryBtn) retryBtn.classList.remove('hidden');
    }
};

document.addEventListener('DOMContentLoaded', () => HotspotActivation.init());