<?php
/**
 * WooCommerce compatibility and page context.
 *
 * @package FindIP_Shield_WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Declares compatibility and exposes non-sensitive storefront context.
 */
final class FindIP_Shield_WC_Context {
	/**
	 * Register WooCommerce compatibility declarations.
	 */
	public function __construct() {
		add_action( 'before_woocommerce_init', array( $this, 'declare_compatibility' ) );
	}

	/**
	 * Declare HPOS and Cart/Checkout Blocks compatibility.
	 */
	public function declare_compatibility() {
		if ( ! class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			return;
		}

		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', FINDIP_SHIELD_WC_FILE, true );
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', FINDIP_SHIELD_WC_FILE, true );
	}

	/**
	 * Describe the current WooCommerce page without reading object data.
	 *
	 * @return string
	 */
	public static function get_page_event() {
		if ( function_exists( 'is_order_received_page' ) && is_order_received_page() ) {
			return 'order_received';
		}

		if ( function_exists( 'is_checkout' ) && is_checkout() ) {
			return 'checkout_view';
		}

		if ( function_exists( 'is_cart' ) && is_cart() ) {
			return 'cart_view';
		}

		if ( function_exists( 'is_product' ) && is_product() ) {
			return 'product_view';
		}

		return '';
	}
}
