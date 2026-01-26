<?php
/**
 * Enrollment CRUD class.
 *
 * @package School_Management_System
 */

namespace School_Management_System;

/**
 * Enrollment CRUD class
 */
class Enrollment {

	/**
	 * Add a new enrollment.
	 *
	 * @param array $enrollment_data Enrollment data.
	 * @return int|false Enrollment ID on success, false on failure.
	 */
	public static function add( $enrollment_data ) {
		if ( empty( $enrollment_data['student_id'] ) || empty( $enrollment_data['class_id'] ) ) {
			return new \WP_Error( 'missing_fields', __( 'Student and Class are required.', 'school-management-system' ) );
		}

		if ( self::is_enrolled( $enrollment_data['student_id'], $enrollment_data['class_id'] ) ) {
			return new \WP_Error( 'duplicate_enrollment', __( 'This student is already enrolled in this class.', 'school-management-system' ) );
		}

		if ( empty( $enrollment_data['enrollment_date'] ) ) {
			$enrollment_data['enrollment_date'] = current_time( 'Y-m-d' );
		}

		if ( empty( $enrollment_data['status'] ) ) {
			$enrollment_data['status'] = 'enrolled';
		}

		return Database::insert( 'enrollments', $enrollment_data );
	}

	/**
	 * Get enrollment by ID.
	 *
	 * @param int $enrollment_id Enrollment ID.
	 * @return object|null Enrollment object or null if not found.
	 */
	public static function get( $enrollment_id ) {
		return Database::get_row( 'enrollments', array( 'id' => $enrollment_id ) );
	}

	/**
	 * Get all enrollments.
	 *
	 * @param array $filters Filter parameters.
	 * @param int   $limit   Number of records per page.
	 * @param int   $offset  Number of records to skip.
	 * @return array Array of enrollment objects.
	 */
	public static function get_all( $filters = array(), $limit = 10, $offset = 0 ) {
		return Database::get_results( 'enrollments', $filters, 'id', 'DESC', $limit, $offset );
	}

	/**
	 * Update enrollment.
	 *
	 * @param int   $enrollment_id Enrollment ID.
	 * @param array $enrollment_data Updated enrollment data.
	 * @return int|false Number of rows updated or false on failure.
	 */
	public static function update( $enrollment_id, $enrollment_data ) {
		if ( empty( $enrollment_id ) ) {
			return false;
		}

		return Database::update( 'enrollments', $enrollment_data, array( 'id' => $enrollment_id ) );
	}

	/**
	 * Delete enrollment.
	 *
	 * @param int $enrollment_id Enrollment ID.
	 * @return int|false Number of rows deleted or false on failure.
	 */
	public static function delete( $enrollment_id ) {
		if ( empty( $enrollment_id ) ) {
			return false;
		}

		return Database::delete( 'enrollments', array( 'id' => $enrollment_id ) );
	}

	/**
	 * Count total enrollments.
	 *
	 * @param array $filters Filter parameters.
	 * @return int Total number of enrollments.
	 */
	public static function count( $filters = array() ) {
		return Database::count( 'enrollments', $filters );
	}

	/**
	 * Get enrollments for a student.
	 *
	 * @param int $student_id Student ID.
	 * @return array Array of enrollment objects.
	 */
	public static function get_student_enrollments( $student_id ) {
		return Database::get_results( 'enrollments', array( 'student_id' => $student_id ) );
	}

	/**
	 * Get enrollments for a class.
	 *
	 * @param int $class_id Class ID.
	 * @return array Array of enrollment objects.
	 */
	public static function get_class_enrollments( $class_id ) {
		return Database::get_results( 'enrollments', array( 'class_id' => $class_id, 'status' => 'enrolled' ) );
	}

	/**
	 * Check if student is enrolled in class.
	 *
	 * @param int $student_id Student ID.
	 * @param int $class_id Class ID.
	 * @return bool True if enrolled, false otherwise.
	 */
	public static function is_enrolled( $student_id, $class_id ) {
		return Database::exists( 'enrollments', array( 'student_id' => $student_id, 'class_id' => $class_id, 'status' => 'enrolled' ) );
	}
}
