<?php
/**
 * Template pour l'archive des produits
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

?>

<div class="twigapaie-archive-products-page">
    <div class="twigapaie-archive-container">
        
        <header class="twigapaie-archive-header">
            <h1><?php _e('Nos Produits', 'twiga-commerce-donation'); ?></h1>
            <?php if (term_description()): ?>
                <div class="twigapaie-archive-description">
                    <?php echo term_description(); ?>
                </div>
            <?php endif; ?>
        </header>
        
        <?php if (have_posts()): ?>
            <div class="twigapaie-products-grid">
                <?php
                global $wpdb;
                $table_products = $wpdb->prefix . 'twigapaie_products';
                $currency = get_option('twigapaie_currency', 'CDF');
                
                while (have_posts()): the_post();
                    $product_id = get_the_ID();
                    $product_data = $wpdb->get_row($wpdb->prepare(
                        "SELECT * FROM $table_products WHERE post_id = %d AND is_active = 1",
                        $product_id
                    ));
                    
                    if ($product_data):
                        $price = ($currency === 'CDF') ? $product_data->price_cdf : $product_data->price_usd;
                ?>
                        <div class="twigapaie-product-card">
                            <?php if (has_post_thumbnail()): ?>
                                <div class="twigapaie-product-image">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="twigapaie-product-content">
                                <h3 class="twigapaie-product-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                
                                <div class="twigapaie-product-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                                </div>
                                
                                <div class="twigapaie-product-footer">
                                    <div class="twigapaie-product-price">
                                        <strong><?php echo number_format($price, 2); ?> <?php echo esc_html($currency); ?></strong>
                                    </div>
                                    
                                    <a href="<?php the_permalink(); ?>" class="twigapaie-btn twigapaie-btn-secondary">
                                        <?php _e('Voir le produit', 'twiga-commerce-donation'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                <?php
                    endif;
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
            <div class="twigapaie-no-products">
                <p><?php _e('Aucun produit disponible pour le moment.', 'twiga-commerce-donation'); ?></p>
            </div>
        <?php endif; ?>
        
    </div>
</div>

<style>
.twigapaie-archive-products-page {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
}

.twigapaie-archive-header {
    text-align: center;
    margin-bottom: 40px;
}

.twigapaie-archive-header h1 {
    font-size: 36px;
    margin-bottom: 15px;
}

.twigapaie-archive-description {
    color: #646970;
    max-width: 600px;
    margin: 0 auto;
}

.twigapaie-pagination {
    margin-top: 40px;
    text-align: center;
}

.twigapaie-no-products {
    text-align: center;
    padding: 60px 20px;
    background: #f8f9fa;
    border-radius: 8px;
}
</style>

<?php

get_footer();
