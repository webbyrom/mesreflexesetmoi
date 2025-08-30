<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2024 ThemePunch
*/

if(!defined('ABSPATH')) exit();

class RsAddOnShapeBurstBase {
	
	const MINIMUM_VERSION = '6.7.14';

	protected function systemsCheck() {
		
		if(!class_exists('RevSliderFront')) {
		
			return 'add_notice_plugin';
		
		}
		else if(!version_compare(RevSliderGlobals::SLIDER_REVISION, RsAddOnShapeBurstBase::MINIMUM_VERSION, '>=')) {
		
			return 'add_notice_version';
		
		}
		else if(get_option('revslider-valid', 'false') == 'false') {
		
			 return 'add_notice_activation';
		
		}
		
		return false;
		
	}
	
	protected function loadClasses() {
		global $SR_GLOBALS;
		$isAdmin = is_admin();
		
		if($isAdmin) {
			
			//handle update process, this uses the typical ThemePunch server process
			require_once(static::$_PluginPath . 'admin/includes/update.class.php');
			$update_admin = new RevAddOnShapeBurstUpdate(static::$_Version);

			add_filter('pre_set_site_transient_update_plugins', array($update_admin, 'set_update_transient'));
			add_filter('plugins_api', array($update_admin, 'set_updates_api_results'), 10, 3);
			
			// admin CSS/JS
			add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
			
			add_filter('sr_exportSlider_usedMedia', array($this, 'export_adddon_images'), 10, 3);	
			add_filter('revslider_importSliderFromPost_modify_data', array($this, 'import_update_addon_image_urls'), 10, 3);
			
			if(!isset($SR_GLOBALS['front_version']) || $SR_GLOBALS['front_version'] !== 7){
				/* 
				frontend scripts always enqueued for admin previews
				*/
				require_once(static::$_PluginPath . 'sr6/includes/slider.class.php');
				require_once(static::$_PluginPath . 'sr6/includes/slide.class.php');
				
				new RsShapeBurstSliderFront(static::$_Version, static::$_PluginUrl, static::$_PluginTitle, $isAdmin);
				new RsShapeBurstSlideFront(static::$_PluginTitle);
			}
		}
		
		if(isset($SR_GLOBALS['front_version']) && $SR_GLOBALS['front_version'] === 7){
			//v7, load always as things are needed in overview page
			require_once(static::$_PluginPath . 'public/includes/front.class.php');
			new SrShapeBurstFront(static::$_PluginTitle);
		}
	}
	
	/**
	 * Load the textdomain
	 **/
	public function _loadPluginTextDomain(){
		
		load_plugin_textdomain('rs_' . static::$_PluginTitle, false, static::$_PluginPath . 'languages/');
		
	}

	// load admin scripts
	public function enqueue_admin_scripts($hook) {

		if($hook === 'toplevel_page_revslider') {

			if(!isset($_GET['page']) || !isset($_GET['view'])) return;
			
			$page = $_GET['page'];
			if($page !== 'revslider') return;
			
			$_handle = 'rs-' . static::$_PluginTitle;
			$_base   = static::$_PluginUrl . 'admin/assets/';
			
			// load fronted Script for some global function
			$_jsPathMin = file_exists(RS_SHAPEBURST_PLUGIN_PATH . 'sr6/assets/js/revolution.addon.' . static::$_PluginTitle . '.js') ? '' : '.min';	
			wp_enqueue_script($_handle.'-js', static::$_PluginUrl . 'sr6/assets/js/revolution.addon.' . static::$_PluginTitle . $_jsPathMin . '.js', array('jquery', 'revbuilder-admin'), static::$_Version, true);
			
			$_jsPathMin = file_exists(static::$_PluginPath . 'admin/assets/js/revslider-' . static::$_PluginTitle . '-addon-admin.dev.js') ? '.dev' : '';
			wp_enqueue_style($_handle.'-css', $_base . 'css/revslider-' . static::$_PluginTitle . '-addon-admin.css', array(), static::$_Version);
			wp_enqueue_script($_handle.'-addon-admin-js', $_base . 'js/revslider-' . static::$_PluginTitle . '-addon-admin' . $_jsPathMin . '.js', array('jquery', 'revbuilder-admin'), static::$_Version, true);
			wp_localize_script($_handle.'-addon-admin-js', 'revslider_shapeburst_addon', self::get_var() );
			
			wp_enqueue_script('revbuilder-threejs', RS_PLUGIN_URL . 'sr6/assets/js/libs/three.min.js', array('jquery', 'revbuilder-admin',$_handle.'-js'), RS_REVISION);						
			add_action('revslider_do_ajax', array($this, 'do_ajax'), 10, 2);
		}		
	}

