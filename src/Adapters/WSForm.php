<?php
namespace WP2Lead\Adapters;

class WSForm {
    /**
     * Check if WSForm integration is enabled.
     */
    public static function is_enabled(): bool {
        // Example: check plugin settings or constants
        return (bool) get_option('wp2_lead_wsform_enabled', false);
    }

    /**
     * Inject WSForm config into client config array.
     */
    public static function inject_client_config(array $config): array {
        if (self::is_enabled()) {
            $config['wsform_enabled'] = true;
            // Add more config as needed
        }
        return $config;
    }
}
