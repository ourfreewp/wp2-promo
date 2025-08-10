<?php
// src/Admin/views/settings.php
// This file is included by src/Admin/Dashboard.php
// $mock_data is available in this scope.

if ( ! defined( 'ABSPATH' ) )
	exit;
?>
<div id="settings" class="wp2-tab-content">
	<div class="wp2-settings-grid">
		<aside>
			<nav class="wp2-settings-nav">
				<a href="#" class="settings-nav-item active" data-panel="license"><i class="ph ph-key"></i>
					<?php esc_html_e( 'License', 'wp2-lead' ); ?></a>
				<a href="#" class="settings-nav-item" data-panel="access"><i class="ph ph-users-three"></i>
					<?php esc_html_e( 'Access Control', 'wp2-lead' ); ?></a>
			</nav>
		</aside>
		<div class="wp2-widget wp2-settings-panels">
			<div id="panel-license" class="wp2-settings-panel active">
				<h2><?php esc_html_e( 'License', 'wp2-lead' ); ?></h2>
				<div class="wp2-form-group">
					<label for="license-key"><?php esc_html_e( 'License Key', 'wp2-lead' ); ?></label>
					<input type="text" id="license-key" name="license_key"
						placeholder="<?php esc_attr_e( 'Enter your license key', 'wp2-lead' ); ?>">
					<p class="description">
						<?php esc_html_e( 'A valid license key is required for updates and support.', 'wp2-lead' ); ?>
					</p>
				</div>
			</div>
			<div id="panel-access" class="wp2-settings-panel">
				<h2><?php esc_html_e( 'Access Control', 'wp2-lead' ); ?></h2>
				<div class="wp2-form-group">
					<label for="view-analytics-role"><?php esc_html_e( 'View Analytics', 'wp2-lead' ); ?></label>
					<select id="view-analytics-role">
						<option><?php esc_html_e( 'Administrator', 'wp2-lead' ); ?></option>
						<option selected><?php esc_html_e( 'Editor', 'wp2-lead' ); ?></option>
					</select>
					<p class="description">
						<?php esc_html_e( 'Minimum role required to view analytics and experiments.', 'wp2-lead' ); ?>
					</p>
				</div>
			</div>
		</div>
	</div>
</div>