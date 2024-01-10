<?php

namespace Iktickets\http;

use \WP_REST_Response;
use \WP_Error;

class OrderController extends IkController
{
    const ENUM_PAYMENT_MODE = array(
        'card', // card
        'twint', // twint
        'postfinance', // postfinance
    );
    const ENUM_PAYMENT = array(
        'mode', // payment mode
        'url_default', // return page
        'url_ok', // confirmation page
        'url_error', // error page
        'locale', // language (fr-CH, de-CH, it-CH)
    );

    /**
     * Create order
     * @param $request
     * @return WP_REST_Response|WP_Error
     */
    public function create_order($request): WP_REST_Response|WP_Error
    {
        // Use the parent::apiRequest() method to send the API request and get the response
        $response_data = parent::apiRequest('/order/create', parent::LANG_FR, parent::CURRENCY_CHF, [], [], 'POST');

        if (is_wp_error($response_data)) {
            return $response_data;  // Return the WP_Error
        }

        // Check data => error_code
        if (!empty($response_data['error_code'])) {
            return new WP_REST_Response(array('status' => 'error', 'message' => $response_data['error']), $response_data['error_code']);
        }

        // If no errors, return the response data wrapped in a WP_REST_Response
        return new WP_REST_Response(array('status' => 'success', 'order_id' => $response_data, 'message' => "Order created successfully"), 200);
    }

    /**
     * Add tickets to order
     * @param $request
     * @return WP_REST_Response|WP_Error
     */
    public function add_tickets_to_order($request): WP_REST_Response|WP_Error
    {
        // Get the order id from the request
        $order_id = $request->get_param('order_id');

        // Check if the order id is valid
        if (empty($order_id) || !is_numeric($order_id)) {
            return new WP_REST_Response(array('status' => 'error', 'message' => 'Invalid order id'), 400);
        }

        // Get the request body
        $body = json_decode($request->get_body(), true);

        // Use the parent::apiRequest() method to send the API request and get the response
        $response_data = parent::apiRequest('/order/' . $order_id . '/tickets', parent::LANG_FR, parent::CURRENCY_CHF, [], $body, 'POST');

        if (is_wp_error($response_data)) {
            return $response_data;  // Return the WP_Error
        }

        // Check data => error_code
        if (!empty($response_data['error_code'])) {
            return new WP_REST_Response(array('status' => 'error', 'message' => $response_data['error']), $response_data['error_code']);
        }

        // If no errors, return the response data wrapped in a WP_REST_Response
        return new WP_REST_Response(array('status' => 'success', 'order' => $response_data, 'message' => "Tickets added to order successfully"), 200);
    }

    /**
     * Edit tickets in order
     * @param $request
     * @return WP_REST_Response|WP_Error
     */
    public function edit_tickets_in_order($request): WP_REST_Response|WP_Error
    {
        // Get the order id from the request
        $order_id = $request->get_param('order_id');

        // Check if the order id is valid
        if (empty($order_id) || !is_numeric($order_id)) {
            return new WP_REST_Response(array('status' => 'error', 'message' => 'Invalid order id'), 400);
        }

        // Get the request body
        $body = json_decode($request->get_body(), true);

        // Use the parent::apiRequest() method to send the API request and get the response
        $response_data = parent::apiRequest('/order/' . $order_id . '/tickets', parent::LANG_FR, parent::CURRENCY_CHF, [], $body, 'PUT');

        if (is_wp_error($response_data)) {
            return $response_data;  // Return the WP_Error
        }

        // Check data => error_code
        if (!empty($response_data['error_code'])) {
            return new WP_REST_Response(array('status' => 'error', 'message' => $response_data['error']), $response_data['error_code']);
        }

        // If no errors, return the response data wrapped in a WP_REST_Response
        return new WP_REST_Response(array('status' => 'success', 'order' => $response_data, 'message' => "Tickets edited in order successfully"), 200);
    }

    /**
     * Get order payments
     * @param $request
     * @return WP_REST_Response|WP_Error
     */

    public function get_order_payments($request): WP_REST_Response|WP_Error
    {
        // Get the order id from the request
        $order_id = $request->get_param('order_id');

        // Check if the order id is valid
        if (empty($order_id) || !is_numeric($order_id)) {
            return new WP_REST_Response(array('status' => 'error', 'message' => 'Invalid order id'), 400);
        }

        // Use the parent::apiRequest() method to send the API request and get the response
        $response_data = parent::apiRequest('/order/' . $order_id . '/payments', parent::LANG_FR, parent::CURRENCY_CHF, [], [], 'GET');

        if (is_wp_error($response_data)) {
            return $response_data;  // Return the WP_Error
        }

        // Check data => error_code
        if (!empty($response_data['error_code'])) {
            return new WP_REST_Response(array('status' => 'error', 'message' => $response_data['error']), $response_data['error_code']);
        }

        // If no errors, return the response data wrapped in a WP_REST_Response
        return new WP_REST_Response(array('status' => 'success', 'payments' => $response_data, 'message' => "Payments retrieved successfully"), 200);
    }

