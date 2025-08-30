<?php 
defined('ABSPATH') or die ('Accès non autorisé');

/***
 * fichiers de gestions des menus( main, footer, sidebar)
 * Theme: Mes Réflexes et moi
 * Version: 1.0.0
 * 
 * 
 */
if (!function_exists('Mrm_register_menu')) {
    function Mrm_register_menu()
    {
        register_nav_menus([
            'primary' =>  esc_html__('Primary menu', 'Mrm'),
            'footer'    =>  esc_html__('Menu Footer', 'Mrm'),
        ]);
    }
    add_action('init', 'Mrm_register_menu');
}
if (!function_exists('Mrm_primary_nav')) {
    function Mrm_primary_nav()
    {
        wp_nav_menu([
            'theme_location'    =>  'primary',
            'sort_column'   =>  'menu_order',
            'container' =>  'div',
            'container_class'   =>  'Mrm-collapse',
            'container_id'  =>  'Mrm_collapse',
            'container_aria_label'  =>  'Mrm_m_active',
            'menu_class'    =>  'Mrm-primary-menu-nav nav-menu',
            'menu_id'   =>  'Mrm_primary_menu_nav nav_menu',
            'echo'  =>  true,
            'show_home' =>  true,
            'before'    =>  '',
            'after' =>  '',
            'link_before'   =>  '<span>',
            'link_after'    =>  '</span>',
            'item_wrap' =>  '<ul id="%1$s" class="%2$s">%3$s</ul>',
            'item_spacing'  =>  'preserve',
            'depth' =>  0,
            'walker'    => '',
        ]);
    }
}
/****
 * customisation des éléments ul /li du menu et du sous menu 
 * 
 */
if (!function_exists('MrmCustom_nav_class_li')) {
    function MrmCustom_nav_class_li($classes, $item, $args)
    {
        if (in_array('menu-item-type-post_type', $classes)) {
            $classes[] = 'Mrm-nav-li';
        }
        return $classes;
    }
    add_filter('nav_menu_css_class', 'MrmCustom_nav_class_li', 10, 3);
}

if (!function_exists('Mrm_custom_nav_classes')) {
    function Mrm_custom_nav_classes($classes, $item, $args)
    {
        // Vérifiez si l'élément a la classe 'sub-menu' pour les sous-menus
        if (in_array('sub-menu', $classes)) {
            // Ajoutez la classe 'Mrm-submenu-ul' aux sous-menus
            $classes[] = 'Mrm-submenu-ul';
        }
        return $classes;
    }
    add_filter('nav_menu_submenu_css_class', 'Mrm_custom_nav_classes', 10, 3);
}

/*********
 * add active link custom class
 */
add_filter('nav_menu_css_class', 'Mrm_add_active_class', 10, 2);
function Mrm_add_active_class($classes, $item)
{
    if (
        $item->menu_item_parent == 0 &&
        in_array('current-menu-item', $classes) ||
        in_array('current-menu-ancestor', $classes) ||
        in_array('current-menu-parent', $classes) ||
        in_array('current_page_parent', $classes) ||
        // in_array('menu-item-has-children', $classes) ||
        in_array('current_page_ancestor', $classes)
    ) {
        $classes[] = 'MrM_m_active';
    }
    return $classes;
}
/****
 * Widget menu sidebar et footer
 * 
 */
require_once('widgets/social.php');// ne pas oublier sinon ça ne fonctionne pas 

add_action('widgets_init', function () {
    register_widget(MrM_Social_Widget::class);
    register_sidebar([
        'id'    => 'footer-nav',
        'name'  => __('Footer_nav', 'Mrm'),
        'class' => 'Mrm-footer-sidebar container-fluid',
        'before_title' => '<div class="footer-title">',
        'after_title'    => '</div>',
        'before_widget' => '<div class="footer_col">',
        'after_widget'   => '</div>'
    ]);
    register_sidebar([
        'id'    => 'blog',
        'name'  => __('Blog sidebar', 'Mrm'),
        'before_title' => '<div class="sidebar_title">',
        'after_title'    => '</div>',
        'before_widget' => '<div class="sidebar_widget">',
        'after_widget'   => '</div>'
    ]);
    
});
/***
 * icon reseaux sociaux du footer
 */
function Mrm_icon(string $name): string
{
    $spriteUrl = get_template_directory_uri() . '/assets/logo/logo-reseaux.svg';
    return <<<HTML
    <svg class="icon"><use xlink:href="{$spriteUrl}#{$name}"></use></svg>
    HTML;
}