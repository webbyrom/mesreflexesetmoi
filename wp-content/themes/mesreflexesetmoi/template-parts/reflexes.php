<?php
defined('ABSPATH') || die('accès refusé');

/***
 * Template-parts pour l'affichage des exemples de la page parlons réflexes
 * Les exemples s'affichent en rond avec un fond différetns pour chaque réflexes
 * Utilise les articles wordpress( donc sa boucle).
 */
?>
<?php

$title_first = get_field('titre1');
$text_first = get_field('texte1des_articles_des_reflexes');
$title_second = get_field('titre2');
$texte_second = get_field('texte2reflexes');
$title_third = get_field('titre3');
$texte_third = get_field('texte_3_reflexes');

?>

<article class="Mmr-exemple-pr container-fluid">
    <div class="Mrm-title-contain-exmeple-pr container-fluid">
        <h3 class="Mrm-ttile-exemple"><?= esc_attr(get_the_title()); ?></h3>
    </div>

    <div class="Mrm-reflex-pr-wrapper container-fluid">
        <div class="Mrm-reflex-pr-item">
            <h4 class="Mrm-title-type">
                <?= !empty($title_first) ? wp_kses_post($title_first) : 'Titre non renseigné'; ?>
            </h4>
            <div class="Mrm-reflex-pr-content-textF">
                <?= !empty($text_first) ? wp_kses_post($text_first) : 'Le texte a disparu !!'; ?>
            </div>
        </div>

        <div class="Mrm-reflex-pr-item">
            <h4 class="Mrm-title-type">
                <?= !empty($title_second) ? wp_kses_post($title_second) : 'Titre non renseigné'; ?>
            </h4>
            <div class="Mrm-reflex-pr-content-textSE">
                <?= !empty($texte_second) ? wp_kses_post($texte_second) : 'Le texte a disparu !!'; ?>
            </div>
        </div>

        <div class="Mrm-reflex-pr-item">
            <h4 class="Mrm-title-type">
                <?= !empty($title_third) ? wp_kses_post($title_third) : 'Titre non renseigné'; ?>
            </h4>
            <div class="Mrm-reflex-pr-content-textF">
                <?= !empty($texte_third) ? wp_kses_post($texte_third) : 'Le texte a disparu !!'; ?>
            </div>
        </div>
    </div>
</article>
