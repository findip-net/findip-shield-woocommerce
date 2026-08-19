<?php
/**
 * WordPress privacy-policy integration.
 *
 * @package FindIP_Shield_WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds suggested disclosure text to the privacy-policy guide.
 */
final class FindIP_Shield_WC_Privacy {
	/**
	 * Register privacy hooks.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'add_policy_content' ) );
	}

	/**
	 * Add suggested privacy-policy content.
	 */
	public function add_policy_content() {
		if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
			return;
		}

		$content  = '<p>' . esc_html__( 'This store uses FindIP Shield to receive network and visitor-risk signals for storefront activity. Depending on the configured privacy and consent modes, the service receives the visitor IP address from the network connection and limited technical event metadata. The integration does not send names, email addresses, postal addresses, product identifiers, cart contents, order information, form values, or payment details.', 'findip-shield-for-woocommerce' ) . '</p>';
		$content .= '<p>' . wp_kses_post( __( 'Learn more in the <a href="https://www.findip.net/docs/shield/data-collection">FindIP Shield data-collection documentation</a> and <a href="https://www.findip.net/Docs/privacy-policy">FindIP privacy policy</a>.', 'findip-shield-for-woocommerce' ) ) . '</p>';

		wp_add_privacy_policy_content( 'FindIP Shield for WooCommerce', wp_kses_post( $content ) );
	}
}
