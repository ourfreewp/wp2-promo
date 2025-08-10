<?php
/*
 * REST Controller Interface
 */
namespace WP2Lead\Interfaces\REST;

if ( ! defined( 'ABSPATH' ) ) exit;

interface Controller {
    /**
     * Register REST API routes.
     */
    public function register_routes(): void;
}