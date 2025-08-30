<?php
/**
 * Plugin Name: Mes réflexes et moi - Custom Post Type
 * Plugin URI: https://web-byrom.com
 * Description: Custom Post Type pour "Mes réflexes et moi" - FAQ
 * Version: 1.0.5
 * Author: Romain Fourel
 * Author URI: https://web-byrom.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 6.0.0
 * Tested up to: 6.2.2
 * Requires PHP: 8.0
 * Text Domain: reflexes-cpt
 */

// Sécurité WordPress
defined('ABSPATH') || exit;

require_once plugin_dir_path(__FILE__) . 'includes/post-types.php';
require_once plugin_dir_path(__FILE__) . 'includes/taxonomies.php';

register_activation_hook(__FILE__, 'reflexes_cpt_activate');
function reflexes_cpt_activate() {
    reflexes_cpt_register_post_type();
    reflexes_cpt_register_taxonomy();
    flush_rewrite_rules();
}

register_deactivation_hook(__FILE__, 'flush_rewrite_rules');