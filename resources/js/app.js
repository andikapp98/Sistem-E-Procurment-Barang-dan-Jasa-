import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { router } from '@inertiajs/vue3';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Configure Inertia to use CSRF token from meta tag
router.on('before', (event) => {
    const token = document.head.querySelector('meta[name="csrf-token"]');
    if (token) {
        event.detail.visit.headers = {
            ...event.detail.visit.headers,
            'X-CSRF-TOKEN': token.content,
        };
    }
});

// Handle 419 CSRF errors globally
router.on('error', (event) => {
    const response = event.detail.response;
    if (response && response.status === 419) {
        console.warn('CSRF token mismatch detected (419), refreshing page...');
        // Reload to get fresh CSRF token
        window.location.reload();
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
