<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!session_id()) {
    session_start();
}

$cart = isset($_SESSION['twigapaie_cart']) ? $_SESSION['twigapaie_cart'] : array();
$currency = get_option('twigapaie_currency', 'CDF');

if (empty($cart)) {
    echo '<p>' . __('Votre panier est vide.', 'twiga-commerce-donation') . '</p>';
    return;
}

?>

<div class="twigapaie-checkout">
    <h2><?php _e('Finaliser la commande', 'twiga-commerce-donation'); ?></h2>
    
    <div class="twigapaie-checkout-grid">
        <div class="twigapaie-checkout-form">
            <form id="twigapaie-checkout-form">
                <h3><?php _e('Informations de facturation', 'twiga-commerce-donation'); ?></h3>
                
                <div class="twigapaie-form-group">
                    <label for="customer-name"><?php _e('Nom complet', 'twiga-commerce-donation'); ?> <span class="required">*</span></label>
                    <input type="text" name="customer_name" id="customer-name" required />
                </div>
                
                <div class="twigapaie-form-group">
                    <label for="customer-email"><?php _e('Email', 'twiga-commerce-donation'); ?> <span class="required">*</span></label>
                    <input type="email" name="customer_email" id="customer-email" required />
                </div>
                
                <div class="twigapaie-form-group">
                    <label for="customer-phone"><?php _e('Numéro de téléphone', 'twiga-commerce-donation'); ?> <span class="required">*</span></label>
                    <input type="tel" name="customer_phone" id="customer-phone" placeholder="Ex: 0822032855" required />
                    <small class="twigapaie-help-text"><?php _e('Format: 0XXXXXXXXX (RDC)', 'twiga-commerce-donation'); ?></small>
                </div>
                
                <div class="twigapaie-form-group">
                    <label><?php _e('Méthode de paiement', 'twiga-commerce-donation'); ?></label>
                    <div class="twigapaie-payment-methods">
                        <label class="twigapaie-payment-method">
                            <input type="radio" name="payment_method" value="emoney" checked />
                            <span class="twigapaie-payment-label">
                                <strong><?php _e('Mobile Money', 'twiga-commerce-donation'); ?></strong>
                                <small><?php _e('Orange, Vodacom, Airtel, Africell', 'twiga-commerce-donation'); ?></small>
                            </span>
                        </label>
                        <label class="twigapaie-payment-method">
                            <input type="radio" name="payment_method" value="ecard" />
                            <span class="twigapaie-payment-label">
                                <strong><?php _e('Carte bancaire', 'twiga-commerce-donation'); ?></strong>
                                <small><?php _e('Visa, Mastercard', 'twiga-commerce-donation'); ?></small>
                            </span>
                        </label>
                    </div>
                </div>
                
                <input type="hidden" name="currency" value="<?php echo esc_attr($currency); ?>" />
                
                <div class="twigapaie-form-actions">
                    <button type="submit" class="twigapaie-btn twigapaie-btn-primary twigapaie-btn-large">
                        <?php _e('Confirmer le paiement', 'twiga-commerce-donation'); ?>
                    </button>
                </div>
                
                <div class="twigapaie-form-message" style="display: none;"></div>
            </form>
        </div>
        
        <div class="twigapaie-checkout-summary">
            <h3><?php _e('Résumé de la commande', 'twiga-commerce-donation'); ?></h3>
            
            <div class="twigapaie-checkout-items">
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
                    <div class="twigapaie-checkout-item">
                        <span class="twigapaie-checkout-item-name"><?php echo esc_html($product->post_title); ?></span>
                        <span class="twigapaie-checkout-item-price"><?php echo number_format($price, 2); ?> <?php echo esc_html($currency); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="twigapaie-checkout-total">
                <strong><?php _e('Total:', 'twiga-commerce-donation'); ?></strong>
                <strong class="twigapaie-total-amount"><?php echo number_format($total, 2); ?> <?php echo esc_html($currency); ?></strong>
            </div>
            
            <div class="twigapaie-checkout-security">
                <p>✅ <?php _e('Paiement sécurisé par TwigaPaie', 'twiga-commerce-donation'); ?></p>
            </div>
        </div>
    </div>
</div>
