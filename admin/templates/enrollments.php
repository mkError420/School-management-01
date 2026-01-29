<?php
/**
 * Enrollments admin template.
 *
 * @package School_Management_System
 */

use School_Management_System\Enrollment;
use School_Management_System\Student;
use School_Management_System\Classm;

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'Unauthorized', 'school-management-system' ) );
}

$active_tab = sanitize_text_field( $_GET['tab'] ?? 'enroll_existing' );
$message_class = 'notice-success';
$error_message = '';
$message = '';
if ( isset( $_GET['sms_message'] ) ) {
	$sms_message = sanitize_text_field( $_GET['sms_message'] );
	if ( 'enrollment_added' === $sms_message ) {
		$message = __( 'Enrollment added successfully.', 'school-management-system' );
	} elseif ( 'enrollment_deleted' === $sms_message ) {
		$message = __( 'Enrollment deleted successfully.', 'school-management-system' );
	} elseif ( 'enrollments_bulk_deleted' === $sms_message ) {
		$count = intval( $_GET['count'] ?? 0 );
		$message = sprintf( __( '%d enrollments deleted successfully.', 'school-management-system' ), $count );
	} elseif ( 'student_created_and_enrolled' === $sms_message ) {
		$message = __( 'New student created and enrolled successfully.', 'school-management-system' );
	} elseif ( 'student_updated_and_enrolled' === $sms_message ) {
		$message = __( 'Existing student details updated and enrolled successfully.', 'school-management-system' );
	} elseif ( 'student_already_enrolled' === $sms_message ) {
		$message = __( 'Student is already enrolled in this class.', 'school-management-system' );
		$message_class = 'notice-warning';
	}
}
if ( isset( $_GET['sms_error'] ) ) {
	$error_message = sanitize_text_field( urldecode( $_GET['sms_error'] ) );
}

?>
<div class="wrap">
	<h1><?php esc_html_e( 'Enrollments', 'school-management-system' ); ?></h1>

	<?php if ( ! empty( $message ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $message ); ?></p></div>
	<?php endif; ?>
	<?php if ( ! empty( $error_message ) ) : ?>
		<div class="notice notice-error is-dismissible"><p><?php echo esc_html( $error_message ); ?></p></div>
	<?php endif; ?>

	<!-- Add/Edit Form -->
	<div style="background: #fff; padding: 20px; border: 1px solid #ddd; margin-bottom: 30px; border-radius: 4px;">
		<h2><?php esc_html_e( 'Add New Enrollment', 'school-management-system' ); ?></h2>

		<form method="post" action="">
			<?php wp_nonce_field( 'sms_nonce_form', 'sms_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="student_id"><?php esc_html_e( 'Student *', 'school-management-system' ); ?></label>
					</th>
					<td>
						<select name="student_id" id="student_id" required>
							<option value=""><?php esc_html_e( 'Select Student', 'school-management-system' ); ?></option>
							<?php
							$students = Student::get_all( array(), 1000 );
							foreach ( $students as $student ) {
								?>
								<option value="<?php echo intval( $student->id ); ?>">
									<?php echo esc_html( $student->first_name . ' ' . $student->last_name . ' (' . $student->roll_number . ')' ); ?>
								</option>
								<?php
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="class_id"><?php esc_html_e( 'Class *', 'school-management-system' ); ?></label>
					</th>
					<td>
						<select name="class_id" id="class_id" required>
							<option value=""><?php esc_html_e( 'Select Class', 'school-management-system' ); ?></option>
							<?php
							$classes = Classm::get_all( array(), 100 );
							foreach ( $classes as $class ) {
								?>
								<option value="<?php echo intval( $class->id ); ?>">
									<?php echo esc_html( $class->class_name ); ?>
								</option>
								<?php
							}
							?>
						</select>
					</td>
				</tr>
			</table>
			<button type="submit" name="sms_add_enrollment" class="button button-primary">
				<?php esc_html_e( 'Enroll Student', 'school-management-system' ); ?>
			</button>
		</form>
	</div>

	<!-- Enrollments List -->
	<h2><?php esc_html_e( 'Enrollments List', 'school-management-system' ); ?></h2>

	<form method="get" action="" style="margin-bottom: 20px; float: right;">
		<input type="hidden" name="page" value="sms-enrollments" />
		<input type="search" name="s" value="<?php echo isset( $_GET['s'] ) ? esc_attr( $_GET['s'] ) : ''; ?>" placeholder="<?php esc_attr_e( 'Search by student or class...', 'school-management-system' ); ?>" />
		<button type="submit" class="button"><?php esc_html_e( 'Search', 'school-management-system' ); ?></button>
		<?php if ( ! empty( $_GET['s'] ) ) : ?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-enrollments' ) ); ?>" class="button"><?php esc_html_e( 'Reset', 'school-management-system' ); ?></a>
		<?php endif; ?>
	</form>
	<div style="clear: both;"></div>

	<form method="post" action="">
	<?php wp_nonce_field( 'sms_bulk_delete_enrollments_nonce', 'sms_bulk_delete_enrollments_nonce' ); ?>
	<div class="tablenav top">
		<div class="alignleft actions bulkactions">
			<select name="action">
				<option value="-1"><?php esc_html_e( 'Bulk Actions', 'school-management-system' ); ?></option>
				<option value="bulk_delete_enrollments"><?php esc_html_e( 'Delete', 'school-management-system' ); ?></option>
			</select>
			<input type="submit" class="button action" value="<?php esc_attr_e( 'Apply', 'school-management-system' ); ?>">
		</div>
	</div>
	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<td id="cb" class="manage-column column-cb check-column"><input id="cb-select-all-enrollments" type="checkbox"></td>
				<th><?php esc_html_e( 'Class Name', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Enrollment Date', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'school-management-system' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$search_term = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
			if ( ! empty( $search_term ) ) {
				$enrollments = Enrollment::search( $search_term );
			} else {
				$enrollments = Enrollment::get_all( array(), 50 );
			}
			if ( ! empty( $enrollments ) ) {
				foreach ( $enrollments as $enrollment ) {
					$student = Student::get( $enrollment->student_id );
					$class   = Classm::get( $enrollment->class_id );
					$delete_url = wp_nonce_url( admin_url( 'admin.php?page=sms-enrollments&action=delete&id=' . $enrollment->id ), 'sms_delete_enrollment_nonce', '_wpnonce' );
					?>
					<tr>
						<th scope="row" class="check-column"><input type="checkbox" name="enrollment_ids[]" value="<?php echo intval( $enrollment->id ); ?>"></th>
						<td><?php echo $student ? esc_html( $student->first_name . ' ' . $student->last_name ) : 'N/A'; ?></td>
						<td><?php echo $class ? esc_html( $class->class_name ) : 'N/A'; ?></td>
						<td><?php echo esc_html( $enrollment->enrollment_date ); ?></td>
						<td>
							<a href="<?php echo esc_url( $delete_url ); ?>" onclick="return confirm('<?php esc_attr_e( 'Are you sure?', 'school-management-system' ); ?>')">
								<?php esc_html_e( 'Delete', 'school-management-system' ); ?>
							</a>
						</td>
					</tr>
					<?php
				}
			} else {
				?>
				<tr>
					<td colspan="5"><?php esc_html_e( 'No enrollments found', 'school-management-system' ); ?></td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
	</form>
</div>
