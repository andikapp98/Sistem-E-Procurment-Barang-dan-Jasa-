import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Get CSRF token from meta tag
function getCsrfToken() {
    const token = document.head.querySelector('meta[name="csrf-token"]');
    return token ? token.content : null;
}

// Setup CSRF token for axios
const token = getCsrfToken();
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
} else {
    console.error('CSRF token not found');
}

// Axios interceptor to refresh CSRF token on each request
window.axios.interceptors.request.use(
    function (config) {
        const freshToken = getCsrfToken();
        if (freshToken) {
            config.headers['X-CSRF-TOKEN'] = freshToken;
        }
        return config;
    },
    function (error) {
        return Promise.reject(error);
    }
);

// Handle 419 errors by reloading the page to get fresh CSRF token
window.axios.interceptors.response.use(
    function (response) {
        return response;
    },
    function (error) {
        if (error.response && error.response.status === 419) {
            console.warn('CSRF token mismatch detected, reloading page...');
            window.location.reload();
        }
        return Promise.reject(error);
    }
);
