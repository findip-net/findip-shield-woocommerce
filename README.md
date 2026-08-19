# FindIP Shield for WooCommerce

Official WooCommerce integration for [FindIP Shield](https://www.findip.net/shield/overview), maintained by FindIP at [info@findip.net](mailto:info@findip.net).

The installable plugin is in [`findip-shield-for-woocommerce/`](findip-shield-for-woocommerce/). It adds consent-aware visitor risk signals to WooCommerce storefront activity without transmitting customer, product, cart, order, or payment identifiers.

## Tracked storefront context

- Product, cart, checkout, and order-received page types
- Cart item added or removed signals
- Checkout failures exposed by WooCommerce
- Standard Shield page, session, and form-activity events

Event payloads contain only the integration name and a coarse event/action label. The plugin never reads form values or sends names, email addresses, addresses, cart contents, order data, product identifiers, or payment details.

## Development status

Version `0.1.0` is the initial marketplace MVP. It supports classic WooCommerce templates and the Cart/Checkout Blocks browser events, and declares compatibility with HPOS and Cart/Checkout Blocks.

## Local test

1. Copy `findip-shield-for-woocommerce/` to `wp-content/plugins/findip-shield-for-woocommerce/`.
2. Install and activate WooCommerce.
3. Activate **FindIP Shield for WooCommerce**.
4. Configure it under **WooCommerce → FindIP Shield**.
5. Exercise product, cart, checkout, failure, and order-received flows.
6. Verify events in the Shield dashboard and inspect browser requests for unexpected fields.

## Quality checks

```bash
composer install
composer lint
npm test
find findip-shield-for-woocommerce -name '*.php' -print0 | xargs -0 -n1 php -l
```

## Support

- Product support: [info@findip.net](mailto:info@findip.net)
- Security reports: [security@findip.net](mailto:security@findip.net)
- Documentation: <https://www.findip.net/docs/shield>

## License

GPL-2.0-or-later. See [LICENSE](LICENSE).

