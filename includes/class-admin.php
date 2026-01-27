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
		add_submenu_page(
			'sms-dashboard',
			__( 'Fees Dashboard', 'school-management-system' ),
			__( 'Fees', 'school-management-system' ),
			'manage_options',
			'sms-fees',
			array( $this, 'display_fees' )
		);

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
		add_submenu_page(
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
			<h1><?php esc_html_e( 'School Management System Dashboard', 'school-management-system' ); ?></h1>
			
			<div id="dashboard-widgets-wrap">
				<div id="dashboard-widgets" class="metabox-holder columns-2">
					<div id="postbox-container-1" class="postbox-container">
						<div class="meta-box-sortables">
							<div class="postbox">
								<h2 class="hndle"><span><?php esc_html_e( 'Statistics', 'school-management-system' ); ?></span></h2>
								<div class="inside">
									<div class="sms-dashboard-cards">
										<div class="sms-card">
											<h3><?php esc_html_e( 'Total Students', 'school-management-system' ); ?></h3>
											<p class="sms-card-value"><?php echo intval( Student::count() ); ?></p>
										</div>
										<div class="sms-card">
											<h3><?php esc_html_e( 'Total Teachers', 'school-management-system' ); ?></h3>
											<p class="sms-card-value"><?php echo intval( Teacher::count() ); ?></p>
										</div>
										<div class="sms-card">
											<h3><?php esc_html_e( 'Total Classes', 'school-management-system' ); ?></h3>
											<p class="sms-card-value"><?php echo intval( Classm::count() ); ?></p>
										</div>
										<div class="sms-card">
											<h3><?php esc_html_e( 'Total Exams', 'school-management-system' ); ?></h3>
											<p class="sms-card-value"><?php echo intval( Exam::count() ); ?></p>
										</div>
										<div class="sms-card">
											<h3><?php esc_html_e( 'Present Today', 'school-management-system' ); ?></h3>
											<p class="sms-card-value">
												<?php echo intval( Attendance::count( array( 'attendance_date' => current_time( 'Y-m-d' ), 'status' => 'present' ) ) ); ?>
											</p>
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
													<td colspan="4"><?php esc_html_e( 'No upcoming exams', 'school-management-system' ); ?></td>
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
									<ul style="margin-left: 0; padding-left: 0;">
										<?php foreach ( $uploaded_files as $file ) : ?>
											<li style="margin-bottom: 5px;">
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
	 * Display settings page.
	 */
	public function display_settings() {
		$settings = get_option( 'sms_settings', array() );
		$message = '';
		if ( isset( $_GET['sms_message'] ) && 'settings_saved' === $_GET['sms_message'] ) {
			$message = __( 'Settings saved successfully.', 'school-management-system' );
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'School Management System Settings', 'school-management-system' ); ?></h1>
			
			<?php if ( ! empty( $message ) ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $message ); ?></p></div>
			<?php endif; ?>

			<form method="post" action="">
				<?php wp_nonce_field( 'sms_settings_nonce', 'sms_settings_nonce_field' ); ?>
				
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="school_name"><?php esc_html_e( 'School Name', 'school-management-system' ); ?></label>
						</th>
						<td>
							<input type="text" name="school_name" id="school_name" class="regular-text" value="<?php echo esc_attr( $settings['school_name'] ?? '' ); ?>" />
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="school_email"><?php esc_html_e( 'School Email', 'school-management-system' ); ?></label>
						</th>
						<td>
							<input type="email" name="school_email" id="school_email" class="regular-text" value="<?php echo esc_attr( $settings['school_email'] ?? '' ); ?>" />
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="school_phone"><?php esc_html_e( 'School Phone', 'school-management-system' ); ?></label>
						</th>
						<td>
							<input type="text" name="school_phone" id="school_phone" class="regular-text" value="<?php echo esc_attr( $settings['school_phone'] ?? '' ); ?>" />
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="passing_marks"><?php esc_html_e( 'Passing Marks', 'school-management-system' ); ?></label>
						</th>
						<td>
							<input type="number" name="passing_marks" id="passing_marks" class="small-text" value="<?php echo esc_attr( $settings['passing_marks'] ?? '' ); ?>" />
						</td>
					</tr>
				</table>
				
				<?php submit_button( __( 'Save Settings', 'school-management-system' ), 'primary', 'sms_save_settings' ); ?>
			</form>
		</div>
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
			$currency = 'Taka';
			$school_name = $settings['school_name'] ?? 'School Management System';

			?>
			<!DOCTYPE html>
			<html>
			<head>
				<title><?php esc_html_e( 'Fee Voucher', 'school-management-system' ); ?> - <?php echo intval( $fee->id ); ?></title>
				<style>
					body { font-family: Arial, sans-serif; background: #f0f0f0; padding: 20px; }
					.voucher-container { max-width: 800px; margin: 0 auto; background: #fff; padding: 40px; border: 1px solid #ddd; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
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
					@media print {
						body { background: #fff; padding: 0; }
						.voucher-container { box-shadow: none; border: none; padding: 0; }
						.print-btn { display: none; }
					}
				</style>
			</head>
			<body>
				<div class="voucher-container">
					<a href="#" onclick="window.print(); return false;" class="print-btn"><?php esc_html_e( 'Click here to Print / Save as PDF', 'school-management-system' ); ?></a>
					
					<div class="header">
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
							<tr class="total-row">
								<td colspan="2" style="text-align: right;"><?php esc_html_e( 'Total', 'school-management-system' ); ?></td>
								<td style="text-align: right;"><?php echo esc_html( $currency . ' ' . number_format( $fee->amount, 2 ) ); ?></td>
							</tr>
						</tbody>
					</table>

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
				Enrollment::delete( $enrollment_id );
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

		// Handle enrollment form submission.
		if ( isset( $_POST['sms_add_enrollment'] ) ) {
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
			if ( $result && ! is_wp_error( $result ) ) {
				wp_redirect( admin_url( 'admin.php?page=sms-enrollments&sms_message=enrollment_added' ) );
				exit;
			} else {
				$error_message = is_wp_error( $result ) ? $result->get_error_message() : __( 'Failed to add enrollment.', 'school-management-system' );
				wp_die( $error_message );
			}
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
				'due_date'     => sanitize_text_field( $_POST['due_date'] ?? '' ),
				'payment_date' => sanitize_text_field( $_POST['payment_date'] ?? '' ),
				'status'       => sanitize_text_field( $_POST['status'] ?? 'pending' ),
				'remarks'      => sanitize_textarea_field( $_POST['remarks'] ?? '' ),
			);

			// Handle empty dates.
			if ( empty( $fee_data['due_date'] ) ) {
				$fee_data['due_date'] = null;
			}
			if ( empty( $fee_data['payment_date'] ) ) {
				$fee_data['payment_date'] = null;
			}

			if ( 'paid' === $fee_data['status'] && empty( $fee_data['payment_date'] ) ) {
				$fee_data['payment_date'] = current_time( 'Y-m-d' );
			}

			if ( isset( $_POST['sms_add_fee'] ) ) {
				$result = Fee::add( $fee_data );

				// Self-healing: Check for missing column error and fix.
				if ( is_wp_error( $result ) && strpos( $result->get_error_message(), "Unknown column 'payment_date'" ) !== false ) {
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
				if ( is_wp_error( $result ) && strpos( $result->get_error_message(), "Unknown column 'payment_date'" ) !== false ) {
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
	}
}
