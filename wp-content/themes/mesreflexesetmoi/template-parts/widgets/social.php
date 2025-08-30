<?php

$networks = [
    'facebook'  => 'Facebook',
    'instagram' => 'Instagram',
    'tiktok'    =>  'TikTok'
];
$site_link = !empty($instance['site_link']) ? esc_url($instance['site_link']) : '';
$logo_image = !empty($instance['logo_image']) ? '<img src="' . esc_url($instance['logo_image']) . '" alt="Logo">' : '';
?>

<div class="footer_social">
    <?php foreach ($networks as $name => $label) : ?>
        <?php if (isset($instance[$name]) && !empty($instance[$name])) : ?>
            <a href="<?= esc_url($instance[$name]) ?>" target="_blank" title="<?= sprintf(esc_attr('Me suivre sur %s', 'mesrflexesetmoi'), $label); ?>">
                <?= Mrm_icon($name) ?>
            </a>
        <?php endif ?>
    <?php endforeach; ?>
</div>
<div class="footer_credits">
    <?php if (isset($instance['credits']) && !empty($instance['credits'])) : ?>
        <?= esc_html($instance['credits']) ?>
    <?php endif; ?>
    
    <?php if (!empty($site_link) && !empty($logo_image)) : ?>
        <a href="<?= $site_link ?>" target="_blank" title="<?= esc_attr('Visitez le site', 'mesrflexesetmoi'); ?>">
            <?= $logo_image ?>
        </a>
    <?php endif; ?>
</div>
