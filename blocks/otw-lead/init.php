<?php
/**
 * Conditionally renders the popover creative on the front page with custom data.
 *
 * This refactored version uses a class to encapsulate the block's logic,
 * ensuring data consistency and a more robust execution flow.
 */
class OTW_Lead_Placement {

    private $attributes;

    public function __construct() {
        // Define attributes once to avoid duplication and ensure consistency.
        $this->attributes = [
            'content'   => [
                'headline'   => 'Sign up for the OTW Bass Tournament 2025!',
                'ctaText'    => 'Register Now',
                'ctaUrl'     => 'https://store.onthewater.com/collections/media/products/2025-otw-bass-fall-brawl-registration',
                'media'      => 'https://onthewater.com/wp-content/uploads/2025/08/OTW_Bass_Tournament_2025_600x250.jpg',
                'mediaSmall' => 'https://onthewater.com/wp-content/uploads/2025/08/OTW_Bass_Tournament_2025_Signup.jpg',
            ],
            'trigger'   => [
                'type'           => 'delay',
                'delayInSeconds' => 5,
            ],
            'targeting' => [
                'audience'     => 'new',
                'frequencyCap' => '1',
                'campaignId'   => 'tournament-2025',
            ],
        ];

        // Hook into WordPress actions.
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets'], 100);
        add_action('wp_footer', [$this, 'render_block'], 200);
    }

    public function enqueue_assets() {
        if (!is_front_page()) {
            return;
        }

        // Register and enqueue a minimal script to act as the container for the localized data.
        // The main script is assumed to be handled by an autoloader.
        wp_register_script(
            'otw-lead-placement-data-script',
            '', // An empty string is used as we only need the handle for localization.
            [],
            '1.0.0',
            true
        );

        wp_enqueue_script('otw-lead-placement-data-script');

        // Use wp_localize_script to safely pass the PHP data to the JavaScript file.
        // It is attached to our new data-only script handle.
        wp_localize_script(
            'otw-lead-placement-data-script',
            'otwLeadPlacement',
            [ 'attributes' => $this->attributes ]
        );
    }

    public function render_block() {
        if (!is_front_page()) {
            return;
        }

        $block_id = 'otw-lead/placement';

        // Render the block using the class-level attributes.
        bs_render_block([
            'id'   => $block_id,
            'data' => $this->attributes,
        ]);
    }
}

// Instantiate the class to run the code.
add_action('init', function() {
    new OTW_Lead_Placement();
}, 200);
