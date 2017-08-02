<?php
/**
 * AutomatePlus admin ajax.
 *
 * @package automateplus-mautic
 * @since 1.0.0
 */

if ( ! class_exists( 'AutomatePlusAdminAjax' ) ) :

	/**
	 * Initiator
	 * Create class AutomatePlusAdminAjax
	 * Handles Ajax operations
	 */
	class AutomatePlusAdminAjax {

		/**
		 * Declare a static variable instance.
		 *
		 * @var instance
		 */
		private static $instance;

		/**
		 * Initiate class
		 *
		 * @since 1.0.0
		 * @return object
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new AutomatePlusAdminAjax();
				self::$instance->hooks();
			}
			return self::$instance;
		}

		/**
		 * Call ajax hooks
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function hooks() {
			add_action( 'wp_ajax_clean_mautic_transient', array( $this, 'clean_mautic_transient' ) );
			add_action( 'wp_ajax_config_disconnect_mautic', array( $this, 'config_disconnect_mautic' ) );
			add_action( 'admin_post_apm_rule_list', array( $this, 'handle_rule_list_actions' ) );
		}

		/**
		 * Disconnect mautic
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public static function config_disconnect_mautic() {
			$result = delete_option( 'ampw_mautic_credentials' );
			wp_send_json_success( $result );
		}

		/**
		 * Refresh Mautic transients data
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public static function clean_mautic_transient() {
			$result = delete_transient( 'apm_all_segments' );
			wp_send_json_success( $result );
		}

		/**
		 * Handle multi rule delete
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public static function handle_rule_list_actions() {

			wp_verify_nonce( '_wpnonce' );
			if ( isset( $_POST['bulk-delete'] ) ) {
				$rules_ids = $_POST['bulk-delete'];

				foreach ( $rules_ids as $id ) {
					$id = esc_attr( $id );
					if ( current_user_can( 'delete_post', $id ) ) {
						$result = wp_delete_post( $id );
					}
				}
			}
			$sendback = wp_get_referer();
			wp_redirect( $sendback );
			wp_send_json_success( $result );
		}
	}
	$automateplus_ajax = AutomatePlusAdminAjax::instance();
endif;
