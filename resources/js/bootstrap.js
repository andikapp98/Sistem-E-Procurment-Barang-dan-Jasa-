import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;
window.axios.defaults.withXSRFToken = true;

// Get CSRF token from meta tag and set it for axios
let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    window.axios.defaults.headers.common['X-XSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Refresh CSRF token before each axios request
window.axios.interceptors.request.use(function (config) {
    token = document.head.querySelector('meta[name="csrf-token"]');
    if (token) {
        config.headers['X-CSRF-TOKEN'] = token.content;
        config.headers['X-XSRF-TOKEN'] = token.content;
    }
    return config;
}, function (error) {
    return Promise.reject(error);
});

// Add CSRF token to fetch requests (used by Inertia)
const originalFetch = window.fetch;
window.fetch = function(...args) {
    const [url, config = {}] = args;
    
    // Get fresh CSRF token
    const csrfToken = document.head.querySelector('meta[name="csrf-token"]');
    
    if (csrfToken && config.method && ['POST', 'PUT', 'PATCH', 'DELETE'].includes(config.method.toUpperCase())) {
        config.headers = {
            ...config.headers,
            'X-CSRF-TOKEN': csrfToken.content,
            'X-XSRF-TOKEN': csrfToken.content,
        };
    }
    
    return originalFetch.apply(this, [url, config]);
};
