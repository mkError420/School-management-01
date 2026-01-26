<?php
/**
 * Fee CRUD class.
 *
 * @package School_Management_System
 */

namespace School_Management_System;

/**
 * Fee CRUD class
 */
class Fee {

	/**
	 * Add a new fee record.
	 *
	 * @param array $fee_data Fee data.
	 * @return int|false Fee ID on success, false on failure.
	 */
	public static function add( $fee_data ) {
		if ( empty( $fee_data['student_id'] ) || empty( $fee_data['class_id'] ) || empty( $fee_data['fee_type'] ) || empty( $fee_data['amount'] ) ) {
			return false;
		}

		if ( empty( $fee_data['status'] ) ) {
			$fee_data['status'] = 'pending';
		}

		return Database::insert( 'fees', $fee_data );
	}

	/**
	 * Get fee record by ID.
	 *
	 * @param int $fee_id Fee ID.
	 * @return object|null Fee object or null if not found.
	 */
	public static function get( $fee_id ) {
		return Database::get_row( 'fees', array( 'id' => $fee_id ) );
	}

	/**
	 * Get all fee records.
	 *
	 * @param array $filters Filter parameters.
	 * @param int   $limit   Number of records per page.
	 * @param int   $offset  Number of records to skip.
	 * @return array Array of fee objects.
	 */
	public static function get_all( $filters = array(), $limit = 10, $offset = 0 ) {
		return Database::get_results( 'fees', $filters, 'id', 'DESC', $limit, $offset );
	}

	/**
	 * Update fee record.
	 *
	 * @param int   $fee_id Fee ID.
	 * @param array $fee_data Updated fee data.
	 * @return int|false Number of rows updated or false on failure.
	 */
	public static function update( $fee_id, $fee_data ) {
		if ( empty( $fee_id ) ) {
			return false;
		}

		return Database::update( 'fees', $fee_data, array( 'id' => $fee_id ) );
	}

	/**
	 * Delete fee record.
	 *
	 * @param int $fee_id Fee ID.
	 * @return int|false Number of rows deleted or false on failure.
	 */
	public static function delete( $fee_id ) {
		if ( empty( $fee_id ) ) {
			return false;
		}

		return Database::delete( 'fees', array( 'id' => $fee_id ) );
	}

	/**
	 * Count total fee records.
	 *
	 * @param array $filters Filter parameters.
	 * @return int Total number of records.
	 */
	public static function count( $filters = array() ) {
		return Database::count( 'fees', $filters );
	}

	/**
	 * Get fees for a student.
	 *
	 * @param int $student_id Student ID.
	 * @return array Array of fee records.
	 */
	public static function get_student_fees( $student_id ) {
		return Database::get_results( 'fees', array( 'student_id' => $student_id ), 'id', 'DESC' );
	}

	/**
	 * Get pending fees for a student.
	 *
	 * @param int $student_id Student ID.
	 * @return array Array of pending fee records.
	 */
	public static function get_pending_fees( $student_id ) {
		return Database::get_results( 'fees', array( 'student_id' => $student_id, 'status' => 'pending' ), 'due_date', 'ASC' );
	}

	/**
	 * Calculate total fees for a student.
	 *
	 * @param int $student_id Student ID.
	 * @return float Total fees.
	 */
	public static function get_total_fees( $student_id ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'sms_fees';

		$total = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT SUM(amount) FROM $table_name WHERE student_id = %d",
				$student_id
			)
		);

		return floatval( $total ?? 0 );
	}

	/**
	 * Calculate paid fees for a student.
	 *
	 * @param int $student_id Student ID.
	 * @return float Paid fees.
	 */
	public static function get_paid_fees( $student_id ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'sms_fees';

		$total = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT SUM(amount) FROM $table_name WHERE student_id = %d AND status = 'paid'",
				$student_id
			)
		);

		return floatval( $total ?? 0 );
	}

	/**
	 * Mark fee as paid.
	 *
	 * @param int    $fee_id Fee ID.
	 * @param string $payment_date Payment date.
	 * @return int|false Number of rows updated or false on failure.
	 */
	public static function mark_paid( $fee_id, $payment_date = null ) {
		if ( ! $payment_date ) {
			$payment_date = current_time( 'Y-m-d' );
		}

		return self::update( $fee_id, array( 'status' => 'paid', 'payment_date' => $payment_date ) );
	}
}