    /**
     * Add operations to order
     * @param $request
     * @return WP_REST_Response|WP_Error
     */
    public function add_operations_to_order($request): WP_REST_Response|WP_Error
    {
        // Get the order id from the request
        $order_id = $request->get_param('order_id');

        // Check if the order id is valid
        if (empty($order_id) || !is_numeric($order_id)) {
            return new WP_REST_Response(array('status' => 'error', 'message' => 'Invalid order id'), 400);
        }

        // Get the request body
        $body = json_decode($request->get_body(), true);

        // Use the parent::apiRequest() method to send the API request and get the response
        $response_data = parent::apiRequest('/order/' . $order_id . '/operations', parent::LANG_FR, parent::CURRENCY_CHF, [], $body, 'POST');

        if (is_wp_error($response_data)) {
            return $response_data;  // Return the WP_Error
        }

        // Check data => error_code
        if (!empty($response_data['error_code'])) {
            return new WP_REST_Response(array('status' => 'error', 'message' => $response_data['error']), $response_data['error_code']);
        }

        // If no errors, return the response data wrapped in a WP_REST_Response
        return new WP_REST_Response(array('status' => 'success', 'order' => $response_data, 'message' => "Operations added to order successfully"), 200);
    }

    /**
     * Add customer to order
     * @param $request
     * @return WP_REST_Response|WP_Error
     */
    public function add_customer_to_order($request): WP_REST_Response|WP_Error
    {
        // Get the order id from the request
        $order_id = $request->get_param('order_id');

        // Check if the order id is valid
        if (empty($order_id) || !is_numeric($order_id)) {
            return new WP_REST_Response(array('status' => 'error', 'message' => 'Invalid order id'), 400);
        }

        // Get the request body
        $body = json_decode($request->get_body(), true);

        // Use the parent::apiRequest() method to send the API request and get the response
        $response_data = parent::apiRequest('/order/' . $order_id . '/customer', parent::LANG_FR, parent::CURRENCY_CHF, [], $body, 'POST');

        if (is_wp_error($response_data)) {
            return $response_data;  // Return the WP_Error
        }

        // Check data => error_code
        if (!empty($response_data['error_code'])) {
            return new WP_REST_Response(array('status' => 'error', 'message' => $response_data['error']), $response_data['error_code']);
        }

        // If no errors, return the response data wrapped in a WP_REST_Response
        return new WP_REST_Response(array('status' => 'success', 'order' => $response_data, 'message' => "Customer added to order successfully"), 200);
    }

    /**
     * Get bank payment form
     * @param $request
     * @return WP_REST_Response|WP_Error
     */
    public function get_bank_payment_form($request): WP_REST_Response|WP_Error
    {
        $paiements_url = array();

        // Get the order id from the request
        $order_id = $request->get_param('order_id');

        // Check if the order id is valid
        if (empty($order_id) || !is_numeric($order_id)) {
            return new WP_REST_Response(array('status' => 'error', 'message' => 'Invalid order id'), 400);
        }

        // Use the parent::apiRequest() method to send the API request and get the response for each self::ENUM_PAYMENT_MODE
        foreach (self::ENUM_PAYMENT_MODE as $payment_mode) {
            $base_url = parent::getBaseUrl();

            if (str_contains($base_url, 'hawai.li')) {
                $base_url = parent::getBaseUrl() . '/' . parent::PROJECT_NAME;
            }

            $params = array(
                "mode" => $payment_mode,
                "url_default" => $base_url . parent::URL_DEFAULT,
                "url_ok" => $base_url . parent::URL_OK,
                "url_error" => $base_url . parent::URL_ERROR,
                "locale" => $base_url . parent::LOCALE,
            );
            
            // Use the parent::apiRequest() method to send the API request and get the response
            $response_data = parent::apiRequest('/order/' . $order_id . '/payment?' . http_build_query($params), parent::LANG_FR, parent::CURRENCY_CHF, [], [], 'GET');
            
            if (is_wp_error($response_data)) {
                continue;  // Return the WP_Error
            }

            // If no errors, return the response data wrapped in a WP_REST_Response
            $paiements_url[$payment_mode] = $response_data;
        }

        if (empty($paiements_url)) {
            return new WP_REST_Response(array('status' => 'error', 'message' => 'No payment url found'), 400);
        }

        // If no errors, return the response data wrapped in a WP_REST_Response
        return new WP_REST_Response(array('status' => 'success', 'urls' => $paiements_url, 'message' => "Payment urls retrieved successfully"), 200);
    }

