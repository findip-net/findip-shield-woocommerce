<?php
/**
 * Plugin Name:       FindIP Shield for WooCommerce
 * Plugin URI:        https://www.findip.net/shield/overview
 * Description:       Adds privacy-conscious visitor risk signals to WooCommerce without collecting customer, cart, order, or payment data.
 * Version:           0.1.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Requires Plugins:  woocommerce
 * Author:            FindIP
 * Author URI:        https://www.findip.net/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       findip-shield-woocommerce
 * Update URI:        https://github.com/findip-net/findip-shield-woocommerce
 * WC requires at least: 8.2
 * WC tested up to:   11.0
 *
 * @package FindIP_Shield_WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FINDIP_SHIELD_WC_VERSION', '0.1.0' );
define( 'FINDIP_SHIELD_WC_FILE', __FILE__ );
define( 'FINDIP_SHIELD_WC_DIR', plugin_dir_path( __FILE__ ) );
define( 'FINDIP_SHIELD_WC_URL', plugin_dir_url( __FILE__ ) );

// Compatibility must be declared before WooCommerce finishes loading.
require_once FINDIP_SHIELD_WC_DIR . 'includes/class-findip-shield-wc-context.php';
new FindIP_Shield_WC_Context();

/**
 * Show an admin notice when the standalone WordPress plugin is also active.
 */
function findip_shield_wc_companion_notice() {
	?>
	<div class="notice notice-warning">
		<p><?php echo esc_html__( 'FindIP Shield already includes WooCommerce support. Deactivate either FindIP Shield or FindIP Shield for WooCommerce to prevent duplicate events.', 'findip-shield-woocommerce' ); ?></p>
	</div>
	<?php
}

/**
 * Show an admin notice when WooCommerce is unavailable.
 */
function findip_shield_wc_dependency_notice() {
	?>
	<div class="notice notice-error">
		<p><?php echo esc_html__( 'FindIP Shield for WooCommerce requires WooCommerce to be installed and active.', 'findip-shield-woocommerce' ); ?></p>
	</div>
	<?php
}

/**
 * Load the integration after plugins have initialized.
 */
function findip_shield_wc_bootstrap() {
	if ( defined( 'FINDIP_SHIELD_VERSION' ) ) {
		add_action( 'admin_notices', 'findip_shield_wc_companion_notice' );
		return;
	}

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'findip_shield_wc_dependency_notice' );
		return;
	}

	require_once FINDIP_SHIELD_WC_DIR . 'includes/class-findip-shield-wc-admin.php';
	require_once FINDIP_SHIELD_WC_DIR . 'includes/class-findip-shield-wc-privacy.php';
	require_once FINDIP_SHIELD_WC_DIR . 'includes/class-findip-shield-wc-plugin.php';

	FindIP_Shield_WC_Plugin::instance();
}
add_action( 'plugins_loaded', 'findip_shield_wc_bootstrap', 20 );
