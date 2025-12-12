<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();

?>

<div class="twigapaie-payment-result twigapaie-payment-decline">
    <div class="twigapaie-payment-icon">❌</div>
    <h1><?php _e('Paiement refusé', 'twiga-commerce-donation'); ?></h1>
    <p><?php _e('Votre paiement a été refusé par votre fournisseur de paiement.', 'twiga-commerce-donation'); ?></p>
    
    <div class="twigapaie-decline-reasons">
        <h3><?php _e('Raisons possibles:', 'twiga-commerce-donation'); ?></h3>
        <ul>
            <li><?php _e('Solde insuffisant', 'twiga-commerce-donation'); ?></li>
            <li><?php _e('Carte expirée ou invalide', 'twiga-commerce-donation'); ?></li>
            <li><?php _e('Limite de transaction dépassée', 'twiga-commerce-donation'); ?></li>
            <li><?php _e('Problème avec votre fournisseur de paiement', 'twiga-commerce-donation'); ?></li>
        </ul>
    </div>
    
    <p><?php _e('Veuillez vérifier vos informations de paiement et réessayer.', 'twiga-commerce-donation'); ?></p>
    
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
