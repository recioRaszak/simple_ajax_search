<?php

class JSON_AJAX_Search {
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_shortcode('json_ajax_search', array($this, 'shortcode'));
        add_action('wp_ajax_json_ajax_search', array($this, 'ajax_search'));
        add_action('wp_ajax_nopriv_json_ajax_search', array($this, 'ajax_search'));
    }

    public function enqueue_scripts() {
        wp_enqueue_style('json-ajax-search', JSON_AJAX_SEARCH_URL . 'assets/css/json-ajax-search.css', array(), '1.0');
        wp_enqueue_script('json-ajax-search', JSON_AJAX_SEARCH_URL . 'assets/js/json-ajax-search.js', array('jquery'), '1.0', true);
        wp_localize_script('json-ajax-search', 'json_ajax_search_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('json_ajax_search_nonce')
        ));
    }

    public function shortcode() {
        ob_start();
        ?>
        <div id="json-ajax-search">
            <form id="json-ajax-search-form">
                <input type="text" name="name" placeholder="Name">
                <input type="text" name="surname1" placeholder="Surname 1">
                <input type="text" name="surname2" placeholder="Surname 2">
                <input type="email" name="email" placeholder="Email">
                <button type="submit">Search</button>
            </form>
            <div id="json-ajax-search-results"></div>
            <div id="json-ajax-search-pagination"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function ajax_search() {
        check_ajax_referer('json_ajax_search_nonce', 'nonce');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $per_page = 5;

        $json_file = JSON_AJAX_SEARCH_URL . 'db.json';
        $json_data = file_get_contents($json_file);
        $data = json_decode($json_data, true);

        if (!is_array($data)) {
            wp_send_json_error('Invalid JSON data');
            return;
        }

        // Debug: Log the original data
        error_log('Original data: ' . print_r($data, true));

        $filtered_data = $this->filter_data($data['usuarios'], $_POST);

        // Debug: Log the filtered data
        error_log('Filtered data: ' . print_r($filtered_data, true));

        $total_items = count($filtered_data);
        $total_pages = max(1, ceil($total_items / $per_page));

        $paginated_data = array_slice($filtered_data, ($page - 1) * $per_page, $per_page);

        $response = array(
            'data' => $paginated_data,
            'total_pages' => $total_pages,
            'current_page' => $page,
            'total_items' => $total_items, // Add this for debugging
        );

        // Debug: Log the response
        error_log('Response: ' . print_r($response, true));

        wp_send_json_success($response);
    }

    private function filter_data($data, $filters) {
        return array_filter($data, function($item) use ($filters) {
            foreach ($filters as $key => $value) {
                if ($key === 'action' || $key === 'nonce' || $key === 'page') {
                    continue; // Skip non-filter fields
                }
                if (!empty($value) && isset($item[$key]) && stripos($item[$key], $value) === false) {
                    return false;
                }
            }
            return true;
        });
    }
}
