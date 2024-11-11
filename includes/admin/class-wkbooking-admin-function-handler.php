<?php
/**
 * Admin Function Handler File.
 *
 * @version 1.1.0
 *
 * @package Booking Commerce
 */

namespace WKBOOKING\Includes\Admin;

defined( 'ABSPATH' ) || exit(); // Direct access is not allowed.

use WKBOOKING\Templates\Admin as AdminTemplate;
use WKBOOKING\Includes as MainWidget;

/**
 * Class WKBOOKING Admin Function Handler.
 *
 * @version 1.1.0
 */
class WKBOOKING_Admin_Function_Handler {
	/**
	 * Instance variable.
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
		$this->wkbooking_commerce_commerce_widget();
		add_action( 'init', array( $this, 'wkbooking_shortcodes_init' ) );
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

	/**
	 * Booking commerce widget.
	 *
	 * @return void
	 */
	public function wkbooking_commerce_commerce_widget() {
		new MainWidget\WKBOOKING_Commerce_Widget();
	}

	/**
	 * Register Widget.
	 *
	 * @return void
	 */
	public function wkbooking_register_widgets() {
		// Check if the widget class exists before registering it.
		if ( class_exists( 'WKBOOKING\Includes\WKBOOKING_Commerce_Widget' ) ) {
			register_widget( 'WKBOOKING\Includes\WKBOOKING_Commerce_Widget' );
		}
	}

	/**
	 * Admin Settings.
	 *
	 * @param array $links Links.
	 *
	 * @return array $links Links.
	 */
	public function plugin_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=booking-setting">' . __( 'Settings', 'booking-commerce' ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}

	/**
	 * For registering setting.
	 *
	 * @return void
	 */
	public function wkbooking_register_booking_link() {
		register_setting( 'booking-commerce-link', 'wkbooking_link' );
	}

	/**
	 * Self Script.
	 *
	 * @return mixed
	 */
	public function wkbooking_sc_require() {
		return $this->wkbooking_script();
	}

	/**
	 * Admin Menu.
	 *
	 * @return void
	 */
	public function wkbooking_menu_item() {
		add_options_page(
			esc_html__( 'Booking', 'booking-commerce' ),
			esc_html__( 'Booking', 'booking-commerce' ),
			'read',
			'wkbooking_settings',
			array( $this, 'wkbooking_settings_page_content' )
		);
	}

	/**
	 * Display the settings page content with dynamic hooks for tabs.
	 *
	 * @return void
	 */
	public function wkbooking_settings_page_content() {
		// Fetch the current tab dynamically.
		$current_tab = ! empty( filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ) ? filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : esc_html( 'general' );

		// Define the tabs dynamically.
		$tabs = array(
			'general'    => esc_html__( 'General Settings', 'booking-commerce' ),
			'services'   => esc_html__( 'Services & Support', 'booking-commerce' ),
			'extensions' => esc_html__( 'Extensions', 'booking-commerce' ),
		);
		?>
			<div class="wrap">
				<h1 class="wkbc-header">
					<img src="<?php echo esc_url( WKBOOKING_PLUGIN_URL . 'assets/images/wordpress-booking-plugin-webkul_1.png' ); ?>" alt="Logo" class="wkbc-logo" />
				<?php esc_html_e( 'Booking Widget Settings', 'booking-commerce' ); ?>
				</h1>
				<h2 class="nav-tab-wrapper">
				<?php foreach ( $tabs as $tab_key => $tab_label ) { ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php/?page=wkbooking_settings&tab=' . esc_attr( $tab_key ) ) ); ?>" class="nav-tab <?php echo ( $tab_key === $current_tab ) ? 'nav-tab-active' : ''; ?>">
					<?php echo esc_attr( $tab_label ); ?>
					</a>
				<?php } ?>
				</h2>
				<?php
				/**
				 * Fires for the currently selected tab.
				 *
				 * @since v1.1.0
				 * @param string $current_tab The currently selected tab.
				 */
					do_action( 'wkbooking_render_settings_' . $current_tab );
				?>
			</div>
			<?php
	}

	/**
	 * Render the General Settings tab content.
	 *
	 * @return void
	 */
	public function wkbooking_render_general_settings() {
		new AdminTemplate\WKBOOKING_Admin_Creds_Settings();
	}

