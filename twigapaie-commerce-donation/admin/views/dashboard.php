<?php
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

// Récupérer les statistiques
$donations_table = $wpdb->prefix . 'twigapaie_donations';
$orders_table = $wpdb->prefix . 'twigapaie_orders';

$total_donations = $wpdb->get_var("SELECT COUNT(*) FROM $donations_table WHERE payment_status = 'completed'");
$total_donations_amount = $wpdb->get_var("SELECT SUM(amount) FROM $donations_table WHERE payment_status = 'completed'");

$total_orders = $wpdb->get_var("SELECT COUNT(*) FROM $orders_table WHERE payment_status = 'completed'");
$total_orders_amount = $wpdb->get_var("SELECT SUM(total_amount) FROM $orders_table WHERE payment_status = 'completed'");

$recent_donations = $wpdb->get_results("SELECT * FROM $donations_table ORDER BY created_at DESC LIMIT 10");
$recent_orders = $wpdb->get_results("SELECT * FROM $orders_table ORDER BY created_at DESC LIMIT 10");

?>

<div class="wrap twigapaie-dashboard">
    <h1><?php _e('Tableau de bord TwigaPaie', 'twiga-commerce-donation'); ?></h1>
    
    <div class="twigapaie-stats-grid">
        <div class="twigapaie-stat-card">
            <div class="twigapaie-stat-icon">💝</div>
            <div class="twigapaie-stat-content">
                <h3><?php echo number_format($total_donations); ?></h3>
                <p><?php _e('Donations complétées', 'twiga-commerce-donation'); ?></p>
            </div>
        </div>
        
        <div class="twigapaie-stat-card">
            <div class="twigapaie-stat-icon">💰</div>
            <div class="twigapaie-stat-content">
                <h3><?php echo number_format($total_donations_amount, 2); ?> CDF</h3>
                <p><?php _e('Total donations', 'twiga-commerce-donation'); ?></p>
            </div>
        </div>
        
        <div class="twigapaie-stat-card">
            <div class="twigapaie-stat-icon">🛍️</div>
            <div class="twigapaie-stat-content">
                <h3><?php echo number_format($total_orders); ?></h3>
                <p><?php _e('Commandes complétées', 'twiga-commerce-donation'); ?></p>
            </div>
        </div>
        
        <div class="twigapaie-stat-card">
            <div class="twigapaie-stat-icon">💵</div>
            <div class="twigapaie-stat-content">
                <h3><?php echo number_format($total_orders_amount, 2); ?> CDF</h3>
                <p><?php _e('Total ventes', 'twiga-commerce-donation'); ?></p>
            </div>
        </div>
    </div>
    
    <div class="twigapaie-dashboard-row">
        <div class="twigapaie-dashboard-col">
            <div class="twigapaie-box">
                <h2><?php _e('Dernières donations', 'twiga-commerce-donation'); ?></h2>
                <?php if (empty($recent_donations)): ?>
                    <p><?php _e('Aucune donation pour le moment.', 'twiga-commerce-donation'); ?></p>
                <?php else: ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php _e('Donateur', 'twiga-commerce-donation'); ?></th>
                                <th><?php _e('Montant', 'twiga-commerce-donation'); ?></th>
                                <th><?php _e('Statut', 'twiga-commerce-donation'); ?></th>
                                <th><?php _e('Date', 'twiga-commerce-donation'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_donations as $donation): ?>
                                <tr>
                                    <td><?php echo esc_html($donation->donor_name); ?></td>
                                    <td><?php echo number_format($donation->amount, 2); ?> <?php echo esc_html($donation->currency); ?></td>
                                    <td>
                                        <span class="twigapaie-status twigapaie-status-<?php echo esc_attr($donation->payment_status); ?>">
                                            <?php echo esc_html(ucfirst($donation->payment_status)); ?>
                                        </span>
                                    </td>
                                    <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($donation->created_at))); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="twigapaie-dashboard-col">
            <div class="twigapaie-box">
                <h2><?php _e('Dernières commandes', 'twiga-commerce-donation'); ?></h2>
                <?php if (empty($recent_orders)): ?>
                    <p><?php _e('Aucune commande pour le moment.', 'twiga-commerce-donation'); ?></p>
                <?php else: ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php _e('Client', 'twiga-commerce-donation'); ?></th>
                                <th><?php _e('Montant', 'twiga-commerce-donation'); ?></th>
                                <th><?php _e('Statut', 'twiga-commerce-donation'); ?></th>
                                <th><?php _e('Date', 'twiga-commerce-donation'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td><?php echo esc_html($order->customer_name); ?></td>
                                    <td><?php echo number_format($order->total_amount, 2); ?> <?php echo esc_html($order->currency); ?></td>
                                    <td>
                                        <span class="twigapaie-status twigapaie-status-<?php echo esc_attr($order->payment_status); ?>">
                                            <?php echo esc_html(ucfirst($order->payment_status)); ?>
                                        </span>
                                    </td>
                                    <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($order->created_at))); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
