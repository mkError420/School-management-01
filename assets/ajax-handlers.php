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

// Register AJAX hooks.
add_action( 'wp_ajax_sms_submit_attendance', __NAMESPACE__ . '\sms_ajax_submit_attendance' );
add_action( 'wp_ajax_nopriv_sms_submit_attendance', __NAMESPACE__ . '\sms_ajax_submit_attendance' );

add_action( 'wp_ajax_sms_enroll_student', __NAMESPACE__ . '\sms_ajax_enroll_student' );
add_action( 'wp_ajax_nopriv_sms_enroll_student', __NAMESPACE__ . '\sms_ajax_enroll_student' );

add_action( 'wp_ajax_sms_search_data', __NAMESPACE__ . '\sms_ajax_search_data' );
add_action( 'wp_ajax_nopriv_sms_search_data', __NAMESPACE__ . '\sms_ajax_search_data' );
