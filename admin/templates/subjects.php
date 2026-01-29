<?php
/**
 * Subjects admin template.
 *
 * @package School_Management_System
 */

use School_Management_System\Subject;

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'Unauthorized', 'school-management-system' ) );
}

$subject = null;
$is_edit = false;
$action = sanitize_text_field( $_GET['action'] ?? '' );
$subject_id = intval( $_GET['id'] ?? 0 );

if ( 'edit' === $action && $subject_id ) {
	$subject = Subject::get( $subject_id );
	if ( ! $subject ) {
		wp_die( esc_html__( 'Subject not found', 'school-management-system' ) );
	}
	$is_edit = true;
}

$message = '';
if ( isset( $_GET['sms_message'] ) ) {
	$sms_message = sanitize_text_field( $_GET['sms_message'] );
	if ( 'subjects_bulk_deleted' === $sms_message ) {
		$count = intval( $_GET['count'] ?? 0 );
		$message = sprintf( __( '%d subjects deleted successfully.', 'school-management-system' ), $count );
	}
}

?>
<div class="wrap">
	<h1><?php esc_html_e( 'Subjects', 'school-management-system' ); ?></h1>

	<?php if ( ! empty( $message ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $message ); ?></p></div>
	<?php endif; ?>

	<!-- Add/Edit Form -->
	<div style="background: #fff; padding: 20px; border: 1px solid #ddd; margin-bottom: 30px; border-radius: 4px;">
		<h2><?php echo $is_edit ? esc_html__( 'Edit Subject', 'school-management-system' ) : esc_html__( 'Add New Subject', 'school-management-system' ); ?></h2>

		<form method="post" action="">
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="subject_name"><?php esc_html_e( 'Subject Name *', 'school-management-system' ); ?></label>
					</th>
					<td>
						<input type="text" name="subject_name" id="subject_name" required value="<?php echo $subject ? esc_attr( $subject->subject_name ) : ''; ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="subject_code"><?php esc_html_e( 'Subject Code *', 'school-management-system' ); ?></label>
					</th>
					<td>
						<input type="text" name="subject_code" id="subject_code" required value="<?php echo $subject ? esc_attr( $subject->subject_code ) : ''; ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="status"><?php esc_html_e( 'Status', 'school-management-system' ); ?></label>
					</th>
					<td>
						<select name="status" id="status">
							<option value="active" <?php echo ! $subject || 'active' === $subject->status ? 'selected' : ''; ?>>
								<?php esc_html_e( 'Active', 'school-management-system' ); ?>
							</option>
							<option value="inactive" <?php echo $subject && 'inactive' === $subject->status ? 'selected' : ''; ?>>
								<?php esc_html_e( 'Inactive', 'school-management-system' ); ?>
							</option>
						</select>
					</td>
				</tr>
			</table>

			<?php if ( $is_edit ) : ?>
				<input type="hidden" name="subject_id" value="<?php echo intval( $subject->id ); ?>" />
				<button type="submit" name="sms_edit_subject" class="button button-primary">
					<?php esc_html_e( 'Update Subject', 'school-management-system' ); ?>
				</button>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-subjects' ) ); ?>" class="button">
					<?php esc_html_e( 'Cancel', 'school-management-system' ); ?>
				</a>
			<?php else : ?>
				<button type="submit" name="sms_add_subject" class="button button-primary">
					<?php esc_html_e( 'Add Subject', 'school-management-system' ); ?>
				</button>
			<?php endif; ?>
		</form>
	</div>

	<!-- Subjects List -->
	<h2><?php esc_html_e( 'Subjects List', 'school-management-system' ); ?></h2>

	<form method="get" action="" style="margin-bottom: 20px; float: right;">
		<input type="hidden" name="page" value="sms-subjects" />
		<input type="search" name="s" value="<?php echo isset( $_GET['s'] ) ? esc_attr( $_GET['s'] ) : ''; ?>" placeholder="<?php esc_attr_e( 'Search subjects...', 'school-management-system' ); ?>" />
		<button type="submit" class="button"><?php esc_html_e( 'Search', 'school-management-system' ); ?></button>
		<?php if ( ! empty( $_GET['s'] ) ) : ?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-subjects' ) ); ?>" class="button"><?php esc_html_e( 'Reset', 'school-management-system' ); ?></a>
		<?php endif; ?>
	</form>
	<div style="clear: both;"></div>

	<form method="post" action="">
	<?php wp_nonce_field( 'sms_bulk_delete_subjects_nonce', 'sms_bulk_delete_subjects_nonce' ); ?>
	<div class="tablenav top">
		<div class="alignleft actions bulkactions">
			<select name="action">
				<option value="-1"><?php esc_html_e( 'Bulk Actions', 'school-management-system' ); ?></option>
				<option value="bulk_delete_subjects"><?php esc_html_e( 'Delete', 'school-management-system' ); ?></option>
			</select>
			<input type="submit" class="button action" value="<?php esc_attr_e( 'Apply', 'school-management-system' ); ?>">
		</div>
	</div>
	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<td id="cb" class="manage-column column-cb check-column"><input id="cb-select-all-subjects" type="checkbox"></td>
				<th><?php esc_html_e( 'ID', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Subject Name', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Subject Code', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Status', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'school-management-system' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$search_term = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
			if ( ! empty( $search_term ) ) {
				$subjects = Subject::search( $search_term );
			} else {
				$subjects = Subject::get_all( array(), 50 );
			}
			if ( ! empty( $subjects ) ) {
				foreach ( $subjects as $subject ) {
					?>
					<tr>
						<th scope="row" class="check-column"><input type="checkbox" name="subject_ids[]" value="<?php echo intval( $subject->id ); ?>"></th>
						<td><?php echo intval( $subject->id ); ?></td>
						<td><?php echo esc_html( $subject->subject_name ); ?></td>
						<td><?php echo esc_html( $subject->subject_code ); ?></td>
						<td><?php echo esc_html( $subject->status ); ?></td>
						<td>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-subjects&action=edit&id=' . $subject->id ) ); ?>">
								<?php esc_html_e( 'Edit', 'school-management-system' ); ?>
							</a>
						</td>
					</tr>
					<?php
				}
			} else {
				?>
				<tr>
					<td colspan="6"><?php esc_html_e( 'No subjects found', 'school-management-system' ); ?></td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
	</form>
</div>
