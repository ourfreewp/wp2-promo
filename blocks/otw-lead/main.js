// File: assets/js/main.js
(function () {
  'use strict';

  // Declare DOM element variables in a wider scope for caching.
  let creative, backdrop, closeBtn, ctaLink, focusableElements;

  if (
    typeof window === 'undefined' ||
    !window.otwLeadPlacement ||
    !window.otwLeadPlacement.attributes
  ) {
    console.error('OTW Popover: Required data missing (window.otwLeadPlacement.attributes). Abort.');
    return;
  }

  console.log('OTW Popover script loaded.');

  const { attributes } = window.otwLeadPlacement;
  console.log('OTW Popover: Localized attributes found:', attributes);
  
  // Helper function to centralize Fathom event tracking.
  const trackFathomEvent = (eventName) => {
    if (window.fathom) {
      const campaignId = attributes.targeting.campaignId;
      window.fathom.trackEvent(`otw-lead:${campaignId}:${eventName}`);
    }
  };

  class SessionManager {
    constructor(storageKey = 'otw_lead_impressions') {
      this.storageKey = storageKey;
      this.sessionImpressions = new Set();
    }

    getImpressions() {
      try {
        const stored = localStorage.getItem(this.storageKey);
        // Ensure the stored data is a valid object before parsing.
        return stored ? JSON.parse(stored) : {};
      } catch (e) {
        return {};
      }
    }

    recordImpression(campaignId) {
      const impressions = this.getImpressions();
      impressions[campaignId] = Date.now();
      localStorage.setItem(this.storageKey, JSON.stringify(impressions));
      this.sessionImpressions.add(campaignId);
      console.log(`OTW Popover: Recorded impression for campaign ID "${campaignId}".`);
    }

    canShow(campaignId, capRule) {
      console.log(
        `OTW Popover: Checking if creative can be shown for campaign ID "${campaignId}" with cap rule "${capRule}".`
      );
      
      // Simplified check for 'no cap' values.
      if (!capRule || capRule === '0' || capRule === '0s') {
        return true;
      }

      // Handle session-based caps, including the string '1' as requested.
      if (capRule === 'session' || capRule === '1') {
        const hasSessionImpression = sessionStorage.getItem(`otw_lead_session_${campaignId}`);
        if (hasSessionImpression) {
          console.log('OTW Popover: Frequency cap rule is "session" or "1" and an impression has already been recorded.');
          return false;
        } else {
          return true;
        }
      }

      const capSeconds = parseInt(capRule, 10);
      if (isNaN(capSeconds) || capSeconds <= 0) {
        return true;
      }

      const impressions = this.getImpressions();
      const lastImpression = impressions[campaignId];
      if (!lastImpression) {
        console.log('OTW Popover: No previous impression found. Creative can be shown.');
        return true;
      }

      const timeSince = (Date.now() - lastImpression) / 1000;
      const canShow = timeSince > capSeconds;
      console.log(
        `OTW Popover: Last impression was ${timeSince.toFixed(2)} seconds ago. Can show: ${canShow}.`
      );
      return canShow;
    }

    clear() {
      localStorage.removeItem(this.storageKey);
      this.sessionImpressions.clear();
      console.log('OTW Popover: All session impressions cleared from local storage.');
    }
  }

  let timeoutId = null;
  
  // Implements a focus trap for accessibility.
  function trapFocus(e) {
    if (e.key === 'Tab') {
      const firstFocusable = focusableElements[0];
      const lastFocusable = focusableElements[focusableElements.length - 1];

      if (e.shiftKey) { // Shift + Tab
        if (document.activeElement === firstFocusable) {
          lastFocusable.focus();
          e.preventDefault();
        }
      } else { // Tab
        if (document.activeElement === lastFocusable) {
          firstFocusable.focus();
          e.preventDefault();
        }
      }
    }
  }

  function showCreative() {
    if (!creative || !backdrop) {
      console.error('OTW Popover: Missing required DOM nodes.');
      return;
    }

    if (creative.classList.contains('is-visible')) {
      console.log('OTW Popover: Creative is already visible, ignoring show call.');
      return;
    }

    backdrop.classList.add('is-visible');
    creative.classList.add('is-visible');

    const firstFocusable = focusableElements[0];
    if (firstFocusable) {
      firstFocusable.focus();
    }
    
    // Add event listener for focus trap
    creative.addEventListener('keydown', trapFocus);
    
    // Record the impression now that the creative is actually visible.
    const capRule = attributes.targeting.frequencyCap;
    const campaignId = attributes.targeting.campaignId;
    const sessionManager = new SessionManager();
    
    if (capRule && (capRule === 'session' || capRule === '1')) {
      sessionStorage.setItem(`otw_lead_session_${campaignId}`, 'true');
    } else if (capRule && capRule !== '0' && capRule !== '0s') {
      sessionManager.recordImpression(campaignId);
    }

    console.log('OTW Popover: Creative is now visible.');
    trackFathomEvent('popover displayed');
  }

  function hideCreative() {
    if (!creative || !backdrop) {
      return;
    }

    if (!creative.classList.contains('is-visible')) {
      return;
    }

    backdrop.classList.remove('is-visible');
    creative.classList.remove('is-visible');
    
    // Remove event listener for focus trap
    creative.removeEventListener('keydown', trapFocus);

    try {
      if (document.activeElement) {
        document.activeElement.blur();
      }
    } catch (e) {}

    console.log('OTW Popover: Creative is now hidden.');
    trackFathomEvent('popover closed');
  }

  function setupTrigger(triggerType, delayInSeconds) {
    console.log(
      `OTW Popover: Setting up trigger. Type: ${triggerType}, Delay: ${delayInSeconds} seconds.`
    );
    if (timeoutId) {
      clearTimeout(timeoutId);
    }

    const campaignId = attributes.targeting.campaignId;
    const sessionManager = new SessionManager();
    const capRule = attributes.targeting.frequencyCap;

    if (!sessionManager.canShow(campaignId, capRule)) {
      console.log('OTW Popover: Creative cannot be shown due to frequency capping. Aborting trigger setup.');
      trackFathomEvent('trigger aborted');
      return;
    }
    
    trackFathomEvent('popover triggered');

    switch (triggerType) {
      case 'delay':
        timeoutId = setTimeout(showCreative, (delayInSeconds || 0) * 1000);
        break;
      case 'manual':
      default:
        break;
    }
  }

  function initializeCreative() {
    console.log('OTW Popover: Initializing creative.');
    
    // Cache DOM elements here.
    creative = document.getElementById('otw-lead-popover');
    backdrop = document.getElementById('otw-lead-backdrop');
    closeBtn = document.getElementById('otw-lead-popover-close-btn');
    ctaLink = document.querySelector('.otw-lead-cta-link');

    if (!closeBtn || !backdrop || !ctaLink || !creative) {
      console.error('OTW Popover: One or more required DOM elements not found. Popover cannot be initialized.');
      return;
    }

    // Get all focusable elements within the modal.
    focusableElements = Array.from(creative.querySelectorAll(
      'button,[href],input,select,textarea,[tabindex]:not([tabindex="-1"])'
    ));

    closeBtn.addEventListener('click', hideCreative);
    backdrop.addEventListener('click', hideCreative);

    if (ctaLink) {
      ctaLink.addEventListener('click', () => {
        trackFathomEvent('cta clicked');
        console.log(`OTW Popover: CTA click tracked for campaign ID "${attributes.targeting.campaignId}".`);
      });
    }

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && creative.classList.contains('is-visible')) {
        hideCreative();
      }
    });

    setupTrigger(attributes.trigger.type, attributes.trigger.delayInSeconds);
    trackFathomEvent('popover loaded');
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeCreative);
  } else {
    initializeCreative();
  }
})();
