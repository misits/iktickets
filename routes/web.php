<?php

namespace Iktickets\routes;

use Iktickets\http\CustomerController;
use Iktickets\http\TicketController;
use Iktickets\http\EventController;
use Iktickets\http\OrderController;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

add_action('rest_api_init', function() {
    error_log('Registering Iktickets routes');

    $customerController = new CustomerController();
    $eventController = new EventController();
    $ticketController = new TicketController();
    $orderController = new OrderController();

    // Define the namespace
    $namespace = 'api/v1/iktickets/';

    /**
     * CUSTOMERS
     */
    register_rest_route($namespace, '/customer/create', array(
        'methods' => 'POST',
        'callback' => array($customerController, 'register_new_customer'),
    ));

    register_rest_route($namespace, '/customer/connexion', array(
        'methods' => 'POST',
        'callback' => array($customerController, 'login_customer'),
    ));

    register_rest_route($namespace, '/customer/logout', array(
        'methods' => 'POST',
        'callback' => array($customerController, 'logout_customer'),
    ));

    register_rest_route($namespace, '/customer', array(
        'methods' => 'PUT',
        'callback' => array($customerController, 'edit_current_customer_details'),
    ));

    register_rest_route($namespace, '/customer', array(
        'methods' => 'GET',
        'callback' => array($customerController, 'get_current_customer_details'),
    ));

    /**
     * ORDERS
     */
    register_rest_route($namespace, '/order/create', array(
        'methods' => 'POST',
        'callback' => array($orderController, 'create_order'),
    ));

    register_rest_route($namespace, '/order/(?P<order_id>\d+)/tickets', array(
        'methods' => 'POST',
        'callback' => array($orderController, 'add_tickets_to_order'),
    ));

    register_rest_route($namespace, '/order/(?P<order_id>\d+)/tickets', array(
        'methods' => 'PUT',
        'callback' => array($orderController, 'edit_tickets_in_order'),
    ));

    register_rest_route($namespace, '/order/(?P<order_id>\d+)/cancel-operation/{reference}', array(
        'methods' => 'PUT',
        'callback' => array($orderController, 'cancel_operation'),
    ));

    register_rest_route($namespace, '/order/(?P<order_id>\d+)/valid', array(
        'methods' => 'PUT',
        'callback' => array($orderController, 'valid_free_order'),
    ));

    register_rest_route($namespace, '/order/(?P<order_id>\d+)/customer', array(
        'methods' => 'POST',
        'callback' => array($orderController, 'add_customer_to_order'),
    ));

    register_rest_route($namespace, '/order/(?P<order_id>\d+)/operations', array(
        'methods' => 'POST',
        'callback' => array($orderController, 'add_operations_to_order'),
    ));

    register_rest_route($namespace, '/order/(?P<order_id>\d+)/', array(
        'methods' => 'GET',
        'callback' => array($orderController, 'get_order_by_id'),
    ));

    register_rest_route($namespace, '/order/(?P<order_id>\d+)/', array(
        'methods' => 'DELETE',
        'callback' => array($orderController, 'delete_prebooked_order'),        
    ));

    register_rest_route($namespace, '/order/(?P<order_id>\d+)/payments', array(
        'methods' => 'GET',
        'callback' => array($orderController, 'get_order_payments'),
    ));

    register_rest_route($namespace, '/order/(?P<order_id>\d+)/payment', array(
        'methods' => 'GET',
        'callback' => array($orderController, 'get_bank_payment_form'),
    ));


    /**
     * TICKETS
     */
    register_rest_route($namespace, '/tickets', array(
        'methods' => 'GET',
        'callback' => array($ticketController, 'get_all_tickets'),
    ));

    register_rest_route($namespace, '/ticket/(?P<ticket_id>\d+)', array(
        'methods' => 'GET',
        'callback' => array($ticketController, 'get_ticket_by_id')
    ));

    register_rest_route($namespace, '/tickets/sync', array(
        'methods' => 'GET',
        'callback' => array($ticketController, 'sync_tickets'),
    ));

    /**
     * EVENTS
     */
    register_rest_route($namespace, '/events', array(
        'methods' => 'GET',
        'callback' => array($eventController, 'get_all_events'),
    ));

    register_rest_route($namespace, '/event/(?P<event_id>\d+)', array(
        'methods' => 'GET',
        'callback' => array($eventController, 'get_event_by_id')
    ));

    register_rest_route($namespace, '/event/(?P<event_id>\d+)/zones', array(
        'methods' => 'GET',
        'callback' => array($eventController, 'get_event_zone')
    ));

    register_rest_route($namespace, '/event/(?P<event_id>\d+)/realfreeseats', array(
        'methods' => 'GET',
        'callback' => array($eventController, 'get_event_realfreeseats')
    ));
});

