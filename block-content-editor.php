<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://grow-group.jp
 * @since             1.0.0
 * @package           Block_Content_Editor
 *
 * @wordpress-plugin
 * Plugin Name:       Block Content Editor
 * Plugin URI:        http://github.com/1shiharat/block-content-editor
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            1shiharat
 * Author URI:        http://grow-group.jp
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       block-content-editor
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-block-content-editor-activator.php
 */
function activate_block_content_editor() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-block-content-editor-activator.php';
	Block_Content_Editor_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-block-content-editor-deactivator.php
 */
function deactivate_block_content_editor() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-block-content-editor-deactivator.php';
	Block_Content_Editor_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_block_content_editor' );
register_deactivation_hook( __FILE__, 'deactivate_block_content_editor' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-block-content-editor.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_block_content_editor() {

	$plugin = new Block_Content_Editor();
	$plugin->run();

}
run_block_content_editor();