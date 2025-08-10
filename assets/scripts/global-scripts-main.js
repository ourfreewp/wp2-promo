
import { atom, onMount } from 'blockstudio/nanostores@0.9.0';
import { computePosition, offset, flip, shift, autoUpdate } from 'blockstudio/@floating-ui/dom@1.6.0';

// Create a persistent map store for dismissed campaigns using Nanostores.
const dismissedCampaigns = atom(JSON.parse(localStorage.getItem('wp2_lead_dismissed') || '{}'));

// Sync store changes back to localStorage whenever the store is updated.
onMount(dismissedCampaigns, () => {
	const unsubscribe = dismissedCampaigns.listen(value => {
		localStorage.setItem('wp2_lead_dismissed', JSON.stringify(value));
	});
	return () => unsubscribe();
});

class RulesEngine {
	constructor(rules) { this.rules = rules || []; }
	evaluate() {
		if (this.rules.length === 0) return true;
		return this.rules.every(rule => this.checkRule(rule));
	}
	checkRule(rule) {
		const url = new URL(window.location.href);
		const referrer = document.referrer ? new URL(document.referrer) : null;
		switch (rule.param) {
			case 'url_contains': return window.location.href.includes(rule.value);
			case 'url_param_is': const [key, val] = rule.value.split('='); return url.searchParams.get(key) === val;
			case 'referrer_is': return referrer && referrer.hostname.includes(rule.value);
			default: return true;
		}
	}
}

class TriggerEngine {
	constructor(triggerSettings, callback) {
		this.type = triggerSettings.type || 'load';
		this.scrollDepth = triggerSettings.scroll_depth || 50;
		this.callback = callback;
		this.fired = false;
		this.init();
	}
	init() {
		if (this.type === 'load') this.fire();
		else if (this.type === 'scroll') document.addEventListener('scroll', this.onScroll.bind(this), { passive: true });
		else if (this.type === 'exit') document.documentElement.addEventListener('mouseleave', this.onMouseLeave.bind(this), { once: true });
	}
	fire() { if (this.fired) return; this.fired = true; this.callback(); }
	onScroll() {
		const scrollPercent = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
		if (scrollPercent >= this.scrollDepth) { this.fire(); document.removeEventListener('scroll', this.onScroll.bind(this)); }
	}
	onMouseLeave(e) { if (e.clientY <= 0) this.fire(); }
}

class WP2Lead {
	constructor(element) {
		this.element = element;
		this.campaignId = this.element.dataset.wp2LeadCampaignId;
		this.campaign = null;
		this.selectedVariant = null;
		this.apiBase = window.wp2LeadData.rest_url;
		this.nonce = window.wp2LeadData.nonce;
		this.init();
	}

	async init() {
		if (this.isDismissed()) return;
		const response = await this.fetchCampaign();
		if (!response || !response.campaign) return;
		this.campaign = response.campaign;
		const rulesEngine = new RulesEngine(this.campaign.settings.targeting_rules);
		if (!rulesEngine.evaluate()) return;
		const triggerSettings = { type: this.campaign.settings.trigger_type, scroll_depth: this.campaign.settings.trigger_scroll_depth };
		new TriggerEngine(triggerSettings, () => {
			this.selectVariant();
			this.renderCampaign();
		});
	}

	isDismissed() {
		const allDismissed = dismissedCampaigns.get();
		const record = allDismissed[this.campaignId];
		return record && new Date().getTime() < record.expires;
	}

	dismissCampaign() {
		const days = this.campaign.settings.dismiss_for_days || 14;
		const expires = new Date().getTime() + (days * 24 * 60 * 60 * 1000);

		const currentDismissed = dismissedCampaigns.get();
		dismissedCampaigns.set({
			...currentDismissed,
			[this.campaignId]: { expires }
		});

		this.sendAnalytics('dismissal');
		if (this.campaignContainer) this.campaignContainer.remove();
	}

	async fetchCampaign() {
		try {
			const res = await fetch(`${this.apiBase}campaigns/${this.campaignId}`);
			if (!res.ok) throw new Error('Failed to fetch campaign');
			return await res.json();
		} catch (e) {
			console.error('WP2Lead Fetch Error:', e);
			this.notify('Error: Could not load campaign content.');
			return null;
		}
	}

