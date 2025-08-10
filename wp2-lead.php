<?php
/*
 * WP2 Lead
 *
 * @package   WP2Lead
 * @author    Vinny's Green
 * @license   GPL-2.0-or-later
 * @link      https://github.com/webmultipliers/examplepress
 * @copyright 2025 Vinny's Green
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */
/**
 * Plugin Name:       WP2 Lead
 * Description:       Lightweight, high-conversion lead-capture campaigns for WordPress.
 * Version:           1.0.0
 * Author:            Vinny's Green
 * Text Domain:       wp2-lead
 * Domain Path:       /languages
 */


if ( ! defined( 'ABSPATH' ) )
	exit;

// Define plugin constants
define( 'WP2_LEAD_VERSION', '1.0.0' );
define( 'WP2_LEAD_PATH', plugin_dir_path( __FILE__ ) );
define( 'WP2_LEAD_URL', plugin_dir_url( __FILE__ ) );
define( 'WP2_LEAD_FILE', __FILE__ );

/**
 * Register Blockstudio instances for /blocks and /assets.
 * Ensures Blockstudio manages all block and global assets.
 */
add_action( 'init', function () {
	if ( class_exists( 'Blockstudio\\Build' ) ) {
		Blockstudio\Build::init( [ 
			'dir' => __DIR__ . '/blocks'
		] );
		Blockstudio\Build::init( [ 
			'dir' => __DIR__ . '/assets'
		] );
	}
} );


/**
 * Autoload Composer dependencies.
 * Shows admin notice if dependencies are missing.
 */
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
} else {
	add_action( 'admin_notices', function () {
		echo '<div class="notice notice-error is-dismissible"><p><strong>WP2 Lead</strong> requires Composer dependencies. Please run <code>composer install</code> in the plugin directory.</p></div>';
	} );
	return;
}

use WP2Lead\App\Core;
use MetaBox\CustomTable\API as MBCustomTableAPI;

/**
 * The core plugin instance.
 * @var Core|null
 */
global $wp2_lead_plugin_core;

/**
 * Returns the main instance of WP2Lead\App\Core.
 *
 * @return Core
 */
function wp2_lead(): Core {
	global $wp2_lead_plugin_core;
	if ( ! isset( $wp2_lead_plugin_core ) ) {
		$wp2_lead_plugin_core = Core::get_instance();
	}
	return $wp2_lead_plugin_core;
}

/**
 * Initialize the plugin.
 */
wp2_lead();

/**
 * Activation Hook: Create custom tables for campaigns and analytics.
 *
 * @return void
 */

register_activation_hook( WP2_LEAD_FILE, 'wp2_lead_activate' );
function wp2_lead_activate() {
	if ( ! class_exists( 'MetaBox\CustomTable\API' ) ) {
		deactivate_plugins( plugin_basename( WP2_LEAD_FILE ) );
		add_action( 'admin_notices', function () {
			echo '<div class="notice notice-error is-dismissible"><p><strong>WP2 Lead</strong> requires the Meta Box Custom Table extension. Please install and activate it.</p></div>';
		} );
		return;
	}
	// Only create analytics table (campaigns are CPT + Meta Box)
	\WP2Lead\Models\Analytics\Registrar::create_table();
}

/**
 * Uninstall Hook: Drop custom tables and delete options.
 *
 * @return void
 */


/**
 * Dependency check and admin notice for Meta Box Custom Table API.
 *
 * @return void
 */
add_action( 'admin_init', function () {
	if ( ! class_exists( 'MetaBox\CustomTable\API' ) ) {
		add_action( 'admin_notices', function () {
			echo '<div class="notice notice-error"><p>' . esc_html__( 'WP2 Lead requires Meta Box Custom Table API. Please install and activate it.', 'wp2-lead' ) . '</p></div>';
		} );
	}
} );

/**
 * Add top-level admin menu for WP2 Lead.
 *
 * @return void
 */
add_action( 'admin_menu', 'wp2_lead_add_admin_menu' );
function wp2_lead_add_admin_menu() {
	add_menu_page(
		'WP2 Lead',
		'WP2 Lead',
		'manage_options',
		'wp2-lead',
		[ wp2_lead(), 'render_admin_dashboard_placeholder' ],
		'dashicons-chart-area',
		25
	);
}
