<?php
/**
 * AJAX handlers for School Management System.
 *
 * @package School_Management_System
 */

namespace School_Management_System;

/**
 * Submit attendance via AJAX.
 */
function sms_ajax_submit_attendance() {
	check_ajax_referer( 'sms_admin_nonce', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( __( 'Unauthorized', 'school-management-system' ) );
	}

	$student_id = intval( $_POST['student_id'] ?? 0 );
	$class_id = intval( $_POST['class_id'] ?? 0 );
	$attendance_date = sanitize_text_field( $_POST['attendance_date'] ?? '' );
	$status = sanitize_text_field( $_POST['status'] ?? 'present' );

	if ( empty( $student_id ) || empty( $class_id ) || empty( $attendance_date ) ) {
		wp_send_json_error( __( 'Missing required fields', 'school-management-system' ) );
	}

	$result = Attendance::mark_attendance( $student_id, $class_id, $attendance_date, $status );

	if ( $result ) {
		wp_send_json_success( __( 'Attendance marked successfully', 'school-management-system' ) );
	} else {
		wp_send_json_error( __( 'Failed to mark attendance', 'school-management-system' ) );
	}
}

/**
 * Enroll student via AJAX.
 */
function sms_ajax_enroll_student() {
	check_ajax_referer( 'sms_admin_nonce', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( __( 'Unauthorized', 'school-management-system' ) );
	}

	$student_id = intval( $_POST['student_id'] ?? 0 );
	$class_id = intval( $_POST['class_id'] ?? 0 );
	$subject_id = intval( $_POST['subject_id'] ?? 0 );

	if ( empty( $student_id ) || empty( $class_id ) ) {
		wp_send_json_error( __( 'Missing required fields', 'school-management-system' ) );
	}

	$enrollment_data = array(
		'student_id' => $student_id,
		'class_id'   => $class_id,
	);

	if ( ! empty( $subject_id ) ) {
		$enrollment_data['subject_id'] = $subject_id;
	}

	$result = Enrollment::add( $enrollment_data );

	if ( $result ) {
		wp_send_json_success( __( 'Student enrolled successfully', 'school-management-system' ) );
	} else {
		wp_send_json_error( __( 'Failed to enroll student', 'school-management-system' ) );
	}
}

/**
 * Search data via AJAX.
 */
function sms_ajax_search_data() {
	check_ajax_referer( 'sms_admin_nonce', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( __( 'Unauthorized', 'school-management-system' ) );
	}

	$search_term = sanitize_text_field( $_POST['search_term'] ?? '' );
	$type = sanitize_text_field( $_POST['type'] ?? 'students' );

	if ( empty( $search_term ) ) {
		wp_send_json_error( __( 'Search term is required', 'school-management-system' ) );
	}

	$results = array();

	switch ( $type ) {
		case 'students':
			$results = Student::search( $search_term );
			break;
		case 'teachers':
			$results = Teacher::search( $search_term );
			break;
		case 'classes':
			$results = Classm::search( $search_term );
			break;
		case 'subjects':
			$results = Subject::search( $search_term );
			break;
		case 'exams':
			$results = Exam::search( $search_term );
			break;
	}

	if ( ! empty( $results ) ) {
		wp_send_json_success( $results );
	} else {
		wp_send_json_error( __( 'No results found', 'school-management-system' ) );
	}
}

/**
 * Generate payment voucher via AJAX.
 */
