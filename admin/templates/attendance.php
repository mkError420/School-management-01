<?php
/**
 * Attendance admin template.
 *
 * @package School_Management_System
 */

use School_Management_System\Attendance;

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'Unauthorized', 'school-management-system' ) );
}

?>
<div class="wrap">
	<h1><?php esc_html_e( 'Attendance', 'school-management-system' ); ?></h1>

	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'ID', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Student ID', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Class ID', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Date', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Status', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'school-management-system' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$records = Attendance::get_all( array(), 50 );
			if ( ! empty( $records ) ) {
				foreach ( $records as $record ) {
					?>
					<tr>
						<td><?php echo intval( $record->id ); ?></td>
						<td><?php echo intval( $record->student_id ); ?></td>
						<td><?php echo intval( $record->class_id ); ?></td>
						<td><?php echo esc_html( $record->attendance_date ); ?></td>
						<td><?php echo esc_html( $record->status ); ?></td>
						<td>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-attendance&action=edit&id=' . $record->id ) ); ?>">
								<?php esc_html_e( 'Edit', 'school-management-system' ); ?>
							</a>
						</td>
					</tr>
					<?php
				}
			} else {
				?>
				<tr>
					<td colspan="6"><?php esc_html_e( 'No attendance records found', 'school-management-system' ); ?></td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
</div>
