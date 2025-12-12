<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!session_id()) {
    session_start();
}

$cart = isset($_SESSION['twigapaie_cart']) ? $_SESSION['twigapaie_cart'] : array();
$currency = get_option('twigapaie_currency', 'CDF');

?>

<div class="twigapaie-cart">
    <h2><?php _e('Panier d\'achat', 'twiga-commerce-donation'); ?></h2>
    
    <?php if (empty($cart)): ?>
        <div class="twigapaie-empty-cart">
            <p><?php _e('Votre panier est vide.', 'twiga-commerce-donation'); ?></p>
            <a href="<?php echo get_post_type_archive_link('twigapaie_product'); ?>" class="twigapaie-btn twigapaie-btn-primary">
                <?php _e('Voir les produits', 'twiga-commerce-donation'); ?>
            </a>
        </div>
    <?php else: ?>
        <div class="twigapaie-cart-items">
            <?php 
            global $wpdb;
            $table_products = $wpdb->prefix . 'twigapaie_products';
            $total = 0;
            
            foreach ($cart as $product_id):
                $product = get_post($product_id);
                $product_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_products WHERE post_id = %d", $product_id));
                
                if (!$product || !$product_data) continue;
                
                $price = ($currency === 'CDF') ? $product_data->price_cdf : $product_data->price_usd;
                $total += $price;
            ?>
                <div class="twigapaie-cart-item" data-product-id="<?php echo esc_attr($product_id); ?>">
                    <?php if (has_post_thumbnail($product_id)): ?>
                        <div class="twigapaie-cart-item-image">
                            <?php echo get_the_post_thumbnail($product_id, 'thumbnail'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="twigapaie-cart-item-details">
                        <h4><?php echo esc_html($product->post_title); ?></h4>
                        <p class="twigapaie-cart-item-price"><?php echo number_format($price, 2); ?> <?php echo esc_html($currency); ?></p>
                    </div>
                    
                    <button type="button" class="twigapaie-remove-from-cart" data-product-id="<?php echo esc_attr($product_id); ?>">
                        &times;
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="twigapaie-cart-summary">
            <div class="twigapaie-cart-total">
                <strong><?php _e('Total:', 'twiga-commerce-donation'); ?></strong>
                <strong class="twigapaie-total-amount"><?php echo number_format($total, 2); ?> <?php echo esc_html($currency); ?></strong>
            </div>
            
            <div class="twigapaie-cart-actions">
                <a href="<?php echo get_post_type_archive_link('twigapaie_product'); ?>" class="twigapaie-btn twigapaie-btn-secondary">
                    <?php _e('Continuer les achats', 'twiga-commerce-donation'); ?>
                </a>
                <a href="<?php echo esc_url(add_query_arg('page', 'checkout')); ?>" class="twigapaie-btn twigapaie-btn-primary">
                    <?php _e('Passer à la caisse', 'twiga-commerce-donation'); ?>
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>
