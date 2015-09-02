<?php
/**
 * プラグインメインファイル
 *
 * @link              http://grow-group.jp
 * @since             0.0.4
 * @package           BCE
 *
 * @wordpress-plugin
 * Plugin Name:       BCE
 * Plugin URI:        http://github.com/1shiharat/block-content-editor
 * Description:       block based visual editor.
 * Version:           0.0.4
 * Author:            1shiharat
 * Author URI:        http://grow-group.jp
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       block-content-editor
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * 有効時のアクション
 */
function activate_block_content_editor() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bce-activator.php';
	BCE_Activator::activate();
}

/**
 * 無効化時のアクション
 */
function deactivate_block_content_editor() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bce-deactivator.php';
	BCE_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_block_content_editor' );
register_deactivation_hook( __FILE__, 'deactivate_block_content_editor' );

/**
 * プラグインコアファイルを読み込み
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-bce.php';

/**
 * プラグインを実行する関数
 *
 * after_setup_theme フックで実行
 *
 * @since    1.0.0
 */
function run_block_content_editor() {
	$plugin = new BCE();
	$plugin->run();
}
add_action('after_setup_theme', 'run_block_content_editor');
