<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2024 ThemePunch
*/

if(!defined('ABSPATH')) exit();

require_once(RS_LIQUIDEFFECT_PLUGIN_PATH . 'framework/base.class.php');

class RsLiquideffectBase extends RsAddOnLiquideffectBase {
	
	protected static $_PluginPath    = RS_LIQUIDEFFECT_PLUGIN_PATH,
					 $_PluginUrl     = RS_LIQUIDEFFECT_PLUGIN_URL,
					 $_PluginTitle   = 'liquideffect',
				     $_FilePath      = __FILE__,
				     $_Version       = '6.7.3';
	
	public function __construct() {
		
		//load the textdomain, if the plugin should be translateable
		add_action('after_setup_theme', array($this, '_loadPluginTextDomain'), 10, 1);
		
		// check to make sure all requirements are met
		$notice = $this->systemsCheck();
		if($notice) {
			
			require_once(RS_LIQUIDEFFECT_PLUGIN_PATH . 'framework/notices.class.php');
			
			new RsAddOnLiquideffectNotice($notice, static::$_PluginTitle, static::$_Version);
			return;
			
		}
		
		parent::loadClasses();

	}

}
?>