	/**
	 * Handle form submission and save booking link and selected pages.
	 *
	 * @return void
	 */
	public function wkbooking_handle_form_submission() {
		// Check nonce.
		$nonce = filter_input( INPUT_POST, 'save_booking_link_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$nonce = empty( $nonce ) ? '' : wp_unslash( $nonce );

		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'save_booking_link_action' ) ) {
			// Redirect with error message.
			wp_safe_redirect( add_query_arg( 'msg', 'nonce_error', admin_url( 'options-general.php?page=wkbooking_settings' ) ) );
			exit;
		}

		$booking_link             = ! empty( $_POST['wkbooking_link'] ) ? sanitize_text_field( wp_unslash( $_POST['wkbooking_link'] ) ) : '';
		$wkbooking_selected_pages = ! empty( $_POST['wkbooking_selected_pages'] ) ? array_map( 'sanitize_key', $_POST['wkbooking_selected_pages'] ) : array();

		// Check capabilities.
		if ( current_user_can( 'manage_options' ) ) {
			// Save booking link.
			if ( ! empty( $booking_link ) ) {
				update_option( 'wkbooking_link', esc_url_raw( $booking_link ) );
			}
			// Save selected pages.
			if ( ! empty( $wkbooking_selected_pages ) ) {
				$selected_pages = array_map( 'intval', $wkbooking_selected_pages ); // Clean selected page IDs.
				update_option( 'wkbooking_selected_pages', $selected_pages ); // Save the selected pages in the database.
			} else {
				delete_option( 'wkbooking_selected_pages' ); // If no pages selected, clear the option.
			}

			// Redirect with success message.
			wp_safe_redirect( add_query_arg( 'msg', 'settings_updated', admin_url( 'options-general.php?page=wkbooking_settings' ) ) );
			exit;
		}
		// If capabilities check fails, redirect with error message.
		wp_safe_redirect( add_query_arg( 'msg', 'capability_error', admin_url( 'options-general.php?page=wkbooking_settings' ) ) );
		exit;
	}

	/**
	 * Automatically append the [book_widget] shortcode to the content of selected pages.
	 *
	 * @param string $content Original page content.
	 *
	 * @return string Modified content with shortcode.
	 */
	public function wkbooking_auto_apply_shortcode( $content ) {
		// Get the list of selected page IDs from the database.
		$selected_pages = get_option( 'wkbooking_selected_pages', array() );

		// Check if the current page ID is in the list of selected pages.
		if ( is_page() && in_array( get_the_ID(), $selected_pages, true ) ) {
			// Append the [book_widget] shortcode content to the page content.
			$content .= do_shortcode( '[book_widget]' );
		}

		return $content;
	}

	/**
	 * Central location to create all short codes.
	 *
	 * @return void
	 */
	public function wkbooking_shortcodes_init() {
		add_shortcode( 'book_widget', array( $this, 'wkbooking_sc_require' ) );
	}

	/**
	 * Render settings for services.
	 *
	 * @return void
	 */
	public function wkbooking_render_settings_services() {
		?>
			<wk-area></wk-area>
		<?php
	}

	/**
	 * Support and services menu.
	 *
	 * @return void
	 */
	public function wkbooking_render_settings_extensions() {
		?>
		<div class="wkbc-wrap extensions" >
			<webkul-extensions></webkul-extensions>
		</div>
		<?php
	}

	/**
	 * Script for booking commerce.
	 *
	 * @return mixed
	 */
	public function wkbooking_script() {
		$url = get_option( 'wkbooking_link', '' );
		// Check if the booking link is set, else return early.
		if ( empty( $url ) ) {
			return;
		}
		// Enqueue an inline script with dynamic content.
		wp_enqueue_script( 'booking_widget', esc_url( $url . '/widget/js/widget.js' ), array(), WKBOOKING_PLUGIN_VERSION, true );
		// Add the inline script that will initialize the widget after it has been loaded.
		$inline_script = "
			(function() {
				var widget = document.createElement('script');
				widget.async = true;
				widget.src = '" . esc_url( $url ) . "/widget/js/widget.js';
				widget.charset = 'UTF-8';
				widget.setAttribute('crossorigin', '*');
				widget.onload = function() {
					new beWidget({
						'baseurl': '" . esc_url( $url ) . "/en',
						'brandColor': '#1747E3',
						'widgetType': 'global'
					});
				};
				document.head.appendChild( widget );
			})();";
		// Add the inline script after the enqueued script is loaded.
		wp_add_inline_script( 'booking_widget', $inline_script );
	}

	/**
	 * Enqueue admin script.
	 *
	 * @return void
	 */
	public function wkbooking_enqueue_admin_script() {
		$screen   = get_current_screen();
		$get_data = ! empty( filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ) ? filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : esc_html( 'general' );

		if ( 'settings_page_wkbooking_settings' === $screen->id ) {
			wp_enqueue_style( 'wkbc-admin-style', WKBOOKING_PLUGIN_URL . 'assets/dist/css/wkbc-admin.min.css', array(), WKBOOKING_PLUGIN_VERSION );
			wp_enqueue_script( 'wkbc-admin-script', WKBOOKING_PLUGIN_URL . '/assets/dist/js/wkbc-admin-script.min.js', array( 'jquery' ), WKBOOKING_PLUGIN_VERSION, true );
			// Localize script with strings.
			wp_localize_script(
				'wkbc-admin-script',
				'wkbooking_params',
				array(
					'copyMessage' => esc_html__( 'Shortcode copied!', 'booking-commerce' ),
				)
			);
			// Enqueue the Select2 script and CSS.
			if ( ! wp_script_is( 'select2', 'registered' ) ) {
				wp_enqueue_script(
					'wkbc-select-script',
					WKBOOKING_PLUGIN_URL . '/assets/dist/js/select2.min.js',
					array( 'jquery' ),
					WKBOOKING_PLUGIN_VERSION,
					true
				);
			}

			if ( ! wp_style_is( 'select2', 'registered' ) ) {
				wp_enqueue_style(
					'wkbc-select-style',
					WKBOOKING_PLUGIN_URL . '/assets/dist/css/select2.min.css',
					array(),
					WKBOOKING_PLUGIN_VERSION
				);
			}

			if ( ! empty( $get_data ) && 'services' === $get_data ) {
				wp_enqueue_script(
					'wkwp-addons-support-services',
					'https://webkul.com/common/modules/wksas.bundle.js',
					array(),
					WKBOOKING_PLUGIN_VERSION,
					true
				);
			}
			if ( ! empty( $get_data ) && 'extensions' === $get_data ) {
				wp_enqueue_script(
					'wkwp-addons-extensions',
					'https://wpdemo.webkul.com/wk-extensions/client/wk.ext.js',
					array(),
					WKBOOKING_PLUGIN_VERSION,
					true
				);
			}
		}
	}

	/**
	 * Show setting links.
	 *
	 * @param array $links Setting links.
	 *
	 * @return array
	 */
	public function wkbooking_add_plugin_setting_links( $links ) {
		$action_links = array(
			'settings' => '<a href="' . esc_url( admin_url( 'options-general.php?page=wkbooking_settings' ) ) . '" aria-label="' . esc_attr__( 'Settings', 'booking-commerce' ) . '">' . esc_html__( 'Settings', 'booking-commerce' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}

	/**
	 * Plugin row data.
	 *
	 * @param string $links Links.
	 * @param string $file Filepath.
	 *
	 * @hooked 'plugin_row_meta' filter hook.
	 *
	 * @return array $links links.
	 */
	public function wkbooking_plugin_row_meta( $links, $file ) {
		if ( plugin_basename( WKBOOKING_BASE_NAME ) === $file ) {
			$row_meta = array(
				'docs'    => '<a target="_blank" href="' . esc_url( 'https://webkul.com/blog/wordpress-booking-plugin/' ) . '" aria-label="' . esc_attr__( 'View documentation', 'booking-commerce' ) . '">' . esc_html__( 'Docs', 'booking-commerce' ) . '</a>',
				'support' => '<a target="_blank" href="' . esc_url( 'https://webkul.uvdesk.com/' ) . '" aria-label="' . esc_attr__( 'Visit customer support', 'booking-commerce' ) . '">' . esc_html__( 'Support', 'booking-commerce' ) . '</a>',
			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}
}
