<?php

namespace WP2Lead\Admin\Analytics;

use WP2Lead\Services\Analytics\Provider as AnalyticsService;


class Dashboard {
    public function __construct() {
        add_action('admin_menu', [ $this, 'add_submenu' ]);
        add_action('admin_enqueue_scripts', [ $this, 'enqueue_assets' ]);
    }

    public function add_submenu() {
        add_submenu_page(
            'wp2-lead',
            __('Analytics', 'wp2-lead'),
            __('Analytics', 'wp2-lead'),
            'manage_options',
            'wp2-lead-analytics',
            [ $this, 'render_dashboard' ]
        );
    }

    public function enqueue_assets($hook) {
        if ($hook !== 'toplevel_page_wp2-lead-analytics' && $hook !== 'wp2-lead_page_wp2-lead-analytics') {
            return;
        }
        wp_enqueue_script(
            'wp2-lead-admin-analytics',
            WP2_LEAD_URL . 'assets/scripts/admin-analytics.js',
            [ 'chart-js' ],
            WP2_LEAD_VERSION,
            true
        );
        wp_localize_script(
            'wp2-lead-admin-analytics',
            'wp2LeadAdmin',
            [
                'rest_url' => rest_url('wp2-lead/v1/'),
                'nonce'    => wp_create_nonce('wp_rest'),
            ]
        );
        wp_enqueue_style(
            'wp2-lead-admin-analytics',
            WP2_LEAD_URL . 'assets/styles/admin-analytics.css',
            [],
            WP2_LEAD_VERSION
        );
        // Chart.js from CDN if not already registered
        if (!wp_script_is('chart-js', 'registered')) {
            wp_register_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', [], null, true);
        }
    }

    public function render_dashboard() {
        // Campaign selector and chart container
        $campaigns = get_posts([
            'post_type' => 'wp2_lead_campaign',
            'numberposts' => -1,
            'fields' => 'ids',
        ]);
        echo '<div class="wrap"><h1>' . esc_html__('WP2 Lead Analytics', 'wp2-lead') . '</h1>';
        echo '<label for="wp2-lead-campaign-selector">' . esc_html__('Select Campaign:', 'wp2-lead') . '</label> ';
        echo '<select id="wp2-lead-campaign-selector"><option value="">' . esc_html__('Choose...', 'wp2-lead') . '</option>';
        foreach ($campaigns as $cid) {
            $title = get_the_title($cid);
            echo '<option value="' . esc_attr($cid) . '">' . esc_html($title) . '</option>';
        }
        echo '</select>';
        echo '<canvas id="wp2-lead-analytics-chart" width="600" height="300"></canvas>';
        echo '</div>';
    }
}
