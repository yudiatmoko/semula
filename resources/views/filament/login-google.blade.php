@php
    $loginGeo = [
        'targetLat' => config('location.login_google.target_lat'),
        'targetLng' => config('location.login_google.target_lng'),
        'maxRadiusMeters' => config('location.login_google.max_radius_meters'),
        'loginUrl' => route('auth.google'),
    ];
@endphp

<div class="grid gap-y-6" data-login-geo='@json($loginGeo)'>

    <div id="location-status" class="hidden transition-all duration-500 ease-out opacity-0 translate-y-2">
        <div id="status-content" class="flex flex-col items-center justify-center gap-2 rounded-xl p-5 border shadow-sm ring-1 ring-inset text-center">

            <div id="status-icon" class="shrink-0"></div>

            <div class="w-full">
                <h3 id="status-title" class="text-base font-bold"></h3>
                <p id="status-message" class="text-sm mt-1 leading-relaxed text-gray-500 dark:text-gray-400 hidden"></p>
            </div>
        </div>
    </div>

    <a id="btn-google" href="#" onclick="return false;" class="fi-btn relative grid-flow-col items-center justify-center py-2.5 px-4 text-sm font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg gap-2 hidden cursor-not-allowed bg-gray-50 text-gray-400 border border-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-500 shadow-sm">

        <svg id="loading-icon" class="animate-spin h-5 w-5 text-primary-600 dark:text-primary-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>

        <svg id="google-icon" class="h-5 w-5 hidden" viewBox="0 0 24 24" fill="currentColor">
            <path d="M23.5 12.2c0-.9-.1-1.7-.2-2.5H12v4.8h6.4c-.3 1.4-1.1 2.6-2.4 3.4v2.9h3.9c2.3-2.1 3.6-5.3 3.6-8.6z" fill="#FFFFFF" />
            <path d="M12 24c3.2 0 5.9-1.1 7.9-2.9l-3.9-2.9c-1.1.7-2.5 1.1-4 1.1-3.1 0-5.7-2.1-6.6-4.9H1.4v3.1C3.5 21.6 7.5 24 12 24z" fill="#FFFFFF" />
            <path d="M5.4 14.2c-.2-.7-.4-1.4-.4-2.2s.2-1.5.4-2.2V6.7H1.4C.5 8.4 0 10.2 0 12s.5 3.6 1.4 5.3l4-3.1z" fill="#FFFFFF" />
            <path d="M12 4.8c1.7 0 3.3.6 4.5 1.8l3.4-3.4C17.9 1.4 15.2 0 12 0 7.5 0 3.5 2.4 1.4 6.7l4 3.1c.9-2.8 3.5-4.9 6.6-4.9z" fill="#FFFFFF" />
        </svg>

        <span id="btn-text">Mendeteksi Lokasi...</span>
    </a>

    <div class="text-center">
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400" id="distance-info">
            Menunggu sinyal GPS...
        </p>
    </div>

</div>

@vite('resources/js/app.js')
