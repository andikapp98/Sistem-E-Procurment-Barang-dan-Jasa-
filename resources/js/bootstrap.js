import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Setup CSRF token for axios - this will be used by Inertia automatically
let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    // Also set it for fetch API which Inertia uses
    window.csrfToken = token.content;
    console.log('CSRF Token loaded:', token.content.substring(0, 10) + '...');
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Make CSRF token globally available
if (token) {
    window.csrf_token = token.content;
}

// Intercept Inertia requests to ensure CSRF token is always sent
if (window.axios) {
    const originalRequest = window.axios.request;
    window.axios.request = function(config) {
        // Ensure CSRF token is in headers for all requests
        if (!config.headers) {
            config.headers = {};
        }
        if (window.csrfToken && !config.headers['X-CSRF-TOKEN']) {
            config.headers['X-CSRF-TOKEN'] = window.csrfToken;
        }
        return originalRequest.call(this, config);
    };
}
