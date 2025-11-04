<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'acf/init', function() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) return;

    acf_add_local_field_group(array(
        'key' => 'group_books_fields',
        'title' => 'Book Fields',
        'fields' => array(
            array(
                'key' => 'field_book_title',
                'label' => 'Book Title',
                'name' => 'book_title',
                'type' => 'text',
            ),
            array(
                'key' => 'field_author',
                'label' => 'Author',
                'name' => 'author',
                'type' => 'text',
            ),
            array(
                'key' => 'field_isbn',
                'label' => 'ISBN',
                'name' => 'isbn',
                'type' => 'text',
            ),
            array(
                'key' => 'field_price',
                'label' => 'Price',
                'name' => 'price',
                'type' => 'number',
                'step' => '0.01',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'book',
                ),
            ),
        ),
    ));
});