<?php
/**
 * Teachers admin template.
 *
 * @package School_Management_System
 */

use School_Management_System\Teacher;

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'Unauthorized', 'school-management-system' ) );
}

$teacher = null;
$is_edit = false;
$action = sanitize_text_field( $_GET['action'] ?? '' );
$teacher_id = intval( $_GET['id'] ?? 0 );

if ( 'edit' === $action && $teacher_id ) {
	$teacher = Teacher::get( $teacher_id );
	if ( ! $teacher ) {
		wp_die( esc_html__( 'Teacher not found', 'school-management-system' ) );
	}
	$is_edit = true;
}

// The $teacher variable is set above when editing.
$first_name     = $is_edit ? $teacher->first_name : '';
$last_name      = $is_edit ? $teacher->last_name : '';
$email          = $is_edit ? $teacher->email : '';
$phone          = $is_edit ? $teacher->phone : '';
$employee_id    = $is_edit ? $teacher->employee_id : '';
$qualifications = $is_edit ? $teacher->qualification : '';
$status         = $is_edit ? $teacher->status : 'active';

?>
<style>
/* Responsive styles for the teachers list */
@media screen and (max-width: 782px) {
	.wp-list-table.teachers-table {
		border: 0;
	}
	.wp-list-table.teachers-table thead {
		display: none;
	}
	.wp-list-table.teachers-table tr {
		margin-bottom: 20px;
		display: block;
		border: 1px solid #ddd;
		border-radius: 4px;
		box-shadow: 0 1px 1px rgba(0,0,0,.04);
	}
	.wp-list-table.teachers-table td {
		display: block;
		text-align: right;
		border-bottom: 1px solid #eee;
		padding-right: 15px;
	}
	.wp-list-table.teachers-table td:last-child {
		border-bottom: 0;
	}
	.wp-list-table.teachers-table td::before {
		content: attr(data-label);
		float: left;
		font-weight: bold;
	}
}
</style>
<div class="wrap">
	<h1><?php esc_html_e( 'Teachers', 'school-management-system' ); ?></h1>

	<!-- Add/Edit Form -->
	<div style="background: #fff; padding: 20px; border: 1px solid #ddd; margin-bottom: 30px; border-radius: 4px;">
		<h2 style="margin-top: 0; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0;">
			<?php
			if ( $is_edit ) {
				esc_html_e( 'Edit Teacher', 'school-management-system' );
			} else {
				esc_html_e( 'Add New Teacher', 'school-management-system' );
			}
			?>
		</h2>

		<form method="post" action="">
			<?php wp_nonce_field( 'sms_nonce_form', 'sms_nonce' ); ?>

			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<label for="first_name"><?php esc_html_e( 'First Name', 'school-management-system' ); ?></label>
						</th>
						<td>
							<input type="text" name="first_name" id="first_name" class="regular-text" value="<?php echo esc_attr( $first_name ); ?>" required>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="last_name"><?php esc_html_e( 'Last Name', 'school-management-system' ); ?></label>
						</th>
						<td>
							<input type="text" name="last_name" id="last_name" class="regular-text" value="<?php echo esc_attr( $last_name ); ?>" required>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="email"><?php esc_html_e( 'Email', 'school-management-system' ); ?></label>
						</th>
						<td>
							<input type="email" name="email" id="email" class="regular-text" value="<?php echo esc_attr( $email ); ?>" required>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="phone"><?php esc_html_e( 'Phone', 'school-management-system' ); ?></label>
						</th>
						<td>
							<input type="text" name="phone" id="phone" class="regular-text" value="<?php echo esc_attr( $phone ); ?>">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="employee_id"><?php esc_html_e( 'Employee ID', 'school-management-system' ); ?></label>
						</th>
						<td>
							<input type="text" name="employee_id" id="employee_id" class="regular-text" value="<?php echo esc_attr( $employee_id ); ?>" placeholder="<?php esc_attr_e( 'Auto-generated if empty', 'school-management-system' ); ?>">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="qualifications"><?php esc_html_e( 'Qualifications', 'school-management-system' ); ?></label>
						</th>
						<td>
							<textarea name="qualifications" id="qualifications" class="large-text" rows="5"><?php echo esc_textarea( $qualifications ); ?></textarea>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="status"><?php esc_html_e( 'Status', 'school-management-system' ); ?></label>
						</th>
						<td>
							<select name="status" id="status">
								<option value="active" <?php selected( $status, 'active' ); ?>><?php esc_html_e( 'Active', 'school-management-system' ); ?></option>
								<option value="inactive" <?php selected( $status, 'inactive' ); ?>><?php esc_html_e( 'Inactive', 'school-management-system' ); ?></option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>

			<?php
			if ( $is_edit ) {
				?>
				<input type="hidden" name="teacher_id" value="<?php echo intval( $teacher->id ); ?>" />
				<button type="submit" name="sms_edit_teacher" class="button button-primary">
					<?php esc_html_e( 'Update Teacher', 'school-management-system' ); ?>
				</button>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-teachers' ) ); ?>" class="button">
					<?php esc_html_e( 'Cancel', 'school-management-system' ); ?>
				</a>
				<?php
			} else {
				?>
				<button type="submit" name="sms_add_teacher" class="button button-primary">
					<?php esc_html_e( 'Add Teacher', 'school-management-system' ); ?>
				</button>
				<?php
			}
			?>
		</form>
	</div>

	<!-- Teachers List -->
	<h2 style="margin-top: 40px; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0;">
		<?php esc_html_e( 'Teachers List', 'school-management-system' ); ?>
	</h2>

	<form method="get" action="" style="margin-bottom: 20px; float: right;">
		<input type="hidden" name="page" value="sms-teachers" />
		<input type="search" name="s" value="<?php echo isset( $_GET['s'] ) ? esc_attr( $_GET['s'] ) : ''; ?>" placeholder="<?php esc_attr_e( 'Search by name, email, or ID...', 'school-management-system' ); ?>" />
		<button type="submit" class="button"><?php esc_html_e( 'Search', 'school-management-system' ); ?></button>
		<?php if ( ! empty( $_GET['s'] ) ) : ?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-teachers' ) ); ?>" class="button"><?php esc_html_e( 'Reset', 'school-management-system' ); ?></a>
		<?php endif; ?>
	</form>
	<div style="clear: both;"></div>

	<table class="wp-list-table widefat fixed striped teachers-table">
		<thead>
			<tr>
				<th><?php esc_html_e( 'ID', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Name', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Employee ID', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Email', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Phone', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Qualifications', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Status', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'school-management-system' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$search_term = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
			if ( ! empty( $search_term ) ) {
				$teachers = Teacher::search( $search_term );
			} else {
				$teachers = Teacher::get_all( array(), 50 );
			}
			if ( ! empty( $teachers ) ) {
				foreach ( $teachers as $teacher ) {
					?>
					<tr class="teacher-row">
						<td data-label="<?php esc_attr_e( 'ID', 'school-management-system' ); ?>"><?php echo intval( $teacher->id ); ?></td>
						<td data-label="<?php esc_attr_e( 'Name', 'school-management-system' ); ?>"><?php echo esc_html( $teacher->first_name . ' ' . $teacher->last_name ); ?></td>
						<td data-label="<?php esc_attr_e( 'Employee ID', 'school-management-system' ); ?>"><?php echo esc_html( $teacher->employee_id ); ?></td>
						<td data-label="<?php esc_attr_e( 'Email', 'school-management-system' ); ?>"><?php echo esc_html( $teacher->email ); ?></td>
						<td data-label="<?php esc_attr_e( 'Phone', 'school-management-system' ); ?>"><?php echo esc_html( $teacher->phone ); ?></td>
						<td data-label="<?php esc_attr_e( 'Qualifications', 'school-management-system' ); ?>"><?php echo esc_html( $teacher->qualification ); ?></td>
						<td data-label="<?php esc_attr_e( 'Status', 'school-management-system' ); ?>"><?php echo esc_html( $teacher->status ); ?></td>
						<td data-label="<?php esc_attr_e( 'Actions', 'school-management-system' ); ?>">
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-teachers&action=edit&id=' . $teacher->id ) ); ?>">
								<?php esc_html_e( 'Edit', 'school-management-system' ); ?>
							</a>
							|
							<a href="#" onclick="return confirm('<?php esc_attr_e( 'Are you sure?', 'school-management-system' ); ?>')">
								<?php esc_html_e( 'Delete', 'school-management-system' ); ?>
							</a>
						</td>
					</tr>
					<?php
				}
			} else {
				?>
				<tr>
					<td colspan="8"><?php esc_html_e( 'No teachers found', 'school-management-system' ); ?></td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
</div>
