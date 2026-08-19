import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import test from 'node:test';
import vm from 'node:vm';

const bridgeSource = readFileSync(
  new URL('../../findip-shield-woocommerce/assets/js/findip-shield-woocommerce.js', import.meta.url),
  'utf8'
);

function normalize(value) {
  return JSON.parse(JSON.stringify(value));
}

function createHarness(overrides = {}) {
  const documentListeners = new Map();
  const bodyListeners = new Map();
  const jqueryListeners = new Map();
  const calls = {
    init: [],
    consent: [],
    track: []
  };

  const body = {
    addEventListener(name, handler) {
      bodyListeners.set(name, handler);
    }
  };

  const document = {
    body,
    addEventListener(name, handler) {
      documentListeners.set(name, handler);
    }
  };

  const jqueryApi = {
    on(name, handler) {
      jqueryListeners.set(name, handler);
      return jqueryApi;
    }
  };

  const shield = {
    init(options) {
      calls.init.push(options);
    },
    setConsent(granted) {
      calls.consent.push(granted);
    },
    track(name, payload) {
      calls.track.push({ name, payload });
      return Promise.resolve();
    }
  };

  const settings = {
    siteKey: 'pub_0123456789abcdef',
    privacyMode: 'strict',
    autoTrack: true,
    autoDetectForms: true,
    consentRequired: false,
    noConsentMode: 'strict',
    pageEvent: 'checkout_view',
    trackProductViews: true,
    trackCartEvents: true,
    trackCheckoutEvents: true,
    ...overrides
  };

  const window = {
    FindIP: shield,
    findipShieldWooCommerceSettings: settings,
    jQuery() {
      return jqueryApi;
    }
  };

  vm.runInNewContext(bridgeSource, {
    Date,
    document,
    window
  });

  return {
    calls,
    dispatchBody(name) {
      bodyListeners.get(name)?.({});
    },
    dispatchConsent(granted) {
      documentListeners.get('findip:consent')?.({ detail: { granted } });
    },
    dispatchJquery(name) {
      jqueryListeners.get(name)?.();
    }
  };
}

test('initializes the SDK with privacy and consent settings', () => {
  const harness = createHarness({ privacyMode: 'balanced' });

  assert.equal(harness.calls.init.length, 1);
  assert.deepEqual(normalize(harness.calls.init[0]), {
    siteKey: 'pub_0123456789abcdef',
    privacyMode: 'balanced',
    autoTrack: true,
    autoDetectForms: true,
    consentRequired: false,
    noConsentMode: 'strict',
    pushToDataLayer: true
  });
});

test('sends only coarse WooCommerce context', () => {
  const harness = createHarness();

  harness.dispatchBody('wc-blocks_added_to_cart');
  harness.dispatchJquery('checkout_error');

  assert.deepEqual(normalize(harness.calls.track), [
    {
      name: 'custom',
      payload: {
        custom: {
          integration: 'woocommerce',
          event_type: 'checkout_view'
        }
      }
    },
    {
      name: 'custom',
      payload: {
        custom: {
          integration: 'woocommerce',
          event_type: 'cart_updated',
          action: 'item_added'
        }
      }
    },
    {
      name: 'payment_failed',
      payload: {
        custom: {
          integration: 'woocommerce',
          event_type: 'checkout_failed'
        }
      }
    }
  ]);

  const serialized = JSON.stringify(harness.calls.track);
  for (const forbidden of [
    'customer_id',
    'email',
    'product_id',
    'order_id',
    'cart_contents',
    'payment_method'
  ]) {
    assert.equal(serialized.includes(forbidden), false);
  }
});

test('does not emit WooCommerce events before required consent in disabled mode', () => {
  const harness = createHarness({
    consentRequired: true,
    noConsentMode: 'disabled',
    pageEvent: 'product_view'
  });

  harness.dispatchBody('wc-blocks_added_to_cart');
  assert.equal(harness.calls.track.length, 0);
  assert.deepEqual(harness.calls.consent, [false]);

  harness.dispatchConsent(true);
  harness.dispatchBody('wc-blocks_added_to_cart');

  assert.deepEqual(harness.calls.consent, [false, true]);
  assert.equal(harness.calls.track.length, 2);
  assert.equal(harness.calls.track[0].payload.custom.event_type, 'product_view');
  assert.equal(harness.calls.track[1].payload.custom.event_type, 'cart_updated');
});

test('respects event-category settings', () => {
  const harness = createHarness({
    pageEvent: 'cart_view',
    trackCartEvents: false,
    trackCheckoutEvents: false
  });

  harness.dispatchBody('wc-blocks_added_to_cart');
  harness.dispatchJquery('checkout_error');

  assert.deepEqual(harness.calls.track, []);
});
