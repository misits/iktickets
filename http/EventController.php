<?php

namespace Iktickets\http;

use Iktickets\models\Event;
use Iktickets\models\EventCategory;
use Iktickets\http\IkController;
use \Error;
use \WP_REST_Response;
use \WP_Error;
use \WP_Query;

class EventController extends IkController
{
    const HOOK_NAME = "sync-iktickets_events";
    
    const ENUM_PARAMS = array(
        'ids',
        'search',
        'limit',
        'offset',
        'withQuota',
        'withProperties',
        'sort',
    );

    const ENUM_SORT = array(
        'id',
        'date',
        'name',
        'end'
    );

    const VISIBLE_CUSTOMER = "EN VENTE";
    const HIDDEN_CUSTOMER = "CACHÉ CLIENT";

    /**
     * Register
     */
    public static function register()
    {
        self::scheduledSync();
    }

    public static function scheduledSync()
    {
        // Create a scheduled event every hour
        add_action(self::HOOK_NAME, [self::class, "register_events"]);
        if (!wp_next_scheduled(self::HOOK_NAME)) {
            wp_schedule_event(time(), 'weekly', self::HOOK_NAME);
        }
    }

    /**
     * Register events 
     */
    public static function register_events()
    {
        // Implement a locking mechanism to prevent concurrent execution
        $lockFile = IKTICKETS_DIR . '/lockfile.txt';
        $fileHandle = fopen($lockFile, 'w');

        if (flock($fileHandle, LOCK_EX | LOCK_NB)) {
            // You have obtained the lock, proceed with synchronization
            error_log("Syncing events start (cron job)");
            $status = EventController::all_event_json();
            if ($status && array_key_exists('status', $status) && $status['status'] == 'error') {
                flock($fileHandle, LOCK_UN);
                unlink($lockFile);
                error_log("Syncing events end (cron job)");
                return $status;
            }

            EventController::register_representations();
            EventController::register_thumbnail();
            flock($fileHandle, LOCK_UN); // Release the lock when done
            unlink($lockFile);
            error_log("Syncing events end (cron job)");
        } else {
            // Another instance of the cron job is already running, exit gracefully
            fclose($fileHandle);
            exit;
        }

        fclose($fileHandle);
    }


    /**
     * Return all events json
     * @param $request
     */

    public static function all_event_json()
    {
        error_log("Syncing events");
        $events = new EventController();
        // empty request
        $request = new \WP_REST_Request();
        $response = $events->get_all_events($request);
        // check for errors
        if (is_wp_error($response)) {
            error_log($response->get_error_message());
            return;
        }
        $data = $response->get_data();

        if ($data && array_key_exists('status', $data) && $data['status'] == 'error') {
            error_log($data['message']);
            return $data;
        }

        $events = [];
        
        foreach ($data as $event) {
            $post_id = null;
            $slug = sanitize_title($event['name']);
            $events[$slug][$event['event_id']] = $event;
            // Check if post exists
            $post = new \WP_Query(array(
                'post_type' => Event::TYPE,
                'posts_per_page' => 1,
                'name' => $slug,
                'post_status' => array('publish', 'draft')
            ));

            $args = array(
                'post_title' => $event['name'],
                'post_type' => Event::TYPE,
                'post_status' => $event['status_label']['value'] == self::VISIBLE_CUSTOMER ? 'publish' : 'draft',
            );
               
            // If post doesn't exist, create it
            $posts = $post->get_posts();
            if (!$posts) {
                $post_id = wp_insert_post($args);
                update_post_meta($post_id, 'group_event_id', $event['group_event_id']);
                $image_url = $event['portal_horizontal_big'] ?? $event['portal'];
                // Remove all after ? in url
                $image_url = explode('?', $image_url)[0];
                update_post_meta($post_id, 'portal_image', $image_url);
            } else {
                // get the post
                $post_id = $posts[0]->ID;
                // update the post
                wp_update_post(array(
                    'ID' => $post_id,
                    'post_title' => $event['name'],
                    'post_name' => $slug,
                    'post_type' => Event::TYPE,
                    'post_status' => $event['status_label']['value'] == self::VISIBLE_CUSTOMER ? 'publish' : 'draft',
                ));
                $image_url = $event['portal_horizontal_big'] ?? $event['portal'];
                // Remove all after ? in url
                $image_url = explode('?', $image_url)[0];
                update_post_meta($post_id, 'portal_image', $image_url);
            }

            // category by name
            $category = get_term_by('name', $event['category'], EventCategory::TYPE);

            if ($category) {
                wp_set_post_terms($post_id, $category->term_id, EventCategory::TYPE);
            }
        }

        // add last update date
        $events['last_update'] = date_i18n('Y-m-d H:i:s');

        // save to wordpress option table as json
        $json_data = json_encode($events);
        // clear the option first
        delete_option(parent::WP_OPTION_EVENTS);
        update_option(parent::WP_OPTION_EVENTS, htmlspecialchars($json_data, ENT_QUOTES, 'UTF-8'));
    }

    /**
     * Register custom post type for each event as CustomPostType Event
     */

