<?php
/**
 * Plugin Name: Booking Commerce
 * Plugin URI: https://wordpress.org/plugins/booking-commerce/
 * Description: The "Booking Commerce Plugin" adds a booking widget to WordPress, letting admins choose display pages for seamless booking portal integration.
 * Version: 1.1.0
 * Author: Webkul
 * Author URI: https://webkul.com
 * Text Domain: booking-commerce
 * Domain Path: /languages
 *
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Requires at least: 6.5
 * Tested up to: 6.7
 * Requires PHP: 7.4
 * Tested up to PHP: 8.3
 *
 * Store URI: https://wordpress.org/plugins/booking-commerce/
 * Blog URI: https://webkul.com/blog/wordpress-booking-plugin/
 *
 * @package Booking Commerce
 */

defined( 'ABSPATH' ) || exit(); // Direct access is not allowed.

use WKBOOKING\Includes;

/** Define constant variables */
! defined( 'WKBOOKING_PLUGIN_FILE' ) && define( 'WKBOOKING_PLUGIN_FILE', plugin_dir_path( __FILE__ ) );
! defined( 'WKBOOKING_PLUGIN_URL' ) && define( 'WKBOOKING_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
! defined( 'WKBOOKING_BASE_NAME' ) && define( 'WKBOOKING_BASE_NAME', plugin_basename( __FILE__ ) );

/** Check function exist. */
if ( ! function_exists( 'wkbooking_native_includes' ) ) {
	/**
	 * Function to include necessary files and initialize the plugin.
	 *
	 * This function is responsible for loading the plugin's text domain,
	 * autoloading classes, and initializing the main file handler.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	function wkbooking_native_includes() {
		// Load core auto-loader.
		require WKBOOKING_PLUGIN_FILE . '/autoloader/class-wkbooking-autoload.php';

		// Initialize the main final file handler.
		new Includes\WKBOOKING_Booking_Commerce();

		// Ensure the widget class is loaded.
		if ( class_exists( 'WKBOOKING\Includes\WKBOOKING_Commerce_Widget' ) ) {
			// Hook the widget registration method to the widgets_init action .
			add_action( 'widgets_init', array( Includes\Admin\WKBOOKING_Admin_Function_Handler::get_instance(), 'wkbooking_register_widgets' ) );
		}
	}
	// Hook the function to the 'plugins_loaded' action to ensure it runs after WordPress is fully loaded.
	add_action( 'plugins_loaded', 'wkbooking_native_includes' );
}

/**
 * Function to delete all plugin options and settings.
 *
 * @return void
 */
function wkbooking_delete_plugin_data() {
	// Delete all options or settings associated with this plugin.
	delete_option( 'wkbooking_link' );
	delete_option( 'wkbooking_selected_pages' );
}

// Register uninstall hook.
register_uninstall_hook( __FILE__, 'wkbooking_delete_plugin_data' );
