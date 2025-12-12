<?php
if (!defined('ABSPATH')) {
    exit;
}

$campaign_id = isset($atts['campaign_id']) ? intval($atts['campaign_id']) : 0;
$campaign = null;
$goal = 0;
$currency = get_option('twigapaie_currency', 'CDF');
$preset_amounts = array(1000, 5000, 10000, 25000);

if ($campaign_id > 0) {
    $campaign = get_post($campaign_id);
    $goal = get_post_meta($campaign_id, '_campaign_goal', true);
    $currency = get_post_meta($campaign_id, '_campaign_currency', true) ?: 'CDF';
    $preset_str = get_post_meta($campaign_id, '_campaign_preset_amounts', true);
    if ($preset_str) {
        $preset_amounts = array_map('intval', explode(',', $preset_str));
    }
}

?>

<div class="twigapaie-donation-form" data-campaign-id="<?php echo esc_attr($campaign_id); ?>">
    <?php if ($campaign): ?>
        <div class="twigapaie-campaign-header">
            <?php if (has_post_thumbnail($campaign_id)): ?>
                <div class="twigapaie-campaign-image">
                    <?php echo get_the_post_thumbnail($campaign_id, 'large'); ?>
                </div>
            <?php endif; ?>
            <h2><?php echo esc_html($campaign->post_title); ?></h2>
            <div class="twigapaie-campaign-description">
                <?php echo wpautop($campaign->post_content); ?>
            </div>
            <?php if ($goal > 0): ?>
                <div class="twigapaie-campaign-goal">
                    <p><strong><?php _e('Objectif:', 'twiga-commerce-donation'); ?></strong> <?php echo number_format($goal, 2); ?> <?php echo esc_html($currency); ?></p>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <h2><?php _e('Faire un don', 'twiga-commerce-donation'); ?></h2>
    <?php endif; ?>
    
    <form class="twigapaie-donation-form-inner" id="twigapaie-donation-form">
        <div class="twigapaie-form-group">
            <label><?php _e('Montant du don', 'twiga-commerce-donation'); ?></label>
            <div class="twigapaie-amount-presets">
                <?php foreach ($preset_amounts as $amount): ?>
                    <button type="button" class="twigapaie-amount-btn" data-amount="<?php echo esc_attr($amount); ?>">
                        <?php echo number_format($amount); ?> <?php echo esc_html($currency); ?>
                    </button>
                <?php endforeach; ?>
            </div>
            <div class="twigapaie-custom-amount">
                <input type="number" name="amount" id="donation-amount" placeholder="<?php _e('Montant personnalisé', 'twiga-commerce-donation'); ?>" min="1" step="0.01" required />
                <span class="twigapaie-currency-label"><?php echo esc_html($currency); ?></span>
            </div>
        </div>
        
        <div class="twigapaie-form-group">
            <label for="donor-name"><?php _e('Nom complet', 'twiga-commerce-donation'); ?> <span class="required">*</span></label>
            <input type="text" name="donor_name" id="donor-name" required />
        </div>
        
        <div class="twigapaie-form-group">
            <label for="donor-email"><?php _e('Email', 'twiga-commerce-donation'); ?> <span class="required">*</span></label>
            <input type="email" name="donor_email" id="donor-email" required />
        </div>
        
        <div class="twigapaie-form-group">
            <label for="donor-phone"><?php _e('Numéro de téléphone', 'twiga-commerce-donation'); ?> <span class="required">*</span></label>
            <input type="tel" name="donor_phone" id="donor-phone" placeholder="Ex: 0822032855" required />
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
        <input type="hidden" name="campaign_id" value="<?php echo esc_attr($campaign_id); ?>" />
        
        <div class="twigapaie-form-actions">
            <button type="submit" class="twigapaie-btn twigapaie-btn-primary">
                <?php _e('Faire un don', 'twiga-commerce-donation'); ?>
            </button>
        </div>
        
        <div class="twigapaie-form-message" style="display: none;"></div>
    </form>
</div>
