<?php
namespace WP2Lead\Models\Analytics;
use MetaBox\CustomTable\API as MBCustomTableAPI;

class Registrar {
    private static $table_name = 'wp2_lead_analytics';

    public function __construct() {
        add_action('init', [$this, 'define_table_schema']);
    }

    public function define_table_schema() {
        if (!function_exists('mb_register_custom_table')) return;
        mb_register_custom_table(self::$table_name, [
            'id'          => ['type' => 'bigint', 'unsigned' => true, 'auto_increment' => true],
            'campaign_id' => ['type' => 'bigint', 'unsigned' => true],
            'variant_id'  => ['type' => 'varchar', 'length' => 255],
            'event_type'  => ['type' => 'varchar', 'length' => 50],
            'event_date'  => ['type' => 'datetime'],
            'meta'        => ['type' => 'text', 'null' => true],
        ]);
    }

    public static function create_table() {
        if (!class_exists('MetaBox\CustomTable\API')) return;
        MBCustomTableAPI::create(self::$table_name, [
            'campaign_id' => 'BIGINT NOT NULL',
            'variant_id'  => 'VARCHAR(255) NOT NULL',
            'event_type'  => 'VARCHAR(50) NOT NULL',
            'event_date'  => 'DATETIME NOT NULL',
            'meta'        => 'TEXT NULL',
        ], ['campaign_id', 'event_type']);
    }
}
