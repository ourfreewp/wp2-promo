<?php
/**
 * Main Admin Dashboard class for WP2 Lead.
 *
 * This class handles the top-level admin menu and content for the entire dashboard.
 * It acts as the primary router, loading different 'views' based on the active tab.
 *
 * @package WP2Lead\Admin
 */

namespace WP2Lead\Admin;

if ( ! defined( 'ABSPATH' ) )
	exit;

class Dashboard {

	/**
	 * Dashboard constructor.
	 * Hooks into WordPress to add the admin menu and enqueue assets.
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_admin_page' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	/**
	 * Adds the main WP2 Lead dashboard page.
	 */
	public function add_admin_page() {
		add_menu_page(
			'WP2 Lead',
			'WP2 Lead',
			'manage_options',
			'wp2-lead-dashboard',
			[ $this, 'render_dashboard' ],
			'dashicons-chart-area',
			25
		);
	}

	/**
	 * Enqueues the necessary CSS and JavaScript assets for the dashboard.
	 *
	 * @param string $hook The current admin page hook suffix.
	 */
	public function enqueue_assets( $hook ) {
		if ( strpos( $hook, 'wp2-lead-dashboard' ) === false ) {
			return;
		}

		// CSS for the entire dashboard
		wp_enqueue_style(
			'wp2-lead-admin-dashboard-styles',
			WP2_LEAD_URL . 'assets/admin/css/admin-dashboard.css',
			[],
			WP2_LEAD_VERSION
		);

		// JS for interactive components (tabs, modals, charts)
		wp_enqueue_script(
			'wp2-lead-admin-dashboard-scripts',
			WP2_LEAD_URL . 'assets/admin/js/admin-dashboard.js',
			[ 'jquery' ],
			WP2_LEAD_VERSION,
			true
		);

		// External libraries
		wp_enqueue_script( 'chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', [], null, true );
		wp_enqueue_style( 'phosphor-icons', 'https://cdnjs.cloudflare.com/ajax/libs/phosphor-icons/1.4.2/css/phosphor.min.css', [], null );

		// Localize data for JavaScript
		$mock_data = [ 
			'mainChart' => [ 
				[ 'name' => 'Jan', 'Leads' => 65 ],
				[ 'name' => 'Feb', 'Leads' => 59 ],
				[ 'name' => 'Mar', 'Leads' => 80 ],
				[ 'name' => 'Apr', 'Leads' => 81 ],
				[ 'name' => 'May', 'Leads' => 56 ],
				[ 'name' => 'Jun', 'Leads' => 55 ],
				[ 'name' => 'Jul', 'Leads' => 40 ],
			],
			'systemStatus' => [ 
				[ 'label' => 'PHP Version', 'status' => 'ok', 'value' => '8.1.10', 'tooltip' => 'The version of PHP running on your server. WP2-Lead recommends 8.0+.' ],
				[ 'label' => 'Meta Box Plugin', 'status' => 'ok', 'value' => 'Active' ],
				[ 'label' => 'REST API Reachable', 'status' => 'ok', 'value' => 'OK' ],
			],
		];

		wp_localize_script(
			'wp2-lead-admin-dashboard-scripts',
			'wp2LeadDashboardData',
			$mock_data
		);
	}

