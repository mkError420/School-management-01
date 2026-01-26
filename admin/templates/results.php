<?php
/**
 * Results admin template.
 *
 * @package School_Management_System
 */

use School_Management_System\Result;
use School_Management_System\Student;
use School_Management_System\Exam;

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'Unauthorized', 'school-management-system' ) );
}

$result = null;
$is_edit = false;
$action = sanitize_text_field( $_GET['action'] ?? '' );
$result_id = intval( $_GET['id'] ?? 0 );

if ( 'edit' === $action && $result_id ) {
	$result = Result::get( $result_id );
	if ( ! $result ) {
		wp_die( esc_html__( 'Result not found', 'school-management-system' ) );
	}
	$is_edit = true;
}

?>
<div class="wrap">
	<h1><?php esc_html_e( 'Results', 'school-management-system' ); ?></h1>

	<!-- Add/Edit Form -->
	<div style="background: #fff; padding: 20px; border: 1px solid #ddd; margin-bottom: 30px; border-radius: 4px;">
		<h2><?php echo $is_edit ? esc_html__( 'Edit Result', 'school-management-system' ) : esc_html__( 'Add New Result', 'school-management-system' ); ?></h2>

		<form method="post" action="">
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
								<option value="<?php echo intval( $student->id ); ?>" <?php echo $result && $result->student_id === $student->id ? 'selected' : ''; ?>>
									<?php echo esc_html( $student->first_name . ' ' . $student->last_name ); ?>
								</option>
								<?php
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="exam_id"><?php esc_html_e( 'Exam *', 'school-management-system' ); ?></label>
					</th>
					<td>
						<select name="exam_id" id="exam_id" required>
							<option value=""><?php esc_html_e( 'Select Exam', 'school-management-system' ); ?></option>
							<?php
							$exams = Exam::get_all( array(), 1000 );
							foreach ( $exams as $exam ) {
								?>
								<option value="<?php echo intval( $exam->id ); ?>" <?php echo $result && $result->exam_id === $exam->id ? 'selected' : ''; ?>>
									<?php echo esc_html( $exam->exam_name ); ?>
								</option>
								<?php
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="obtained_marks"><?php esc_html_e( 'Obtained Marks *', 'school-management-system' ); ?></label>
					</th>
					<td>
						<input type="number" name="obtained_marks" id="obtained_marks" step="0.01" required value="<?php echo $result ? floatval( $result->obtained_marks ) : ''; ?>" />
					</td>
				</tr>
			</table>

			<?php if ( $is_edit ) : ?>
				<input type="hidden" name="result_id" value="<?php echo intval( $result->id ); ?>" />
				<button type="submit" name="sms_edit_result" class="button button-primary">
					<?php esc_html_e( 'Update Result', 'school-management-system' ); ?>
				</button>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-results' ) ); ?>" class="button">
					<?php esc_html_e( 'Cancel', 'school-management-system' ); ?>
				</a>
			<?php else : ?>
				<button type="submit" name="sms_add_result" class="button button-primary">
					<?php esc_html_e( 'Add Result', 'school-management-system' ); ?>
				</button>
			<?php endif; ?>
		</form>
	</div>

	<!-- Results List -->
	<h2><?php esc_html_e( 'Results List', 'school-management-system' ); ?></h2>

	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'ID', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Student ID', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Exam ID', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Obtained Marks', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Percentage', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Grade', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'school-management-system' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$results = Result::get_all( array(), 50 );
			if ( ! empty( $results ) ) {
				foreach ( $results as $result ) {
					?>
					<tr>
						<td><?php echo intval( $result->id ); ?></td>
						<td><?php echo intval( $result->student_id ); ?></td>
						<td><?php echo intval( $result->exam_id ); ?></td>
						<td><?php echo floatval( $result->obtained_marks ); ?></td>
						<td><?php echo number_format( floatval( $result->percentage ), 2 ); ?>%</td>
						<td><?php echo esc_html( $result->grade ); ?></td>
						<td>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-results&action=edit&id=' . $result->id ) ); ?>">
								<?php esc_html_e( 'Edit', 'school-management-system' ); ?>
							</a>
						</td>
					</tr>
					<?php
				}
			} else {
				?>
				<tr>
					<td colspan="7"><?php esc_html_e( 'No results found', 'school-management-system' ); ?></td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
</div>
