<?php
defined('ABSPATH') or die('exit');
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Mes réflexes et moi
 * @since Mes réflexes et moi 1.0.0
 */

?>
</main><!----#Mrm-main---->
</div><!----#Mrm-secondary-content---->

</div><!----#Mrm-primary-content---->


<footer class="Mrm-footer container-fluid">
    <?php
    if (is_active_sidebar('footer-nav')) {
        dynamic_sidebar('footer-nav');
    }
    ?>
    <div id="Mrm_scroll_top_button" class="Mrm-button-to-top">
        <a href="#" class="Mrm-scroll-totop">
            <span class="dashicons dashicons-arrow-up-alt2"></span>
        </a>
    </div>
    <?php wp_footer(); ?>
</footer>

</body>

</html>