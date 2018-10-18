<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://audilu.com
 * @since             1.0.0
 * @package           Mu_Forms
 *
 * @wordpress-plugin
 * Plugin Name:       Mu Forms
 * Plugin URI:        http://audilu.com
 * Description:       To help users build forms quickly and then export the data input from forms in xls format.
 * Version:           1.0.0
 * Author:            Audi Lu
 * Author URI:        http://audilu.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mu-forms
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
define( 'MU_FORMS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mu-forms-activator.php
 */
function activate_mu_forms() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mu-forms-activator.php';
	Mu_Forms_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mu-forms-deactivator.php
 */
function deactivate_mu_forms() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mu-forms-deactivator.php';
	Mu_Forms_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_mu_forms' );
register_deactivation_hook( __FILE__, 'deactivate_mu_forms' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-mu-forms.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_mu_forms() {

	$plugin = new Mu_Forms();
	$plugin->run();

}
run_mu_forms();
