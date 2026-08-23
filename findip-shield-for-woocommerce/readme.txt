=== FindIP Shield for WooCommerce ===
Contributors: findipshield
Tags: woocommerce, fraud detection, vpn detection, proxy detection, visitor risk
Requires at least: 6.4
Tested up to: 7.1
Requires PHP: 7.4
Requires Plugins: woocommerce
WC requires at least: 8.2
WC tested up to: 11.0
Stable tag: 0.1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add privacy-conscious visitor risk signals to WooCommerce without collecting customer, cart, order, or payment data.

== Description ==

FindIP Shield for WooCommerce reports explainable VPN, proxy, Tor, relay, hosting, datacenter, malicious-IP, and network-service signals for storefront activity.

The plugin provides:

* Guided setup using a public Shield site key.
* Strict, balanced, and advanced privacy modes.
* Consent-aware initialization with strict or disabled pre-consent behavior.
* Product, cart, checkout, failure, and order-received signals without business-object identifiers.
* Classic-template and Cart/Checkout Blocks browser-event support.
* Compatibility declarations for High-Performance Order Storage and Cart/Checkout Blocks.
* Suggested disclosure text for the WordPress privacy-policy editor.

FindIP Shield provides risk signals, not proof of fraud. Do not automatically block a visitor only because they use a VPN, proxy, Tor, or hosting network. Important decisions should use server verification.

== Data minimization ==

WooCommerce event payloads contain only:

* The integration name (`woocommerce`).
* A coarse event label such as `cart_view` or `checkout_failed`.
* For cart changes, `item_added` or `item_removed`.

The integration does not transmit customer IDs, names, email addresses, phone numbers, postal addresses, product IDs, SKUs, cart contents, order IDs, coupons, form values, payment methods, or payment details.

== External services ==

This plugin connects to FindIP Shield, an external service operated by FindIP.

When configured, it downloads a pinned JavaScript SDK from `https://cdn.findip.net` and sends website events to `https://shield.findip.net`. The service receives the visitor IP address from the network connection and limited event metadata based on the selected privacy mode. The service is required for the plugin to provide risk intelligence. No connection is made until an administrator saves a valid public Shield site key.

* Service: https://www.findip.net/shield/overview
* Data collection: https://www.findip.net/docs/shield/data-collection
* Privacy modes: https://www.findip.net/docs/shield/privacy-modes
* Privacy policy: https://www.findip.net/Docs/privacy-policy
* Terms: https://www.findip.net/docs/shield/terms-free-preview

== Installation ==

1. Install and activate WooCommerce.
2. Install and activate FindIP Shield for WooCommerce.
3. Create a Shield site for the storefront domain at findip.net.
4. Open **WooCommerce > FindIP Shield**.
5. Paste the public site key and choose the privacy, consent, and event settings.
6. Save, visit the storefront, and confirm the first event in the Shield dashboard.

== Consent integration ==

With **Require an explicit consent signal** enabled, the plugin starts in strict or disabled mode. A consent-management plugin or theme can update Shield by dispatching:

`document.dispatchEvent(new CustomEvent('findip:consent', { detail: { granted: true } }));`

Send `granted: false` when consent is withdrawn.

== Frequently Asked Questions ==

= Does the integration transmit orders or cart contents? =

No. It reports only coarse storefront actions and never reads WooCommerce customer, product, cart, order, coupon, or payment objects.

= Does it collect checkout form values? =

No. The SDK can recognize form activity, but it does not read or transmit names, addresses, passwords, payment details, message contents, or other field values.

= Does it automatically block shoppers? =

No. The integration reports signals and recommendations. Browser-side results are not an authorization boundary.

= Can I install this with the general FindIP Shield WordPress plugin? =

No. The general plugin already includes WooCommerce support. Keep only one FindIP Shield plugin active to prevent duplicate events.

= Where can I get help? =

Email info@findip.net. Report security issues privately to security@findip.net.

== Changelog ==

= 0.1.1 =

* Pin FindIP Shield SDK 1.0.8 with the one-session-start fix and correct integration attribution.
* Report events under the woocommerce integration instead of Google Tag Manager.

= 0.1.0 =

* Initial standalone WooCommerce marketplace release.

