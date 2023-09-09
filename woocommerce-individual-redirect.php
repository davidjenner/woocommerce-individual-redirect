<?php
/*
Plugin Name: WooCommerce Individual Product Redirect
Description: Adds a custom field to WooCommerce products for individual redirection after adding to the cart.
Version: 1.0
Author: David Jenner
*/

// Add custom field to product edit page
function add_custom_redirect_field() {
    woocommerce_wp_text_input(
        array(
            'id' => 'custom_redirect_url',
            'label' => __('Custom Redirect URL', 'woocommerce'),
            'placeholder' => __('Enter custom redirect URL', 'woocommerce'),
            'description' => __('Enter the URL to redirect to after adding this product to the cart.', 'woocommerce'),
        )
    );
}
add_action('woocommerce_product_options_general_product_data', 'add_custom_redirect_field');

// Save custom field data
function save_custom_redirect_field($post_id) {
    $custom_redirect_url = isset($_POST['custom_redirect_url']) ? esc_url_raw($_POST['custom_redirect_url']) : '';
    update_post_meta($post_id, 'custom_redirect_url', $custom_redirect_url);
}
add_action('woocommerce_process_product_meta', 'save_custom_redirect_field');

// Redirect after adding to cart
add_filter('woocommerce_add_to_cart_redirect', 'custom_redirect_after_add_to_cart');

function custom_redirect_after_add_to_cart($url) {
    global $woocommerce;
    $product_id = isset($_REQUEST['add-to-cart']) ? intval($_REQUEST['add-to-cart']) : 0;
    $custom_redirect_url = get_post_meta($product_id, 'custom_redirect_url', true);

    // If a custom URL is set for this product, use it for redirection
    if (!empty($custom_redirect_url)) {
        return $custom_redirect_url;
    } else {
        return $url; // Use the default redirect URL
    }
}
