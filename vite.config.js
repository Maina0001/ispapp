import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/portal.css',
                'resources/js/portal/portal.js',
                'resources/js/portal/plans.js',
                'resources/js/portal/mpesa.js',
                'resources/js/portal/voucher.js',
                'resources/js/portal/freeTrial.js',
                'resources/js/portal/reconnect.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        outDir: 'public/portal-assets', // Your custom directory
        assetsDir: '',                  // Keep files at the root of the outDir
        rollupOptions: {
            output: {
                // Remove [hash] to keep filenames clean as requested
                entryFileNames: `js/[name].js`,
                chunkFileNames: `js/[name].js`,
                assetFileNames: `css/[name].[ext]`
            }
        }
    }
});