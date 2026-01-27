<?php
/**
 * Exams admin template.
 *
 * @package School_Management_System
 */

use School_Management_System\Exam;
use School_Management_System\Classm;

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'Unauthorized', 'school-management-system' ) );
}

$exam = null;
$is_edit = false;
$action = sanitize_text_field( $_GET['action'] ?? '' );
$exam_id = intval( $_GET['id'] ?? 0 );

if ( 'edit' === $action && $exam_id ) {
	$exam = Exam::get( $exam_id );
	if ( ! $exam ) {
		wp_die( esc_html__( 'Exam not found', 'school-management-system' ) );
	}
	$is_edit = true;
}

?>
<div class="wrap">
	<h1><?php esc_html_e( 'School Management System Dashboard', 'school-management-system' ); ?></h1>
	<h2><?php esc_html_e( 'Exams', 'school-management-system' ); ?></h2>

	<!-- Add/Edit Form -->
	<div style="background: #fff; padding: 20px; border: 1px solid #ddd; margin-bottom: 30px; border-radius: 4px;">
		<h3><?php echo $is_edit ? esc_html__( 'Edit Exam', 'school-management-system' ) : esc_html__( 'Add New Exam', 'school-management-system' ); ?></h3>

		<form method="post" action="">
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="exam_name"><?php esc_html_e( 'Exam Name *', 'school-management-system' ); ?></label>
					</th>
					<td>
						<input type="text" name="exam_name" id="exam_name" required value="<?php echo $exam ? esc_attr( $exam->exam_name ) : ''; ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="exam_code"><?php esc_html_e( 'Exam Code *', 'school-management-system' ); ?></label>
					</th>
					<td>
						<input type="text" name="exam_code" id="exam_code" required value="<?php echo $exam ? esc_attr( $exam->exam_code ) : ''; ?>" />
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
								<option value="<?php echo intval( $class->id ); ?>" <?php echo $exam && $exam->class_id === $class->id ? 'selected' : ''; ?>>
									<?php echo esc_html( $class->class_name ); ?>
								</option>
								<?php
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="exam_date"><?php esc_html_e( 'Exam Date *', 'school-management-system' ); ?></label>
					</th>
					<td>
						<input type="date" name="exam_date" id="exam_date" required value="<?php echo $exam ? esc_attr( $exam->exam_date ) : ''; ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="total_marks"><?php esc_html_e( 'Total Marks', 'school-management-system' ); ?></label>
					</th>
					<td>
						<input type="number" name="total_marks" id="total_marks" value="<?php echo $exam ? intval( $exam->total_marks ) : '100'; ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="passing_marks"><?php esc_html_e( 'Passing Marks', 'school-management-system' ); ?></label>
					</th>
					<td>
						<input type="number" name="passing_marks" id="passing_marks" value="<?php echo $exam ? intval( $exam->passing_marks ) : '40'; ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="status"><?php esc_html_e( 'Status', 'school-management-system' ); ?></label>
					</th>
					<td>
						<select name="status" id="status">
							<option value="scheduled" <?php echo ! $exam || 'scheduled' === $exam->status ? 'selected' : ''; ?>>
								<?php esc_html_e( 'Scheduled', 'school-management-system' ); ?>
							</option>
							<option value="completed" <?php echo $exam && 'completed' === $exam->status ? 'selected' : ''; ?>>
								<?php esc_html_e( 'Completed', 'school-management-system' ); ?>
							</option>
							<option value="cancelled" <?php echo $exam && 'cancelled' === $exam->status ? 'selected' : ''; ?>>
								<?php esc_html_e( 'Cancelled', 'school-management-system' ); ?>
							</option>
						</select>
					</td>
				</tr>
			</table>

			<?php if ( $is_edit ) : ?>
				<input type="hidden" name="exam_id" value="<?php echo intval( $exam->id ); ?>" />
				<button type="submit" name="sms_edit_exam" class="button button-primary">
					<?php esc_html_e( 'Update Exam', 'school-management-system' ); ?>
				</button>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-exams' ) ); ?>" class="button">
					<?php esc_html_e( 'Cancel', 'school-management-system' ); ?>
				</a>
			<?php else : ?>
				<button type="submit" name="sms_add_exam" class="button button-primary">
					<?php esc_html_e( 'Add Exam', 'school-management-system' ); ?>
				</button>
			<?php endif; ?>
		</form>
	</div>

	<!-- Exams List -->
	<h3><?php esc_html_e( 'Exams List', 'school-management-system' ); ?></h3>

	<form method="get" action="" style="margin-bottom: 20px; float: right;">
		<input type="hidden" name="page" value="sms-exams" />
		<input type="search" name="s" value="<?php echo isset( $_GET['s'] ) ? esc_attr( $_GET['s'] ) : ''; ?>" placeholder="<?php esc_attr_e( 'Search exams...', 'school-management-system' ); ?>" />
		<button type="submit" class="button"><?php esc_html_e( 'Search', 'school-management-system' ); ?></button>
		<?php if ( ! empty( $_GET['s'] ) ) : ?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-exams' ) ); ?>" class="button"><?php esc_html_e( 'Reset', 'school-management-system' ); ?></a>
		<?php endif; ?>
	</form>
	<div style="clear: both;"></div>

	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'ID', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Exam Name', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Class', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Exam Code', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Date', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Status', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'school-management-system' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$search_term = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
			if ( ! empty( $search_term ) ) {
				$exams = Exam::search( $search_term );
			} else {
				$exams = Exam::get_all( array(), 50 );
			}
			if ( ! empty( $exams ) ) {
				foreach ( $exams as $exam ) {
					$class = Classm::get( $exam->class_id );
					?>
					<tr>
						<td><?php echo intval( $exam->id ); ?></td>
						<td><?php echo esc_html( $exam->exam_name ); ?></td>
						<td><?php echo $class ? esc_html( $class->class_name ) : ''; ?></td>
						<td><?php echo esc_html( $exam->exam_code ); ?></td>
						<td><?php echo esc_html( $exam->exam_date ); ?></td>
						<td><?php echo esc_html( $exam->status ); ?></td>
						<td>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-exams&action=edit&id=' . $exam->id ) ); ?>">
								<?php esc_html_e( 'Edit', 'school-management-system' ); ?>
							</a>
						</td>
					</tr>
					<?php
				}
			} else {
				?>
				<tr>
					<td colspan="7"><?php esc_html_e( 'No exams found', 'school-management-system' ); ?></td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
</div>
