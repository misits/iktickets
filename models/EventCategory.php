<?php

namespace Iktickets\models;

use Iktickets\models\Event;
use Iktickets\models\Taxonomy;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

class EventCategory extends Taxonomy
{
    const TYPE = 'ikevent_category';

    public static function register()
    {
        register_taxonomy(self::TYPE, Event::TYPE, [
            'hierarchical' => true,
            'show_admin_column' => true,
            'publicly_queryable' => false,
            'show_in_rest' => true,
            'labels' => [
                'name'              => __('Catégories', 'iktickets'),
                'singular_name'     => __('Catégorie', 'iktickets'),
                'search_items'      => __('Rechercher une catégorie', 'iktickets'),
                'all_items'         => __('Tout les catégories', 'iktickets'),
                'parent_item'       => __('Catégorie parente', 'iktickets'),
                'parent_item_colon' => __('Catégorie parente:', 'iktickets'),
                'edit_item'         => __('Éditer la catégorie', 'iktickets'),
                'update_item'       => __('Modifier la catégorie', 'iktickets'),
                'add_new_item'      => __('Ajouter une nouvelle catégorie', 'iktickets'),
                'new_item_name'     => __('Nouvelle catégorie', 'iktickets'),
                'menu_name'         => __('Catégories', 'iktickets'),
            ]
        ]);

        // Register default categories
        self::generate_categories();
    }

    private static function default_categories() {
        $default = array(
            __('Théâtre et arts vivants'),
            __('Concerts'),
            __('Humour et comédie'),
            __('Ateliers et stages'),
            __('Culture et spectacles'),
            __('Sport'),
            __('Festivals'),
            __('Conférences'),
            __('Loisirs'),
            __('Famille'),
            __('Musique classique'),
            __('Danse'),
            __('Formations'),
            __('Balades et visites'),
            __('Expos et musées'),
            __('Cinéma')
        );
    
        return $default;
    }

    private static function generate_categories() {
        $default = self::default_categories();
        foreach ($default as $cat) {
            if (!term_exists($cat, self::TYPE)) {
                wp_insert_term($cat, self::TYPE);
            }
        }
    }
}
