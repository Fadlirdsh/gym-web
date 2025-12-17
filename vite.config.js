import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/output.css',
                'resources/css/admin/login.css', // <-- tambahkan ini
                'resources/js/app.js',
                'resources/js/dashboard.js',
            ],
            refresh: true,
        }),
    ],
});