    /**
     * Valid free order
     * @param $request
     * @return WP_REST_Response|WP_Error
     */
    public function valid_free_order($request): WP_REST_Response|WP_Error
    {
        // Get the order id from the request
        $order_id = $request->get_param('order_id');

        // Check if the order id is valid
        if (empty($order_id) || !is_numeric($order_id)) {
            return new WP_REST_Response(array('status' => 'error', 'message' => 'Invalid order id'), 400);
        }

        // Get the request body
        $body = json_decode($request->get_body(), true);

        // Use the parent::apiRequest() method to send the API request and get the response
        $response_data = parent::apiRequest('/order/' . $order_id . '/valid', parent::LANG_FR, parent::CURRENCY_CHF, [], $body, 'PUT');

        if (is_wp_error($response_data)) {
            return $response_data;  // Return the WP_Error
        }

        // Check data => error_code
        if (!empty($response_data['error_code'])) {
            return new WP_REST_Response(array('status' => 'error', 'message' => $response_data['error']), $response_data['error_code']);
        }

        // If no errors, return the response data wrapped in a WP_REST_Response
        return new WP_REST_Response(array('status' => 'success', 'order' => $response_data, 'message' => "Order validated successfully"), 200);
    }

    /**
     * Cancel operation
     * @param $request
     * @return WP_REST_Response|WP_Error
     */
    public function cancel_operation($request): WP_REST_Response|WP_Error
    {
        // Get the order id from the request
        $order_id = $request->get_param('order_id');
        $reference = $request->get_param('reference');

        // Check if the order id is valid
        if (empty($order_id) || !is_numeric($order_id) || empty($reference)) {
            return new WP_REST_Response(array('status' => 'error', 'message' => 'Invalid order id or reference'), 400);
        }

        // Get the request body
        $body = json_decode($request->get_body(), true);

        // Use the parent::apiRequest() method to send the API request and get the response
        $response_data = parent::apiRequest('/order/' . $order_id . '/cancel-operation/' . $reference, parent::LANG_FR, parent::CURRENCY_CHF, [], $body, 'PUT');

        if (is_wp_error($response_data)) {
            return $response_data;  // Return the WP_Error
        }

        // Check data => error_code
        if (!empty($response_data['error_code'])) {
            return new WP_REST_Response(array('status' => 'error', 'message' => $response_data['error']), $response_data['error_code']);
        }

        // If no errors, return the response data wrapped in a WP_REST_Response
        return new WP_REST_Response(array('status' => 'success', 'cancel' => $response_data, 'message' => "Operation canceled successfully"), 200);
    }

    /**
     * Get order by id
     * @param $request
     * @return WP_REST_Response|WP_Error
     */
    public function get_order_by_id($request): WP_REST_Response|WP_Error
    {
        // Get the order id from the request
        $order_id = $request->get_param('order_id');

        // Check if the order id is valid
        if (empty($order_id) || !is_numeric($order_id)) {
            return new WP_REST_Response(array('status' => 'error', 'message' => 'Invalid order id'), 400);
        }

        // Use the parent::apiRequest() method to send the API request and get the response
        $response_data = parent::apiRequest('/order/' . $order_id, parent::LANG_FR, parent::CURRENCY_CHF, [], [], 'GET');

        if (is_wp_error($response_data)) {
            return $response_data;  // Return the WP_Error
        }

        // If no errors, return the response data wrapped in a WP_REST_Response
        return new WP_REST_Response(array('status' => 'success', 'order' => $response_data, 'message' => "Order retrieved successfully"), 200);
    }

    /**
     * Delete order by id
     * @param $request
     * @return WP_REST_Response|WP_Error
     */
    public function delete_prebooked_order($request): WP_REST_Response|WP_Error
    {
        // Get the order id from the request
        $order_id = $request->get_param('order_id');

        // Check if the order id is valid
        if (empty($order_id) || !is_numeric($order_id)) {
            return new WP_REST_Response(array('status' => 'error', 'message' => 'Invalid order id or reference'), 400);
        }

        // Use the parent::apiRequest() method to send the API request and get the response
        $response_data = parent::apiRequest('/order/' . $order_id, parent::LANG_FR, parent::CURRENCY_CHF, [], [], 'DELETE');

        if (is_wp_error($response_data)) {
            return $response_data;  // Return the WP_Error
        }

        // If no errors, return the response data wrapped in a WP_REST_Response
        return new WP_REST_Response(array('status' => 'success', 'delete' => $response_data, 'message' => "Order deleted successfully"), 200);
    }
}
