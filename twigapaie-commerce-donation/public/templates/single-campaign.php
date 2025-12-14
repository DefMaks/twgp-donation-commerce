<?php
/**
 * Template pour l'affichage d'une seule campagne
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$campaign_id = get_the_ID();
$goal = get_post_meta($campaign_id, '_campaign_goal', true);
$currency = get_post_meta($campaign_id, '_campaign_currency', true) ?: 'CDF';
$end_date = get_post_meta($campaign_id, '_campaign_end_date', true);

?>

<div class="twigapaie-single-campaign-page">
    <div class="twigapaie-campaign-container">
        
        <?php if (has_post_thumbnail()): ?>
            <div class="twigapaie-campaign-main-image">
                <?php the_post_thumbnail('large'); ?>
            </div>
        <?php endif; ?>
        
        <div class="twigapaie-campaign-details">
            <h1 class="twigapaie-campaign-title"><?php the_title(); ?></h1>
            
            <?php if ($goal): ?>
                <div class="twigapaie-campaign-goal-box">
                    <strong><?php _e('Objectif:', 'twiga-commerce-donation'); ?></strong>
                    <?php echo number_format($goal, 2); ?> <?php echo esc_html($currency); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($end_date): ?>
                <div class="twigapaie-campaign-end-date">
                    <strong><?php _e('Date de fin:', 'twiga-commerce-donation'); ?></strong>
                    <?php echo date_i18n(get_option('date_format'), strtotime($end_date)); ?>
                </div>
            <?php endif; ?>
            
            <div class="twigapaie-campaign-content">
                <?php the_content(); ?>
            </div>
            
            <div class="twigapaie-campaign-form">
                <h3><?php _e('Faire un don', 'twiga-commerce-donation'); ?></h3>
                <?php echo do_shortcode('[twigapaie_donation_form campaign_id="' . $campaign_id . '"]'); ?>
            </div>
        </div>
        
    </div>
</div>

<style>
.twigapaie-single-campaign-page {
    max-width: 800px;
    margin: 40px auto;
    padding: 0 20px;
}

.twigapaie-campaign-container {
    background: #fff;
    padding: 40px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.twigapaie-campaign-main-image {
    margin-bottom: 30px;
}

.twigapaie-campaign-main-image img {
    width: 100%;
    height: auto;
    border-radius: 8px;
}

.twigapaie-campaign-title {
    font-size: 32px;
    margin: 0 0 20px 0;
    color: #1e1e1e;
}

.twigapaie-campaign-goal-box,
.twigapaie-campaign-end-date {
    padding: 15px;
    background: #e8f5e9;
    border-left: 4px solid #4caf50;
    margin-bottom: 15px;
    border-radius: 4px;
}

.twigapaie-campaign-content {
    margin: 30px 0;
    line-height: 1.8;
}

.twigapaie-campaign-form {
    margin-top: 40px;
    padding-top: 40px;
    border-top: 2px solid #e0e0e0;
}

.twigapaie-campaign-form h3 {
    margin-top: 0;
}
</style>

<?php

get_footer();
