<?php
defined('ABSPATH') or die('Acces non autorisé.');
/****
 * Theme: Mesreflexesetmoi
 * Version: 1.0.1
 * Template Name: Acceuil / Home
 * Author: Romain Fourel / Web-byrom
 *  
 */
get_header(); ?>
<section class="Mrm-slider-home container-fluid">
    <div class="SLider-Mrm container-fluid">
        <!-------slider ici ------>
        <?php add_revslider('Professional-Training-Slider-Template1'); ?>

    </div>
</section>
<section class="Mrm-title-home container-fluid">
    <div class="Mrm-home-title container-fluid">
        <h1 class="title-Mrm"><?php the_title() ?></h1>
    </div>
</section>
<?php //remplace la section les refelxes archaïques
mrm_display_section_custom(
    'image_pour_le_texte_1_',
    'presentation_texte_1',
    'bouton_voir_plus',
    'Mrm-Presentaiotn-home container-fluid',
    'Mrm-prez-Img container-fluid',
    'MRm-prezImg container-fluid',
    'Mrm_ImgPrez1',
    'Mrm-img1-prezt',
    'Mrm-text1 container-fluid',
    'Mrm-text1-title container-fluid',
    'MrmTitleRefelxe',
    'Mrmtext1 container-fluid',
    'Mrm-button container-fluid',
    'les Réfelxes archaïques'

)
?>
<div class="Mrm-sectionWhy-who container-fluid">
    <h3 class="MrmtitleWhyWho">Pour Qui-Pour Quoi?</h3>
</div>

<?php mrm_display_section_customPourquiPourquoi(
    'Mrm-PqPquoi container-fluid', // $section_class
    'Mrm-PourQ container-fluid', // $article_enfants_class
    'Mrm-textPourQEnf container-fluid', // $text_enfants_container_class
    'Mrm-pourChildTitle container-fluid', // $text_enfants_title_class
    'Mrm-titlechild-ado', // $title_enfants_class
    'Mrm_child-text container-fluid', // $text_enfant_para_class
    'Mrm-button container-fluid', // $button_container_class (enfant)
    'Mrm-textPourqImg container-fluid', // $image_enfnats_container_class
    'Mrm_ImgrefelxChild', // $images_figure_enf_class
    'Mrm-child-reflexes', // $imgae_enfants_class
    'MrM-textePourQAdu container-fluid', // $article_adulte_class
    'Mrm-ImgtextePourQAdu container-fluid', // $img_adult_container_class
    'Mrm-imgAdulte', // $img_figure_adulte_class
    'Mrm-img-adulte', // $img_adult_class
    'MRm-textAdulte container-fluid', // $Texte_adulte_container_class
    'Mrm-title-adulte container-fluid', // $title_container_adulte_class
    'Mrm-adulte-title', // $title_adulte_class
    'Mrm-texte-adulte-paragraphe container-fluid', // $texte_adult_paragr_class (j'ai utilisé une classe générique, adaptez-la si nécessaire)
    'Mrm-button container-fluid', // $button_container_class (adulte)
    'bouton_voir_plus', // $button_field
    'Mrm-PersoAge container-fluid', // $article_age_container_class'
    'Mrm-text-contain-PersoAge container-fluid', // $text_age_container_class'
    'Mrm-persoAge-title container-fluid', // $title_container_age_class
    'Mrm-title-persoAge', // $title_persoAge
    'Mrm-para-persoAge container-fluid', // $text_age_para_class
    'Mrm-button container-fluid', // $button_container_age_class
    'Mrm-persoAge-img container-fluid', // $img_Persp_age_container_class
    'Mrm-figure-persoAge container-fluid' // $img_figur_PersoAge_class'
) ?>

<section class="Mrm-miseGarde container-fluid">
    <div class="Mrm-garde container-fluid">
        <h4 class="Mrm-titleMeG">Les raisons de consulter</h4>
        <?php
        $TextMiseGarde = get_field('mise_en_garde'); ?>
        <p class="Mrm-textMeG container-fluid">
            <?php echo !empty($TextMiseGarde) ? wp_kses_post($TextMiseGarde) : 'Le texte n\'est pas encore disponible'; ?>
        </p>
       
       
    </div>
</section>
<?php get_footer(); ?>