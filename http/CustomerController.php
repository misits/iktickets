<?php

namespace Iktickets\http;

use \WP_REST_Response;
use \WP_Error;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

class CustomerController extends IkController
{
    const ENUM_PARAMS_CREATE_USER = array(
        'civility',
        'firstname',
        'lastname',
        'email',
        'password',
        'language',
        'firm',
        'iktickets-phone',
        'address',
        'city',
        'zipcode',
        'country',
        "newsletter",
    );

    const ENUM_PARAMS_EDIT_USER = array(
        'civility',
        'firstname',
        'lastname',
        'email',
        'language',
        'firm',
        'iktickets-phone',
        'address',
        'city',
        'zipcode',
        'country',
        "newsletter",
        'custom',
    );

    const ENUM_LOGIN = array(
        'email',
        'password', // optional
    );

    /**
     * Register new user to Infomaniak Tickets
     * @param $request
     * @return WP_REST_Response|WP_Error
     */
    public function register_new_customer($request): WP_REST_Response|WP_Error
    {
        // Extract data from the request
        $params = $request->get_params();

        // Has correct parameters
        if (!empty($params)) {
            parent::checkParams($params, self::ENUM_PARAMS_CREATE_USER);
        }

        // Has correct civility
        if (!empty($params['civility'])) {
            parent::checkCivility($params['civility']);
        }

        // Has correct language
        if (!empty($params['language'])) {
            parent::checkLanguage($params['language']);
        }

        // Has correct country
        if (!empty($params['country'])) {
            parent::checkCountry($params['country']);
        }

        // Prepare the data for the API request
        $api_data = array(
            'civility'  => $params['civility'],
            'firstname' => $params['firstname'],
            'lastname'  => $params['lastname'],
            'email'     => $params['email'],
            'password'  => $params['password'],
            'language'  => $params['language'],
            'firm'      => $params['firm'],
            'iktickets-phone'     => $params['iktickets-phone'],
            'address'   => $params['address'],
            'city'      => $params['city'],
            'zipcode'   => $params['zipcode'],
            'country'   => $params['country'],
            'newsletter' => $params['newsletter'] ?? null,
        );

        // Filter out null values
        $api_data = parent::removeEmptyParams($api_data);

        // All fields are required but not 'firm'
        foreach ($api_data as $key => $value) {
            if (empty($value) && $key !== 'firm') {
                return new WP_Error('missing_field', 'Missing required field: ' . $key, array('status' => 400));
            }
        }

        // Use the parent::apiRequest() method to send the API request and get the response
        $response_data = parent::apiRequest('/customer/create', parent::LANG_FR, parent::CURRENCY_CHF, [], $api_data, 'POST');

        if (is_wp_error($response_data)) {
            return $response_data;  // Return the WP_Error
        }

        // Check data => error_code
        if (!empty($response_data['error_code'])) {
            return new WP_REST_Response(array('status' => 'error', 'message' => $response_data['error']), $response_data['error_code']);
        }

        // If no errors, return the response data wrapped in a WP_REST_Response
        return new WP_REST_Response(array('status' => 'success', 'customer' => $response_data, 'message' => 'User created successfully'), 200);
    }

    /**
     * Login user to Infomaniak Tickets
     * @param $request
     * @return WP_REST_Response|WP_Error
     */
    public function login_customer($request): WP_REST_Response|WP_Error
    {
        // Extract data from the request
        $params = $request->get_params();

        // Has correct parameters
        if (!empty($params)) {
            parent::checkParams($params, self::ENUM_LOGIN);
        }

        // Prepare the data for the API request
        $api_data = array(
            'email'    => $params['email'],
            'password' => $params['password'],
        );

        // Filter out null values
        $api_data = parent::removeEmptyParams($api_data);

        // All fields are required
        foreach ($api_data as $key => $value) {
            if (empty($value) && $key !== 'password') {
                return new WP_REST_Response(array('status' => 'error', 'message' => 'Missing required field: ' . $key), 400);
            }
        }

        // Use the parent::apiRequest() method to send the API request and get the response
        $response_data = parent::apiRequest('/customer/connexion', parent::LANG_FR, parent::CURRENCY_CHF, [], $api_data, 'POST');

        if (is_wp_error($response_data)) {
            return $response_data;  // Return the WP_Error
        }

        // Check data => error_code
        if (!empty($response_data['error_code'])) {
            return new WP_REST_Response(array('status' => 'error', 'message' => $response_data['error']), $response_data['error_code']);
        }


        // if only int return array(id => int)
        if (is_int($response_data['id'])) {
            return new WP_REST_Response(array('status' => 'success', 'customer' => $response_data, 'message' => 'Guest connected successfully'), 200);
        }

        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $customer = array(
            'email' => $params['email'],
            'token' => $response_data['access_token'],
            'expires_at' => $response_data['expires_at'],
        );

        // Save the token in the session as customer
        $_SESSION['customer'] = json_encode($customer);

        // Get customer details from API
        $customer_details = $this->get_current_customer_details();

        if (is_wp_error($customer_details)) {
            return $customer_details;  // Return the WP_Error
        }

        // If no errors, return the response data json wrapped in a WP_REST_Response
        return new WP_REST_Response(array('status' => 'success', 'customer' => $customer_details, 'message' => 'User connected successfully'), 200);
    }

