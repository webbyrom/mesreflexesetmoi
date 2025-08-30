<?php
/*
Plugin Name: Slider Revolution Shape Burst Add-On
Plugin URI: http://www.themepunch.com/
Description: Add Awesome Interactive Particle - Shape Bursting Effects to your Layers
Author: ThemePunch
Version: 7.0.1
Author URI: http://themepunch.com
*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_SHAPEBURST_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_SHAPEBURST_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));

require_once(RS_SHAPEBURST_PLUGIN_PATH . 'includes/base.class.php');

/**
* handle everything by calling the following function *
**/
function rs_shapeburst_init(){

	new RsShapeBurstBase();
	
}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_shapeburst_init');
register_activation_hook( __FILE__, 'rs_shapeburst_init');

//build js global var for activation
add_filter( 'revslider_activate_addon', array('RsAddOnShapeBurstBase','get_data'),10,2);

// get help definitions on-demand.  merges AddOn definitions with core revslider definitions
add_filter( 'revslider_help_directory', array('RsAddOnShapeBurstBase','get_help'),10,1);

?>