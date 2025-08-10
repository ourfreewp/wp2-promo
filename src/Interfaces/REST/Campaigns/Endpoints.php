<?php
namespace WP2Lead\Interfaces\REST\Campaigns;
use WP2Lead\Services\Campaigns\Service as CampaignsService;
use WP_Error;
if ( ! defined( 'ABSPATH' ) ) exit;

class Endpoints {
    protected $namespace = 'wp2-lead/v1';
    protected $rest_base = 'campaigns';
    protected $service;

    public function __construct( $service = null ) {
        $this->service = $service ?: new CampaignsService();
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    public function register_routes(): void {
        $controller = new \WP2Lead\API\Campaigns\Controller();
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<id>\d+)',
            [
                'methods' => 'GET',
                'callback' => [ $controller, 'get_campaign' ],
                'permission_callback' => [ $controller, 'permission' ],
            ]
        );
    }
}