     public static function register_representations() {
        // Get events from the WordPress option
        $events_encoded = get_option(parent::WP_OPTION_EVENTS);
        $events_json = htmlspecialchars_decode($events_encoded);
        $events = json_decode($events_json, true);
        
        $ticketIdLookup = [];
    
        foreach ($events as $slug => $items) {
            // Get Event by slug
            $find = get_page_by_path($slug, OBJECT, Event::TYPE);
            if (!$find) continue;
    
            $event = new Event($find->ID);
            $representations = $event->meta('representations');
    
            // Initialize as an empty array if it's null
            $representations = $representations ?? [];
    
            // Create a lookup map for ticket IDs
            foreach ($representations as &$representation) {
                $ticketIdLookup[$representation['ticket_id']] = &$representation;
            }
    
            foreach ($items as $item) {
                $ticketId = $item['event_id'];
    
                if (isset($ticketIdLookup[$ticketId])) {
                    $ticketIdLookup[$ticketId]['date'] = date_i18n('Y-m-d', strtotime($item['date']));
                    $ticketIdLookup[$ticketId]['start'] = date_i18n('H:i', strtotime($item['date']));
                    $ticketIdLookup[$ticketId]['end'] = date_i18n('H:i', strtotime($item['end'] ?? "00:00:00"));
                } else {
                    $representations[] = [
                        'date' => date_i18n('Y-m-d', strtotime($item['date'])),
                        'start_hour' => date_i18n('H:i', strtotime($item['date'])),
                        'end_hour' => date_i18n('H:i', strtotime($item['end'] ?? "00:00:00")),
                        'ticket_id' => $ticketId
                    ];
                }
            }
            update_post_meta($event->id(), 'representations', $representations);
        }
    }

    /**
     * Register thumbnail for each event
     */

    public static function register_thumbnail()
    {
        // Get all events
        $events = Event::all();

        if (empty($events)) {
            return;
        }

        foreach ($events as $event) {
            $filename = basename($event->meta('portal_image'));

            // if no thumbnail, set it
            if (!has_post_thumbnail($event->id())) {
                error_log("Setting thumbnail for event ID: " . $event->id());
                parent::set_featured_image_from_url($event->id(), $event->meta('portal_image'), $filename);
            } else {
                error_log("Thumbnail already exists for event ID: " . $event->id());
            }
        }
    }

    /**
     * Get all events
     * @return WP_REST_Response | WP_Error
     */
    public function get_all_events($request): WP_REST_Response|WP_Error
    {
        // Get the parameters from the request
        $params = $request->get_params();


        // Has correct parameters
        if (!empty($params)) {
            parent::checkParams($params, self::ENUM_PARAMS);
        }

        // Has correct sort parameter
        if (isset($params['sort'])) {
            parent::checkSort($params['sort'], self::ENUM_SORT);
        }

        // Define the API request body
        $api_data = array(
            'ids'             => $params['ids'] ?? null,
            'search'          => $params['search'] ?? null,
            'limit'           => $params['limit'] ?? null,
            'offset'          => $params['offset'] ?? null,
            'withQuota'       => $params['withQuota'] ?? true,
            'withProperties'  => $params['withProperties'] ?? true,
            'sort'            => $params['sort'] ?? null,
        );

        // Filter out null values
        $api_data = parent::removeEmptyParams($api_data);

        // Use the parent::apiRequest() method to send the API request and get the response
        $response_data = parent::apiRequest('/events', parent::LANG_FR, parent::CURRENCY_CHF, [], $api_data, 'GET');

        // Check for errors
        if (is_wp_error($response_data)) {
            return $response_data;  // Return the WP_Error
        }

        // Check data => error_code
        if (!empty($response_data['error_code'])) {
            return new WP_REST_Response(array('status' => 'error', 'message' => $response_data['error']), $response_data['error_code']);
        }

        // If no errors, return the response data wrapped in a WP_REST_Response
        return new WP_REST_Response($response_data, 200);
    }

    /**
     * Get event tickets
     * @param $request
     * @return WP_REST_Response | WP_Error
     */
    public function get_event_by_id($request): WP_REST_Response|WP_Error
    {
        // Parameters from the url
        $event_id = $request->get_param('event_id');

        $response_data = parent::apiRequest('/event/' . $event_id, parent::LANG_FR, parent::CURRENCY_CHF, [], [], 'GET');

        // Check for errors
        if (is_wp_error($response_data)) {
            return $response_data;  // Return the WP_Error
        }

        // Get zone
        $zone = parent::apiRequest('/event/' . $event_id . '/zones', parent::LANG_FR, parent::CURRENCY_CHF, [], [], 'GET');

        // Add to response
        $response_data['zone'] = $zone;

        // If no errors, return the response data wrapped in a WP_REST_Response
        return new WP_REST_Response($response_data, 200);
    }

    /**
     * Get event zones
     * @param $request
     * @return WP_REST_Response | WP_Error
     */
    public function get_event_zone($request): WP_REST_Response|WP_Error
    {
        // Parameters from the url
        $event_id = $request->get_param('event_id');

        $response_data = parent::apiRequest('/event/' . $event_id . '/zones', parent::LANG_FR, parent::CURRENCY_CHF, [], [], 'GET');

        // Check for errors
        if (is_wp_error($response_data)) {
            return $response_data;  // Return the WP_Error
        }

        // If no errors, return the response data wrapped in a WP_REST_Response
        return new WP_REST_Response($response_data, 200);
    }

    /**
     * Get event realfreeseats
     * @param $request
     * @return WP_REST_Response | WP_Error
     */

     public function get_event_realfreeseats($request) : WP_REST_Response|WP_Error
    {
        $event_id = $request->get_param('event_id');
        $response_data = parent::apiRequest('/event/' . $event_id . '/realfreeseats', parent::LANG_FR, parent::CURRENCY_CHF, [], [], 'GET');
        // Check for errors
        if (is_wp_error($response_data)) {
            return $response_data;  // Return the WP_Error
        }

        // If no errors, return the response data wrapped in a WP_REST_Response
        return new WP_REST_Response($response_data, 200);
    }
}
