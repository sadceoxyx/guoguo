<?php
/**
 * @package Affiliate_Press
 * @version 0.3.8
 */
/*
Plugin Name: Affiliate Press
Plugin URI: http://wordpress.org/extend/plugins/affiliate-press/
Description: Affiliate Press allows you to set up an affiliate website based on product feeds as easy as 1-2-3.
Author: ldebrouwer
Version: 0.3.8
Author URI: http://lucdebrouwer.nl/
*/

/* Constants */
define( 'LDB_AP_PATH', plugin_dir_path( __FILE__ ) );
define( 'LDB_AP_VIEW_PATH', plugin_dir_path( __FILE__ ) . 'views/' );
define( 'LDB_AP_VIEW_URL', plugin_dir_url( __FILE__ ) . 'views/' );
define( 'LDB_AP_URL', plugin_dir_url( __FILE__ ) );
define( 'LDB_AP_SCRIPTS_URL', plugin_dir_url( __FILE__ ) . 'scripts/' );
define( 'LDB_AP_CSS_URL', plugin_dir_url( __FILE__ ) . 'css/' );
define('DEBUG', true);

/* Load translation */
load_plugin_textdomain( 'LDB_AP', false, LDB_AP_PATH . '/languages' );

/* Includes */
include( LDB_AP_PATH . 'includes/class-affiliate-press.php' );
include( LDB_AP_PATH . 'includes/class-pointers.php' );
include( LDB_AP_PATH . 'includes/class-help.php' );
include( LDB_AP_PATH . 'includes/class-feeds-table.php' );
include( LDB_AP_PATH . 'includes/class-items-table.php' );

function AP_activation() {
	include( LDB_AP_PATH . 'affiliate-press-upgrade.php' );
	update_option( 'LDB_Affiliate_Press_Version', '0.3.8' );
	wp_schedule_event( time(), 'hourly', 'AP_cronjob' );
}

function AP_deactivation() {
	wp_clear_scheduled_hook( 'AP_cronjob' );
}

/* Check if an update is required and set the cronjob */
register_activation_hook( __FILE__, 'AP_activation' );

/* Unset the cronjob */
register_deactivation_hook(__FILE__, 'AP_deactivation');

/* Start the plugin */
$ap = new LDB_Affiliate_Press;