<?php
// Add column to wp_wc_product_meta_lookup table
function add_ar_product_column()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'wc_product_meta_lookup';
    $column_name = 'ar_product';

    // Check if the column already exists in the table
    $column_exists = $wpdb->get_var("SHOW COLUMNS FROM $table_name LIKE '$column_name'") == $column_name;

    // If the column doesn't exist, add it to the table
    if (!$column_exists) {
        $wpdb->query("ALTER TABLE $table_name ADD COLUMN $column_name VARCHAR(255) DEFAULT 'no'");
    }
}

// Add path of model to product meta
function add_ar_model_path_column_to_wc_product_meta_lookup_table()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'wc_product_meta_lookup';
    $column_name = 'ar_model_path';

    // Check if the column already exists in the table
    $column_exists = $wpdb->get_var("SHOW COLUMNS FROM $table_name LIKE '$column_name'") == $column_name;

    // If the column doesn't exist, add it to the table
    if (!$column_exists) {
        $sql = "ALTER TABLE $table_name ADD $column_name VARCHAR(255) NULL";
        $wpdb->query($sql);
    }
}
