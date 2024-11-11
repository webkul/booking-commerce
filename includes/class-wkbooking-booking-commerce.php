<?php
/**
 * Main final class file.
 *
 * @version 1.1.0
 *
 * @package Booking Commerce
 */

namespace WKBOOKING\Includes;

defined( 'ABSPATH' ) || exit(); // Exit if accessed directly.

use WKBOOKING\Includes\Admin;

/**
 * WKBC Booking Commerce Main Class.
 */
class WKBOOKING_Booking_Commerce {
	/**
	 * Instance variable.
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * Constructor function.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function __construct() {
		$this->wkbooking_init_hooks();
		$this->wkbooking_define_constants();
	}

	/**
	 * Defining plugin's constant.
	 *
	 * @return void
	 */
	public function wkbooking_define_constants() {
		! defined( 'WKBOOKING_PLUGIN_SLUG' ) && define( 'WKBOOKING_PLUGIN_SLUG', 'booking-commerce' );
		! defined( 'WKBOOKING_PLUGIN_VERSION' ) && define( 'WKBOOKING_PLUGIN_VERSION', '1.1.0' );
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @return void
	 */
	private function wkbooking_init_hooks() {
		add_action( 'init', array( $this, 'wkbooking_load_plugin' ) );
	}

	/**
	 * Load message indication plugin.
	 *
	 * @return void
	 */
	public function wkbooking_load_plugin() {
		load_plugin_textdomain( 'booking-commerce', false, basename( __DIR__ ) . '/languages' );
		new Admin\WKBOOKING_Admin_Hook_Handler();
	}

	/**
	 * This is a singleton page, access the single instance just using this method.
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
