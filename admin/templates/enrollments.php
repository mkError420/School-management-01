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

$message = '';
if ( isset( $_GET['sms_message'] ) ) {
	$sms_message = sanitize_text_field( $_GET['sms_message'] );
	if ( 'enrollment_added' === $sms_message ) {
		$message = __( 'Student enrolled successfully.', 'school-management-system' );
	} elseif ( 'enrollment_deleted' === $sms_message ) {
		$message = __( 'Enrollment deleted successfully.', 'school-management-system' );
	}
}

?>
<div class="wrap">
	<h1><?php esc_html_e( 'Enrollments', 'school-management-system' ); ?></h1>

	<?php if ( ! empty( $message ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $message ); ?></p></div>
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
	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'ID', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Student Name', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Class Name', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Enrollment Date', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Status', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'school-management-system' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$enrollments = Enrollment::get_all( array(), 50 );
			if ( ! empty( $enrollments ) ) {
				foreach ( $enrollments as $enrollment ) {
					$student = Student::get( $enrollment->student_id );
					$class   = Classm::get( $enrollment->class_id );
					$delete_url = wp_nonce_url( admin_url( 'admin.php?page=sms-enrollments&action=delete&id=' . $enrollment->id ), 'sms_delete_enrollment_nonce', '_wpnonce' );
					?>
					<tr>
						<td><?php echo intval( $enrollment->id ); ?></td>
						<td><?php echo $student ? esc_html( $student->first_name . ' ' . $student->last_name ) : 'N/A'; ?></td>
						<td><?php echo $class ? esc_html( $class->class_name ) : 'N/A'; ?></td>
						<td><?php echo esc_html( $enrollment->enrollment_date ); ?></td>
						<td><?php echo esc_html( $enrollment->status ); ?></td>
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
					<td colspan="6"><?php esc_html_e( 'No enrollments found', 'school-management-system' ); ?></td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
</div>
