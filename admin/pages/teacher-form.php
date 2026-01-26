<?php
/**
 * Teacher add/edit form.
 *
 * @package School_Management_System
 */

// The $teacher variable is set in teachers.php when editing.
$is_edit = isset( $teacher );

$first_name     = $is_edit ? $teacher->first_name : '';
$last_name      = $is_edit ? $teacher->last_name : '';
$email          = $is_edit ? $teacher->email : '';
$phone          = $is_edit ? $teacher->phone : '';
$employee_id    = $is_edit ? $teacher->employee_id : '';
$qualifications = $is_edit ? $teacher->qualification : '';
$status         = $is_edit ? $teacher->status : 'active';

?>
<div class="wrap">
	<h1>
		<?php
		if ( $is_edit ) {
			esc_html_e( 'Edit Teacher', 'school-management-system' );
		} else {
			esc_html_e( 'Add New Teacher', 'school-management-system' );
		}
		?>
	</h1>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=sms-teachers' ) ); ?>">
		<input type="hidden" name="action" value="<?php echo $is_edit ? 'sms_edit_teacher' : 'sms_add_teacher'; ?>">
		<?php if ( $is_edit ) { ?>
			<input type="hidden" name="teacher_id" value="<?php echo intval( $teacher->id ); ?>">
		<?php } ?>
		<?php wp_nonce_field( $is_edit ? 'sms_edit_teacher_' . $teacher->id : 'sms_add_teacher' ); ?>

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
						<input type="text" name="employee_id" id="employee_id" class="regular-text" value="<?php echo esc_attr( $employee_id ); ?>" required>
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
			submit_button( esc_html__( 'Update Teacher', 'school-management-system' ), 'primary', 'submit' );
		} else {
			submit_button( esc_html__( 'Add Teacher', 'school-management-system' ), 'primary', 'submit' );
		}
		?>
	</form>
</div>