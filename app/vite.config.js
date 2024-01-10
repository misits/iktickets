import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import liveReload from "vite-plugin-live-reload";
import sass from "sass";
import { resolve } from "path";

// https://vitejs.dev/config/
export default defineConfig({
    plugins: [vue(), liveReload(__dirname + "/**/*.php")],
    server: {
        host: "0.0.0.0",
        cors: true,
    },
    css: {
        preprocessorOptions: {
            scss: {
                implementation: sass,
            },
        },
    },
    build: {
        // output dir for production build
        outDir: resolve(__dirname, "/compiled"),
        emptyOutDir: false,

        // emit manifest so PHP can find the hashed files
        manifest: true,

        // esbuild target
        target: "es2018",

        // our entry
        rollupOptions: {
            input: {
              main: resolve(__dirname + "/main.js"),
            },
            output: {
              entryFileNames: "js/[name].js",
              chunkFileNames: "js/[name].js",
              assetFileNames: "[ext]/[name].[ext]",
            },
          },

        // minifying switch
        minify: true,
        write: true,
    },
    resolve: {
        alias: {
            "@": "/src",
        },
    },
    // Other configurations...
});
