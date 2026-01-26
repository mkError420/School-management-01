<?php
/**
 * Result CRUD class.
 *
 * @package School_Management_System
 */

namespace School_Management_System;

/**
 * Result CRUD class
 */
class Result {

	/**
	 * Add a new result.
	 *
	 * @param array $result_data Result data.
	 * @return int|false Result ID on success, false on failure.
	 */
	public static function add( $result_data ) {
		if ( empty( $result_data['student_id'] ) || empty( $result_data['exam_id'] ) || empty( $result_data['obtained_marks'] ) ) {
			return false;
		}

		// Calculate percentage and grade.
		$exam = Exam::get( $result_data['exam_id'] );
		if ( ! $exam ) {
			return false;
		}

		$percentage = ( $result_data['obtained_marks'] / $exam->total_marks ) * 100;
		$grade = self::calculate_grade( $percentage, $exam->passing_marks );

		$result_data['percentage'] = $percentage;
		$result_data['grade'] = $grade;

		return Database::insert( 'results', $result_data );
	}

	/**
	 * Get result by ID.
	 *
	 * @param int $result_id Result ID.
	 * @return object|null Result object or null if not found.
	 */
	public static function get( $result_id ) {
		return Database::get_row( 'results', array( 'id' => $result_id ) );
	}

	/**
	 * Get all results.
	 *
	 * @param array $filters Filter parameters.
	 * @param int   $limit   Number of records per page.
	 * @param int   $offset  Number of records to skip.
	 * @return array Array of result objects.
	 */
	public static function get_all( $filters = array(), $limit = 10, $offset = 0 ) {
		return Database::get_results( 'results', $filters, 'id', 'DESC', $limit, $offset );
	}

	/**
	 * Update result.
	 *
	 * @param int   $result_id Result ID.
	 * @param array $result_data Updated result data.
	 * @return int|false Number of rows updated or false on failure.
	 */
	public static function update( $result_id, $result_data ) {
		if ( empty( $result_id ) ) {
			return false;
		}

		// Recalculate percentage and grade if marks changed.
		if ( ! empty( $result_data['obtained_marks'] ) ) {
			$result = self::get( $result_id );
			if ( $result ) {
				$exam = Exam::get( $result->exam_id );
				if ( $exam ) {
					$percentage = ( $result_data['obtained_marks'] / $exam->total_marks ) * 100;
					$grade = self::calculate_grade( $percentage, $exam->passing_marks );
					$result_data['percentage'] = $percentage;
					$result_data['grade'] = $grade;
				}
			}
		}

		return Database::update( 'results', $result_data, array( 'id' => $result_id ) );
	}

	/**
	 * Delete result.
	 *
	 * @param int $result_id Result ID.
	 * @return int|false Number of rows deleted or false on failure.
	 */
	public static function delete( $result_id ) {
		if ( empty( $result_id ) ) {
			return false;
		}

		return Database::delete( 'results', array( 'id' => $result_id ) );
	}

	/**
	 * Count total results.
	 *
	 * @param array $filters Filter parameters.
	 * @return int Total number of results.
	 */
	public static function count( $filters = array() ) {
		return Database::count( 'results', $filters );
	}

	/**
	 * Get results for a student.
	 *
	 * @param int $student_id Student ID.
	 * @return array Array of result objects.
	 */
	public static function get_student_results( $student_id ) {
		return Database::get_results( 'results', array( 'student_id' => $student_id ), 'id', 'DESC' );
	}

	/**
	 * Get results for an exam.
	 *
	 * @param int $exam_id Exam ID.
	 * @return array Array of result objects.
	 */
	public static function get_exam_results( $exam_id ) {
		return Database::get_results( 'results', array( 'exam_id' => $exam_id ), 'obtained_marks', 'DESC' );
	}

	/**
	 * Calculate grade based on percentage.
	 *
	 * @param float $percentage Percentage.
	 * @param float $passing_marks Passing marks.
	 * @return string Grade.
	 */
	private static function calculate_grade( $percentage, $passing_marks ) {
		if ( $percentage < $passing_marks ) {
			return 'F';
		} elseif ( $percentage >= 80 ) {
			return 'A+';
		} elseif ( $percentage >= 70 ) {
			return 'A';
		} elseif ( $percentage >= 60 ) {
			return 'B';
		} elseif ( $percentage >= 50 ) {
			return 'C';
		} else {
			return 'D';
		}
	}

	/**
	 * Get student's average marks in an exam.
	 *
	 * @param int $exam_id Exam ID.
	 * @return float Average marks.
	 */
	public static function get_exam_average( $exam_id ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'sms_results';

		$average = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT AVG(obtained_marks) FROM $table_name WHERE exam_id = %d",
				$exam_id
			)
		);

		return floatval( $average ?? 0 );
	}
}
