<?php
/**
 * Fees admin template.
 *
 * @package School_Management_System
 */

use School_Management_System\Fee;
use School_Management_System\Student;
use School_Management_System\Classm;
use School_Management_System\Enrollment;

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'Unauthorized', 'school-management-system' ) );
}

$fee = null;
$is_edit = false;
$action = sanitize_text_field( $_GET['action'] ?? '' );
$fee_id = intval( $_GET['id'] ?? 0 );
$student_id = intval( $_GET['student_id'] ?? 0 );
$class_id_filter = intval( $_GET['class_id'] ?? 0 );
$status_filter = sanitize_text_field( $_GET['status'] ?? '' );
$date_filter = sanitize_text_field( $_GET['date'] ?? '' );
$fee_type_filter = sanitize_text_field( $_GET['fee_type'] ?? '' );
$current_page = sanitize_text_field( $_GET['page'] ?? '' );

if ( 'edit' === $action && $fee_id ) {
	$fee = Fee::get( $fee_id );
	if ( ! $fee ) {
		wp_die( esc_html__( 'Fee record not found', 'school-management-system' ) );
	}
	$is_edit = true;
}

$show_form = ( 'add' === $action || $is_edit );
$show_dashboard = ( 'sms-fees' === $current_page && ! $show_form );

$message = '';
$message_class = 'notice-success';
if ( isset( $_GET['sms_message'] ) ) {
	$sms_message = sanitize_text_field( $_GET['sms_message'] );
	if ( 'fee_added' === $sms_message ) {
		$message = __( 'Fee added successfully.', 'school-management-system' );
	} elseif ( 'fee_updated' === $sms_message ) {
		$message = __( 'Fee updated successfully.', 'school-management-system' );
	} elseif ( 'fee_add_error' === $sms_message ) {
		$error_detail = isset( $_GET['error'] ) ? sanitize_text_field( urldecode( $_GET['error'] ) ) : '';
		$message = sprintf( __( 'Error: Could not add the fee record. %s', 'school-management-system' ), $error_detail );
		$message_class = 'notice-error';
	} elseif ( 'fee_update_error' === $sms_message ) {
		$error_detail = isset( $_GET['error'] ) ? sanitize_text_field( urldecode( $_GET['error'] ) ) : '';
		$message = sprintf( __( 'Error: Could not update the fee record. %s', 'school-management-system' ), $error_detail );
		$message_class = 'notice-error';
	} elseif ( 'fee_deleted' === $sms_message ) {
		$message = __( 'Fee deleted successfully.', 'school-management-system' );
	}
}

