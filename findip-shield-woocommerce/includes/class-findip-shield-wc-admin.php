<?php
/**
 * WooCommerce administration settings.
 *
 * @package FindIP_Shield_WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and renders privacy-safe plugin settings.
 */
final class FindIP_Shield_WC_Admin {
	/**
	 * Register admin hooks.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
	}

	/**
	 * Register the plugin settings.
	 */
	public function register_settings() {
		register_setting(
			'findip_shield_woocommerce',
			FindIP_Shield_WC_Plugin::OPTION_NAME,
			array(
				'type'              => 'array',
				'default'           => FindIP_Shield_WC_Plugin::default_settings(),
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
			)
		);
	}

	/**
	 * Sanitize all plugin settings.
	 *
	 * @param mixed $input Submitted settings.
	 * @return array<string, mixed>
	 */
	public function sanitize_settings( $input ) {
		$defaults = FindIP_Shield_WC_Plugin::default_settings();
		$input    = is_array( $input ) ? $input : array();
		$site_key = isset( $input['site_key'] ) ? sanitize_text_field( wp_unslash( $input['site_key'] ) ) : '';

		if ( '' !== $site_key && ! FindIP_Shield_WC_Plugin::is_valid_site_key( $site_key ) ) {
			add_settings_error(
				FindIP_Shield_WC_Plugin::OPTION_NAME,
				'invalid_site_key',
				esc_html__( 'The public site key must start with pub_ and contain lowercase hexadecimal characters.', 'findip-shield-woocommerce' )
			);
			$site_key = '';
		}

		$privacy_mode = isset( $input['privacy_mode'] ) ? sanitize_key( $input['privacy_mode'] ) : $defaults['privacy_mode'];
		if ( ! in_array( $privacy_mode, array( 'strict', 'balanced', 'advanced' ), true ) ) {
			$privacy_mode = $defaults['privacy_mode'];
		}

		$no_consent_mode = isset( $input['no_consent_mode'] ) ? sanitize_key( $input['no_consent_mode'] ) : $defaults['no_consent_mode'];
		if ( ! in_array( $no_consent_mode, array( 'strict', 'disabled' ), true ) ) {
			$no_consent_mode = $defaults['no_consent_mode'];
		}

		return array(
			'site_key'              => $site_key,
			'privacy_mode'          => $privacy_mode,
			'auto_track'            => ! empty( $input['auto_track'] ),
			'auto_detect_forms'     => ! empty( $input['auto_detect_forms'] ),
			'consent_required'      => ! empty( $input['consent_required'] ),
			'no_consent_mode'       => $no_consent_mode,
			'track_product_views'   => ! empty( $input['track_product_views'] ),
			'track_cart_events'     => ! empty( $input['track_cart_events'] ),
			'track_checkout_events' => ! empty( $input['track_checkout_events'] ),
		);
	}

