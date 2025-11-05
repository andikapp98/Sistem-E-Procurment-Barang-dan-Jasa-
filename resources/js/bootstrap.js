import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;
window.axios.defaults.withXSRFToken = true;

// Function to get fresh CSRF token
function getCsrfToken() {
    const token = document.head.querySelector('meta[name="csrf-token"]');
    return token ? token.content : null;
}

// Get CSRF token from meta tag and set it for axios
let token = getCsrfToken();

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    window.axios.defaults.headers.common['X-XSRF-TOKEN'] = token;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Refresh CSRF token before each axios request
window.axios.interceptors.request.use(function (config) {
    const freshToken = getCsrfToken();
    if (freshToken) {
        config.headers['X-CSRF-TOKEN'] = freshToken;
        config.headers['X-XSRF-TOKEN'] = freshToken;
    }
    return config;
}, function (error) {
    return Promise.reject(error);
});

// Handle 419 errors by refreshing the page (which will get new CSRF token)
window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.status === 419) {
            console.warn('CSRF token mismatch detected, reloading page...');
            // Give user a moment to see the error before reload
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
        return Promise.reject(error);
    }
);

// Add CSRF token to fetch requests (used by Inertia)
const originalFetch = window.fetch;
window.fetch = function(...args) {
    const [url, config = {}] = args;
    
    // Get fresh CSRF token
    const csrfToken = getCsrfToken();
    
    if (csrfToken && config.method && ['POST', 'PUT', 'PATCH', 'DELETE'].includes(config.method.toUpperCase())) {
        config.headers = {
            ...config.headers,
            'X-CSRF-TOKEN': csrfToken,
            'X-XSRF-TOKEN': csrfToken,
        };
    }
    
    return originalFetch.apply(this, [url, config]);
};
