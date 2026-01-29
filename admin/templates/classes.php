<?php
/**
 * Classes admin template.
 *
 * @package School_Management_System
 */

use School_Management_System\Classm;

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'Unauthorized', 'school-management-system' ) );
}

$class = null;
$is_edit = false;
$action = sanitize_text_field( $_GET['action'] ?? '' );
$class_id = intval( $_GET['id'] ?? 0 );

if ( 'edit' === $action && $class_id ) {
	$class = Classm::get( $class_id );
	if ( ! $class ) {
		wp_die( esc_html__( 'Class not found', 'school-management-system' ) );
	}
	$is_edit = true;
}

$message = '';
if ( isset( $_GET['sms_message'] ) ) {
	$sms_message = sanitize_text_field( $_GET['sms_message'] );
	if ( 'classes_bulk_deleted' === $sms_message ) {
		$count = intval( $_GET['count'] ?? 0 );
		$message = sprintf( __( '%d classes deleted successfully.', 'school-management-system' ), $count );
	}
}

?>
<div class="wrap">
	<h1><?php esc_html_e( 'Classes', 'school-management-system' ); ?></h1>

	<?php if ( ! empty( $message ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $message ); ?></p></div>
	<?php endif; ?>

	<!-- Add/Edit Form -->
	<div style="background: #fff; padding: 20px; border: 1px solid #ddd; margin-bottom: 30px; border-radius: 4px;">
		<h2><?php echo $is_edit ? esc_html__( 'Edit Class', 'school-management-system' ) : esc_html__( 'Add New Class', 'school-management-system' ); ?></h2>

		<form method="post" action="">
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="class_name"><?php esc_html_e( 'Class Name *', 'school-management-system' ); ?></label>
					</th>
					<td>
						<input type="text" name="class_name" id="class_name" required value="<?php echo $class ? esc_attr( $class->class_name ) : ''; ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="class_code"><?php esc_html_e( 'Class Code *', 'school-management-system' ); ?></label>
					</th>
					<td>
						<input type="text" name="class_code" id="class_code" required value="<?php echo $class ? esc_attr( $class->class_code ) : ''; ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="capacity"><?php esc_html_e( 'Capacity', 'school-management-system' ); ?></label>
					</th>
					<td>
						<input type="number" name="capacity" id="capacity" value="<?php echo $class ? intval( $class->capacity ) : ''; ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="status"><?php esc_html_e( 'Status', 'school-management-system' ); ?></label>
					</th>
					<td>
						<select name="status" id="status">
							<option value="active" <?php echo ! $class || 'active' === $class->status ? 'selected' : ''; ?>>
								<?php esc_html_e( 'Active', 'school-management-system' ); ?>
							</option>
							<option value="inactive" <?php echo $class && 'inactive' === $class->status ? 'selected' : ''; ?>>
								<?php esc_html_e( 'Inactive', 'school-management-system' ); ?>
							</option>
						</select>
					</td>
				</tr>
			</table>

			<?php if ( $is_edit ) : ?>
				<input type="hidden" name="class_id" value="<?php echo intval( $class->id ); ?>" />
				<button type="submit" name="sms_edit_class" class="button button-primary">
					<?php esc_html_e( 'Update Class', 'school-management-system' ); ?>
				</button>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-classes' ) ); ?>" class="button">
					<?php esc_html_e( 'Cancel', 'school-management-system' ); ?>
				</a>
			<?php else : ?>
				<button type="submit" name="sms_add_class" class="button button-primary">
					<?php esc_html_e( 'Add Class', 'school-management-system' ); ?>
				</button>
			<?php endif; ?>
		</form>
	</div>

	<!-- Classes List -->
	<h2><?php esc_html_e( 'Classes List', 'school-management-system' ); ?></h2>

	<form method="get" action="" style="margin-bottom: 20px; float: right;">
		<input type="hidden" name="page" value="sms-classes" />
		<input type="search" name="s" value="<?php echo isset( $_GET['s'] ) ? esc_attr( $_GET['s'] ) : ''; ?>" placeholder="<?php esc_attr_e( 'Search classes...', 'school-management-system' ); ?>" />
		<button type="submit" class="button"><?php esc_html_e( 'Search', 'school-management-system' ); ?></button>
		<?php if ( ! empty( $_GET['s'] ) ) : ?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-classes' ) ); ?>" class="button"><?php esc_html_e( 'Reset', 'school-management-system' ); ?></a>
		<?php endif; ?>
	</form>
	<div style="clear: both;"></div>

	<form method="post" action="">
	<?php wp_nonce_field( 'sms_bulk_delete_classes_nonce', 'sms_bulk_delete_classes_nonce' ); ?>
	<div class="tablenav top">
		<div class="alignleft actions bulkactions">
			<select name="action">
				<option value="-1"><?php esc_html_e( 'Bulk Actions', 'school-management-system' ); ?></option>
				<option value="bulk_delete_classes"><?php esc_html_e( 'Delete', 'school-management-system' ); ?></option>
			</select>
			<input type="submit" class="button action" value="<?php esc_attr_e( 'Apply', 'school-management-system' ); ?>">
		</div>
	</div>
	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<td id="cb" class="manage-column column-cb check-column"><input id="cb-select-all-classes" type="checkbox"></td>
				<th><?php esc_html_e( 'ID', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Class Name', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Class Code', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Capacity', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Status', 'school-management-system' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'school-management-system' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$search_term = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
			if ( ! empty( $search_term ) ) {
				$classes = Classm::search( $search_term );
			} else {
				$classes = Classm::get_all( array(), 50 );
			}
			if ( ! empty( $classes ) ) {
				foreach ( $classes as $class ) {
					?>
					<tr>
						<th scope="row" class="check-column"><input type="checkbox" name="class_ids[]" value="<?php echo intval( $class->id ); ?>"></th>
						<td><?php echo intval( $class->id ); ?></td>
						<td><?php echo esc_html( $class->class_name ); ?></td>
						<td><?php echo esc_html( $class->class_code ); ?></td>
						<td><?php echo intval( $class->capacity ); ?></td>
						<td><?php echo esc_html( $class->status ); ?></td>
						<td>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-classes&action=edit&id=' . $class->id ) ); ?>">
								<?php esc_html_e( 'Edit', 'school-management-system' ); ?>
							</a>
						</td>
					</tr>
					<?php
				}
			} else {
				?>
				<tr>
					<td colspan="7"><?php esc_html_e( 'No classes found', 'school-management-system' ); ?></td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
	</form>
</div>
