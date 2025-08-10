<?php
// src/Admin/views/activities.php
// This file is included by src/Admin/Dashboard.php
// $mock_data is available in this scope.

if ( ! defined( 'ABSPATH' ) )
	exit;
?>
<div id="activities" class="wp2-tab-content">
	<div class="wp2-page-header">
		<h2><?php esc_html_e( 'Activity Log', 'wp2-lead' ); ?></h2>
		<input type="text" class="wp2-form-group" placeholder="<?php esc_attr_e( 'Search activity...', 'wp2-lead' ); ?>"
			style="max-width: 300px; margin-bottom: 0;">
	</div>
	<div class="wp2-table-container">
		<table class="wp2-list-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Event', 'wp2-lead' ); ?></th>
					<th><?php esc_html_e( 'Details', 'wp2-lead' ); ?></th>
					<th><?php esc_html_e( 'Date', 'wp2-lead' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $mock_data['activities'] as $activity ) : ?>
					<tr>
						<td>
							<span class="wp2-status-badge event-badge">
								<?php echo esc_html( $activity['event'] ); ?>
							</span>
						</td>
						<td><?php echo esc_html( $activity['details'] ); ?></td>
						<td><?php echo esc_html( $activity['date'] ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>