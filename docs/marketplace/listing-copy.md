# Woo Marketplace listing copy — FindIP Shield for WooCommerce

Working draft for the Marketplace product page. Keep claims in sync with
readme.txt and https://www.findip.net/docs/shield/woocommerce.

## Product name

FindIP Shield for WooCommerce

## One-liner (short description)

Know which visitors are hiding: VPN, proxy, Tor, and malicious-IP detection
with a risk score for every storefront session — without collecting customer,
cart, order, or payment data.

## Long description

### See the risk behind every session

Fraudulent orders, promo abuse, and card testing rarely come from ordinary
home connections. FindIP Shield gives your store the network context to spot
them early: every visitor session is scored using FindIP's IP intelligence —
VPN, proxy, Tor, relay, hosting, datacenter, malicious-IP, and
network-service signals plus a 0–100 risk score — shown live in your FindIP
dashboard alongside the storefront actions that session took.

### Built for privacy from the first line

Shield watches the network, not your customers. The integration sends only
coarse event labels (`product_view`, `cart_updated`, `checkout_view`,
`payment_failed`, `order_received`), page paths, and privacy-mode-limited
browser metadata. It never reads or transmits:

- customer names, emails, phones, or addresses
- product IDs, SKUs, prices, or cart contents
- order IDs, coupons, or payment details
- form values or keystrokes

Three privacy modes (strict, balanced, advanced) control exactly what is
collected, and consent-aware initialization integrates with any consent
tool via a single browser event. Sites in strict mode set no visitor
cookie at all.

### Made for modern WooCommerce

- Classic themes and Cart/Checkout Blocks
- High-Performance Order Storage (HPOS) compatible
- Per-category event toggles (product, cart, checkout)
- Fail-safe by design: storefront behavior never depends on the service
- The browser SDK is pinned to an immutable version and loaded with
  subresource integrity

### From signal to decision

Watch sessions in the live dashboard — IP rotation, network-type shifts,
risky countries and ASNs — and act on high-risk sessions your way: manual
review, checkout friction, or a server-side check. For decisions that
matter, verify the session's authoritative risk from your backend with a
secret key; browser signals are never your enforcement boundary.

### Free to start

Shield's free preview includes a daily event quota per site. Create a free
FindIP account, register your storefront domain, paste your site key, and
the first events arrive in seconds.

## Feature bullets (for the sidebar)

- VPN, proxy, Tor, relay, hosting, datacenter and malicious-IP detection
- 0–100 risk score and recommendation per session
- Live event and session dashboards with IP-rotation signals
- Product, cart, checkout, payment-failure and order signals — metadata only
- Strict / balanced / advanced privacy modes; consent-aware startup
- Classic + Blocks support, HPOS compatible
- Pinned SDK with subresource integrity
- Server-side session verification API

## FAQ

**Does it transmit orders or cart contents?**
No. It reports only coarse storefront actions and never reads WooCommerce
customer, product, cart, order, coupon, or payment objects.

**Does it collect checkout form values?**
No. Form activity is recognized from field *attributes* only; values are
never read or transmitted.

**Does it block shoppers automatically?**
No. Shield reports signals and recommendations. Decisions stay yours, and
important ones should use server-side verification.

**Do I need the FindIP Shield WordPress plugin as well?**
No — keep only one FindIP Shield plugin active. This extension is the
WooCommerce-focused integration with richer storefront signals.

**What does it cost?**
The extension is free and connects to Shield's free preview (daily event
quota per site). See findip.net for current terms.

## Links

- Documentation: https://www.findip.net/docs/shield/woocommerce
- Data collection: https://www.findip.net/docs/shield/data-collection
- Privacy modes: https://www.findip.net/docs/shield/privacy-modes
- Server verification: https://www.findip.net/docs/shield/server-verification
- Privacy policy: https://www.findip.net/Docs/privacy-policy
- Terms: https://www.findip.net/docs/shield/terms-free-preview
- Support: info@findip.net · Security: security@findip.net

## Asset checklist (to produce before submission)

- Product icon and listing banner per current Woo asset specs (check the
  vendor dashboard for exact dimensions at submission time)
- 4–6 screenshots: settings page, live events dashboard with a risky
  session, session detail drawer with IP history, storefront with dev-tools
  request showing the metadata-only payload, consent-gating demonstration
- Optional: 60–90 s screencast (install → paste key → events appearing),
  mirroring the Shopify review screencast
