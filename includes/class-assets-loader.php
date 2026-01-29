<?php
/**
 * Assets Loader class for enqueuing CSS and JS files.
 *
 * @package School_Management_System
 */

namespace School_Management_System;

/**
 * Assets Loader class
 */
class Assets_Loader {

	/**
	 * Enqueue admin scripts and styles.
	 *
	 * @param string $hook_suffix The current admin page hook suffix.
	 */
	public function enqueue_admin_scripts( $hook_suffix ) {
		// Only enqueue on SMS pages.
		if ( strpos( $hook_suffix, 'sms-' ) === false ) {
			return;
		}

		$script_dependencies = array( 'jquery', 'wp-api' );

		// For settings page, enqueue media uploader scripts.
		if ( 'school-management_page_sms-settings' === $hook_suffix ) {
			wp_enqueue_media();
			$script_dependencies[] = 'media-editor';
		}

		// Enqueue admin stylesheet.
		wp_enqueue_style(
			'sms-admin-style',
			SMS_PLUGIN_URL . 'public/css/admin-style.css',
			array(),
			SMS_VERSION
		);

		$custom_css = "
			/* General Styles */
			.sms-wrap h1, .sms-wrap h2 {
				color: #2c3e50;
			}

			/* List Tables */
			.wp-list-table {
				border-radius: 8px;
				box-shadow: 0 2px 10px rgba(0,0,0,0.05);
				border: 1px solid #e0e0e0;
				overflow: hidden;
				background: #fff;
			}
			.wp-list-table thead th {
				background: #f8f9fa;
				border-bottom: 2px solid #e0e0e0 !important;
				font-weight: 600;
				color: #3c434a;
			}
			.wp-list-table tbody tr:nth-child(even) {
				background: #fdfdfd;
			}
			.wp-list-table tbody tr:hover {
				background: #f1f6ff;
				color: #222;
			}
			.wp-list-table td, .wp-list-table th {
				padding: 15px 12px;
				vertical-align: middle;
			}
			.wp-list-table .row-actions {
				color: #999;
				font-size: 13px;
			}
			.wp-list-table .row-actions .edit a,
			.wp-list-table .row-actions .view a {
				color: #3498db;
			}
			.wp-list-table .row-actions .delete a,
			.wp-list-table .row-actions .trash a {
				color: #e74c3c;
			}

			/* Forms & Postboxes */
			.sms-form-wrap .postbox, #dashboard-widgets .postbox {
				border-radius: 8px !important;
				box-shadow: 0 5px 15px rgba(0,0,0,0.05) !important;
				border: 1px solid #e0e0e0 !important;
			}
			.sms-form-wrap .postbox .hndle, #dashboard-widgets .postbox .hndle {
				background: #f7f9fc !important;
				border-bottom: 1px solid #e0e0e0 !important;
				font-size: 16px !important;
				font-weight: 600 !important;
				padding: 15px 20px !important;
				border-top-left-radius: 8px;
				border-top-right-radius: 8px;
			}

			/* Buttons */
			.page-title-action, .button-primary {
				background: #3498db !important;
				border-color: #2980b9 !important;
				box-shadow: none !important;
				text-shadow: none !important;
				border-radius: 5px !important;
				transition: background 0.2s;
				padding: 8px 16px !important;
				height: auto !important;
				font-size: 14px;
				font-weight: 600;
			}
			.page-title-action:hover, .button-primary:hover {
				background: #2980b9 !important;
			}

			/* Adding icons to page titles */
			.wrap > h1 { display: flex; align-items: center; gap: 10px; font-size: 23px; font-weight: 600; color: #2c3e50; }
			.wrap > h1::before { font-family: dashicons; font-size: 30px; color: #3498db; }
			
			/* Unique Page Styles */
			.school-management_page_sms-students .wrap > h1::before { content: '\\f307'; }
			.school-management_page_sms-students .wp-list-table { border-top: 4px solid #3498db; }
			
			.school-management_page_sms-teachers .wrap > h1::before { content: '\\f338'; }
			.school-management_page_sms-teachers .wp-list-table { border-top: 4px solid #e67e22; }
			.school-management_page_sms-teachers .wrap > h1::before { color: #e67e22; }
			
			.school-management_page_sms-classes .wrap > h1::before { content: '\\f331'; }
			.school-management_page_sms-classes .wp-list-table { border-top: 4px solid #2ecc71; }
			.school-management_page_sms-classes .wrap > h1::before { color: #2ecc71; }
			
			.school-management_page_sms-subjects .wrap > h1::before { content: '\\f108'; }
			.school-management_page_sms-subjects .wp-list-table { border-top: 4px solid #9b59b6; }
			.school-management_page_sms-subjects .wrap > h1::before { color: #9b59b6; }
			
			.school-management_page_sms-enrollments .wrap > h1::before { content: '\\f110'; }
			.school-management_page_sms-enrollments .wp-list-table { border-top: 4px solid #1abc9c; }
			.school-management_page_sms-enrollments .wrap > h1::before { color: #1abc9c; }
			
			.school-management_page_sms-attendance .wrap > h1::before { content: '\\f522'; }
			
			.school-management_page_sms-student-attendance .wrap > h1::before { content: '\\f145'; }
			
			.school-management_page_sms-exams .wrap > h1::before { content: '\\f473'; }
			.school-management_page_sms-exams .wp-list-table { border-top: 4px solid #e74c3c; }
			.school-management_page_sms-exams .wrap > h1::before { color: #e74c3c; }
			
			.school-management_page_sms-results .wrap > h1::before { content: '\\f158'; }
			.school-management_page_sms-results .wp-list-table { border-top: 4px solid #f1c40f; }
			.school-management_page_sms-results .wrap > h1::before { color: #f1c40f; }

			/* Dashboard Notice Files List */
			#dashboard-widgets .postbox .inside ul li {
				background: #fcfcfc; border: 1px solid #f0f0f0; margin-bottom: 8px; border-radius: 6px; padding: 10px 15px; transition: all 0.2s;
			}
			#dashboard-widgets .postbox .inside ul li:hover { background: #fff; border-color: #3498db; box-shadow: 0 2px 5px rgba(0,0,0,0.05); transform: translateX(2px); }
		";
		wp_add_inline_style( 'sms-admin-style', $custom_css );

		// Enqueue Select2.
		wp_enqueue_style( 'select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), '4.1.0' );

		// Enqueue admin JavaScript.
		wp_enqueue_script(
			'sms-admin-script',
			SMS_PLUGIN_URL . 'public/js/admin-script.js',
			$script_dependencies,
			SMS_VERSION,
			true
		);

		// Enqueue Select2 JS.
		wp_enqueue_script( 'select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array( 'jquery' ), '4.1.0', true );

		// Localize script with AJAX URL and nonce.
		wp_localize_script(
			'sms-admin-script',
			'smsAdmin',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'sms_admin_nonce' ),
			)
		);
	}

	/**
	 * Enqueue frontend scripts and styles.
	 */
	public function enqueue_frontend_scripts() {
		// Enqueue frontend stylesheet.
		wp_enqueue_style(
			'sms-frontend-style',
			SMS_PLUGIN_URL . 'public/css/style.css',
			array(),
			SMS_VERSION
		);

		// Enqueue frontend JavaScript.
		wp_enqueue_script(
			'sms-frontend-script',
			SMS_PLUGIN_URL . 'public/js/script.js',
			array( 'jquery' ),
			SMS_VERSION,
			true
		);

		// Localize script with AJAX URL and nonce.
		wp_localize_script(
			'sms-frontend-script',
			'smsFrontend',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'sms_frontend_nonce' ),
			)
		);
	}
}