	selectVariant() {
		const variants = this.campaign.variants || [];
		if (variants.length === 0) return;
		if (variants.length === 1) { this.selectedVariant = variants[0]; return; }
		const totalWeight = variants.reduce((sum, v) => sum + (parseInt(v.weight, 10) || 0), 0);
		let random = Math.random() * totalWeight;
		for (const variant of variants) {
			const weight = parseInt(variant.weight, 10) || 0;
			if (random < weight) { this.selectedVariant = variant; return; }
			random -= weight;
		}
		this.selectedVariant = variants[0];
	}

	renderCampaign() {
		if (!this.selectedVariant) return;
		this.campaignContainer = document.createElement('div');
		this.campaignContainer.className = `wp2-lead-campaign wp2-lead-position--${this.campaign.settings.position || 'center'}`;
		this.campaignContainer.innerHTML = this.selectedVariant.html;
		const closeButton = document.createElement('button');
		closeButton.className = 'wp2-lead-close-button';
		closeButton.innerHTML = '&times;';
		closeButton.setAttribute('aria-label', 'Close Campaign');
		closeButton.onclick = () => this.dismissCampaign();
		this.campaignContainer.prepend(closeButton);
		document.body.appendChild(this.campaignContainer);
		this.setConversionCookie();
		this.setupPositioning(this.campaignContainer, this.campaign.settings.position);
		this.sendAnalytics('impression');
		this.focusTrap();
	}

	setupPositioning(element, position) {
		const virtualEl = {
			getBoundingClientRect: () => ({
				width: 0, height: 0,
				x: window.innerWidth / 2, y: window.innerHeight / 2,
				top: window.innerHeight / 2, left: window.innerWidth / 2,
				right: window.innerWidth / 2, bottom: window.innerHeight / 2,
			})
		};
		if (position === 'top') virtualEl.getBoundingClientRect = () => ({ width: window.innerWidth, height: 0, x: 0, y: 0, top: 0, left: 0, right: window.innerWidth, bottom: 0 });
		if (position === 'bottom') virtualEl.getBoundingClientRect = () => ({ width: window.innerWidth, height: 0, x: 0, y: window.innerHeight, top: window.innerHeight, left: 0, right: window.innerWidth, bottom: window.innerHeight });

		autoUpdate(virtualEl, element, () => {
			computePosition(virtualEl, element, {
				placement: position === 'top' ? 'bottom' : (position === 'bottom' ? 'top' : 'center'),
				middleware: [offset(10), flip(), shift({ padding: 10 })]
			}).then(({ x, y }) => {
				Object.assign(element.style, { left: `${x}px`, top: `${y}px` });
			});
		});
	}

	async sendAnalytics(eventType) {
		try {
			await fetch(`${this.apiBase}analytics`, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': this.nonce },
				body: JSON.stringify({
					campaign_id: this.campaignId,
					variant_id: this.selectedVariant.id, // Use the now-guaranteed ID
					event_type: eventType,
				}),
			});
		} catch (e) { console.error('WP2Lead Analytics Error:', e); }
	}

	notify(message, duration = 3000) {
		const toast = document.createElement('div');
		toast.className = 'wp2-lead-toast';
		toast.textContent = message;
		document.body.appendChild(toast);
		setTimeout(() => toast.classList.add('is-visible'), 10);
		setTimeout(() => {
			toast.classList.remove('is-visible');
			toast.addEventListener('transitionend', () => toast.remove());
		}, duration);
	}

	focusTrap() {
		const focusable = this.campaignContainer.querySelectorAll('a[href], button, textarea, input, select, [tabindex]:not([tabindex="-1"])');
		if (focusable.length === 0) return;
		const first = focusable[0];
		const last = focusable[focusable.length - 1];
		this.campaignContainer.addEventListener('keydown', (e) => {
			if (e.key === 'Tab') {
				if (e.shiftKey) { if (document.activeElement === first) { last.focus(); e.preventDefault(); } }
				else { if (document.activeElement === last) { first.focus(); e.preventDefault(); } }
			} else if (e.key === 'Escape') { this.dismissCampaign(); }
		});
		first.focus();
	}

	setConversionCookie() {
		const formId = this.campaign.settings.linked_form_id;
		if (!formId) return;
		document.cookie = `wp2l_conversion_candidate=${JSON.stringify({ campaignId: this.campaignId, formId: formId })};path=/;SameSite=Lax`;
	}
}

document.addEventListener('DOMContentLoaded', () => {
	document.querySelectorAll('[data-wp2-lead-campaign-id]').forEach(el => new WP2Lead(el));
});
