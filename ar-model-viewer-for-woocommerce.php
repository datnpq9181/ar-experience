<?php
/*
Plugin Name: AR Model Viewer for WooCommerce
Description: Display 3D models for products on WordPress
Version: 1.0.0
Author: Dat Ngo
Author URI: https://wedev.mobi
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

//Set Safe-Mode
if ( ! ini_get( 'safe_mode' ) ) {
    ini_set( 'open_basedir', '' );
}
// include functions file
include( plugin_dir_path( __FILE__ ) . 'admin/ar-model-viewer-for-woocommerce-admin.php' );
include( plugin_dir_path( __FILE__ ) . 'includes/ar-model-viewer-for-woocommerce-functions.php' );
include( plugin_dir_path( __FILE__ ) . 'includes/helpers/ar-model-viewer-for-woocommerce-helpers.php' );
include( plugin_dir_path( __FILE__ ) . 'public/ar-model-viewer-for-woocommerce-public.php' );

//No script kiddies
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Function to run when the plugin is activated
function activate_ar_plugin() {
  add_ar_product_column();
  add_ar_model_path_column_to_wc_product_meta_lookup_table();
}

// Hook the activation function to the 'activate' action
register_activation_hook(__FILE__, 'activate_ar_plugin');

function add_custom_script_to_header() {
    // Add model-viewer library
    $model_viewer_src = plugins_url('/public/assets/js/model-viewer.min.js', __FILE__);
    echo '<script type="module" src="' . esc_url($model_viewer_src) . '"></script>';

    // Add model-dimension.js
    $model_dimension_src = plugins_url('/public/assets/js/model-dimension.js', __FILE__);
    echo '<script type="module" src="' . esc_url($model_dimension_src) . '"></script>';

    // Add style.css
    $style_src = plugins_url('/public/assets/css/style.css', __FILE__);
    echo '<link rel="stylesheet" type="text/css" href="' . esc_url($style_src) . '">';
}
add_action('wp_head', 'add_custom_script_to_header');
