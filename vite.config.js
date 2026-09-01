import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    optimizeDeps: {
        include: ['jquery', 'select2'],
    },
    server: {
        cors: true, // Enables Access-Control-Allow-Origin headers
        host: 'localhost', // Ensures consistent IPv4/IPv6 resolution (or use '0.0.0.0' to listen on all interfaces)
        port: 5173,
        origin: 'http://localhost:5173', // Fixes absolute asset URLs
    },
    plugins: [
        laravel({
            input: [
                'resources/scss/ixp-pack.scss',
                'resources/js/ixp-pack.js',
                'resources/js/jquery-fileuploader.js',
                'resources/js/clipboard.js',
            ],
            refresh: true,
        }),
    ],
});
