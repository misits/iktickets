<?php

namespace Iktickets\models;

use Iktickets\http\EventController;
use Iktickets\models\EventCategory;
use Iktickets\models\CustomPostType;

class Event extends CustomPostType implements \JsonSerializable
{
    const TYPE = 'ikevent';
    const SLUG = 'evenements';

    public static function type_settings()
    {

        return [
            "menu_position" => 2.2,
            "label" => __("Événements", "iktickets"),
            "labels" => [
                "name" => __("Événements", "iktickets"),
                "singular_name" => __("Événements", "iktickets"),
                "menu_name" => __("Événements", "iktickets"),
                "all_items" => __("Tous les événements", "iktickets"),
                "add_new" => __("Ajouter", "iktickets"),
                "add_new_item" => __("Ajouter un événement", "iktickets"),
                "edit_item" => __("Modifier événement", "iktickets"),
                "new_item" => __("Nouvel événement", "iktickets"),
                "view_item" => __("Voir l'événement", "iktickets"),
                "view_items" => __("Voir les événement", "iktickets"),
                "search_items" => __("Rechercher un événement", "iktickets"),
            ],
            "description" => "",
            "public" => true,
            "publicly_queryable" => true,
            "show_ui" => true,
            "show_in_rest" => true,
            "show_in_nav_menus" => true,
            "rest_base" => "",
            "has_archive" => true,
            "show_in_menu" => true,
            "exclude_from_search" => false,
            "capability_type" => "post",
            "map_meta_cap" => true,
            "hierarchical" => false,
            "rewrite" => ["slug" => self::SLUG, "with_front" => false],
            "query_var" => true,
            "menu_icon" => "dashicons-tickets-alt",
            'taxonomies' => ['ikevent_category'],
            "supports" => ["title", "editor", "thumbnail", "excerpt"],
        ];
    }

    /**
     * Get team as JSON
     * 
     * @return array
     */
    public function jsonSerialize(): mixed
    {
        return [
            "id" => $this->id(),
            "title" => $this->title(),
            "slug" => $this->slug(),
            "link" => $this->link(),
            "excerpt" => $this->excerpt(),
            "content" => $this->content(),
            "categories" => $this->get_categories(true),
            "next_events" => $this->get_events(1, function ($next_event) {
                return $next_event;
            }),
            "thumbnail" => $this->thumbnail_url(),
        ];
    }

    public static function allToJson()
    {
        $events = static::all();

        // sort all by next event date
        usort($events, function ($a, $b) {
            $a_next_event = $a->get_events(1, function ($next_event) {
                return $next_event;
            });
            $b_next_event = $b->get_events(1, function ($next_event) {
                return $next_event;
            });

            if (empty($a_next_event) || empty($b_next_event)) {
                return 0;
            }

            return strtotime($a_next_event[0]['date']) <=> strtotime($b_next_event[0]['date']);
        });

        $result = [];
        foreach ($events as $event) {

            if ($event->is_archived()) {
                continue;
            }

            $result[] = $event->jsonSerialize();
        }

        return json_encode($result);
    }

    public static function current_event(callable $callback)
    {
        $now = date("n");
        $all = Event::all();
        $current_season = get_option('iktickets_current_season');

        // Remove event not in current season
        $all = array_filter($all, function ($event) use ($current_season) {
            $event_season = $event->meta('season_year');
            return $event_season == $current_season;
        });

        // Remove event passed
        $all = array_filter($all, function ($event) use ($now) {
            $event_month = $event->meta('event_periode');
            return $event_month !== '0';
        });

        // Remove event with all representations passed
        $all = array_filter($all, function ($event) {
            return !$event->is_archived();
        });

        // Sort by event month
        usort($all, function ($a, $b) {
            return $a->meta('event_periode') <=> $b->meta('event_periode');
        });

        $model = array_shift($all);

        return $callback($model);
    }

    public static function has_current_event()
    {

        $model = static::current_event(function ($model) {
            return $model;
        });
        
        if ($model) {
            return true;
        }

        return false;
    }



    public function previous_event(callable $callback)
    {

        $current_event_month = $this->meta('event_periode');
        $current_event_season = $this->meta('season_year');

        if ($current_event_month != 1) {
            $previous_event_month = $current_event_month - 1;
        } elseif ($current_event_month == 1) {
            $previous_event_month = 12;
        }

        $previous_event = QueryBuilder::from(static::class)
            ->add_meta_query("season_year", $current_event_season, "=")
            ->add_meta_query("event_periode", $previous_event_month, "=")
            ->find_one();

        if ($previous_event) {
            return $callback($previous_event);
        } else {
            return null;
        }
    }

