<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://datngo.com
 * @since             1.0.0
 * @package           Ar_Experience
 *
 * @wordpress-plugin
 * Plugin Name:       AR Experience DEV
 * Plugin URI:        https://realitech.dev
 * Description:       Bring AR experience including 3D models display, virtual try on and many things for products on WordPress
 * Version:           1.0.0
 * Author:            Dat Ngo
 * Author URI:        https://datngo.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ar-experience
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'AR_EXPERIENCE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ar-experience-activator.php
 */
function activate_ar_experience() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ar-experience-activator.php';
	Ar_Experience_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ar-experience-deactivator.php
 */
function deactivate_ar_experience() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ar-experience-deactivator.php';
	Ar_Experience_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ar_experience' );
register_deactivation_hook( __FILE__, 'deactivate_ar_experience' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ar-experience.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ar_experience() {

	$plugin = new Ar_Experience();
	$plugin->run();

}
run_ar_experience();
