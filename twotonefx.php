<?php
/**
 * Twotone FX
 *
 * @package   TwotoneFX
 * @author    Brady Vercher
 * @copyright Copyright (c) 2015 AudioTheme, LLC
 * @license   GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Twotone FX
 * Plugin URI:  https://audiotheme.com/view/twotonefx/?utm_source=wordpress-plugin&utm_medium=link&utm_content=twotonefx-plugin-uri&utm_campaign=plugins
 * Description: Apply a duotone effect to photos in the media library.
 * Version:     1.0.0
 * Author:      AudioTheme
 * Author URI:  https://audiotheme.com/?utm_source=wordpress-plugin&utm_medium=link&utm_content=twotonefx-author-uri&utm_campaign=plugins
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: twotonefx
 * Domain Path: /languages
 */

include( 'php/class-abstract-plugin.php' );
include( 'php/class-plugin.php' );

$twotonefx_plugin = new TwotoneFX_Plugin();
$twotonefx_plugin
	->set_directory( plugin_dir_path( __FILE__ ) )
	->set_file( __FILE__ )
	->set_slug( 'twotonefx' )
	->set_url( plugin_dir_url( __FILE__ ) );

/**
 * Localize the plugin.
 *
 * @since 1.0.0
 */
function twotonefx_load_textdomain() {
	$plugin_rel_path = dirname( plugin_basename( __FILE__ ) ) . '/languages';
	load_plugin_textdomain( 'twotonefx', false, $plugin_rel_path );
}
add_action( 'plugins_loaded', 'twotonefx_load_textdomain' );

/**
 * Load the plugin.
 */
add_action( 'plugins_loaded', array( $twotonefx_plugin, 'register_hooks' ) );
