<?php
/**
 * Admin class for handling admin menu and pages.
 *
 * @package School_Management_System
 */

namespace School_Management_System;

/**
 * Admin class
 */
class Admin {

	/**
	 * Add admin menu and submenus.
	 */
	public function add_menu() {
		// Check if user has admin capability.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Main menu.
		add_menu_page(
			__( 'School Management', 'school-management-system' ),
			__( 'School Management', 'school-management-system' ),
			'manage_options',
			'sms-dashboard',
			array( $this, 'display_dashboard' ),
			'dashicons-education',
			26
		);

		// Students submenu.
		add_submenu_page(
			'sms-dashboard',
			__( 'Students', 'school-management-system' ),
			__( 'Students', 'school-management-system' ),
			'manage_options',
			'sms-students',
			array( $this, 'display_students' )
		);

		// Teachers submenu.
		add_submenu_page(
			'sms-dashboard',
			__( 'Teachers', 'school-management-system' ),
			__( 'Teachers', 'school-management-system' ),
			'manage_options',
			'sms-teachers',
			array( $this, 'display_teachers' )
		);

		// Classes submenu.
		add_submenu_page(
			'sms-dashboard',
			__( 'Classes', 'school-management-system' ),
			__( 'Classes', 'school-management-system' ),
			'manage_options',
			'sms-classes',
			array( $this, 'display_classes' )
		);

		// Subjects submenu.
		add_submenu_page(
			'sms-dashboard',
			__( 'Subjects', 'school-management-system' ),
			__( 'Subjects', 'school-management-system' ),
			'manage_options',
			'sms-subjects',
			array( $this, 'display_subjects' )
		);

		// Enrollments submenu.
		add_submenu_page(
			'sms-dashboard',
			__( 'Enrollments', 'school-management-system' ),
			__( 'Enrollments', 'school-management-system' ),
			'manage_options',
			'sms-enrollments',
			array( $this, 'display_enrollments' )
		);

		// Attendance submenu.
		add_submenu_page(
			'sms-dashboard',
			__( 'Notice', 'school-management-system' ),
			__( 'Notice', 'school-management-system' ),
			'manage_options',
			'sms-attendance',
			array( $this, 'display_attendance' )
		);

		// Student Attendance submenu.
		add_submenu_page(
			'sms-dashboard',
			__( 'Attendance', 'school-management-system' ),
			__( 'Attendance', 'school-management-system' ),
			'manage_options',
			'sms-student-attendance',
			array( $this, 'display_student_attendance' )
		);

		// Fees submenu (Dashboard).
		$fees_page = add_submenu_page(
			'sms-dashboard',
			__( 'Fees Dashboard', 'school-management-system' ),
			__( 'Fees', 'school-management-system' ),
			'manage_options',
			'sms-fees',
			array( $this, 'display_fees' )
		);
		add_action( 'admin_footer-' . $fees_page, array( $this, 'fees_page_scripts' ) );

		// Exams submenu.
		add_submenu_page(
			'sms-dashboard',
			__( 'Exams', 'school-management-system' ),
			__( 'Exams', 'school-management-system' ),
			'manage_options',
			'sms-exams',
			array( $this, 'display_exams' )
		);

		// Results submenu.
		add_submenu_page(
			'sms-dashboard',
			__( 'Results', 'school-management-system' ),
			__( 'Results', 'school-management-system' ),
			'manage_options',
			'sms-results',
			array( $this, 'display_results' )
		);

		// Settings submenu.
		$settings_page = add_submenu_page(
			'sms-dashboard',
			__( 'Settings', 'school-management-system' ),
			__( 'Settings', 'school-management-system' ),
			'manage_options',
			'sms-settings',
			array( $this, 'display_settings' )
		);
	}