    public function next_event(callable $callback)
    {

        $current_event_month = $this->meta('event_periode');
        $current_event_season = $this->meta('season_year');

        if ($current_event_month != 12) {
            $next_event_month = $current_event_month + 1;
        } elseif ($current_event_month == 12) {
            $next_event_month = 1;
        }

        $next_event = QueryBuilder::from(static::class)
            ->add_meta_query("season_year", $current_event_season, "=")
            ->add_meta_query("event_periode", $next_event_month, "=")
            ->find_one();

        if ($next_event) {
            return $callback($next_event);
        } else {
            return null;
        }
    }

     /*
     * Get next events from today or last event if all events are passed
     * @param int $limit
     * @return array
     */

     public function get_events($limit = 3, callable $callback = null): array
     {
         $today = date('Ymd');
         $event_dates = $this->meta('representations');
 
         $next_events = array_filter($event_dates, function ($event_date) use ($today) {
             // Is the event date greater than today ?
             return strtotime($event_date['date']) >= strtotime($today);
         });
 
         usort($next_events, function ($a, $b) {
             return strtotime($a['date']) <=> strtotime($b['date']);
         });
 
         // Is all events are passed ?
         if (empty($next_events)) {
             // Return last event
             $next_events = array_filter($event_dates, function ($event_date) use ($today) {
                 return strtotime($event_date['date']) < strtotime($today);
             });
 
             usort($next_events, function ($a, $b) {
                 return strtotime($b['date']) <=> strtotime($a['date']);
             });
 
             $next_event = array_shift($next_events);
             return $callback ? [$callback($next_event)] : [$next_event];
         }
 
         // Return next events
         if ($limit == 1) {
             $next_event = array_shift($next_events);
             return $callback ? [$callback($next_event)] : [$next_event];
         } else if ($limit == -1) {
             $result = [];
             foreach ($next_events as $event) {
                 $result[] = $callback ? $callback($event) : $event;
             }
             return $result;
         } else {
             $next_events = array_slice($next_events, 0, $limit);
             $result = [];
             foreach ($next_events as $event) {
                 $result[] = $callback ? $callback($event) : $event;
             }
             return $result;
         }
     }
 
     /*
      * Get all past events
      * @return array
      */
 
     public function is_archived(): bool
     {
         $today = date('Ymd');
         $event_dates = $this->meta('representations');
         $representations_count = count($event_dates);
 
         $past_events = array_filter($event_dates, function ($event_date) use ($today) {
             // Is the event date greater than today ?
             return strtotime($event_date['date']) < strtotime($today);
         });
 
         // If all are past events, it means this Event is archived
         if (count($past_events) === $representations_count) {
             return true;
         } else {
             return false;
         }
     }
 
     /**
      * Get all event ticket url
      * @return string
      */
     public function get_ticket_url($ticket_id): string
     {
 
         if ($ticket_id) {
             return home_url('/iktickets/#/events/') . $this->slug() . '/order/' . $ticket_id;
         }
 
         return '';
     }
 
     /**
      * Get available seats for an event
      * @return int
      */
     public function get_available_seats(int $ticket_id = 0): int
     {
         if (!$ticket_id || $ticket_id == 0) {
             // get next event
             $next_event = $this->get_events(1, function ($next_event) {
                 return $next_event;
             });
 
             $ticket_id = $next_event[0]['ticket_id'];
         }
 
         if (!$ticket_id || $ticket_id == 0) {
             return -1;
         }
 
         $event = new EventController();
         $request = new \WP_REST_Request();
         $request->set_param('event_id', $ticket_id);
         $response = $event->get_event_zone($request);
         $data = $response->get_data()[0] ?? [];
 
         if (empty($data)) {
             return -1;
         }
 
         return $data["free_seats"] ?? 0;
     }


    /*
     * Get categories as array
     * @return array
     */

    public function categories(callable $callback)
    {
        return $this->terms(EventCategory::class, $callback);
    }

    /*
     * Get categories as string or array
     * @param bool $as_array
     * @param string $splitter
     * @return string|array
     */
    public function get_categories(bool $as_array = false, $splitter = ', ')
    {
        $categories = $this->categories(function ($category) {
            return $category->title();
        });

        if (empty($categories)) {
            return '';
        }

        if ($as_array) {
            return $categories;
        }

        if (count($categories) === 1) {
            return $categories[0];
        } else if (count($categories) === 2) {
            return implode(', ', $categories);
        }

        return implode($splitter, $categories);
    }

