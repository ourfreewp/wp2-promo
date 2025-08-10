<?php
/**
 * Analytics Service Provider
 *
 * Handles recording and retrieving analytics events for WP2 Lead campaigns.
 *
 * @package WP2Lead\Services\Analytics
 */


namespace WP2Lead\Services\Analytics;

use MetaBox\CustomTable\API as MBCustomTableAPI;
use WP_Error;

class Provider {

    /**
     * Record an analytics event.
     *
     * @param int $campaign_id
     * @param string $event_type
     * @param array $meta
     * @return int|WP_Error Inserted row ID or WP_Error
     */
    public function record_event( $campaign_id, $event_type, $meta = [] ) {
        if ( ! class_exists( 'MetaBox\\CustomTable\\API' ) ) {
            return new WP_Error( 'mb_custom_table_missing', __( 'Meta Box Custom Table API not available.', 'wp2-lead' ) );
        }
        $data = [
            'campaign_id' => $campaign_id,
            'event_type'  => $event_type,
            'event_date'  => current_time( 'mysql', 1 ),
            'meta'        => !empty($meta) ? wp_json_encode($meta) : null,
        ];
        $result = MBCustomTableAPI::add( 'wp2_lead_analytics', $data );
        if ( ! $result ) {
            return new WP_Error( 'wp2_lead_db_error', __( 'Failed to record analytics event.', 'wp2-lead' ) );
        }
        return $result;
    }

    /**
     * Get analytics event data.
     *
     * @param array $args (Optional) Query args: campaign_id, event_type, date_from, date_to
     * @return array
     */
    public function get_analytics_data( $args = [] ) {
        if ( ! class_exists( 'MetaBox\\CustomTable\\API' ) ) {
            return new WP_Error( 'mb_custom_table_missing', __( 'Meta Box Custom Table API not available.', 'wp2-lead' ) );
        }
        $where = [];
        if ( isset( $args['campaign_id'] ) ) {
            $where['campaign_id'] = $args['campaign_id'];
        }
        if ( isset( $args['event_type'] ) ) {
            $where['event_type'] = $args['event_type'];
        }
        // Date range filtering
        if ( isset( $args['date_from'] ) || isset( $args['date_to'] ) ) {
            $where_sql = [];
            if ( isset( $args['date_from'] ) ) {
                $where_sql[] = "event_date >= '" . esc_sql( $args['date_from'] ) . "'";
            }
            if ( isset( $args['date_to'] ) ) {
                $where_sql[] = "event_date <= '" . esc_sql( $args['date_to'] ) . "'";
            }
            $where['__where'] = implode( ' AND ', $where_sql );
        }
        return MBCustomTableAPI::query( 'wp2_lead_analytics', [ 'where' => $where ] );
    }

    /**
     * Get aggregated analytics data (impressions, conversions, dismissals).
     *
     * @param int $campaign_id
     * @param array $args (Optional) Additional query args
     * @return array
     */
    public function get_aggregated_analytics( $campaign_id, $args = [] ) {
        global $wpdb;
        $table = $wpdb->prefix . 'wp2_lead_analytics';
        $sql = $wpdb->prepare(
            "SELECT event_type, COUNT(*) as count FROM $table WHERE campaign_id = %d GROUP BY event_type",
            $campaign_id
        );
        $results = $wpdb->get_results( $sql, ARRAY_A );
        if ( $results === null ) {
            return new WP_Error( 'wp2_lead_db_error', __( 'Failed to fetch analytics data.', 'wp2-lead' ) );
        }
        $counts = [
            'impression' => 0,
            'conversion' => 0,
            'dismissal'  => 0,
        ];
        foreach ( $results as $row ) {
            if ( isset( $counts[ $row['event_type'] ] ) ) {
                $counts[ $row['event_type'] ] = (int) $row['count'];
            }
        }
        return $counts;
    }
}
