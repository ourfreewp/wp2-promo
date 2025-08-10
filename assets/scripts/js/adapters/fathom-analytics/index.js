// Fathom Analytics Adapter
// Usage: import and call trackFathomEvent(eventName, value)

export function trackFathomEvent(eventName, value = null) {
  if (typeof window.fathom === 'undefined' || typeof window.fathom.trackEvent !== 'function') return;
  if (value !== null) {
    window.fathom.trackEvent(eventName, { _value: value });
  } else {
    window.fathom.trackEvent(eventName);
  }
}
