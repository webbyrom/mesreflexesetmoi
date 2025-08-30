<?php
defined('ABSPATH') or die('Acces non autorisé.');

/**
 * Class Customizer
 */
class Mrm_Customizer {

    private $text_domain = 'mesreflexesetmoi'; // Utilise un seul text domain

    public function __construct() {
        add_action('customize_register', array($this, 'register_customizations'));
        add_action('customize_preview_init', array($this, 'enqueue_customizer_scripts'));
    }

    public function register_customizations($wp_customize) {
        $this->add_logo_customization($wp_customize);
        $this->add_header_color_customization($wp_customize);
        $this->add_404_customization($wp_customize); // fonction pour la 404.
    }

    public function add_logo_customization(WP_Customize_Manager $manager) {
        $manager->add_section('mrm_logo_section', array(
            'title' => __('Votre Logo', $this->text_domain),
        ));

        $manager->add_setting('mrm_logo_setting', array(
            'sanitize_callback' => 'esc_url_raw',
        ));

        $manager->add_control(new WP_Customize_Image_Control($manager, 'mrm_logo_setting', array(
            'label' => __('Logo', $this->text_domain),
            'section' => 'mrm_logo_section',
        )));
    }

    public function add_header_color_customization(WP_Customize_Manager $manager) {
        $manager->add_section('mrm_header_section', array( // Nom de section plus précis.
            'title' => __('Couleur Header', $this->text_domain),
        ));

        $manager->add_setting('mrm_header_background', array( // nom plus précis.
            'default' => '#87d1c2',
            'transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color',
        ));

        $manager->add_control(new WP_Customize_Color_Control($manager, 'mrm_header_background', array( // nom plus précis.
            'label' => __('Header Color', $this->text_domain),
            'section' => 'mrm_header_section',
        )));
    }
    public function add_404_customization($wp_customize){
        $wp_customize->add_section('mrm_404_background_section', array(
            'title' => __('Image de fond 404', $this->text_domain),
            'priority' => 30,
        ));
        $wp_customize->add_setting('mrm_404_background_image', array(
            'default'=>'',
            'transport' => 'refresh',
            'sanitize_callback' => 'esc_url_raw',

        ));
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'mrm_404_background_image', array(
            'label' => __('Image de fond 404', $this->text_domain),
            'section' => 'mrm_404_background_section',
            'settings' => 'mrm_404_background_image',
        )));
    }

    public function enqueue_customizer_scripts() {
        if (is_customize_preview()) {
            wp_enqueue_script(
                'mrm_customizer_preview', // Nom de script plus précis.
                get_template_directory_uri() . 'src/assets/js/Mrm.js/apparence.js',
                array('jquery', 'customize-preview'),
                '',
                true
            );
        }
    }
}

new Mrm_Customizer();
