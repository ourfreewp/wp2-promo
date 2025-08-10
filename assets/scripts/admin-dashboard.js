/**
 * WP2 Lead Admin Dashboard Script
 * Handles settings panel navigation, tooltips, modal accessibility, and Chart.js rendering.
 * @since 1.0.0
 */

document.addEventListener('DOMContentLoaded', function() {

	// --- Settings Panel Navigation & ARIA ---
	const settingsNavItems = document.querySelectorAll('.settings-nav-item');
	settingsNavItems.forEach(item => {
		item.addEventListener('click', (e) => {
			e.preventDefault();
			settingsNavItems.forEach(i => {
				i.classList.remove('active');
				i.setAttribute('aria-selected', 'false');
			});
			document.querySelectorAll('.wp2-settings-panel').forEach(p => p.classList.remove('active'));
			item.classList.add('active');
			item.setAttribute('aria-selected', 'true');
			const panel = document.getElementById('panel-' + item.dataset.panel);
			if(panel) {
				panel.classList.add('active');
				item.setAttribute('aria-controls', 'panel-' + item.dataset.panel);
			}
		});
	});

	// --- Tooltip Logic ---
	const tooltip = document.getElementById('tooltip');
	document.querySelectorAll('.info-icon').forEach(icon => {
		icon.addEventListener('mouseenter', showTooltip);
		icon.addEventListener('focus', showTooltip);
		icon.addEventListener('mouseleave', hideTooltip);
		icon.addEventListener('blur', hideTooltip);
	});
	function showTooltip(e) {
		if (!tooltip) return;
		const content = e.target.getAttribute('data-tooltip-content');
		tooltip.textContent = content;
		tooltip.style.display = 'block';
		const rect = e.target.getBoundingClientRect();
		tooltip.style.left = rect.left + window.scrollX + rect.width/2 + 'px';
		tooltip.style.top = rect.bottom + window.scrollY + 8 + 'px';
		tooltip.setAttribute('role', 'tooltip');
		tooltip.setAttribute('aria-live', 'polite');
	}
	function hideTooltip() {
		if (tooltip) tooltip.style.display = 'none';
	}

	// --- Modal Accessibility & Action ---
	const modal = document.getElementById('add-campaign-modal');
	const addBtn = document.getElementById('add-campaign-btn');
	const closeBtn = document.getElementById('close-modal-btn');
	const cancelBtn = document.getElementById('cancel-modal-btn');
	const createEditBtn = document.getElementById('create-edit-campaign-btn');

	const showModal = () => { if(modal) modal.style.display = 'flex'; };
	const hideModal = () => { if(modal) modal.style.display = 'none'; };

	if(addBtn) addBtn.addEventListener('click', showModal);
	if(closeBtn) closeBtn.addEventListener('click', hideModal);
	if(cancelBtn) cancelBtn.addEventListener('click', hideModal);
	if(modal) modal.addEventListener('click', (e) => {
		if (e.target === modal) hideModal();
	});
	if(modal) {
		modal.setAttribute('role', 'dialog');
		modal.setAttribute('aria-modal', 'true');
		modal.setAttribute('tabindex', '-1');
		modal.addEventListener('keydown', function(e) {
			if (e.key === 'Escape') hideModal();
		});
		// Focus trap for modal
		function trapFocus(element) {
			const focusableEls = element.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
			const firstFocusableEl = focusableEls[0];
			const lastFocusableEl = focusableEls[focusableEls.length - 1];
			element.addEventListener('keydown', function(e) {
				if (e.key === 'Tab') {
					if (e.shiftKey) {
						if (document.activeElement === firstFocusableEl) {
							lastFocusableEl.focus();
							e.preventDefault();
						}
					} else {
						if (document.activeElement === lastFocusableEl) {
							firstFocusableEl.focus();
							e.preventDefault();
						}
					}
				}
			});
		}
		trapFocus(modal);
	}
	if(createEditBtn) {
		createEditBtn.addEventListener('click', function(e) {
			e.preventDefault();
			// Example: AJAX call to create campaign (replace with real logic)
			// fetch('/wp-json/wp2-lead/v1/campaigns', { method: 'POST', body: ... })
			//   .then(res => res.json()).then(data => { ... });
			alert('Create & Edit action triggered!');
			hideModal();
		});
	}

	// --- Chart.js ---
	const mainCtx = document.getElementById('mainChart');
	if (mainCtx && typeof wp2LeadDashboardData !== 'undefined') {
		const chartData = {
			labels: wp2LeadDashboardData.mainChart.map(item => item.name),
			datasets: [{ 
				label: 'Leads', 
				data: wp2LeadDashboardData.mainChart.map(item => item.Leads), 
				fill: true, 
				backgroundColor: 'rgba(0, 122, 204, 0.1)', 
				borderColor: '#007ACC',
				tension: 0.4,
				pointBackgroundColor: '#007ACC',
				pointRadius: 4
			}]
		};
		const chartOptions = {
			responsive: true,
			maintainAspectRatio: false,
			plugins: { legend: { display: false } },
			scales: { 
				y: { 
					beginAtZero: true,
					grid: { color: 'rgba(0,0,0,0.05)' },
					ticks: { color: '#555555' }
				},
				x: {
					grid: { display: false },
					ticks: { color: '#555555' }
				}
			}
		};
		new Chart(mainCtx, { type: 'line', data: chartData, options: chartOptions });
	}

});
