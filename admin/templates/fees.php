<?php
/**
 * Fees admin template.
 *
 * @package School_Management_System
 */

use School_Management_System\Fee;
use School_Management_System\Student;
use School_Management_System\Classm;

if ( isset( $_POST['sms_add_fee'] ) && isset( $_POST['sms_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['sms_nonce'] ), 'sms_nonce_form' ) ) {
	$due_date     = sanitize_text_field( $_POST['due_date'] );
	$payment_date = sanitize_text_field( $_POST['payment_date'] );

	$data = array(
		'student_id'   => intval( $_POST['student_id'] ),
		'class_id'     => intval( $_POST['class_id'] ),
		'fee_type'     => sanitize_text_field( $_POST['fee_type'] ),
		'amount'       => floatval( $_POST['amount'] ),
		'due_date'     => ! empty( $due_date ) ? $due_date : null,
		'payment_date' => ! empty( $payment_date ) ? $payment_date : null,
		'status'       => sanitize_text_field( $_POST['status'] ),
		'remarks'      => sanitize_textarea_field( $_POST['remarks'] ),
	);

	$result = Fee::add( $data );

	if ( false === $result ) {
		wp_safe_redirect( admin_url( 'admin.php?page=sms-fees&sms_message=fee_add_error' ) );
	} else {
		wp_safe_redirect( admin_url( 'admin.php?page=sms-fees&sms_message=fee_added' ) );
	}
	exit;
}

if ( isset( $_POST['sms_edit_fee'] ) && isset( $_POST['fee_id'] ) && isset( $_POST['sms_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['sms_nonce'] ), 'sms_nonce_form' ) ) {
	$fee_id = intval( $_POST['fee_id'] );
	$due_date     = sanitize_text_field( $_POST['due_date'] );
	$payment_date = sanitize_text_field( $_POST['payment_date'] );

	$data   = array(
		'student_id'   => intval( $_POST['student_id'] ),
		'class_id'     => intval( $_POST['class_id'] ),
		'fee_type'     => sanitize_text_field( $_POST['fee_type'] ),
		'amount'       => floatval( $_POST['amount'] ),
		'due_date'     => ! empty( $due_date ) ? $due_date : null,
		'payment_date' => ! empty( $payment_date ) ? $payment_date : null,
		'status'       => sanitize_text_field( $_POST['status'] ),
		'remarks'      => sanitize_textarea_field( $_POST['remarks'] ),
	);

	$result = Fee::update( $fee_id, $data );

	if ( false === $result ) {
		wp_safe_redirect( admin_url( 'admin.php?page=sms-fees&action=edit&id=' . $fee_id . '&sms_message=fee_update_error' ) );
	} else {
		wp_safe_redirect( admin_url( 'admin.php?page=sms-fees&sms_message=fee_updated' ) );
	}
	exit;
}

if ( isset( $_GET['action'] ) && 'delete' === $_GET['action'] && isset( $_GET['id'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'sms_delete_fee_nonce' ) ) {
	$fee_id = intval( $_GET['id'] );
	Fee::delete( $fee_id );
	wp_safe_redirect( admin_url( 'admin.php?page=sms-fees&sms_message=fee_deleted' ) );
	exit;
}

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'Unauthorized', 'school-management-system' ) );
}

$fee = null;
$is_edit = false;
$action = sanitize_text_field( $_GET['action'] ?? '' );
$fee_id = intval( $_GET['id'] ?? 0 );

if ( 'edit' === $action && $fee_id ) {
	$fee = Fee::get( $fee_id );
	if ( ! $fee ) {
		wp_die( esc_html__( 'Fee record not found', 'school-management-system' ) );
	}
	$is_edit = true;
}

$message = '';
$message_class = 'notice-success';
if ( isset( $_GET['sms_message'] ) ) {
	$sms_message = sanitize_text_field( $_GET['sms_message'] );
	if ( 'fee_added' === $sms_message ) {
		$message = __( 'Fee added successfully.', 'school-management-system' );
	} elseif ( 'fee_updated' === $sms_message ) {
		$message = __( 'Fee updated successfully.', 'school-management-system' );
	} elseif ( 'fee_add_error' === $sms_message ) {
		$message = __( 'Error: Could not add the fee record. Please try again.', 'school-management-system' );
		$message_class = 'notice-error';
	} elseif ( 'fee_update_error' === $sms_message ) {
		$message = __( 'Error: Could not update the fee record. Please try again.', 'school-management-system' );
		$message_class = 'notice-error';
	} elseif ( 'fee_deleted' === $sms_message ) {
		$message = __( 'Fee deleted successfully.', 'school-management-system' );
	}
}

?>
<div class="wrap">
	<h1>
		<?php esc_html_e( 'Fees', 'school-management-system' ); ?>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-fees' ) ); ?>" class="page-title-action"><?php esc_html_e( 'Add New Fee', 'school-management-system' ); ?></a>
	</h1>

	<?php if ( ! empty( $message ) ) : ?>
		<div class="notice <?php echo esc_attr( $message_class ); ?> is-dismissible"><p><?php echo esc_html( $message ); ?></p></div>
	<?php endif; ?>

	<!-- Add/Edit Form -->
	<div style="background: #fff; padding: 20px; border: 1px solid #ddd; margin-bottom: 30px; border-radius: 4px;">
		<h2><?php echo $is_edit ? esc_html__( 'Edit Fee', 'school-management-system' ) : esc_html__( 'Add New Fee', 'school-management-system' ); ?></h2>

		<form method="post" action="">
			<?php wp_nonce_field( 'sms_nonce_form', 'sms_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><label for="student_id"><?php esc_html_e( 'Student *', 'school-management-system' ); ?></label></th>
					<td>
						<select name="student_id" id="student_id" required>
							<option value=""><?php esc_html_e( 'Select Student', 'school-management-system' ); ?></option>
							<?php
							$students = Student::get_all( array(), 1000 );
							foreach ( $students as $student ) {
								?>
								<option value="<?php echo intval( $student->id ); ?>" <?php echo $fee && $fee->student_id === $student->id ? 'selected' : ''; ?>>
									<?php echo esc_html( $student->first_name . ' ' . $student->last_name . ' (' . $student->roll_number . ')' ); ?>
								</option>
								<?php
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="class_id"><?php esc_html_e( 'Class *', 'school-management-system' ); ?></label></th>
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
					<th scope="row"><label for="fee_type"><?php esc_html_e( 'Fee Type *', 'school-management-system' ); ?></label></th>
					<td><input type="text" name="fee_type" id="fee_type" required value="<?php echo $fee ? esc_attr( $fee->fee_type ) : ''; ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="amount"><?php esc_html_e( 'Amount *', 'school-management-system' ); ?></label></th>
					<td><input type="number" name="amount" id="amount" step="0.01" required value="<?php echo $fee ? esc_attr( $fee->amount ) : ''; ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="due_date"><?php esc_html_e( 'Date', 'school-management-system' ); ?></label></th>
					<td><input type="date" name="due_date" id="due_date" required value="<?php echo $fee ? esc_attr( $fee->due_date ) : ''; ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="payment_date"><?php esc_html_e( 'Payment Date', 'school-management-system' ); ?></label></th>
					<td><input type="date" name="payment_date" id="payment_date" value="<?php echo $fee ? esc_attr( $fee->payment_date ) : ''; ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="status"><?php esc_html_e( 'Status', 'school-management-system' ); ?></label></th>
					<td>
						<select name="status" id="status">
							<option value="pending" <?php echo ! $fee || 'pending' === $fee->status ? 'selected' : ''; ?>><?php esc_html_e( 'Pending', 'school-management-system' ); ?></option>
							<option value="paid" <?php echo $fee && 'paid' === $fee->status ? 'selected' : ''; ?>><?php esc_html_e( 'Paid', 'school-management-system' ); ?></option>
							<option value="overdue" <?php echo $fee && 'overdue' === $fee->status ? 'selected' : ''; ?>><?php esc_html_e( 'Overdue', 'school-management-system' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="remarks"><?php esc_html_e( 'Remarks', 'school-management-system' ); ?></label></th>
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

	<h2><?php esc_html_e( 'Fees List', 'school-management-system' ); ?></h2>
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
			$fees = Fee::get_all( array( 'orderby' => 'id', 'order' => 'DESC' ), 500 );
			if ( ! empty( $fees ) ) {
				$fees = array_reverse( $fees );
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
</div>
