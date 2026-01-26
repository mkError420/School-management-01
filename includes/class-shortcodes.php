<?php
/**
 * Shortcodes class for frontend portals.
 *
 * @package School_Management_System
 */

namespace School_Management_System;

/**
 * Shortcodes class
 */
class Shortcodes {

	/**
	 * Register all shortcodes.
	 */
	public function register_shortcodes() {
		add_shortcode( 'sms_student_login', array( $this, 'student_login_shortcode' ) );
		add_shortcode( 'sms_student_portal', array( $this, 'student_portal_shortcode' ) );
		add_shortcode( 'sms_parent_portal', array( $this, 'parent_portal_shortcode' ) );
		add_shortcode( 'sms_class_timetable', array( $this, 'timetable_shortcode' ) );
		add_shortcode( 'sms_exam_results', array( $this, 'exam_results_shortcode' ) );
	}

	/**
	 * Student login shortcode.
	 *
	 * @return string HTML for student login form.
	 */
	public function student_login_shortcode() {
		if ( is_user_logged_in() ) {
			$user = wp_get_current_user();
			if ( Auth::is_student( $user->ID ) ) {
				return '<p>' . esc_html__( 'You are already logged in', 'school-management-system' ) . '</p>';
			}
		}

		ob_start();
		?>
		<div class="sms-login-container">
			<h2><?php esc_html_e( 'Student Login', 'school-management-system' ); ?></h2>
			<form method="post" class="sms-login-form">
				<?php wp_nonce_field( 'sms_login_form', 'sms_login_nonce' ); ?>
				
				<div class="sms-form-group">
					<label for="sms_email"><?php esc_html_e( 'Email', 'school-management-system' ); ?></label>
					<input type="email" id="sms_email" name="sms_email" required />
				</div>

				<div class="sms-form-group">
					<label for="sms_password"><?php esc_html_e( 'Password', 'school-management-system' ); ?></label>
					<input type="password" id="sms_password" name="sms_password" required />
				</div>

				<button type="submit" name="sms_login_submit" class="sms-btn"><?php esc_html_e( 'Login', 'school-management-system' ); ?></button>
			</form>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Student portal shortcode.
	 *
	 * @return string HTML for student portal.
	 */
	public function student_portal_shortcode() {
		if ( ! is_user_logged_in() || ! Auth::is_student() ) {
			return '<p>' . esc_html__( 'You must be logged in as a student to view this content', 'school-management-system' ) . '</p>';
		}

		$user = wp_get_current_user();
		$student = Student::get_by_user_id( $user->ID );

		if ( ! $student ) {
			return '<p>' . esc_html__( 'Student profile not found', 'school-management-system' ) . '</p>';
		}

		ob_start();
		?>
		<div class="sms-student-portal">
			<h2><?php esc_html_e( 'Student Portal', 'school-management-system' ); ?></h2>

			<div class="sms-student-info">
				<h3><?php esc_html_e( 'Student Information', 'school-management-system' ); ?></h3>
				<table>
					<tr>
						<th><?php esc_html_e( 'Name', 'school-management-system' ); ?></th>
						<td><?php echo esc_html( $student->first_name . ' ' . $student->last_name ); ?></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Roll Number', 'school-management-system' ); ?></th>
						<td><?php echo esc_html( $student->roll_number ); ?></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Email', 'school-management-system' ); ?></th>
						<td><?php echo esc_html( $student->email ); ?></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Enrollment Date', 'school-management-system' ); ?></th>
						<td><?php echo esc_html( $student->enrollment_date ); ?></td>
					</tr>
				</table>
			</div>

			<h3><?php esc_html_e( 'Results', 'school-management-system' ); ?></h3>
			<table class="sms-results-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Exam', 'school-management-system' ); ?></th>
						<th><?php esc_html_e( 'Obtained Marks', 'school-management-system' ); ?></th>
						<th><?php esc_html_e( 'Percentage', 'school-management-system' ); ?></th>
						<th><?php esc_html_e( 'Grade', 'school-management-system' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$results = Result::get_student_results( $student->id );
					if ( ! empty( $results ) ) {
						foreach ( $results as $result ) {
							$exam = Exam::get( $result->exam_id );
							?>
							<tr>
								<td><?php echo esc_html( $exam->exam_name ?? 'N/A' ); ?></td>
								<td><?php echo floatval( $result->obtained_marks ); ?></td>
								<td><?php echo number_format( floatval( $result->percentage ), 2 ); ?>%</td>
								<td><?php echo esc_html( $result->grade ); ?></td>
							</tr>
							<?php
						}
					} else {
						?>
						<tr>
							<td colspan="4"><?php esc_html_e( 'No results found', 'school-management-system' ); ?></td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>

			<p>
				<a href="<?php echo esc_url( home_url( '/?sms_action=logout' ) ); ?>" class="sms-btn-logout">
					<?php esc_html_e( 'Logout', 'school-management-system' ); ?>
				</a>
			</p>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Parent portal shortcode.
	 *
	 * @return string HTML for parent portal.
	 */
	public function parent_portal_shortcode() {
		if ( ! is_user_logged_in() || ! Auth::is_parent() ) {
			return '<p>' . esc_html__( 'You must be logged in as a parent to view this content', 'school-management-system' ) . '</p>';
		}

		ob_start();
		?>
		<div class="sms-parent-portal">
			<h2><?php esc_html_e( 'Parent Portal', 'school-management-system' ); ?></h2>
			<p><?php esc_html_e( 'Welcome to the parent portal. You can view your child\'s academic progress here.', 'school-management-system' ); ?></p>

			<p>
				<a href="<?php echo esc_url( home_url( '/?sms_action=logout' ) ); ?>" class="sms-btn-logout">
					<?php esc_html_e( 'Logout', 'school-management-system' ); ?>
				</a>
			</p>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Timetable shortcode.
	 *
	 * @return string HTML for class timetable.
	 */
	public function timetable_shortcode() {
		ob_start();
		?>
		<div class="sms-timetable">
			<h2><?php esc_html_e( 'Class Timetable', 'school-management-system' ); ?></h2>
			<p><?php esc_html_e( 'Timetable feature coming soon.', 'school-management-system' ); ?></p>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Exam results shortcode.
	 *
	 * @return string HTML for exam results lookup.
	 */
	public function exam_results_shortcode() {
		ob_start();
		?>
		<div class="sms-exam-results">
			<h2><?php esc_html_e( 'Exam Results Lookup', 'school-management-system' ); ?></h2>
			
			<form method="get" class="sms-results-search">
				<input type="text" name="roll_number" placeholder="<?php esc_attr_e( 'Enter Roll Number', 'school-management-system' ); ?>" />
				<button type="submit"><?php esc_html_e( 'Search', 'school-management-system' ); ?></button>
			</form>

			<?php
			if ( isset( $_GET['roll_number'] ) ) {
				$roll_number = sanitize_text_field( $_GET['roll_number'] );
				$student = Student::get_by_roll_number( $roll_number );

				if ( $student ) {
					$results = Result::get_student_results( $student->id );
					?>
					<div class="sms-results-display">
						<h3><?php echo esc_html( $student->first_name . ' ' . $student->last_name ); ?></h3>
						<table class="sms-results-table">
							<thead>
								<tr>
									<th><?php esc_html_e( 'Exam', 'school-management-system' ); ?></th>
									<th><?php esc_html_e( 'Obtained Marks', 'school-management-system' ); ?></th>
									<th><?php esc_html_e( 'Percentage', 'school-management-system' ); ?></th>
									<th><?php esc_html_e( 'Grade', 'school-management-system' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								if ( ! empty( $results ) ) {
									foreach ( $results as $result ) {
										$exam = Exam::get( $result->exam_id );
										?>
										<tr>
											<td><?php echo esc_html( $exam->exam_name ?? 'N/A' ); ?></td>
											<td><?php echo floatval( $result->obtained_marks ); ?></td>
											<td><?php echo number_format( floatval( $result->percentage ), 2 ); ?>%</td>
											<td><?php echo esc_html( $result->grade ); ?></td>
										</tr>
										<?php
									}
								} else {
									?>
									<tr>
										<td colspan="4"><?php esc_html_e( 'No results found', 'school-management-system' ); ?></td>
									</tr>
									<?php
								}
								?>
							</tbody>
						</table>
					</div>
					<?php
				} else {
					?>
					<p><?php esc_html_e( 'Student not found', 'school-management-system' ); ?></p>
					<?php
				}
			}
			?>
		</div>
		<?php
		return ob_get_clean();
	}
}