    /**
     * Logout user from Infomaniak Tickets
     * @return void
     */
    public function logout_customer(): void
    {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Remove customer from session
        unset($_SESSION['customer']);

        // Destroy session
        session_destroy();

        // Redirect to home page with 302 status code and message
        wp_redirect(home_url() . '?logout=true', 302);

        exit;
    }

    /**
     * Get current auth user
     * @return array|WP_Error
     */
    public function get_current_customer_details(): array|WP_Error
    {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Check if session has customer token
        if (empty(json_decode($_SESSION['customer'])->token)) {
            return new WP_Error('infomaniak_api_error', 'No customer token in session', array('status' => 400));
        }

        $api_headers = array(
            'customer-email' => json_decode($_SESSION['customer'])->email,
            'customer-key' => json_decode($_SESSION['customer'])->token,
            'customer-token' => json_decode($_SESSION['customer'])->token,
        );

        // Use the parent::apiRequest() method to send the API request and get the response
        $response_data = parent::apiRequest('/customer', parent::LANG_FR, parent::CURRENCY_CHF, $api_headers, [], 'GET');

        if (is_wp_error($response_data)) {
            return $response_data;  // Return the WP_Error
        }

        // Check data => error_code
        if (!empty($response_data['error_code'])) {
            return new WP_REST_Response(array('status' => 'error', 'message' => $response_data['error']), $response_data['error_code']);
        }

        // Add to session['customer'] the whole response data already decoded to json
        $_SESSION['customer'] = json_encode(array_merge(json_decode($_SESSION['customer'], true), $response_data));

        // remove token from session
        $customer = json_decode($_SESSION['customer']);
        unset($customer->token);

        // return customer details as array (not json) or array in array
        return json_decode(json_encode($customer), true);
    }

    /**
     * Edit current auth user
     * @param $request
     * @return WP_REST_Response|WP_Error
     */
    public function edit_current_customer_details($request): WP_REST_Response|WP_Error
    {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Check if session has customer
        if (empty($_SESSION['customer'])) {
            return new WP_REST_Response(array('status' => 'error', 'message' => 'No customer in session'), 400);
        }

        // Extract data from the request
        $params = $request->get_params();

        // Prepare the data for the API request
        $api_data = array(
            'civility'  => $params['civility'] ?? null,
            'firstname' => $params['firstname'] ?? null,
            'lastname'  => $params['lastname'] ?? null,
            'email'     => $params['email'] ?? null,
            'password'  => $params['password'] ?? null,
            'language'  => $params['language'] ?? null,
            'firm'      => $params['firm'] ?? null,
            'iktickets-phone'     => $params['iktickets-phone'] ?? null,
            'address'   => $params['address'] ?? null,
            'city'      => $params['city'] ?? null,
            'zipcode'   => $params['zipcode'] ?? null,
            'country'   => $params['country'] ?? null,
            'custom'    => $params['custom'] ?? null,
        );

        // Filter out null values
        $api_data = parent::removeEmptyParams($api_data);

        // All fields are required
        foreach ($api_data as $key => $value) {
            if (empty($value)) {
                return new WP_Error('missing_field', 'Missing required field: ' . $key, array('status' => 400));
            }
        }

        $api_headers = array(
            'customer-email' => json_decode($_SESSION['customer'])->email,
            'customer-key' => json_decode($_SESSION['customer'])->token,
            'customer-token' => json_decode($_SESSION['customer'])->token,
        );

        // Use the parent::apiRequest() method to send the API request and get the response
        $response_data = parent::apiRequest('/customer', parent::LANG_FR, parent::CURRENCY_CHF, $api_headers, $api_data, 'PUT');

        if (is_wp_error($response_data)) {
            return $response_data;  // Return the WP_Error
        }

        // Check data => error_code
        if (!empty($response_data['error_code'])) {
            return new WP_REST_Response(array('status' => 'error', 'message' => $response_data['error']), $response_data['error_code']);
        }

        // Update session['customer'] with the new data
        $_SESSION['customer'] = json_encode(array_merge(json_decode($_SESSION['customer'], true), $response_data));

        // remove token from session
        $customer = json_decode($_SESSION['customer']);
        unset($customer->token);

        // return response
        return new WP_REST_Response(json_decode(json_encode($customer), true), 200);
    }
}
