<?php
/**
 * Students admin template.
 *
 * @package School_Management_System
 */

use School_Management_System\Student;
use School_Management_System\Classm;
use School_Management_System\Enrollment;

// Check user capability.
if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'Unauthorized', 'school-management-system' ) );
}

$student = null;
$is_edit = false;
$action = sanitize_text_field( $_GET['action'] ?? '' );
$student_id = intval( $_GET['id'] ?? 0 );

if ( 'edit' === $action && $student_id ) {
	$student = Student::get( $student_id );
	if ( ! $student ) {
		wp_die( esc_html__( 'Student not found', 'school-management-system' ) );
	}
	$is_edit = true;
}

$show_form = ( 'add' === $action || $is_edit );

$current_class_id = 0;
if ( $is_edit && $student ) {
	$enrollments = Enrollment::get_student_enrollments( $student->id );
	if ( ! empty( $enrollments ) ) {
		// Get the most recent enrollment.
		$current_class_id = $enrollments[0]->class_id;
	}
}

$message = '';
if ( isset( $_GET['sms_message'] ) ) {
	$sms_message = sanitize_text_field( $_GET['sms_message'] );
	if ( 'student_added' === $sms_message ) {
		$message = __( 'Student added successfully.', 'school-management-system' );
	} elseif ( 'student_updated' === $sms_message ) {
		$message = __( 'Student updated successfully.', 'school-management-system' );
	} elseif ( 'student_deleted' === $sms_message ) {
		$message = __( 'Student deleted successfully.', 'school-management-system' );
	} elseif ( 'students_bulk_deleted' === $sms_message ) {
		$count = intval( $_GET['count'] ?? 0 );
		$message = sprintf( __( '%d students deleted successfully.', 'school-management-system' ), $count );
	} elseif ( 'import_completed' === $sms_message ) {
		$count = intval( $_GET['count'] ?? 0 );
		$failed = intval( $_GET['failed'] ?? 0 );
		$error_msg = isset( $_GET['error'] ) ? sanitize_text_field( urldecode( $_GET['error'] ) ) : '';
		$message = sprintf( __( 'Import completed. %d students added successfully. %d failed.', 'school-management-system' ), $count, $failed );
		if ( $failed > 0 && ! empty( $error_msg ) ) {
			$message .= ' ' . sprintf( __( 'Last error: %s', 'school-management-system' ), $error_msg );
		} elseif ( $failed > 0 ) {
			$message .= ' ' . __( '(duplicates or missing fields)', 'school-management-system' );
		}
	}
}

