<?php
/**
 * Admin Hook Handler File.
 *
 * @version 1.1.0
 *
 * @package Booking Commerce
 */

namespace WKBOOKING\Includes\Admin;

defined( 'ABSPATH' ) || exit(); // Direct access is not allowed.

/**
 * Admin Hook Handler Class.
 *
 * @version 1.1.0
 */
class WKBOOKING_Admin_Hook_Handler {
	/**
	 * Instance variable
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * Class constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		$function_handler = new WKBOOKING_Admin_Function_Handler();
		// Action Hooks For Admin.
		add_action( 'admin_enqueue_scripts', array( $function_handler, 'wkbooking_enqueue_admin_script' ) );
		add_action( 'admin_menu', array( $function_handler, 'wkbooking_menu_item' ) );
		add_action( 'plugin_action_links_' . WKBOOKING_PLUGIN_FILE, array( $function_handler, 'plugin_settings_link' ) );
		add_action( 'admin_init', array( $function_handler, 'wkbooking_register_booking_link' ), 0 );

		// Hook to render the General Settings tab.
		add_action( 'wkbooking_render_settings_general', array( $function_handler, 'wkbooking_render_general_settings' ) );

		// Hook to render the Advanced Settings tab.
		add_action( 'wkbooking_render_settings_services', array( $function_handler, 'wkbooking_render_settings_services' ) );
		add_action( 'wkbooking_render_settings_extensions', array( $function_handler, 'wkbooking_render_settings_extensions' ) );

		// Hook into the admin_post action to handle the form submission.
		add_action( 'admin_post_save_booking_link', array( $function_handler, 'wkbooking_handle_form_submission' ) );

		// Add settings for admin plugin settings.
		add_filter( 'plugin_action_links_' . WKBOOKING_BASE_NAME, array( $function_handler, 'wkbooking_add_plugin_setting_links' ) );
		add_filter( 'plugin_row_meta', array( $function_handler, 'wkbooking_plugin_row_meta' ), 10, 2 );

		if ( ! empty( get_option( 'wkbooking_selected_pages', array() ) ) ) {
			// Hook into 'the_content' to append the shortcode to selected pages.
			add_filter( 'the_content', array( $function_handler, 'wkbooking_auto_apply_shortcode' ) );
		}
	}

	/**
	 * Ensures only one instance of this class is loaded or can be loaded.
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( ! static::$instance ) {
			static::$instance = new self();
		}
		return static::$instance;
	}
}
