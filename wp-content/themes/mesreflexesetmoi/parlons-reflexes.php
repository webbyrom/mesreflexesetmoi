<?php
defined('ABSPATH') || die('Accès non autorisé.');
/****
 * Template Name: Parlons réflexes
 * Theme: Mes reflexes et moi
 * Version: 1.0.0
 * Author: Romain Fourel
 * 
 * 
 */
get_header(); ?>
<section class="Mrm-slider-pr container-fluid">
    <div class="SLider-Mrm container-fluid">
        <?php add_revslider('Particle-Effect'); ?>
    </div>
</section>
<section class="Mrm_title-pr container-fluid">
    <div class="Mrm-st-title container-fluid">
        <h1 class="titlte-Mrm"><?php the_title(); ?></h1>
    </div>
</section>
<section class="Mrm-Pr container-fluid">
    <h2 class="Mrm-title-ksko">KESAKO ?</h2>
    <article class="Mrm-prkso container-fluid">
        <div class="Mrm-textLPr container-fluid">
            <!-----texte à gauche---->
            <?php
            $textGPr = get_field('texteG_parlons_reflexes');
            ?>
            <?php echo !empty($textGPr) ? wp_kses_post($textGPr) : 'Le texte n\'est pas encore disponible'; ?>
        </div>
        <div class="MRm-imgC container-fluid">
            <!--- image centrale---->
            <?php
            $Img_prc = get_field('img_1_parlons_reflexes');

            if (!empty($Img_prc)) : ?>
                <figure class="Mrm-figurePrcenter"><!---Mrm-figure-persoAge container-fluid--->
                    <img src="<?php echo esc_url($Img_prc); ?>" alt="" class="Mrm-Pr-img Mrm-Pr-img-color">
                <?php else : ?>
                    <img width="50%" height="250" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mN8c/CJCwAICQLXkCArnAAAAABJRU5ErkJggg==" alt="Image manquante">
                <?php endif; ?>

        </div>
        <div class="Mrm-textDpR container-fluid">
            <!-----texte à droite---->
            <?php
            $textdpr = get_field('texter_parlons_reflexes');
            echo !empty($textdpr) ? wp_kses_post($textdpr) : 'Le texte n\'est pas encore disponible';

            ?>
        </div>
    </article>
</section>
<section class="Mrm-exemple-pr-section container-fluid">
    <div class="Mrm-pr-exemple-contain-title container-fluid">
        <h2 class="Mrm-title-PRex">Par Exemples</h2>
    </div>
    <?php

    $reflex_arg = array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'category_name' => 'reflexe-archaique',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC',

    );
    $Query_reflexe = new WP_Query($reflex_arg);
    if ($Query_reflexe->have_posts()):
        while ($Query_reflexe->have_posts()) {
            $Query_reflexe->the_post();
            get_template_part('template-parts/reflexes');
        }

    endif;

    wp_reset_postdata();
    ?>

</section>
<section class="Mrm-wdothis container-fluid">
    <h5 class="MRm-wdothis-title">Et comment on s'en occupe? </h5>
    <div class="Mrm-button-pr container-fluid">
        <a href="/seance-type/" class="MRm-button" target="">une séance type</a>
    </div>
</section>

<?php get_footer(); ?>