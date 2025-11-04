<?php
if ( ! defined('WP_LOAD_PATH') ) {
}
if ( function_exists('my_theme_run_books_import') ) {
    $res = my_theme_run_books_import();
    echo $res . PHP_EOL;
} else {
    echo "Importer function not found. Make sure theme files are loaded." . PHP_EOL;
}