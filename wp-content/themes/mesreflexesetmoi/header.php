<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="geo.placename" content="Auvergne-Rhône-Alpes">
    <meta name="robots" content="follow, index, max-image-preview:large, max-video-preview:-1">
    <meta name="googlebot" content="follow, index, max-image-preview:large, max-video-preview:-1">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <?php Mesreflexesetmoi_meta_tags(); ?>
    <title><?php the_title(); ?> <?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    <header id="Mrm_header" class="Mrm-header">
        <!------------ Nav Menu--------------->
        <nav id="Mrm_nav_menu" class="Mrm-nav-menu nav container-fluid" style="background-color: <?= get_theme_mod('mrm_header_background') ?>">
            <a href="#" id="Mrm_icon_responsive" class="Mrm-icon-responsive">
                <div class="Mrm-menu-icon menu-icon-animate">
                    <span class="Mrm-respon-icon1"></span>
                </div>

            </a>
            <!---------logo du site------------>
            <a href="<?= home_url('/'); ?>" class="nav-logo" title="<?= __('homepage', 'mesreflexesetmoi') ?>">
                <img src="<?= get_theme_mod('mrm_logo_setting') ?>" alt="" id="logo_img_header" class="logo-img-header">
            </a>
            <?= esc_html(Mrm_primary_nav()); ?>

        </nav>
    </header>
    <div id="Mrm_primary_content" class="Mrm-primary-content container-fluid">
        <div id="Mrm_secondary_content" class="Mrm-secondary-content container-fluid">
            <main id="Mrm_main" class="Mrm-main container-fluid">
                