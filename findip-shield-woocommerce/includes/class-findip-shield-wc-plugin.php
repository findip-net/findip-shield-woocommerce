<?php
/**
 * Main plugin runtime.
 *
 * @package FindIP_Shield_WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loads the Shield SDK and coordinates the WooCommerce integration.
 */
final class FindIP_Shield_WC_Plugin {
	const OPTION_NAME = 'findip_shield_woocommerce_settings';
	const SDK_VERSION = '1.0.5';
	const SDK_SRI     = 'sha384-rwISMrRaCS6RMzWGm8J3VimsKtyhHuJwOc+ZmUYZPyynHyU5kICyoXXQsYlTp/5e';

	/**
	 * Singleton instance.
	 *
	 * @var FindIP_Shield_WC_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Get the plugin instance.
	 *
	 * @return FindIP_Shield_WC_Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register hooks and components.
	 */
	private function __construct() {
		new FindIP_Shield_WC_Admin();
		new FindIP_Shield_WC_Privacy();

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
		add_filter( 'script_loader_tag', array( $this, 'secure_sdk_script_tag' ), 10, 3 );
		add_filter( 'plugin_action_links_' . plugin_basename( FINDIP_SHIELD_WC_FILE ), array( $this, 'add_settings_link' ) );
	}

	/**
	 * Default, privacy-preserving settings.
	 *
	 * @return array<string, mixed>
	 */
	public static function default_settings() {
		return array(
			'site_key'               => '',
			'privacy_mode'           => 'strict',
			'auto_track'             => true,
			'auto_detect_forms'      => true,
			'consent_required'       => true,
			'no_consent_mode'        => 'strict',
			'track_product_views'   => true,
			'track_cart_events'     => true,
			'track_checkout_events' => true,
		);
	}

	/**
	 * Read normalized settings.
	 *
	 * @return array<string, mixed>
	 */
	public static function settings() {
		$stored = get_option( self::OPTION_NAME, array() );

		return wp_parse_args( is_array( $stored ) ? $stored : array(), self::default_settings() );
	}

	/**
	 * Validate a public Shield site key.
	 *
	 * @param string $site_key Candidate public site key.
	 * @return bool
	 */
	public static function is_valid_site_key( $site_key ) {
		return 1 === preg_match( '/^pub_[a-f0-9]+$/', $site_key );
	}

	/**
	 * Enqueue the pinned Shield SDK and WooCommerce bridge.
	 */
	public function enqueue_frontend_assets() {
		$settings = self::settings();
		$site_key = isset( $settings['site_key'] ) ? (string) $settings['site_key'] : '';

		if ( ! self::is_valid_site_key( $site_key ) ) {
			return;
		}

		wp_enqueue_script(
			'findip-shield-wc-sdk',
			'https://cdn.findip.net/shield/' . self::SDK_VERSION . '/findip-shield.min.js',
			array(),
			self::SDK_VERSION,
			true
		);

		wp_enqueue_script(
			'findip-shield-woocommerce',
			FINDIP_SHIELD_WC_URL . 'assets/js/findip-shield-woocommerce.js',
			array( 'findip-shield-wc-sdk' ),
			FINDIP_SHIELD_WC_VERSION,
			true
		);

		wp_localize_script(
			'findip-shield-woocommerce',
			'findipShieldWooCommerceSettings',
			array(
				'siteKey'             => $site_key,
				'privacyMode'         => $settings['privacy_mode'],
				'autoTrack'           => (bool) $settings['auto_track'],
				'autoDetectForms'     => (bool) $settings['auto_detect_forms'],
				'consentRequired'     => (bool) $settings['consent_required'],
				'noConsentMode'       => $settings['no_consent_mode'],
				'pageEvent'           => FindIP_Shield_WC_Context::get_page_event(),
				'trackProductViews'   => (bool) $settings['track_product_views'],
				'trackCartEvents'     => (bool) $settings['track_cart_events'],
				'trackCheckoutEvents' => (bool) $settings['track_checkout_events'],
			)
		);
	}

	/**
	 * Add integrity protection to the remotely hosted SDK.
	 *
	 * @param string $tag    Generated script tag.
	 * @param string $handle Script handle.
	 * @param string $src    Script source.
	 * @return string
	 */
	public function secure_sdk_script_tag( $tag, $handle, $src ) {
		if ( 'findip-shield-wc-sdk' !== $handle ) {
			return $tag;
		}

		return sprintf(
			// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript -- This filter adds SRI to a script registered with wp_enqueue_script().
			'<script src="%1$s" integrity="%2$s" crossorigin="anonymous" id="findip-shield-wc-sdk-js"></script>' . "\n",
			esc_url( $src ),
			esc_attr( self::SDK_SRI )
		);
	}

	/**
	 * Add the configuration shortcut on the Plugins page.
	 *
	 * @param string[] $links Existing links.
	 * @return string[]
	 */
	public function add_settings_link( $links ) {
		array_unshift(
			$links,
			'<a href="' . esc_url( admin_url( 'admin.php?page=findip-shield-woocommerce' ) ) . '">' .
			esc_html__( 'Configure', 'findip-shield-woocommerce' ) .
			'</a>'
		);

		return $links;
	}
}
