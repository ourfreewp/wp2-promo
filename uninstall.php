<?php
// If uninstall not called from WordPress, exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Delete all campaign posts
$posts = get_posts([
    'post_type' => 'wp2_lead_campaign',
    'numberposts' => -1,
    'fields' => 'ids'
]);
foreach ($posts as $post_id) {
    wp_delete_post($post_id, true);
}

// Drop analytics table
if (class_exists('MetaBox\CustomTable\API')) {
    MetaBox\CustomTable\API::delete_table('wp2_lead_analytics');
}

// Delete settings
delete_option('wp2_lead_settings');
