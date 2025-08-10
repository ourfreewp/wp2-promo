<?php
// src/Admin/views/help.php
// This file is included by src/Admin/Dashboard.php
// $mock_data is available in this scope.

if ( ! defined( 'ABSPATH' ) )
	exit;
?>
<div id="help" class="wp2-tab-content">
	<div class="wp2-help-grid">
		<div class="wp2-help-card">
			<div class="icon"><i class="ph ph-book-open"></i></div>
			<h3><?php esc_html_e( 'Documentation', 'wp2-lead' ); ?></h3>
			<p><?php esc_html_e( 'Browse our comprehensive guides and tutorials.', 'wp2-lead' ); ?></p>
			<button class="wp2-btn wp2-btn--primary"><?php esc_html_e( 'Read Docs', 'wp2-lead' ); ?></button>
		</div>
		<div class="wp2-help-card">
			<div class="icon"><i class="ph ph-users-three"></i></div>
			<h3><?php esc_html_e( 'Community Forum', 'wp2-lead' ); ?></h3>
			<p><?php esc_html_e( 'Ask questions and share tips with other users.', 'wp2-lead' ); ?></p>
			<button class="wp2-btn wp2-btn--secondary"><?php esc_html_e( 'Visit Forum', 'wp2-lead' ); ?></button>
		</div>
		<div class="wp2-help-card">
			<div class="icon"><i class="ph ph-lifebuoy"></i></div>
			<h3><?php esc_html_e( 'Email Support', 'wp2-lead' ); ?></h3>
			<p><?php esc_html_e( 'Can\'t find an answer? Get in touch with our team.', 'wp2-lead' ); ?></p>
			<button class="wp2-btn wp2-btn--secondary"><?php esc_html_e( 'Contact Us', 'wp2-lead' ); ?></button>
		</div>
	</div>
</div>