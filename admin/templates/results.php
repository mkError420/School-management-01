<?php
/**
 * Results admin template.
 *
 * @package School_Management_System
 */

use School_Management_System\Result;
use School_Management_System\Exam;
use School_Management_System\Student;
use School_Management_System\Classm;
use School_Management_System\Subject;

if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'edit_posts' ) ) {
	wp_die( esc_html__( 'Unauthorized', 'school-management-system' ) );
}

$result = null;
$is_edit = false;
$action = sanitize_text_field( $_GET['action'] ?? '' );
$result_id = intval( $_GET['id'] ?? 0 );

// Filters
$class_id_filter = intval( $_GET['class_id'] ?? 0 );
$exam_id_filter = intval( $_GET['exam_id'] ?? 0 );
$subject_id_filter = intval( $_GET['subject_id'] ?? 0 );

if ( 'edit' === $action && $result_id ) {
	$result = Result::get( $result_id );
	if ( ! $result ) {
		wp_die( esc_html__( 'Result record not found', 'school-management-system' ) );
	}
	$is_edit = true;
}

$show_form = ( 'add' === $action || $is_edit );
?>

<div class="wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Exam Results', 'school-management-system' ); ?></h1>
	<?php if ( ! $show_form ) : ?>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-results&action=add' ) ); ?>" class="page-title-action"><?php esc_html_e( 'Add New Result', 'school-management-system' ); ?></a>
	<?php endif; ?>
	<hr class="wp-header-end">

	<?php if ( $show_form ) : ?>
		<div class="card" style="max-width: 100%; padding: 20px; margin-top: 20px;">
			<h2><?php echo $is_edit ? esc_html__( 'Edit Result', 'school-management-system' ) : esc_html__( 'Add New Result', 'school-management-system' ); ?></h2>
			<form method="post" action="">
				<?php wp_nonce_field( 'sms_nonce_form', 'sms_nonce' ); ?>
				<table class="form-table">
					<tr>
						<th scope="row"><label for="exam_id"><?php esc_html_e( 'Exam', 'school-management-system' ); ?></label></th>
						<td>
							<select name="exam_id" id="exam_id" required>
								<option value=""><?php esc_html_e( 'Select Exam', 'school-management-system' ); ?></option>
								<?php
								$exams = Exam::get_all( array(), 1000 );
								foreach ( $exams as $exam ) {
									$class = Classm::get( $exam->class_id );
									$class_name = $class ? $class->class_name : 'Unknown Class';
									?>
									<option value="<?php echo intval( $exam->id ); ?>" <?php selected( $result ? $result->exam_id : 0, $exam->id ); ?>>
										<?php echo esc_html( $exam->exam_name . ' (' . $class_name . ')' ); ?>
									</option>
									<?php
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="subject_id"><?php esc_html_e( 'Subject', 'school-management-system' ); ?></label></th>
						<td>
							<select name="subject_id" id="subject_id" required>
								<option value=""><?php esc_html_e( 'Select Subject', 'school-management-system' ); ?></option>
								<?php
								$subjects = Subject::get_all( array(), 1000 );
								foreach ( $subjects as $subject ) {
									?>
									<option value="<?php echo intval( $subject->id ); ?>" <?php selected( $result ? $result->subject_id : 0, $subject->id ); ?>>
										<?php echo esc_html( $subject->subject_name . ' (' . $subject->subject_code . ')' ); ?>
									</option>
									<?php
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="student_id"><?php esc_html_e( 'Student', 'school-management-system' ); ?></label></th>
						<td>
							<select name="student_id" id="student_id" required>
								<option value=""><?php esc_html_e( 'Select Student', 'school-management-system' ); ?></option>
								<?php
								$students = Student::get_all( array(), 1000 );
								foreach ( $students as $student ) {
									?>
									<option value="<?php echo intval( $student->id ); ?>" <?php selected( $result ? $result->student_id : 0, $student->id ); ?>>
										<?php echo esc_html( $student->first_name . ' ' . $student->last_name . ' (' . $student->roll_number . ')' ); ?>
									</option>
									<?php
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="obtained_marks"><?php esc_html_e( 'Obtained Marks', 'school-management-system' ); ?></label></th>
						<td>
							<input type="number" name="obtained_marks" id="obtained_marks" step="0.01" required value="<?php echo $result ? esc_attr( $result->obtained_marks ) : ''; ?>" />
						</td>
					</tr>
				</table>
				<?php if ( $is_edit ) : ?>
					<input type="hidden" name="result_id" value="<?php echo intval( $result->id ); ?>" />
					<button type="submit" name="sms_edit_result" class="button button-primary"><?php esc_html_e( 'Update Result', 'school-management-system' ); ?></button>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-results' ) ); ?>" class="button"><?php esc_html_e( 'Cancel', 'school-management-system' ); ?></a>
				<?php else : ?>
					<button type="submit" name="sms_add_result" class="button button-primary"><?php esc_html_e( 'Add Result', 'school-management-system' ); ?></button>
				<?php endif; ?>
			</form>
		</div>
	<?php else : ?>

		<!-- Filters -->
		<div class="tablenav top">
			<form method="get" action="">
				<input type="hidden" name="page" value="sms-results" />
				<div class="alignleft actions">
					<select name="class_id">
						<option value=""><?php esc_html_e( 'All Classes', 'school-management-system' ); ?></option>
						<?php
						$classes = Classm::get_all();
						foreach ( $classes as $class ) {
							echo '<option value="' . intval( $class->id ) . '" ' . selected( $class_id_filter, $class->id, false ) . '>' . esc_html( $class->class_name ) . '</option>';
						}
						?>
					</select>

					<select name="exam_id">
						<option value=""><?php esc_html_e( 'All Exams', 'school-management-system' ); ?></option>
						<?php
						$exams = Exam::get_all();
						foreach ( $exams as $exam ) {
							echo '<option value="' . intval( $exam->id ) . '" ' . selected( $exam_id_filter, $exam->id, false ) . '>' . esc_html( $exam->exam_name ) . '</option>';
						}
						?>
					</select>

					<select name="subject_id">
						<option value=""><?php esc_html_e( 'All Subjects', 'school-management-system' ); ?></option>
						<?php
						$subjects = Subject::get_all();
						foreach ( $subjects as $subject ) {
							echo '<option value="' . intval( $subject->id ) . '" ' . selected( $subject_id_filter, $subject->id, false ) . '>' . esc_html( $subject->subject_name ) . '</option>';
						}
						?>
					</select>

					<button type="submit" class="button"><?php esc_html_e( 'Filter', 'school-management-system' ); ?></button>
				</div>
			</form>
		</div>

		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Student', 'school-management-system' ); ?></th>
					<th><?php esc_html_e( 'Class', 'school-management-system' ); ?></th>
					<th><?php esc_html_e( 'Exam', 'school-management-system' ); ?></th>
					<th><?php esc_html_e( 'Subject', 'school-management-system' ); ?></th>
					<th><?php esc_html_e( 'Marks', 'school-management-system' ); ?></th>
					<th><?php esc_html_e( 'Grade', 'school-management-system' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$filters = array(
					'class_id'   => $class_id_filter,
					'exam_id'    => $exam_id_filter,
					'subject_id' => $subject_id_filter,
				);
				$results = Result::get_by_filters( $filters );

				if ( ! empty( $results ) ) {
					foreach ( $results as $row ) {
						?>
						<tr>
							<td>
								<strong><?php echo esc_html( $row->first_name . ' ' . $row->last_name ); ?></strong><br>
								<span class="description"><?php echo esc_html( $row->roll_number ); ?></span>
							</td>
							<td><?php echo esc_html( $row->class_name ); ?></td>
							<td><?php echo esc_html( $row->exam_name ); ?></td>
							<td><?php echo esc_html( $row->subject_name ); ?></td>
							<td><?php echo esc_html( $row->obtained_marks ); ?></td>
							<td>
								<span class="sms-status-badge" style="background: #e8f5e9; color: #2e7d32; padding: 4px 8px; border-radius: 4px;">
									<?php echo esc_html( $row->grade ); ?>
								</span>
								(<?php echo number_format( $row->percentage, 1 ); ?>%)
							</td>
						</tr>
						<?php
					}
				} else {
					echo '<tr><td colspan="6">' . esc_html__( 'No results found.', 'school-management-system' ) . '</td></tr>';
				}
				?>
			</tbody>
		</table>
	<?php endif; ?>
</div>