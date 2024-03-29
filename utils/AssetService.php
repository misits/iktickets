<?php

namespace Iktickets\utils;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

class AssetService
{

    const ASYNC_SCRIPTS = [];
    const DEFER_SCRIPTS = ["app"];

    public static function register()
    {
        add_action("wp_enqueue_scripts", [self::class, "enqueue_styles"]);
        add_action("wp_enqueue_scripts", [self::class, "enqueue_scripts"]);
        add_action("admin_enqueue_scripts", function () {
            wp_enqueue_style(
                "iktickets-admin-css",
                IKTICKETS_URL . "/admin/assets/css/admin.css",
                [],
            );
        });
        add_filter("script_loader_tag", [self::class, "loader"], 10, 2);
    }


    public static function enqueue_styles()
    {
        if (!self::is_vite_running()) {
            // Production environment (Local build)
            $assets_dir = IKTICKETS_DIR . "/app/compiled/css/";

            if (!file_exists($assets_dir)) {
                return;
            }
            $files = scandir($assets_dir);

            if (!is_array($files)) {
                return;
            }

            foreach ($files as $file) {
                if (preg_match('/\.css$/', $file)) {
                    wp_enqueue_style(
                        "vite-iktickets-" . basename($file, ".css"),
                        IKTICKETS_URL . "/app/compiled/css/" . $file,
                        [],
                    );
                }
            }
        }
    }

    public static function enqueue_scripts()
    {   
        $head = is_admin() ? 'admin_head' : 'wp_head';

        if (self::is_vite_running()) {
            // Development environment (Vite server)
            add_action($head, [self::class, 'vite_dev_server_scripts']);
        } else {
            remove_action($head, [self::class, 'vite_dev_server_scripts']);
      
            // Production environment (local build)
            self::enqueue_production_scripts();
        }
    }

    public static function vite_dev_server_scripts()
    {
        // Watch if already injected from another plugin
        if (has_action('wp_head', 'vite_dev_server_scripts')) {
            return;
        }

        if (self::is_vite_running()) {
            echo '
            <!-- Vite Dev Server -->
            <script type="module">
                import RefreshRuntime from "http://localhost:5173/@react-refresh"
                RefreshRuntime.injectIntoGlobalHook(window)
                window.$RefreshReg$ = () => {}
                window.$RefreshSig$ = () => (type) => type
                window.__vite_plugin_react_preamble_installed__ = true
            </script>';
            echo '<script type="module" crossorigin src="http://localhost:5173/@vite/client"></script>';
            echo '<script type="module" crossorigin src="http://localhost:5173/main.js"></script>';
            echo '<!-- End Vite Dev Server -->';
        } else {
            echo '<!-- Vite Dev Server -->';
            echo '<!-- End Vite Dev Server -->';
        }
    }


    private static function is_vite_running()
    {
        $dev_file = IKTICKETS_DIR . "/app/.dev";

        if (file_exists($dev_file)) {
            return true;
        }

        return false;
    }

    private static function enqueue_production_scripts()
    {
        $assets_dir = IKTICKETS_DIR . "/app/compiled/js/";
        if (!file_exists($assets_dir)) {
            return;
        }
        $files = scandir($assets_dir);

        

        foreach ($files as $file) {
            if (preg_match('/\.js$/', $file)) {
                wp_enqueue_script(
                    "vite-iktickets-" . basename($file, ".js"),
                    IKTICKETS_URL . "/app/compiled/js/" . $file,
                    [],
                    null,
                    true
                );
                add_filter(
                    "script_loader_tag",
                    function ($tag, $handle, $src) use ($file) {
                        if ($handle === "vite-iktickets-" . basename($file, ".js")) {
                            $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
                        }
                        return $tag;
                    },
                    10,
                    3
                );
            }
        }
    }

    public static function loader($tag, $handle)
    {
        if (in_array($handle, self::ASYNC_SCRIPTS)) {
            $tag = str_replace(' src', ' async src', $tag);
        }

        if (in_array($handle, self::DEFER_SCRIPTS)) {
            $tag = str_replace(' src', ' defer src', $tag);
        }

        return $tag;
    }


    public static function version($file = null)
    {
        $path = IKTICKETS_URL . "/app/compiled/.vite/mix-manifest.json";

        if (!file_exists($path)) {
            return null;
        }

        $manifest_content = file_get_contents($path);
        $manifest_json = json_decode($manifest_content, true);

        if ($file === null or !isset($manifest_json[$file])) {
            return md5($manifest_content);
        }

        $file_data = explode("?id=", $manifest_json[$file]);

        if (isset($file_data[1])) {
            return $file_data[1];
        }

        return null;
    }
}
