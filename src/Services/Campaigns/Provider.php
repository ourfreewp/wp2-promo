<?php


namespace WP2Lead\Services\Campaigns;

use WP_Error;
if ( ! function_exists('__') ) {
    function __( $text, $domain = 'default' ) { return $text; }
}

if ( ! defined( 'ABSPATH' ) ) exit;

class Provider {
    /**
     * Check if the current user can view a campaign.
     * @param int $id
     * @param string|null $nonce
     * @return bool
     */
    /**
     * Check if the current user can view a campaign.
     * @param int $id
     * @param string|null $nonce
     * @return bool
     */
    public function can_view_campaign( $id, $nonce ) {
        $post = function_exists('get_post') ? get_post($id) : null;
        if ( ! $post || $post->post_status !== 'publish' ) {
            return false;
        }
        // Accept nonce from X-WP-Nonce header or parameter
        if ( empty( $nonce ) && isset( $_SERVER['HTTP_X_WP_NONCE'] ) ) {
            $nonce = $_SERVER['HTTP_X_WP_NONCE'];
        }
        return function_exists('wp_verify_nonce') ? wp_verify_nonce( $nonce, 'wp_rest' ) : false;
    }

    public function get_campaign_data(int $id) {
        $post = get_post($id);
        if (!$post || 'wp2_lead_campaign' !== $post->post_type || 'publish' !== $post->post_status) {
            return new WP_Error('campaign_not_found', __('Campaign not found or is not published.', 'wp2-lead'), ['status' => 404]);
        }
        $variants = rwmb_meta('wp2l_variants', ['object_type' => 'post'], $id);
        // Auto-assign a unique ID to variants if one is not provided.
        foreach ($variants as $i => &$variant) {
            if (empty($variant['id'])) {
                $variant['id'] = 'variant_' . $i;
            }
        }
        return [
            'campaign' => [
                'id' => $id,
                'title' => $post->post_title,
                'variants' => $variants,
                'settings' => [
                    'position' => rwmb_meta('wp2l_position', ['object_type' => 'post'], $id),
                    'dismiss_for_days' => rwmb_meta('wp2l_dismiss_for_days', ['object_type' => 'post'], $id),
                    'linked_form_id' => rwmb_meta('wp2l_linked_form_id', ['object_type' => 'post'], $id),
                    'trigger_type' => rwmb_meta('wp2l_trigger_type', ['object_type' => 'post'], $id),
                    'trigger_scroll_depth' => rwmb_meta('wp2l_trigger_scroll_depth', ['object_type' => 'post'], $id),
                    'targeting_rules' => rwmb_meta('wp2l_targeting_rules', ['object_type' => 'post'], $id),
                ]
            ]
        ];
    }
}
