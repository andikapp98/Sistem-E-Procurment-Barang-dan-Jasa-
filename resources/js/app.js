import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        // Configure Inertia to handle CSRF tokens
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue);

        // Add global error handler for 419 errors
        app.config.errorHandler = (err, instance, info) => {
            if (err.response && err.response.status === 419) {
                console.warn('CSRF token mismatch detected, refreshing page...');
                window.location.reload();
            }
        };

        return app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
