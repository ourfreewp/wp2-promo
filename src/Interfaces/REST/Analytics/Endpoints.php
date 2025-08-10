<?php

namespace WP2Lead\Interfaces\REST\Analytics;
use WP2Lead\Services\Analytics\Service as AnalyticsService;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;
if ( ! defined( 'ABSPATH' ) ) exit;


class Endpoints {
    protected $service;
    protected $namespace = 'wp2-lead/v1';
    protected $rest_base = 'analytics';

    public function __construct( $service = null ) {
        $this->service = $service ?: new AnalyticsService();
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    public function register_routes(): void {
        $controller = new \WP2Lead\API\Analytics\Controller();
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            [
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => [ $controller, 'record_event' ],
                'permission_callback' => [ $this, 'record_event_permissions_check' ],
                'args' => $controller->get_record_event_args(),
            ]
        );
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/aggregated',
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [ $controller, 'get_aggregated_data' ],
                'permission_callback' => [ $this, 'get_aggregated_data_permissions_check' ],
                'args' => $controller->get_aggregated_data_args(),
            ]
        );
    }

}
