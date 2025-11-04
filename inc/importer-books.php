<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Helper: Create or update WooCommerce product for a Book
 */
function bookstore_sync_book_to_product($book_id, $title, $isbn, $price) {
    if ( ! function_exists('wc_get_product_id_by_sku') ) return;

    // Check if product exists
    $product_id = wc_get_product_id_by_sku($isbn);

    if ( $product_id ) {
        // ✅ Update existing product
        $product = wc_get_product($product_id);
        $product->set_name($title);
        $product->set_regular_price($price);
        $product->save();
    } else {
        // 🆕 Create new product
        $product = new WC_Product_Simple();
        $product->set_name($title);
        $product->set_sku($isbn);
        $product->set_regular_price($price);
        $product->set_status('publish');
        $product_id = $product->save();
    }

    // Link Book → Product
    update_post_meta($book_id, '_linked_product_id', $product_id);
}

/**
 * Safe book importer — checks for existing ISBNs before creating.
 * Re-runnable: imports only new entries.
 */
function bookstore_import_books_safe() {
    $csv_path = get_stylesheet_directory() . '/data/books.csv';
    if ( ! file_exists( $csv_path ) ) {
        return; // no file, silently skip
    }

    $handle = fopen( $csv_path, 'r' );
    if ( ! $handle ) return;

    $header = fgetcsv( $handle ); // skip first row

    while ( ( $row = fgetcsv( $handle ) ) !== false ) {
        list($title, $author, $isbn, $price) = $row;

        // Check if a book with same ISBN exists
        $existing = new WP_Query(array(
            'post_type'  => 'book',
            'meta_query' => array(array(
                'key'   => 'isbn',
                'value' => $isbn,
            )),
        ));

        if ( $existing->have_posts() ) {
            wp_reset_postdata();
            continue; // skip existing book
        }

        // Insert new book
        $post_id = wp_insert_post(array(
            'post_title'  => $title,
            'post_type'   => 'book',
            'post_status' => 'publish',
        ));

        if ( $post_id ) {
            update_field('author', $author, $post_id);
            update_field('isbn', $isbn, $post_id);
            update_field('price', $price, $post_id);

            // 🔗 Auto-create/update WooCommerce product
            bookstore_sync_book_to_product($post_id, $title, $isbn, $price);
        }
    }

    fclose($handle);
}
add_action('admin_init', 'bookstore_import_books_safe');

/**
 * Admin page: Tools → Import Books
 */
function bookstore_register_import_books_page() {
    add_management_page(
        'Import Books',
        'Import Books',
        'manage_options',
        'import-books',
        'bookstore_import_books_page_html'
    );
}
add_action( 'admin_menu', 'bookstore_register_import_books_page' );

function bookstore_import_books_page_html() {
    if ( ! current_user_can( 'manage_options' ) ) return;

    echo '<div class="wrap"><h1>Import Books from CSV</h1>';

    if ( isset($_POST['submit']) && !empty($_FILES['csv_file']['tmp_name']) ) {
        bookstore_process_uploaded_books_csv($_FILES['csv_file']['tmp_name']);
    }

    echo '<form method="post" enctype="multipart/form-data">';
    echo '<input type="file" name="csv_file" accept=".csv" required />';
    submit_button('Upload and Import');
    echo '</form></div>';
}

/**
 * Handles uploaded CSV files (same logic, non-duplicating)
 */
function bookstore_process_uploaded_books_csv($file) {
    $handle = fopen($file, 'r');
    if ( !$handle ) {
        echo '<p style="color:red;">Failed to open file.</p>';
        return;
    }

    $header = fgetcsv($handle);
    $imported = 0; $skipped = 0;

    while ( ($row = fgetcsv($handle)) !== false ) {
        list($title, $author, $isbn, $price) = $row;

        $existing = get_posts([
            'post_type'  => 'book',
            'meta_key'   => 'isbn',
            'meta_value' => $isbn,
            'fields'     => 'ids',
        ]);

        if ( !empty($existing) ) {
            // ✅ Update WooCommerce product even for existing book
            $book_id = $existing[0];
            bookstore_sync_book_to_product($book_id, $title, $isbn, $price);
            $skipped++;
            continue;
        }

        $book_id = wp_insert_post([
            'post_type'   => 'book',
            'post_title'  => $title,
            'post_status' => 'publish',
        ]);

        if ($book_id) {
            update_post_meta($book_id, 'author', $author);
            update_post_meta($book_id, 'isbn', $isbn);
            update_post_meta($book_id, 'price', $price);

            // 🔗 Create or update WooCommerce product
            bookstore_sync_book_to_product($book_id, $title, $isbn, $price);

            $imported++;
        }
    }

    fclose($handle);

    echo '<p style="color:green;">Imported ' . $imported . ' new books. Updated ' . $skipped . ' existing entries.</p>';
}