    public static function metabox()
    {
        add_action('add_meta_boxes', function () {
            add_meta_box(
                'event_details_metabox',      // ID of the metabox
                'Event Details',              // Title of the metabox
                function ($post) {
                    $representations = get_post_meta($post->ID, 'representations', true);
                    $group_event_id = get_post_meta($post->ID, 'group_event_id', true);
                    $portal_image = get_post_meta($post->ID, 'portal_image', true);

                    wp_nonce_field('event_details_metabox', 'event_details_nonce');

                    // Group event ID field
                    echo '<div class="fields">';
                    echo '<label for="group_event_id">Group Event ID:</label>';
                    echo '<input type="text" id="group_event_id" name="group_event_id" value="' . esc_attr($group_event_id) . '" class="widefat">';
                    echo '</div>';
                    // Portal image URL field
                    echo '<div class="fields">';
                    echo '<label for="portal_image">Portal Image URL:</label>';
                    echo '<input type="text" id="portal_image" name="portal_image" value="' . esc_attr($portal_image) . '" class="widefat">';
                    echo '</div>';

                    // If there are no saved representations, start with an empty array
                    if (empty($representations)) {
                        $representations = array(array('date' => '', 'start_hour' => '', 'end_hour' => '', 'ticket_id' => ''));
                    }

                    echo '<div id="representations_container">';
                    foreach ($representations as $index => $representation) {
?>
                    <div class="representation">
                        <div class="fields">
                            <label for="representations[<?php echo $index; ?>][date]">Date:</label>
                            <input type="date" id="representations_<?php echo $index; ?>_date" name="representations[<?php echo $index; ?>][date]" value="<?php echo esc_attr($representation['date']); ?>" class="widefat">
                        </div>
                        <div class="fields">
                            <label for="representations[<?php echo $index; ?>][start_hour]">Start Time:</label>
                            <input type="time" id="representations_<?php echo $index; ?>_start_hour" name="representations[<?php echo $index; ?>][start_hour]" value="<?php echo esc_attr($representation['start_hour']); ?>" class="widefat">
                        </div>
                        <div class="fields">
                            <label for="representations[<?php echo $index; ?>][end_hour]">End Time:</label>
                            <input type="time" id="representations_<?php echo $index; ?>_end_hour" name="representations[<?php echo $index; ?>][end_hour]" value="<?php echo esc_attr($representation['end_hour']); ?>" class="widefat">
                        </div>
                        <div class="fields">
                            <label for="representations[<?php echo $index; ?>][ticket_id]">Ticket ID:</label>
                            <input type="text" id="representations_<?php echo $index; ?>_ticket_id" name="representations[<?php echo $index; ?>][ticket_id]" value="<?php echo esc_attr($representation['ticket_id']); ?>" class="widefat">
                        </div>
                        <button type="button" class="remove_representation button">Remove</button>
                    </div>
                <?php
                    }
                    echo '</div>';
                    echo '<button type="button" id="add_representation" class="button">Add Representation</button>';

                    // Here we include jQuery to handle the add/remove functionality
                ?>
                <script type="text/javascript">
                    jQuery(document).ready(function($) {
                        var container = $('#representations_container');
                        $('#add_representation').on('click', function() {
                            var new_index = container.children('.representation').length;
                            var new_field = `
                <div class="representation">
                    <div class="fields">
                        <label>Date:</label>
                        <input type="date" name="representations[` + new_index + `][date]" class="widefat">
                    </div>
                    <div class="fields">
                        <label>Start Time:</label>
                        <input type="time" name="representations[` + new_index + `][start_hour]" class="widefat">
                    </div>
                    <div class="fields">
                        <label>End Time:</label>
                        <input type="time" name="representations[` + new_index + `][end_hour]" class="widefat">
                    </div>
                    <div class="fields">
                        <label>Ticket ID:</label>
                        <input type="text" name="representations[` + new_index + `][ticket_id]" class="widefat">
                    </div>
                    <button type="button" class="remove_representation button">Remove</button>
                </div>`;
                            container.append(new_field);
                        });

                        // Use event delegation to handle the remove button click for both
                        // existing items and new items.
                        container.on('click', '.remove_representation', function() {
                            $(this).parent('.representation').remove();
                        });
                    });
                </script>
<?php
                }, // Callback function
                self::TYPE,      // Post type
                'normal',        // Context
                'default'        // Priority
            );
        });

        add_action('save_post', function ($post_id) {
            // Check nonce for security
            if (!isset($_POST['event_details_nonce']) || !wp_verify_nonce($_POST['event_details_nonce'], 'event_details_metabox')) {
                return;
            }

            // Check if the current user has permission to edit the post
            if (!current_user_can('edit_post', $post_id)) {
                return;
            }

            // Save the Group event ID
            if (isset($_POST['group_event_id'])) {
                update_post_meta($post_id, 'group_event_id', sanitize_text_field($_POST['group_event_id']));
            }

            // Save the Portal image URL
            if (isset($_POST['portal_image'])) {
                update_post_meta($post_id, 'portal_image', esc_url_raw($_POST['portal_image']));
            }

            // Save the representations
            if (isset($_POST['representations'])) {
                $representations = array();
                foreach ($_POST['representations'] as $representation) {
                    $representations[] = array(
                        'date' => sanitize_text_field($representation['date']),
                        'start_hour' => sanitize_text_field($representation['start_hour']),
                        'end_hour' => sanitize_text_field($representation['end_hour']),
                        'ticket_id' => sanitize_text_field($representation['ticket_id']),
                    );
                }
                update_post_meta($post_id, 'representations', $representations);
            }
        });
    }
}
