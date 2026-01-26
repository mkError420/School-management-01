<?php
/**
 * Student Attendance admin template.
 *
 * @package School_Management_System
 */

use School_Management_System\Classm;
use School_Management_System\Student;
use School_Management_System\Attendance;

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'Unauthorized', 'school-management-system' ) );
}

$class_id = intval( $_GET['class_id'] ?? 0 );
$date     = sanitize_text_field( $_GET['date'] ?? current_time( 'Y-m-d' ) );

$message = '';
if ( isset( $_GET['sms_message'] ) && 'attendance_saved' === $_GET['sms_message'] ) {
	$message = __( 'Attendance saved successfully.', 'school-management-system' );
}
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Student Attendance', 'school-management-system' ); ?></h1>

	<?php if ( ! empty( $message ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $message ); ?></p></div>
	<?php endif; ?>

	<div style="background: #fff; padding: 20px; border: 1px solid #ddd; margin-bottom: 30px; border-radius: 4px;">
		<form method="get" action="">
			<input type="hidden" name="page" value="sms-student-attendance" />
			<table class="form-table">
				<tr>
					<th scope="row"><label for="class_id"><?php esc_html_e( 'Select Class', 'school-management-system' ); ?></label></th>
					<td>
						<select name="class_id" id="class_id">
							<option value=""><?php esc_html_e( 'Select Class', 'school-management-system' ); ?></option>
							<?php
							$classes = Classm::get_all( array(), 100 );
							foreach ( $classes as $class ) {
								?>
								<option value="<?php echo intval( $class->id ); ?>" <?php selected( $class_id, $class->id ); ?>>
									<?php echo esc_html( $class->class_name ); ?>
								</option>
								<?php
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="date"><?php esc_html_e( 'Date', 'school-management-system' ); ?></label></th>
					<td>
						<input type="date" name="date" id="date" value="<?php echo esc_attr( $date ); ?>" />
					</td>
				</tr>
			</table>
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Manage Attendance', 'school-management-system' ); ?></button>
		</form>
	</div>

	<?php if ( $class_id > 0 ) : ?>
		<form method="post" action="">
			<?php wp_nonce_field( 'sms_nonce_form', 'sms_nonce' ); ?>
			<input type="hidden" name="class_id" value="<?php echo intval( $class_id ); ?>" />
			<input type="hidden" name="attendance_date" value="<?php echo esc_attr( $date ); ?>" />

			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Roll Number', 'school-management-system' ); ?></th>
						<th><?php esc_html_e( 'Student Name', 'school-management-system' ); ?></th>
						<th><?php esc_html_e( 'Status', 'school-management-system' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$students = Classm::get_students( $class_id );
					if ( ! empty( $students ) ) {
						foreach ( $students as $student ) {
							// Get existing attendance for this specific date.
							$records = Attendance::get_all( array( 'student_id' => $student->id, 'class_id' => $class_id, 'attendance_date' => $date ), 1 );
							$current_status = ! empty( $records ) ? $records[0]->status : 'present';
							?>
							<tr>
								<td><?php echo esc_html( $student->roll_number ); ?></td>
								<td><?php echo esc_html( $student->first_name . ' ' . $student->last_name ); ?></td>
								<td>
									<label style="margin-right: 10px;">
										<input type="radio" name="attendance[<?php echo intval( $student->id ); ?>]" value="present" <?php checked( $current_status, 'present' ); ?> />
										<?php esc_html_e( 'Present', 'school-management-system' ); ?>
									</label>
									<label style="margin-right: 10px;">
										<input type="radio" name="attendance[<?php echo intval( $student->id ); ?>]" value="absent" <?php checked( $current_status, 'absent' ); ?> />
										<?php esc_html_e( 'Absent', 'school-management-system' ); ?>
									</label>
									<label style="margin-right: 10px;">
										<input type="radio" name="attendance[<?php echo intval( $student->id ); ?>]" value="late" <?php checked( $current_status, 'late' ); ?> />
										<?php esc_html_e( 'Late', 'school-management-system' ); ?>
									</label>
									<label>
										<input type="radio" name="attendance[<?php echo intval( $student->id ); ?>]" value="excused" <?php checked( $current_status, 'excused' ); ?> />
										<?php esc_html_e( 'Excused', 'school-management-system' ); ?>
									</label>
								</td>
							</tr>
							<?php
						}
					} else {
						?>
						<tr>
							<td colspan="3"><?php esc_html_e( 'No students found in this class.', 'school-management-system' ); ?></td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
			<?php if ( ! empty( $students ) ) : ?>
				<p class="submit">
					<button type="submit" name="sms_mark_attendance" class="button button-primary"><?php esc_html_e( 'Save Attendance', 'school-management-system' ); ?></button>
				</p>
			<?php endif; ?>
		</form>
	<?php endif; ?>
</div>