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

// Get currency setting
$settings = get_option( 'sms_settings' );
$currency = $settings['currency'] ?? '৳';

$fee = null;
$is_edit = false;
$action = sanitize_text_field( $_GET['action'] ?? '' );
$fee_id = intval( $_GET['id'] ?? 0 );
$student_id = intval( $_GET['student_id'] ?? 0 );
$class_id_filter = intval( $_GET['class_id'] ?? 0 );
$status_filter = sanitize_text_field( $_GET['status'] ?? '' );
$date_filter = sanitize_text_field( $_GET['date'] ?? '' );
$fee_type_filter = sanitize_text_field( $_GET['fee_type'] ?? '' );
$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'dashboard';
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

/* Report Filters */
.sms-report-filters { background: #fff; padding: 20px; border: 1px solid #ddd; margin-bottom: 20px; border-radius: 4px; }
.sms-report-filters .filter-item { display: inline-block; margin-right: 15px; margin-bottom: 10px; }
.sms-report-filters label { display: block; margin-bottom: 5px; font-weight: 600; }
.sms-report-filters input, .sms-report-filters select { padding: 6px 10px; border: 1px solid #ddd; border-radius: 4px; }
</style>

<div class="wrap">
	<h1><?php esc_html_e( 'Fees Management', 'school-management-system' ); ?></h1>
	<hr class="wp-header-end">

	<?php if ( ! empty( $message ) ) : ?>
		<div class="notice <?php echo esc_attr( $message_class ); ?> is-dismissible"><p><?php echo esc_html( $message ); ?></p></div>
	<?php endif; ?>

	<!-- Navigation Tabs -->
	<nav class="nav-tab-wrapper">
		<a href="?page=sms-fees&tab=dashboard" class="nav-tab <?php echo 'dashboard' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Dashboard', 'school-management-system' ); ?></a>
		<a href="?page=sms-fees&action=add" class="nav-tab <?php echo $show_form ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Add Fee', 'school-management-system' ); ?></a>
		<a href="?page=sms-fees&tab=report" class="nav-tab <?php echo 'report' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Reports', 'school-management-system' ); ?></a>
		<a href="?page=sms-fees&action=export_fees_report&_wpnonce=<?php echo wp_create_nonce( 'sms_export_fees_nonce' ); ?>" class="nav-tab" style="float: right;"><?php esc_html_e( 'Export CSV', 'school-management-system' ); ?></a>
	</nav>

	<?php if ( $show_form ) : ?>
	<div style="background: #fff; padding: 20px; border: 1px solid #ddd; margin-top: 20px; border-radius: 4px;">
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

		<?php if ( 'dashboard' === $active_tab ) : ?>

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
		.sms-status-partially-paid { color: #ef6c00; background: rgba(239, 108, 0, 0.1); padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; margin-left: 8px; }
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
						$status_text  = esc_html( strtoupper( str_replace( '_', ' ', $fee->status ) ) );
						$status_class = 'sms-status-paid';
						if ( 'partially_paid' === $fee->status ) {
							$status_class = 'sms-status-partially-paid';
						}
						?>
						<div class="sms-list-item">
							<div class="sms-student-info">
								<span class="sms-student-name">
									<?php echo esc_html( $student ? $student->first_name . ' ' . $student->last_name : 'Unknown' ); ?>
									<span class="<?php echo esc_attr( $status_class ); ?>" style="text-transform: uppercase;"><?php echo $status_text; ?></span>
								</span>
								<span class="sms-fee-date"><span class="dashicons dashicons-calendar" style="font-size:14px; width:14px; height:14px;"></span> <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $fee->payment_date ) ) ); ?></span>
							</div>
							<div class="sms-amount-badge" style="color: #28a745; background: rgba(40, 167, 69, 0.1);">
								+ <?php echo esc_html( number_format( $fee->paid_amount, 2 ) ); ?>
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

		<?php elseif ( 'report' === $active_tab ) : ?>
			<?php
			// Filters for the report.
			$report_class_id   = intval( $_GET['class_id'] ?? 0 );
			$report_status     = sanitize_text_field( $_GET['status'] ?? '' );
			$report_start_date = sanitize_text_field( $_GET['start_date'] ?? '' );
			$report_end_date   = sanitize_text_field( $_GET['end_date'] ?? '' );

			$report_filters = array(
				'class_id'   => $report_class_id,
				'status'     => $report_status,
				'start_date' => $report_start_date,
				'end_date'   => $report_end_date,
			);
			$fees_report = Fee::get_fees_report( $report_filters );
			
			// Calculate statistics
			$total_records = count( $fees_report );
			$total_amount = 0;
			$total_paid   = 0;
			$total_due    = 0;
			$paid_count   = 0;
			$pending_count = 0;
			$partial_count = 0;
			
			if ( ! empty( $fees_report ) ) {
				foreach ( $fees_report as $fee ) {
					$due = $fee->amount - $fee->paid_amount;
					$total_amount += $fee->amount;
					$total_paid   += $fee->paid_amount;
					$total_due    += $due;
					
					if ( 'paid' === $fee->status ) {
						$paid_count++;
					} elseif ( 'pending' === $fee->status ) {
						$pending_count++;
					} elseif ( 'partially_paid' === $fee->status ) {
						$partial_count++;
					}
				}
			}
			?>
			
			<!-- Unique Reports Section Design -->
			<div class="sms-reports-container">
				<!-- Advanced Filter Section -->
				<div class="sms-filter-card">
					<div class="sms-filter-header">
						<h3><span class="dashicons dashicons-filter" style="margin-right: 8px;"></span><?php esc_html_e( 'Advanced Filters', 'school-management-system' ); ?></h3>
						<div class="sms-filter-actions">
							<button type="button" class="button sms-toggle-filters" data-expanded="true">
								<span class="dashicons dashicons-arrow-up-alt2"></span>
							</button>
						</div>
					</div>
					<div class="sms-filter-content">
						<form method="get" action="" class="sms-filter-form">
							<input type="hidden" name="page" value="sms-fees" />
							<input type="hidden" name="tab" value="report" />
							
							<div class="sms-filter-grid">
								<div class="sms-filter-group">
									<label class="sms-filter-label">
										<span class="dashicons dashicons-calendar-alt"></span>
										<?php esc_html_e( 'Date Range', 'school-management-system' ); ?>
									</label>
									<div class="sms-date-range">
										<input type="date" name="start_date" id="start_date" value="<?php echo esc_attr( $report_start_date ); ?>" placeholder="<?php esc_html_e( 'From', 'school-management-system' ); ?>" />
										<span class="sms-date-separator"><?php esc_html_e( 'to', 'school-management-system' ); ?></span>
										<input type="date" name="end_date" id="end_date" value="<?php echo esc_attr( $report_end_date ); ?>" placeholder="<?php esc_html_e( 'To', 'school-management-system' ); ?>" />
									</div>
								</div>
								
								<div class="sms-filter-group">
									<label class="sms-filter-label">
										<span class="dashicons dashicons-groups"></span>
										<?php esc_html_e( 'Class', 'school-management-system' ); ?>
									</label>
									<select name="class_id" class="sms-filter-select">
										<option value=""><?php esc_html_e( 'All Classes', 'school-management-system' ); ?></option>
										<?php
										$classes = Classm::get_all( array(), 100 );
										foreach ( $classes as $class ) {
											echo '<option value="' . intval( $class->id ) . '" ' . selected( $report_class_id, $class->id, false ) . '>' . esc_html( $class->class_name ) . '</option>';
										}
										?>
									</select>
								</div>
								
								<div class="sms-filter-group">
									<label class="sms-filter-label">
										<span class="dashicons dashicons-yes-alt"></span>
										<?php esc_html_e( 'Payment Status', 'school-management-system' ); ?>
									</label>
									<select name="status" class="sms-filter-select">
										<option value=""><?php esc_html_e( 'All Statuses', 'school-management-system' ); ?></option>
										<option value="paid" <?php selected( $report_status, 'paid' ); ?>><?php esc_html_e( 'Paid', 'school-management-system' ); ?></option>
										<option value="pending" <?php selected( $report_status, 'pending' ); ?>><?php esc_html_e( 'Unpaid', 'school-management-system' ); ?></option>
										<option value="partially_paid" <?php selected( $report_status, 'partially_paid' ); ?>><?php esc_html_e( 'Partially Paid', 'school-management-system' ); ?></option>
									</select>
								</div>
							</div>
							
							<div class="sms-filter-buttons">
								<button type="submit" class="button button-primary sms-generate-btn">
									<span class="dashicons dashicons-chart-bar"></span>
									<?php esc_html_e( 'Generate Report', 'school-management-system' ); ?>
								</button>
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-fees&tab=report' ) ); ?>" class="button">
									<span class="dashicons dashicons-update"></span>
									<?php esc_html_e( 'Reset Filters', 'school-management-system' ); ?>
								</a>
								<a href="?page=sms-fees&action=export_fees_report&_wpnonce=<?php echo wp_create_nonce( 'sms_export_fees_nonce' ); ?>" class="button sms-export-btn">
									<span class="dashicons dashicons-download"></span>
									<?php esc_html_e( 'Export CSV', 'school-management-system' ); ?>
								</a>
							</div>
						</form>
					</div>
				</div>

				<!-- Statistics Cards -->
				<div class="sms-stats-grid">
					<div class="sms-stat-card sms-total-records">
						<div class="sms-stat-icon">
							<span class="dashicons dashicons-list-view"></span>
						</div>
						<div class="sms-stat-content">
							<div class="sms-stat-number"><?php echo esc_html( $total_records ); ?></div>
							<div class="sms-stat-label"><?php esc_html_e( 'Total Records', 'school-management-system' ); ?></div>
						</div>
					</div>
					
					<div class="sms-stat-card sms-total-amount">
						<div class="sms-stat-icon">
							<span class="dashicons dashicons-money-alt"></span>
						</div>
						<div class="sms-stat-content">
							<div class="sms-stat-number"><?php echo esc_html( $currency . ' ' . number_format( $total_amount, 2 ) ); ?></div>
							<div class="sms-stat-label"><?php esc_html_e( 'Total Amount', 'school-management-system' ); ?></div>
						</div>
					</div>
					
					<div class="sms-stat-card sms-paid-amount">
						<div class="sms-stat-icon">
							<span class="dashicons dashicons-yes-alt"></span>
						</div>
						<div class="sms-stat-content">
							<div class="sms-stat-number"><?php echo esc_html( $currency . ' ' . number_format( $total_paid, 2 ) ); ?></div>
							<div class="sms-stat-label"><?php esc_html_e( 'Paid Amount', 'school-management-system' ); ?></div>
						</div>
					</div>
					
					<div class="sms-stat-card sms-due-amount">
						<div class="sms-stat-icon">
							<span class="dashicons dashicons-warning"></span>
						</div>
						<div class="sms-stat-content">
							<div class="sms-stat-number"><?php echo esc_html( $currency . ' ' . number_format( $total_due, 2 ) ); ?></div>
							<div class="sms-stat-label"><?php esc_html_e( 'Due Amount', 'school-management-system' ); ?></div>
						</div>
					</div>
				</div>

				<!-- Status Distribution -->
				<div class="sms-status-distribution">
					<h3><?php esc_html_e( 'Payment Status Distribution', 'school-management-system' ); ?></h3>
					<div class="sms-status-bars">
						<div class="sms-status-item">
							<div class="sms-status-info">
								<span class="sms-status-label"><?php esc_html_e( 'Paid', 'school-management-system' ); ?></span>
								<span class="sms-status-count"><?php echo esc_html( $paid_count ); ?></span>
							</div>
							<div class="sms-status-bar">
								<div class="sms-status-progress sms-paid" style="width: <?php echo $total_records > 0 ? ( $paid_count / $total_records ) * 100 : 0; ?>%;"></div>
							</div>
						</div>
						
						<div class="sms-status-item">
							<div class="sms-status-info">
								<span class="sms-status-label"><?php esc_html_e( 'Pending', 'school-management-system' ); ?></span>
								<span class="sms-status-count"><?php echo esc_html( $pending_count ); ?></span>
							</div>
							<div class="sms-status-bar">
								<div class="sms-status-progress sms-pending" style="width: <?php echo $total_records > 0 ? ( $pending_count / $total_records ) * 100 : 0; ?>%;"></div>
							</div>
						</div>
						
						<div class="sms-status-item">
							<div class="sms-status-info">
								<span class="sms-status-label"><?php esc_html_e( 'Partially Paid', 'school-management-system' ); ?></span>
								<span class="sms-status-count"><?php echo esc_html( $partial_count ); ?></span>
							</div>
							<div class="sms-status-bar">
								<div class="sms-status-progress sms-partial" style="width: <?php echo $total_records > 0 ? ( $partial_count / $total_records ) * 100 : 0; ?>%;"></div>
							</div>
						</div>
					</div>
				</div>

				<!-- Enhanced Data Table -->
				<div class="sms-data-table-container">
					<div class="sms-table-header">
						<h3><span class="dashicons dashicons-table" style="margin-right: 8px;"></span><?php esc_html_e( 'Fee Details', 'school-management-system' ); ?></h3>
						<div class="sms-table-info">
							<span class="sms-record-count"><?php printf( esc_html__( '%d records found', 'school-management-system' ), $total_records ); ?></span>
						</div>
					</div>
					
					<div class="sms-table-wrapper">
						<table class="sms-enhanced-table">
							<thead>
								<tr>
									<th class="sms-col-student">
										<span class="dashicons dashicons-admin-users"></span>
										<?php esc_html_e( 'Student', 'school-management-system' ); ?>
									</th>
									<th class="sms-col-class">
										<span class="dashicons dashicons-groups"></span>
										<?php esc_html_e( 'Class', 'school-management-system' ); ?>
									</th>
									<th class="sms-col-fee-type">
										<span class="dashicons dashicons-tag"></span>
										<?php esc_html_e( 'Fee Type', 'school-management-system' ); ?>
									</th>
									<th class="sms-col-amount">
										<span class="dashicons dashicons-money-alt"></span>
										<?php esc_html_e( 'Amount', 'school-management-system' ); ?>
									</th>
									<th class="sms-col-paid">
										<span class="dashicons dashicons-yes-alt"></span>
										<?php esc_html_e( 'Paid', 'school-management-system' ); ?>
									</th>
									<th class="sms-col-due">
										<span class="dashicons dashicons-warning"></span>
										<?php esc_html_e( 'Due', 'school-management-system' ); ?>
									</th>
									<th class="sms-col-status">
										<span class="dashicons dashicons-flag"></span>
										<?php esc_html_e( 'Status', 'school-management-system' ); ?>
									</th>
									<th class="sms-col-dates">
										<span class="dashicons dashicons-calendar"></span>
										<?php esc_html_e( 'Dates', 'school-management-system' ); ?>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php if ( ! empty( $fees_report ) ) : ?>
									<?php foreach ( $fees_report as $index => $fee ) : ?>
										<?php 
										$due = $fee->amount - $fee->paid_amount;
										$row_class = ( $index % 2 === 0 ) ? 'sms-even-row' : 'sms-odd-row';
										if ( 'pending' === $fee->status ) {
											$row_class .= ' sms-overdue-row';
										}
										?>
										<tr class="<?php echo esc_attr( $row_class ); ?>">
											<td class="sms-student-cell">
												<div class="sms-student-avatar">
													<span class="dashicons dashicons-admin-users"></span>
												</div>
												<div class="sms-student-details">
													<div class="sms-student-name"><?php echo esc_html( $fee->first_name . ' ' . $fee->last_name ); ?></div>
													<div class="sms-student-roll"><?php echo esc_html( $fee->roll_number ); ?></div>
												</div>
											</td>
											<td class="sms-class-cell">
												<span class="sms-class-badge"><?php echo esc_html( $fee->class_name ); ?></span>
											</td>
											<td class="sms-fee-type-cell">
												<div class="sms-fee-type"><?php echo esc_html( $fee->fee_type ); ?></div>
											</td>
											<td class="sms-amount-cell">
												<span class="sms-amount"><?php echo esc_html( number_format( $fee->amount, 2 ) ); ?></span>
											</td>
											<td class="sms-paid-cell">
												<span class="sms-paid-amount sms-positive"><?php echo esc_html( number_format( $fee->paid_amount, 2 ) ); ?></span>
											</td>
											<td class="sms-due-cell">
												<span class="sms-due-amount <?php echo $due > 0 ? 'sms-negative' : 'sms-positive'; ?>">
													<?php echo esc_html( number_format( $due, 2 ) ); ?>
												</span>
											</td>
											<td class="sms-status-cell">
												<span class="sms-status-badge-enhanced status-<?php echo esc_attr( $fee->status ); ?>">
													<?php 
													$status_icons = array(
														'paid' => 'yes-alt',
														'pending' => 'clock',
														'partially_paid' => 'warning'
													);
													$icon = $status_icons[ $fee->status ] ?? 'marker';
													?>
													<span class="dashicons dashicons-<?php echo esc_attr( $icon ); ?>"></span>
													<?php echo esc_html( ucfirst( str_replace( '_', ' ', $fee->status ) ) ); ?>
												</span>
											</td>
											<td class="sms-dates-cell">
												<div class="sms-date-info">
													<div class="sms-due-date">
														<span class="dashicons dashicons-calendar-alt"></span>
														<?php echo esc_html( $fee->due_date ); ?>
													</div>
													<?php if ( ! empty( $fee->payment_date ) ) : ?>
														<div class="sms-payment-date">
															<span class="dashicons dashicons-yes"></span>
															<?php echo esc_html( $fee->payment_date ); ?>
														</div>
													<?php endif; ?>
												</div>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php else : ?>
									<tr class="sms-empty-row">
										<td colspan="8">
											<div class="sms-empty-state">
												<span class="dashicons dashicons-search"></span>
												<p><?php esc_html_e( 'No fee records found for the selected filters.', 'school-management-system' ); ?></p>
												<small><?php esc_html_e( 'Try adjusting your filter criteria to see more results.', 'school-management-system' ); ?></small>
											</div>
										</td>
									</tr>
								<?php endif; ?>
							</tbody>
							<tfoot>
								<tr class="sms-summary-row">
									<th colspan="3" class="sms-summary-label">
										<span class="dashicons dashicons-chart-bar"></span>
										<?php esc_html_e( 'TOTAL SUMMARY', 'school-management-system' ); ?>
									</th>
									<td class="sms-summary-amount">
										<span class="sms-total-amount"><?php echo esc_html( number_format( $total_amount, 2 ) ); ?></span>
									</td>
									<td class="sms-summary-paid">
										<span class="sms-total-paid sms-positive"><?php echo esc_html( number_format( $total_paid, 2 ) ); ?></span>
									</td>
									<td class="sms-summary-due">
										<span class="sms-total-due <?php echo $total_due > 0 ? 'sms-negative' : 'sms-positive'; ?>">
											<?php echo esc_html( number_format( $total_due, 2 ) ); ?>
										</span>
									</td>
									<td colspan="2" class="sms-summary-actions">
										<div class="sms-summary-percentage">
											<?php 
											$collection_rate = $total_amount > 0 ? ( $total_paid / $total_amount ) * 100 : 0;
											printf( esc_html__( '%.1f%% Collected', 'school-management-system' ), $collection_rate );
											?>
										</div>
									</td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<script>
	jQuery(document).ready(function($) {
		// Collection tabs functionality
		$('.sms-collection-tab').on('click', function() {
			var target = $(this).data('target');
			$('.sms-collection-tab').removeClass('active');
			$('.sms-collection-content').removeClass('active');
			$(this).addClass('active');
			$('#' + target).addClass('active');
		});

		// Auto-fill class when student is selected
		$('#student_id').on('change', function() {
			var studentId = $(this).val();
			var classId = $(this).find('option:selected').data('class-id');
			if (classId) {
				$('#class_id').val(classId);
			}
		});

		// Show/hide partial payment fields based on status
		$('#status').on('change', function() {
			var status = $(this).val();
			if (status === 'partially_paid') {
				$('.partial-payment-field').show();
			} else {
				$('.partial-payment-field').hide();
				if (status === 'paid') {
					var amount = parseFloat($('#amount').val()) || 0;
					$('#paid_amount').val(amount);
				}
			}
		});

		// Calculate due amount when paid amount changes
		$('#amount, #paid_amount').on('input', function() {
			var amount = parseFloat($('#amount').val()) || 0;
			var paidAmount = parseFloat($('#paid_amount').val()) || 0;
			var dueAmount = amount - paidAmount;
			$('#due_amount').val(dueAmount.toFixed(2));
		});

		// Initialize on page load
		$('#status').trigger('change');
	});
	</script>
</div>