	/**
	 * add images to the export
	 **/
	/* public function export_adddon_images($used_media, $slides, $sliderParams){
		$func = new RevSliderFunctions();

		foreach($slides ?? [] as $slide){
			$layers = $func->get_val($slide, 'layers', []);
			foreach($layers ?? [] as $layer){
				$image = $func->get_val($layer, array('addOns', 'revslider-shapeburst-addon', 'parbackground'), '');

				if(!empty($image)) $used_media[$image] = true;
			}
		}
		
		return $used_media;
	} */

	public function export_adddon_images($data, $slides, $sliderParams){
		$func = new RevSliderFunctions();
		foreach($slides as $slide){
			$layers = $func->get_val($slide, 'layers', array());
			if(!empty($layers)){
				foreach($layers as $layer){
					$image = $func->get_val($layer, array('addOns', 'revslider-shapeburst-addon', 'parbackground'), '');

					if(!empty($image)) $data['used_images'][$image] = true;
				}
			}
		}
		
		return $data;
	}

	/**
	 * import images if existing
	 **/
	public function import_update_addon_image_urls($data, $slidetype, $image_path) {
		global $wp_filesystem;
		
		$func = new RevSliderFunctions();

		$alias = $func->get_val($data, array('sliderParams', 'alias'), '');
		if(!empty($alias)) {
			$upload_dir = wp_upload_dir();
			$path = '/';

			$layers = $func->get_val($data, 'layers', array());
			if(!empty($layers)){
				foreach($layers as $k => $layer){
					$_images = array(
						'parbackground' => $func->get_val($layer, array('addOns', 'revslider-shapeburst-addon', 'parbackground'), '')
					);

					foreach($_images as $key => $_image){
						if(empty($_image)) continue;
						
						$imported = $func->get_val($data, 'imported', array());
						
						$strip	= false;
						$zimage	= $wp_filesystem->exists($image_path.'images/'.$_image);
						if(!$zimage){
							$zimage	= $wp_filesystem->exists(str_replace('//', '/', $image_path.'images/'.$_image));
							$strip	= true;
						}

						$ext = pathinfo($_image, PATHINFO_EXTENSION);
						if($ext == 'svg'){
							//check if we need to import it, if its available in the zip file
							if(!$zimage) $_image = content_url().$_image;
						}
						
						if($zimage){
							if(!isset($imported['images/'.$_image])){
								//check if we are object folder, if yes, do not import into media library but add it to the object folder
								$uimg = ($strip == true) ? str_replace('//', '/', 'images/'.$_image) : $_image; //pclzip
								
								$file = $upload_dir['basedir'] . $path . $_image;
								$_file = $upload_dir['baseurl'] . $path . $_image;
								
								@mkdir(dirname($file), 0777, true);
								@copy($image_path.'images/'.$_image, $file);
								
								$imported['images/'.$_image] = $_file;
								$_image = $_file;
							}else{
								$_image = $imported['images/'.$_image];
							}
						}

						if(!empty($_image)){
							$data['layers'][$k]['addOns']['revslider-shapeburst-addon'][$key] = $_image;
						}
					}
				}
			}
		}

		return $data;
	}

	
	/**
	 * New function for ajax activation to include AddOn help definitions
	 *
	 * @since    2.0.0
	 */
	public static function get_data($var='',$slug='revslider-shapeburst-addon') {
		
		if($slug === 'revslider-shapeburst-addon'){
			
			$obj = self::get_var();
			$obj['help'] = self::get_definitions();
			return $obj;
			
		}
		
		return $var;
	
	}
	
