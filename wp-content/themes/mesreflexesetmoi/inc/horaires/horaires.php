<?php
// File: MrmHoraire.php

defined('ABSPATH') or die();
class MrmHoraire
{
    const GROUP = 'Horaires_cabinet';

    public static function register()
    {
        add_action('admin_menu', [self::class, 'addMenu']);
        add_action('admin_init', [self::class, 'registerSettings']);
        add_action('admin_enqueue_scripts', [self::class, 'registerScripts']);
    }

    public static function registerScripts($suffix)
    {
        if ($suffix === 'toplevel_page_' . self::GROUP) {
            wp_register_style('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', [], false);
            wp_register_script('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr', [], false, true);
            wp_register_script('Mrm_admin', get_template_directory_uri() . '/assets/js/Mrm.js/Mrm-admin.js', ['flatpickr'], false, true);
            wp_enqueue_style('flatpickr');
            wp_enqueue_script('flatpickr');
            wp_enqueue_script('Mrm_admin');

            $jours_semaine = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
            foreach ($jours_semaine as $jour) {
                wp_add_inline_script('Mrm_admin', "
                    flatpickr('#Mrm_options_horaire_$jour', {
                        enableTime: true,
                        noCalendar: true,
                        dateFormat: 'H:i',
                    });
                ");
            }
        }
    }

    public static function registerSettings()
    {
        register_setting(self::GROUP, 'Mrm_horaires_semaine');
        register_setting(self::GROUP, 'Mrm_conges');
        register_setting(self::GROUP, 'Mrm_telephone');
        register_setting(self::GROUP, 'Mrm_adresse');

        add_settings_section('Mrm_options_section', 'Paramètres', function () {
            echo "Vous pouvez gérer les horaires et les congés du cabinet.";
        }, self::GROUP);

        $jours_semaine = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
        foreach ($jours_semaine as $jour) {
            add_settings_field("Mrm_options_horaire_ouverture_$jour", "Heure d'ouverture pour le $jour", function () use ($jour) {
?>
                <input type="time" id="Mrm_options_horaire_ouverture_<?= $jour ?>" name="Mrm_horaires_semaine[<?= $jour ?>][ouverture]" value="<?= esc_attr(get_option('Mrm_horaires_semaine')[$jour]['ouverture'] ?? '') ?>">
<?php
            }, self::GROUP, 'Mrm_options_section');

            add_settings_field("Mrm_options_horaire_fermeture_$jour", "Heure de fermeture pour le $jour", function () use ($jour) {
?>
                <input type="time" id="Mrm_options_horaire_fermeture_<?= $jour ?>" name="Mrm_horaires_semaine[<?= $jour ?>][fermeture]" value="<?= esc_attr(get_option('Mrm_horaires_semaine')[$jour]['fermeture'] ?? '') ?>">
<?php
            }, self::GROUP, 'Mrm_options_section');
        }

        add_settings_field("Mrm_options_conges_debut", "Début de congés", function () {
?>
            <input type="datetime-local" id="Mrm_options_conges_debut" name="Mrm_conges[debut]" value="<?= esc_attr(get_option('Mrm_conges')['debut'] ?? '') ?>">
<?php
        }, self::GROUP, 'Mrm_options_section');

        add_settings_field("Mrm_options_conges_fin", "Fin de congés", function () {
?>
            <input type="datetime-local" id="Mrm_options_conges_fin" name="Mrm_conges[fin]" value="<?= esc_attr(get_option('Mrm_conges')['fin'] ?? '') ?>">
<?php
        }, self::GROUP, 'Mrm_options_section');

        add_settings_field("Mrm_options_telephone", "Numéro de téléphone", function () {
            $telephone = get_option('Mrm_telephone');
?>
            <input type="text" id="Mrm_options_telephone" name="Mrm_telephone" value="<?= esc_attr($telephone ?? '') ?>">
<?php
            if ($telephone && !self::validatePhoneNumber($telephone)) {
                echo '<p class="error">Numéro de téléphone invalide. Veuillez saisir un numéro de téléphone valide.</p>';
            }
        }, self::GROUP, 'Mrm_options_section');

        add_settings_field("Mrm_options_adresse", "Adresse", function () {
?>
            <textarea id="Mrm_options_adresse" name="Mrm_adresse"><?= esc_textarea(get_option('Mrm_adresse') ?? '') ?></textarea>
<?php
        }, self::GROUP, 'Mrm_options_section');

        add_action('admin_init', [self::class, 'saveSettings']);
    }

    public static function saveSettings()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $telephone = $_POST['Mrm_telephone'];
            $adresse = $_POST['Mrm_adresse'];

            if ($telephone && !self::validatePhoneNumber($telephone)) {
                add_settings_error('Mrm_telephone', 'invalid_phone', 'Numéro de téléphone invalide. Veuillez saisir un numéro de téléphone valide.');
                return;
            }

            update_option('Mrm_telephone', $telephone);
            update_option('Mrm_adresse', $adresse);
        }
    }

    public static function validatePhoneNumber($phone)
    {
        $cleanedPhone = preg_replace('/[^0-9]/', '', $phone);
        return preg_match('/^0[1-9]([0-9]{2}){4}$/', $cleanedPhone);
    }

    public static function addMenu()
    {
        add_menu_page(
            'Horaires du cabinet',
            'Horaires',
            'manage_options',
            self::GROUP,
            [self::class, 'render'],
            'dashicons-clock',
            25
        );
    }

    public static function render()
    {
        settings_errors(self::GROUP);
?>
        <h1>Horaires du cabinet</h1>
        <form action="options.php" method="post">
            <?php
            settings_fields(self::GROUP);
            do_settings_sections(self::GROUP);
            submit_button('');

            $horaires_semaine = get_option('Mrm_horaires_semaine');

            if (!empty($horaires_semaine)) {
                echo '<h2>Horaires d\'ouverture du cabinet</h2>';
                echo '<div class="horaires-wrapper">';
                $jours_semaine = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
                foreach ($jours_semaine as $jour) {
                    $horaire_ouverture = $horaires_semaine[$jour]['ouverture'] ?? '';
                    $horaire_fermeture = $horaires_semaine[$jour]['fermeture'] ?? '';
                    if (!empty($horaire_ouverture) && !empty($horaire_fermeture)) {
                        echo '<div class="horaire-item">';
                        echo '<div class="jour">' . ucfirst($jour) . '</div>';
                        echo '<div class="horaires">' . esc_html($horaire_ouverture) . ' - ' . esc_html($horaire_fermeture) . '</div>';
                        echo '</div>';
                    }
                }
                echo '</div>';

                $telephone = get_option('Mrm_telephone');
                $adresse = get_option('Mrm_adresse');

                if (!empty($telephone)) {
                    echo '<h2>Numéro de téléphone</h2>';
                    echo '<p>' . esc_html($telephone) . '</p>';
                }

                if (!empty($adresse)) {
                    echo '<h2>Adresse</h2>';
                    echo '<p>' . nl2br(esc_html($adresse)) . '</p>';
                }
            }
            ?>
        </form>
<?php
    }
}