	/**
	 * Register a settings page under WooCommerce.
	 */
	public function add_settings_page() {
		add_submenu_page(
			'woocommerce',
			esc_html__( 'FindIP Shield', 'findip-shield-woocommerce' ),
			esc_html__( 'FindIP Shield', 'findip-shield-woocommerce' ),
			'manage_woocommerce',
			'findip-shield-woocommerce',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Render the settings page.
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$settings   = FindIP_Shield_WC_Plugin::settings();
		$option_name = FindIP_Shield_WC_Plugin::OPTION_NAME;
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'FindIP Shield for WooCommerce', 'findip-shield-woocommerce' ); ?></h1>
			<p>
				<?php
				echo wp_kses_post(
					sprintf(
						/* translators: %s: FindIP Shield dashboard URL. */
						__( 'Create a site in the <a href="%s" target="_blank" rel="noopener noreferrer">FindIP Shield dashboard</a>, then paste its public key below.', 'findip-shield-woocommerce' ),
						esc_url( 'https://www.findip.net/shield' )
					)
				);
				?>
			</p>
			<p><?php echo esc_html__( 'This integration sends coarse storefront activity only. It does not send customer, product, cart, order, form, or payment data.', 'findip-shield-woocommerce' ); ?></p>

			<?php settings_errors( FindIP_Shield_WC_Plugin::OPTION_NAME ); ?>
			<form method="post" action="options.php">
				<?php settings_fields( 'findip_shield_woocommerce' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="findip-shield-wc-site-key"><?php echo esc_html__( 'Public site key', 'findip-shield-woocommerce' ); ?></label></th>
						<td>
							<input id="findip-shield-wc-site-key" class="regular-text code" name="<?php echo esc_attr( $option_name ); ?>[site_key]" value="<?php echo esc_attr( $settings['site_key'] ); ?>" placeholder="pub_0123456789abcdef" pattern="pub_[a-f0-9]+">
							<p class="description"><?php echo esc_html__( 'Use the public identifier for the exact storefront domain, never a secret verification key.', 'findip-shield-woocommerce' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="findip-shield-wc-privacy-mode"><?php echo esc_html__( 'Privacy mode', 'findip-shield-woocommerce' ); ?></label></th>
						<td>
							<select id="findip-shield-wc-privacy-mode" name="<?php echo esc_attr( $option_name ); ?>[privacy_mode]">
								<option value="strict" <?php selected( $settings['privacy_mode'], 'strict' ); ?>><?php echo esc_html__( 'Strict', 'findip-shield-woocommerce' ); ?></option>
								<option value="balanced" <?php selected( $settings['privacy_mode'], 'balanced' ); ?>><?php echo esc_html__( 'Balanced', 'findip-shield-woocommerce' ); ?></option>
								<option value="advanced" <?php selected( $settings['privacy_mode'], 'advanced' ); ?>><?php echo esc_html__( 'Advanced', 'findip-shield-woocommerce' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo esc_html__( 'General collection', 'findip-shield-woocommerce' ); ?></th>
						<td>
							<label><input type="checkbox" name="<?php echo esc_attr( $option_name ); ?>[auto_track]" value="1" <?php checked( $settings['auto_track'] ); ?>> <?php echo esc_html__( 'Track page and session events automatically', 'findip-shield-woocommerce' ); ?></label><br>
							<label><input type="checkbox" name="<?php echo esc_attr( $option_name ); ?>[auto_detect_forms]" value="1" <?php checked( $settings['auto_detect_forms'] ); ?>> <?php echo esc_html__( 'Detect form activity without reading form values', 'findip-shield-woocommerce' ); ?></label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo esc_html__( 'WooCommerce events', 'findip-shield-woocommerce' ); ?></th>
						<td>
							<label><input type="checkbox" name="<?php echo esc_attr( $option_name ); ?>[track_product_views]" value="1" <?php checked( $settings['track_product_views'] ); ?>> <?php echo esc_html__( 'Track product-page views without product identifiers', 'findip-shield-woocommerce' ); ?></label><br>
							<label><input type="checkbox" name="<?php echo esc_attr( $option_name ); ?>[track_cart_events]" value="1" <?php checked( $settings['track_cart_events'] ); ?>> <?php echo esc_html__( 'Track cart views and anonymous item-added/item-removed signals', 'findip-shield-woocommerce' ); ?></label><br>
							<label><input type="checkbox" name="<?php echo esc_attr( $option_name ); ?>[track_checkout_events]" value="1" <?php checked( $settings['track_checkout_events'] ); ?>> <?php echo esc_html__( 'Track checkout views, failures, and order-received page visits', 'findip-shield-woocommerce' ); ?></label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo esc_html__( 'Consent', 'findip-shield-woocommerce' ); ?></th>
						<td>
							<label><input type="checkbox" name="<?php echo esc_attr( $option_name ); ?>[consent_required]" value="1" <?php checked( $settings['consent_required'] ); ?>> <?php echo esc_html__( 'Require an explicit consent signal', 'findip-shield-woocommerce' ); ?></label>
							<p class="description"><?php echo esc_html__( 'Your consent manager should dispatch findip:consent with detail.granted set to true or false.', 'findip-shield-woocommerce' ); ?></p>
							<select name="<?php echo esc_attr( $option_name ); ?>[no_consent_mode]">
								<option value="strict" <?php selected( $settings['no_consent_mode'], 'strict' ); ?>><?php echo esc_html__( 'Before consent: strict mode', 'findip-shield-woocommerce' ); ?></option>
								<option value="disabled" <?php selected( $settings['no_consent_mode'], 'disabled' ); ?>><?php echo esc_html__( 'Before consent: no tracking', 'findip-shield-woocommerce' ); ?></option>
							</select>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
			<p>
				<a href="https://www.findip.net/docs/shield" target="_blank" rel="noopener noreferrer"><?php echo esc_html__( 'Documentation', 'findip-shield-woocommerce' ); ?></a>
				· <a href="mailto:info@findip.net"><?php echo esc_html__( 'Support', 'findip-shield-woocommerce' ); ?></a>
				· <a href="mailto:security@findip.net"><?php echo esc_html__( 'Security', 'findip-shield-woocommerce' ); ?></a>
			</p>
		</div>
		<?php
	}
}

