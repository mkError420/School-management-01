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

		// Fees submenu.
		add_submenu_page(
			'sms-dashboard',
			__( 'Fees', 'school-management-system' ),
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
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'School Management System Settings', 'school-management-system' ); ?></h1>
			
			<form method="post" action="">
				<?php wp_nonce_field( 'sms_settings_nonce' ); ?>
				
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="school_name"><?php esc_html_e( 'School Name', 'school-management-system' ); ?></label>
						</th>
						<td>
							<input type="text" name="school_name" id="school_name" class="regular-text" value="" />
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="school_email"><?php esc_html_e( 'School Email', 'school-management-system' ); ?></label>
						</th>
						<td>
							<input type="email" name="school_email" id="school_email" class="regular-text" value="" />
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="school_phone"><?php esc_html_e( 'School Phone', 'school-management-system' ); ?></label>
						</th>
						<td>
							<input type="text" name="school_phone" id="school_phone" class="regular-text" value="" />
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="passing_marks"><?php esc_html_e( 'Passing Marks', 'school-management-system' ); ?></label>
						</th>
						<td>
							<input type="number" name="passing_marks" id="passing_marks" class="small-text" value="" />
						</td>
					</tr>
				</table>
				
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Handle form submissions.
	 */
	public function handle_form_submission() {
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