	/**
	 * Called via php filter.  Merges AddOn definitions with core revslider definitions
	 *
	 * @since    2.0.0
	 */
	public static function get_help($definitions) {
		
		if(empty($definitions) || !isset($definitions['editor_settings'])) return $definitions;
		
		if(isset($definitions['editor_settings']['layer_settings']) && isset($definitions['editor_settings']['layer_settings']['addons'])) {
			$help = self::get_definitions();
			$definitions['editor_settings']['layer_settings']['addons']['shapeburst_addon'] = $help['layer'];
		}
		
		return $definitions;
	
	}
	

	public function do_ajax($return = "",$action ="") {
		switch ($action) {
			case 'delete_custom_templates_revslider-shapeburst-addon':
				$return = $this->delete_template($_REQUEST["data"]);
				if($return){
					return  __('Particle Wave Template deleted', 'revslider-shapeburst-addon');
				}
				else{
					return  __('Particle Wave Template could not be deleted', 'revslider-shapeburst-addon');
				}
				break;
			case 'save_custom_templates_revslider-shapeburst-addon':
				$return = $this->save_template($_REQUEST["data"]);
				if(empty($return) || !$return){
					return  __('Particle Wave Template could not be saved', 'revslider-shapeburst-addon');
				} 
				else {
					return  array( 'message' => __('Particle Wave Template saved', 'revslider-shapeburst-addon'), 'data' => array("id" => $return));	
				}
				break;
			default:
				return $return;
				break;
		}
	}

	/**
	 * Save Custom Template
	 *
	 * @since    2.0.0
	 */
	private function save_template($template){		
		//load already saved templates
		$custom = $this->get_templates();
		
		//empty custom templates?
		if(!$custom && !is_array($custom)){
			$custom = array();
			$new_id = 1;
		}
		else{
			//custom templates exist
			if(isset($template["id"]) && is_numeric($template["id"]) ){
				//id exists , overwrite
				$new_id = $template["id"];
			}
			else{
				//id does not exist , new template
				$new_id = max(array_keys($custom))+1;
			}
		}
		
		//update or insert template
		$custom[$new_id]["title"] = $template["obj"]["title"];
		$custom[$new_id]["preset"] = $template["obj"]["preset"];
		if(update_option( 'revslider_addon_shapeburst_templates', $custom )){
			//return the ID the template was saved with
			return $new_id;	
		}
		else {
			//updating failed, blank result set
			return "";
		}
	
	}

	/**
	 * Delete Custom Template
	 *
	 * @since    2.0.0
	 */
	private function delete_template($template){
		//load templates array
		$custom = $this->get_templates();
		
		//custom template exist
		if(isset($template["id"]) && is_numeric($template["id"]) ){
			//delete given ID
			$delete_id = $template["id"];
			unset($custom[$delete_id]);
			//save the resulting templates array again
			if(update_option( 'revslider_addon_shapeburst_templates', $custom )){
				return true;	
			}
			else {
				return false;
			}
		}
	}

	/**
	 * Read Custom Templates from WP option, false if not set
	 *
	 * @since    2.0.0
	 */
	private static function get_templates(){
		//load WP option
		$custom = get_option('revslider_addon_shapeburst_templates',false);

		return $custom;
	}

