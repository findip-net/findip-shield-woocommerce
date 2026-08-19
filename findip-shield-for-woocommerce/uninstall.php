<?php
/**
 * Remove plugin settings on uninstall.
 *
 * @package FindIP_Shield_WooCommerce
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'findip_shield_woocommerce_settings' );