function sms_ajax_generate_voucher() {
	// Enable error reporting for debugging
	error_reporting(E_ALL);
	ini_set('display_errors', 0); // Don't display errors, but log them
	
	// Log the start of the process
	error_log('Voucher Generation Started: ' . date('Y-m-d H:i:s'));
	error_log('POST Data: ' . print_r($_POST, true));
	
	try {
		check_ajax_referer( 'sms_generate_voucher_nonce', 'nonce' );
		error_log('Nonce verification passed');

		if ( ! current_user_can( 'manage_options' ) ) {
			error_log('Permission denied - user lacks manage_options capability');
			wp_send_json_error( __( 'Unauthorized', 'school-management-system' ) );
		}

		$fee_id = intval( $_POST['fee_id'] ?? 0 );
		error_log('Fee ID: ' . $fee_id);

		if ( empty( $fee_id ) ) {
			error_log('Empty fee ID provided');
			wp_send_json_error( __( 'Missing fee ID', 'school-management-system' ) );
		}

		// Get fee details
		error_log('Attempting to get fee details...');
		$fee = Fee::get( $fee_id );
		if ( ! $fee ) {
			error_log('Fee not found for ID: ' . $fee_id);
			wp_send_json_error( __( 'Fee record not found', 'school-management-system' ) );
		}
		error_log('Fee details retrieved successfully');

		// Get student and class details
		error_log('Getting student details...');
		$student = Student::get( $fee->student_id );
		$class = Classm::get( $fee->class_id );

		if ( ! $student || ! $class ) {
			error_log('Student or class information not found');
			wp_send_json_error( __( 'Student or class information not found', 'school-management-system' ) );
		}
		error_log('Student and class details retrieved successfully');

		// Generate voucher HTML
		error_log('Generating voucher HTML...');
		$voucher_html = generate_voucher_html( $fee, $student, $class );
		error_log('Voucher HTML generated successfully');

		// Create temporary file
		$upload_dir = wp_upload_dir();
		$vouchers_dir = $upload_dir['basedir'] . '/school-vouchers/';
		error_log('Vouchers directory: ' . $vouchers_dir);
		
		if ( ! file_exists( $vouchers_dir ) ) {
			error_log('Creating vouchers directory...');
			if ( ! wp_mkdir_p( $vouchers_dir ) ) {
				error_log('Failed to create vouchers directory');
				wp_send_json_error( __( 'Failed to create vouchers directory', 'school-management-system' ) );
			}
			error_log('Vouchers directory created successfully');
		}

		// Check if directory is writable
		if ( ! is_writable( $vouchers_dir ) ) {
			error_log('Vouchers directory is not writable');
			wp_send_json_error( __( 'Vouchers directory is not writable', 'school-management-system' ) );
		}
		error_log('Vouchers directory is writable');

		$filename = 'voucher_' . $fee->id . '_' . time() . '.html'; // Changed to .html by default
		$filepath = $vouchers_dir . $filename;
		error_log('File path: ' . $filepath);

		// Generate HTML voucher (simplified approach)
		error_log('Creating HTML voucher...');
		$html_result = create_simple_html_voucher( $voucher_html, $filepath );

		if ( $html_result && file_exists( $filepath ) ) {
			error_log('HTML voucher created successfully');
			$html_url = $upload_dir['baseurl'] . '/school-vouchers/' . $filename;
			
			wp_send_json_success( array(
				'url' => $html_url,
				'filename' => 'Payment_Voucher_' . $student->roll_number . '_' . date( 'Y-m-d' ) . '.html',
				'type' => 'html',
				'message' => __( 'Voucher downloaded as HTML file. Press Ctrl+P (Windows/Linux) or Cmd+P (Mac) to save as PDF.', 'school-management-system' )
			) );
		} else {
			error_log('Failed to create HTML voucher');
			wp_send_json_error( __( 'Failed to generate voucher file', 'school-management-system' ) );
		}
		
	} catch ( Exception $e ) {
		error_log( 'Voucher Generation Exception: ' . $e->getMessage() );
		error_log( 'Exception Trace: ' . $e->getTraceAsString() );
		wp_send_json_error( __( 'An error occurred while generating the voucher: ', 'school-management-system' ) . $e->getMessage() );
	} catch ( Error $e ) {
		error_log( 'Voucher Generation Fatal Error: ' . $e->getMessage() );
		error_log( 'Fatal Error Trace: ' . $e->getTraceAsString() );
		wp_send_json_error( __( 'A fatal error occurred while generating the voucher: ', 'school-management-system' ) . $e->getMessage() );
	}
}

/**
 * Create a simple HTML voucher (fallback method).
 */
function create_simple_html_voucher( $voucher_html, $filepath ) {
	try {
		error_log('Creating simple HTML voucher...');

		$result = file_put_contents( $filepath, $voucher_html );
		error_log('File write result: ' . ($result ? 'success' : 'failed'));
		
		return $result !== false;
		
	} catch ( Exception $e ) {
		error_log( 'Simple HTML Voucher Error: ' . $e->getMessage() );
		return false;
	}
}

/**
 * Generate voucher HTML content.
 */