	/**
	 * Returns the global JS variable
	 *
	 * @since    2.0.0
	 */
	public static function get_var() {
			
		$_textdomain = 'revslider-shapeburst-addon';
		return array(
		
			'bricks' => array(		
				// GENERAL
				'shapeburst' => __('Shape Burst', $_textdomain),
				'shapeburstdatt' => __('Shapeburst Setup', $_textdomain),
				'amount' => __('Amount', $_textdomain),

				// PRESETS
				'defaultpres' => __('Default Presets', $_textdomain),
				'custompres' => __('Custom Presets', $_textdomain),
				'presetslib' => __('Presets', $_textdomain),

				// FOLDERS
				'sbMain' => __('Main', $_textdomain),
				'sbInter' => __('Interaction', $_textdomain),
				'sbParticles' => __('Particles', $_textdomain),
				'sbTrans' => __('Transition', $_textdomain),
				'sbFX' => __('FX', $_textdomain),

				// HEADERS
				'imageHeader' => __('Image Setup', $_textdomain),
				'textHeader' => __('Text Setup', $_textdomain),

				// MAIN
				'image' => __('Image', $_textdomain),
				'luminaBoost' => __('Lighten Image', $_textdomain),
				'darkLimit' => __('Limit Darkness', $_textdomain),
				'colorTrue' => __('Color Image', $_textdomain),
				'text' => __('Text', $_textdomain),
				'imgSource' => __('Type', $_textdomain),
				'imgSize' => __('Source Type', $_textdomain),
				'position' => __('Position', $_textdomain),
				'cover' => __('Cover', $_textdomain),
				'stretch' => __('Stretch', $_textdomain),
				'contain' => __('Contain', $_textdomain),

				'textFont' => __('Font', $_textdomain),
				'textFontWeight' => __('Weight', $_textdomain),


				// PARTICLES
				'particleSize' => __('Size', $_textdomain),
				'randomness' => __('Randomness', $_textdomain),
				'speed' => __('Speed', $_textdomain),
				
				// INTERACTION
				'explosionRadius' => __('Radius', $_textdomain),
				'maxBurstDur' => __('Duration', $_textdomain),
				'explosionPower' => __('Power', $_textdomain),
				'sizeChOnInter' => __('Enlarge', $_textdomain),

				// TRANSITION
				'transitionON' => __('Transition', $_textdomain),
				'transitionAnimTime' => __('Duration', $_textdomain),

				// FX
				'fxOn' => __('Blur', $_textdomain),
				'fxOnlyOnInter' => __('Only on Hover', $_textdomain),
				
			)
		);
	
	}
	
