<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', function() {
    $labels = array(
        'name'               => 'Books',
        'singular_name'      => 'Book',
        'menu_name'          => 'Books',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'show_in_rest'       => true,
        'supports'           => array( 'title', 'editor', 'thumbnail' ),
        'rewrite'            => array( 'slug' => 'books' ),
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-book',
    );

    register_post_type( 'book', $args );
} );