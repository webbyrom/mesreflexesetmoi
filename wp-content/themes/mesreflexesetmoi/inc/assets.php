<?php 
defined('ABSPATH') or die('Acces non autorisé.');
/****
 * fichiers Css et Js nécéssaire au Thème
 * Theme: Mesreflexesetmoi
 * Version: 1.0.0
 * 
 * 
 */
function Mrm_scripts() {
    // Styles pour le front-end
    wp_enqueue_style('Mrm-styles-main', get_template_directory_uri(). '/build/style.css');
    wp_enqueue_style('Mrm-bootstrap-style', get_template_directory_uri(). '/src/assets/css/bootstrap/bootstrap.min.css');

    //Scripts pour le fornt-end 
    wp_enqueue_script('Mrm-main-script', get_template_directory_uri(). '/build/main.js', array('jquery'), false, true);//Ajout de la dépendence jQuery
    wp_enqueue_script('Mrm-boostrapt-script', get_template_directory_uri(). '/src/assets/js/bootstrap/bootstrap.bundle.min.js', array('jquery'), false, true);


}
add_action('wp_enqueue_scripts', 'Mrm_scripts');
