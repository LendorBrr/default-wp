<?php
/**
 * Plugin Name: WC Date Time Picker
 * Plugin URI: https://github.com/LendorBrr/wp-wc-calendar
 * Description: Adds a date and time picker to WooCommerce products.
 * Version: 1.0
 * Author: OpenAI
 * Author URI: https://www.openai.com/
 * Text Domain: wc-date-time-picker
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('WC_Date_Time_Picker')) {
    class WC_Date_Time_Picker
    {
        public function __construct()
        {
            add_action('woocommerce_before_add_to_cart_button', array($this, 'add_date_time_picker'));
            add_filter('woocommerce_add_cart_item_data', array($this, 'add_date_time_picker_to_cart_item_data'), 10, 3);
            add_action('woocommerce_checkout_create_order_line_item', array($this, 'add_date_time_picker_to_order_items'), 10, 4);
            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
            add_action('wp', array($this, 'conditionally_add_validation_filter'));
        }

        public function add_date_time_picker()
        {
            echo '<div class="wc-date-time-picker">';
            echo '<label for="wc-date-picker">' . __('Date:', 'wc-date-time-picker') . '</label>';
            echo '<input type="text" id="wc-date-picker" name="wc_date" class="wc-date-picker" />';
            echo '<label for="wc-time-picker">' . __('Time:', 'wc-date-time-picker') . '</label>';
            echo '<input type="text" id="wc-time-picker" name="wc_time" class="wc-time-picker" />';
            echo '</div>';
        }

        public function add_date_time_picker_to_cart_item_data($cart_item_data, $product_id, $variation_id)
        {
            if (isset($_POST['wc_date']) && isset($_POST['wc_time'])) {
                $cart_item_data['wc_date_time'] = sanitize_text_field($_POST['wc_date']) . ' ' . sanitize_text_field($_POST['wc_time']);
                $cart_item_data['unique_key'] = md5(microtime().rand());
            }
            return $cart_item_data;
        }

        public function add_date_time_picker_to_order_items($item, $cart_item_key, $values, $order)
        {
            if (isset($values['wc_date_time'])) {
                $item->add_meta_data(__('Date & Time', 'wc-date-time-picker'), $values['wc_date_time']);
            }
        }

        public function enqueue_scripts()
        {
            wp_register_style('jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css');
            wp_enqueue_style('jquery-ui');
        }

        public function conditionally_add_validation_filter()
        {
            global $post;
            if (is_product()) {
                $current_product_id = get_the_ID();
                $product = wc_get_product($current_product_id);

                $allowed_products = get_option('wc_datetimepicker_products');
                $allowed_products = !empty($allowed_products) ? $allowed_products : array();

                if ($product && in_array($current_product_id, $allowed_products)) {
                    wp_enqueue_script('wc-date-time-picker', plugins_url('wc-date-time-picker.js', __FILE__), array('jquery'));
                    wp_localize_script('wc-date-time-picker', 'wc_date_time_picker_vars', array(
                        'ajax_url' => admin_url('admin-ajax.php'),
                        'product_id' => $current_product_id,
                        'allowed_products' => $allowed_products,
                    ));
                }
            }
        }
    }

    new WC_Date_Time_Picker();
}

