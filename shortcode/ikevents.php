<?php

namespace Iktickets\views;

use Iktickets\utils\WPML;
use Iktickets\http\IkController;
use Iktickets\models\Page;
use Iktickets\models\Event;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

$model = new Page(get_the_ID());

?>

<div
    id="iktickets" 
    data-theme="<?= get_option(IkController::WP_OPTION_THEME) ?>"
    data-events="<?= get_option(IkController::WP_OPTION_EVENTS) ?>" 
    data-events-posts="<?=  htmlspecialchars(Event::allToJson(), ENT_QUOTES, 'UTF-8') ?>"
    data-content="<?= htmlspecialchars($model->toJSON(), ENT_QUOTES, 'UTF-8') ?>" 
    data-lang="<?= WPML::current_language() ?>">
</div>