<?php
/**
 * Plugin Name: JSON AJAX Search
 * Description: Simple ajax search filter from a json sample file
 * Version: 1.0
 * Author: Israel Mateo Manzano
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define('JSON_AJAX_SEARCH_PATH', plugin_dir_path(__FILE__));
define('JSON_AJAX_SEARCH_URL', plugin_dir_url(__FILE__));

require_once JSON_AJAX_SEARCH_PATH . 'includes/class-json-ajax-search.php';

function json_ajax_search_init() {
    new JSON_AJAX_Search();
}

add_action('plugins_loaded', 'json_ajax_search_init');
