<?php
defined('ABSPATH') or die('Accès non autorisé');
/******
 * Template Name: Pelaude du Judo Connexion
 * Theme: Pelaude du Judo
 * Version: 1.0.1
 * Author: Romain Fourel / Webbyrom
 */
get_header(); ?>
<section class="Pdj-conect-logo container-fluid">
    <div class="Pdj-title-conect container-fluid">
        <h1 class="PDj-coTitle"><?php printf('%s - %s', get_bloginfo('name'), get_the_title()); ?></h1>
    </div>
    <div class="Pdj-logo-connect container-fluid">
        <img src="https://local.pelaude:7890/wp-content/uploads/2025/02/logo-ico-pelaude-fond-transparent.png" class="Pdj-connect-logo" alt="Pelaude du judo ?>">
    </div>
</section>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="entry-content container-fluid Pdj-form-access">
        <?php
        if (!is_user_logged_in()) {
            // On utilise wp_login_form() mais avec l'action correctement définie
            $args = array(
                'redirect' => home_url('/admin'), // Cela redirige vers la page que tu souhaites après la connexion
                'form_id' => 'loginform-custom',
                'label_username' => __('Nom d\'utilisateur ou e-mail'),
                'label_password' => __('Mot de passe'),
                'label_remember' => __('Se souvenir de moi'),
                'label_log_in' => __('Se connecter'),
                'value_username' => '', // Optionnel : préremplir si nécessaire
                'value_remember' => true // Optionnel : garder "se souvenir de moi" activé par défaut
            );
            wp_login_form($args); // Affiche le formulaire de connexion
        } else {
            echo '<p>Vous êtes déjà connecté. <a href="' . wp_logout_url(home_url()) . '">Déconnexion</a></p>';
        }
        ?>
    </div>
</article>

<?php get_footer(); ?>
