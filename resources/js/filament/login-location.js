const root = document.querySelector('[data-login-geo]');

const initLoginLocation = (rootEl) => {
    const btn = document.getElementById('btn-google');
    const btnText = document.getElementById('btn-text');
    const loadingIcon = document.getElementById('loading-icon');
    const googleIcon = document.getElementById('google-icon');
    const distInfo = document.getElementById('distance-info');
    const statusBox = document.getElementById('location-status');
    const statusContent = document.getElementById('status-content');
    const statusTitle = document.getElementById('status-title');
    const statusMessage = document.getElementById('status-message');
    const statusIcon = document.getElementById('status-icon');

    const config = parseConfig(rootEl.dataset.loginGeo);
    const targetLat = Number(config.targetLat);
    const targetLng = Number(config.targetLng);
    const maxRadiusMeters = Number(config.maxRadiusMeters);
    const loginUrl = config.loginUrl || '';

    if (
        !Number.isFinite(targetLat) ||
        !Number.isFinite(targetLng) ||
        !Number.isFinite(maxRadiusMeters) ||
        !loginUrl
    ) {
        return;
    }

    if (
        !btn ||
        !btnText ||
        !loadingIcon ||
        !googleIcon ||
        !distInfo ||
        !statusBox ||
        !statusContent ||
        !statusTitle ||
        !statusMessage ||
        !statusIcon
    ) {
        return;
    }

    const initLocationCheck = () => {
        btn.classList.remove('hidden');
        btn.classList.add('flex');

        if (!navigator.geolocation) {
            lockButton();
            showStatus('error', 'GPS Error', 'Browser tidak mendukung geolocation.');
            distInfo.innerText = 'Gagal mengambil lokasi';
            return;
        }

        navigator.geolocation.getCurrentPosition(successLocation, errorLocation, {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0,
        });
    };

    const successLocation = (position) => {
        const userLat = position.coords.latitude;
        const userLng = position.coords.longitude;
        const distance = getDistanceFromLatLonInMeters(userLat, userLng, targetLat, targetLng);
        const distanceRounded = Math.round(distance);

        distInfo.innerHTML = `Jarak Anda: <span class="font-bold text-gray-700 dark:text-gray-200">${distanceRounded}m</span> <span class="text-gray-400 mx-1">|</span> Batas: ${maxRadiusMeters}m`;

        if (distance <= maxRadiusMeters) {
            unlockButton(userLat, userLng);
            showStatus('success', 'Lokasi Anda Terverifikasi', null);
        } else {
            lockButton();
            showStatus('error', 'Akses Ditolak', `Anda terlalu jauh (${distanceRounded}m). Harap mendekat.`);
        }
    };

    const errorLocation = (err) => {
        let msg = 'Gagal mendeteksi lokasi.';
        if (err.code === 1) msg = 'Izin GPS ditolak browser.';
        if (err.code === 2) msg = 'Sinyal GPS lemah/hilang.';

        lockButton();
        showStatus('error', 'GPS Error', msg);
        distInfo.innerText = 'Gagal mengambil lokasi';
    };

    const showStatus = (type, title, message) => {
        statusBox.classList.remove('hidden', 'opacity-0', 'translate-y-2');

        statusContent.className =
            'flex flex-col items-center justify-center rounded-xl p-2 border shadow-sm ring-1 ring-inset transition-colors duration-300 text-center';

        statusTitle.className = 'text-base font-bold';
        statusTitle.innerText = title;

        if (message) {
            statusMessage.innerText = message;
            statusMessage.classList.remove('hidden');
        } else {
            statusMessage.classList.add('hidden');
        }

        if (type === 'success') {
            statusContent.classList.add(
                'bg-green-50',
                'border-green-200',
                'ring-green-100',
                'dark:bg-green-900/20',
                'dark:border-green-800',
                'dark:ring-green-900'
            );
            statusTitle.classList.add('text-green-700', 'dark:text-green-400');
            statusIcon.innerHTML =
                '<svg class="w-6 h-6 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
        } else {
            statusContent.classList.add(
                'bg-red-50',
                'border-red-200',
                'ring-red-100',
                'dark:bg-red-900/20',
                'dark:border-red-800',
                'dark:ring-red-900'
            );
            statusTitle.classList.add('text-red-700', 'dark:text-red-400');
            statusIcon.innerHTML =
                '<svg class="w-6 h-6 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>';
        }
    };

    const unlockButton = (lat, lng) => {
        btn.className =
            'fi-btn relative grid-flow-col items-center justify-center py-2.5 px-4 text-sm font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg gap-2 flex bg-primary-600 text-white hover:bg-primary-500 shadow-sm ring-1 ring-primary-600';

        loadingIcon.classList.add('hidden');
        googleIcon.classList.remove('hidden');
        btnText.innerText = 'Masuk dengan Google';
        btn.href = `${loginUrl}?lat=${lat}&lng=${lng}`;
        btn.onclick = null;

        const expires = new Date(Date.now() + 86400 * 1000).toUTCString();
        document.cookie = `user_lat=${lat}; expires=${expires}; path=/`;
        document.cookie = `user_lng=${lng}; expires=${expires}; path=/`;
    };

    const lockButton = () => {
        btn.className =
            'fi-btn relative grid-flow-col items-center justify-center py-2.5 px-4 text-sm font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg gap-2 flex cursor-not-allowed bg-gray-50 text-gray-400 border border-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-500 shadow-sm';

        loadingIcon.classList.add('hidden');
        googleIcon.classList.add('hidden');
        btnText.innerText = 'Akses Dibatasi';
        btn.onclick = () => false;
    };

    const getDistanceFromLatLonInMeters = (lat1, lon1, lat2, lon2) => {
        const earthRadiusMeters = 6371000;
        const dLat = ((lat2 - lat1) * Math.PI) / 180;
        const dLon = ((lon2 - lon1) * Math.PI) / 180;
        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos((lat1 * Math.PI) / 180) *
                Math.cos((lat2 * Math.PI) / 180) *
                Math.sin(dLon / 2) *
                Math.sin(dLon / 2);
        return earthRadiusMeters * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLocationCheck);
    } else {
        initLocationCheck();
    }
};

if (root) {
    initLoginLocation(root);
}

function parseConfig(rawConfig) {
    if (!rawConfig) {
        return {};
    }

    try {
        return JSON.parse(rawConfig);
    } catch (error) {
        return {};
    }
}
