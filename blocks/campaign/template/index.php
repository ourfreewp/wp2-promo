
<?php
$campaign_id = $attributes['campaignId'] ?? null;
$is_preview = $is_preview ?? false;

if (empty($campaign_id)) {
    if ($is_preview) {
        echo '<div class="wp2-lead-block-placeholder" style="padding:1rem; background:#f0f0f0; border:1px dashed #ccc;">' . esc_html__('Please select a campaign from the block settings panel.', 'wp2-lead') . '</div>';
    }
    return;
}

if (!$is_preview) {
    echo '<div data-wp2-lead-campaign-id="' . esc_attr($campaign_id) . '"></div>';
} else {
    echo '<div class="wp2-lead-block-placeholder" style="padding:1rem; background:#f0f0f0; border:1px solid #ccc;">' . sprintf(esc_html__('Displaying Campaign ID: %s', 'wp2-lead'), esc_html($campaign_id)) . '</div>';
}