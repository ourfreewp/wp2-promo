<?php
// src/Admin/views/home.php
// This file is included by src/Admin/Dashboard.php
// $mock_data is available in this scope.

if ( ! defined( 'ABSPATH' ) )
	exit;
?>
<div id="home" class="wp2-tab-content active">
	<div class="wp2-widget wp2-getting-started" style="margin-bottom: 2rem;">
		<div class="wp2-widget-header">
			<h2><?php esc_html_e( 'Getting Started', 'wp2-lead' ); ?></h2>
			<button class="wp2-close-btn"
				aria-label="<?php esc_attr_e( 'Dismiss getting started guide', 'wp2-lead' ); ?>">&times;</button>
		</div>
		<div class="step">
			<div class="step-number">1</div>
			<p><strong><?php esc_html_e( 'Connect an Integration:', 'wp2-lead' ); ?></strong>
				<?php esc_html_e( 'Go to the ', 'wp2-lead' ); ?><a
					href="?page=wp2-lead-dashboard&tab=integrations"><?php esc_html_e( 'Integrations', 'wp2-lead' ); ?></a>
				<?php esc_html_e( 'tab to connect services.', 'wp2-lead' ); ?></p>
		</div>
		<div class="step">
			<div class="step-number">2</div>
			<p><strong><?php esc_html_e( 'Create a Campaign:', 'wp2-lead' ); ?></strong>
				<?php esc_html_e( 'Go to the ', 'wp2-lead' ); ?><a
					href="?page=wp2-lead-dashboard&tab=campaigns"><?php esc_html_e( 'Campaigns', 'wp2-lead' ); ?></a>
				<?php esc_html_e( 'tab and create your first lead form.', 'wp2-lead' ); ?></p>
		</div>
		<div class="step">
			<div class="step-number">3</div>
			<p><strong><?php esc_html_e( 'Go Live:', 'wp2-lead' ); ?></strong>
				<?php esc_html_e( 'Use the "WP2 Lead Campaign" block to add your campaign to any page.', 'wp2-lead' ); ?>
			</p>
		</div>
	</div>
	<div class="wp2-dashboard-main-grid">
		<div class="wp2-widget">
			<h2><?php esc_html_e( 'Lead Events This Year', 'wp2-lead' ); ?></h2>
			<canvas id="mainChart" height="250"></canvas>
		</div>
		<div class="wp2-widget">
			<h2><?php esc_html_e( 'Optimization', 'wp2-lead' ); ?></h2>
			<div class="wp2-optimization-card success">
				<i class="ph ph-trophy icon"></i>
				<p><strong>'Winter Sale' Variant B</strong>
					<?php esc_html_e( 'is outperforming Variant A. ', 'wp2-lead' ); ?><a
						href="#"><?php esc_html_e( 'Promote winner.', 'wp2-lead' ); ?></a></p>
			</div>
			<div class="wp2-optimization-card warning">
				<i class="ph ph-test-tube icon"></i>
				<p><strong>'Free Consultation'</strong> <?php esc_html_e( 'has no A/B test. ', 'wp2-lead' ); ?><a
						href="#"><?php esc_html_e( 'Create a variant.', 'wp2-lead' ); ?></a></p>
			</div>
		</div>
	</div>
</div>