<?php
defined('ABSPATH') or die('Accès non autorisé');
/****
 * Theme: Mes reflexes et moi
 * Description: Thème responsive, Wordrpress , 
 * 
 * 
 * 
 * 
 */
require_once('inc/menus.php');
require_once('inc/assets.php');
require_once('inc/appearence.php');
require_once('inc/horaires/horaires.php');
MrmHoraire::register();

// fonctions
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('menus');
    add_theme_support('html5', [
        'comment-list',
        'comment-form',
        'search-form',
        'gallery',
        'caption',
        'style',
        'script',
        'navigation-widgets'
    ]);
    add_theme_support('post-thumbnails');
    add_theme_support('post-formats', array(
        'aside',
        'gallery',
        'link',
        'quote',
        'audio',
        'image',
        'status'
    ));
    add_theme_support('custom-header');
    add_theme_support('automatic-feed-links');
    add_theme_support('wp-block-styles');
    add_theme_support('custom-logo', array(
        'heigth'    => 100,
        'width' => 400,
        'flex-height'   => true,
        'flex-width'    => true,
        'header-text'   => array('site-title', 'site-description')
    ));
    // Add theme support for selective refresh for widgets.
    add_theme_support('customize-selective-refresh-widgets');

    //Add support for full and wide align images
    add_theme_support('align-wide');
});

/****
 * fonctions pour les mméta description 
 * 
 * 
 * 
 */
function Mesreflexesetmoi_meta_tags()
{
    global $post;

    // Détermination de la description
    if (is_front_page() || is_home()) {
        $meta_description = get_bloginfo('description');
    } elseif (get_post_meta(get_the_ID(), 'meta_description', true)) {
        $meta_description = get_post_meta(get_the_ID(), 'meta_description', true);
    } else {
        $meta_description = wp_trim_words(get_the_content(), 20, '...');
    }

    // Image de partage : dynamique via wp_get_upload_dir
    $upload_dir = wp_get_upload_dir();
    $default_image = $upload_dir['baseurl'] . '/2025/02/screenshot.png';
    $meta_image = get_the_post_thumbnail_url(get_the_ID(), 'large') ?: $default_image;

    $meta_title = get_the_title() . ' | ' . get_bloginfo('name');
    $og_type = (is_single() || is_page()) ? 'article' : 'website';

?>
    <!-- Meta SEO -->
    <meta name="description" content="<?php echo esc_attr($meta_description); ?>">
    <meta name="author" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow">

    <!-- Open Graph (Facebook, LinkedIn, etc.) -->
    <meta property="og:locale" content="fr-FR">
    <meta property="og:title" content="<?php echo esc_attr($meta_title); ?>">
    <meta property="og:type" content="<?php echo esc_attr($og_type); ?>">
    <meta property="og:url" content="<?php echo esc_url(get_permalink()); ?>">
    <meta property="og:image" content="<?php echo esc_url($meta_image); ?>">
    <meta property="og:description" content="<?php echo esc_attr($meta_description); ?>">
    <meta property="og:site_name" content="<?php echo esc_attr(get_bloginfo('name')); ?>">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc_attr($meta_title); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr($meta_description); ?>">
    <meta name="twitter:image" content="<?php echo esc_url($meta_image); ?>">
    <meta name="twitter:site" content="@votrecompte">

    <!-- Balises supplémentaires -->
    <meta name="geo.region" content="FR">
    <meta name="geo.placename" content="Auvergne-Rhône-Alpes">
    <meta name="geo.position" content="45.764043;4.835659">
    <meta name="ICBM" content="45.764043, 4.835659">
    <link rel="canonical" href="<?php echo esc_url(get_permalink()); ?>">
<?php
}
/*****
 * Fonction pour le titre de la page et du site 
 * 
 */

function Mrm_title($title)
{
    // Récupérer le titre de la page actuelle
    $current_title = get_the_title();

    // Ajouter le séparateur et le nom du site au titre existant
    $title .= ' - ' . get_bloginfo('name', 'display');

    // Ajouter le titre de la page actuelle au début
    $title = $current_title . $title;

    return $title;
}
add_filter('wp_title', 'Mrm_title', 10, 2);

/***
 * create function the excerpt to a different file
 * 
 * Filter the except length to 20 words.
 *
 * @param int $length Excerpt length.
 * @return int (Maybe) modified excerpt length.
 * 
 * créer un extrait et un bouton lire la suite
 */

