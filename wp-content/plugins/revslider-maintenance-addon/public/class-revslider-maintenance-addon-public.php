<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Revslider_Maintenance_Addon
 * @subpackage Revslider_Maintenance_Addon/public
 * @author     ThemePunch <info@themepunch.com>
 */
if(!defined('ABSPATH')) exit();
 
class Revslider_Maintenance_Addon_Public {

	/**
	 * The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 */
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function enqueue_styles() {
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 */
	public function enqueue_scripts() {
	}

	/**
	 * Maintenance Page
	 *
	 * Displays the coming soon page for anyone who's not logged in.
	 * The login page gets excluded so that you can login if necessary.
	 */
	public function maintenance_mode(){
		$enabled = get_option( "revslider_maintenance_enabled" );
		if(!$enabled) return;
		
		global $pagenow;
		
		$revslider_maintenance_addon_values = self::return_mta_data();

		//get current page and check if we are REST/AJAX, other variables to check if we are REST/AJAX do not yet exist. This way, we do allow ajax requests that are needed for v7
		$current_page = '//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$rest_url = str_replace(array('https:', 'http:'), '', rest_url());
		$ajax_url = str_replace(array('https:', 'http:'), '', admin_url('admin-ajax.php'));

		//if not login page, admin user, addon inactive show maintenance page
		if ( strpos($current_page, $rest_url) === false && strpos($current_page, $ajax_url) === false && !in_array($pagenow, array('wp-login.php', 'revslider-sharing-addon-call.php', 'revslider-login-addon-public-display.php'), true) && ! current_user_can( 'manage_options' ) && ! is_admin() ) {
			// Fix for 502 Error since 2.0.1
			//header( 'HTTP/1.1 Service Unavailable', true, 503 );
			//header( 'Content-Type: text/html; charset=utf-8' );
			$protocol = $_SERVER['SERVER_PROTOCOL'];
			if('HTTP/1.1' != $protocol && 'HTTP/1.0' != $protocol) $protocol = 'HTTP/1.0';

			header("$protocol 503 Service Unavailable", true, 503);
			header('Content-Type: text/html; charset=utf-8');
			if(file_exists(plugin_dir_path( __FILE__ ) . 'partials/revslider-maintenance-addon-public-display.php')){
				require_once(plugin_dir_path( __FILE__ ) . 'partials/revslider-maintenance-addon-public-display.php');
			}
			
			die();
		}
	
	}
	
	public static function return_mta_data(){
		$mta = array();
		parse_str(get_option('revslider_maintenance_addon'), $mta);

		//defaults
		$mta['revslider-maintenance-addon-type'] = isset($mta['revslider-maintenance-addon-type']) ? $mta['revslider-maintenance-addon-type'] : 'slider';
		// $mta['revslider-maintenance-addon-active'] = isset($mta['revslider-maintenance-addon-active']) ? $mta['revslider-maintenance-addon-active'] : '0';
		$mta['revslider-maintenance-addon-slider'] = isset($mta['revslider-maintenance-addon-slider']) ? $mta['revslider-maintenance-addon-slider'] : '';
		$mta['revslider-maintenance-addon-page'] = isset($mta['revslider-maintenance-addon-page']) ? $mta['revslider-maintenance-addon-page'] : '';

		//Date Defaults
		$date=date_create(date('Y-m-d G:i',time()));
		$default_date = date_format($date,"F d, Y");
		$default_hour = date_format($date,"G");
		$default_minute = date_format($date,"i");

		$mta['revslider-maintenance-addon-countdown-day'] = isset($mta['revslider-maintenance-addon-countdown-day']) ? $mta['revslider-maintenance-addon-countdown-day'] : $default_date;
		$mta['revslider-maintenance-addon-countdown-hour'] = isset($mta['revslider-maintenance-addon-countdown-hour']) ? $mta['revslider-maintenance-addon-countdown-hour'] : $default_hour;
		$mta['revslider-maintenance-addon-countdown-minute'] = isset($mta['revslider-maintenance-addon-countdown-minute']) ? $mta['revslider-maintenance-addon-countdown-minute'] : $default_minute;
		$mta['revslider-maintenance-addon-countdown-active'] = isset($mta['revslider-maintenance-addon-countdown-active']) ? $mta['revslider-maintenance-addon-countdown-active'] : '0';
		$mta['revslider-maintenance-addon-auto-deactive'] = isset($mta['revslider-maintenance-addon-auto-deactive']) ? $mta['revslider-maintenance-addon-auto-deactive'] : '0';
		
		$addonTime = strtotime($mta['revslider-maintenance-addon-countdown-day']." ".$mta['revslider-maintenance-addon-countdown-hour'].":".$mta['revslider-maintenance-addon-countdown-minute']);
		$currentTime = current_time('timestamp');
		
		/*
		 * This is the time difference between the scheduled time and the real time (server-side)
		 * the difference is set here in the wp_option and then read/printed by the JS in the public-display class for front-end calculation
		*/
		$mta['revslider-maintenance-addon-real-time'] = $addonTime - $currentTime;
		update_option('revslider_maintenance_addon', http_build_query($mta));
		
		//if autodeactivate is on and set autodeactivate
		if(isset($mta['revslider-maintenance-addon-auto-deactive']) && $mta['revslider-maintenance-addon-auto-deactive']){
			//if now exceeded end date turn maintenance off
			if($addonTime - $currentTime <= 0){
				$mta['revslider-maintenance-addon-active'] = 0;
				update_option('revslider_maintenance_addon', http_build_query($mta));
				update_option('revslider_maintenance_enabled', 0);
			}
		}
		
		return $mta;
	}
	
	public static function add_js($mta){
		global $rs_maintanence_script_added;
		if($rs_maintanence_script_added === true) return;
		
		$rs_maintanence_script_added = true;
		?>
		<script>
			// countdown.js Modificated for Revolution Slider
			// Original : https://github.com/hilios/jQuery.countdown
			!function(t){"use strict";"function"==typeof define&&define.amd?define(t):t()}((function(){"use strict";var t=[],e=[],s={precision:100,elapse:!1,defer:!1};e.push(/^[0-9]*$/.source),e.push(/([0-9]{1,2}\/){2}[0-9]{4}( [0-9]{1,2}(:[0-9]{2}){2})?/.source),e.push(/[0-9]{4}([\/\-][0-9]{1,2}){2}( [0-9]{1,2}(:[0-9]{2}){2})?/.source),e=new RegExp(e.join("|"));var o={Y:"years",m:"months",n:"daysToMonth",d:"daysToWeek",w:"weeks",W:"weeksToMonth",H:"hours",M:"minutes",S:"seconds",D:"totalDays",I:"totalHours",N:"totalMinutes",T:"totalSeconds"};function i(t,e){var s="s",o="";return t&&(1===(t=t.replace(/(:|;|\s)/gi,"").split(/\,/)).length?s=t[0]:(o=t[0],s=t[1])),Math.abs(e)>1?s:o}var n=function(e,o,i){this.el=e,this.interval=null,this.offset={},this.options=Object.assign({},s),this.instanceNumber=t.length,t.push(this),i&&("function"==typeof i?(this.el.addEventListener("update.countdown",i),this.el.addEventListener("stoped.countdown",i),this.el.addEventListener("finish.countdown",i)):this.options=Object.assign({},s,i)),this.setFinalDate(o),!1===this.options.defer&&this.start()};n.prototype.start=function(){null!==this.interval&&clearInterval(this.interval);var t=this;this.update(),this.interval=setInterval((function(){t.update.call(t)}),this.options.precision)},n.prototype.stop=function(){clearInterval(this.interval),this.interval=null,this.dispatchEvent("stoped")},n.prototype.toggle=function(){this.interval?this.stop():this.start()},n.prototype.pause=function(){this.stop()},n.prototype.resume=function(){this.start()},n.prototype.remove=function(){this.stop.call(this),t[this.instanceNumber]=null,delete this.el.dataset.countdownInstance},n.prototype.setFinalDate=function(t){this.finalDate=function(t){if(t instanceof Date)return t;if(String(t).match(e))return String(t).match(/^[0-9]*$/)&&(t=Number(t)),String(t).match(/\-/)&&(t=String(t).replace(/\-/g,"/")),new Date(t);throw new Error("Couldn't cast `"+t+"` to a date object.")}(t)},n.prototype.update=function(){if(document.body.contains(this.el)){var t,e=new Date;t=this.finalDate.getTime()-e.getTime(),t=Math.ceil(t/1e3),t=!this.options.elapse&&t<0?0:Math.abs(t),this.totalSecsLeft===t||this.firstTick?this.firstTick=!1:(this.totalSecsLeft=t,this.elapsed=e>=this.finalDate,this.offset={seconds:this.totalSecsLeft%60,minutes:Math.floor(this.totalSecsLeft/60)%60,hours:Math.floor(this.totalSecsLeft/3600)%24,days:Math.floor(this.totalSecsLeft/86400)%7,daysToWeek:Math.floor(this.totalSecsLeft/86400)%7,daysToMonth:Math.floor(this.totalSecsLeft/86400%30.4368),weeks:Math.floor(this.totalSecsLeft/604800),weeksToMonth:Math.floor(this.totalSecsLeft/604800)%4,months:Math.floor(this.totalSecsLeft/2629739.52),years:Math.abs(this.finalDate.getFullYear()-e.getFullYear()),totalDays:Math.floor(this.totalSecsLeft/86400),totalHours:Math.floor(this.totalSecsLeft/3600),totalMinutes:Math.floor(this.totalSecsLeft/60),totalSeconds:this.totalSecsLeft},this.options.elapse||0!==this.totalSecsLeft?this.dispatchEvent("update"):(this.stop(),this.dispatchEvent("finish")))}else this.remove()},n.prototype.dispatchEvent=function(t){var e={finalDate:this.finalDate,elapsed:this.elapsed,offset:Object.assign({},this.offset),strftime:t=>{return(e=this.offset,function(t){var s,n=t.match(/%(-|!)?[A-Z]{1}(:[^;]+;)?/gi);if(n)for(var a=0,h=n.length;a<h;++a){var r=n[a].match(/%(-|!)?([a-zA-Z]{1})(:[^;]+;)?/),l=(s=void 0,s=r[0].toString().replace(/([.?*+^$[\]\\(){}|-])/g,"\\$1"),new RegExp(s)),c=r[1]||"",f=r[3]||"",u=null;r=r[2],o.hasOwnProperty(r)&&(u=o[r],u=Number(e[u])),null!==u&&("!"===c&&(u=i(f,u)),""===c&&u<10&&(u="0"+u.toString()),t=t.replace(l,u.toString()))}return t.replace(/%%/,"%")})(t);var e}},s=new CustomEvent(t+".countdown",{detail:e});this.el.dispatchEvent(s)},window.countdown=function(t,e,s){new n(t,e,s)}}));

			(function() {
				function initCountDown(api) {
					<?php
					/*
					* the "real-time" is the AddOn scheduled time minus the server time
					* the difference is then concatenated onto the user-side time here for time-zone independent accuracy
					* (the countdown script is compatible with this new Epoch time stamp by default)
					*/
					?>
					var d = '<?php echo $mta['revslider-maintenance-addon-real-time']; ?>';
					var t = new Date().getTime();
					var targetdate = parseInt(t, 10) + (parseInt(d, 10) * 1000);

					var slidechanges = [
							{ days:0, hours:0, minutes:0, seconds:12, slide:2},
							{ days:0, hours:0, minutes:0, seconds:0, slide:3}
						],
						quickjump = 15000,
						t_days,
						t_hours,
						t_minutes,
						t_seconds;
													
					function maint_quick_change(a) {
						// Select all elements with class 'sr7-layer' or tag 'sr7-layer'
						const elements = document.querySelectorAll('.sr7-layer, sr7-layer');
						const retelements = [];
						const types = ["sr7-grp","sr7-row","sr7-col","container"];
						elements.forEach(function(element) {						
							// Check if element contains the specified text
							let type = SR7.F?.getLayer?.(element.id)?.c?.type ?? element.tagName.toLowerCase();							
							if (types.indexOf(type)==-1 && (element.textContent.includes('%' + a + '%') || element.textContent.includes('{{' + a + '}}'))) {
								retelements.push(element);																								
								// Replace the specified text patterns with the new HTML
								element.innerHTML = element.innerHTML
									.replace(new RegExp('%' + a + '%', 'g'), `<${a} class="${a}" style="display:inline-block;position:relative;">00</${a}>`)
									.replace(new RegExp('{{' + a + '}}', 'g'), `<${a} class="${a}" style="display:inline-block;position:relative;">00</${a}>`);								
							}
						});
						return retelements;
					}

					t_days = maint_quick_change("t_days");
					t_hours = maint_quick_change("t_hours");
					t_minutes = maint_quick_change("t_minutes");
					t_seconds = maint_quick_change("t_seconds");
										
					var currentd,currenth,currentm,currents;

					function animateAndUpdate(elements,nt,ot) {																
						for (var i in elements) {						
							if (!elements.hasOwnProperty(i)) continue;
							var o = elements[i];

							// Create an inner wrapper for the content
							const innerWrapper = document.createElement("div");
							innerWrapper.style.position = "relative";
							innerWrapper.style.display = "inline-block"; // Matches inline behavior
							innerWrapper.style.width = "100%";
							innerWrapper.style.height = "100%";
							innerWrapper.style.overflow = "hidden";

							// Move current content inside the wrapper
							while (o.firstChild) {
								innerWrapper.appendChild(o.firstChild);
							}
							o.appendChild(innerWrapper);
							
							if (ot==undefined) {
								o.innerHTML = nt;
							} else {							
								if (o.style.opacity!==" " && o.style.opacity!==0) {								
									punchgs.TweenLite.fromTo(innerWrapper,0.45,{autoAlpha:1,backfaceVisibility:"visible", rotationY:0,scale:1},{autoAlpha:0,rotationY:-180,scale:0.5,ease:punchgs.back.in,onComplete:function() { innerWrapper.innerHTML = nt;} });
									punchgs.TweenLite.fromTo(innerWrapper,0.45,{autoAlpha:0,rotationY:180,scale:0.5},{autoAlpha:1,rotationY:0,scale:1,ease:punchgs.back.out,delay:0.5 });
								} else o.innerHTML = nt;								
							}
						}
						return nt;
					}

					function countprocess(event) {						
						var newd = event.detail.strftime('%D'),
						newh = event.detail.strftime('%H'),
						newm = event.detail.strftime('%M'),
						news = event.detail.strftime('%S');

						<?php if($mta['revslider-maintenance-addon-auto-deactive']){ ?>if(newd=="00" && newh=="00" && newm=="00" && news=="00") window.location.reload();<?php } ?>

						if (t_days.length>0 && newd != currentd) currentd = animateAndUpdate(t_days,newd,currentd);
						if (t_hours.length>0 && newh != currenth) currenth = animateAndUpdate(t_hours,newh,currenth);
						if (t_minutes.length>0 && newm != currentm) currentm = animateAndUpdate(t_minutes,newm,currentm);
						if (t_seconds.length>0 && news != currents) currents = animateAndUpdate(t_seconds,news,currents);
						

						slidechanges.forEach(function(obj) {
							var dr = obj.days === undefined || obj.days >= newd,
								hr = obj.hours === undefined || obj.hours >= newh,
								mr = obj.minutes === undefined || obj.minutes >= newm,
								sr = obj.seconds === undefined || obj.seconds >= news;

							if (dr && hr && mr && sr && !obj.changedown) {
								api.showSlide(obj.slide);
								obj.changedown = true;
							}
						});						
					}
					//return {element:api.element, targetdate:targetdate, countprocess:countprocess};								
					window.countdown(api.element,targetdate, countprocess);
				}

				document.addEventListener("sr.module.ready", function (e) {
					if (window.countDownInited) return;
					window.countDownParams = initCountDown(window["revapi"+((e.id).split("_")[1])]);
					window.countDownInited = true;		
				});	
				/*document.addEventListener("sr.slide.afterChange", function (e) {
					if (window.countDownStarted) return;
					window.countdown(window.countDownParams.element,window.countDownParams.targetdate, window.countDownParams.countprocess);
					//initCountDown(window["revapi"+((e.id).split("_")[1])]);
					window.countDownStarted = true;		
				});*/								
			})();
		</script>
		<?php
	}
}
