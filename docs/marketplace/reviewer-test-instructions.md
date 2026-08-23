# FindIP Shield for WooCommerce — Reviewer Test Instructions

Version under review: **0.1.2** · Support: info@findip.net · Security: security@findip.net

FindIP Shield for WooCommerce adds visitor risk detection (VPN, proxy, Tor,
relay, hosting, datacenter, malicious-IP signals plus a session risk score)
to a WooCommerce storefront. It is a connector to the FindIP Shield SaaS:
the plugin loads a pinned browser SDK and sends coarse event metadata to
`shield.findip.net`; risk intelligence is shown in the FindIP dashboard.

Setup takes about 5 minutes and requires only a free FindIP account.

## 1. Environment

Any WordPress 6.4+ site with WooCommerce 8.2+ (tested up to WordPress 7.1 /
WooCommerce 11.0). Works with classic themes and Cart/Checkout Blocks, with
HPOS enabled or disabled. PHP 7.4+.

The site's public hostname must be reachable by a browser (events are sent
from the visitor's browser and validated against the registered domain).
A local site works if you register the hostname you actually browse.

## 2. Create a Shield site key

1. Register a free account at <https://www.findip.net> (no card required).
2. Open **Shield** in the top navigation → **Sites** → **New site**.
3. Enter any name and the **exact hostname** of your test store
   (e.g. `mystore.example.com`), accept the free-preview terms, and create.
4. Copy the site's **public key** — it starts with `pub_`.

## 3. Install and connect

1. Install the plugin ZIP and activate (WooCommerce must be active; the
   general "FindIP Shield" WordPress plugin must NOT be active at the same
   time — the plugin blocks this combination with an admin notice).
2. Open **WooCommerce → FindIP Shield**.
3. Paste the `pub_` key, keep the defaults, and save.
4. No request is made anywhere until a syntactically valid key is saved.

## 4. Verify the core flow (2–3 minutes)

1. Open the storefront in a fresh browser/incognito window.
2. Browse: a product page → add an item to the cart → cart page →
   checkout page → place a test order (e.g. cash on delivery).
3. In the FindIP dashboard, open your site → **Live Events**. Within
   seconds you should see, in order: one `session_start`, `page_view`s,
   and `custom` events labeled `product_view`, `cart_updated (item_added)`,
   `cart_view`, `checkout_view`, and `order_received` — each with a risk
   score, network flags, and country/ASN for your IP.
4. Note what is **absent**: no product names/IDs, no prices, no customer
   fields, no order IDs. Inspect the requests to
   `https://shield.findip.net/v1/shield/track` in browser dev tools to
   confirm — payloads contain only event labels, page paths, session ids,
   and privacy-mode-limited browser metadata.

## 5. Privacy modes

- Default mode is **strict**: the browser payload contains only user agent,
  language, and timezone; no visitor cookie is set (only a session-scoped
  `_fip_sid`).
- Switch to **balanced** in the settings and reload the storefront: payloads
  add platform/screen/viewport and a 30-day first-party `_fip_vid` cookie.
- What is never collected in any mode: form values, passwords, payment
  fields, raw emails/phones, or WooCommerce business objects. Details:
  <https://www.findip.net/docs/shield/data-collection>.

## 6. Consent gating

1. In settings, enable **Require an explicit consent signal** and set
   pre-consent behavior to **Disabled**; save.
2. Load a storefront page in a fresh incognito window: **no** requests are
   made to `shield.findip.net`.
3. In the browser console run:
   `document.dispatchEvent(new CustomEvent('findip:consent', { detail: { granted: true } }));`
4. Tracking starts from that moment (the suppressed page event is emitted).
   With pre-consent behavior **Strict**, events flow immediately but in
   strict mode. This integrates with any CMP that can dispatch the event.

## 7. Toggles and uninstall

- Each event category (product views, cart events, checkout events) and the
  automatic tracking/form detection can be disabled individually in
  settings; disabled categories emit nothing.
- Deactivating the plugin removes all frontend output. Uninstalling deletes
  the plugin's single settings option. The plugin stores nothing else in
  the database and adds no cron jobs, admin notices, or upsells.

## 8. Compatibility notes

- **HPOS**: fully compatible (declares `custom_order_tables`); tested with
  HPOS enabled — orders placed during testing land in `wp_wc_orders`.
- **Cart/Checkout Blocks**: cart events use `wc-blocks_added_to_cart` /
  `wc-blocks_removed_from_cart`; classic themes use the jQuery
  `added_to_cart` / `removed_from_cart` / `checkout_error` events. When
  both fire for one action, a 500 ms dedupe emits a single event.
- Telemetry is wrapped in try/catch and `credentials: omit`; storefront
  behavior never depends on the service being reachable.
- The SDK is pinned to an exact immutable version on `cdn.findip.net` and
  loaded with subresource integrity — the browser refuses a modified file.

## 9. Server-side verification (optional)

Browser risk results are informational. The authoritative check is
`POST https://shield.findip.net/v1/shield/sessions/verify` with the site's
secret key: <https://www.findip.net/docs/shield/server-verification>.

## Demo

A configured demo store (WordPress 7 + WooCommerce 11, HPOS on, Storefront
theme) is available on request, as is a walkthrough screencast — contact
info@findip.net.
