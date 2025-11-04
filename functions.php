<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Theme Setup
 */
add_action( 'after_setup_theme', function() {
    // Enable WooCommerce compatibility
    add_theme_support( 'woocommerce' );
});

/**
 * Enqueue Styles
 * Ensures parent + child theme styles load properly
 */
add_action( 'wp_enqueue_scripts', function() {

    // Change this to your actual parent theme handle
    $parent_style = 'twentytwentyfour-style';

    // Load parent theme stylesheet
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );

    // Load child theme stylesheet (depends on parent)
    wp_enqueue_style(
        'bookstore-child-style',
        get_stylesheet_uri(),
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
});

/**
 * Includes
 * Keep modular and organized
 */
require_once get_stylesheet_directory() . '/inc/cpt-book.php';
require_once get_stylesheet_directory() . '/inc/acf-local-book.php';
require_once get_stylesheet_directory() . '/inc/importer-books.php';
require_once get_stylesheet_directory() . '/inc/woocommerce-product-mapper.php';