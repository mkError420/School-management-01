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

	// Logo uploader for settings page.
	if ($('#upload_logo_button').length) {
		var image_frame;

		$('#upload_logo_button').on('click', function (e) {
			e.preventDefault();

			if (image_frame) {
				image_frame.open();
				return;
			}

			image_frame = wp.media({
				title: 'Select or Upload Logo',
				multiple: false,
				library: {
					type: 'image',
				},
			});

			image_frame.on('select', function () {
				var media_attachment = image_frame.state().get('selection').first().toJSON();
				$('#school_logo').val(media_attachment.url);
				$('#logo-preview').html('<img src="' + media_attachment.url + '" style="max-height: 100px; border: 1px solid #ddd; padding: 5px;" />');
			});

			image_frame.open();
		});
	}

	// Select All checkbox for bulk actions
	$('#cb-select-all-1').on('click', function() {
		var isChecked = $(this).prop('checked');
		$('input[name="student_ids[]"]').prop('checked', isChecked);
	});

	// Select All checkbox for bulk actions (Classes)
	$('#cb-select-all-classes').on('click', function() {
		var isChecked = $(this).prop('checked');
		$('input[name="class_ids[]"]').prop('checked', isChecked);
	});

	// Select All checkbox for bulk actions (Teachers)
	$('#cb-select-all-teachers').on('click', function() {
		var isChecked = $(this).prop('checked');
		$('input[name="teacher_ids[]"]').prop('checked', isChecked);
	});

	// Select All checkbox for bulk actions (Subjects)
	$('#cb-select-all-subjects').on('click', function() {
		var isChecked = $(this).prop('checked');
		$('input[name="subject_ids[]"]').prop('checked', isChecked);
	});

	// Select All checkbox for bulk actions (Enrollments)
	$('#cb-select-all-enrollments').on('click', function() {
		var isChecked = $(this).prop('checked');
		$('input[name="enrollment_ids[]"]').prop('checked', isChecked);
	});
});
