<?php
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb, $product;

$product_id = $product->ID;
$table_products = $wpdb->prefix . 'twigapaie_products';
$product_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_products WHERE post_id = %d", $product_id));

if (!$product_data || !$product_data->is_active) {
    return;
}

$currency = get_option('twigapaie_currency', 'CDF');
$price = ($currency === 'CDF') ? $product_data->price_cdf : $product_data->price_usd;

?>

<div class="twigapaie-product-card" data-product-id="<?php echo esc_attr($product_id); ?>">
    <?php if (has_post_thumbnail($product_id)): ?>
        <div class="twigapaie-product-image">
            <?php echo get_the_post_thumbnail($product_id, 'medium'); ?>
        </div>
    <?php endif; ?>
    
    <div class="twigapaie-product-content">
        <h3 class="twigapaie-product-title"><?php echo esc_html($product->post_title); ?></h3>
        
        <div class="twigapaie-product-excerpt">
            <?php echo wp_trim_words($product->post_excerpt ?: $product->post_content, 20); ?>
        </div>
        
        <div class="twigapaie-product-footer">
            <div class="twigapaie-product-price">
                <strong><?php echo number_format($price, 2); ?> <?php echo esc_html($currency); ?></strong>
            </div>
            
            <button type="button" class="twigapaie-btn twigapaie-btn-secondary twigapaie-add-to-cart" data-product-id="<?php echo esc_attr($product_id); ?>">
                <?php _e('Ajouter au panier', 'twiga-commerce-donation'); ?>
            </button>
        </div>
    </div>
</div>
