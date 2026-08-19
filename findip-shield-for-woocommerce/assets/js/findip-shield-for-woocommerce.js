(function () {
  'use strict';

  var settings = window.findipShieldWooCommerceSettings || {};
  var shield = window.FindIP;
  var consentGranted = settings.consentRequired !== true;
  var pageEventSent = false;
  var lastCartSignal = '';
  var lastCartSignalAt = 0;

  if (!shield || !settings.siteKey) {
    return;
  }

  if (settings.consentRequired === true) {
    shield.setConsent(false);
  }

  shield.init({
    siteKey: settings.siteKey,
    privacyMode: settings.privacyMode || 'strict',
    autoTrack: settings.autoTrack !== false,
    autoDetectForms: settings.autoDetectForms !== false,
    consentRequired: settings.consentRequired === true,
    noConsentMode: settings.noConsentMode || 'strict',
    pushToDataLayer: true
  });

  function canTrack() {
    return (
      settings.consentRequired !== true ||
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
      return settings.trackProductViews !== false;
    }

    if (eventType === 'cart_view') {
      return settings.trackCartEvents !== false;
    }

    if (
      eventType === 'checkout_view' ||
      eventType === 'order_received'
    ) {
      return settings.trackCheckoutEvents !== false;
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

    if (settings.trackCartEvents === false || !canTrack()) {
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
        if (settings.trackCheckoutEvents !== false) {
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

