<?php
// includes/taxonomies.php

defined('ABSPATH') || exit;

add_action('init', 'reflexes_cpt_register_taxonomy');

function reflexes_cpt_register_taxonomy() {

    $labels = [
        'name' => __('Questions-Réponses', 'reflexes-cpt'),
        'singular_name' => __('Question-Réponse', 'reflexes-cpt'),
        'search_items' => __('Rechercher Question-Réponse', 'reflexes-cpt'),
        'all_items' => __('Toutes les Questions-Réponses', 'reflexes-cpt'),
        'edit_item' => __('Modifier Question-Réponse', 'reflexes-cpt'),
        'view_item' => __('Voir Question-Réponse', 'reflexes-cpt'),
        'add_new_item' => __('Ajouter une nouvelle Question-Réponse', 'reflexes-cpt'),
        'new_item_name' => __('Nom de la nouvelle Question-Réponse', 'reflexes-cpt'),
    ];

    register_taxonomy('question-reponse', 'FAQ-reflexes', [
        'labels' => $labels,
        'hierarchical' => true,
        'show_ui' => true,
        'show_in_rest' => true,
        'rewrite' => ['slug' => apply_filters('reflexes_cpt_taxonomy_slug', 'question-reponse')],
        'show_admin_column' => true,
    ]);
}
