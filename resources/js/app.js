import '../css/app.css';
import './bootstrap';

import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import axios from 'axios';
import csrfUtils from './utils/csrf';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Function to get CSRF token
function getCsrfToken() {
    const token = document.head.querySelector('meta[name="csrf-token"]');
    return token ? token.content : '';
}

// Function to refresh CSRF token from server
async function refreshCsrfToken() {
    try {
        await axios.get('/sanctum/csrf-cookie');
        // Wait a bit for the cookie to be set
        await new Promise(resolve => setTimeout(resolve, 100));
    } catch (error) {
        console.error('Failed to refresh CSRF token:', error);
    }
}

// Configure Axios globally before Inertia
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-CSRF-TOKEN'] = getCsrfToken();

// Update axios token before each request
axios.interceptors.request.use(config => {
    config.headers['X-CSRF-TOKEN'] = getCsrfToken();
    return config;
});

// Handle 419 errors by refreshing CSRF token
axios.interceptors.response.use(
    response => response,
    async error => {
        if (error.response && error.response.status === 419) {
            console.log('419 error detected, refreshing CSRF token...');
            await refreshCsrfToken();
            // Retry the request
            const config = error.config;
            config.headers['X-CSRF-TOKEN'] = getCsrfToken();
            return axios.request(config);
        }
        return Promise.reject(error);
    }
);

// Configure Inertia to use fresh CSRF token before each request
router.on('before', (event) => {
    const token = getCsrfToken();
    if (token && event.detail.visit.method !== 'get') {
        event.detail.visit.headers = event.detail.visit.headers || {};
        event.detail.visit.headers['X-CSRF-TOKEN'] = token;
    }
});

// Handle Inertia errors - specifically 419
router.on('error', async (event) => {
    const error = event.detail.errors;
    const response = event.detail.response;
    
    // If it's a 419 error, refresh CSRF and retry
    if (response && response.status === 419) {
        console.log('419 Page Expired - Refreshing CSRF token and retrying...');
        event.preventDefault(); // Prevent default error handling
        
        await refreshCsrfToken();
        
        // Get fresh token
        const newToken = getCsrfToken();
        
        // Retry the same request with fresh token
        const originalVisit = event.detail.visit;
        if (originalVisit) {
            // Add small delay to ensure cookie is set
            setTimeout(() => {
                router.visit(originalVisit.url, {
                    method: originalVisit.method,
                    data: originalVisit.data,
                    headers: {
                        ...originalVisit.headers,
                        'X-CSRF-TOKEN': newToken
                    },
                    preserveState: originalVisit.preserveState ?? false,
                    preserveScroll: originalVisit.preserveScroll ?? false,
                    only: originalVisit.only,
                    onSuccess: originalVisit.onSuccess,
                    onError: originalVisit.onError,
                });
            }, 200);
        }
    }
});

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
