<?php
/**
 * Fees admin template.
 *
 * @package School_Management_System
 */

use School_Management_System\Fee;
use School_Management_System\Student;
use School_Management_System\Classm;

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'Unauthorized', 'school-management-system' ) );
}

$fee = null;
$is_edit = false;
$action = sanitize_text_field( $_GET['action'] ?? '' );
$fee_id = intval( $_GET['id'] ?? 0 );
$student_id = intval( $_GET['student_id'] ?? 0 );
$class_id_filter = intval( $_GET['class_id'] ?? 0 );
$status_filter = sanitize_text_field( $_GET['status'] ?? '' );
$date_filter = sanitize_text_field( $_GET['date'] ?? '' );
$fee_type_filter = sanitize_text_field( $_GET['fee_type'] ?? '' );
$current_page = sanitize_text_field( $_GET['page'] ?? '' );

if ( 'edit' === $action && $fee_id ) {
	$fee = Fee::get( $fee_id );
	if ( ! $fee ) {
		wp_die( esc_html__( 'Fee record not found', 'school-management-system' ) );
	}
	$is_edit = true;
}

$show_form = ( 'add' === $action || $is_edit );
$show_dashboard = ( 'sms-fees' === $current_page && ! $show_form );

$message = '';
$message_class = 'notice-success';
if ( isset( $_GET['sms_message'] ) ) {
	$sms_message = sanitize_text_field( $_GET['sms_message'] );
	if ( 'fee_added' === $sms_message ) {
		$message = __( 'Fee added successfully.', 'school-management-system' );
	} elseif ( 'fee_updated' === $sms_message ) {
		$message = __( 'Fee updated successfully.', 'school-management-system' );
	} elseif ( 'fee_add_error' === $sms_message ) {
		$error_detail = isset( $_GET['error'] ) ? sanitize_text_field( urldecode( $_GET['error'] ) ) : '';
		$message = sprintf( __( 'Error: Could not add the fee record. %s', 'school-management-system' ), $error_detail );
		$message_class = 'notice-error';
	} elseif ( 'fee_update_error' === $sms_message ) {
		$error_detail = isset( $_GET['error'] ) ? sanitize_text_field( urldecode( $_GET['error'] ) ) : '';
		$message = sprintf( __( 'Error: Could not update the fee record. %s', 'school-management-system' ), $error_detail );
		$message_class = 'notice-error';
	} elseif ( 'fee_deleted' === $sms_message ) {
		$message = __( 'Fee deleted successfully.', 'school-management-system' );
	}
}