function Mrm_custom_excerpt_length($text, $length = 20, $custom_permalink = '', $custom_anchor = '')
{
    $excerpt = wp_trim_words($text, $length);

    // Récupérer l'URL du site
    $permalink = home_url();

    // Si un permalien personnalisé est fourni, utiliser celui-ci
    if (!empty($custom_permalink)) {
        $permalink = esc_url($custom_permalink);
    }

    // Générer le lien "Lire la suite"
    if (empty($custom_anchor)) {
        $read_more_link = '<a href="' . $permalink . '" class="read-more-link btn btn-outline-light">Lire la suite <i class="fa-regular fa-circle-right fa-fade"></i></a>';
    } else {
        $anchor = '#' . $custom_anchor;
        $read_more_link = '<a href="' . $permalink . $anchor . '" class="read-more-link btn btn-outline-light">Lire la suite <i class="fa-regular fa-circle-right fa-fade"></i></a>';
    }

    return wp_kses_post($excerpt . ' ' . $read_more_link);
}
add_filter('excerpt_length', 'Mrm_custom_excerpt_length', 999);
/********
 * Allow SVG donwload
 * Filters the list of allowed mime types and file extensions.
 *
 * @param array             $t    Mime types keyed by the file extension regex corresponding to those types.
 * @param int|\WP_User|null $user User ID, User object or null if not provided (indicates current user).
 * @return array Mime types keyed by the file extension regex corresponding to those types.
 */
add_filter('upload_mimes', function ($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    // autoriser les fichiers avec extensions.ico
    $mimes['ico'] = 'image/x-icon';
    return $mimes;
});

/**
 * Génère une section dynamique avec image, titre, texte et bouton ACF.
 * Permet de personnaliser toutes les classes CSS des éléments HTML.
 * Utiliser pour la page accueil 
 * @param string $image_field Nom du champ ACF pour l'image.
 * @param string $text_field Nom du champ ACF pour le texte.
 * @param string $button_field Nom du champ ACF pour le bouton.
 * @param string $section_class Classe CSS pour la <section>.
 * @param string $article_class Classe CSS pour le <article>.
 * @param string $image_container_class Classe CSS pour le conteneur d'image.
 * @param string $image_class Classe CSS pour l'image.
 * @param string $text_container_class Classe CSS pour le conteneur du texte.
 * @param string $title_class Classe CSS pour le titre.
 * @param string $paragraph_class Classe CSS pour le paragraphe.
 * @param string $button_container_class Classe CSS pour le conteneur du bouton.
 * @param string $title Texte du titre de la section.
 */
function mrm_display_section_custom(
    $image_field,
    $text_field,
    $button_field,
    $section_class,
    $article_class,
    $image_container_class,
    $figure_class,
    $image_class,
    $text_container_class,
    $title_container_class,
    $title_class,
    $paragraph_class,
    $button_container_class,
    $title
) {
    $image_url = get_field($image_field);
    $text = get_field($text_field);
?>
    <section class="<?php echo esc_attr($section_class); ?> container-fluid">
        <article class="<?php echo esc_attr($article_class); ?>">
            <div class="<?php echo esc_attr($image_container_class); ?>">
                <?php if (!empty($image_url)) : ?>
                    <figure class="<?php echo esc_attr($figure_class); ?>">
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>" class="<?php echo esc_attr($image_class); ?>">
                    </figure>
                <?php else : ?>
                    <img width="50%" height="250"
                        src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mN8c/CJCwAICQLXkCArnAAAAABJRU5ErkJggg=="
                        class="<?php echo esc_attr($image_class); ?>"
                        alt="Image non disponible">
                <?php endif; ?>
            </div>

            <div class="<?php echo esc_attr($text_container_class); ?>">
                <div class="<?php echo esc_attr($title_container_class); ?>">
                    <h2 class="<?php echo esc_attr($title_class); ?>"><?php echo esc_html($title); ?></h2>
                </div>
                <p class="<?php echo esc_attr($paragraph_class); ?>">
                    <?php echo !empty($text) ? wp_kses_post($text) : 'Le texte n\'est pas encore disponible'; ?>
                </p>
                <div class="<?php echo esc_attr($button_container_class); ?>">
                    <?php mrm_display_button($button_field, 'Mrm-button-link'); ?>
                </div>
            </div>


        </article>
    </section>
    <?php
}
/**
 * Fonction pour afficher un bouton avec un lien provenant d'un champ ACF.
 *
 * @param string $acf_field_name Le nom du champ ACF contenant l'URL du lien.
 * @param string $class Le nom de classe CSS optionnel à ajouter au bouton (par défaut : '').
 */
