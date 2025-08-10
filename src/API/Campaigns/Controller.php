<?php
namespace WP2Lead\API\Campaigns;

use WP2Lead\Services\Campaigns\Provider as CampaignsService;
use WP_REST_Controller;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

/**
 * REST API Controller for WP2 Lead Campaigns.
 *
 * @package WP2Lead\API\Campaigns
 * @since 1.0.0
 */
class Controller extends WP_REST_Controller implements \WP2Lead\Interfaces\REST\Controller {
	/**
	 * Campaigns service provider.
	 *
	 * @var CampaignsService
	 */
	protected $service;

	/**
	 * Constructor with dependency injection for CampaignsService Provider.
	 *
	 * @param CampaignsService|null $service
	 */
	public function __construct( $service = null ) {
		$this->service = $service ?: new CampaignsService();
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Registers REST API routes for campaigns.
	 *
	 * @return void
	 */
	public function register_routes(): void {
		register_rest_route(
			'wp2-lead/v1',
			'/campaigns/(?P<id>\d+)',
			[ 
				'methods' => WP_REST_Server::READABLE,
				'callback' => [ $this, 'get_campaign' ],
				'permission_callback' => [ $this, 'permission' ],
				'args' => [ 
					'id' => [ 
						'validate_callback' => function ($param) {
							return is_numeric( $param );
						},
					],
				],
			]
		);
	}

	/**
	 * Permission callback for campaign endpoint.
	 * Only allow if post is published and nonce is valid.
	 *
	 * @param WP_REST_Request $request
	 * @return bool
	 */
	public function permission( $request ): bool {
		$id = isset( $request['id'] ) ? intval( $request['id'] ) : 0;
		return $this->service->can_view_campaign( $id, $request->get_header( 'X-WP-Nonce' ) );
	}

	/**
	 * Get campaign data by ID.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_campaign( $request ) {
		$id = intval( $request['id'] );
		$result = $this->service->get_campaign_data( $id );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( ! isset( $result['campaign'] ) || ! $result['campaign'] ) {
			return new WP_Error(
				'not_found',
				__( 'Campaign not found', 'wp2-lead' ),
				[ 'status' => 404 ]
			);
		}

		return rest_ensure_response( $result );
	}
}
