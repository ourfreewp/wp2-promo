<?php
namespace WP2Lead\Models\Settings;

if ( ! defined( 'ABSPATH' ) )
	exit;
/**
 * Settings Model Registrar
 *
 * Registers the Meta Box settings page and fields for WP2 Lead.
 *
 * @package WP2Lead\Models\Settings
 */
class Registrar {
	/**
	 * Registrar constructor.
	 * Hooks settings page and field registration into Meta Box filters.
	 */
	public function __construct() {
		add_filter( 'mb_settings_pages', [ $this, 'register_settings_page' ] );
		add_filter( 'rwmb_meta_boxes', [ $this, 'register_settings_fields' ] );
	}

	/**
	 * Register the Meta Box settings page for WP2 Lead.
	 */
	public function register_settings_page( array $settings_pages ): array {
		$settings_pages[] = [ 
			'id' => 'wp2_lead_settings',
			'option_name' => 'wp2_lead_settings',
			'menu_title' => __( 'WP2 Lead Settings', 'wp2-lead' ),
			'icon_url' => 'dashicons-megaphone',
			'position' => 80,
		];
		return $settings_pages;
	}

	/**
	 * Register settings fields for the WP2 Lead settings page.
	 */
	public function register_settings_fields( array $meta_boxes ): array {
		$meta_boxes[] = [ 
			'id' => 'wp2_lead_settings_fields',
			'settings_pages' => [ 'wp2_lead_settings' ],
			'title' => __( 'WP2 Lead Settings', 'wp2-lead' ),
			'fields' => [ 
				[ 
					'id' => 'trigger_delay',
					'name' => __( 'Default Trigger Delay (ms)', 'wp2-lead' ),
					'type' => 'number',
					'std' => 5000,
					'sanitize_callback' => function ($value) {
						return absint( $value );
					},
					'validate_callback' => function ($value) {
						return is_numeric( $value ) && $value >= 0;
					},
				],
				[ 
					'id' => 'debug_mode',
					'name' => __( 'Enable Debug Mode', 'wp2-lead' ),
					'type' => 'checkbox',
					'sanitize_callback' => function ($value) {
						return (bool) $value;
					},
					'validate_callback' => function ($value) {
						return is_bool( $value ) || $value === '1' || $value === '0';
					},
				],
			],
		];
		return $meta_boxes;
	}
}
