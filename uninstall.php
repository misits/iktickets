<?php

namespace Iktickets;

use Iktickets\models\Event;
use Iktickets\models\EventCategory;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

// if uninstall.php is not called by WordPress, die
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

// Delete options.
delete_option( 'iktickets' );

// Delete custom post types.
$to_delete = [
    Event::TYPE,
];

foreach ($to_delete as $item) {
    $posts = get_posts([
        'post_type' => $item,
        'numberposts' => -1,
        'post_status' => 'any',
    ]);

    foreach ($posts as $post) {
        wp_delete_post($post->ID, true);
    }
}

// Delete custom taxonomies.
$terms = get_terms([
    'taxonomy' => EventCategory::TYPE,
    'hide_empty' => false,
]);

foreach ($terms as $term) {
    wp_delete_term($term->term_id, EventCategory::TYPE);
}