<?php
/*
Plugin Name: Cleio Toolbox
Plugin URI: http://cleio.co
Description: Packed with nifty widgets and shortcodes that will cover all the basics of blogging. Photo Posts, Pretty Archives, Biography Widget, RSS and Loop filters, Social Networks Manager, Instagram/Twitter/Facebook Widgets and more! On an elegant, light and clutter-free framework.
Version: 1.0.2
Author: <a href="http://cleio.co">Cleio&Co</a>
Author URI: http://cleio.co/
*/

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Including classes helpers
 */
if ( !class_exists( 'Cleio_Framework' ) ) require_once( 'cleio-resources/classes/framework/Cleio_Framework.php');
if ( !class_exists( 'CleioToolbox_Helpers' ) ) require_once( 'resources/classes/CleioToolbox_Helpers.php');

/**
 * Including classes files & activation hook
 */
require_once( 'resources/classes/CleioToolbox_Admin.php' );
register_activation_hook( __FILE__, array( 'CleioToolbox_Admin', 'activate' ) );
require_once( 'resources/classes/CleioToolbox_Contents.php' );
require_once( 'resources/classes/CleioToolbox_Widgets.php' );
require_once( 'resources/classes/CleioToolbox_Shortcodes.php' );

/**
 * Initialisation of CleioMaps elements
 */
$GLOBALS['CleioToolbox_Contents'] = new CleioToolbox_Contents();
$GLOBALS['CleioToolbox_Widgets'] = new CleioToolbox_Widgets();
$GLOBALS['CleioToolbox_Shortcodes'] = new CleioToolbox_Shortcodes();
$GLOBALS['CleioToolbox_Admin'] = new CleioToolbox_Admin();
?>