?>
<div class="wrap">
	<?php if ( $show_dashboard ) : ?>
		<h1>
			<?php esc_html_e( 'Fees Dashboard', 'school-management-system' ); ?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-fees&action=add' ) ); ?>" class="page-title-action"><?php esc_html_e( 'Add New Fee', 'school-management-system' ); ?></a>
		</h1>
		<p><?php esc_html_e( 'Here you can view, search, and manage all student fee records. Use filters to sort by class, payment status, or due dates. Click a fee entry to view or edit details.', 'school-management-system' ); ?></p>
	<?php elseif ( $show_form ) : ?>
		<h1><?php echo $is_edit ? esc_html__( 'Edit Fee', 'school-management-system' ) : esc_html__( 'Add New Fee', 'school-management-system' ); ?></h1>
	<?php endif; ?>

	<?php if ( ! empty( $message ) ) : ?>
		<div class="notice <?php echo esc_attr( $message_class ); ?> is-dismissible"><p><?php echo esc_html( $message ); ?></p></div>
	<?php endif; ?>

	<!-- Add/Edit Form -->
	<?php if ( $show_form ) : ?>
	<div style="background: #fff; padding: 20px; border: 1px solid #ddd; margin-bottom: 30px; border-radius: 4px;">
		<h2><?php echo $is_edit ? esc_html__( 'Edit Fee', 'school-management-system' ) : esc_html__( 'Add New Fee', 'school-management-system' ); ?></h2>

		<p><?php esc_html_e( 'Please fill in the details below to add new fee information for a student. After saving, the fee record will be available on the dashboard for review.', 'school-management-system' ); ?></p>

		<form method="post" action="">
			<?php wp_nonce_field( 'sms_nonce_form', 'sms_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><label for="student_id"><?php esc_html_e( 'Student Name / ID', 'school-management-system' ); ?></label></th>
					<td>
						<select name="student_id" id="student_id" required>
							<option value=""><?php esc_html_e( 'Select Student', 'school-management-system' ); ?></option>
							<?php
							$students = Student::get_all( array(), 1000 );
							foreach ( $students as $student ) {
								?>
								<option value="<?php echo intval( $student->id ); ?>" <?php selected( $fee ? $fee->student_id : $student_id, $student->id ); ?>>
									<?php echo esc_html( $student->first_name . ' ' . $student->last_name . ' (' . $student->roll_number . ')' ); ?>
								</option>
								<?php
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="class_id"><?php esc_html_e( 'Class / Grade', 'school-management-system' ); ?></label></th>
					<td>
						<select name="class_id" id="class_id" required>
							<option value=""><?php esc_html_e( 'Select Class', 'school-management-system' ); ?></option>
							<?php
							$classes = Classm::get_all( array(), 100 );
							foreach ( $classes as $class ) {
								?>
								<option value="<?php echo intval( $class->id ); ?>" <?php echo $fee && $fee->class_id === $class->id ? 'selected' : ''; ?>>
									<?php echo esc_html( $class->class_name ); ?>
								</option>
								<?php
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="fee_type"><?php esc_html_e( 'Fee Type', 'school-management-system' ); ?></label></th>
					<td><input type="text" name="fee_type" id="fee_type" required value="<?php echo $fee ? esc_attr( $fee->fee_type ) : ''; ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="amount"><?php esc_html_e( 'Amount', 'school-management-system' ); ?></label></th>
					<td><input type="number" name="amount" id="amount" step="0.01" required value="<?php echo $fee ? esc_attr( $fee->amount ) : ''; ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="due_date"><?php esc_html_e( 'Due Date', 'school-management-system' ); ?></label></th>
					<td><input type="date" name="due_date" id="due_date" required value="<?php echo $fee ? esc_attr( $fee->due_date ) : ''; ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="status"><?php esc_html_e( 'Payment Status', 'school-management-system' ); ?></label></th>
					<td>
						<select name="status" id="status">
							<option value="paid" <?php echo $fee && 'paid' === $fee->status ? 'selected' : ''; ?>><?php esc_html_e( 'Paid', 'school-management-system' ); ?></option>
							<option value="pending" <?php echo ! $fee || 'pending' === $fee->status ? 'selected' : ''; ?>><?php esc_html_e( 'Unpaid', 'school-management-system' ); ?></option>
							<option value="partially_paid" <?php echo $fee && 'partially_paid' === $fee->status ? 'selected' : ''; ?>><?php esc_html_e( 'Partially Paid', 'school-management-system' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="payment_date"><?php esc_html_e( 'Payment Date', 'school-management-system' ); ?></label></th>
					<td><input type="date" name="payment_date" id="payment_date" value="<?php echo ( $fee && ! empty( $fee->payment_date ) && strtotime( $fee->payment_date ) > 0 ) ? esc_attr( date( 'Y-m-d', strtotime( $fee->payment_date ) ) ) : ''; ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="remarks"><?php esc_html_e( 'Notes (optional)', 'school-management-system' ); ?></label></th>
					<td><textarea name="remarks" id="remarks"><?php echo $fee ? esc_textarea( $fee->remarks ) : ''; ?></textarea></td>
				</tr>
			</table>
			<?php if ( $is_edit ) : ?>
				<input type="hidden" name="fee_id" value="<?php echo intval( $fee->id ); ?>" />
				<button type="submit" name="sms_edit_fee" class="button button-primary"><?php esc_html_e( 'Update Fee', 'school-management-system' ); ?></button>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-fees' ) ); ?>" class="button"><?php esc_html_e( 'Cancel', 'school-management-system' ); ?></a>
			<?php else : ?>
				<button type="submit" name="sms_add_fee" class="button button-primary"><?php esc_html_e( 'Add Fee', 'school-management-system' ); ?></button>
			<?php endif; ?>
		</form>
	</div>
	<?php endif; ?>

	<?php if ( $show_dashboard ) : ?>

	<!-- Statistics Section -->
	<div class="sms-dashboard-cards" style="display: flex; gap: 20px; margin-bottom: 30px;">
		<div class="sms-card" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px; flex: 1;">
			<h3 style="margin-top: 0;"><?php esc_html_e( 'Total Fees Collected', 'school-management-system' ); ?></h3>
			<p class="sms-card-value" style="font-size: 24px; font-weight: bold; margin: 0; color: #46b450;">
				<?php echo 'Taka:-  ' . number_format( Fee::get_total_amount_by_status( 'paid' ), 2 ); ?>
			</p>
		</div>
		<div class="sms-card" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px; flex: 1;">
			<h3 style="margin-top: 0;"><?php esc_html_e( 'Pending Fees', 'school-management-system' ); ?></h3>
			<p class="sms-card-value" style="font-size: 24px; font-weight: bold; margin: 0; color: #dc3232;">
				<?php echo 'Taka:-  ' . number_format( Fee::get_total_amount_by_status( 'pending' ), 2 ); ?>
			</p>
		</div>
	</div>

	<div style="display: flex; gap: 20px; margin-bottom: 30px;">
		<!-- Upcoming Due Dates -->
		<div style="flex: 1; background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px;">
			<h3 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px;"><?php esc_html_e( 'Upcoming Due Dates', 'school-management-system' ); ?></h3>
			<ul style="margin: 0; padding: 0; list-style: none;">
				<?php
				$upcoming_fees = Fee::get_upcoming_due_fees( 5 );
				if ( ! empty( $upcoming_fees ) ) {
					foreach ( $upcoming_fees as $fee ) {
						$student = Student::get( $fee->student_id );
						echo '<li style="padding: 8px 0; border-bottom: 1px solid #f0f0f0;">';
						echo '<strong>' . esc_html( $fee->due_date ) . '</strong>: ';
						echo esc_html( $student ? $student->first_name . ' ' . $student->last_name : 'Unknown' );
						echo ' - ' . esc_html( $fee->amount );
						echo '</li>';
					}
				} else {
					echo '<li>' . esc_html__( 'No upcoming due fees.', 'school-management-system' ) . '</li>';
				}
				?>
			</ul>
		</div>

		<!-- Recent Payments -->
		<div style="flex: 1; background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px;">
			<h3 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px;"><?php esc_html_e( 'Recent Payments', 'school-management-system' ); ?></h3>
			<ul style="margin: 0; padding: 0; list-style: none;">
				<?php
				$recent_payments = Fee::get_recent_payments( 5 );
				if ( ! empty( $recent_payments ) ) {
					foreach ( $recent_payments as $fee ) {
						$student = Student::get( $fee->student_id );
						echo '<li style="padding: 8px 0; border-bottom: 1px solid #f0f0f0;">';
						echo '<strong>' . esc_html( $fee->payment_date ) . '</strong>: ';
						echo esc_html( $student ? $student->first_name . ' ' . $student->last_name : 'Unknown' );
						echo ' - ' . esc_html( $fee->amount );
						echo ' <span style="color: green;">(' . esc_html__( 'Paid', 'school-management-system' ) . ')</span>';
						echo '</li>';
					}
				} else {
					echo '<li>' . esc_html__( 'No recent payments.', 'school-management-system' ) . '</li>';
				}
				?>
			</ul>
		</div>
	</div>

	<h2><?php esc_html_e( 'Fees List', 'school-management-system' ); ?></h2>

	<!-- Filter Form -->
	<form method="get" action="" style="margin-bottom: 20px;">
		<input type="hidden" name="page" value="sms-fees" />
		
		<select name="class_id">
			<option value=""><?php esc_html_e( 'All Classes', 'school-management-system' ); ?></option>
			<?php
			$classes = Classm::get_all( array(), 100 );
			foreach ( $classes as $class ) {
				?>
				<option value="<?php echo intval( $class->id ); ?>" <?php selected( $class_id_filter, $class->id ); ?>>
					<?php echo esc_html( $class->class_name ); ?>
				</option>
				<?php
			}
			?>
		</select>

		<select name="status">
			<option value=""><?php esc_html_e( 'All Statuses', 'school-management-system' ); ?></option>
			<option value="paid" <?php selected( $status_filter, 'paid' ); ?>><?php esc_html_e( 'Paid', 'school-management-system' ); ?></option>
			<option value="pending" <?php selected( $status_filter, 'pending' ); ?>><?php esc_html_e( 'Unpaid', 'school-management-system' ); ?></option>
			<option value="partially_paid" <?php selected( $status_filter, 'partially_paid' ); ?>><?php esc_html_e( 'Partially Paid', 'school-management-system' ); ?></option>
		</select>

		<input type="date" name="date" value="<?php echo esc_attr( $date_filter ); ?>" placeholder="<?php esc_attr_e( 'Due Date', 'school-management-system' ); ?>" />
		
		<input type="text" name="fee_type" value="<?php echo esc_attr( $fee_type_filter ); ?>" placeholder="<?php esc_attr_e( 'Fee Type', 'school-management-system' ); ?>" />

		<button type="submit" class="button"><?php esc_html_e( 'Filter', 'school-management-system' ); ?></button>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-fees' ) ); ?>" class="button"><?php esc_html_e( 'Reset', 'school-management-system' ); ?></a>
	</form>

	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'ID', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Student Name', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Class', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Fee Type', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Amount', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Date', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Payment Date', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Status', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'school-management-system' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$filters = array();
			if ( $student_id > 0 ) {
				$filters['student_id'] = $student_id;
			}
			if ( $class_id_filter > 0 ) {
				$filters['class_id'] = $class_id_filter;
			}
			if ( ! empty( $status_filter ) ) {
				$filters['status'] = $status_filter;
			}
			if ( ! empty( $date_filter ) ) {
				$filters['due_date'] = $date_filter;
			}
			if ( ! empty( $fee_type_filter ) ) {
				$filters['fee_type'] = $fee_type_filter;
			}
			$fees = Fee::get_all( $filters, 500 );
			if ( ! empty( $fees ) ) {
				foreach ( $fees as $fee ) {
					$student = Student::get( $fee->student_id );
					$class = Classm::get( $fee->class_id );
					$delete_url = wp_nonce_url( admin_url( 'admin.php?page=sms-fees&action=delete&id=' . $fee->id ), 'sms_delete_fee_nonce', '_wpnonce' );
					?>
					<tr>
						<td><?php echo intval( $fee->id ); ?></td>
						<td><?php echo $student ? esc_html( $student->first_name . ' ' . $student->last_name ) : 'N/A'; ?></td>
						<td><?php echo $class ? esc_html( $class->class_name ) : 'N/A'; ?></td>
						<td><?php echo esc_html( $fee->fee_type ); ?></td>
						<td><?php echo esc_html( $fee->amount ); ?></td>
						<td><?php echo esc_html( $fee->due_date ); ?></td>
						<td><?php echo esc_html( $fee->payment_date ); ?></td>
						<td><?php echo esc_html( $fee->status ); ?></td>
						<td>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-fees&action=edit&id=' . $fee->id ) ); ?>">
								<?php esc_html_e( 'Edit', 'school-management-system' ); ?>
							</a>
							|
							<a href="<?php echo esc_url( $delete_url ); ?>" onclick="return confirm('<?php esc_attr_e( 'Are you sure?', 'school-management-system' ); ?>')"><?php esc_html_e( 'Delete', 'school-management-system' ); ?></a>
						</td>
					</tr>
					<?php
				}
			} else {
				?>
				<tr>
					<td colspan="9"><?php esc_html_e( 'No fees found', 'school-management-system' ); ?></td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
	<?php endif; ?>
</div>
