<?php
/**
 * Template pour l'affichage d'un seul produit
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

global $wpdb, $post;

$product_id = get_the_ID();
$table_products = $wpdb->prefix . 'twigapaie_products';
$product_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_products WHERE post_id = %d", $product_id));

if (!$product_data || !$product_data->is_active) {
    echo '<p>' . __('Ce produit n\'est pas disponible.', 'twiga-commerce-donation') . '</p>';
    get_footer();
    return;
}

$currency = get_option('twigapaie_currency', 'CDF');
$price = ($currency === 'CDF') ? $product_data->price_cdf : $product_data->price_usd;

?>

<div class="twigapaie-single-product-page">
    <div class="twigapaie-product-container">
        
        <?php if (has_post_thumbnail()): ?>
            <div class="twigapaie-product-main-image">
                <?php the_post_thumbnail('large'); ?>
            </div>
        <?php endif; ?>
        
        <div class="twigapaie-product-details">
            <h1 class="twigapaie-product-title"><?php the_title(); ?></h1>
            
            <div class="twigapaie-product-price-large">
                <strong><?php echo number_format($price, 2); ?> <?php echo esc_html($currency); ?></strong>
            </div>
            
            <?php
            $categories = get_the_terms($post->ID, 'product_category');
            if ($categories && !is_wp_error($categories)):
            ?>
                <div class="twigapaie-product-categories">
                    <strong><?php _e('Catégories:', 'twiga-commerce-donation'); ?></strong>
                    <?php
                    $cat_names = array();
                    foreach ($categories as $category) {
                        $cat_names[] = $category->name;
                    }
                    echo implode(', ', $cat_names);
                    ?>
                </div>
            <?php endif; ?>
            
            <?php
            $tags = get_the_terms($post->ID, 'product_tag');
            if ($tags && !is_wp_error($tags)):
            ?>
                <div class="twigapaie-product-tags">
                    <strong><?php _e('Mots-clés:', 'twiga-commerce-donation'); ?></strong>
                    <?php
                    $tag_names = array();
                    foreach ($tags as $tag) {
                        $tag_names[] = $tag->name;
                    }
                    echo implode(', ', $tag_names);
                    ?>
                </div>
            <?php endif; ?>
            
            <div class="twigapaie-product-content">
                <?php the_content(); ?>
            </div>
            
            <div class="twigapaie-product-actions">
                <button type="button" 
                        class="twigapaie-btn twigapaie-btn-primary twigapaie-btn-large twigapaie-add-to-cart" 
                        data-product-id="<?php echo esc_attr($product_id); ?>">
                    <?php _e('Ajouter au panier', 'twiga-commerce-donation'); ?>
                </button>
            </div>
        </div>
        
    </div>
</div>

<style>
.twigapaie-single-product-page {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
}

.twigapaie-product-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    background: #fff;
    padding: 40px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

@media (max-width: 768px) {
    .twigapaie-product-container {
        grid-template-columns: 1fr;
    }
}

.twigapaie-product-main-image img {
    width: 100%;
    height: auto;
    border-radius: 8px;
}

.twigapaie-product-title {
    font-size: 32px;
    margin: 0 0 20px 0;
    color: #1e1e1e;
}

.twigapaie-product-price-large {
    font-size: 28px;
    color: #2271b1;
    margin-bottom: 20px;
}

.twigapaie-product-categories,
.twigapaie-product-tags {
    margin-bottom: 15px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 4px;
}

.twigapaie-product-content {
    margin: 30px 0;
    line-height: 1.8;
}

.twigapaie-product-actions {
    margin-top: 30px;
}

.twigapaie-product-actions button {
    width: 100%;
}
</style>

<?php

get_footer();