?>
<style>
/* Status Badges */
.sms-status-badge {
	padding: 6px 12px;
	border-radius: 20px;
	font-size: 11px;
	font-weight: 700;
	text-transform: uppercase;
	display: inline-block;
	letter-spacing: 0.5px;
}
.status-paid { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
.status-pending { background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
.status-partially_paid { background: #fff3e0; color: #ef6c00; border: 1px solid #ffe0b2; }

.fee-details-row td {
	background-color: #f9f9f9;
	padding: 20px !important;
}
.fee-details-list {
	list-style: none;
	margin: 0;
	padding: 0;
}
.fee-details-list li {
	margin-bottom: 8px;
}
.fee-details-list strong {
	display: inline-block;
	width: 150px;
	color: #555;
}

/* Collection Widget Tabs */
.sms-collection-tabs { display: flex; background: #f8f9fa; border-bottom: 1px solid #eee; }
.sms-collection-tab { flex: 1; text-align: center; padding: 12px 5px; cursor: pointer; font-size: 12px; font-weight: 600; color: #666; transition: all 0.2s; border-bottom: 2px solid transparent; }
.sms-collection-tab:hover { background: #f0f0f0; color: #333; }
.sms-collection-tab.active { background: #fff; color: #2271b1; border-bottom: 2px solid #2271b1; }
.sms-collection-content { display: none; animation: fadeIn 0.3s; }
.sms-collection-content.active { display: block; }
.sms-collection-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 20px; border-bottom: 1px solid #f0f0f0; }
.sms-collection-item:last-child { border-bottom: none; }
.sms-collection-label { display: flex; align-items: center; gap: 10px; font-weight: 500; color: #444; }
.sms-collection-amount { font-weight: 700; color: #2c3e50; background: #eef2f7; padding: 4px 10px; border-radius: 12px; font-size: 12px; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

/* Class-wise Fees List Design */
.sms-class-fees-container {
	margin-top: 30px;
}
.sms-class-fee-widget {
	background: #fff;
	border-radius: 8px;
	box-shadow: 0 5px 15px rgba(0,0,0,0.05);
	margin-bottom: 25px;
	overflow: hidden;
	border: 1px solid #e5e5e5;
}
.sms-class-fee-header {
	background: #f8f9fa;
	border-bottom: 1px solid #e9ecef;
	padding: 15px 20px;
	margin: 0;
	font-size: 18px;
	font-weight: 600;
	color: #343a40;
}
.sms-class-fee-widget .wp-list-table {
	border: none;
	box-shadow: none;
}
.sms-class-fee-widget .wp-list-table th {
	background: #fff;
	color: #6c757d;
	font-size: 12px;
	text-transform: uppercase;
	border-bottom: 2px solid #f1f1f1;
}
.sms-class-fee-widget .wp-list-table td {
	border-top: 1px solid #f1f1f1;
}
.sms-class-fee-widget .wp-list-table tr:first-child td {
	border-top: none;
}
</style>
<div class="wrap">
	<?php if ( $show_dashboard ) : ?>
		<?php
		// Get settings for currency.
		$settings = get_option( 'sms_settings' );
		$currency = $settings['currency'] ?? '৳';
		?>
		<h1>
			<?php esc_html_e( 'Fees Dashboard', 'school-management-system' ); ?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-fees&action=add' ) ); ?>" class="page-title-action"><?php esc_html_e( 'Add New Fee', 'school-management-system' ); ?></a>
		</h1>
		<p><?php esc_html_e( 'Here you can view, search, and manage all student fee records. Use filters to sort by class, payment status, or due dates. Click a fee entry to view or edit details.', 'school-management-system' ); ?></p>
	<?php elseif ( $show_form ) : ?>
		<h1><?php echo $is_edit ? esc_html__( 'Edit Fee', 'school-management-system' ) : esc_html__( 'Add New Fee', 'school-management-system' ); ?></h1>
	<?php endif; ?>

	<?php if ( ! empty( $message ) ) : ?>
		<div class="notice <?php echo esc_attr( $message_class ); ?> is-dismissible"><p><?php echo esc_html( $message ); ?></p></div>
	<?php endif; ?>

	<!-- Add/Edit Form -->
	<?php if ( $show_form ) : ?>
	<div style="background: #fff; padding: 20px; border: 1px solid #ddd; margin-bottom: 30px; border-radius: 4px;">
		<h2><?php echo $is_edit ? esc_html__( 'Edit Fee', 'school-management-system' ) : esc_html__( 'Add New Fee', 'school-management-system' ); ?></h2>

		<p><?php esc_html_e( 'Please fill in the details below to add new fee information for a student. After saving, the fee record will be available on the dashboard for review.', 'school-management-system' ); ?></p>

		<form method="post" action="">
			<?php wp_nonce_field( 'sms_nonce_form', 'sms_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><label for="student_id"><?php esc_html_e( 'Student Name / ID', 'school-management-system' ); ?></label></th>
					<td>
						<select name="student_id" id="student_id" required>
							<option value=""><?php esc_html_e( 'Select Student', 'school-management-system' ); ?></option>
							<?php
							$selected_student_id = $fee ? $fee->student_id : $student_id;
							
							// Pre-fetch enrollments to map students to classes efficiently.
							$student_class_map = array();
							$all_enrollments = Enrollment::get_all( array( 'status' => 'enrolled' ), 2000 );
							if ( ! empty( $all_enrollments ) ) {
								foreach ( $all_enrollments as $enr ) {
									if ( ! isset( $student_class_map[ $enr->student_id ] ) ) {
										$student_class_map[ $enr->student_id ] = $enr->class_id;
									}
								}
							}

							$students = Student::get_all( array(), 1000 );
							foreach ( $students as $student ) {
								$class_attr = isset( $student_class_map[ $student->id ] ) ? 'data-class-id="' . intval( $student_class_map[ $student->id ] ) . '"' : '';
								?>
								<option value="<?php echo intval( $student->id ); ?>" <?php echo $class_attr; ?> <?php selected( $selected_student_id, $student->id ); ?>>
									<?php echo esc_html( $student->first_name . ' ' . $student->last_name . ' (' . $student->roll_number . ')' ); ?>
								</option>
								<?php
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="class_id"><?php esc_html_e( 'Class / Grade', 'school-management-system' ); ?></label></th>
					<td>
						<select name="class_id" id="class_id" required>
							<option value=""><?php esc_html_e( 'Select Class', 'school-management-system' ); ?></option>
							<?php
							$classes = Classm::get_all( array(), 100 );
							foreach ( $classes as $class ) {
								?>
								<option value="<?php echo intval( $class->id ); ?>" <?php echo $fee && $fee->class_id === $class->id ? 'selected' : ''; ?>>
									<?php echo esc_html( $class->class_name ); ?>
								</option>
								<?php
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="fee_type"><?php esc_html_e( 'Fee Type', 'school-management-system' ); ?></label></th>
					<td><input type="text" name="fee_type" id="fee_type" required value="<?php echo $fee ? esc_attr( $fee->fee_type ) : ''; ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="amount"><?php esc_html_e( 'Amount', 'school-management-system' ); ?></label></th>
					<td><input type="number" name="amount" id="amount" step="0.01" required value="<?php echo $fee ? esc_attr( $fee->amount ) : ''; ?>" /></td>
				</tr>
				<tr>
					<th scope="row" class="partial-payment-field" style="display:none;"><label for="paid_amount"><?php esc_html_e( 'Paid Amount', 'school-management-system' ); ?></label></th>
					<td class="partial-payment-field" style="display:none;"><input type="number" name="paid_amount" id="paid_amount" step="0.01" value="<?php echo $fee ? esc_attr( $fee->paid_amount ) : ''; ?>" /></td>
				</tr>
				<tr>
					<th scope="row" class="partial-payment-field" style="display:none;"><label for="due_amount"><?php esc_html_e( 'Due Amount', 'school-management-system' ); ?></label></th>
					<td class="partial-payment-field" style="display:none;"><input type="number" name="due_amount" id="due_amount" step="0.01" readonly style="background-color: #eee;" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="fee_month"><?php esc_html_e( 'Fee Month', 'school-management-system' ); ?></label></th>
					<td>
						<?php
						$current_month = $fee && $fee->due_date ? date( 'n', strtotime( $fee->due_date ) ) : date( 'n' );
						$current_year  = $fee && $fee->due_date ? date( 'Y', strtotime( $fee->due_date ) ) : date( 'Y' );
						?>
						<select name="fee_month" id="fee_month" required style="margin-right: 10px;">
							<?php
							for ( $m = 1; $m <= 12; $m++ ) {
								$month_name = date_i18n( 'F', mktime( 0, 0, 0, $m, 1 ) );
								echo '<option value="' . esc_attr( $m ) . '" ' . selected( $current_month, $m, false ) . '>' . esc_html( $month_name ) . '</option>';
							}
							?>
						</select>
						<select name="fee_year" id="fee_year" required>
							<?php
							$start_year = date( 'Y' ) - 1;
							$end_year   = date( 'Y' ) + 2;
							for ( $y = $start_year; $y <= $end_year; $y++ ) {
								echo '<option value="' . esc_attr( $y ) . '" ' . selected( $current_year, $y, false ) . '>' . esc_html( $y ) . '</option>';
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="status"><?php esc_html_e( 'Payment Status', 'school-management-system' ); ?></label></th>
					<td>
						<select name="status" id="status">
							<option value="paid" <?php echo $fee && 'paid' === $fee->status ? 'selected' : ''; ?>><?php esc_html_e( 'Paid', 'school-management-system' ); ?></option>
							<option value="pending" <?php echo ! $fee || 'pending' === $fee->status ? 'selected' : ''; ?>><?php esc_html_e( 'Unpaid', 'school-management-system' ); ?></option>
							<option value="partially_paid" <?php echo $fee && 'partially_paid' === $fee->status ? 'selected' : ''; ?>><?php esc_html_e( 'Partially Paid', 'school-management-system' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="payment_date"><?php esc_html_e( 'Payment Date', 'school-management-system' ); ?></label></th>
					<td><input type="date" name="payment_date" id="payment_date" value="<?php echo ( $fee && ! empty( $fee->payment_date ) && strtotime( $fee->payment_date ) > 0 ) ? esc_attr( date( 'Y-m-d', strtotime( $fee->payment_date ) ) ) : ''; ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="remarks"><?php esc_html_e( 'Notes (optional)', 'school-management-system' ); ?></label></th>
					<td><textarea name="remarks" id="remarks"><?php echo $fee ? esc_textarea( $fee->remarks ) : ''; ?></textarea></td>
				</tr>
			</table>
			<?php if ( $is_edit ) : ?>
				<input type="hidden" name="fee_id" value="<?php echo intval( $fee->id ); ?>" />
				<button type="submit" name="sms_edit_fee" class="button button-primary"><?php esc_html_e( 'Update Fee', 'school-management-system' ); ?></button>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-fees' ) ); ?>" class="button"><?php esc_html_e( 'Cancel', 'school-management-system' ); ?></a>
			<?php else : ?>
				<button type="submit" name="sms_add_fee" class="button button-primary"><?php esc_html_e( 'Add Fee', 'school-management-system' ); ?></button>
			<?php endif; ?>
		</form>
	</div>
	<?php endif; ?>

	<?php if ( $show_dashboard ) : ?>

	<!-- Statistics Section -->
	<style>
		.sms-dashboard-wrapper { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; margin-bottom: 30px; margin-top: 20px; }
		.sms-stat-card { background: #fff; border-radius: 12px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); position: relative; overflow: hidden; transition: transform 0.3s ease, box-shadow 0.3s ease; color: #fff; }
		.sms-stat-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
		.sms-stat-card.collected { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
		.sms-stat-card.pending { background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%); }
		.sms-stat-card h3 { margin: 0 0 10px; font-size: 15px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: rgba(255,255,255,0.9); }
		.sms-stat-card .value { font-size: 36px; font-weight: 700; margin: 0; line-height: 1.2; }
		.sms-stat-card .dashicons { position: absolute; right: 20px; top: 50%; transform: translateY(-50%); font-size: 80px; width: 80px; height: 80px; opacity: 0.15; }
		
		.sms-widgets-row { display: flex; gap: 25px; margin-bottom: 30px; flex-wrap: wrap; }
		.sms-widget { flex: 1; min-width: 300px; background: #fff; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.03); overflow: hidden; border: 1px solid #f0f0f0; }
		.sms-widget-header { padding: 15px 20px; background: #fff; border-bottom: 1px solid #f0f0f0; display: flex; align-items: center; justify-content: space-between; }
		.sms-widget-header h3 { margin: 0; font-size: 16px; color: #333; font-weight: 600; }
		.sms-widget-content { padding: 0; }
		.sms-list-item { padding: 15px 20px; border-bottom: 1px solid #f5f5f5; display: flex; align-items: center; justify-content: space-between; transition: background 0.2s; }
		.sms-list-item:last-child { border-bottom: none; }
		.sms-list-item:hover { background-color: #fafafa; }
		.sms-student-info { display: flex; flex-direction: column; }
		.sms-student-name { font-weight: 600; color: #333; font-size: 14px; margin-bottom: 3px; }
		.sms-fee-date { font-size: 12px; color: #888; display: flex; align-items: center; gap: 4px; }
		.sms-amount-badge { background: #f0f0f0; color: #333; padding: 6px 12px; border-radius: 20px; font-weight: 700; font-size: 13px; }
		.sms-status-paid { color: #28a745; background: rgba(40, 167, 69, 0.1); padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; margin-left: 8px; }
		.sms-empty-state { padding: 30px; text-align: center; color: #999; font-style: italic; }
	</style>

	<div class="sms-dashboard-wrapper">
		<div class="sms-stat-card collected">
			<span class="dashicons dashicons-money-alt"></span>
			<h3><?php esc_html_e( 'Total Fees Collected', 'school-management-system' ); ?></h3>
			<p class="value">
				<?php echo esc_html( $currency ) . ' ' . number_format( Fee::get_total_collected(), 2 ); ?>
			</p>
		</div>
		<div class="sms-stat-card pending">
			<span class="dashicons dashicons-warning"></span>
			<h3 style="margin-top: 0;"><?php esc_html_e( 'Pending Fees', 'school-management-system' ); ?></h3>
			<p class="value">
				<?php echo esc_html( $currency ) . ' ' . number_format( Fee::get_total_pending(), 2 ); ?>
			</p>
		</div>
	</div>

	<div class="sms-widgets-row">
		<!-- Fees Collection Reports -->
		<div class="sms-widget">
			<div class="sms-widget-header" style="padding: 0; border-bottom: none;">
				<div class="sms-collection-tabs">
					<div class="sms-collection-tab active" data-target="tab-class"><?php esc_html_e( 'By Class', 'school-management-system' ); ?></div>
					<div class="sms-collection-tab" data-target="tab-month"><?php esc_html_e( 'By Month', 'school-management-system' ); ?></div>
					<div class="sms-collection-tab" data-target="tab-year"><?php esc_html_e( 'By Year', 'school-management-system' ); ?></div>
				</div>
			</div>
			<div class="sms-widget-content">
				<?php
				$collection_data = Fee::get_collection_summary();
				?>
				
				<!-- Class Wise -->
				<div id="tab-class" class="sms-collection-content active">
					<?php if ( ! empty( $collection_data['class_wise'] ) ) : ?>
						<?php foreach ( $collection_data['class_wise'] as $item ) : ?>
							<div class="sms-collection-item">
								<div class="sms-collection-label">
									<span class="dashicons dashicons-groups" style="color: #3498db;"></span>
									<?php echo esc_html( $item->class_name ? $item->class_name : 'Unknown Class' ); ?>
								</div>
								<div class="sms-collection-amount">
									<?php echo esc_html( $currency ) . ' ' . number_format( $item->total, 2 ); ?>
								</div>
							</div>
						<?php endforeach; ?>
					<?php else : ?>
						<div class="sms-empty-state"><?php esc_html_e( 'No data available.', 'school-management-system' ); ?></div>
					<?php endif; ?>
				</div>

				<!-- Month Wise -->
				<div id="tab-month" class="sms-collection-content">
					<?php if ( ! empty( $collection_data['month_wise'] ) ) : ?>
						<?php foreach ( $collection_data['month_wise'] as $item ) : ?>
							<div class="sms-collection-item">
								<div class="sms-collection-label">
									<span class="dashicons dashicons-calendar-alt" style="color: #9b59b6;"></span>
									<?php echo date_i18n( 'F', mktime( 0, 0, 0, $item->month, 10 ) ); ?>
								</div>
								<div class="sms-collection-amount">
									<?php echo esc_html( $currency ) . ' ' . number_format( $item->total, 2 ); ?>
								</div>
							</div>
						<?php endforeach; ?>
					<?php else : ?>
						<div class="sms-empty-state"><?php esc_html_e( 'No data available.', 'school-management-system' ); ?></div>
					<?php endif; ?>
				</div>

				<!-- Year Wise -->
				<div id="tab-year" class="sms-collection-content">
					<?php if ( ! empty( $collection_data['year_wise'] ) ) : ?>
						<?php foreach ( $collection_data['year_wise'] as $item ) : ?>
							<div class="sms-collection-item">
								<div class="sms-collection-label">
									<span class="dashicons dashicons-chart-bar" style="color: #e67e22;"></span>
									<?php echo esc_html( $item->year ); ?>
								</div>
								<div class="sms-collection-amount">
									<?php echo esc_html( $currency ) . ' ' . number_format( $item->total, 2 ); ?>
								</div>
							</div>
						<?php endforeach; ?>
					<?php else : ?>
						<div class="sms-empty-state"><?php esc_html_e( 'No data available.', 'school-management-system' ); ?></div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<!-- Recent Payments -->
		<div class="sms-widget">
			<div class="sms-widget-header">
				<h3><span class="dashicons dashicons-yes-alt" style="margin-right:8px; color:#2ecc71;"></span><?php esc_html_e( 'Recent Payments', 'school-management-system' ); ?></h3>
			</div>
			<div class="sms-widget-content">
				<?php
				$recent_payments = Fee::get_recent_payments( 5 );
				if ( ! empty( $recent_payments ) ) {
					foreach ( $recent_payments as $fee ) {
						$student = Student::get( $fee->student_id );
						?>
						<div class="sms-list-item">
							<div class="sms-student-info">
								<span class="sms-student-name">
									<?php echo esc_html( $student ? $student->first_name . ' ' . $student->last_name : 'Unknown' ); ?>
									<span class="sms-status-paid"><?php esc_html_e( 'PAID', 'school-management-system' ); ?></span>
								</span>
								<span class="sms-fee-date"><span class="dashicons dashicons-calendar" style="font-size:14px; width:14px; height:14px;"></span> <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $fee->payment_date ) ) ); ?></span>
							</div>
							<div class="sms-amount-badge" style="color: #28a745; background: rgba(40, 167, 69, 0.1);">
								+ <?php echo esc_html( $fee->amount ); ?>
							</div>
						</div>
						<?php
					}
				} else {
					echo '<div class="sms-empty-state">' . esc_html__( 'No recent payments.', 'school-management-system' ) . '</div>';
				}
				?>
			</div>
		</div>
	</div>

	<h2><?php esc_html_e( 'Fees List', 'school-management-system' ); ?></h2>

	<!-- Filter Form -->
	<form method="get" action="" style="margin-bottom: 20px;">
		<input type="hidden" name="page" value="sms-fees" />
		
		<select name="class_id">
			<option value=""><?php esc_html_e( 'All Classes', 'school-management-system' ); ?></option>
			<?php
			$classes = Classm::get_all( array(), 100 );
			foreach ( $classes as $class ) {
				?>
				<option value="<?php echo intval( $class->id ); ?>" <?php selected( $class_id_filter, $class->id ); ?>>
					<?php echo esc_html( $class->class_name ); ?>
				</option>
				<?php
			}
			?>
		</select>

		<select name="status">
			<option value=""><?php esc_html_e( 'All Statuses', 'school-management-system' ); ?></option>
			<option value="paid" <?php selected( $status_filter, 'paid' ); ?>><?php esc_html_e( 'Paid', 'school-management-system' ); ?></option>
			<option value="pending" <?php selected( $status_filter, 'pending' ); ?>><?php esc_html_e( 'Unpaid', 'school-management-system' ); ?></option>
			<option value="partially_paid" <?php selected( $status_filter, 'partially_paid' ); ?>><?php esc_html_e( 'Partially Paid', 'school-management-system' ); ?></option>
		</select>

		<input type="date" name="date" value="<?php echo esc_attr( $date_filter ); ?>" placeholder="<?php esc_attr_e( 'Due Date', 'school-management-system' ); ?>" />
		
		<input type="text" name="fee_type" value="<?php echo esc_attr( $fee_type_filter ); ?>" placeholder="<?php esc_attr_e( 'Fee Type', 'school-management-system' ); ?>" />

		<button type="submit" class="button"><?php esc_html_e( 'Filter', 'school-management-system' ); ?></button>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-fees' ) ); ?>" class="button"><?php esc_html_e( 'Reset', 'school-management-system' ); ?></a>
	</form>

	<div class="sms-class-fees-container">
		<?php
		$filters = array();
		if ( $student_id > 0 ) {
			$filters['student_id'] = $student_id;
		}
		if ( $class_id_filter > 0 ) {
			$filters['class_id'] = $class_id_filter;
		}
		if ( ! empty( $status_filter ) ) {
			$filters['status'] = $status_filter;
		}
		if ( ! empty( $date_filter ) ) {
			$filters['due_date'] = $date_filter;
		}
		if ( ! empty( $fee_type_filter ) ) {
			$filters['fee_type'] = $fee_type_filter;
		}
		$fees = Fee::get_all( $filters, 500 );

		$grouped_fees = array();
		if ( ! empty( $fees ) ) {
			foreach ( $fees as $fee ) {
				$grouped_fees[ $fee->class_id ][] = $fee;
			}
		}

		if ( ! empty( $grouped_fees ) ) {
			foreach ( $grouped_fees as $class_id => $class_fees ) {
				$class = Classm::get( $class_id );
				?>
				<div class="sms-class-fee-widget">
					<h3 class="sms-class-fee-header"><?php echo esc_html( $class ? $class->class_name : 'Unassigned' ); ?></h3>
					<table class="wp-list-table widefat fixed striped">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Student Name', 'school-management-system' ); ?></th>
								<th><?php esc_html_e( 'Fee Type', 'school-management-system' ); ?></th>
								<th><?php esc_html_e( 'Amount', 'school-management-system' ); ?></th>
								<th><?php esc_html_e( 'Status', 'school-management-system' ); ?></th>
								<th><?php esc_html_e( 'Actions', 'school-management-system' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ( $class_fees as $fee ) {
								$student = Student::get( $fee->student_id );
								$delete_url = wp_nonce_url( admin_url( 'admin.php?page=sms-fees&action=delete&id=' . $fee->id ), 'sms_delete_fee_nonce', '_wpnonce' );
								$download_url = wp_nonce_url( admin_url( 'admin.php?action=sms_download_fee_voucher&id=' . $fee->id ), 'sms_download_fee_voucher_nonce', '_wpnonce' );
								?>
								<tr>
									<td><?php echo $student ? esc_html( $student->first_name . ' ' . $student->last_name ) : 'N/A'; ?></td>
									<td><?php echo esc_html( $fee->fee_type ); ?></td>
									<td><?php echo esc_html( number_format( $fee->amount, 2 ) ); ?></td>
									<td>
										<span class="sms-status-badge status-<?php echo esc_attr( $fee->status ); ?>">
											<?php echo esc_html( ucfirst( str_replace( '_', ' ', $fee->status ) ) ); ?>
										</span>
									</td>
									<td>
										<button class="button button-small toggle-fee-details-btn" data-target="#fee-details-<?php echo intval( $fee->id ); ?>" title="<?php esc_attr_e( 'View Details', 'school-management-system' ); ?>">
											<span class="dashicons dashicons-visibility"></span>
										</button>
										<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-fees&action=edit&id=' . $fee->id ) ); ?>" class="button button-small" title="<?php esc_attr_e( 'Edit', 'school-management-system' ); ?>">
											<span class="dashicons dashicons-edit"></span>
										</a>
										<a href="<?php echo esc_url( $delete_url ); ?>" class="button button-small" style="color: #a00;" onclick="return confirm('<?php esc_attr_e( 'Are you sure?', 'school-management-system' ); ?>')" title="<?php esc_attr_e( 'Delete', 'school-management-system' ); ?>">
											<span class="dashicons dashicons-trash"></span>
										</a>
									</td>
								</tr>
								<tr id="fee-details-<?php echo intval( $fee->id ); ?>" class="fee-details-row" style="display: none;">
									<td colspan="5">
										<ul class="fee-details-list">
											<li><strong><?php esc_html_e( 'ID', 'school-management-system' ); ?>:</strong> <?php echo intval( $fee->id ); ?></li>
											<li><strong><?php esc_html_e( 'Student Name', 'school-management-system' ); ?>:</strong> <?php echo $student ? esc_html( $student->first_name . ' ' . $student->last_name ) : 'N/A'; ?></li>
											<li><strong><?php esc_html_e( 'Fee Type', 'school-management-system' ); ?>:</strong> <?php echo esc_html( $fee->fee_type ); ?></li>
											<li><strong><?php esc_html_e( 'Amount', 'school-management-system' ); ?>:</strong> <?php echo esc_html( $fee->amount ); ?></li>
											<li><strong><?php esc_html_e( 'Paid Amount', 'school-management-system' ); ?>:</strong> <?php echo esc_html( $fee->paid_amount ); ?></li>
											<li><strong><?php esc_html_e( 'Due Date', 'school-management-system' ); ?>:</strong> <?php echo esc_html( $fee->due_date ); ?></li>
											<li><strong><?php esc_html_e( 'Payment Date', 'school-management-system' ); ?>:</strong> <?php echo esc_html( $fee->payment_date ); ?></li>
											<li><strong><?php esc_html_e( 'Status', 'school-management-system' ); ?>:</strong> <?php echo esc_html( $fee->status ); ?></li>
											<li><strong><?php esc_html_e( 'Notes', 'school-management-system' ); ?>:</strong> <?php echo esc_html( $fee->remarks ); ?></li>
										</ul>
										<div style="margin-top: 15px;">
											<a href="<?php echo esc_url( $download_url ); ?>" class="button button-secondary" target="_blank">
												<span class="dashicons dashicons-pdf" style="vertical-align: middle;"></span> <?php esc_html_e( 'Download Voucher', 'school-management-system' ); ?>
											</a>
										</div>
									</td>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
				</div>
				<?php
			}
		} else {
			?>
			<div class="sms-empty-state" style="background: #fff; padding: 30px; text-align: center; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
				<?php esc_html_e( 'No fees found for the selected filters.', 'school-management-system' ); ?>
			</div>
			<?php
		}
		?>
	</div>
	<?php endif; ?>

	<script>
	jQuery(document).ready(function($) {
		$('.toggle-fee-details-btn').on('click', function(e) {
			e.preventDefault();
			var targetRow = $(this).data('target');
			$(targetRow).toggle();
		});

		// Handle Partial Payment fields
		function togglePartialFields() {
			var status = $('#status').val();
			if (status === 'partially_paid') {
				$('.partial-payment-field').show();
				$('#paid_amount').prop('required', true);
			} else {
				$('.partial-payment-field').hide();
				$('#paid_amount').prop('required', false);
			}
			calculateDue();
		}

		function calculateDue() {
			var amount = parseFloat($('#amount').val()) || 0;
			var paid = parseFloat($('#paid_amount').val()) || 0;
			var due = amount - paid;
			if(due < 0) due = 0;
			$('#due_amount').val(due.toFixed(2));
		}

		$('#status').on('change', togglePartialFields);
		$('#amount, #paid_amount').on('input', calculateDue);
		
		// Initialize on load
		togglePartialFields();

		// Collection Tabs
		$('.sms-collection-tab').on('click', function() {
			var target = $(this).data('target');
			$(this).siblings().removeClass('active');
			$(this).addClass('active');
			$(this).closest('.sms-widget').find('.sms-collection-content').removeClass('active');
			$('#' + target).addClass('active');
		});
	});
	</script>
</div>