?>
<style>
.student-details-row td {
	background-color: #f9f9f9;
	padding: 20px !important;
}
.student-details-list {
	list-style: none;
	margin: 0;
	padding: 0;
}
.student-details-list li {
	margin-bottom: 8px;
}
.student-details-list strong {
	display: inline-block;
	width: 120px;
	color: #555;
}
</style>
<div class="wrap">
	<h1><?php esc_html_e( 'Students', 'school-management-system' ); ?></h1>
	<hr class="wp-header-end">

	<?php if ( ! empty( $message ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $message ); ?></p></div>
	<?php endif; ?>

	<!-- Add/Edit Form -->
	<div style="background: #fff; padding: 20px; border: 1px solid #ddd; margin-bottom: 30px; border-radius: 4px;">
		<h2><?php echo $is_edit ? esc_html__( 'Edit Student', 'school-management-system' ) : esc_html__( 'Add New Student', 'school-management-system' ); ?></h2>

		<form method="post" action="">
			<?php wp_nonce_field( 'sms_nonce_form', 'sms_nonce' ); ?>

			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="first_name"><?php esc_html_e( 'Name', 'school-management-system' ); ?></label>
					</th>
					<td>
						<input type="text" name="first_name" id="first_name" required value="<?php echo $student ? esc_attr( $student->first_name ) : ''; ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="class_id"><?php esc_html_e( 'Class', 'school-management-system' ); ?></label>
					</th>
					<td>
						<select name="class_id" id="class_id">
							<option value=""><?php esc_html_e( 'Select Class', 'school-management-system' ); ?></option>
							<?php
							$classes = Classm::get_all( array(), 100 );
							foreach ( $classes as $class ) {
								?>
								<option value="<?php echo intval( $class->id ); ?>" <?php selected( $current_class_id, $class->id ); ?>>
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
						<label for="email"><?php esc_html_e( 'Email', 'school-management-system' ); ?></label>
					</th>
					<td>
						<input type="email" name="email" id="email" value="<?php echo $student ? esc_attr( $student->email ) : ''; ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="roll_number"><?php esc_html_e( 'Roll Number', 'school-management-system' ); ?></label>
					</th>
					<td>
						<input type="text" name="roll_number" id="roll_number" value="<?php echo $student ? esc_attr( $student->roll_number ) : ''; ?>" placeholder="<?php esc_attr_e( 'Auto-generated if empty', 'school-management-system' ); ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="dob"><?php esc_html_e( 'Date of Birth', 'school-management-system' ); ?></label>
					</th>
					<td>
						<input type="date" name="dob" id="dob" required value="<?php echo $student ? esc_attr( $student->dob ) : ''; ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="gender"><?php esc_html_e( 'Gender', 'school-management-system' ); ?></label>
					</th>
					<td>
						<select name="gender" id="gender" required>
							<option value=""><?php esc_html_e( 'Select', 'school-management-system' ); ?></option>
							<option value="Male" <?php echo $student && 'Male' === $student->gender ? 'selected' : ''; ?>>
								<?php esc_html_e( 'Male', 'school-management-system' ); ?>
							</option>
							<option value="Female" <?php echo $student && 'Female' === $student->gender ? 'selected' : ''; ?>>
								<?php esc_html_e( 'Female', 'school-management-system' ); ?>
							</option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="address"><?php esc_html_e( 'Address', 'school-management-system' ); ?></label>
					</th>
					<td>
						<textarea name="address" id="address" required><?php echo $student ? esc_textarea( $student->address ) : ''; ?></textarea>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="parent_name"><?php esc_html_e( 'Parent Name', 'school-management-system' ); ?></label>
					</th>
					<td>
						<input type="text" name="parent_name" id="parent_name" required value="<?php echo $student ? esc_attr( $student->parent_name ) : ''; ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="parent_phone"><?php esc_html_e( 'Parent Phone', 'school-management-system' ); ?></label>
					</th>
					<td>
						<input type="text" name="parent_phone" id="parent_phone" required value="<?php echo $student ? esc_attr( $student->parent_phone ) : ''; ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="status"><?php esc_html_e( 'Status', 'school-management-system' ); ?></label>
					</th>
					<td>
						<select name="status" id="status">
							<option value="active" <?php echo ! $student || 'active' === $student->status ? 'selected' : ''; ?>>
								<?php esc_html_e( 'Active', 'school-management-system' ); ?>
							</option>
							<option value="inactive" <?php echo $student && 'inactive' === $student->status ? 'selected' : ''; ?>>
								<?php esc_html_e( 'Inactive', 'school-management-system' ); ?>
							</option>
						</select>
					</td>
				</tr>
			</table>

			<?php if ( $is_edit ) : ?>
				<input type="hidden" name="student_id" value="<?php echo intval( $student->id ); ?>" />
				<button type="submit" name="sms_edit_student" class="button button-primary">
					<?php esc_html_e( 'Update Student', 'school-management-system' ); ?>
				</button>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-students' ) ); ?>" class="button">
					<?php esc_html_e( 'Cancel', 'school-management-system' ); ?>
				</a>
			<?php else : ?>
				<button type="submit" name="sms_add_student" class="button button-primary">
					<?php esc_html_e( 'Add Student', 'school-management-system' ); ?>
				</button>
			<?php endif; ?>
		</form>
	</div>

	<!-- Students List -->
	<h2><?php esc_html_e( 'Students List', 'school-management-system' ); ?></h2>

	<form method="get" action="" style="margin-bottom: 20px; float: right;">
		<input type="hidden" name="page" value="sms-students" />
		<input type="search" name="s" value="<?php echo isset( $_GET['s'] ) ? esc_attr( $_GET['s'] ) : ''; ?>" placeholder="<?php esc_attr_e( 'Search by name, email, or roll...', 'school-management-system' ); ?>" />
		<button type="submit" class="button"><?php esc_html_e( 'Search', 'school-management-system' ); ?></button>
		<?php if ( ! empty( $_GET['s'] ) ) : ?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-students' ) ); ?>" class="button"><?php esc_html_e( 'Reset', 'school-management-system' ); ?></a>
		<?php endif; ?>
	</form>
	<div style="clear: both;"></div>

	<form method="post" action="">
	<?php wp_nonce_field( 'sms_bulk_delete_students_nonce', 'sms_bulk_delete_nonce' ); ?>
	<div class="tablenav top">
		<div class="alignleft actions bulkactions">
			<select name="action">
				<option value="-1"><?php esc_html_e( 'Bulk Actions', 'school-management-system' ); ?></option>
				<option value="bulk_delete"><?php esc_html_e( 'Delete', 'school-management-system' ); ?></option>
			</select>
			<input type="submit" class="button action" value="<?php esc_attr_e( 'Apply', 'school-management-system' ); ?>">
		</div>
	</div>
	<table class="wp-list-table widefat fixed striped students-table">
		<thead>
			<tr>
				<td id="cb" class="manage-column column-cb check-column"><input id="cb-select-all-1" type="checkbox"></td>
				<th><?php esc_html_e( 'Name', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Class', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Parent Phone', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'school-management-system' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$search_term = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
			if ( ! empty( $search_term ) ) {
				$students = Student::search( $search_term );
			} else {
				$students = Student::get_all( array(), 50 );
			}

			if ( ! empty( $students ) ) {
				foreach ( $students as $student ) {
					$class_name = '';
					$enrollments = Enrollment::get_student_enrollments( $student->id );
					if ( ! empty( $enrollments ) ) {
						$class_obj = Classm::get( $enrollments[0]->class_id );
						if ( $class_obj ) {
							$class_name = $class_obj->class_name;
						}
					}
					$delete_url = wp_nonce_url( admin_url( 'admin.php?page=sms-students&action=delete&id=' . $student->id ), 'sms_delete_student_nonce', '_wpnonce' );
					?>
					<tr>
						<th scope="row" class="check-column"><input type="checkbox" name="student_ids[]" value="<?php echo intval( $student->id ); ?>"></th>
						<td><?php echo esc_html( $student->first_name . ' ' . $student->last_name ); ?></td>
						<td><?php echo esc_html( $class_name ); ?></td>
						<td><?php echo esc_html( $student->parent_phone ?? '' ); ?></td>
						<td>
							<button class="button button-small toggle-details-btn" data-target="#details-<?php echo intval( $student->id ); ?>">
								<?php esc_html_e( 'Details', 'school-management-system' ); ?>
							</button>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-students&action=edit&id=' . $student->id ) ); ?>">
								<?php esc_html_e( 'Edit', 'school-management-system' ); ?>
							</a>
							|
							<a href="<?php echo esc_url( $delete_url ); ?>" onclick="return confirm('<?php esc_attr_e( 'Are you sure?', 'school-management-system' ); ?>')">
								<?php esc_html_e( 'Delete', 'school-management-system' ); ?>
							</a>
						</td>
					</tr>
					<tr id="details-<?php echo intval( $student->id ); ?>" class="student-details-row" style="display: none;">
						<td colspan="5">
							<ul class="student-details-list">
								<li><strong><?php esc_html_e( 'ID', 'school-management-system' ); ?>:</strong> <?php echo intval( $student->id ); ?></li>
								<li><strong><?php esc_html_e( 'Email', 'school-management-system' ); ?>:</strong> <?php echo esc_html( $student->email ); ?></li>
								<li><strong><?php esc_html_e( 'Roll Number', 'school-management-system' ); ?>:</strong> <?php echo esc_html( $student->roll_number ?? '' ); ?></li>
								<li><strong><?php esc_html_e( 'Date of Birth', 'school-management-system' ); ?>:</strong> <?php echo esc_html( $student->dob ); ?></li>
								<li><strong><?php esc_html_e( 'Gender', 'school-management-system' ); ?>:</strong> <?php echo esc_html( $student->gender ); ?></li>
								<li><strong><?php esc_html_e( 'Address', 'school-management-system' ); ?>:</strong> <?php echo esc_html( $student->address ); ?></li>
								<li><strong><?php esc_html_e( 'Parent Name', 'school-management-system' ); ?>:</strong> <?php echo esc_html( $student->parent_name ); ?></li>
								<li><strong><?php esc_html_e( 'Status', 'school-management-system' ); ?>:</strong> <?php echo esc_html( $student->status ); ?></li>
							</ul>
						</td>
					</tr>
					<?php
				}
			} else {
				?>
				<tr>
					<td colspan="5"><?php esc_html_e( 'No students found', 'school-management-system' ); ?></td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
	</form>

	<script>
	jQuery(document).ready(function($) {
		$('.toggle-details-btn').on('click', function(e) {
			e.preventDefault();
			var targetRow = $(this).data('target');
			$(targetRow).toggle();
		});
	});
	</script>
</div>
