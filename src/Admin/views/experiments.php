<?php
// src/Admin/views/experiments.php
// This file is included by src/Admin/Dashboard.php
// $mock_data is available in this scope.

if ( ! defined( 'ABSPATH' ) )
	exit;
?>
<div id="experiments" class="wp2-tab-content">
	<div class="wp2-page-header">
		<h2><?php esc_html_e( 'Running Experiments', 'wp2-lead' ); ?></h2>
	</div>
	<div class="wp2-table-container">
		<table class="wp2-list-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Experiment Name', 'wp2-lead' ); ?></th>
					<th><?php esc_html_e( 'Status', 'wp2-lead' ); ?></th>
					<th><?php esc_html_e( 'Duration', 'wp2-lead' ); ?></th>
					<th><?php esc_html_e( 'Summary', 'wp2-lead' ); ?></th>
					<th><?php esc_html_e( 'Actions', 'wp2-lead' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $mock_data['experiments'] as $experiment ) : ?>
					<tr>
						<td class="wp2-campaign-name"><?php echo esc_html( $experiment['name'] ); ?></td>
						<td>
							<span
								class="wp2-status-badge <?php echo $experiment['status'] === 'running' ? 'status-running' : 'status-concluded'; ?>">
								<?php echo esc_html( ucwords( $experiment['status'] ) ); ?>
							</span>
						</td>
						<td><?php echo esc_html( $experiment['duration'] ); ?></td>
						<td><?php echo esc_html( $experiment['summary'] ); ?></td>
						<td><button
								class="wp2-btn wp2-btn--secondary"><?php esc_html_e( 'View Results', 'wp2-lead' ); ?></button>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>