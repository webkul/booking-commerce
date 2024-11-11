<?php
/**
 * Admin Creds Settings File.
 *
 * @version 1.1.0
 *
 * @package Booking Commerce
 */
namespace WKBOOKING\Templates\Admin;

defined( 'ABSPATH' ) || exit(); // Direct access is not allowed.

/**
 * Class WKBC Admin Creds Settings File.
 */
class WKBOOKING_Admin_Creds_Settings {
	/**
	 * Instance variable.
	 *
	 * @var $instance
	 */
	protected static $instance = null;

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

	/**
	 * Constructor Function.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function __construct() {
		$this->wkbooking_render_general_settings();
	}

	/**
	 * Render the General Settings tab content.
	 *
	 * @return void
	 */
	public static function wkbooking_render_general_settings() {
		$get_data = ! empty( filter_input( INPUT_GET, 'msg', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ) ? filter_input( INPUT_GET, 'msg', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : esc_html( '' );
		// Check for messages in the URL and display accordingly.
		if ( isset( $get_data ) ) {
			switch ( $get_data ) {
				case 'settings_updated':
					add_settings_error( 'wkbooking_links', 'settings_updated', esc_html__( 'Settings saved successfully.', 'booking-commerce' ), 'updated' );
					break;
				case 'nonce_error':
					add_settings_error( 'wkbooking_links', 'nonce_error', esc_html__( 'Nonce verification failed. Please try again.', 'booking-commerce' ), 'error' );
					break;
				case 'capability_error':
					add_settings_error( 'wkbooking_links', 'capability_error', esc_html__( 'You do not have sufficient permissions to perform this action.', 'booking-commerce' ), 'error' );
					break;
			}
		}
		// Display saved settings messages.
		settings_errors( 'wkbooking_links' );
		// Fetch stored options.
		$booking_link   = get_option( 'wkbooking_link', '' );
		$selected_pages = get_option( 'wkbooking_selected_pages', array() ); // Get selected pages from database.
		$pages          = get_pages(); // Get all WordPress pages.
		?>
			<div class="wkbc-widget-container">

				<!-- Form for Booking Link and Selected Pages -->
				<form class="wkbc-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php
					// Security and action fields.
					wp_nonce_field( 'save_booking_link_action', 'save_booking_link_nonce' );
				?>
					<input type="hidden" name="action" value="save_booking_link">
					<!-- Select multiple pages -->
					<div>
						<h2><?php echo esc_html__( 'Select Pages for Shortcode', 'booking-commerce' ); ?></h2>
						<select id="wkbooking_page_select" name="wkbooking_selected_pages[]" class="wkbc-multiselect" multiple>
						<?php
						foreach ( $pages as $page ) {
							$selected = in_array( $page->ID, $selected_pages, true ) ? 'selected' : '';
							echo '<option value="' . esc_attr( $page->ID ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $page->post_title ) . '</option>';
						}
						?>
						</select>
					</div>
					<!-- Booking link input field -->
					<div>
						<h3><?php esc_html_e( 'Booking Commerce Domain', 'booking-commerce' ); ?></h3>
						<p>
							<input type="text" name="wkbooking_link" placeholder="<?php echo esc_url( 'http://testcom.bookingcommerce.com' ); ?>"
						value="<?php echo esc_attr( $booking_link ); ?>" class="widefat" />
						</p>
						<!-- Error message container -->
						<div id="wkbooking_domain_error" class="wkbooking-error-message" style="display: none;">
							<?php esc_html_e( 'Please enter a valid domain in the format http://subdomain.bookingcommerce.com', 'booking-commerce' ); ?>
						</div>
					</div>
					<div>
						<h4><?php echo esc_html__( 'Appearance -> Widgets -> Booking Widget', 'booking-commerce' ); ?></h4>
						<p><?php echo esc_html__( 'Directly add from the widget section Or use shortcodes inside your page/article:', 'booking-commerce' ); ?></p>
						<div class="wkbc-shortcode-wrapper">
							<input type="text" id="wkbooking_shortcode_field" class="wkbc-code" value="[book_widget]" readonly />
							<button class="button button-secondary" type="button" id="wkbooking_copy_button" onclick="wkbooking_copy_shortcode()">
							<?php esc_html_e( 'Copy Shortcode', 'booking-commerce' ); ?>
							</button>
						</div>
						<div id="wkbooking_copy_message" class="wkbc-copy-message">
							<?php esc_html_e( 'Short code copied!', 'booking-commerce' ); ?>
						</div>
					</div>

					<!-- Submit button -->
					<div class="wpf-center">
						<input type="submit" class="button button-primary wkbc-button" name="save_url" value="<?php esc_attr_e( 'Save Changes', 'booking-commerce' ); ?>">
					</div>
				</form>
			</div>
			<?php
	}
}
