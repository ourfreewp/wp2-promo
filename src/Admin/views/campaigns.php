<?php
// src/Admin/views/campaigns.php
// This file is included by src/Admin/Dashboard.php
// $mock_data is available in this scope.

if ( ! defined( 'ABSPATH' ) )
	exit;
?>
<div id="campaigns" class="wp2-tab-content">
	<div class="wp2-page-header">
		<h2><?php esc_html_e( 'Campaigns', 'wp2-lead' ); ?></h2>
		<input type="text" class="wp2-form-group"
			placeholder="<?php esc_attr_e( 'Search campaigns...', 'wp2-lead' ); ?>"
			style="max-width: 300px; margin-bottom: 0;">
	</div>
	<div class="wp2-table-container">
		<table class="wp2-list-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Campaign Name', 'wp2-lead' ); ?></th>
					<th><?php esc_html_e( 'Status', 'wp2-lead' ); ?></th>
					<th><?php esc_html_e( 'Lead Events (7d)', 'wp2-lead' ); ?></th>
					<th><?php esc_html_e( 'Conv. Rate', 'wp2-lead' ); ?></th>
					<th><?php esc_html_e( 'Actions', 'wp2-lead' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $mock_data['campaigns'] as $campaign ) : ?>
					<tr>
						<td class="wp2-campaign-name"><?php echo esc_html( $campaign['name'] ); ?></td>
						<td>
							<span
								class="wp2-status-badge <?php echo $campaign['status'] === 'active' ? 'status-active' : 'status-paused'; ?>">
								<i
									class="ph <?php echo $campaign['status'] === 'active' ? 'ph-play-circle' : 'ph-pause-circle'; ?>"></i>
								<?php echo esc_html( ucwords( $campaign['status'] ) ); ?>
							</span>
						</td>
						<td><?php echo esc_html( $campaign['leads'] ); ?></td>
						<td><?php echo esc_html( $campaign['convRate'] ); ?></td>
						<td><button
								class="wp2-btn wp2-btn--secondary"><?php esc_html_e( 'Analytics', 'wp2-lead' ); ?></button>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>