import Utils from './utils.js';

const Plans = {
    async fetchAll() {
        const response = await fetch('/api/v1/network/bandwidth-profiles?public=1');
        const plans = await response.json();
        this.render(plans.data);
    },

    render(plans) {
        const container = document.getElementById('plans-container');
        if (!container) return;

        container.innerHTML = plans.map(plan => `
            <div class="bg-white rounded-xl shadow-sm p-4 flex items-center border border-gray-200">
                <div class="duration-circle bg-orange-100 text-orange-600">
                    ${plan.download_speed}
                </div>
                <div class="ml-4 flex-grow">
                    <h4 class="font-extrabold text-gray-800">${plan.name}</h4>
                    <p class="text-sm text-gray-500">${Utils.formatMoney(plan.price)}</p>
                </div>
                <button onclick="openPaymentModal(${plan.id}, ${plan.price}, '${plan.name}')" 
                        class="bg-orange-500 text-white px-5 py-2 rounded-full font-bold text-sm">
                    BUY
                </button>
            </div>
        `).join('');
    }
};

export default Plans;