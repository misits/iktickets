<?php

namespace Iktickets\utils;

use Iktickets\http\EventController;
use Iktickets\http\IkController;
use Iktickets\models\Event;
use Iktickets\utils\Size;
use Iktickets\utils\Upscale;

// register routes
include(IKTICKETS_DIR . '/routes/web.php');

// add image sizes
$size = Size::get_instance();
$size->init();
const FULL_SIZE = 99999;
add_filter(
    "image_resize_dimensions",
    [Upscale::class, "resize"],
    10,
    6
);

add_action("init", function () {
    //Size::add(string $name, int $width, int $height, bool|array $crop = false);

    // Exemple full-width
    Size::add("image-xl-2x", 3840, FULL_SIZE, false);
    Size::add("image-xl", 1920, FULL_SIZE, false);
    Size::add("image-l-2x", 2560, FULL_SIZE, false);
    Size::add("image-l", 1280, FULL_SIZE, false);
    Size::add("image-m-2x", 1720, FULL_SIZE, false);
    Size::add("image-m", 860, FULL_SIZE, false);
    Size::add("image-s-2x", 800, FULL_SIZE, false);
    Size::add("image-s", 400, FULL_SIZE, false);
});

// add new size for wysiwyg
add_filter("image_size_names_choose", function ($sizes) {
    // "size_name" => "Label"
    return $sizes;
});


// register cron schedules
add_filter('cron_schedules', function ($schedules) {
    $schedules['15min'] = array(
        'interval' => 60 * 15,
        'display' => 'Once every 15 minutes'
    );
    $schedules['5min'] = array(
        'interval' => 60 * 5,
        'display' => 'Once every 5 minutes'
    );
    $schedules['2min'] = array(
        'interval' => 60 * 2,
        'display' => 'Once every 2 minutes'
    );
    $schedules['1min'] = array(
        'interval' => 60,
        'display' => 'Once every minute'
    );
    return $schedules;
});

// Sync button in menu to sync events
add_action('admin_post_sync_hiktickets', function () {
    // Call the sync function
    EventController::register_events();

    // Redirect back to the admin page after the sync
    wp_redirect(admin_url('admin.php?page=iktickets'));
});

add_action('admin_menu', function () {
    add_menu_page(
        'Iktickets',
        'Iktickets',
        'edit_posts',
        'iktickets',
        function () {
            echo '<div class="wrap">';
            echo '<h1 class="wp-heading-inline">Iktickets</h1>';
            // Get events from the wp option
            $events_encoded = get_option(IkController::WP_OPTION_EVENTS);
            $events_json = htmlspecialchars_decode($events_encoded);
            $events = json_decode($events_json, true);

            // Display the list of events
            echo '<div class="iktickets-events-list">';
            // Start the table
            echo '<table class="wp-list-table widefat fixed striped table-view-list">';
            echo '<thead>';
            echo '<tr>';
            echo '<th><strong>Slug</strong></th>';
            echo '<th><strong>'. translate("Nombre d'événements", "iktickets") . '</strong></th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            // Populate the table with data
            if ($events) 
            {
                foreach ($events as $slug => $items) {
                    $event_post = get_page_by_path($slug, OBJECT, Event::TYPE);
                    if ($event_post) {
                        $post_id = $event_post->ID;
                        echo '<tr>';
                        echo '<td><a href="' . admin_url('post.php?post=' . $post_id . '&action=edit') . '">' . $slug . '</a></td>';
                        echo '<td><strong>' . count($items) . '</strong></td>';
                        echo '</tr>';
                    }
                }
            } else {
                echo '<tr>';
                echo '<td colspan="2"><strong>'. translate("Aucun événement trouvé", "iktickets") . '</strong></td>';
                echo '</tr>';
            }

            // End the table
            echo '</tbody>';
            echo '</table>';

            echo '<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>';
            echo '</div>';

            // Api key & secret key

            // Check last update date
            if ($events)
            {
                echo '<p>'. translate("Dernière mise à jour", "iktickets") . ': <strong>' . date_i18n("d F Y - G\hi", strtotime($events['last_update'])) . '</strong></p>';
            } else {
                echo '<p>'. translate("Dernière mise à jour", "iktickets") . ': <strong>' . translate("Maintenant", "iktickets") . '</strong></p>';
            }
            // Add button to sync events EventController::all_event_json();
            echo '<form class="form-iktickets-sync" method="post" action="' . admin_url('admin-post.php') . '">';
            echo '<input type="hidden" name="action" value="sync_hiktickets">';
            echo '<input type="submit" value="'. translate("Synchroniser maintenant", "iktickets") . '"class="button button-primary">';
            echo '</form>';
            echo '</div>';
        },
        'dashicons-iktickets',
        2
    );

    add_submenu_page(
        'iktickets', // Parent slug
        __('Paramètres Iktickets', 'iktickets'), // Page title
        __('Paramètres', 'iktickets'), // Menu title
        'manage_options', // Capability
        'iktickets-settings', // Menu slug
        function() {
            ?>
            <div class="wrap">
                <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
                <h2><?= translate("Shortcode", "iktickets") ?></h2>
                <p><?= translate("Utilisez le shortcode suivant pour afficher les événements dans une page", "iktickets") ?> : <strong>[iktickets_events]</strong></p>
                <p><?= translate("Utilisez le code suivant pour afficher les événements dans un template", "iktickets") ?> : <strong>echo do_shortcode("[iktickets_events]");</strong></p>
                <form action="options.php" method="post">
                    <?php
                    // Output security fields for the registered setting "iktickets"
                    settings_fields('iktickets');
                    // Output setting sections and their fields
                    do_settings_sections('iktickets');
                    // Output save settings button
                    submit_button('Save Settings');
                    ?>
                </form>
                
            </div>
            <?php
        } // Callback function
    );
});

