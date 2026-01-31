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
use School_Management_System\Enrollment;

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
$student_id_filter = intval( $_GET['student_id'] ?? 0 );

if ( 'edit' === $action && $result_id ) {
	$result = Result::get( $result_id );
	if ( ! $result ) {
		wp_die( esc_html__( 'Result record not found', 'school-management-system' ) );
	}
	$is_edit = true;
}

$show_form = ( 'add' === $action || $is_edit );
?>

<style>
 .sms-results-page { max-width: 100%; }
 .sms-results-header {
 	display: flex;
 	justify-content: space-between;
 	align-items: flex-start;
 	gap: 16px;
 	background: linear-gradient(135deg, #00c9ff 0%, #92fe9d 100%);
 	color: #fff;
 	padding: 22px;
 	border-radius: 16px;
 	box-shadow: 0 10px 30px rgba(0, 201, 255, 0.22);
 	margin: 10px 0 18px;
 }
 .sms-results-title h1 { margin: 0; color: #fff; font-size: 22px; line-height: 1.2; }
 .sms-results-subtitle { margin: 6px 0 0; opacity: 0.92; font-size: 13px; }
 .sms-results-header-actions { display: flex; gap: 10px; flex-wrap: wrap; justify-content: flex-end; }
 .sms-cta-btn {
 	background: #2c3e50;
 	border: 1px solid #2c3e50;
 	color: #fff;
 	padding: 10px 14px;
 	border-radius: 10px;
 	font-weight: 700;
 	text-decoration: none;
 	display: inline-flex;
 	align-items: center;
 	gap: 8px;
 	cursor: pointer;
 }
 .sms-cta-btn:hover { background: #1a252f; color: #fff; border-color: #1a252f; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }

 .sms-panel {
 	background: #fff;
 	border: 1px solid #e9ecef;
 	border-radius: 16px;
 	box-shadow: 0 8px 22px rgba(0,0,0,0.06);
 	overflow: hidden;
 	margin-bottom: 18px;
 }
 .sms-panel-header {
 	background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
 	color: #fff;
 	padding: 14px 18px;
 	display: flex;
 	justify-content: space-between;
 	align-items: center;
 	gap: 12px;
 }
 .sms-panel-header h2 { margin: 0; font-size: 15px; font-weight: 800; color: #fff; }
 .sms-panel-body { padding: 18px; }

 .sms-status-badge {
 	display: inline-flex;
 	align-items: center;
 	gap: 6px;
 	padding: 6px 10px;
 	border-radius: 999px;
 	font-size: 11px;
 	font-weight: 800;
 	text-transform: uppercase;
 	letter-spacing: 0.4px;
 	border: 1px solid;
 }
 .sms-status-badge { background: rgba(67, 233, 123, 0.12); color: #155724; border-color: rgba(67, 233, 123, 0.28); }

 @media (max-width: 782px) {
 	.sms-results-header { flex-direction: column; align-items: flex-start; }
 	.sms-results-header-actions { width: 100%; justify-content: flex-start; }
 }
</style>

<div class="wrap">
	<div class="sms-results-page">
		<div class="sms-results-header">
			<div class="sms-results-title">
				<h1><?php esc_html_e( 'Exam Results', 'school-management-system' ); ?></h1>
				<div class="sms-results-subtitle"><?php esc_html_e( 'Manage student exam results, grades, and performance.', 'school-management-system' ); ?></div>
			</div>
			<div class="sms-results-header-actions">
				<?php if ( ! $show_form ) : ?>
					<a class="sms-cta-btn" href="<?php echo esc_url( admin_url( 'admin.php?page=sms-results&action=add' ) ); ?>">
						<span class="dashicons dashicons-plus-alt"></span>
						<?php esc_html_e( 'Add New Result', 'school-management-system' ); ?>
					</a>
				<?php else : ?>
					<a class="sms-cta-btn" href="<?php echo esc_url( admin_url( 'admin.php?page=sms-results' ) ); ?>">
						<span class="dashicons dashicons-arrow-left-alt"></span>
						<?php esc_html_e( 'Back to Results', 'school-management-system' ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $show_form ) : ?>
			<div class="sms-panel" id="sms-result-form">
				<div class="sms-panel-header">
					<h2><?php echo $is_edit ? esc_html__( 'Edit Result', 'school-management-system' ) : esc_html__( 'Add New Result', 'school-management-system' ); ?></h2>
				</div>
				<div class="sms-panel-body">
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
											<option value="<?php echo intval( $exam->id ); ?>" data-class-id="<?php echo intval( $exam->class_id ); ?>" <?php selected( $result ? $result->exam_id : 0, $exam->id ); ?>>
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
										// Pre-fetch enrollments to map students to classes.
										$student_class_map = array();
										$all_enrollments = Enrollment::get_all( array( 'status' => 'active' ), 5000 );
										if ( ! empty( $all_enrollments ) ) {
											foreach ( $all_enrollments as $enr ) {
												$student_class_map[ $enr->student_id ] = $enr->class_id;
											}
										}

										$students = Student::get_all( array(), 1000 );
										foreach ( $students as $student ) {
											$s_class_id = isset( $student_class_map[ $student->id ] ) ? $student_class_map[ $student->id ] : 0;
											?>
											<option value="<?php echo intval( $student->id ); ?>" data-class-id="<?php echo intval( $s_class_id ); ?>" <?php selected( $result ? $result->student_id : 0, $student->id ); ?>>
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
					<script>
					jQuery(document).ready(function($) {
						function filterStudents() {
							var examSelect = $('#exam_id');
							var studentSelect = $('#student_id');
							var selectedOption = examSelect.find('option:selected');
							var classId = selectedOption.data('class-id');
							var currentStudent = studentSelect.val();

							if (!classId) {
								studentSelect.find('option').show();
								return;
							}

							var validSelection = false;

							studentSelect.find('option').each(function() {
								var option = $(this);
								var studentClassId = option.data('class-id');
								
								if (option.val() === "") {
									option.show();
									return;
								}

								if (studentClassId == classId) {
									option.show();
									if (option.val() == currentStudent) validSelection = true;
								} else {
									option.hide();
								}
							});

							if (!validSelection) {
								studentSelect.val('');
							}
						}

						$('#exam_id').on('change', filterStudents);
						
						// Run on load if exam is selected
						if ($('#exam_id').val()) {
							filterStudents();
						}
					});
					</script>
				</div>
			</div>
		<?php else : ?>
			<div class="sms-panel" id="sms-result-filter">
				<div class="sms-panel-header">
					<h2><?php esc_html_e( 'Exam Results', 'school-management-system' ); ?></h2>
				</div>
				<div class="sms-panel-body">
					<form method="get" action="">
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
							
							<select name="student_id">
								<option value=""><?php esc_html_e( 'All Students', 'school-management-system' ); ?></option>
								<?php
								if ( $class_id_filter ) {
									$students = Student::get_by_class( $class_id_filter );
								} else {
									$students = Student::get_all( array(), 1000 );
								}
								foreach ( $students as $student ) {
									echo '<option value="' . intval( $student->id ) . '" ' . selected( $student_id_filter, $student->id, false ) . '>' . esc_html( $student->first_name . ' ' . $student->last_name . ' (' . $student->roll_number . ')' ) . '</option>';
								}
								?>
							</select>

							<button type="submit" class="button"><?php esc_html_e( 'Filter', 'school-management-system' ); ?></button>
							<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=sms-results&action=export_results&class_id=' . $class_id_filter . '&exam_id=' . $exam_id_filter . '&subject_id=' . $subject_id_filter . '&student_id=' . $student_id_filter ), 'sms_export_results_nonce' ) ); ?>" class="button button-primary" style="margin-left: 5px;">
								<span class="dashicons dashicons-download" style="line-height: 1.3;"></span> <?php esc_html_e( 'Export CSV', 'school-management-system' ); ?>
							</a>
						</div>
					</form>
				</div>
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
						'student_id' => $student_id_filter,
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
</div>