function mrm_display_button($acf_field_name, $class = '')
{
    // Vérifier si Advanced Custom Fields est activé
    if (!function_exists('get_field')) {
        return;
    }

    // Récupérer la valeur du champ ACF
    $MrmseeMoore = get_field($acf_field_name);

    // Vérifier si l'URL est valide et non vide
    if (!empty($MrmseeMoore) && filter_var($MrmseeMoore, FILTER_VALIDATE_URL)) {
        $link_destination = esc_url($MrmseeMoore);
        $page_id = url_to_postid($link_destination);
        $target = '_blank'; // Ouvre dans un autre onglet
        // Vérifier si c'est un lien externe
        if ($page_id === 0) {
            $target = '_blank'; // Ouvrir dans un nouvel onglet pour les liens externes
        }

        // Récupérer le titre de la page si c'est un lien interne
        $button_text = 'En savoir plus';

    ?>
        <a href="<?php echo esc_url($link_destination); ?>"
            class="Mrm-button-home <?php echo esc_attr($class); ?>"
            target="<?php echo esc_attr($target); ?>"
            rel="noopener noreferrer">
            <?php echo esc_html($button_text); ?>
        </a>
    <?php
    } else {
        // Affichage en cas de lien invalide
    ?>
        <span class="lien-brisé">Lien invalide</span>
    <?php
        error_log('URL invalide détectée: ' . ($MrmseeMoore ? esc_url($MrmseeMoore) : 'Vide'));
    }
}
/******
 * Fonction pour générer la section dynamique appelée pour qui - pour quoi
 * Génére l'image, le texte et le bouton en savoir plus à partir de champ ACF 
 * Permet la personnalisaton des classes CSS des éléments HTML
 * 
 * @param string $section_class Classe CSS pour la <section>.
 * @param string $article_enfants_class Classe CSS pour le <article> enfants.
 * @param string $text_enfants_container_class Classe CSS pour le conteneur du texte enfants.
 * @param string $text_enfants_title_class Classe CSS pour le titre du texte enfants.
 * @param string $title_enfants_class Classe CSS pour le titre enfants.
 * @param string $text_enfant_para_class Classe CSS pour le paragraphe du texte enfants.
 * @param string $button_container_class Classes CSS pour le conteneur du bouton.
 * @param string $button_field Nom du champ ACF pour le bouton.
 * @param string $image_enfnats_container_class Classe CSS pour le conteneur d'image enfants.
 * @param string $images_figure_enf_class Classe CSS pour la <figure> enfants.
 * @param string $imgae_enfants_class Classe CSS pour l'image enfants.
 * @param string $article_adulte_class Classe CSS pour le <article> adultes.
 * @param string $img_adult_container_class Classe CSS pour le conteneur d'image adultes.
 * @param string $img_figure_adulte_class Classe CSS pour la <figure> adultes.
 * @param string $img_adult_class Classe CSS pour l'image adultes.
 * @param string $Texte_adulte_container_class Classe CSS pour le conteneur du texte adultes.
 * @param string $title_container_adulte_class Classe CSS pour le conteneur du titre adultes.
 * @param string $title_adulte_class Classe CSS pour le titre adultes.
 * @param string $texte_adult_paragr_class Classe CSS pour le paragraphe du texte adultes.
 * 
 *
 */
