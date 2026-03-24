const Utils = {
    // Get parameter from URL query string
    getParam: (name) => {
        return new URLSearchParams(window.location.search).get(name);
    },

    // Persistent storage for hotspot session
    store: (key, value) => localStorage.setItem('hs_' + key, value),
    
    get: (key) => localStorage.getItem('hs_' + key),

    // Formatting currency
    formatMoney: (amount) => 'Ksh ' + Number(amount).toLocaleString(),

    // UI Toast Notification (Simple)
    notify: (message, type = 'info') => {
        alert(message); // Replace with a custom toast UI if preferred
    }
};

export default Utils;