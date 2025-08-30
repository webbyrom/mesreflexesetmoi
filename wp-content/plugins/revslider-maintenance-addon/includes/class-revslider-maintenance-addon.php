<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Revslider_Maintenance_Addon
 * @subpackage Revslider_Maintenance_Addon/includes
 * @author     ThemePunch <info@themepunch.com>
 */

if(!defined('ABSPATH')) exit();

class Revslider_Maintenance_Addon {

	protected $plugin_name;
	protected $version;
	
	/**
	 * Stores if the JavaScript was already added to the page
	 */
	public $bricket_found = false;
	
	/**
	 * Define the core functionality of the plugin.
	 */
	public function __construct() {

		$this->plugin_name = 'revslider-maintenance-addon';
		$this->version = REV_ADDON_MAINTENANCE_VERSION;

		$this->load_dependencies();
		$this->set_locale();
		
		$enabled = get_option('revslider_maintenance_enabled');
		if($enabled !== false) add_filter('revslider_layer_content', array($this, 'check_if_slider_has_options'), 10, 5);
		if(is_admin()){
			$this->define_admin_hooks();
		}else{
			if($enabled !== false) $this->define_public_hooks();
		}

	}

	/**
	 * Load the required dependencies for this plugin.
	 */
	private function load_dependencies() {
		global $SR_GLOBALS;
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-revslider-maintenance-addon-admin.php';

		if(isset($SR_GLOBALS['front_version']) && $SR_GLOBALS['front_version'] === 7){
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-revslider-maintenance-addon-public.php';
		}else{
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'sr6/class-revslider-maintenance-addon-public.php';
		}

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-revslider-maintenance-addon-update.php';

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Revslider_Maintenance_Addon_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 */
	private function set_locale() {
		add_action('after_setup_theme', array($this, 'load_plugin_textdomain'), 10, 1);
	}

	public function load_plugin_textdomain() {
		load_plugin_textdomain($this->get_plugin_name(), false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 */
	private function define_admin_hooks(){

		$plugin_admin = new Revslider_Maintenance_Addon_Admin( $this->get_plugin_name(), $this->get_version() );
		$update_admin = new RevAddOnMaintenanceUpdate(REV_ADDON_MAINTENANCE_VERSION);

		add_action('admin_enqueue_scripts', array($plugin_admin, 'enqueue_scripts'), 10, 1);
		add_action('revslider_do_ajax', array($plugin_admin, 'do_ajax'), 10, 2);
		
		//updates
		add_action('pre_set_site_transient_update_plugins', array($update_admin, 'set_update_transient'), 10, 1);
		add_action('plugins_api', array($update_admin, 'set_updates_api_results'), 10, 3);
		
		//build js global var for activation
		add_action('revslider_activate_addon', array($plugin_admin, 'get_var'), 10, 2);
	}

	/**
	 * Register all of the hooks related to the public-facing functionality of the plugin.
	 */
	private function define_public_hooks() {
		$plugin_public = new Revslider_Maintenance_Addon_Public( $this->get_plugin_name(), $this->get_version() );
		
		//redirect if needed
		//add_action('plugins_loaded', array($plugin_public, 'maintenance_mode'), 10, 1);
		add_action('wp_loaded', array($plugin_public, 'maintenance_mode'), 10, 1);
	}
	
	/**
	 * check if any slider has maintenance brickets. If yes, include the JavaScript file for that Slider
	 **/
	public function check_if_slider_has_options($text, $_text, $slider_id, $slide, $layer){
		if($this->bricket_found) return $text;
	
		if(
			strpos($text, '{{t_days}}') !== false ||
			strpos($text, '{{t_hours}}') !== false ||
			strpos($text, '{{t_minutes}}') !== false ||
			strpos($text, '{{t_seconds}}') !== false
		){
			//set that the javascript is loaded
			$this->bricket_found = true;
			add_action('revslider_add_slider_base_post', array($this, 'add_dynamic_js'), 10, 1);
		}

		return $text;
	}
	
	/**
	 * adds the javascript to the page
	 **/
	public function add_dynamic_js($_output){
		$mta = Revslider_Maintenance_Addon_Public::return_mta_data();
		Revslider_Maintenance_Addon_Public::add_js($mta);
	}
	
	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
