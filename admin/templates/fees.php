<?php
/**
 * Fees admin template.
 *
 * @package School_Management_System
 */

use School_Management_System\Fee;

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'Unauthorized', 'school-management-system' ) );
}

?>
<div class="wrap">
	<h1><?php esc_html_e( 'Fees', 'school-management-system' ); ?></h1>

	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'ID', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Student ID', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Fee Type', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Amount', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Status', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'school-management-system' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$fees = Fee::get_all( array(), 50 );
			if ( ! empty( $fees ) ) {
				foreach ( $fees as $fee ) {
					?>
					<tr>
						<td><?php echo intval( $fee->id ); ?></td>
						<td><?php echo intval( $fee->student_id ); ?></td>
						<td><?php echo esc_html( $fee->fee_type ); ?></td>
						<td><?php echo esc_html( $fee->amount ); ?></td>
						<td><?php echo esc_html( $fee->status ); ?></td>
						<td>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-fees&action=edit&id=' . $fee->id ) ); ?>">
								<?php esc_html_e( 'Edit', 'school-management-system' ); ?>
							</a>
						</td>
					</tr>
					<?php
				}
			} else {
				?>
				<tr>
					<td colspan="6"><?php esc_html_e( 'No fees found', 'school-management-system' ); ?></td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
</div>
