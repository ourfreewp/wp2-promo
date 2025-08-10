<?php
/**
 * Main plugin orchestrator class for WP2 Lead
 * @package WP2Lead\App
 */

namespace WP2Lead\App;

use WP2Lead\Admin\Analytics\Dashboard;
use WP2Lead\API\Analytics\Controller as AnalyticsAPIController;
use WP2Lead\API\Campaigns\Controller as CampaignsAPIController;
use WP2Lead\Models\Campaigns\Registrar as CampaignsRegistrar;
use WP2Lead\Models\Settings\Registrar as SettingsRegistrar;
use WP2Lead\Services\Analytics\Provider as AnalyticsService;
use WP2Lead\Services\Campaigns\Provider as CampaignsService;

class Core {

	protected static $instance = null;

	public static function get_instance(): Core {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	protected function __construct() {
		$this->define_constants();
		$this->includes();
		$this->setup_hooks();
	}

	private function define_constants(): void {
		// Constants are defined in wp2-lead.php
	}

	private function includes(): void {
		new CampaignsRegistrar();
		new SettingsRegistrar();
		new CampaignsAPIController( new CampaignsService() );
		new AnalyticsAPIController( new AnalyticsService() );
		new Dashboard();
		new \WP2Lead\Models\Analytics\Registrar();
		if ( class_exists( 'WP2Lead\\Services\\Integrations\\WSForm' ) ) {
			new \WP2Lead\Services\Integrations\WSForm();
		}
	}

	private function setup_hooks(): void {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ] );
		add_action( 'mb_relationships_init', [ $this, 'register_mb_relationships' ] );
	}

	public function render_admin_dashboard_placeholder(): void {
		echo '<div class="wrap"><h1>' . esc_html__( 'Welcome to WP2 Lead!', 'wp2-lead' ) . '</h1>';
		echo '<p>' . esc_html__( 'Please navigate to Campaigns or Analytics from the submenu.', 'wp2-lead' ) . '</p></div>';
	}

	public function enqueue_frontend_assets(): void {
		wp_enqueue_script(
			'wp2-lead-sdk',
			WP2_LEAD_URL . 'assets/scripts/_dist/global-scripts-main-1754481621.js',
			[],
			WP2_LEAD_VERSION,
			true
		);
		wp_enqueue_style(
			'wp2-lead-main',
			WP2_LEAD_URL . 'assets/styles/_dist/global-styles-main-1754482764.css',
			[],
			WP2_LEAD_VERSION
		);

		wp_localize_script(
			'wp2-lead-sdk',
			'wp2LeadData',
			[ 
				'rest_url' => rest_url( 'wp2-lead/v1/' ),
				'nonce' => wp_create_nonce( 'wp_rest' ),
				'global_settings' => function_exists( 'rwmb_meta' ) ? rwmb_meta( '', [ 'object_type' => 'setting' ], 'wp2_lead_settings' ) : [],
			]
		);
	}


	public function register_mb_relationships(): void {
		if ( ! class_exists( 'MB_Relationships_API' ) ) {
			error_log( 'WP2 Lead: Meta Box Relationships API not found. Please ensure Meta Box and its Relationships extension are active.' );
			return;
		}
		\MB_Relationships_API::register( [ 
			'id' => 'campaign_to_form',
			'from' => [ 'object_type' => 'post', 'post_type' => 'wp2_lead_campaign' ],
			'to' => [ 'object_type' => 'post', 'post_type' => 'ws-form' ],
			'reciprocal' => true,
			'admin_column' => true,
		] );
	}
}