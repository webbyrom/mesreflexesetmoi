<?php
defined('ABSPATH') || die('Accès non autorisé.');
/****
 * Template Name: Infos pratique
 * Theme: Mes reflexes et moi
 * Version: 1.0.5
 * Author: Romain Fourel
 * 
 * 
 */
get_header(); ?>
<section class="Mrm-sliderinfos container-fluid">
    <div class="SLider-Mrm container-fluid">
        <?php add_revslider('infos'); ?>
    </div>
</section>
<section class="Mrm_title-infos container-fluid">
    <div class="Mrm-infos-title container-fluid">
        <h1 class="titlte-Mrm"><?php the_title(); ?></h1>
    </div>
</section>
<section class="Mrm-form-contact container-fluid">
    <div class="Mrm-contact-form container-fluid">
        <h2>Me contacter</h2>
    </div>
    <div class="Mrm-contact-form container-fluid">
        <?= do_shortcode('[contact-form-7 id="e4608cb" title="Formulaire de contact page infos"]'); ?>
    </div>
</section>
<section class="Mrm-dayOpen container-fluid">
    <div id="Mrm_contact_hours" class="Mrm-contact-hours container-fluid">

        <?php
        $horaires_semaine = get_option('Mrm_horaires_semaine');
        $Mrm_congés = get_option('Mrm_conges');
        ?>
        <h3 class="Mrm-contact-horaires-title">Horaires d'ouverture du cabinet.</h3>
        <?php if (!empty($horaires_semaine)) : ?>
            <ul class="Mrm-contact-horaires">
                <?php foreach ($horaires_semaine as $jour => $horaires) : ?>
                    <?php if (!empty($horaires['ouverture']) && !empty($horaires['fermeture'])) : ?>
                        <?php
                        $ouverture_formattee = date('H:i', strtotime($horaires['ouverture']));
                        $fermeture_formattee = date('H:i', strtotime($horaires['fermeture']));
                        ?>
                        <li class="Mrm-contact-list-horaires"><?= ucfirst($jour) ?> : <?= esc_html($ouverture_formattee) ?> - <?= esc_html($fermeture_formattee) ?></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p class="Mrm-contact-coment">Les horaires d'ouverture ne sont pas disponibles.</p>
        <?php endif; ?>
    </div>
    <div id="Mrm_contact_conges" class="Mrm-contact-conges container-fluid">
        <h3 class="Mrm-contact-congés-title">Dates de congés du cabinet.</h3>
        <?php if (!empty($Mrm_congés['debut']) && !empty($Mrm_congés['fin'])) : ?>
            <?php
            $debut_formatte = date('d/m/Y H:i', strtotime($Mrm_congés['debut']));
            $fin_formattee = date('d/m/Y H:i', strtotime($Mrm_congés['fin']));
            ?>
            <p class="Mrm-contact-début-congé">Début de congés : <?= esc_html($debut_formatte) ?></p>
            <p class="Mrm-contact-fin-congé">Fin de congés : <?= esc_html($fin_formattee) ?></p>
        <?php else : ?>
            <p class="Mrm-contact-coment-congé">Les dates de congés ne sont pas disponibles.</p>
        <?php endif; ?>
        <div id="Mrm_info_anchor"></div>
    </div>
    <!-- Nouvelle section pour afficher le numéro de téléphone et l'adresse -->
    <div id="Mrm_contact_info" class="Mrm-contact-info container-fluid">
        <?php
        // Récupérer le numéro de téléphone et l'adresse enregistrés
        $telephone = get_option('Mrm_telephone');
        $adresse = get_option('Mrm_adresse');
        ?>

        <?php
        // Afficher le numéro de téléphone et l'adresse
        if (!empty($telephone)) {
        ?>
            <h4 class="Mrm-info-contact-title">Numéro de téléphone</h4>
            <p class="Mrm-contact-tel"><a class="Mrm-tel-contact" href="tel:<?php echo esc_html($telephone);?>"><span class="dashicons dashicons-phone m-2"></span><?php echo esc_html($telephone); ?></a></p>
        <?php
        }
        ?>

        <?php
        $adresse = get_option('Mrm_adresse');
        if (!empty($adresse)) {

        ?>
            <h4 class="Mrm-title-adress">Adresse</h4>
            <p class="Mrm-info-adress"><?= nl2br(esc_html($adresse)) ?></p>


        <?php
        }
        ?>
    </div>
</section>

<?php get_footer(); ?>