add_action('admin_init', function () {
    // Register a new setting for "iktickets" page
    register_setting('iktickets', 'iktickets_api_key');
    register_setting('iktickets', 'iktickets_api_token');
    register_setting('iktickets', 'iktickets_current_season');
    register_setting('iktickets', 'iktickets_color_theme');

    // Add a new section to a "iktickets" page
    add_settings_section(
        'iktickets_api_settings', // ID
        __('Options', 'iktickets'), // Title
        function () { echo __('Entrez les détails de votre API ici.', 'iktickets'); }, // Callback
        'iktickets' // Page
    );

    // Add a new field to the "iktickets_api_settings" section of "iktickets" page
    add_settings_field(
        'iktickets_api_key', // ID
        'API Key', // Title
        function () { // Callback
            $setting = get_option('iktickets_api_key');
            echo '<input type="text" name="iktickets_api_key" value="' . esc_attr($setting) . '">';
        },
        'iktickets', // Page
        'iktickets_api_settings' // Section
    );

    add_settings_field(
        'iktickets_api_token', // ID
        'API Token', // Title
        function () { // Callback
            $setting = get_option('iktickets_api_token');
            echo '<input type="text" name="iktickets_api_token" value="' . esc_attr($setting) . '">';
        },
        'iktickets', // Page
        'iktickets_api_settings' // Section
    );

    // Add a select with all seasons as year-1/year from 1970 to current year
    add_settings_field(
        'iktickets_current_season', // ID
        __('Saison courante', 'iktickets'), // Title
        function () { // Callback
            $setting = get_option('iktickets_current_season');
            $current_year = date('Y');
            $options = [];
            for ($i = $current_year; $i >= 1970; $i--) {
                $options[] = '<option value="' . $i . '-' . ($i + 1) . '" ' . selected($setting, $i . '-' . ($i + 1), false) . '>' . $i . '-' . ($i + 1) . '</option>';
            }
            echo '<select name="iktickets_current_season">' . implode('', $options) . '</select>';
        },
        'iktickets', // Page
        'iktickets_api_settings' // Section
    );

    // Add color theme  form html color picker
    add_settings_field(
        'iktickets_color_theme', // ID
        __('Couleur du thème', 'iktickets'), // Title
        function () { // Callback
            $setting = get_option('iktickets_color_theme');
            echo '<input type="color" name="iktickets_color_theme" value="' . esc_attr($setting) . '">';
        },
        'iktickets', // Page
        'iktickets_api_settings' // Section
    );
    
});

// Add IkTickets page
add_action('init', function () {

    // Pages to create
    $pages = array(
        'Events' => array(
            'slug' => 'ikevents',
            'template' => '',
        ),
    );

    // Create pages
    foreach ($pages as $page_name => $page) {
        $page_id = get_page_by_path($page['slug']);

        if (!$page_id) {
            $page_id = wp_insert_post([
                'post_title' => $page_name,
                'post_name' => $page['slug'],
                'post_type' => 'page',
                'post_status' => 'publish',
                'comment_status' => 'closed',
            ]);

            if (!empty($page['template'])) {
                update_post_meta($page_id, '_wp_page_template', $page['template']);
            }
        }
    }

    // Add label to ikevents page
    add_filter('display_post_states', function ($states, $post) {
        if ($post->post_name == 'ikevents') {
            $states['ikevents_slug'] = __('Iktickets Page', 'iktickets');
        }
        return $states;
    }, 10, 2);

    // add short code for events
    add_shortcode('iktickets_events', function ($atts) {
        $atts = shortcode_atts([
            'season' => get_option('iktickets_current_season'),
        ], $atts);

        ob_start();
        include(IKTICKETS_DIR . '/shortcode/ikevents.php');
        return ob_get_clean();
    });
});