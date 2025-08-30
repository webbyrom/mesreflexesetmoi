<?php
defined('ABSPATH') || die('Accès non autorisé.');
/****
 * Template Name: Séance type
 * Theme: Mes reflexes et moi
 * Version: 1.0.0
 * Author: Romain Fourel
 *
 *
 */
get_header(); ?>
<section class="Mrm-sliderseance container-fluid">
  <div class="SLider-Mrm container-fluid">
    <?php add_revslider('seance-type'); ?>
  </div>
</section>
<section class="Mrm_title-st container-fluid">
  <div class="Mrm-st-title container-fluid">
    <h1 class="titlte-Mrm"><?php the_title(); ?></h1>
  </div>
</section>
<section class="Mrm-faq container-fluid">
  <article class="Mrm-faq-type container-fluid">

    <?php
    $foirs_arg = array(
      'post_type' => 'FAQ-reflexes', // Correction IMPORTANTE ici
      'post_status' => 'publish',
      'posts_per_page' => -1,
      'orderby' => 'title',
      'order' => 'ASC'
    );
    $foirs_query = new WP_Query($foirs_arg);
    if ($foirs_query->have_posts()) :
    ?>
      <div class="accordion accordion-flush" id="accordionFlushExample">
        <?php
        while ($foirs_query->have_posts()) {
          $foirs_query->the_post();
          // var_dump($foirs_query); die;
          //get_template_part('template-parts/foire.php');
          $texte_fq = get_field('texte_accordion');

          // ID unique basé sur l'ID du post
          $id_unique = 'faq-' . get_the_ID();
        ?>

          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?= esc_attr($id_unique); ?>" aria-expanded="false" aria-controls="<?= esc_attr($id_unique); ?>">
                <?= esc_html(get_the_title()); ?>
              </button>
            </h2>
            <div id="<?= esc_attr($id_unique); ?>" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
              <div class="accordion-body">
                <?= !empty($texte_fq) ? wp_kses_post($texte_fq) : 'Le texte a disparu !!'; ?>
              </div>
            </div>
          </div>
        <?php
        }
        ?>
      </div>
    <?php
    endif;

    wp_reset_postdata();

    ?>
  </article>
</section>
<section class="Mrm-setypede container-fluid">
  <?php
  //variable champ ACF integration
  $titleInteg = get_field('titre_partie_textest');
  $TextInteg = get_field('texte-seance-page');
  $ImgInteg = get_field('img-page-seance');

  //variable cham ACF Bilan
  $Titlebilan = get_field('titre-rdv-bilan');
  $TexteBilan = get_field('text-rdv-bilan');
  $imgBilan = get_field('img-rdv-bilan');

  ?>
  <div class="Mrm-setypede-title-contain container-fluid">
    <h3 class="Mrm-title-setypede">Déroulement d'une séance type</h3>
  </div>
  <article class="Mrm-setypede-articel container-fluid">
    <div class="Mrm-texte-setypede container-fluid">
      <div class="Mrm-texteSetypede-titlecontain container-fluid">
        <h4 class="Mrm-title-texteSetypede"><?= !empty($titleInteg) ? esc_html($titleInteg) : 'Titre perdu!'; ?>

        </h4>
      </div>
      <div class="Mrm-textContain-Setypede container-fluid">
        <?= !empty($TextInteg) ? wp_kses_post($TextInteg) : 'Pas de Texte!'; ?>

      </div>
    </div>
    <div class="Mrm-setypede-containImg container-fluid">
      <?php if (!empty($ImgInteg)) : ?>
        <figure class="Mrm-figure-setypede">
          <img src="<?php echo esc_url($ImgInteg); ?>" alt="" class="Mrm-Img-setypede">
        </figure>
      <?php else : ?>
        <img width="50%" height="250"
          src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mN8c/CJCwAICQLXkCArnAAAAABJRU5ErkJggg=="
          class="MRm-img-fake"
          alt="Image non disponible">
      <?php endif; ?>
    </div>

  </article><!-- partie bilan --->
  <article class="Mrm-setypede-articelInv container-fluid">
    <div class="Mrm-texte-setypede container-fluid">
      <div class="Mrm-texteSetypede-titlecontain container-fluid">
        <h4 class="Mrm-title-texteSetypede"><?= !empty($Titlebilan) ? esc_html($Titlebilan) : 'Titre perdu!'; ?>

        </h4>
      </div>
      <div class="Mrm-textContain-Setypede container-fluid">
        <?= !empty($TexteBilan) ? wp_kses_post($TexteBilan) : 'Pas de Texte!'; ?>

      </div>
    </div>
    <div class="Mrm-setypede-containImg container-fluid">
      <?php if (!empty($imgBilan)) : ?>
        <figure class="Mrm-figure-setypede">
          <img src="<?php echo esc_url($imgBilan); ?>" alt="" class="Mrm-Img-setypede">
        </figure>
      <?php else : ?>
        <img width="50%" height="250"
          src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mN8c/CJCwAICQLXkCArnAAAAABJRU5ErkJggg=="
          class="MRm-img-fake"
          alt="Image non disponible">
      <?php endif; ?>
    </div>
    </div>
  </article>
</section>

<?php get_footer(); ?>