function generate_voucher_html( $fee, $student, $class ) {
	$settings = get_option( 'sms_settings' );
	$school_name = $settings['school_name'] ?? 'School Management System';
	$school_logo = $settings['school_logo'] ?? '';
	$school_address = $settings['school_address'] ?? '';
	$school_phone = $settings['school_phone'] ?? '';
	$currency = $settings['currency'] ?? '৳';

	$due_amount = $fee->amount - $fee->paid_amount;
	
	ob_start();
	?>
	<!DOCTYPE html>
	<html>
	<head>
		<meta charset="utf-8">
		<title>Payment Voucher</title>
		<style>
			@page { 
				size: A4; 
				margin: 10mm; 
			}
			body { 
				font-family: 'Georgia', serif; 
				margin: 0; 
				padding: 0; 
				background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
				font-size: 12px;
			}
			.voucher-container { 
				max-width: 100%; 
				margin: 0 auto; 
				background: white; 
				border-radius: 10px; 
				overflow: hidden;
				box-shadow: 0 10px 20px rgba(0,0,0,0.1);
				position: relative;
			}
			.voucher-header { 
				background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
				color: white; 
				padding: 20px 15px; 
				text-align: center; 
				position: relative;
				overflow: hidden;
			}
			.voucher-header .header-inner {
				display: flex;
				align-items: center;
				justify-content: center;
				gap: 12px;
			}
			.voucher-logo {
				max-height: 36px;
				max-width: 36px;
				border-radius: 6px;
				background: rgba(255,255,255,0.18);
				padding: 4px;
			}
			.voucher-header::before {
				content: '';
				position: absolute;
				top: -50%;
				right: -50%;
				width: 200%;
				height: 200%;
				background: repeating-linear-gradient(
					45deg,
					transparent,
					transparent 10px,
					rgba(255,255,255,0.05) 10px,
					rgba(255,255,255,0.05) 20px
				);
				animation: slide 20s linear infinite;
			}
			@keyframes slide {
				0% { transform: translate(0, 0); }
				100% { transform: translate(50px, 50px); }
			}
			.voucher-header h1 { 
				margin: 0; 
				font-size: 24px; 
				font-weight: 700; 
				text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
				position: relative;
				z-index: 2;
			}
			.voucher-header p { 
				margin: 5px 0 0 0; 
				opacity: 0.95; 
				font-size: 12px;
				position: relative;
				z-index: 2;
			}
			.voucher-body { 
				padding: 20px 15px; 
				background: white;
				position: relative;
			}
			.voucher-number { 
				background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 50%, #fecfef 100%);
				color: #d63384; 
				padding: 8px 15px; 
				border-radius: 20px; 
				font-weight: 700; 
				display: inline-block; 
				margin-bottom: 15px;
				font-size: 12px;
				box-shadow: 0 2px 8px rgba(214, 51, 132, 0.2);
				border: 1px solid #fff;
			}
			.voucher-info { 
				display: grid; 
				grid-template-columns: 1fr 1fr; 
				gap: 20px; 
				margin-bottom: 20px;
			}
			.info-section { 
				background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
				padding: 15px; 
				border-radius: 8px;
				border-left: 3px solid #667eea;
				box-shadow: 0 2px 8px rgba(0,0,0,0.05);
			}
			.info-section h3 { 
				margin: 0 0 10px 0; 
				color: #2c3e50; 
				font-size: 14px;
				border-bottom: 1px solid #667eea; 
				padding-bottom: 5px;
				position: relative;
			}
			.info-section h3::after {
				content: '';
				position: absolute;
				bottom: -1px;
				left: 0;
				width: 30px;
				height: 1px;
				background: #764ba2;
			}
			.info-row { 
				display: flex; 
				margin-bottom: 8px;
				align-items: center;
			}
			.info-label { 
				font-weight: 600; 
				color: #495057; 
				min-width: 100px;
				font-size: 11px;
			}
			.info-value { 
				color: #212529; 
				font-weight: 500;
				font-size: 11px;
			}
			.payment-details { 
				background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
				padding: 15px; 
				border-radius: 8px; 
				margin-bottom: 15px;
				border: 1px solid #e9ecef;
				position: relative;
			}
			.payment-details::before {
				content: '💰';
				position: absolute;
				top: -10px;
				left: 15px;
				background: white;
				padding: 2px 8px;
				border-radius: 10px;
				font-size: 14px;
			}
			.payment-details h3 { 
				margin: 0 0 10px 0; 
				color: #2c3e50;
				text-align: center;
				font-size: 14px;
			}
			.payment-row { 
				display: flex; 
				justify-content: space-between; 
				margin-bottom: 8px; 
				padding: 6px 10px;
				background: rgba(102, 126, 234, 0.05);
				border-radius: 5px;
				font-size: 11px;
			}
			.payment-row.total { 
				background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
				color: white;
				font-weight: 700; 
				font-size: 12px;
				margin-top: 10px;
				box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
			}
			.voucher-footer { 
				text-align: center; 
				padding: 15px; 
				background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
				color: white;
				position: relative;
			}
			.voucher-footer p {
				margin: 2px 0;
				font-size: 10px;
			}
			.watermark { 
				position: absolute; 
				top: 50%; 
				left: 50%; 
				transform: translate(-50%, -50%) rotate(-45deg); 
				font-size: 100px; 
				font-weight: 700; 
				z-index: 1;
				pointer-events: none;
			}
			.watermark.paid { color: rgba(40, 167, 69, 0.08); }
			.watermark.partially-paid { color: rgba(255, 193, 7, 0.08); }
			.watermark.pending { color: rgba(108, 117, 125, 0.08); }
			.status-paid { 
				color: #28a745; 
				font-weight: 700; 
				background: rgba(40, 167, 69, 0.1);
				padding: 2px 8px;
				border-radius: 10px;
				border: 1px solid #28a745;
				font-size: 10px;
			}
			.status-partial { 
				color: #ffc107; 
				font-weight: 700;
				background: rgba(255, 193, 7, 0.1);
				padding: 2px 8px;
				border-radius: 10px;
				border: 1px solid #ffc107;
				font-size: 10px;
			}
			.signature-section { 
				margin-top: 20px; 
				padding: 15px; 
				background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
				border-radius: 8px;
				border-top: 2px solid #667eea;
			}
			.signature-row { 
				display: flex; 
				justify-content: space-between; 
				gap: 30px; 
			}
			.signature-box { 
				flex: 1; 
				text-align: center; 
				position: relative;
			}
			.signature-line { 
				border-bottom: 1px solid #495057; 
				height: 30px; 
				margin-bottom: 8px;
				position: relative;
			}
			.signature-line::before {
				content: '';
				position: absolute;
				top: 50%;
				left: 50%;
				transform: translate(-50%, -50%);
				width: 20px;
				height: 20px;
				border: 2px dashed #ccc;
				border-radius: 50%;
				opacity: 0.3;
			}
			.signature-label { 
				margin: 0; 
				font-size: 10px; 
				color: #495057; 
				font-weight: 600;
				text-transform: uppercase;
				letter-spacing: 0.5px;
			}
			.remarks-section {
				background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
				padding: 10px;
				border-radius: 8px;
				margin-bottom: 15px;
				border-left: 3px solid #ffc107;
			}
			.remarks-section h3 {
				margin: 0 0 5px 0;
				color: #856404;
				font-size: 12px;
			}
			.remarks-section p {
				margin: 0;
				font-size: 10px;
			}
			@media print {
				body { background: white; font-size: 10px; }
				.voucher-container { box-shadow: none; margin: 0; }
				.voucher-header::before { display: none; }
				.voucher-header { padding: 15px 10px; }
				.voucher-header h1 { font-size: 20px; }
				.voucher-body { padding: 15px 10px; }
				.voucher-info { gap: 15px; }
				.info-section { padding: 10px; }
				.payment-details { padding: 10px; }
				.signature-section { padding: 10px; margin-top: 15px; }
				.signature-line { height: 25px; }
				.voucher-footer { padding: 10px; }
			}
		</style>
	</head>
	<body>
		<?php
		$watermark_text = 'paid' === $fee->status ? 'PAID' : ( 'partially_paid' === $fee->status ? 'PARTIALLY PAID' : 'PENDING' );
		$watermark_class = 'paid' === $fee->status ? 'paid' : ( 'partially_paid' === $fee->status ? 'partially-paid' : 'pending' );
		?>
		<div class="voucher-container">
			<div class="watermark <?php echo esc_attr( $watermark_class ); ?>"><?php echo esc_html( $watermark_text ); ?></div>
			<div class="voucher-header">
				<div class="header-inner">
					<?php if ( ! empty( $school_logo ) ) : ?>
						<img class="voucher-logo" src="<?php echo esc_url( $school_logo ); ?>" alt="<?php echo esc_attr( $school_name ); ?>">
					<?php endif; ?>
					<h1><?php echo esc_html( $school_name ); ?></h1>
				</div>
			</div>
			
			<div class="voucher-body">
				<div class="voucher-number">
					<?php printf( esc_html__( 'Voucher No: %s', 'school-management-system' ), 'FEE-' . str_pad( $fee->id, 6, '0', STR_PAD_LEFT ) ); ?>
				</div>

				<div class="voucher-info">
					<div class="info-section">
						<h3><?php esc_html_e( 'Student Information', 'school-management-system' ); ?></h3>
						<div class="info-row">
							<span class="info-label"><?php esc_html_e( 'Name:', 'school-management-system' ); ?></span>
							<span class="info-value"><?php echo esc_html( $student->first_name . ' ' . $student->last_name ); ?></span>
						</div>
						<div class="info-row">
							<span class="info-label"><?php esc_html_e( 'Roll Number:', 'school-management-system' ); ?></span>
							<span class="info-value"><?php echo esc_html( $student->roll_number ); ?></span>
						</div>
						<div class="info-row">
							<span class="info-label"><?php esc_html_e( 'Class:', 'school-management-system' ); ?></span>
							<span class="info-value"><?php echo esc_html( $class->class_name ); ?></span>
						</div>
					</div>

					<div class="info-section">
						<h3><?php esc_html_e( 'Payment Information', 'school-management-system' ); ?></h3>
						<div class="info-row">
							<span class="info-label"><?php esc_html_e( 'Fee Type:', 'school-management-system' ); ?></span>
							<span class="info-value"><?php echo esc_html( $fee->fee_type ); ?></span>
						</div>
						<div class="info-row">
							<span class="info-label"><?php esc_html_e( 'Due Date:', 'school-management-system' ); ?></span>
							<span class="info-value"><?php echo esc_html( $fee->due_date ); ?></span>
						</div>
						<div class="info-row">
							<span class="info-label"><?php esc_html_e( 'Payment Date:', 'school-management-system' ); ?></span>
							<span class="info-value"><?php echo esc_html( $fee->payment_date ); ?></span>
						</div>
						<div class="info-row">
							<span class="info-label"><?php esc_html_e( 'Status:', 'school-management-system' ); ?></span>
							<span class="info-value <?php echo 'paid' === $fee->status ? 'status-paid' : 'status-partial'; ?>">
								<?php echo esc_html( ucfirst( str_replace( '_', ' ', $fee->status ) ) ); ?>
							</span>
						</div>
					</div>
				</div>

				<div class="payment-details">
					<h3><?php esc_html_e( 'Payment Breakdown', 'school-management-system' ); ?></h3>
					<div class="payment-row">
						<span><?php esc_html_e( 'Total Amount:', 'school-management-system' ); ?></span>
						<span><?php echo esc_html( $currency . ' ' . number_format( $fee->amount, 2 ) ); ?></span>
					</div>
					<div class="payment-row">
						<span><?php esc_html_e( 'Amount Paid:', 'school-management-system' ); ?></span>
						<span><?php echo esc_html( $currency . ' ' . number_format( $fee->paid_amount, 2 ) ); ?></span>
					</div>
					<?php if ( $due_amount > 0 ) : ?>
					<div class="payment-row">
						<span><?php esc_html_e( 'Due Amount:', 'school-management-system' ); ?></span>
						<span><?php echo esc_html( $currency . ' ' . number_format( $due_amount, 2 ) ); ?></span>
					</div>
					<?php endif; ?>
					<div class="payment-row total">
						<span><?php esc_html_e( 'Total Received:', 'school-management-system' ); ?></span>
						<span><?php echo esc_html( $currency . ' ' . number_format( $fee->paid_amount, 2 ) ); ?></span>
					</div>
				</div>

				<?php if ( ! empty( $fee->remarks ) ) : ?>
				<div class="remarks-section">
					<h3>📝 <?php esc_html_e( 'Remarks', 'school-management-system' ); ?></h3>
					<p><?php echo esc_html( $fee->remarks ); ?></p>
				</div>
				<?php endif; ?>
			</div>

			<div class="voucher-footer">
				<p><strong><?php echo esc_html( $school_name ); ?></strong></p>
				<?php if ( ! empty( $school_address ) ) : ?>
					<p><?php echo esc_html( $school_address ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $school_phone ) ) : ?>
					<p><?php esc_html_e( 'Phone:', 'school-management-system' ); ?> <?php echo esc_html( $school_phone ); ?></p>
				<?php endif; ?>
				<p><small><?php printf( esc_html__( 'Generated on: %s', 'school-management-system' ), date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) ); ?></small></p>
			</div>
			
			<div class="signature-section">
				<div class="signature-row">
					<div class="signature-box">
						<div class="signature-line"></div>
						<p class="signature-label"><?php esc_html_e( 'Authorized sign', 'school-management-system' ); ?></p>
					</div>
					<div class="signature-box">
						<div class="signature-line"></div>
						<p class="signature-label"><?php esc_html_e( 'Student sign', 'school-management-system' ); ?></p>
					</div>
				</div>
			</div>
		</div>
	</body>
	</html>
	<?php
	return ob_get_clean();
}

