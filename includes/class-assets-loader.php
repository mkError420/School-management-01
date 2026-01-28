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

		// Enqueue admin stylesheet.
		wp_enqueue_style(
			'sms-admin-style',
			SMS_PLUGIN_URL . 'public/css/admin-style.css',
			array(),
			SMS_VERSION
		);

		// Enqueue Select2.
		wp_enqueue_style( 'select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), '4.1.0' );

		// Enqueue admin JavaScript.
		wp_enqueue_script(
			'sms-admin-script',
			SMS_PLUGIN_URL . 'public/js/admin-script.js',
			array( 'jquery', 'wp-api' ),
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
