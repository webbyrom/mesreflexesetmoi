<?php
// includes/post-types.php

defined('ABSPATH') || exit;

add_action('init', 'reflexes_cpt_register_post_type');

function reflexes_cpt_register_post_type() {

    $labels = [
        'name' => __('FAQ-reflexes', 'reflexes-cpt'),
        'singular_name' => __('FAQ-reflexes', 'reflexes-cpt'),
        'edit_item' => __('Modifier la FAQ-reflexes', 'reflexes-cpt'),
        'new_item' => __('Nouvelle FAQ-reflexes', 'reflexes-cpt'),
        'view_item' => __('Voir la FAQ-reflexes', 'reflexes-cpt'),
        'view_items' => __('voirles FAQ-refelxes','reflexes-cpt'),
        'search_items' => __('Rechercher une FAQ-reflexes', 'reflexes-cpt'),
        'not_found' => __('Aucune FAQ-reflexes trouvée', 'reflexes-cpt'),
        'not_found_in_trash' => __('Pas de FAQ-reflexesà la poubelle', 'reflexes-cpt'),
        'all_items' => __('Toutes les FAQ-reflexess', 'reflexes-cpt'),
        'archives'=> __('Archives FAQ-reflexes', 'reflexes-cpt')
    ];

    register_post_type('FAQ-reflexes', [
        'label' => __('FAQ-reflexes', 'reflexes-cpt'),
        'labels' => $labels,
        'public' => true,
        'hierarchical' => true,
        'menu_position' => 5,
        'menu_order' => 1,
        'menu_icon' => 'dashicons-paperclip',
        'supports' => ['title', 'editor', 'custom-fields', 'page-attributes', 'comments', 'author', 'excerpt', 'thumbnail'],
        'rewrite' => ['slug' => apply_filters('reflexes_cpt_faq_slug', 'FAQ-reflexes')],
        'show_in_rest' => true,
        'has_archive' => true,
        'taxonomies' => ['question-reponse'],
    ]);
}
