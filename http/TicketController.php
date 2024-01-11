<?php

namespace Iktickets\http;

use Iktickets\http\EventController;
use \WP_REST_Response;
use \WP_Error;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

class TicketController extends IkController
{
    const ENUM_PARAMS = array(
        'ids',
        'search',
        'date_ids',
        'updated_date',
        'begin',
        'end',
        'limit',
        'offset',
        'sort',
    );

    const ENUM_SORT = array(
        'barcode',
        'ticket_id',
        'order_id',
        'updated_at',
        'pass_id',
        'lastname',
        'firstname',
        'email',
        'firm',
        'pass_lastname',
        'pass_firstname',
        'pass_email',
        'pass_firm',
    );

    /**
     * Get all tickets
     * @param $request
     * @return WP_REST_Response|WP_Error
     */

    public function get_all_tickets($request): WP_REST_Response|WP_Error
    {
        // Get the parameters from the request
        $params = $request->get_params();

        // Has access_token for logged in user
        if (!isset($params['access_token'])) {
            return new WP_Error('missing_parameter', 'Missing parameter: access_token', array('status' => 400));
        }

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
            'withQuota'       => $params['withQuota'] ?? null,
            'withProperties'  => $params['withProperties'] ?? null,
            'sort'            => $params['sort'] ?? null,
        );

        // Filter out null values
        $api_data = array_filter($api_data, function($value) {
            return !is_null($value);
        });

        // Use the parent::apiRequest() method to send the API request and get the response
        $response_data = parent::apiRequest(
            '/tickets',
            parent::LANG_FR,
            parent::CURRENCY_CHF,
            array('Credential' => $params['access_token']),
            $api_data,
            'GET'
        );

        // Check for errors
        if (is_wp_error($response_data)) {
            return $response_data;  // Return the WP_Error
        }

        return new WP_REST_Response($response_data, 200);
    }

    /**
     * Purchase a ticket
     * @param $request
     * @return WP_REST_Response | WP_Error
     */
    public function get_ticket_by_id($request): WP_REST_Response|WP_Error
    {
        $ticket_id = $request->get_param('ticket_id');

        $response_data = parent::apiRequest('/ticket/' . $ticket_id, parent::LANG_FR, parent::CURRENCY_CHF, [], [], 'GET');

        // Check for errors
        if (is_wp_error($response_data)) {
            return $response_data;  // Return the WP_Error
        }

        // If no errors, return the response data wrapped in a WP_REST_Response
        return new WP_REST_Response($response_data, 200);
    }

    /**
     * Sync tickets from API
     */
    public function sync_tickets($request): WP_REST_Response|WP_Error
    {
        EventController::register_events();

        return new WP_REST_Response('Tickets synced at ' . date('Y-m-d H:i:s'), 200);
    }

}
