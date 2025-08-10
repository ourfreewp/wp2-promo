<?php
namespace WP2Lead\Models\Campaigns;

if ( ! defined( 'ABSPATH' ) ) exit;

class Registrar {
    public function __construct() {
        add_action('init', [$this, 'register_post_type']);
        add_filter('rwmb_meta_boxes', [$this, 'register_meta_boxes']);
    }

    public function register_post_type() {
        $labels = [
            'name' => _x('Campaigns', 'Post Type General Name', 'wp2-lead'),
            'singular_name' => _x('Campaign', 'Post Type Singular Name', 'wp2-lead'),
            'menu_name' => __('Campaigns', 'wp2-lead'),
            'name_admin_bar' => __('Campaign', 'wp2-lead'),
            'archives' => __('Campaign Archives', 'wp2-lead'),
            'attributes' => __('Campaign Attributes', 'wp2-lead'),
            'parent_item_colon' => __('Parent Campaign:', 'wp2-lead'),
            'all_items' => __('All Campaigns', 'wp2-lead'),
            'add_new_item' => __('Add New Campaign', 'wp2-lead'),
            'add_new' => __('Add New', 'wp2-lead'),
            'new_item' => __('New Campaign', 'wp2-lead'),
            'edit_item' => __('Edit Campaign', 'wp2-lead'),
            'update_item' => __('Update Campaign', 'wp2-lead'),
            'view_item' => __('View Campaign', 'wp2-lead'),
            'view_items' => __('View Campaigns', 'wp2-lead'),
            'search_items' => __('Search Campaign', 'wp2-lead'),
            'not_found' => __('Not found', 'wp2-lead'),
            'not_found_in_trash' => __('No campaigns found in Trash', 'wp2-lead'),
        ];
        $args = [
            'label' => __('Campaign', 'wp2-lead'),
            'description' => __('Lead capture campaigns.', 'wp2-lead'),
            'labels' => $labels,
            'supports' => ['title', 'revisions'],
            'hierarchical' => false,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => 'wp2-lead',
            'menu_position' => 5,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'capability_type' => 'post',
            'show_in_rest' => true,
            'rest_base' => 'campaigns',
        ];
        register_post_type('wp2_lead_campaign', $args);
    }

    public function register_meta_boxes(array $meta_boxes): array {
        $meta_boxes[] = [
            'id'         => 'campaign_configuration',
            'title'      => __('Campaign Configuration', 'wp2-lead'),
            'post_types' => ['wp2_lead_campaign'],
            'context'    => 'normal',
            'priority'   => 'high',
            'fields'     => [
                [
                    'id'   => 'wp2l_variants',
                    'name' => __('Variants (A/B Test)', 'wp2-lead'),
                    'type' => 'group',
                    'clone' => true, 'sort_clone' => true,
                    'fields' => [
                        ['id' => 'id', 'name' => __('Variant ID', 'wp2-lead'), 'type' => 'text', 'desc' => 'A unique ID for this variant (e.g., "promo_banner_blue"). Auto-generated if left blank.'],
                        ['id' => 'html', 'name' => __('HTML Content', 'wp2-lead'), 'type' => 'textarea', 'rows' => 5],
                        ['id' => 'weight', 'name' => __('Weight', 'wp2-lead'), 'type' => 'number', 'std' => 50, 'min' => 0],
                    ],
                ],
                [
                    'id'   => 'wp2l_position',
                    'name' => __('Display Position', 'wp2-lead'),
                    'type' => 'select', 'std'  => 'center',
                    'options' => [
                        'center' => __('Center Modal', 'wp2-lead'),
                        'top' => __('Top Bar', 'wp2-lead'),
                        'bottom' => __('Bottom Bar', 'wp2-lead'),
                    ],
                ],
                [
                    'id'   => 'wp2l_dismiss_for_days',
                    'name' => __('Dismissal Duration (days)', 'wp2-lead'),
                    'type' => 'number', 'std'  => 14, 'min'  => 0,
                ],
                [
                    'id'   => 'wp2l_linked_form_id',
                    'name' => __('Track Conversions on this WSForm', 'wp2-lead'),
                    'type' => 'post', 'post_type' => 'ws-form',
                    'field_type' => 'select_advanced',
                    'placeholder' => __('Select a Form', 'wp2-lead'),
                ],
                [
                    'id' => 'wp2l_trigger_type',
                    'name' => __('Trigger', 'wp2-lead'),
                    'type' => 'select', 'std' => 'load',
                    'options' => [
                        'load' => __('On Page Load', 'wp2-lead'),
                        'scroll' => __('After Scrolling', 'wp2-lead'),
                        'exit' => __('On Exit Intent', 'wp2-lead'),
                    ],
                ],
                [
                    'id' => 'wp2l_trigger_scroll_depth',
                    'name' => __('Scroll Depth (%)', 'wp2-lead'),
                    'type' => 'number', 'std' => 50, 'min' => 1, 'max' => 100,
                    'visible' => ['wp2l_trigger_type', '=', 'scroll'],
                ],
                [
                    'id'   => 'wp2l_targeting_rules',
                    'name' => __('Targeting Rules', 'wp2-lead'),
                    'type' => 'group', 'clone' => true,
                    'fields' => [
                        [
                            'id' => 'param', 'name' => __('Condition', 'wp2-lead'), 'type' => 'select',
                            'options' => [
                                'url_contains' => __('URL Contains', 'wp2-lead'),
                                'url_param_is' => __('URL Parameter Is', 'wp2-lead'),
                                'referrer_is' => __('Referring Domain Is', 'wp2-lead'),
                            ],
                        ],
                        [ 'id' => 'value', 'name' => __('Value', 'wp2-lead'), 'type' => 'text' ],
                    ],
                ],
            ],
        ];
        return $meta_boxes;
    }
}
