<?php

/**
 * Fired during plugin activation
 *
 * @link       https://datngo.com
 * @since      1.0.0
 *
 * @package    Ar_Experience
 * @subpackage Ar_Experience/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ar_Experience
 * @subpackage Ar_Experience/includes
 * @author     Dat Ngo <contact@realitech.dev>
 */
class Ar_Experience_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		
		// Add path of model to product meta
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

}