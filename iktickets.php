<?php

/**
 * Plugin Name: Iktickets
 * Description: Integration of Infomaniak E-tickets into WordPress
 * Plugin URI: https://github.com/misits/iktickets
 * Version: 1.0.0
 * Requires at least: 5.2
 * Requires PHP: 8.0
 * Author: Martin IS IT Services
 * Author URI: https://misits.ch
 * Text Domain: iktickets
 * License: GPL v2 or later
 * Domain Path: /languages
 */

namespace Iktickets;

use Iktickets\http\EventController;
use Iktickets\models\Event;
use Iktickets\models\EventCategory;
use Iktickets\utils\AssetService;

// Autoload classes.
spl_autoload_register(function ($class) {
    $filename = explode("\\", $class);
    $namespace = array_shift($filename);

    array_unshift($filename, __DIR__);

    if ($namespace === __NAMESPACE__) {
        include implode(DIRECTORY_SEPARATOR, $filename) . ".php";
    }
});

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

// Define plugin constants.
define( 'IKTICKETS_DIR', plugin_dir_path(__FILE__) );
define( 'IKTICKETS_URL', plugin_dir_url(__FILE__) );

// Api
include(IKTICKETS_DIR . "/utils/main.php");

// Register classes.
$to_register = [
    AssetService::class,
    // models
    Event::class,
    EventCategory::class,
    EventController::class,
];

add_action('init', function () use ($to_register) {
    foreach ($to_register as $class) {
        $class::register();
    }

    // if Event::TYPE is registered add metabox
    if (in_array(Event::TYPE, get_post_types())) {
        Event::metabox();
    }
});