<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();

?>

<div class="twigapaie-payment-result twigapaie-payment-success">
    <div class="twigapaie-payment-icon">✅</div>
    <h1><?php _e('Paiement réussi !', 'twiga-commerce-donation'); ?></h1>
    <p><?php _e('Merci pour votre paiement. Votre transaction a été confirmée avec succès.', 'twiga-commerce-donation'); ?></p>
    
    <?php if (isset($_GET['order_id'])): ?>
        <p class="twigapaie-order-ref">
            <?php _e('Référence de commande:', 'twiga-commerce-donation'); ?> 
            <strong>#<?php echo esc_html($_GET['order_id']); ?></strong>
        </p>
    <?php endif; ?>
    
    <?php if (isset($_GET['donation_id'])): ?>
        <p class="twigapaie-order-ref">
            <?php _e('Référence de donation:', 'twiga-commerce-donation'); ?> 
            <strong>#<?php echo esc_html($_GET['donation_id']); ?></strong>
        </p>
    <?php endif; ?>
    
    <p><?php _e('Vous recevrez un email de confirmation dans quelques instants.', 'twiga-commerce-donation'); ?></p>
    
    <div class="twigapaie-payment-actions">
        <a href="<?php echo home_url(); ?>" class="twigapaie-btn twigapaie-btn-primary">
            <?php _e('Retour à l\'accueil', 'twiga-commerce-donation'); ?>
        </a>
    </div>
</div>

<?php

get_footer();