	/**
	 * Renders the main dashboard page content.
	 */
	public function render_dashboard() {
		// Mock data to be replaced with real data from the database
		$mock_data = [ 
			'mainChart' => [ 
				[ 'name' => 'Jan', 'Leads' => 65 ],
				[ 'name' => 'Feb', 'Leads' => 59 ],
				[ 'name' => 'Mar', 'Leads' => 80 ],
				[ 'name' => 'Apr', 'Leads' => 81 ],
				[ 'name' => 'May', 'Leads' => 56 ],
				[ 'name' => 'Jun', 'Leads' => 55 ],
				[ 'name' => 'Jul', 'Leads' => 40 ],
			],
			'campaigns' => [ 
				[ 'id' => 1, 'name' => 'Winter Sale Ebook', 'status' => 'active', 'leads' => 152, 'convRate' => '21.2%' ],
				[ 'id' => 2, 'name' => 'Free Consultation Offer', 'status' => 'active', 'leads' => 98, 'convRate' => '18.5%' ],
				[ 'id' => 3, 'name' => 'Homepage Popup', 'status' => 'paused', 'leads' => 12, 'convRate' => '9.3%' ],
			],
			'experiments' => [ 
				[ 'id' => 1, 'name' => 'Winter Sale Ebook', 'status' => 'running', 'duration' => '14 days', 'summary' => 'Variant B is leading by 32%' ],
				[ 'id' => 2, 'name' => 'Newsletter Signup Footer', 'status' => 'concluded', 'duration' => '30 days', 'summary' => 'Variant C was the winner' ],
			],
			'activities' => [ 
				[ 'id' => 1, 'event' => 'Lead Conversion', 'details' => 'Lead sent to Webhook from campaign Winter Sale Ebook', 'date' => 'August 5, 2025, 10:58 PM' ],
				[ 'id' => 2, 'event' => 'Impression', 'details' => 'Campaign Free Consultation Offer viewed', 'date' => 'August 5, 2025, 10:57 PM' ],
			],
			'systemStatus' => [ 
				[ 'label' => 'PHP Version', 'status' => 'ok', 'value' => '8.1.10', 'tooltip' => 'The version of PHP running on your server. WP2-Lead recommends 8.0+.' ],
				[ 'label' => 'Meta Box Plugin', 'status' => 'ok', 'value' => 'Active' ],
				[ 'label' => 'REST API Reachable', 'status' => 'ok', 'value' => 'OK' ],
			],
		];

		// Get the current active tab, default to 'home'
		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'home';
		$tab_map = [ 
			'home' => [ 'icon' => 'ph-house', 'label' => esc_html__( 'Home', 'wp2-lead' ) ],
			'campaigns' => [ 'icon' => 'ph-megaphone-simple', 'label' => esc_html__( 'Campaigns', 'wp2-lead' ) ],
			'experiments' => [ 'icon' => 'ph-test-tube', 'label' => esc_html__( 'Experiments', 'wp2-lead' ) ],
			'activities' => [ 'icon' => 'ph-list-checks', 'label' => esc_html__( 'Activities', 'wp2-lead' ) ],
			'integrations' => [ 'icon' => 'ph-plugs-connected', 'label' => esc_html__( 'Integrations', 'wp2-lead' ) ],
			'settings' => [ 'icon' => 'ph-gear-six', 'label' => esc_html__( 'Settings', 'wp2-lead' ) ],
			'tools' => [ 'icon' => 'ph-wrench', 'label' => esc_html__( 'Tools', 'wp2-lead' ) ],
			'help' => [ 'icon' => 'ph-question', 'label' => esc_html__( 'Help', 'wp2-lead' ) ],
		];
		?>
		<div class="wrap wp2-lead-dashboard-app">
			<header class="wp2-admin-header">
				<h1><i class="ph ph-funnel-simple"></i> <?php esc_html_e( 'WP2-Lead', 'wp2-lead' ); ?></h1>
				<button id="add-campaign-btn" class="wp2-btn wp2-btn--primary"><i class="ph ph-plus-circle"></i>
					<?php esc_html_e( 'Add New Campaign', 'wp2-lead' ); ?></button>
			</header>
			<div class="wp2-nav-tab-wrapper">
				<?php foreach ( $tab_map as $tab_key => $tab_data ) : ?>
					<a href="?page=wp2-lead-dashboard&tab=<?php echo esc_attr( $tab_key ); ?>"
						class="wp2-nav-tab <?php echo $active_tab === $tab_key ? 'nav-tab-active' : ''; ?>">
						<i class="ph <?php echo esc_attr( $tab_data['icon'] ); ?>"></i>
						<?php echo esc_html( $tab_data['label'] ); ?>
					</a>
				<?php endforeach; ?>
			</div>
			<main class="admin-content">
				<?php
				$view_path = plugin_dir_path( __FILE__ ) . 'views/' . sanitize_file_name( $active_tab ) . '.php';
				if ( file_exists( $view_path ) ) {
					include $view_path;
				} else {
					// Fallback to the dashboard view if the tab doesn't exist
					include plugin_dir_path( __FILE__ ) . 'views/home.php';
				}
				?>
			</main>
		</div>
		<!-- Modal for adding a new campaign -->
		<div id="add-campaign-modal" class="wp2-modal-backdrop" style="display: none;">
			<div class="wp2-modal-content">
				<div class="wp2-modal-header">
					<h2><?php esc_html_e( 'Create New Campaign', 'wp2-lead' ); ?></h2>
					<button id="close-modal-btn" class="wp2-close-btn"
						aria-label="<?php esc_attr_e( 'Close modal', 'wp2-lead' ); ?>">&times;</button>
				</div>
				<div class="wp2-modal-body">
					<div class="wp2-form-group">
						<label for="new-campaign-name"><?php esc_html_e( 'Campaign Name', 'wp2-lead' ); ?></label>
						<input type="text" id="new-campaign-name"
							placeholder="<?php esc_attr_e( 'e.g., Summer Sale Popup', 'wp2-lead' ); ?>">
					</div>
				</div>
				<div class="wp2-modal-footer">
					<button class="wp2-btn wp2-btn--secondary"
						id="cancel-modal-btn"><?php esc_html_e( 'Cancel', 'wp2-lead' ); ?></button>
					<button class="wp2-btn wp2-btn--primary"><?php esc_html_e( 'Create & Edit', 'wp2-lead' ); ?></button>
				</div>
			</div>
		</div>
		<!-- Tooltip element -->
		<div id="tooltip">
			<div id="arrow"></div>
		</div>
		<?php
	}
}
