<?php
// src/Admin/views/integrations.php
// This file is included by src/Admin/Dashboard.php
// $mock_data is available in this scope.

if ( ! defined( 'ABSPATH' ) )
	exit;
?>
<div id="integrations" class="wp2-tab-content">
	<div class="wp2-page-header">
		<h2><?php esc_html_e( 'Integrations', 'wp2-lead' ); ?></h2>
	</div>
	<div class="wp2-integrations-grid">
		<div class="wp2-integration-card">
			<div class="wp2-integration-card-header">
				<img src="https://placehold.co/96x96/EFEFEF/333333?text=WS"
					alt="<?php esc_attr_e( 'WSForm Logo', 'wp2-lead' ); ?>">
				<h3><?php esc_html_e( 'WSForm', 'wp2-lead' ); ?></h3>
			</div>
			<p><?php esc_html_e( 'Track submissions from WSForm as lead conversion events in your analytics.', 'wp2-lead' ); ?>
			</p>
			<div class="wp2-integration-card-footer">
				<div class="wp2-connection-status connected"><span class="dot"></span>
					<?php esc_html_e( 'Connected', 'wp2-lead' ); ?></div>
				<button class="wp2-btn wp2-btn--secondary"><?php esc_html_e( 'Manage', 'wp2-lead' ); ?></button>
			</div>
		</div>
		<div class="wp2-integration-card">
			<div class="wp2-integration-card-header">
				<img src="https://placehold.co/96x96/1D1D1F/FFFFFF?text=F"
					alt="<?php esc_attr_e( 'Fathom Logo', 'wp2-lead' ); ?>">
				<h3><?php esc_html_e( 'Fathom Analytics', 'wp2-lead' ); ?></h3>
			</div>
			<p><?php esc_html_e( 'Send campaign impression and conversion events to your Fathom Analytics dashboard.', 'wp2-lead' ); ?>
			</p>
			<div class="wp2-integration-card-footer">
				<div class="wp2-connection-status disconnected"><span class="dot"></span>
					<?php esc_html_e( 'Not Connected', 'wp2-lead' ); ?></div>
				<button class="wp2-btn wp2-btn--primary"><?php esc_html_e( 'Connect', 'wp2-lead' ); ?></button>
			</div>
		</div>
	</div>
</div>