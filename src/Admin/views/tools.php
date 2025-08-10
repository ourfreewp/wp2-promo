<?php
// src/Admin/views/tools.php
// This file is included by src/Admin/Dashboard.php
// $mock_data is available in this scope.

if ( ! defined( 'ABSPATH' ) )
	exit;
?>
<div id="tools" class="wp2-tab-content">
	<div class="wp2-dashboard-main-grid">
		<div class="wp2-widget">
			<h2><?php esc_html_e( 'System Status', 'wp2-lead' ); ?></h2>
			<div class="wp2-status-list">
				<?php foreach ( $mock_data['systemStatus'] as $item ) : ?>
					<div class="item">
						<span><?php echo esc_html( $item['label'] ); ?></span>
						<div>
							<span
								class="wp2-status-badge wp2-status-badge-ok"><?php echo esc_html( $item['value'] ); ?></span>
							<?php if ( isset( $item['tooltip'] ) ) : ?>
								<i class="ph ph-info info-icon"
									data-tooltip-content="<?php echo esc_attr( $item['tooltip'] ); ?>"></i>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="wp2-widget">
			<h2><?php esc_html_e( 'Tools', 'wp2-lead' ); ?></h2>
			<div class="wp2-form-group">
				<button class="wp2-btn wp2-btn--secondary" style="width: 100%"><i class="ph ph-broom"></i>
					<?php esc_html_e( 'Clear Plugin Cache', 'wp2-lead' ); ?></button>
			</div>
			<div class="wp2-form-group">
				<button class="wp2-btn wp2-btn--secondary" style="width: 100%"><i class="ph ph-arrows-clockwise"></i>
					<?php esc_html_e( 'Regenerate Assets', 'wp2-lead' ); ?></button>
			</div>
			<div class="wp2-form-group">
				<button class="wp2-btn wp2-btn--secondary" style="width: 100%"><i class="ph ph-export"></i>
					<?php esc_html_e( 'Export Settings', 'wp2-lead' ); ?></button>
			</div>
			<div class="wp2-form-group">
				<button class="wp2-btn wp2-btn--secondary" style="width: 100%"><i class="ph ph-import"></i>
					<?php esc_html_e( 'Import Settings', 'wp2-lead' ); ?></button>
			</div>
		</div>
	</div>
</div>