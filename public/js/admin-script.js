/* School Management System Admin JavaScript */

jQuery(document).ready(function ($) {
	// Delete confirmation
	$('a[data-sms-confirm]').on('click', function (e) {
		if (!confirm($(this).data('sms-confirm'))) {
			e.preventDefault();
		}
	});

	// Submit attendance via AJAX
	$('#sms-attendance-form').on('submit', function (e) {
		e.preventDefault();

		const studentId = $(this).find('input[name="student_id"]').val();
		const classId = $(this).find('input[name="class_id"]').val();
		const attendanceDate = $(this).find('input[name="attendance_date"]').val();
		const status = $(this).find('select[name="status"]').val();

		$.ajax({
			url: smsAdmin.ajaxurl,
			type: 'POST',
			data: {
				action: 'sms_submit_attendance',
				nonce: smsAdmin.nonce,
				student_id: studentId,
				class_id: classId,
				attendance_date: attendanceDate,
				status: status,
			},
			success: function (response) {
				if (response.success) {
					alert(response.data);
					location.reload();
				} else {
					alert('Error: ' + response.data);
				}
			},
			error: function () {
				alert('Failed to submit attendance');
			},
		});
	});

	// Enroll student via AJAX
	$('#sms-enrollment-form').on('submit', function (e) {
		e.preventDefault();

		const studentId = $(this).find('input[name="student_id"]').val();
		const classId = $(this).find('input[name="class_id"]').val();
		const subjectId = $(this).find('input[name="subject_id"]').val();

		$.ajax({
			url: smsAdmin.ajaxurl,
			type: 'POST',
			data: {
				action: 'sms_enroll_student',
				nonce: smsAdmin.nonce,
				student_id: studentId,
				class_id: classId,
				subject_id: subjectId,
			},
			success: function (response) {
				if (response.success) {
					alert(response.data);
					location.reload();
				} else {
					alert('Error: ' + response.data);
				}
			},
			error: function () {
				alert('Failed to enroll student');
			},
		});
	});

	// Search functionality
	$('.sms-search-form').on('submit', function (e) {
		e.preventDefault();

		const searchTerm = $(this).find('input[name="search_term"]').val();
		const type = $(this).find('input[name="type"]').val();

		$.ajax({
			url: smsAdmin.ajaxurl,
			type: 'POST',
			data: {
				action: 'sms_search_data',
				nonce: smsAdmin.nonce,
				search_term: searchTerm,
				type: type,
			},
			success: function (response) {
				if (response.success) {
					displaySearchResults(response.data);
				} else {
					alert('Error: ' + response.data);
				}
			},
			error: function () {
				alert('Search failed');
			},
		});
	});

	function displaySearchResults(results) {
		let html = '<table class="wp-list-table widefat fixed striped"><thead><tr>';
		html += '<th>ID</th><th>Name</th><th>Email</th></tr></thead><tbody>';

		results.forEach(function (result) {
			html += '<tr><td>' + result.id + '</td>';
			html += '<td>' + (result.first_name || result.subject_name || result.class_name || result.exam_name) + '</td>';
			html += '<td>' + (result.email || '-') + '</td></tr>';
		});

		html += '</tbody></table>';
		$('#sms-search-results').html(html);
	}
});
