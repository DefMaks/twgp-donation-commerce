<?php
/**
 * Template pour l'archive des campagnes
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

?>

<div class="twigapaie-archive-campaigns-page">
    <div class="twigapaie-archive-container">
        
        <header class="twigapaie-archive-header">
            <h1><?php _e('Nos Campagnes', 'twiga-commerce-donation'); ?></h1>
            <?php if (term_description()): ?>
                <div class="twigapaie-archive-description">
                    <?php echo term_description(); ?>
                </div>
            <?php endif; ?>
        </header>
        
        <?php if (have_posts()): ?>
            <div class="twigapaie-campaigns-grid">
                <?php
                while (have_posts()): the_post();
                    $campaign_id = get_the_ID();
                    $goal = get_post_meta($campaign_id, '_campaign_goal', true);
                    $currency = get_post_meta($campaign_id, '_campaign_currency', true) ?: 'CDF';
                ?>
                    <div class="twigapaie-campaign-card">
                        <?php if (has_post_thumbnail()): ?>
                            <div class="twigapaie-campaign-image">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="twigapaie-campaign-content">
                            <h3 class="twigapaie-campaign-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            
                            <?php if ($goal): ?>
                                <div class="twigapaie-campaign-goal">
                                    <strong><?php _e('Objectif:', 'twiga-commerce-donation'); ?></strong>
                                    <?php echo number_format($goal, 2); ?> <?php echo esc_html($currency); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="twigapaie-campaign-excerpt">
                                <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                            </div>
                            
                            <div class="twigapaie-campaign-footer">
                                <a href="<?php the_permalink(); ?>" class="twigapaie-btn twigapaie-btn-primary">
                                    <?php _e('Faire un don', 'twiga-commerce-donation'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php
                endwhile;
                ?>
            </div>
            
            <div class="twigapaie-pagination">
                <?php
                the_posts_pagination(array(
                    'mid_size' => 2,
                    'prev_text' => __('&laquo; Précédent', 'twiga-commerce-donation'),
                    'next_text' => __('Suivant &raquo;', 'twiga-commerce-donation'),
                ));
                ?>
            </div>
        <?php else: ?>
            <div class="twigapaie-no-campaigns">
                <p><?php _e('Aucune campagne disponible pour le moment.', 'twiga-commerce-donation'); ?></p>
            </div>
        <?php endif; ?>
        
    </div>
</div>

<style>
.twigapaie-archive-campaigns-page {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
}

.twigapaie-campaigns-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
}

.twigapaie-campaign-card {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}

.twigapaie-campaign-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.twigapaie-campaign-image {
    width: 100%;
    height: 200px;
    overflow: hidden;
}

.twigapaie-campaign-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.twigapaie-campaign-content {
    padding: 20px;
}

.twigapaie-campaign-title a {
    color: #1e1e1e;
    text-decoration: none;
}

.twigapaie-campaign-title a:hover {
    color: #2271b1;
}

.twigapaie-campaign-goal {
    margin: 10px 0;
    padding: 10px;
    background: #e8f5e9;
    border-radius: 4px;
    font-size: 14px;
}

.twigapaie-campaign-excerpt {
    margin: 15px 0;
    color: #646970;
    line-height: 1.6;
}

.twigapaie-campaign-footer {
    margin-top: 20px;
}

.twigapaie-campaign-footer .twigapaie-btn {
    width: 100%;
    text-align: center;
}
</style>

<?php

get_footer();