function mrm_display_section_customPourquiPourquoi(
    $section_class,
    $article_enfants_class,
    $text_enfants_container_class,
    $text_enfants_title_class,
    $title_enfants_class,
    $text_enfant_para_class,
    $button_container_enfants_class, // Pour les enfants
    $image_enfants_container_class,
    $images_figure_enf_class,
    $image_enfants_class,
    $article_adulte_class,
    $img_adult_container_class,
    $img_figure_adulte_class,
    $img_adult_class,
    $texte_adulte_container_class,
    $title_container_adulte_class,
    $title_adulte_class,
    $texte_adult_paragr_class,
    $button_container_adulte_class, // Pour les adultes
    $button_field, // nom d champ ACF pour le bouton 
    $article_age_container_class, // pour les personnes agées //Mrm-PersoAge container-fluid
    $text_age_container_class, //Mrm-text-contain-PersoAge container-fluid
    $title_container_age_class, //Mrm-persoAge-title container-fluid
    $title_persoAge, //Mrm-title-persoAge
    $text_age_para_class, //Mrm-para-persoAge container-fluid"
    $button_container_age_class, //Mrm-button-persoAge container-fluid
    $img_Persp_age_container_class, //Mrm-persoAge-img container-fluid
    $img_figur_PersoAge_class, //Mrm-figure-persoAge container-fluid




) {
    $ImgReChild = get_field('reflexes_enfants');
    $MrmChildTexte = get_field('texte_reflexes_enfants');
    $ImgAdultes = get_field('images_reflexes_adultes');
    $TexteAdult = get_field('texte_refelxes_adulte');
    $textAge = get_field('text_personnes_agees');
    $Imag_persoAge = get_field('image_personne_ages');

    ?>

    <section class="<?php echo esc_attr($section_class); ?>">

        <!-- Bloc Enfants et Adolescents -->
        <article class="<?php echo esc_attr($article_enfants_class); ?>">
            <div class="<?php echo esc_attr($text_enfants_container_class); ?>">
                <div class="<?php echo esc_attr($text_enfants_title_class); ?>">
                    <h3 class="<?php echo esc_attr($title_enfants_class); ?>">Enfants et Adolescents</h3>
                </div>


                <p class="<?php echo esc_attr($text_enfant_para_class); ?>">
                    <?php echo !empty($MrmChildTexte) ? wp_kses_post($MrmChildTexte) : 'Le texte n\'est pas encore disponible'; ?>
                </p>

                <div class="<?php echo esc_attr($button_container_enfants_class); ?>">
                    <?php mrm_display_button($button_field, 'Mrm-button-enfant-ado'); ?>
                </div>
            </div>

            <div class="<?php echo esc_attr($image_enfants_container_class); ?>">
                <?php if (!empty($ImgReChild)) : ?>
                    <figure class="<?php echo esc_attr($images_figure_enf_class); ?>">
                        <img src="<?php echo esc_url($ImgReChild); ?>" alt="Image enfants" class="<?php echo esc_attr($image_enfants_class); ?>">
                    </figure>
                <?php else : ?>
                    <img width="50%" height="250" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mN8c/CJCwAICQLXkCArnAAAAABJRU5ErkJggg==" alt="Image manquante">
                <?php endif; ?>
            </div>
        </article>

        <!-- Bloc Adultes -->
        <article class="<?php echo esc_attr($article_adulte_class); ?>">
            <div class="<?php echo esc_attr($img_adult_container_class); ?>">
                <?php if (!empty($ImgAdultes)) : ?>
                    <figure class="<?php echo esc_attr($img_figure_adulte_class); ?>">
                        <img src="<?php echo esc_url($ImgAdultes); ?>" alt="Image adultes" class="<?php echo esc_attr($img_adult_class); ?>">
                    </figure>
                <?php else : ?>
                    <img width="50%" height="250" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mN8c/CJCwAICQLXkCArnAAAAABJRU5ErkJggg==" alt="Image manquante">
                <?php endif; ?>
            </div>

            <div class="<?php echo esc_attr($texte_adulte_container_class); ?>">
                <div class="<?php echo esc_attr($title_container_adulte_class); ?>">
                    <h3 class="<?php echo esc_attr($title_adulte_class); ?>">Adultes</h3>
                </div>


                <p class="<?php echo esc_attr($texte_adult_paragr_class); ?>">
                    <?php echo !empty($TexteAdult) ? wp_kses_post($TexteAdult) : 'Le texte n\'est pas encore disponible'; ?>

                </p>


                <div class="<?php echo esc_attr($button_container_adulte_class); ?>">
                    <?php mrm_display_button($button_field, 'Mrm-button-adulte'); ?>
                </div>
            </div>
        </article>
        <!-------bloc personnes agées----->
        <article class="<?php echo esc_attr($article_age_container_class); ?>"><!--Mrm-PersoAge container-fluid-->
            <div class="<?php echo esc_attr($text_age_container_class); ?>">
                <div class="<?php echo esc_attr($title_container_age_class); ?>"><!---Mrm-persoAge-title container-fluid-->
                    <h3 class="<?php echo esc_attr($title_persoAge); ?>">Personnes agées</h3><!---Mrm-title-persoAge-->
                </div>

                <p class="<?php echo esc_attr($text_age_para_class); ?>">
                    <?php echo !empty($textAge) ? wp_kses_post($textAge) : 'Le texte n\'est pas encore disponible'; ?>
                </p>

                <div class="<?php echo esc_attr($button_container_age_class); ?>"><!--Mrm-button-persoAge container-fluid-->
                    <?php mrm_display_button($button_field, 'Mrm-button-P-age'); ?>
                </div>
            </div>
            <div class="<?php echo esc_attr($img_Persp_age_container_class); ?>"><!---Mrm-persoAge-img container-fluid--->
                <?php if (!empty($Imag_persoAge)) : ?>
                    <figure class="<?php echo esc_attr($img_figur_PersoAge_class); ?>"><!---Mrm-figure-persoAge container-fluid--->
                        <img src="<?php echo esc_url($Imag_persoAge); ?>" alt="Image personnes agées" class="<?php echo esc_attr($img_adult_class); ?>"><!--Mrm-img-persoAgImg--->
                    </figure>
                <?php else : ?>
                    <img width="50%" height="250" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mN8c/CJCwAICQLXkCArnAAAAABJRU5ErkJggg==" alt="Image manquante">
                <?php endif; ?>
            </div>
        </article>


    </section>

<?php
}
/****
 * fonctions pour interdire l'accès aux articles 
 * 
 */
add_action('template_redirect', function () {
    if (is_single() && get_post_type() === 'post') {
        wp_redirect(home_url('/'), 301);
        exit;
    }
});
