<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2024 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsShapeBurstSlideFront extends RevSliderFunctions {
	
	private $title;
	
	public function __construct($title) {
		
		$this->title = $title;
		add_action('revslider_add_layer_attributes', array($this, 'write_layer_attributes'), 10, 3);
		add_filter('revslider_putCreativeLayer', array($this, 'check_shapeburst'), 10, 3);
	
	}
	
	// HANDLE ALL TRUE/FALSE
	private function isFalse($val) {
	
		$true = [true, 'on', 1, '1', 'true'];
		return !in_array($val, $true);
	
	}
	
	private function isEnabled($slider){
		
		$settings = $slider->get_params();
		$enabled = $this->get_val($settings, array('addOns', 'revslider-' . $this->title . '-addon', 'enable'), false);
		
		return ($this->isFalse($enabled)) ? false : true;
	}
	
	// removes shapeburst layers that may exist if the AddOn is not officially enabled
	public function check_shapeburst($layers, $output, $static_slide) {
		
		$slider = $this->get_val($output, 'slider', false);
		if(empty($slider)) return;
		// addon enabled
		if ($this->isEnabled($slider)) return $layers;
		$ar = array();
		foreach($layers as $layer) {
			$isShapeBurst = false;
			if(array_key_exists('subtype', $layer)) {
				$shapeburst = $this->get_val($layer, 'subtype', false);
				$isShapeBurst = $shapeburst === 'shapeburst';
			}
			if(!$isShapeBurst) $ar[] = $layer;
		}
		return $ar;
	}

	private function aO($val,$d,$s) {
		return $val==$d ? '' : $s.':'.$val.';';
	}

	private function convertColors($a) {
		if(!empty($a) && is_array($a)){
			foreach($a as $c => $v){
				$a[$c] = RSColorpicker::get($v);				
			}
		}				
		
		return $a;
	}
	
		
	public function write_layer_attributes($layer, $slide, $slider) {
		
		// addon enabled
		$enabled = $this->isEnabled($slider);
		if(empty($enabled)) return;		
		$subtype = $this->get_val($layer, 'subtype', '');
		if(!$subtype || $subtype !== 'shapeburst') return;
				
		$addOn = $this->get_val($layer, ['addOns', 'revslider-' . $this->title . '-addon'], false);
		if(!$addOn) return;
		

		//MAIN I
		$type = $this->get_val($addOn, 'type', 'default');
		$parbackground = $this->get_val($addOn, 'parbackground', 'default');
		$luminaBoost = $this->get_val($addOn, 'luminaBoost', 0);
		$darkLimit = $this->get_val($addOn, 'darkLimit', 0);
		$colorTrue = $this->get_val($addOn, 'colorTrue', false);
		$imgSource = $this->get_val($addOn, 'imgSource', "image");
		$textInput = $this->get_val($addOn, 'textInput', "Roboto");
		$textFont = $this->get_val($addOn, 'textFont', "Roboto");
		$textFontWeight = $this->get_val($addOn, 'textFontWeight', "300");
		$textColor = $this->get_val($addOn, 'textColor', "#ffffff");
		$textFontSize = $this->get_val($addOn, 'textFontSize', 200);
		$imgSize = $this->get_val($addOn, 'imgSize', "contain");
		$bspos = $this->get_val($addOn, 'bspos', "center center");
		
		//PARTICLES I
		$particlesCount = $this->get_val($addOn, 'particlesCount', 15000);
		$particleSize = $this->get_val($addOn, 'particleSize', 15);
		$randomness = $this->get_val($addOn, 'randomness', 20);
		$parSpeed = $this->get_val($addOn, 'parSpeed', 1);
		
		//INTERACTION I
		$explosionRadius = $this->get_val($addOn, 'explosionRadius', 35);
		$maxBurstDur = $this->get_val($addOn, 'maxBurstDur', 10);
		$explosionPower = $this->get_val($addOn, 'explosionPower', 30);
		$interSpeed = $this->get_val($addOn, 'interSpeed', 3);
		$sizeChOnInter = $this->get_val($addOn, 'sizeChOnInter', true);

		//TRANSITION I
		$transitionAnimTime = $this->get_val($addOn, 'transitionAnimTime', 30);
		$transitionON = $this->get_val($addOn, 'transitionON', true);

		//FX I
		$fxOn = $this->get_val($addOn, 'fxOn', false);
		$blurScale = $this->get_val($addOn, 'blurScale', 50);
		$fxOnlyOnInter = $this->get_val($addOn, 'fxOnlyOnInter', false);


		$parbackground = str_replace("http://","//",$parbackground);
		$parbackground = str_replace("https://","//",$parbackground);
		$datas = '';

		//MAIN II
		if($type != 'default') $datas .= 'ddw:'.$type.';';
		if($parbackground != 'default') $datas .= 'pbg:'.$parbackground.';';
		if($luminaBoost != 0) $datas .= 'ml:'.$luminaBoost.';';
		if($darkLimit != 0) $datas .= 'md:'.$darkLimit.';';
		if($colorTrue != false) $datas .= 'mc:'.$colorTrue.';';
		if($imgSource != "image") $datas .= 'mi:'.$imgSource.';';
		if($textInput != "Roboto") $datas .= 'mt:'.addslashes($textInput).';';
		if($textFont != "Roboto") $datas .= 'mf:'.addslashes($textFont).';';
		if($textFontWeight != "300") $datas .= 'mw:'.$textFontWeight.';';
		if($textColor != "#ffffff") $datas .= 'mo:'.$textColor.';';
		if($textFontSize != 200) $datas .= 'ms:'.$textFontSize.';';
		if($imgSize != "contain") $datas .= 'mz:'.$imgSize.';';
		if($bspos != "center center") $datas .= 'mp:'.$bspos.';';

		//PARTICLES II
		if($particlesCount != 15000) $datas .= 'pc:'.$particlesCount.';';
		if($particleSize != 15) $datas .= 'ps:'.$particleSize.';';
		if($randomness != 20) $datas .= 'pr:'.$randomness.';';
		if($parSpeed != 1) $datas .= 'pp:'.$parSpeed.';';

		//INTERACTION II
		if($explosionRadius != 35) $datas .= 'ir:'.$explosionRadius.';';
		if($maxBurstDur != 10) $datas .= 'id:'.$maxBurstDur.';';
		if($explosionPower != 30) $datas .= 'ip:'.$explosionPower.';';
		if($interSpeed != 3) $datas .= 'ie:'.$interSpeed.';';
		if($sizeChOnInter != true) $datas .= 'ic:'.$sizeChOnInter.';';

		//TRANSITION II
		if($transitionAnimTime != 30) $datas .= 'ta:'.$transitionAnimTime.';';
		if($transitionON != true) $datas .= 'tr:'.$transitionON.';';

		//FX II
		if($fxOn != false) $datas .= 'fx:'.$fxOn.';';
		if($blurScale != 50) $datas .= 'fb:'.$blurScale.';';
		if($fxOnlyOnInter != false) $datas .= 'fo:'.$fxOnlyOnInter.';';


		$fontloader = new RevSliderOutput();
		$fontloader->set_clean_font_import($textFont, '', '', (array)$textFontWeight);

		echo RS_T8 . 'data-wpsdata="' .$datas.'"'."'\n";
	}
	
}
?>