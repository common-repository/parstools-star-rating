<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://parstools.com/
 * @since             1.0.0
 * @package           Parstools_Star_Rating
 *
 * @wordpress-plugin
 * Plugin Name:       Parstools star rating
 * Plugin URI:        http://parstools.com/?p=4705
 * Description:       Add fancy star rating to any type of post and page with elegant themes
 * Version:           1.0.0
 * Author:            Parstools
 * Author URI:        http://parstools.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       parstools-star-rating
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-parstools-star-rating-activator.php
 *
function activate_parstools_star_rating() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-parstools-star-rating-activator.php';
	Parstools_Star_Rating_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-parstools-star-rating-deactivator.php
 *
function deactivate_parstools_star_rating() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-parstools-star-rating-deactivator.php';
	Parstools_Star_Rating_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_parstools_star_rating' );
register_deactivation_hook( __FILE__, 'deactivate_parstools_star_rating' );
*/
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-parstools-star-rating.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_parstools_star_rating() {

	$plugin = new Parstools_Star_Rating();
	$plugin->run();

}
run_parstools_star_rating();