	/**
	 * Returns the addon help definitions
	 *
	 * @since    2.0.0
	 */
	private static function get_definitions() {
		
		return array(
			
			'layer' => array(

				//__________ MAIN ____________

				'imgSource' => array(
					
					'buttonTitle' => __('Type', 'revslider-shapeburst-addon'), 
					'title' => __('Type', 'revslider-shapeburst-addon'),
					'helpPath' => 'addOns.revslider-shapeburst-addon.imgSource', 
					'keywords' => array('addon', 'addons', 'shapeburst', 'shapeburst addon', 'Type'), 
					'description' => __("Choose between Image or Text", 'revslider-shapeburst-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/shapeburst-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ShapeBurst',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{shapeburst}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-shapeburst-addon", 
						'scrollTo' => '#form_layerinner_revslider-shapeburst-addon', 
						'focus' => "*[data-r='addOns.revslider-shapeburst-addon.imgSource']"
						
					)
					
				),

				'image' => array(
					
					'buttonTitle' => __('Image', 'revslider-shapeburst-addon'), 
					'title' => __('Image', 'revslider-shapeburst-addon'),
					'helpPath' => 'addOns.revslider-shapeburst-addon.image', 
					'keywords' => array('addon', 'addons', 'shapeburst', 'shapeburst addon', 'Image'), 
					'description' => __("Choose from Image library", 'revslider-shapeburst-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/shapeburst-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ShapeBurst',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{shapeburst}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-shapeburst-addon", 
						'scrollTo' => '#form_layerinner_revslider-shapeburst-addon', 
						'focus' => "*[data-r='addOns.revslider-shapeburst-addon.image']"
						
					)
					
				),

				'imgSize' => array(
					
					'buttonTitle' => __('Source Type', 'revslider-shapeburst-addon'), 
					'title' => __('Source Type', 'revslider-shapeburst-addon'),
					'helpPath' => 'addOns.revslider-shapeburst-addon.imgSize', 
					'keywords' => array('addon', 'addons', 'shapeburst', 'shapeburst addon', 'Source Type'), 
					'description' => __("Select Image positioning on Canvas", 'revslider-shapeburst-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/shapeburst-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ShapeBurst',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{shapeburst}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-shapeburst-addon", 
						'scrollTo' => '#form_layerinner_revslider-shapeburst-addon', 
						'focus' => "*[data-r='addOns.revslider-shapeburst-addon.imgSize']"
						
					)
					
				),

				'colorTrue' => array(
					
					'buttonTitle' => __('Color Image', 'revslider-shapeburst-addon'), 
					'title' => __('Color Image', 'revslider-shapeburst-addon'),
					'helpPath' => 'addOns.revslider-shapeburst-addon.colorTrue', 
					'keywords' => array('addon', 'addons', 'shapeburst', 'shapeburst addon', 'Color Image'), 
					'description' => __("Toggle between Black and White and Color", 'revslider-shapeburst-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/shapeburst-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ShapeBurst',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{shapeburst}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-shapeburst-addon", 
						'scrollTo' => '#form_layerinner_revslider-shapeburst-addon', 
						'focus' => "*[data-r='addOns.revslider-shapeburst-addon.colorTrue']"
						
					)
					
				),

				'luminaBoost' => array(
					
					'buttonTitle' => __('Lighten Image', 'revslider-shapeburst-addon'), 
					'title' => __('Lighten Image', 'revslider-shapeburst-addon'),
					'helpPath' => 'addOns.revslider-shapeburst-addon.luminaBoost', 
					'keywords' => array('addon', 'addons', 'shapeburst', 'shapeburst addon', 'Lighten Image'), 
					'description' => __("Enhances Brightness of the Image", 'revslider-shapeburst-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/shapeburst-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ShapeBurst',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{shapeburst}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-shapeburst-addon", 
						'scrollTo' => '#form_layerinner_revslider-shapeburst-addon', 
						'focus' => "*[data-r='addOns.revslider-shapeburst-addon.luminaBoost']"
						
					)
					
				),

				'darkLimit' => array(
					
					'buttonTitle' => __('Limit Darkness', 'revslider-shapeburst-addon'), 
					'title' => __('Limit Darkness', 'revslider-shapeburst-addon'),
					'helpPath' => 'addOns.revslider-shapeburst-addon.darkLimit', 
					'keywords' => array('addon', 'addons', 'shapeburst', 'shapeburst addon', 'Limit Darkness'), 
					'description' => __("Sets limit for rendering dark Particles, enhancing performance", 'revslider-shapeburst-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/shapeburst-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ShapeBurst',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{shapeburst}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-shapeburst-addon", 
						'scrollTo' => '#form_layerinner_revslider-shapeburst-addon', 
						'focus' => "*[data-r='addOns.revslider-shapeburst-addon.darkLimit']"
						
					)
					
				),

				'text' => array(
					
					'buttonTitle' => __('Text', 'revslider-shapeburst-addon'), 
					'title' => __('Text', 'revslider-shapeburst-addon'),
					'helpPath' => 'addOns.revslider-shapeburst-addon.text', 
					'keywords' => array('addon', 'addons', 'shapeburst', 'shapeburst addon', 'Text'), 
					'description' => __("Text to be displayed", 'revslider-shapeburst-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/shapeburst-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ShapeBurst',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{shapeburst}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-shapeburst-addon", 
						'scrollTo' => '#form_layerinner_revslider-shapeburst-addon', 
						'focus' => "*[data-r='addOns.revslider-shapeburst-addon.text']"
						
					)
					
				),

				'textFont' => array(
					
					'buttonTitle' => __('Font', 'revslider-shapeburst-addon'), 
					'title' => __('Font', 'revslider-shapeburst-addon'),
					'helpPath' => 'addOns.revslider-shapeburst-addon.textFont', 
					'keywords' => array('addon', 'addons', 'shapeburst', 'shapeburst addon', 'Font'), 
					'description' => __("Choose text font", 'revslider-shapeburst-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/shapeburst-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ShapeBurst',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{shapeburst}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-shapeburst-addon", 
						'scrollTo' => '#form_layerinner_revslider-shapeburst-addon', 
						'focus' => "*[data-r='addOns.revslider-shapeburst-addon.textFont']"
						
					)
					
				),

				'textFontWeight' => array(
					
					'buttonTitle' => __('Weight', 'revslider-shapeburst-addon'), 
					'title' => __('Weight', 'revslider-shapeburst-addon'),
					'helpPath' => 'addOns.revslider-shapeburst-addon.textFontWeight', 
					'keywords' => array('addon', 'addons', 'shapeburst', 'shapeburst addon', 'Weight'), 
					'description' => __("Choose text weight", 'revslider-shapeburst-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/shapeburst-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ShapeBurst',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{shapeburst}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-shapeburst-addon", 
						'scrollTo' => '#form_layerinner_revslider-shapeburst-addon', 
						'focus' => "*[data-r='addOns.revslider-shapeburst-addon.textFontWeight']"
						
					)
					
				),

				'textColor' => array(
					
					'buttonTitle' => __(' ', 'revslider-shapeburst-addon'), 
					'title' => __(' ', 'revslider-shapeburst-addon'),
					'helpPath' => 'addOns.revslider-shapeburst-addon.textColor', 
					'keywords' => array('addon', 'addons', 'shapeburst', 'shapeburst addon', ' '), 
					'description' => __("Choose text color", 'revslider-shapeburst-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/shapeburst-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ShapeBurst',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{shapeburst}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-shapeburst-addon", 
						'scrollTo' => '#form_layerinner_revslider-shapeburst-addon', 
						'focus' => "*[data-r='addOns.revslider-shapeburst-addon.textColor']"
						
					)
					
				),

				//__________ Particles ____________

				'particlesCount' => array(
					
					'buttonTitle' => __('Amount', 'revslider-shapeburst-addon'), 
					'title' => __('Amount', 'revslider-shapeburst-addon'),
					'helpPath' => 'addOns.revslider-shapeburst-addon.particlesCount', 
					'keywords' => array('addon', 'addons', 'shapeburst', 'shapeburst addon', 'Amount'), 
					'description' => __("Sets the amount of Particles to be rendered", 'revslider-shapeburst-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/shapeburst-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ShapeBurst',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{shapeburst}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-shapeburst-addon", 
						'scrollTo' => '#form_layerinner_revslider-shapeburst-addon', 
						'focus' => "*[data-r='addOns.revslider-shapeburst-addon.particlesCount']"
						
					)
					
				),

				'particleSize' => array(
					
					'buttonTitle' => __('Size', 'revslider-shapeburst-addon'), 
					'title' => __('Size', 'revslider-shapeburst-addon'),
					'helpPath' => 'addOns.revslider-shapeburst-addon.particleSize', 
					'keywords' => array('addon', 'addons', 'shapeburst', 'shapeburst addon', 'Size'), 
					'description' => __("Sets Particle size", 'revslider-shapeburst-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/shapeburst-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ShapeBurst',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{shapeburst}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-shapeburst-addon", 
						'scrollTo' => '#form_layerinner_revslider-shapeburst-addon', 
						'focus' => "*[data-r='addOns.revslider-shapeburst-addon.particleSize']"
						
					)
					
				),

				'randomness' => array(
					
					'buttonTitle' => __('Randomness', 'revslider-shapeburst-addon'), 
					'title' => __('Randomness', 'revslider-shapeburst-addon'),
					'helpPath' => 'addOns.revslider-shapeburst-addon.randomness', 
					'keywords' => array('addon', 'addons', 'shapeburst', 'shapeburst addon', 'Randomness'), 
					'description' => __("Allow Particles to move away from grid positions", 'revslider-shapeburst-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/shapeburst-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ShapeBurst',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{shapeburst}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-shapeburst-addon", 
						'scrollTo' => '#form_layerinner_revslider-shapeburst-addon', 
						'focus' => "*[data-r='addOns.revslider-shapeburst-addon.randomness']"
						
					)
					
				),

				'speed' => array(
					
					'buttonTitle' => __('Speed', 'revslider-shapeburst-addon'), 
					'title' => __('Speed', 'revslider-shapeburst-addon'),
					'helpPath' => 'addOns.revslider-shapeburst-addon.speed', 
					'keywords' => array('addon', 'addons', 'shapeburst', 'shapeburst addon', 'Speed'), 
					'description' => __("Movement speed without Interaction", 'revslider-shapeburst-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/shapeburst-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ShapeBurst',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{shapeburst}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-shapeburst-addon", 
						'scrollTo' => '#form_layerinner_revslider-shapeburst-addon', 
						'focus' => "*[data-r='addOns.revslider-shapeburst-addon.speed']"
						
					)
					
				),

				//__________ Interaction ____________

				'explosionRadius' => array(
					
					'buttonTitle' => __('Radius', 'revslider-shapeburst-addon'), 
					'title' => __('Radius', 'revslider-shapeburst-addon'),
					'helpPath' => 'addOns.revslider-shapeburst-addon.explosionRadius', 
					'keywords' => array('addon', 'addons', 'shapeburst', 'shapeburst addon', 'Radius'), 
					'description' => __("Interaction radius", 'revslider-shapeburst-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/shapeburst-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ShapeBurst',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{shapeburst}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-shapeburst-addon", 
						'scrollTo' => '#form_layerinner_revslider-shapeburst-addon', 
						'focus' => "*[data-r='addOns.revslider-shapeburst-addon.explosionRadius']"
						
					)
					
				),

				'maxBurstDur' => array(
					
					'buttonTitle' => __('Duration', 'revslider-shapeburst-addon'), 
					'title' => __('Duration', 'revslider-shapeburst-addon'),
					'helpPath' => 'addOns.revslider-shapeburst-addon.maxBurstDur', 
					'keywords' => array('addon', 'addons', 'shapeburst', 'shapeburst addon', 'Duration'), 
					'description' => __("Length the Particles are effected by Interaction", 'revslider-shapeburst-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/shapeburst-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ShapeBurst',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{shapeburst}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-shapeburst-addon", 
						'scrollTo' => '#form_layerinner_revslider-shapeburst-addon', 
						'focus' => "*[data-r='addOns.revslider-shapeburst-addon.maxBurstDur']"
						
					)
					
				),
	
				'explosionPower' => array(
					
					'buttonTitle' => __('Power', 'revslider-shapeburst-addon'), 
					'title' => __('Power', 'revslider-shapeburst-addon'),
					'helpPath' => 'addOns.revslider-shapeburst-addon.explosionPower', 
					'keywords' => array('addon', 'addons', 'shapeburst', 'shapeburst addon', 'Power'), 
					'description' => __("Distance the effected Particles can move", 'revslider-shapeburst-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/shapeburst-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ShapeBurst',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{shapeburst}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-shapeburst-addon", 
						'scrollTo' => '#form_layerinner_revslider-shapeburst-addon', 
						'focus' => "*[data-r='addOns.revslider-shapeburst-addon.explosionPower']"
						
					)
					
				),

				'speed' => array(
					
					'buttonTitle' => __('Speed', 'revslider-shapeburst-addon'), 
					'title' => __('Speed', 'revslider-shapeburst-addon'),
					'helpPath' => 'addOns.revslider-shapeburst-addon.speed', 
					'keywords' => array('addon', 'addons', 'shapeburst', 'shapeburst addon', 'Speed'), 
					'description' => __("Interaction speed", 'revslider-shapeburst-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/shapeburst-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ShapeBurst',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{shapeburst}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-shapeburst-addon", 
						'scrollTo' => '#form_layerinner_revslider-shapeburst-addon', 
						'focus' => "*[data-r='addOns.revslider-shapeburst-addon.speed']"
						
					)
					
				),

				'sizeChOnInter' => array(
					
					'buttonTitle' => __('Enlarge', 'revslider-shapeburst-addon'), 
					'title' => __('Enlarge', 'revslider-shapeburst-addon'),
					'helpPath' => 'addOns.revslider-shapeburst-addon.sizeChOnInter', 
					'keywords' => array('addon', 'addons', 'shapeburst', 'shapeburst addon', 'Enlarge'), 
					'description' => __("Enlarge Particles on Mouse interaction", 'revslider-shapeburst-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/shapeburst-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ShapeBurst',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{shapeburst}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-shapeburst-addon", 
						'scrollTo' => '#form_layerinner_revslider-shapeburst-addon', 
						'focus' => "*[data-r='addOns.revslider-shapeburst-addon.sizeChOnInter']"
						
					)
					
				),

				//__________ Transition ____________

				'transitionON' => array(
					
					'buttonTitle' => __('Transition', 'revslider-shapeburst-addon'), 
					'title' => __('Transition', 'revslider-shapeburst-addon'),
					'helpPath' => 'addOns.revslider-shapeburst-addon.transitionON', 
					'keywords' => array('addon', 'addons', 'shapeburst', 'shapeburst addon', 'Transition'), 
					'description' => __("Enable Transition animation", 'revslider-shapeburst-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/shapeburst-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ShapeBurst',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{shapeburst}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-shapeburst-addon", 
						'scrollTo' => '#form_layerinner_revslider-shapeburst-addon', 
						'focus' => "*[data-r='addOns.revslider-shapeburst-addon.transitionON']"
						
					)
					
				),

				'transitionAnimTime' => array(
					
					'buttonTitle' => __('Duration', 'revslider-shapeburst-addon'), 
					'title' => __('Duration', 'revslider-shapeburst-addon'),
					'helpPath' => 'addOns.revslider-shapeburst-addon.transitionAnimTime', 
					'keywords' => array('addon', 'addons', 'shapeburst', 'shapeburst addon', 'Duration'), 
					'description' => __("Duration of the Transition effect", 'revslider-shapeburst-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/shapeburst-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ShapeBurst',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{shapeburst}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-shapeburst-addon", 
						'scrollTo' => '#form_layerinner_revslider-shapeburst-addon', 
						'focus' => "*[data-r='addOns.revslider-shapeburst-addon.transitionAnimTime']"
						
					)
					
				),

				//__________ FX ____________

				'fxOn' => array( //Blur
					
					'buttonTitle' => __('Blur', 'revslider-shapeburst-addon'), 
					'title' => __('Blur', 'revslider-shapeburst-addon'),
					'helpPath' => 'addOns.revslider-shapeburst-addon.fxOn', 
					'keywords' => array('addon', 'addons', 'shapeburst', 'shapeburst addon', 'Blur'), 
					'description' => __("Enable Blur effect", 'revslider-shapeburst-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/shapeburst-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ShapeBurst',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{shapeburst}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-shapeburst-addon", 
						'scrollTo' => '#form_layerinner_revslider-shapeburst-addon', 
						'focus' => "*[data-r='addOns.revslider-shapeburst-addon.fxOn']"
						
					)
					
				),

				'blurScale' => array(
					
					'buttonTitle' => __('Amount', 'revslider-shapeburst-addon'), 
					'title' => __('Amount', 'revslider-shapeburst-addon'),
					'helpPath' => 'addOns.revslider-shapeburst-addon.blurScale', 
					'keywords' => array('addon', 'addons', 'shapeburst', 'shapeburst addon', 'Amount'), 
					'description' => __("Amount of Blur applied to Particles", 'revslider-shapeburst-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/shapeburst-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ShapeBurst',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{shapeburst}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-shapeburst-addon", 
						'scrollTo' => '#form_layerinner_revslider-shapeburst-addon', 
						'focus' => "*[data-r='addOns.revslider-shapeburst-addon.blurScale']"
						
					)
					
				),

				'fxOnlyOnInter' => array(
					
					'buttonTitle' => __('Only on Hover', 'revslider-shapeburst-addon'), 
					'title' => __('Only on Hover', 'revslider-shapeburst-addon'),
					'helpPath' => 'addOns.revslider-shapeburst-addon.fxOnlyOnInter', 
					'keywords' => array('addon', 'addons', 'shapeburst', 'shapeburst addon', 'Only on Hover'), 
					'description' => __("Only apply Blur with Mouse interaction", 'revslider-shapeburst-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/shapeburst-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ShapeBurst',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{shapeburst}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-shapeburst-addon", 
						'scrollTo' => '#form_layerinner_revslider-shapeburst-addon', 
						'focus' => "*[data-r='addOns.revslider-shapeburst-addon.fxOnlyOnInter']"
						
					)
					
				),

				

				/* __________ FOLDER ____________ */

				/* '___NAME___' => array(
					
					'buttonTitle' => __('___TITLE___', 'revslider-shapeburst-addon'), 
					'title' => __('___TITLE___', 'revslider-shapeburst-addon'),
					'helpPath' => 'addOns.revslider-shapeburst-addon.___NAME___', 
					'keywords' => array('addon', 'addons', 'shapeburst', 'shapeburst addon', '___TITLE___'), 
					'description' => __("___DESCRIBE_SETTING___", 'revslider-shapeburst-addon'), 
					'helpStyle' => 'normal', 
					'article' => 'http://docs.themepunch.com/slider-revolution/shapeburst-addon/', 
					'video' => false,
					'section' => 'Layer Settings -> ShapeBurst',
					'highlight' => array(
						
						'dependencies' => array('layerselected::shape{{shapeburst}}'), 
						'menu' => "#module_layers_trigger, #gst_layer_revslider-shapeburst-addon", 
						'scrollTo' => '#form_layerinner_revslider-shapeburst-addon', 
						'focus' => "*[data-r='addOns.revslider-shapeburst-addon.___NAME___']"
						
					)
					
				), */
			)
			
		);
		
	}

}
	
?>