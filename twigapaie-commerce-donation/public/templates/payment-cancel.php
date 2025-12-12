<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();

?>

<div class="twigapaie-payment-result twigapaie-payment-cancel">
    <div class="twigapaie-payment-icon">⚠️</div>
    <h1><?php _e('Paiement annulé', 'twiga-commerce-donation'); ?></h1>
    <p><?php _e('Vous avez annulé le processus de paiement.', 'twiga-commerce-donation'); ?></p>
    
    <p><?php _e('Si vous avez rencontré un problème, n\'hésitez pas à nous contacter.', 'twiga-commerce-donation'); ?></p>
    
    <div class="twigapaie-payment-actions">
        <a href="<?php echo home_url(); ?>" class="twigapaie-btn twigapaie-btn-secondary">
            <?php _e('Retour à l\'accueil', 'twiga-commerce-donation'); ?>
        </a>
        <a href="javascript:history.back()" class="twigapaie-btn twigapaie-btn-primary">
            <?php _e('Réessayer', 'twiga-commerce-donation'); ?>
        </a>
    </div>
</div>

<?php

get_footer();