	/**
	 * Display dashboard page.
	 */
	public function display_dashboard() {
		?>
		<div class="wrap">
			<?php
			$settings = get_option( 'sms_settings' );
			$logo_url = ! empty( $settings['school_logo'] ) ? esc_url( $settings['school_logo'] ) : '';
			?>
			<div style="display: flex; align-items: center; gap: 20px; margin-bottom: 20px; background: #fff; padding: 15px 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: 1px solid #e0e0e0;">
				<?php if ( $logo_url ) : ?>
					<img src="<?php echo $logo_url; ?>" style="max-height: 60px; border-radius: 8px;" alt="School Logo">
				<?php endif; ?>
				<h1 style="margin: 0;"><?php esc_html_e( 'School Management System Dashboard', 'school-management-system' ); ?></h1>
			</div>

			<div id="dashboard-widgets-wrap">
				<div id="dashboard-widgets" class="metabox-holder columns-2">
					<div id="postbox-container-1" class="postbox-container">
						<div class="meta-box-sortables">
							<div class="postbox">
								<h2 class="hndle"><span><?php esc_html_e( 'Statistics', 'school-management-system' ); ?></span></h2>
								<div class="inside">
									<div class="sms-dashboard-cards">
										<div class="sms-card students">
											<div class="sms-card-icon"><span class="dashicons dashicons-groups"></span></div>
											<div class="sms-card-content">
												<h3><?php esc_html_e( 'Total Students', 'school-management-system' ); ?></h3>
												<p class="sms-card-value"><?php echo intval( Student::count() ); ?></p>
											</div>
										</div>
										<div class="sms-card teachers">
											<div class="sms-card-icon"><span class="dashicons dashicons-businessman"></span></div>
											<div class="sms-card-content">
												<h3><?php esc_html_e( 'Total Teachers', 'school-management-system' ); ?></h3>
												<p class="sms-card-value"><?php echo intval( Teacher::count() ); ?></p>
											</div>
										</div>
										<div class="sms-card classes">
											<div class="sms-card-icon"><span class="dashicons dashicons-book-alt"></span></div>
											<div class="sms-card-content">
												<h3><?php esc_html_e( 'Total Classes', 'school-management-system' ); ?></h3>
												<p class="sms-card-value"><?php echo intval( Classm::count() ); ?></p>
											</div>
										</div>
										<div class="sms-card exams">
											<div class="sms-card-icon"><span class="dashicons dashicons-edit-page"></span></div>
											<div class="sms-card-content">
												<h3><?php esc_html_e( 'Total Exams', 'school-management-system' ); ?></h3>
												<p class="sms-card-value"><?php echo intval( Exam::count() ); ?></p>
											</div>
										</div>
										<div class="sms-card attendance">
											<div class="sms-card-icon"><span class="dashicons dashicons-yes-alt"></span></div>
											<div class="sms-card-content">
												<h3><?php esc_html_e( 'Present Today', 'school-management-system' ); ?></h3>
												<p class="sms-card-value">
													<?php echo intval( Attendance::count( array( 'attendance_date' => current_time( 'Y-m-d' ), 'status' => 'present' ) ) ); ?>
												</p>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="postbox">
								<h2 class="hndle"><span><?php esc_html_e( 'Upcoming Exams', 'school-management-system' ); ?></span></h2>
								<div class="inside">
									<table class="wp-list-table widefat fixed striped">
										<thead>
											<tr>
												<th><?php esc_html_e( 'Exam Name', 'school-management-system' ); ?></th>
												<th><?php esc_html_e( 'Class', 'school-management-system' ); ?></th>
												<th><?php esc_html_e( 'Exam Date', 'school-management-system' ); ?></th>
												<th><?php esc_html_e( 'Status', 'school-management-system' ); ?></th>
											</tr>
										</thead>
										<tbody>
											<?php
											$exams = Exam::get_upcoming_exams( 5 );
											if ( ! empty( $exams ) ) {
												foreach ( $exams as $exam ) {
													$class = Classm::get( $exam->class_id );
													?>
													<tr>
														<td><?php echo esc_html( $exam->exam_name ); ?></td>
														<td><?php echo $class ? esc_html( $class->class_name ) : ''; ?></td>
														<td><?php echo esc_html( $exam->exam_date ); ?></td>
														<td><?php echo esc_html( $exam->status ); ?></td>
													</tr>
													<?php
												}
											} else {
												?>
												<tr>
													<td colspan="4"><p style="text-align: center; padding: 20px;"><?php esc_html_e( 'No upcoming exams', 'school-management-system' ); ?></p></td>
												</tr>
												<?php
											}
											?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>

					<div id="postbox-container-2" class="postbox-container">
						<div class="meta-box-sortables">
							<?php
							$uploaded_files = get_option( 'sms_attendance_uploaded_files', array() );
							?>
							<div class="postbox">
								<h2 class="hndle"><span><?php esc_html_e( 'Notice Files', 'school-management-system' ); ?></span></h2>
								<div class="inside">
									<?php if ( ! empty( $uploaded_files ) && is_array( $uploaded_files ) ) : ?>
									<ul>
										<?php foreach ( $uploaded_files as $file ) : ?>
											<li>
												<a href="<?php echo esc_url( $file['url'] ); ?>" target="_blank">
													<span class="dashicons dashicons-media-document" style="vertical-align: middle; margin-right: 5px;"></span>
													<?php echo esc_html( $file['notice_name'] ?? basename( $file['file'] ) ); ?>
												</a>
											</li>
										<?php endforeach; ?>
									</ul>
									<?php else : ?>
										<p><?php esc_html_e( 'No notices found.', 'school-management-system' ); ?></p>
									<?php endif; ?>
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=sms-attendance' ) ); ?>" class="button">
										<?php esc_html_e( 'Manage Notices', 'school-management-system' ); ?>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Display students page.
	 */
	public function display_students() {
		include SMS_PLUGIN_DIR . 'admin/templates/students.php';
	}

	/**
	 * Display teachers page.
	 */
	public function display_teachers() {
		include SMS_PLUGIN_DIR . 'admin/templates/teachers.php';
	}

	/**
	 * Display classes page.
	 */
	public function display_classes() {
		include SMS_PLUGIN_DIR . 'admin/templates/classes.php';
	}

	/**
	 * Display subjects page.
	 */
	public function display_subjects() {
		include SMS_PLUGIN_DIR . 'admin/templates/subjects.php';
	}

	/**
	 * Display enrollments page.
	 */
	public function display_enrollments() {
		include SMS_PLUGIN_DIR . 'admin/templates/enrollments.php';
	}

	/**
	 * Display attendance page.
	 */
	public function display_attendance() {
		include SMS_PLUGIN_DIR . 'admin/templates/attendance.php';
	}

	/**
	 * Display student attendance page.
	 */
	public function display_student_attendance() {
		include SMS_PLUGIN_DIR . 'admin/templates/student-attendance.php';
	}

	/**
	 * Display fees page.
	 */
	public function display_fees() {
		include SMS_PLUGIN_DIR . 'admin/templates/fees.php';
	}

	/**
	 * Display exams page.
	 */
	public function display_exams() {
		include SMS_PLUGIN_DIR . 'admin/templates/exams.php';
	}

	/**
	 * Display results page.
	 */
	public function display_results() {
		include SMS_PLUGIN_DIR . 'admin/templates/results.php';
	}

	/**
	 * Display settings page with tabs.
	 */
	public function display_settings() {
		$settings = get_option( 'sms_settings', array() );
		$message  = '';
		if ( isset( $_GET['sms_message'] ) && 'settings_saved' === $_GET['sms_message'] ) {
			$message = __( 'Settings saved successfully.', 'school-management-system' );
		}
		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'general';
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'School Management System Settings', 'school-management-system' ); ?></h1>
			
			<?php if ( ! empty( $message ) ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $message ); ?></p></div>
			<?php endif; ?>

			<h2 class="nav-tab-wrapper">
				<a href="?page=sms-settings&tab=general" class="nav-tab <?php echo 'general' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'General', 'school-management-system' ); ?></a>
				<a href="?page=sms-settings&tab=academics" class="nav-tab <?php echo 'academics' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Academics', 'school-management-system' ); ?></a>
				<a href="?page=sms-settings&tab=fees" class="nav-tab <?php echo 'fees' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Fees', 'school-management-system' ); ?></a>
			</h2>

			<form method="post" action="">
				<?php wp_nonce_field( 'sms_settings_nonce', 'sms_settings_nonce_field' ); ?>
				
				<?php if ( 'general' === $active_tab ) : ?>
					<table class="form-table">
						<tr>
							<th scope="row"><label for="school_name"><?php esc_html_e( 'School Name', 'school-management-system' ); ?></label></th>
							<td><input type="text" name="school_name" id="school_name" class="regular-text" value="<?php echo esc_attr( $settings['school_name'] ?? '' ); ?>" /></td>
						</tr>
						<tr>
							<th scope="row"><label for="school_logo"><?php esc_html_e( 'School Logo', 'school-management-system' ); ?></label></th>
							<td>
								<input type="text" name="school_logo" id="school_logo" class="regular-text" value="<?php echo esc_attr( $settings['school_logo'] ?? '' ); ?>">
								<button type="button" class="button" id="upload_logo_button"><?php esc_html_e( 'Upload Logo', 'school-management-system' ); ?></button>
								<p class="description"><?php esc_html_e( 'Upload or choose a logo from the media library.', 'school-management-system' ); ?></p>
								<div id="logo-preview" style="margin-top:10px;">
									<?php if ( ! empty( $settings['school_logo'] ) ) : ?>
										<img src="<?php echo esc_url( $settings['school_logo'] ); ?>" style="max-height: 100px; border: 1px solid #ddd; padding: 5px;" />
									<?php endif; ?>
								</div>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="school_address"><?php esc_html_e( 'School Address', 'school-management-system' ); ?></label></th>
							<td><textarea name="school_address" id="school_address" class="large-text" rows="3"><?php echo esc_textarea( $settings['school_address'] ?? '' ); ?></textarea></td>
						</tr>
						<tr>
							<th scope="row"><label for="school_email"><?php esc_html_e( 'School Email', 'school-management-system' ); ?></label></th>
							<td><input type="email" name="school_email" id="school_email" class="regular-text" value="<?php echo esc_attr( $settings['school_email'] ?? '' ); ?>" /></td>
						</tr>
						<tr>
							<th scope="row"><label for="school_phone"><?php esc_html_e( 'School Phone', 'school-management-system' ); ?></label></th>
							<td><input type="text" name="school_phone" id="school_phone" class="regular-text" value="<?php echo esc_attr( $settings['school_phone'] ?? '' ); ?>" /></td>
						</tr>
					</table>
				<?php elseif ( 'academics' === $active_tab ) : ?>
					<table class="form-table">
						<tr>
							<th scope="row"><label for="academic_year"><?php esc_html_e( 'Current Academic Year', 'school-management-system' ); ?></label></th>
							<td>
								<input type="text" name="academic_year" id="academic_year" class="regular-text" value="<?php echo esc_attr( $settings['academic_year'] ?? date( 'Y' ) ); ?>" />
								<p class="description"><?php esc_html_e( 'e.g., 2024-2025', 'school-management-system' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="passing_marks"><?php esc_html_e( 'Passing Marks (%)', 'school-management-system' ); ?></label></th>
							<td>
								<input type="number" name="passing_marks" id="passing_marks" class="small-text" value="<?php echo esc_attr( $settings['passing_marks'] ?? '40' ); ?>" />
								<p class="description"><?php esc_html_e( 'Default passing marks for exams.', 'school-management-system' ); ?></p>
							</td>
						</tr>
					</table>
				<?php elseif ( 'fees' === $active_tab ) : ?>
					<table class="form-table">
						<tr>
							<th scope="row"><label for="currency"><?php esc_html_e( 'Currency Symbol', 'school-management-system' ); ?></label></th>
							<td>
								<input type="text" name="currency" id="currency" class="regular-text" value="<?php echo esc_attr( $settings['currency'] ?? '৳' ); ?>" />
								<p class="description"><?php esc_html_e( 'e.g., $, €, ৳', 'school-management-system' ); ?></p>
							</td>
						</tr>
					</table>
				<?php endif; ?>
				
				<?php submit_button( __( 'Save Settings', 'school-management-system' ), 'primary', 'sms_save_settings' ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Scripts for fees page.
	 */
	public function fees_page_scripts() {
		?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			if ($.fn.select2 && $('#student_id').length) {
				$('#student_id').select2({
					width: '100%',
					placeholder: '<?php esc_html_e( 'Select Student', 'school-management-system' ); ?>',
				});

				// Auto-select class when student changes.
				$('#student_id').on('change', function() {
					var selected = $(this).find(':selected');
					var classId = selected.data('class-id');
					if (classId) {
						$('#class_id').val(classId).trigger('change');
					}
				});
			}
		});
		</script>
		<?php
	}

	/**
	 * Handle form submissions.
	 */
	public function handle_form_submission() {
		// Handle fee voucher download.
		if ( isset( $_GET['action'] ) && 'sms_download_fee_voucher' === $_GET['action'] ) {
			if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'sms_download_fee_voucher_nonce' ) ) {
				wp_die( esc_html__( 'Security check failed', 'school-management-system' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Unauthorized access', 'school-management-system' ) );
			}

			// Clean output buffer to remove any PHP warnings.
			if ( ob_get_length() ) {
				ob_clean();
			}
			// Suppress display of errors for the voucher output.
			@ini_set( 'display_errors', 0 );

			$fee_id = intval( $_GET['id'] ?? 0 );
			$fee = Fee::get( $fee_id );

			if ( ! $fee ) {
				wp_die( esc_html__( 'Fee record not found.', 'school-management-system' ) );
			}

			$student = Student::get( $fee->student_id );
			$class   = Classm::get( $fee->class_id );
			$settings = get_option( 'sms_settings' );
			$currency = $settings['currency'] ?? '৳';
			$school_logo = ! empty( $settings['school_logo'] ) ? $settings['school_logo'] : '';
			$school_name = $settings['school_name'] ?? 'School Management System';

			// Calculate student account summary.
			$total_fees = Fee::get_total_fees( $student->id );
			$total_paid = Fee::get_paid_fees( $student->id );
			$total_due  = $total_fees - $total_paid;

			?>
			<!DOCTYPE html>
			<html>
			<head>
				<title><?php esc_html_e( 'Fee Voucher', 'school-management-system' ); ?> - <?php echo intval( $fee->id ); ?></title>
				<style>
					body { font-family: Arial, sans-serif; background: #f0f0f0; padding: 20px; position: relative; }
					<?php if ( $school_logo ) : ?>
					body::before {
						content: '';
						position: fixed;
						top: 0;
						left: 0;
						width: 100%;
						height: 100%;
						background-image: url('<?php echo esc_url( $school_logo ); ?>');
						background-repeat: no-repeat;
						background-position: center;
						background-size: contain;
						opacity: 0.05;
						z-index: -1;
						-webkit-print-color-adjust: exact;
						color-adjust: exact;
					}
					<?php endif; ?>
					.voucher-container { max-width: 800px; margin: 0 auto; background: #fff; padding: 40px; border: 1px solid #ddd; box-shadow: 0 2px 10px rgba(0,0,0,0.1); position: relative; }
					.header { text-align: center; border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 30px; }
					.header h1 { margin: 0; color: #333; }
					.header p { margin: 5px 0 0; color: #666; }
					.voucher-info { display: flex; justify-content: space-between; margin-bottom: 30px; }
					.info-group h3 { margin: 0 0 10px; font-size: 16px; color: #555; border-bottom: 1px solid #eee; padding-bottom: 5px; }
					.info-group p { margin: 5px 0; font-size: 14px; }
					.fee-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
					.fee-table th, .fee-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
					.fee-table th { background-color: #f9f9f9; font-weight: bold; }
					.total-row td { font-weight: bold; font-size: 16px; }
					.footer { margin-top: 50px; display: flex; justify-content: space-between; text-align: center; }
					.signature-line { border-top: 1px solid #333; width: 200px; padding-top: 5px; }
					.print-btn { display: block; width: 100%; padding: 15px; background: #333; color: #fff; text-align: center; text-decoration: none; margin-bottom: 20px; font-weight: bold; }
					.voucher-top-right { position: absolute; top: 40px; right: 40px; font-size: 14px; font-weight: bold; color: #333; }
					@media print {
						body { background: #fff !important; padding: 0; }
						<?php if ( $school_logo ) : ?>
						body::before { opacity: 0.08; }
						<?php endif; ?>
						.voucher-container { box-shadow: none; border: none; padding: 0; background: transparent; }
						.print-btn { display: none; }
						.voucher-top-right { top: 20px; right: 20px; }
					}
				</style>
			</head>
			<body>
				<div class="voucher-container">
					<a href="#" onclick="window.print(); return false;" class="print-btn"><?php esc_html_e( 'Click here to Print / Save as PDF', 'school-management-system' ); ?></a>
					
					<div class="voucher-top-right">
						<?php esc_html_e( 'Fee Voucher', 'school-management-system' ); ?> - <?php echo intval( $fee->id ); ?>
					</div>

					<div class="header">
						<?php if ( $school_logo ) : ?>
							<img src="<?php echo esc_url( $school_logo ); ?>" alt="<?php esc_attr_e( 'School Logo', 'school-management-system' ); ?>" style="max-height: 80px; margin-bottom: 15px;">
						<?php endif; ?>
						<h1><?php echo esc_html( $school_name ); ?></h1>
						<p><?php esc_html_e( 'Fee Payment Voucher', 'school-management-system' ); ?></p>
					</div>

					<div class="voucher-info">
						<div class="info-group">
							<h3><?php esc_html_e( 'Student Details', 'school-management-system' ); ?></h3>
							<p><strong><?php esc_html_e( 'Name', 'school-management-system' ); ?>:</strong> <?php echo $student ? esc_html( $student->first_name . ' ' . $student->last_name ) : 'N/A'; ?></p>
							<p><strong><?php esc_html_e( 'Roll Number', 'school-management-system' ); ?>:</strong> <?php echo $student ? esc_html( $student->roll_number ) : 'N/A'; ?></p>
							<p><strong><?php esc_html_e( 'Class', 'school-management-system' ); ?>:</strong> <?php echo $class ? esc_html( $class->class_name ) : 'N/A'; ?></p>
						</div>
						<div class="info-group" style="text-align: right;">
							<h3><?php esc_html_e( 'Voucher Details', 'school-management-system' ); ?></h3>
							<p><strong><?php esc_html_e( 'Voucher No', 'school-management-system' ); ?>:</strong> #<?php echo intval( $fee->id ); ?></p>
							<p><strong><?php esc_html_e( 'Fee Month', 'school-management-system' ); ?>:</strong> <?php echo date_i18n( 'F Y', strtotime( $fee->due_date ) ); ?></p>
							<p><strong><?php esc_html_e( 'Date', 'school-management-system' ); ?>:</strong> <?php echo date_i18n( get_option( 'date_format' ), strtotime( current_time( 'Y-m-d' ) ) ); ?></p>
							<p><strong><?php esc_html_e( 'Status', 'school-management-system' ); ?>:</strong> <span style="text-transform: uppercase;"><?php echo esc_html( $fee->status ); ?></span></p>
						</div>
					</div>

					<table class="fee-table">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Description', 'school-management-system' ); ?></th>
								<th><?php esc_html_e( 'Payment Date', 'school-management-system' ); ?></th>
								<th style="text-align: right;"><?php esc_html_e( 'Amount', 'school-management-system' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php echo esc_html( $fee->fee_type ); ?></td>
								<td><?php echo esc_html( $fee->payment_date ); ?></td>
								<td style="text-align: right;"><?php echo esc_html( $currency . ' ' . number_format( $fee->amount, 2 ) ); ?></td>
							</tr>
							<?php if ( 'partially_paid' === $fee->status ) : ?>
							<tr>
								<td colspan="2" style="text-align: right;"><?php esc_html_e( 'Paid Amount', 'school-management-system' ); ?></td>
								<td style="text-align: right;"><?php echo esc_html( $currency . ' ' . number_format( $fee->paid_amount, 2 ) ); ?></td>
							</tr>
							<tr>
								<td colspan="2" style="text-align: right; color: #dc3232;"><?php esc_html_e( 'Due Amount', 'school-management-system' ); ?></td>
								<td style="text-align: right; color: #dc3232;"><?php echo esc_html( $currency . ' ' . number_format( $fee->amount - $fee->paid_amount, 2 ) ); ?></td>
							</tr>
							<?php endif; ?>
							<tr class="total-row">
								<td colspan="2" style="text-align: right;"><?php esc_html_e( 'Total', 'school-management-system' ); ?></td>
								<td style="text-align: right;"><?php echo esc_html( $currency . ' ' . number_format( $fee->amount, 2 ) ); ?></td>
							</tr>
						</tbody>
					</table>

					<div style="margin-top: 20px; border-top: 2px solid #eee; padding-top: 15px;">
						<h3 style="margin: 0 0 15px; font-size: 16px; color: #333; border-bottom: 1px solid #eee; padding-bottom: 5px;"><?php esc_html_e( 'Account Summary', 'school-management-system' ); ?></h3>
						<table style="width: 100%; border-collapse: collapse;">
							<tr>
								<td style="padding: 8px; color: #666;"><?php esc_html_e( 'Total Fees', 'school-management-system' ); ?>:</td>
								<td style="padding: 8px; text-align: right; font-weight: bold;"><?php echo esc_html( $currency . ' ' . number_format( $total_fees, 2 ) ); ?></td>
							</tr>
							<tr>
								<td style="padding: 8px; color: #666;"><?php esc_html_e( 'Total Paid', 'school-management-system' ); ?>:</td>
								<td style="padding: 8px; text-align: right; font-weight: bold; color: #46b450;"><?php echo esc_html( $currency . ' ' . number_format( $total_paid, 2 ) ); ?></td>
							</tr>
							<tr style="background-color: #fff5f5;">
								<td style="padding: 8px; color: #dc3232; font-weight: bold; border-top: 1px solid #eee;"><?php esc_html_e( 'Total Due', 'school-management-system' ); ?>:</td>
								<td style="padding: 8px; text-align: right; font-weight: bold; color: #dc3232; border-top: 1px solid #eee;"><?php echo esc_html( $currency . ' ' . number_format( $total_due, 2 ) ); ?></td>
							</tr>
						</table>
					</div>

					<?php if ( ! empty( $fee->remarks ) ) : ?>
					<div style="margin-top: 20px; padding: 10px; background-color: #f9f9f9; border-left: 3px solid #ccc;">
						<strong><?php esc_html_e( 'Notes', 'school-management-system' ); ?>:</strong>
						<?php echo nl2br( esc_html( $fee->remarks ) ); ?>
					</div>
					<?php endif; ?>

					<div class="footer">
						<div class="signature-line">
							<?php esc_html_e( 'Depositor Signature', 'school-management-system' ); ?>
						</div>
						<div class="signature-line">
							<?php esc_html_e( 'Authorized Signature', 'school-management-system' ); ?>
						</div>
					</div>
				</div>
				<script>
					window.onload = function() { window.print(); }
				</script>
			</body>
			</html>
			<?php
			exit;
		}

		// Handle Fees Report Export.
		if ( isset( $_GET['action'] ) && 'export_fees_report' === $_GET['action'] ) {
			if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'sms_export_fees_nonce' ) ) {
				wp_die( esc_html__( 'Security check failed', 'school-management-system' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Unauthorized access', 'school-management-system' ) );
			}

			$filters = array(
				'class_id'   => ! empty( $_GET['class_id'] ) ? intval( $_GET['class_id'] ) : '',
				'status'     => ! empty( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : '',
				'start_date' => ! empty( $_GET['start_date'] ) ? sanitize_text_field( $_GET['start_date'] ) : '',
				'end_date'   => ! empty( $_GET['end_date'] ) ? sanitize_text_field( $_GET['end_date'] ) : '',
			);

			$fees_report = Fee::get_fees_report( $filters );

			header( 'Content-Type: text/csv; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename=fees-report-' . date( 'Y-m-d' ) . '.csv' );

			$output = fopen( 'php://output', 'w' );
			fputcsv( $output, array( 'Student Name', 'Roll Number', 'Class', 'Fee Type', 'Amount', 'Paid Amount', 'Due Amount', 'Status', 'Due Date', 'Payment Date' ) );

			if ( ! empty( $fees_report ) ) {
				foreach ( $fees_report as $fee ) {
					fputcsv( $output, array(
						$fee->first_name . ' ' . $fee->last_name,
						$fee->roll_number,
						$fee->class_name,
						$fee->fee_type,
						$fee->amount,
						$fee->paid_amount,
						$fee->amount - $fee->paid_amount,
						ucfirst( str_replace( '_', ' ', $fee->status ) ),
						$fee->due_date,
						$fee->payment_date,
					) );
				}
			}
			fclose( $output );
			exit;
		}

		// Handle Student Export.
		if ( isset( $_GET['action'] ) && 'export_students' === $_GET['action'] ) {
			if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'sms_export_students_nonce' ) ) {
				wp_die( esc_html__( 'Security check failed', 'school-management-system' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Unauthorized access', 'school-management-system' ) );
			}

			header( 'Content-Type: text/csv; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename=students-list-' . date( 'Y-m-d' ) . '.csv' );

			$output = fopen( 'php://output', 'w' );
			fputcsv( $output, array( 'First Name', 'Last Name', 'Class', 'Parent Phone', 'Email', 'Roll Number', 'DOB', 'Gender', 'Parent Name', 'Address', 'Status' ) );

			$students = Student::get_all( array(), 10000 );
			foreach ( $students as $student ) {
				$class_name = '';
				$enrollments = Enrollment::get_student_enrollments( $student->id );
				if ( ! empty( $enrollments ) ) {
					$class_obj = Classm::get( $enrollments[0]->class_id );
					if ( $class_obj ) {
						$class_name = $class_obj->class_name;
					}
				}
				fputcsv( $output, array(
					$student->first_name,
					$student->last_name,
					$class_name,
					$student->parent_phone,
					$student->email,
					$student->roll_number,
					$student->dob,
					$student->gender,
					$student->parent_name,
					$student->address,
					$student->status,
				) );
			}
			fclose( $output );
			exit;
		}

		// Handle Student Import.
		if ( isset( $_POST['sms_import_students'] ) ) {
			if ( ! isset( $_POST['sms_import_nonce'] ) || ! wp_verify_nonce( $_POST['sms_import_nonce'], 'sms_import_students_nonce' ) ) {
				wp_die( esc_html__( 'Security check failed', 'school-management-system' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Unauthorized access', 'school-management-system' ) );
			}

			if ( ! empty( $_FILES['import_file']['tmp_name'] ) ) {
				$file = fopen( $_FILES['import_file']['tmp_name'], 'r' );
				// Enable detection of line endings for Mac CSVs.
				ini_set( 'auto_detect_line_endings', true );

				fgetcsv( $file ); // Skip header row.
				$imported = 0;
				$failed = 0;
				$last_error = '';

				// Pre-fetch classes for mapping.
				$classes = Classm::get_all( array(), 1000 );
				$class_map = array();
				if ( ! empty( $classes ) ) {
					foreach ( $classes as $class ) {
						$class_map[ strtolower( trim( $class->class_name ) ) ] = $class->id;
					}
				}

				while ( ( $row = fgetcsv( $file ) ) !== false ) {
					// Pad row to ensure we have enough columns.
					$row = array_pad( $row, 11, '' );

					// Skip empty rows.
					if ( empty( array_filter( $row ) ) ) {
						continue;
					}
					$student_data = array(
						'first_name'   => sanitize_text_field( $row[0] ),
						'last_name'    => sanitize_text_field( $row[1] ),
						// Class is at index 2
						'parent_phone' => sanitize_text_field( $row[3] ),
						'email'        => sanitize_email( $row[4] ),
						'roll_number'  => sanitize_text_field( $row[5] ),
						'dob'          => sanitize_text_field( $row[6] ),
						'gender'       => sanitize_text_field( $row[7] ),
						'parent_name'  => sanitize_text_field( $row[8] ),
						'address'      => sanitize_textarea_field( $row[9] ),
						'status'       => ! empty( $row[10] ) ? sanitize_text_field( $row[10] ) : 'active',
					);

					// Default first name if empty.
					if ( empty( $student_data['first_name'] ) ) {
						$student_data['first_name'] = 'Student';
					}

					// Default last name if empty.
					if ( empty( $student_data['last_name'] ) ) {
						$student_data['last_name'] = '.';
					}

					// Auto-generate Roll Number if empty.
					if ( empty( $student_data['roll_number'] ) ) {
						$student_data['roll_number'] = 'STU-' . date( 'Y' ) . '-' . str_pad( Student::count() + $imported + $failed + 1, 4, '0', STR_PAD_LEFT ) . '-' . rand( 100, 999 );
					}

					// Fill other required fields with defaults if missing to prevent failure.
					if ( empty( $student_data['dob'] ) ) {
						$student_data['dob'] = date( 'Y-m-d' );
					}
					if ( empty( $student_data['gender'] ) ) {
						$student_data['gender'] = 'Not Specified';
					}
					if ( empty( $student_data['parent_name'] ) ) {
						$student_data['parent_name'] = 'N/A';
					}
					if ( empty( $student_data['parent_phone'] ) ) {
						$student_data['parent_phone'] = 'N/A';
					}
					if ( empty( $student_data['address'] ) ) {
						$student_data['address'] = 'N/A';
					}
					if ( empty( $student_data['email'] ) ) {
						// Generate a unique dummy email if missing.
						$student_data['email'] = strtolower( preg_replace( '/[^a-z0-9]/i', '', $student_data['roll_number'] ) ) . '@school.local';
					}

					$class_name = sanitize_text_field( $row[2] );

					// Enforce class enrollment. If class name is missing, fail the row.
					if ( empty( $class_name ) ) {
						$failed++;
						$last_error = __( 'Class Name is missing for one or more rows.', 'school-management-system' );
						continue;
					}

					$result = Student::add( $student_data );
					if ( ! is_wp_error( $result ) ) {
						if ( ! empty( $class_name ) ) {
							$class_key = strtolower( trim( $class_name ) );

							// Auto-create class if it doesn't exist.
							if ( ! isset( $class_map[ $class_key ] ) ) {
								$class_code = strtoupper( substr( preg_replace( '/[^a-zA-Z0-9]/', '', $class_name ), 0, 8 ) );
								if ( empty( $class_code ) ) {
									$class_code = 'CLS-' . rand( 1000, 9999 );
								}
								$class_code .= '-' . rand( 100, 999 ); // Ensure uniqueness.

								$new_class_id = Classm::add( array(
									'class_name' => $class_name,
									'class_code' => $class_code,
									'capacity'   => 50,
									'status'     => 'active',
								) );

								if ( $new_class_id && ! is_wp_error( $new_class_id ) ) {
									$class_map[ $class_key ] = $new_class_id;
								}
							}

							if ( isset( $class_map[ $class_key ] ) ) {
								Enrollment::add( array( 'student_id' => $result, 'class_id' => $class_map[ $class_key ] ) );
							}
						}
						$imported++;
					} else {
						$failed++;
						$last_error = $result->get_error_message();
					}
				}
				fclose( $file );
				wp_redirect( admin_url( 'admin.php?page=sms-students&sms_message=import_completed&count=' . $imported . '&failed=' . $failed . '&error=' . urlencode( $last_error ) ) );
				exit;
			}
		}

		// Handle student deletion.
		if ( isset( $_GET['action'] ) && 'delete' === $_GET['action'] && isset( $_GET['page'] ) && 'sms-students' === $_GET['page'] ) {
			if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'sms_delete_student_nonce' ) ) {
				wp_die( esc_html__( 'Security check failed', 'school-management-system' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Unauthorized access', 'school-management-system' ) );
			}

			$student_id = intval( $_GET['id'] ?? 0 );
			if ( $student_id > 0 ) {
				Student::delete( $student_id );
			}

			wp_redirect( admin_url( 'admin.php?page=sms-students&sms_message=student_deleted' ) );
			exit;
		}

		// Handle bulk student deletion.
		if ( isset( $_POST['action'] ) && 'bulk_delete' === $_POST['action'] && isset( $_POST['student_ids'] ) ) {
			if ( ! isset( $_POST['sms_bulk_delete_nonce'] ) || ! wp_verify_nonce( $_POST['sms_bulk_delete_nonce'], 'sms_bulk_delete_students_nonce' ) ) {
				wp_die( esc_html__( 'Security check failed', 'school-management-system' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Unauthorized access', 'school-management-system' ) );
			}

			$student_ids = array_map( 'intval', $_POST['student_ids'] );
			$deleted_count = 0;
			foreach ( $student_ids as $student_id ) {
				if ( $student_id > 0 ) {
					Student::delete( $student_id );
					$deleted_count++;
				}
			}
			wp_redirect( admin_url( 'admin.php?page=sms-students&sms_message=students_bulk_deleted&count=' . $deleted_count ) );
			exit;
		}

		// Handle bulk class deletion.
		if ( isset( $_POST['action'] ) && 'bulk_delete_classes' === $_POST['action'] && isset( $_POST['class_ids'] ) ) {
			if ( ! isset( $_POST['sms_bulk_delete_classes_nonce'] ) || ! wp_verify_nonce( $_POST['sms_bulk_delete_classes_nonce'], 'sms_bulk_delete_classes_nonce' ) ) {
				wp_die( esc_html__( 'Security check failed', 'school-management-system' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Unauthorized access', 'school-management-system' ) );
			}

			$class_ids = array_map( 'intval', $_POST['class_ids'] );
			$deleted_count = 0;
			foreach ( $class_ids as $class_id ) {
				if ( $class_id > 0 ) {
					Classm::delete( $class_id );
					$deleted_count++;
				}
			}
			wp_redirect( admin_url( 'admin.php?page=sms-classes&sms_message=classes_bulk_deleted&count=' . $deleted_count ) );
			exit;
		}

		// Handle bulk teacher deletion.
		if ( isset( $_POST['action'] ) && 'bulk_delete_teachers' === $_POST['action'] && isset( $_POST['teacher_ids'] ) ) {
			if ( ! isset( $_POST['sms_bulk_delete_teachers_nonce'] ) || ! wp_verify_nonce( $_POST['sms_bulk_delete_teachers_nonce'], 'sms_bulk_delete_teachers_nonce' ) ) {
				wp_die( esc_html__( 'Security check failed', 'school-management-system' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Unauthorized access', 'school-management-system' ) );
			}

			$teacher_ids = array_map( 'intval', $_POST['teacher_ids'] );
			$deleted_count = 0;
			foreach ( $teacher_ids as $teacher_id ) {
				if ( $teacher_id > 0 ) {
					Teacher::delete( $teacher_id );
					$deleted_count++;
				}
			}
			wp_redirect( admin_url( 'admin.php?page=sms-teachers&sms_message=teachers_bulk_deleted&count=' . $deleted_count ) );
			exit;
		}

		// Handle bulk subject deletion.
		if ( isset( $_POST['action'] ) && 'bulk_delete_subjects' === $_POST['action'] && isset( $_POST['subject_ids'] ) ) {
			if ( ! isset( $_POST['sms_bulk_delete_subjects_nonce'] ) || ! wp_verify_nonce( $_POST['sms_bulk_delete_subjects_nonce'], 'sms_bulk_delete_subjects_nonce' ) ) {
				wp_die( esc_html__( 'Security check failed', 'school-management-system' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Unauthorized access', 'school-management-system' ) );
			}

			$subject_ids = array_map( 'intval', $_POST['subject_ids'] );
			$deleted_count = 0;
			foreach ( $subject_ids as $subject_id ) {
				if ( $subject_id > 0 ) {
					Subject::delete( $subject_id );
					$deleted_count++;
				}
			}
			wp_redirect( admin_url( 'admin.php?page=sms-subjects&sms_message=subjects_bulk_deleted&count=' . $deleted_count ) );
			exit;
		}

		// Handle bulk enrollment deletion.
		if ( isset( $_POST['action'] ) && 'bulk_delete_enrollments' === $_POST['action'] && isset( $_POST['enrollment_ids'] ) ) {
			if ( ! isset( $_POST['sms_bulk_delete_enrollments_nonce'] ) || ! wp_verify_nonce( $_POST['sms_bulk_delete_enrollments_nonce'], 'sms_bulk_delete_enrollments_nonce' ) ) {
				wp_die( esc_html__( 'Security check failed', 'school-management-system' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Unauthorized access', 'school-management-system' ) );
			}

			$enrollment_ids = array_map( 'intval', $_POST['enrollment_ids'] );
			$deleted_count = 0;
			foreach ( $enrollment_ids as $enrollment_id ) {
				if ( $enrollment_id > 0 ) {
					Enrollment::delete( $enrollment_id );
					$deleted_count++;
				}
			}
			wp_redirect( admin_url( 'admin.php?page=sms-enrollments&sms_message=enrollments_bulk_deleted&count=' . $deleted_count ) );
			exit;
		}

		// Handle bulk enrollment deletion.
		if ( isset( $_POST['action'] ) && 'bulk_delete_enrollments' === $_POST['action'] && isset( $_POST['enrollment_ids'] ) ) {
			if ( ! isset( $_POST['sms_bulk_delete_enrollments_nonce'] ) || ! wp_verify_nonce( $_POST['sms_bulk_delete_enrollments_nonce'], 'sms_bulk_delete_enrollments_nonce' ) ) {
				wp_die( esc_html__( 'Security check failed', 'school-management-system' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Unauthorized access', 'school-management-system' ) );
			}

			$enrollment_ids = array_map( 'intval', $_POST['enrollment_ids'] );
			$deleted_count = 0;
			foreach ( $enrollment_ids as $enrollment_id ) {
				if ( $enrollment_id > 0 ) {
					Enrollment::delete( $enrollment_id );
					$deleted_count++;
				}
			}
			wp_redirect( admin_url( 'admin.php?page=sms-enrollments&sms_message=enrollments_bulk_deleted&count=' . $deleted_count ) );
			exit;
		}

		// Handle enrollment deletion.
		if ( isset( $_GET['action'] ) && 'delete' === $_GET['action'] && isset( $_GET['page'] ) && 'sms-enrollments' === $_GET['page'] ) {
			if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'sms_delete_enrollment_nonce' ) ) {
				wp_die( esc_html__( 'Security check failed', 'school-management-system' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Unauthorized access', 'school-management-system' ) );
			}

			$enrollment_id = intval( $_GET['id'] ?? 0 );
			if ( $enrollment_id > 0 ) {
				$enrollment = Enrollment::get( $enrollment_id );
				if ( $enrollment ) {
					$student_id = $enrollment->student_id;
					// Check if this is the last enrollment for the student.
					$enrollment_count = Enrollment::count( array( 'student_id' => $student_id ) );
					if ( $enrollment_count <= 1 ) {
						wp_die( esc_html__( 'Cannot delete the last enrollment for a student. To remove the student, please delete the student record from the Students page.', 'school-management-system' ), __( 'Deletion Error', 'school-management-system' ) );
					}
				}				Enrollment::delete( $enrollment_id );
			}

			wp_redirect( admin_url( 'admin.php?page=sms-enrollments&sms_message=enrollment_deleted' ) );
			exit;
		}

		// Handle fee deletion.
		if ( isset( $_GET['action'] ) && 'delete' === $_GET['action'] && isset( $_GET['page'] ) && 'sms-fees' === $_GET['page'] ) {
			if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'sms_delete_fee_nonce' ) ) {
				wp_die( esc_html__( 'Security check failed', 'school-management-system' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Unauthorized access', 'school-management-system' ) );
			}

			// Clean output buffer to remove any PHP warnings generated during bootstrap.
			if ( ob_get_length() ) {
				ob_clean();
			}
			// Suppress display of errors for the voucher output.
			@ini_set( 'display_errors', 0 );

			$fee_id = intval( $_GET['id'] ?? 0 );
			if ( $fee_id > 0 ) {
				Fee::delete( $fee_id );
			}

			wp_redirect( admin_url( 'admin.php?page=sms-fees&sms_message=fee_deleted' ) );
			exit;
		}

		// Handle notice file upload.
		if ( isset( $_POST['sms_upload_attendance_file'] ) ) {
			if ( ! isset( $_POST['sms_attendance_upload_nonce_field'] ) || ! wp_verify_nonce( $_POST['sms_attendance_upload_nonce_field'], 'sms_attendance_upload_nonce' ) ) {
				wp_die( esc_html__( 'Security check failed', 'school-management-system' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Unauthorized access', 'school-management-system' ) );
			}

			if ( ! empty( $_FILES['attendance_file']['name'] ) ) {
				// These files need to be passed as reference.
				$uploaded_file = $_FILES['attendance_file'];
				$upload_overrides = array( 'test_form' => false );

				// Handle the upload.
				$movefile = wp_handle_upload( $uploaded_file, $upload_overrides );

				if ( $movefile && ! isset( $movefile['error'] ) ) {
					// The file was uploaded successfully.
					$files = get_option( 'sms_attendance_uploaded_files', array() );
					if ( ! is_array( $files ) ) {
						$files = array();
					}
					$movefile['notice_name'] = sanitize_text_field( $_POST['notice_name'] ?? '' );
					$movefile['upload_date'] = current_time( 'Y-m-d H:i:s' );
					$files[] = $movefile;
					update_option( 'sms_attendance_uploaded_files', $files );
					wp_redirect( admin_url( 'admin.php?page=sms-attendance&sms_message=file_uploaded' ) );
					exit;
				} else {
					// An error occurred during the upload.
					wp_redirect( admin_url( 'admin.php?page=sms-attendance&sms_message=file_upload_error' ) );
					exit;
				}
			} else {
				wp_redirect( admin_url( 'admin.php?page=sms-attendance&sms_message=no_file_selected' ) );
				exit;
			}
		}

		// Handle notice file deletion.
		if ( isset( $_GET['action'] ) && 'delete_attendance_file' === $_GET['action'] && isset( $_GET['page'] ) && 'sms-attendance' === $_GET['page'] ) {
			if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'sms_delete_attendance_file_nonce' ) ) {
				wp_die( esc_html__( 'Security check failed', 'school-management-system' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Unauthorized access', 'school-management-system' ) );
			}

			$file_index = isset( $_GET['file_index'] ) ? intval( $_GET['file_index'] ) : -1;
			$files      = get_option( 'sms_attendance_uploaded_files', array() );

			if ( $file_index >= 0 && isset( $files[ $file_index ] ) ) {
				if ( ! empty( $files[ $file_index ]['file'] ) ) {
					wp_delete_file( $files[ $file_index ]['file'] );
				}
				unset( $files[ $file_index ] );
				update_option( 'sms_attendance_uploaded_files', array_values( $files ) );
			}

			wp_redirect( admin_url( 'admin.php?page=sms-attendance&sms_message=file_deleted' ) );
			exit;
		}

		// Handle teacher form submission.
		if ( isset( $_POST['sms_add_teacher'] ) || isset( $_POST['sms_edit_teacher'] ) ) {
			if ( ! isset( $_POST['sms_nonce'] ) || ! wp_verify_nonce( $_POST['sms_nonce'], 'sms_nonce_form' ) ) {
				wp_die( esc_html__( 'Security check failed', 'school-management-system' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Unauthorized access', 'school-management-system' ) );
			}

			$teacher_data = array(
				'first_name'   => sanitize_text_field( $_POST['first_name'] ?? '' ),
				'last_name'    => sanitize_text_field( $_POST['last_name'] ?? '' ),
				'email'        => sanitize_email( $_POST['email'] ?? '' ),
				'employee_id'  => sanitize_text_field( $_POST['employee_id'] ?? '' ),
				'phone'        => sanitize_text_field( $_POST['phone'] ?? '' ),
				'qualification' => sanitize_textarea_field( $_POST['qualifications'] ?? '' ),
				'status'       => sanitize_text_field( $_POST['status'] ?? 'active' ),
			);

			// Auto-generate Employee ID if empty.
			if ( empty( $teacher_data['employee_id'] ) ) {
				$teacher_data['employee_id'] = 'TCH-' . date( 'Y' ) . '-' . str_pad( Teacher::count() + 1, 3, '0', STR_PAD_LEFT );
			}

			if ( isset( $_POST['sms_add_teacher'] ) ) {
				$result = Teacher::add( $teacher_data );
				if ( $result && ! is_wp_error( $result ) ) {
					wp_redirect( admin_url( 'admin.php?page=sms-teachers' ) );
					exit;
				} else {
					$error_message = esc_html__( 'Failed to add teacher. Please check that all required fields are filled and that the employee ID is unique.', 'school-management-system' );
					if ( is_wp_error( $result ) ) {
						$error_message = 'Error: ' . $result->get_error_message();
					}
					wp_die( $error_message );
				}
			} elseif ( isset( $_POST['sms_edit_teacher'] ) ) {
				$teacher_id = intval( $_POST['teacher_id'] ?? 0 );
				$result = Teacher::update( $teacher_id, $teacher_data );
				if ( $result ) {
					wp_redirect( admin_url( 'admin.php?page=sms-teachers' ) );
					exit;
				} else {
					wp_die( esc_html__( 'Failed to update teacher.', 'school-management-system' ) );
				}
			}
		}

		// Handle student form submission.
		if ( isset( $_POST['sms_add_student'] ) || isset( $_POST['sms_edit_student'] ) ) {
			if ( ! isset( $_POST['sms_nonce'] ) || ! wp_verify_nonce( $_POST['sms_nonce'], 'sms_nonce_form' ) ) {
				wp_die( esc_html__( 'Security check failed', 'school-management-system' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Unauthorized access', 'school-management-system' ) );
			}

			$student_data = array(
				'first_name'   => sanitize_text_field( $_POST['first_name'] ?? '' ),
				'last_name'    => '.', // Default value as field is removed.
				'email'        => sanitize_email( $_POST['email'] ?? '' ),
				'roll_number'  => sanitize_text_field( $_POST['roll_number'] ?? '' ),
				'dob'          => sanitize_text_field( $_POST['dob'] ?? '' ),
				'gender'       => sanitize_text_field( $_POST['gender'] ?? '' ),
				'parent_name'  => sanitize_text_field( $_POST['parent_name'] ?? '' ),
				'parent_phone' => sanitize_text_field( $_POST['parent_phone'] ?? '' ),
				'address'      => sanitize_textarea_field( $_POST['address'] ?? '' ),
				'status'       => sanitize_text_field( $_POST['status'] ?? 'active' ),
			);

			$class_id = intval( $_POST['class_id'] ?? 0 );

			// Auto-generate Roll Number if empty.
			if ( empty( $student_data['roll_number'] ) ) {
				if ( $class_id > 0 ) {
					$class_obj = Classm::get( $class_id );
					if ( $class_obj ) {
						$prefix = preg_replace( '/[^A-Za-z0-9]/', '', $class_obj->class_code );
						$count = Enrollment::count( array( 'class_id' => $class_id ) );
						$sequence = $count + 1;
						do {
							$generated_roll = strtoupper( $prefix ) . '-' . date( 'Y' ) . '-' . str_pad( $sequence, 3, '0', STR_PAD_LEFT );
							$exists = Student::get_by_roll_number( $generated_roll );
							if ( $exists ) {
								$sequence++;
							}
						} while ( $exists );
						$student_data['roll_number'] = $generated_roll;
					} else {
						$student_data['roll_number'] = 'STU-' . date( 'Y' ) . '-' . str_pad( Student::count() + 1, 4, '0', STR_PAD_LEFT );
					}
				} else {
					$student_data['roll_number'] = 'STU-' . date( 'Y' ) . '-' . str_pad( Student::count() + 1, 4, '0', STR_PAD_LEFT );
				}
			}

			if ( isset( $_POST['sms_add_student'] ) ) {
				$result = Student::add( $student_data );
				if ( $result && ! is_wp_error( $result ) ) {
					if ( $class_id > 0 ) {
						Enrollment::add( array( 'student_id' => $result, 'class_id' => $class_id ) );
					}
					wp_redirect( admin_url( 'admin.php?page=sms-students&sms_message=student_added' ) );
					exit;
				} else {
					$error_message = is_wp_error( $result ) ? $result->get_error_message() : __( 'Failed to add student. Please ensure all required fields are filled correctly.', 'school-management-system' );
					wp_die( $error_message );
				}
			} elseif ( isset( $_POST['sms_edit_student'] ) ) {
				$student_id = intval( $_POST['student_id'] ?? 0 );
				$result     = Student::update( $student_id, $student_data );
				if ( false !== $result ) {
					if ( $class_id > 0 ) {
						// Update enrollment or add new.
						$enrollments = Enrollment::get_student_enrollments( $student_id );
						if ( ! empty( $enrollments ) ) {
							Enrollment::update( $enrollments[0]->id, array( 'class_id' => $class_id ) );
						} else {
							Enrollment::add( array( 'student_id' => $student_id, 'class_id' => $class_id ) );
						}
					}
					wp_redirect( admin_url( 'admin.php?page=sms-students&sms_message=student_updated' ) );
					exit;
				} else {
					wp_die( esc_html__( 'Failed to update student.', 'school-management-system' ) );
				}
			}
		}

		// Handle enrollment of an existing student.
		if ( isset( $_POST['sms_enroll_existing_student'] ) ) {
			if ( ! isset( $_POST['sms_nonce'] ) || ! wp_verify_nonce( $_POST['sms_nonce'], 'sms_nonce_form' ) ) {
				wp_die( esc_html__( 'Security check failed', 'school-management-system' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Unauthorized access', 'school-management-system' ) );
			}

			$enrollment_data = array(
				'student_id' => intval( $_POST['student_id'] ?? 0 ),
				'class_id'   => intval( $_POST['class_id'] ?? 0 ),
			);

			$result = Enrollment::add( $enrollment_data );
			if ( is_wp_error( $result ) && 'duplicate_enrollment' === $result->get_error_code() ) {
				wp_redirect( admin_url( 'admin.php?page=sms-enrollments&sms_message=student_already_enrolled' ) );
			} else {
				wp_redirect( admin_url( 'admin.php?page=sms-enrollments&sms_message=enrollment_added' ) );
			}
			exit;
		}
		// Handle creation and enrollment of a new student.
		if ( isset( $_POST['sms_create_and_enroll_student'] ) ) {
			if ( ! isset( $_POST['sms_nonce'] ) || ! wp_verify_nonce( $_POST['sms_nonce'], 'sms_nonce_form' ) ) {
				wp_die( esc_html__( 'Security check failed', 'school-management-system' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Unauthorized access', 'school-management-system' ) );
			}

			// Collect student data from form.
			$student_data = array(
				'first_name'   => sanitize_text_field( $_POST['first_name'] ?? '' ),
				'last_name'    => sanitize_text_field( $_POST['last_name'] ?? '' ),
				'email'        => sanitize_email( $_POST['email'] ?? '' ),
				'roll_number'  => sanitize_text_field( $_POST['roll_number'] ?? '' ),
				'dob'          => sanitize_text_field( $_POST['dob'] ?? '' ),
				'gender'       => sanitize_text_field( $_POST['gender'] ?? '' ),
				'parent_name'  => sanitize_text_field( $_POST['parent_name'] ?? '' ),
				'parent_phone' => sanitize_text_field( $_POST['parent_phone'] ?? '' ),
				'address'      => sanitize_textarea_field( $_POST['address'] ?? '' ),
				'status'       => 'active',
			);
			$class_id = intval( $_POST['class_id'] ?? 0 );

			if ( empty( $student_data['first_name'] ) || empty( $student_data['last_name'] ) || empty( $class_id ) ) {
				wp_redirect( admin_url( 'admin.php?page=sms-enrollments&sms_error=' . urlencode( 'First Name, Last Name, and Class are required.' ) ) );
				exit;
			}

			// Auto-generate Roll Number if empty.
			if ( empty( $student_data['roll_number'] ) ) {
				$student_data['roll_number'] = 'STU-' . date( 'Y' ) . '-' . str_pad( Student::count() + 1, 4, '0', STR_PAD_LEFT ) . '-' . rand( 100, 999 );
			}

			// Generate dummy email if empty.
			if ( empty( $student_data['email'] ) ) {
				$student_data['email'] = strtolower( preg_replace( '/[^a-z0-9]/i', '', $student_data['roll_number'] ) ) . '@school.local';
			}

			$student_id = 0;
			$existing_student = null;
			$redirect_message = '';

			// Check if student exists by roll number or email.
			if ( ! empty( $student_data['roll_number'] ) ) {
				$existing_student = Student::get_by_roll_number( $student_data['roll_number'] );
			}
			if ( ! $existing_student && ! empty( $student_data['email'] ) && strpos( $student_data['email'], '@school.local' ) === false ) {
				$user = get_user_by( 'email', $student_data['email'] );
				if ( $user ) {
					$existing_student = Student::get_by_user_id( $user->ID );
				}
			}

			if ( $existing_student ) {
				// Update existing student.
				$student_id = $existing_student->id;
				Student::update( $student_id, $student_data );
				$redirect_message = 'student_updated_and_enrolled';
			} else {
				// Create new student.
				$student_id = Student::add( $student_data );
				if ( is_wp_error( $student_id ) ) {
					wp_redirect( admin_url( 'admin.php?page=sms-enrollments&sms_error=' . urlencode( $student_id->get_error_message() ) ) );
					exit;
				}
				$redirect_message = 'student_created_and_enrolled';
			}

			// Enroll the student.
			if ( $student_id > 0 ) {
				$enrollment_result = Enrollment::add( array( 'student_id' => $student_id, 'class_id' => $class_id ) );

				if ( is_wp_error( $enrollment_result ) && 'duplicate_enrollment' === $enrollment_result->get_error_code() ) {
					wp_redirect( admin_url( 'admin.php?page=sms-enrollments&sms_message=student_already_enrolled' ) );
				} else {
					wp_redirect( admin_url( 'admin.php?page=sms-enrollments&sms_message=' . $redirect_message ) );
				}
			} else {
				wp_redirect( admin_url( 'admin.php?page=sms-enrollments&sms_error=' . urlencode( 'Could not create or find student.' ) ) );
			}
			exit;
		}

		// Handle class form submission.
		if ( isset( $_POST['sms_add_class'] ) || isset( $_POST['sms_edit_class'] ) ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Unauthorized access', 'school-management-system' ) );
			}

			$class_data = array(
				'class_name' => sanitize_text_field( $_POST['class_name'] ?? '' ),
				'class_code' => sanitize_text_field( $_POST['class_code'] ?? '' ),
				'capacity'   => intval( $_POST['capacity'] ?? 0 ),
				'status'     => sanitize_text_field( $_POST['status'] ?? 'active' ),
			);

			if ( isset( $_POST['sms_add_class'] ) ) {
				Classm::add( $class_data );
				wp_redirect( admin_url( 'admin.php?page=sms-classes' ) );
				exit;
			} elseif ( isset( $_POST['sms_edit_class'] ) ) {
				$class_id = intval( $_POST['class_id'] ?? 0 );
				Classm::update( $class_id, $class_data );
				wp_redirect( admin_url( 'admin.php?page=sms-classes' ) );
				exit;
			}
		}

		// Handle subject form submission.
		if ( isset( $_POST['sms_add_subject'] ) || isset( $_POST['sms_edit_subject'] ) ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Unauthorized access', 'school-management-system' ) );
			}

			$subject_data = array(
				'subject_name' => sanitize_text_field( $_POST['subject_name'] ?? '' ),
				'subject_code' => sanitize_text_field( $_POST['subject_code'] ?? '' ),
				'status'       => sanitize_text_field( $_POST['status'] ?? 'active' ),
			);

			if ( isset( $_POST['sms_add_subject'] ) ) {
				Subject::add( $subject_data );
				wp_redirect( admin_url( 'admin.php?page=sms-subjects' ) );
				exit;
			} elseif ( isset( $_POST['sms_edit_subject'] ) ) {
				$subject_id = intval( $_POST['subject_id'] ?? 0 );
				Subject::update( $subject_id, $subject_data );
				wp_redirect( admin_url( 'admin.php?page=sms-subjects' ) );
				exit;
			}
		}

		// Handle exam form submission.
		if ( isset( $_POST['sms_add_exam'] ) || isset( $_POST['sms_edit_exam'] ) ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Unauthorized access', 'school-management-system' ) );
			}

			$exam_data = array(
				'exam_name'     => sanitize_text_field( $_POST['exam_name'] ?? '' ),
				'exam_code'     => sanitize_text_field( $_POST['exam_code'] ?? '' ),
				'class_id'      => intval( $_POST['class_id'] ?? 0 ),
				'exam_date'     => sanitize_text_field( $_POST['exam_date'] ?? '' ),
				'total_marks'   => intval( $_POST['total_marks'] ?? 100 ),
				'passing_marks' => intval( $_POST['passing_marks'] ?? 40 ),
				'status'        => sanitize_text_field( $_POST['status'] ?? 'scheduled' ),
			);

			if ( isset( $_POST['sms_add_exam'] ) ) {
				Exam::add( $exam_data );
				wp_redirect( admin_url( 'admin.php?page=sms-exams' ) );
				exit;
			} elseif ( isset( $_POST['sms_edit_exam'] ) ) {
				$exam_id = intval( $_POST['exam_id'] ?? 0 );
				Exam::update( $exam_id, $exam_data );
				wp_redirect( admin_url( 'admin.php?page=sms-exams' ) );
				exit;
			}
		}

		// Handle result form submission.
		if ( isset( $_POST['sms_add_result'] ) || isset( $_POST['sms_edit_result'] ) ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Unauthorized access', 'school-management-system' ) );
			}

			$result_data = array(
				'student_id'    => intval( $_POST['student_id'] ?? 0 ),
				'exam_id'       => intval( $_POST['exam_id'] ?? 0 ),
				'subject_id'    => intval( $_POST['subject_id'] ?? 0 ),
				'obtained_marks' => floatval( $_POST['obtained_marks'] ?? 0 ),
			);

			if ( isset( $_POST['sms_add_result'] ) ) {
				Result::add( $result_data );
				wp_redirect( admin_url( 'admin.php?page=sms-results' ) );
				exit;
			} elseif ( isset( $_POST['sms_edit_result'] ) ) {
				$result_id = intval( $_POST['result_id'] ?? 0 );
				Result::update( $result_id, $result_data );
				wp_redirect( admin_url( 'admin.php?page=sms-results' ) );
				exit;
			}
		}

		// Handle fee form submission.
		if ( isset( $_POST['sms_add_fee'] ) || isset( $_POST['sms_edit_fee'] ) ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Unauthorized access', 'school-management-system' ) );
			}

			if ( ! isset( $_POST['sms_nonce'] ) || ! wp_verify_nonce( $_POST['sms_nonce'], 'sms_nonce_form' ) ) {
				wp_die( esc_html__( 'Security check failed', 'school-management-system' ) );
			}

			$fee_data = array(
				'student_id'   => intval( $_POST['student_id'] ?? 0 ),
				'class_id'     => intval( $_POST['class_id'] ?? 0 ),
				'fee_type'     => sanitize_text_field( $_POST['fee_type'] ?? '' ),
				'amount'       => sanitize_text_field( $_POST['amount'] ?? '' ),
				'payment_date' => sanitize_text_field( $_POST['payment_date'] ?? '' ),
				'status'       => sanitize_text_field( $_POST['status'] ?? 'pending' ),
				'remarks'      => sanitize_textarea_field( $_POST['remarks'] ?? '' ),
			);

			// Handle Paid Amount logic.
			$paid_amount = 0;
			if ( 'paid' === $fee_data['status'] ) {
				$paid_amount = floatval( $fee_data['amount'] );
			} elseif ( 'partially_paid' === $fee_data['status'] ) {
				$paid_amount = floatval( $_POST['paid_amount'] ?? 0 );
			} else {
				$paid_amount = 0;
			}
			$fee_data['paid_amount'] = $paid_amount;

			// Handle Fee Month/Year to Due Date.
			$month = intval( $_POST['fee_month'] ?? date('n') );
			$year  = intval( $_POST['fee_year'] ?? date('Y') );
			// Defaulting to the 10th of the month as the due date.
			$fee_data['due_date'] = date( 'Y-m-d', mktime( 0, 0, 0, $month, 10, $year ) );

			if ( empty( $fee_data['payment_date'] ) ) {
				$fee_data['payment_date'] = null;
			}

			if ( 'paid' === $fee_data['status'] && empty( $fee_data['payment_date'] ) ) {
				$fee_data['payment_date'] = current_time( 'Y-m-d' );
			}

			if ( isset( $_POST['sms_add_fee'] ) ) {
				$result = Fee::add( $fee_data );

				// Self-healing: Check for missing column error and fix.
				if ( is_wp_error( $result ) && ( strpos( $result->get_error_message(), "Unknown column 'payment_date'" ) !== false || strpos( $result->get_error_message(), "Unknown column 'paid_amount'" ) !== false ) ) {
					require_once SMS_PLUGIN_DIR . 'includes/class-activator.php';
					Activator::activate();
					$result = Fee::add( $fee_data );
				}

				if ( $result && ! is_wp_error( $result ) ) {
					wp_redirect( admin_url( 'admin.php?page=sms-fees&sms_message=fee_added' ) );
				} else {
					$error_msg = is_wp_error( $result ) ? $result->get_error_message() : 'Unknown error';
					wp_redirect( admin_url( 'admin.php?page=sms-fees&sms_message=fee_add_error&error=' . urlencode( $error_msg ) . '&student_id=' . $fee_data['student_id'] ) );
				}
				exit;
			} elseif ( isset( $_POST['sms_edit_fee'] ) ) {
				$fee_id = intval( $_POST['fee_id'] ?? 0 );
				$result = Fee::update( $fee_id, $fee_data );

				// Self-healing: Check for missing column error and fix.
				if ( is_wp_error( $result ) && ( strpos( $result->get_error_message(), "Unknown column 'payment_date'" ) !== false || strpos( $result->get_error_message(), "Unknown column 'paid_amount'" ) !== false ) ) {
					require_once SMS_PLUGIN_DIR . 'includes/class-activator.php';
					Activator::activate();
					$result = Fee::update( $fee_id, $fee_data );
				}

				if ( $result !== false && ! is_wp_error( $result ) ) {
					wp_redirect( admin_url( 'admin.php?page=sms-fees&sms_message=fee_updated&student_id=' . $fee_data['student_id'] ) );
				} else {
					$error_msg = is_wp_error( $result ) ? $result->get_error_message() : 'Unknown error';
					wp_redirect( admin_url( 'admin.php?page=sms-fees&sms_message=fee_update_error&error=' . urlencode( $error_msg ) . '&student_id=' . $fee_data['student_id'] ) );
				}
				exit;
			}
		}

		// Handle attendance marking.
		if ( isset( $_POST['sms_mark_attendance'] ) ) {
			if ( ! isset( $_POST['sms_nonce'] ) || ! wp_verify_nonce( $_POST['sms_nonce'], 'sms_nonce_form' ) ) {
				wp_die( esc_html__( 'Security check failed', 'school-management-system' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Unauthorized access', 'school-management-system' ) );
			}

			$class_id = intval( $_POST['class_id'] ?? 0 );
			$attendance_date = sanitize_text_field( $_POST['attendance_date'] ?? '' );
			$attendance_data = $_POST['attendance'] ?? array();

			if ( $class_id > 0 && ! empty( $attendance_date ) && is_array( $attendance_data ) ) {
				foreach ( $attendance_data as $student_id => $status ) {
					Attendance::mark_attendance( intval( $student_id ), $class_id, $attendance_date, sanitize_text_field( $status ) );
				}
				wp_redirect( add_query_arg( array( 'page' => 'sms-student-attendance', 'class_id' => $class_id, 'date' => $attendance_date, 'sms_message' => 'attendance_saved' ), admin_url( 'admin.php' ) ) );
				exit;
			} else {
				wp_die( esc_html__( 'Invalid data provided.', 'school-management-system' ) );
			}
		}

		// Handle settings save.
		if ( isset( $_POST['sms_save_settings'] ) ) {
			if ( ! isset( $_POST['sms_settings_nonce_field'] ) || ! wp_verify_nonce( $_POST['sms_settings_nonce_field'], 'sms_settings_nonce' ) ) {
				wp_die( esc_html__( 'Security check failed', 'school-management-system' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Unauthorized access', 'school-management-system' ) );
			}

			$settings = get_option( 'sms_settings', array() );

			// Sanitize and save settings from all tabs.
			// General Tab.
			if ( isset( $_POST['school_name'] ) ) {
				$settings['school_name'] = sanitize_text_field( $_POST['school_name'] );
			}
			if ( isset( $_POST['school_logo'] ) ) {
				$settings['school_logo'] = esc_url_raw( $_POST['school_logo'] );
			}
			if ( isset( $_POST['school_address'] ) ) {
				$settings['school_address'] = sanitize_textarea_field( $_POST['school_address'] );
			}
			if ( isset( $_POST['school_email'] ) ) {
				$settings['school_email'] = sanitize_email( $_POST['school_email'] );
			}
			if ( isset( $_POST['school_phone'] ) ) {
				$settings['school_phone'] = sanitize_text_field( $_POST['school_phone'] );
			}

			// Academics Tab.
			if ( isset( $_POST['academic_year'] ) ) {
				$settings['academic_year'] = sanitize_text_field( $_POST['academic_year'] );
			}
			if ( isset( $_POST['passing_marks'] ) ) {
				$settings['passing_marks'] = intval( $_POST['passing_marks'] );
			}

			// Fees Tab.
			if ( isset( $_POST['currency'] ) ) {
				$settings['currency'] = sanitize_text_field( $_POST['currency'] );
			}

			update_option( 'sms_settings', $settings );

			$redirect_url = add_query_arg( array( 'sms_message' => 'settings_saved' ), wp_get_referer() );
			wp_redirect( $redirect_url );
			exit;
		}
	}
}
