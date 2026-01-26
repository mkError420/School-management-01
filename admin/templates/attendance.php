<?php
/**
 * Attendance admin template.
 *
 * @package School_Management_System
 */

use School_Management_System\Attendance;

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'Unauthorized', 'school-management-system' ) );
}

$message = '';
$message_class = 'notice-success';
if ( isset( $_GET['sms_message'] ) ) {
	$sms_message = sanitize_text_field( $_GET['sms_message'] );
	if ( 'file_uploaded' === $sms_message ) {
		$message = __( 'File uploaded successfully.', 'school-management-system' );
	} elseif ( 'file_deleted' === $sms_message ) {
		$message = __( 'File deleted successfully.', 'school-management-system' );
	} elseif ( 'file_upload_error' === $sms_message ) {
		$message = __( 'Error uploading file.', 'school-management-system' );
		$message_class = 'notice-error';
	} elseif ( 'no_file_selected' === $sms_message ) {
		$message = __( 'No file was selected for upload.', 'school-management-system' );
		$message_class = 'notice-warning';
	}
}

?>
<div class="wrap">
	<h1><?php esc_html_e( 'Attendance', 'school-management-system' ); ?></h1>

	<?php if ( ! empty( $message ) ) : ?>
		<div class="notice <?php echo esc_attr( $message_class ); ?> is-dismissible"><p><?php echo esc_html( $message ); ?></p></div>
	<?php endif; ?>

	<!-- File Upload Form -->
	<div style="background: #fff; padding: 20px; border: 1px solid #ddd; margin-bottom: 30px; border-radius: 4px;">
		<h2><?php esc_html_e( 'Upload Attendance File', 'school-management-system' ); ?></h2>
		<p><?php esc_html_e( 'Upload a PDF, Word, or CSV file containing attendance records.', 'school-management-system' ); ?></p>
		<form method="post" action="" enctype="multipart/form-data">
			<?php wp_nonce_field( 'sms_attendance_upload_nonce', 'sms_attendance_upload_nonce_field' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="attendance_file"><?php esc_html_e( 'Attendance File', 'school-management-system' ); ?></label>
					</th>
					<td>
						<input type="file" name="attendance_file" id="attendance_file" accept=".pdf,.doc,.docx,.csv" />
					</td>
				</tr>
			</table>
			<button type="submit" name="sms_upload_attendance_file" class="button button-primary">
				<?php esc_html_e( 'Upload File', 'school-management-system' ); ?>
			</button>
		</form>
	</div>

	<!-- Display Uploaded File -->
	<?php
	$uploaded_file = get_option( 'sms_attendance_uploaded_file' );
	if ( $uploaded_file && ! empty( $uploaded_file['url'] ) ) :
		$delete_url = wp_nonce_url( admin_url( 'admin.php?page=sms-attendance&action=delete_attendance_file' ), 'sms_delete_attendance_file_nonce', '_wpnonce' );
		?>
	<div style="background: #fff; padding: 20px; border: 1px solid #ddd; margin-bottom: 30px; border-radius: 4px;">
		<h2><?php esc_html_e( 'Current Attendance File', 'school-management-system' ); ?></h2>
		<p><a href="<?php echo esc_url( $uploaded_file['url'] ); ?>" target="_blank"><?php echo esc_html( basename( $uploaded_file['file'] ) ); ?></a></p>
		<a href="<?php echo esc_url( $delete_url ); ?>" class="button button-danger" onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to delete this file?', 'school-management-system' ); ?>')"><?php esc_html_e( 'Delete File', 'school-management-system' ); ?></a>
	</div>
	<?php endif; ?>

	<h2><?php esc_html_e( 'Attendance Records', 'school-management-system' ); ?></h2>
	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'ID', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Student ID', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Class ID', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Date', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Status', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'school-management-system' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$records = Attendance::get_all( array(), 50 );
			if ( ! empty( $records ) ) {
				foreach ( $records as $record ) {
					?>
					<tr>
						<td><?php echo intval( $record->id ); ?></td>
						<td><?php echo intval( $record->student_id ); ?></td>
						<td><?php echo intval( $record->class_id ); ?></td>
						<td><?php echo esc_html( $record->attendance_date ); ?></td>
						<td><?php echo esc_html( $record->status ); ?></td>
						<td>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-attendance&action=edit&id=' . $record->id ) ); ?>">
								<?php esc_html_e( 'Edit', 'school-management-system' ); ?>
							</a>
						</td>
					</tr>
					<?php
				}
			} else {
				?>
				<tr>
					<td colspan="6"><?php esc_html_e( 'No attendance records found', 'school-management-system' ); ?></td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
</div>
