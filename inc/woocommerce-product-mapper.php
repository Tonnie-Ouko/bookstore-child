<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Link Books to WooCommerce Products (run manually once)
 */
function bookstore_link_books_to_products_once() {
    
    if ( get_option( 'bookstore_books_linked' ) ) {
        return;
    }

    $books = get_posts(array(
        'post_type'      => 'book',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ));

    foreach ( $books as $book ) {
        $isbn  = get_field('isbn', $book->ID);
        $price = get_field('price', $book->ID);

        if ( empty($isbn) ) continue;

        // This runs if product already exists
        $existing_product_id = wc_get_product_id_by_sku($isbn);
        if ( $existing_product_id ) {
            update_post_meta($book->ID, '_linked_product_id', $existing_product_id);
            continue;
        }

        // Creation of a new WooCommerce product
        $product_id = wp_insert_post(array(
            'post_title'  => $book->post_title,
            'post_type'   => 'product',
            'post_status' => 'publish',
        ));

        if ( $product_id ) {
            update_post_meta($product_id, '_sku', $isbn);
            update_post_meta($product_id, '_price', $price);
            update_post_meta($product_id, '_regular_price', $price);
            update_post_meta($book->ID, '_linked_product_id', $product_id);
        }
    }

    // Save flag so it doesn’t run again automatically
    update_option('bookstore_books_linked', true);
}
// Run only when admin visits Tools > Import or manually triggers it
add_action('admin_init', 'bookstore_link_books_to_products_once');
