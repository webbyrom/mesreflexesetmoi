=== Mes réflexes et moi - Custom Post Type ===
Contributors: romainfourel
Tags: custom post type, faq, wordpress, cpt
Requires at least: 6.0
Tested up to: 6.2.2
Requires PHP: 8.0
Stable tag: 1.0.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Un plugin WordPress pour gérer un Custom Post Type "FAQ" avec taxonomie personnalisée.

== Description ==

"Mes réflexes et moi - Custom Post Type" vous permet de créer un type de contenu personnalisé pour les FAQs (Foire Aux Questions).

Fonctionnalités principales :

* Custom Post Type : FAQ
* Taxonomie personnalisée : Questions-Réponses
* Compatible Gutenberg / REST API
* Slugs filtrables via hook
* Support des champs personnalisés, thumbnails, commentaires

== Installation ==

1. Uploadez le plugin dans le répertoire `/wp-content/plugins/`
2. Activez le plugin depuis le menu "Extensions" dans WordPress
3. Configurez vos FAQs via le menu "FAQ"

== FAQ ==

= Puis-je changer les slugs des URLs ? =

Oui, utilisez les filtres suivants dans votre `functions.php` :

    add_filter( 'reflexes_cpt_faq_slug', function() { return 'votre-slug'; });
    add_filter( 'reflexes_cpt_taxonomy_slug', function() { return 'votre-taxonomie'; });

== Changelog ==

= 1.0.5 =
* Optimisation du code
* Amélioration des traductions
* Meilleures pratiques WordPress

= 1.0.4 =
* Version initiale

== License ==

Ce plugin est distribue sous licence GPLv2 ou ultérieure.

