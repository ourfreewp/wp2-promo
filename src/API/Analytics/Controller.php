<?php
/**
 * REST API Controller for Analytics.
 * Handles recording analytics events and providing aggregated data.
 *
 * @package WP2Lead\API\Analytics
 */

namespace WP2Lead\API\Analytics;

use WP2Lead\Services\Analytics\Provider as AnalyticsService;
use WP_REST_Controller;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

class Controller extends WP_REST_Controller implements \WP2Lead\Interfaces\REST\Controller {
	/**
	 * Controller constructor.
	 * Registers REST API routes for analytics endpoints.
	 *
	 * @param AnalyticsService $service Analytics service provider.
	 */
	protected AnalyticsService $service;

	/**
	 * Constructor with dependency injection for AnalyticsService Provider.
	 * @param AnalyticsService $service
	 */
	public function __construct( AnalyticsService $service ) {
		$this->namespace = 'wp2-lead/v1';
		$this->rest_base = 'analytics';
		$this->service = $service;
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	public function register_routes(): void {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			[ 
				'methods' => WP_REST_Server::CREATABLE,
				'callback' => [ $this, 'record_event' ],
				'permission_callback' => [ $this, 'record_event_permissions_check' ],
				'args' => $this->get_record_event_args(),
			]
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/aggregated',
			[ 
				'methods' => WP_REST_Server::READABLE,
				'callback' => [ $this, 'get_aggregated_data' ],
				'permission_callback' => [ $this, 'get_aggregated_data_permissions_check' ],
				'args' => $this->get_aggregated_data_args(),
			]
		);
	}

	public function record_event_permissions_check( WP_REST_Request $request ): bool|WP_Error {
		/**
		 * Permission check for recording analytics events.
		 *
		 * @param WP_REST_Request $request REST request object.
		 * @return bool|WP_Error True if permitted, WP_Error otherwise.
		 */
		$nonce = $request->get_header( 'X-WP-Nonce' );
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new WP_Error( 'rest_forbidden', __( 'Invalid or missing nonce.', 'wp2-lead' ), [ 'status' => 403 ] );
		}
		return true;
	}

	public function get_record_event_args(): array {
		/**
		 * Get argument schema for recording analytics events.
		 *
		 * @return array Argument schema array.
		 */
		return [ 
			'campaign_id' => [ 
				'description' => __( 'The ID of the campaign.', 'wp2-lead' ),
				'type' => 'integer',
				'required' => true,
			],
			'variant_id' => [ 
				'description' => __( 'The ID of the variant.', 'wp2-lead' ),
				'type' => 'string',
				'required' => true,
			],
			'event_type' => [ 
				'description' => __( 'The type of event.', 'wp2-lead' ),
				'type' => 'string',
				'required' => true,
			],
			'timestamp' => [ 
				'description' => __( 'The timestamp of the event.', 'wp2-lead' ),
				'type' => 'string',
				'required' => false,
			],
		];
	}

	public function record_event( WP_REST_Request $request ): WP_REST_Response|WP_Error {
		/**
		 * Record an analytics event via REST API.
		 *
		 * @param WP_REST_Request $request REST request object.
		 * @return WP_REST_Response|WP_Error Success or error response.
		 */
		$campaign_id = (int) $request->get_param( 'campaign_id' );
		$event_type = $request->get_param( 'event_type' );
		$meta = [ 'variant_id' => $request->get_param( 'variant_id' ), 'timestamp' => $request->get_param( 'timestamp' ) ];

		$result = $this->service->record_event( $campaign_id, $event_type, $meta );
		if ( is_wp_error( $result ) ) {
			return $result;
		}
		return new WP_REST_Response( [ 'success' => true, 'message' => __( 'Analytics event recorded.', 'wp2-lead' ) ], 200 );
	}

	public function get_aggregated_data_permissions_check( WP_REST_Request $request ): bool|WP_Error {
		/**
		 * Permission check for viewing aggregated analytics data.
		 *
		 * @param WP_REST_Request $request REST request object.
		 * @return bool|WP_Error True if permitted, WP_Error otherwise.
		 */
		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error( 'rest_forbidden', __( 'You do not have permission to view analytics.', 'wp2-lead' ), [ 'status' => 403 ] );
		}
		return true;
	}

	public function get_aggregated_data_args(): array {
		/**
		 * Get argument schema for aggregated analytics data endpoint.
		 *
		 * @return array Argument schema array.
		 */
		return [ 
			'campaign_id' => [ 
				'description' => __( 'The ID of the campaign to get data for.', 'wp2-lead' ),
				'type' => 'integer',
				'required' => true,
			],
		];
	}

	public function get_aggregated_data( WP_REST_Request $request ): WP_REST_Response|WP_Error {
		/**
		 * Get aggregated analytics data via REST API.
		 *
		 * @param WP_REST_Request $request REST request object.
		 * @return WP_REST_Response|WP_Error Success or error response.
		 */
		$campaign_id = (int) $request->get_param( 'campaign_id' );
		$data = $this->service->get_aggregated_analytics( $campaign_id );
		if ( is_wp_error( $data ) ) {
			return $data;
		}
		return new WP_REST_Response( $data, 200 );
	}
}
