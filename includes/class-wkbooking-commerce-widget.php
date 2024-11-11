<?php
/**
 * Widget for booking commerce.
 *
 * @version 1.1.0
 *
 * @package Booking Commerce
 */

namespace WKBOOKING\Includes;

defined( 'ABSPATH' ) || exit(); // Direct access is not allowed.

use WKBOOKING\Includes\Admin;

/**
 * Register widget for booking commerce.
 */
class WKBOOKING_Commerce_Widget extends \WP_Widget {
	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		// Instantiate the parent object.
		$widget_options = array(
			'classname'   => 'wkbooking_widget',
			'description' => esc_html__( 'This is booking widget.', 'booking-commerce' ),
		);
		parent::__construct( 'wkbooking_widget', esc_html__( 'Booking Widget', 'booking-commerce' ), $widget_options );
	}

	/**
	 * Widget content.
	 *
	 * @param array $args arguments.
	 * @param array $instance array of instance.
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {
		if ( class_exists( 'WKBOOKING\Includes\Admin\WKBC_Admin_Function_Handler' ) ) {
			$function_handler = new Admin\WKBOOKING_Admin_Function_Handler();
			$script_output    = $function_handler->wkbooking_script();

			if ( ! empty( $script_output ) ) {
				echo wp_kses_post( $script_output );
			}
		}
	}
}