/**
 * Create voucher PDF file.
 */
function create_voucher_pdf( $html, $filepath ) {
	try {
		// For now, let's create a reliable HTML file instead of trying to generate PDF
		// This avoids the complex PDF generation issues
		$html_filepath = str_replace( '.pdf', '.html', $filepath );
		
		// Create a clean, print-friendly HTML file
		$print_html = create_printable_voucher_html( $html );
		
		// Save the HTML file
		$result = file_put_contents( $html_filepath, $print_html );
		
		if ( $result ) {
			// Try to create a simple PDF if possible, but don't fail if we can't
			$pdf_result = create_simple_pdf_fallback( $print_html, $filepath );
			
			// Return true if either HTML was created successfully
			return true;
		}
		
		return false;
		
	} catch ( Exception $e ) {
		error_log( 'Voucher PDF Creation Error: ' . $e->getMessage() );
		// Fallback: try to save as HTML
		$html_filepath = str_replace( '.pdf', '.html', $filepath );
		return file_put_contents( $html_filepath, $html ) !== false;
	}
}

/**
 * Create a printable HTML voucher.
 */
function create_printable_voucher_html( $html ) {
	// Extract the body content from the original HTML
	$dom = new DOMDocument();
	@$dom->loadHTML($html);
	$body = $dom->getElementsByTagName('body')->item(0);
	$body_content = $dom->saveHTML($body);
	
	// Create clean HTML for printing
	$body_content = preg_replace('/<body[^>]*>/', '', $body_content);
	$body_content = preg_replace('/<\/body>/', '', $body_content);
	
	// Build print-friendly HTML with simplified CSS
	$print_html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Voucher</title>
    <style>
        @page { 
            size: A4; 
            margin: 15mm; 
        }
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 20px; 
            background: white; 
            color: black;
        }
        .voucher-container { 
            max-width: 100%; 
            margin: 0 auto; 
            background: white; 
            border: 2px solid #333; 
            padding: 20px; 
            box-sizing: border-box;
        }
        .voucher-header { 
            text-align: center; 
            border-bottom: 3px double #333; 
            padding-bottom: 20px; 
            margin-bottom: 20px; 
        }
        .voucher-header h1 { 
            margin: 0; 
            font-size: 24px; 
            font-weight: bold; 
            color: #333; 
        }
        .voucher-header p { 
            margin: 5px 0 0 0; 
            font-size: 14px; 
            color: #666; 
        }
        .voucher-number { 
            background: #f0f0f0; 
            padding: 10px; 
            text-align: center; 
            font-weight: bold; 
            margin-bottom: 20px; 
            border: 1px solid #ccc; 
            font-size: 14px;
        }
        .voucher-info { 
            display: table; 
            width: 100%; 
            margin-bottom: 20px; 
            border-collapse: collapse;
        }
        .info-row { 
            display: table-row; 
        }
        .info-label, .info-value { 
            display: table-cell; 
            padding: 8px 5px; 
            border: 1px solid #ddd; 
            font-size: 12px;
            vertical-align: top;
        }
        .info-label { 
            font-weight: bold; 
            width: 30%; 
            background: #f9f9f9; 
        }
        .info-value { 
            width: 70%; 
        }
        .payment-details { 
            border: 2px solid #333; 
            padding: 15px; 
            margin-bottom: 20px; 
        }
        .payment-details h3 { 
            margin: 0 0 10px 0; 
            font-size: 16px; 
            font-weight: bold; 
            text-align: center; 
        }
        .payment-row { 
            display: flex; 
            justify-content: space-between; 
            margin-bottom: 5px; 
            padding: 5px 0; 
            font-size: 12px; 
            border-bottom: 1px solid #eee;
        }
        .payment-row:last-child {
            border-bottom: none;
        }
        .payment-row.total { 
            border-top: 2px solid #333; 
            font-weight: bold; 
            font-size: 14px; 
            padding-top: 10px; 
            margin-bottom: 0;
        }
        .voucher-footer { 
            text-align: center; 
            border-top: 1px solid #ccc; 
            padding-top: 15px; 
            margin-top: 20px; 
            font-size: 11px; 
            color: #666; 
        }
        .watermark { 
            position: fixed; 
            top: 50%; 
            left: 50%; 
            transform: translate(-50%, -50%) rotate(-45deg); 
            font-size: 120px; 
            color: rgba(0,0,0,0.1); 
            font-weight: bold; 
            z-index: -1; 
        }
        .status-paid { color: #006600; font-weight: bold; }
        .status-partial { color: #cc6600; font-weight: bold; }
        .print-instructions {
            background: #f0f8ff;
            border: 1px solid #b0d4f1;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
            text-align: center;
        }
        @media print {
            body { margin: 0; }
            .voucher-container { border: none; }
            .print-instructions { display: none; }
        }
    </style>
</head>
<body>
    <div class="watermark">PAID</div>
    <div class="voucher-container">
        ' . $body_content . '
        <div class="print-instructions">
            <h3>' . __( 'Print Instructions', 'school-management-system' ) . '</h3>
            <p>' . __( 'Press Ctrl+P (Windows/Linux) or Cmd+P (Mac) to print this voucher as PDF', 'school-management-system' ) . '</p>
        </div>
    </div>
</body>
</html>';

	return $print_html;
}

/**
 * Create a simple PDF fallback (minimal implementation).
 */
function create_simple_pdf_fallback( $html, $filepath ) {
	try {
		// For now, just return false to indicate we couldn't create PDF
		// The HTML file will be used instead
		return false;
	} catch ( Exception $e ) {
		return false;
	}
}

// Register AJAX hooks.
add_action( 'wp_ajax_sms_submit_attendance', __NAMESPACE__ . '\sms_ajax_submit_attendance' );
add_action( 'wp_ajax_nopriv_sms_submit_attendance', __NAMESPACE__ . '\sms_ajax_submit_attendance' );

add_action( 'wp_ajax_sms_enroll_student', __NAMESPACE__ . '\sms_ajax_enroll_student' );
add_action( 'wp_ajax_nopriv_sms_enroll_student', __NAMESPACE__ . '\sms_ajax_enroll_student' );

add_action( 'wp_ajax_sms_search_data', __NAMESPACE__ . '\sms_ajax_search_data' );
add_action( 'wp_ajax_nopriv_sms_search_data', __NAMESPACE__ . '\sms_ajax_search_data' );

add_action( 'wp_ajax_sms_generate_voucher', __NAMESPACE__ . '\sms_ajax_generate_voucher' );
add_action( 'wp_ajax_nopriv_sms_generate_voucher', __NAMESPACE__ . '\sms_ajax_generate_voucher' );

// Add a test endpoint for debugging
add_action( 'wp_ajax_sms_test_voucher', __NAMESPACE__ . '\sms_ajax_test_voucher' );
function sms_ajax_test_voucher() {
	try {
		// Test basic functionality
		$test_data = array(
			'timestamp' => current_time( 'mysql' ),
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'upload_dir' => wp_upload_dir(),
			'php_version' => PHP_VERSION,
			'wp_version' => get_bloginfo( 'version' ),
			'memory_limit' => ini_get( 'memory_limit' ),
			'max_execution_time' => ini_get( 'max_execution_time' ),
			'fileinfo' => extension_loaded( 'fileinfo' ),
			'vouchers_dir' => wp_upload_dir()['basedir'] . '/school-vouchers/',
			'vouchers_dir_exists' => file_exists( wp_upload_dir()['basedir'] . '/school-vouchers/' ),
			'vouchers_dir_writable' => is_writable( wp_upload_dir()['basedir'] . '/school-vouchers/' ) ?: 'Directory not found'
		);
		
		wp_send_json_success( $test_data );
	} catch ( Exception $e ) {
		wp_send_json_error( 'Test failed: ' . $e->getMessage() );
	}
}
