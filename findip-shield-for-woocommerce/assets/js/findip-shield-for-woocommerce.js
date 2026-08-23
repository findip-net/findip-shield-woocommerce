(function () {
  'use strict';

  var settings = window.findipShieldWooCommerceSettings || {};
  var shield = window.FindIP;

  // wp_localize_script casts booleans to strings ("1" / ""), so every flag
  // must be normalized before comparison.
  function flag(value, fallback) {
    if (value === true || value === '1' || value === 1) {
      return true;
    }

    if (value === false || value === '' || value === '0' || value === 0) {
      return false;
    }

    return fallback;
  }

  var consentRequired = flag(settings.consentRequired, false);
  var autoTrack = flag(settings.autoTrack, true);
  var autoDetectForms = flag(settings.autoDetectForms, true);
  var trackProductViews = flag(settings.trackProductViews, true);
  var trackCartEvents = flag(settings.trackCartEvents, true);
  var trackCheckoutEvents = flag(settings.trackCheckoutEvents, true);
  var consentGranted = !consentRequired;
  var pageEventSent = false;
  var lastCartSignal = '';
  var lastCartSignalAt = 0;

  if (!shield || !settings.siteKey) {
    return;
  }

  if (consentRequired) {
    shield.setConsent(false);
  }

  shield.init({
    siteKey: settings.siteKey,
    privacyMode: settings.privacyMode || 'strict',
    autoTrack: autoTrack,
    autoDetectForms: autoDetectForms,
    consentRequired: consentRequired,
    noConsentMode: settings.noConsentMode || 'strict',
    integration: settings.integration || 'woocommerce',
    pushToDataLayer: true
  });

  function canTrack() {
    return (
      !consentRequired ||
      consentGranted ||
      settings.noConsentMode !== 'disabled'
    );
  }

  function safeTrack(eventName, eventType, action) {
    var custom;
    var result;

    if (!canTrack()) {
      return;
    }

    custom = {
      integration: 'woocommerce',
      event_type: eventType
    };

    if (action) {
      custom.action = action;
    }

    try {
      result = shield.track(eventName, { custom: custom });
      if (result && typeof result.catch === 'function') {
        result.catch(function () {});
      }
    } catch (error) {
      // Storefront behavior must not depend on telemetry availability.
    }
  }

  function pageEventEnabled(eventType) {
    if (eventType === 'product_view') {
      return trackProductViews;
    }

    if (eventType === 'cart_view') {
      return trackCartEvents;
    }

    if (
      eventType === 'checkout_view' ||
      eventType === 'order_received'
    ) {
      return trackCheckoutEvents;
    }

    return false;
  }

  function trackPageEvent() {
    if (
      pageEventSent ||
      !settings.pageEvent ||
      !pageEventEnabled(settings.pageEvent) ||
      !canTrack()
    ) {
      return;
    }

    pageEventSent = true;
    safeTrack('custom', settings.pageEvent);
  }

  function trackCartSignal(action) {
    var now = Date.now();
    var signal = 'cart_updated:' + action;

    if (!trackCartEvents || !canTrack()) {
      return;
    }

    // Classic and Blocks integrations can emit equivalent events together.
    if (lastCartSignal === signal && now - lastCartSignalAt < 500) {
      return;
    }

    lastCartSignal = signal;
    lastCartSignalAt = now;
    safeTrack('custom', 'cart_updated', action);
  }

  document.addEventListener('findip:consent', function (event) {
    var detail = event.detail || {};

    consentGranted = detail.granted === true;
    shield.setConsent(consentGranted);

    if (consentGranted) {
      trackPageEvent();
    }
  });

  trackPageEvent();

  if (window.jQuery) {
    window.jQuery(document.body)
      .on('added_to_cart', function () {
        trackCartSignal('item_added');
      })
      .on('removed_from_cart', function () {
        trackCartSignal('item_removed');
      })
      .on('checkout_error', function () {
        if (trackCheckoutEvents) {
          safeTrack('payment_failed', 'checkout_failed');
        }
      });
  }

  if (document.body && document.body.addEventListener) {
    document.body.addEventListener('wc-blocks_added_to_cart', function () {
      trackCartSignal('item_added');
    });

    document.body.addEventListener('wc-blocks_removed_from_cart', function () {
      trackCartSignal('item_removed');
    });
  }
})();
