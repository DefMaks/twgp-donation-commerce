<?php
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

// Récupérer les filtres
$filter_type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : 'all';
$filter_status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';

// Récupérer les donations
$donations_table = $wpdb->prefix . 'twigapaie_donations';
$donations_query = "SELECT *, 'donation' as type FROM $donations_table WHERE 1=1";

if ($filter_status !== 'all') {
    $donations_query .= $wpdb->prepare(" AND payment_status = %s", $filter_status);
}

$donations = ($filter_type === 'all' || $filter_type === 'donation') ? $wpdb->get_results($donations_query) : array();

// Récupérer les commandes
$orders_table = $wpdb->prefix . 'twigapaie_orders';
$orders_query = "SELECT *, 'order' as type FROM $orders_table WHERE 1=1";

if ($filter_status !== 'all') {
    $orders_query .= $wpdb->prepare(" AND payment_status = %s", $filter_status);
}

$orders = ($filter_type === 'all' || $filter_type === 'order') ? $wpdb->get_results($orders_query) : array();

// Fusionner et trier
$all_transactions = array_merge($donations, $orders);
usort($all_transactions, function($a, $b) {
    return strtotime($b->created_at) - strtotime($a->created_at);
});

?>

<div class="wrap twigapaie-transactions">
    <h1><?php _e('Transactions', 'twiga-commerce-donation'); ?></h1>
    
    <div class="twigapaie-filters">
        <form method="get" action="">
            <input type="hidden" name="page" value="twigapaie-transactions" />
            
            <select name="type" id="filter-type">
                <option value="all" <?php selected($filter_type, 'all'); ?>><?php _e('Tous les types', 'twiga-commerce-donation'); ?></option>
                <option value="donation" <?php selected($filter_type, 'donation'); ?>><?php _e('Donations', 'twiga-commerce-donation'); ?></option>
                <option value="order" <?php selected($filter_type, 'order'); ?>><?php _e('Commandes', 'twiga-commerce-donation'); ?></option>
            </select>
            
            <select name="status" id="filter-status">
                <option value="all" <?php selected($filter_status, 'all'); ?>><?php _e('Tous les statuts', 'twiga-commerce-donation'); ?></option>
                <option value="pending" <?php selected($filter_status, 'pending'); ?>><?php _e('En attente', 'twiga-commerce-donation'); ?></option>
                <option value="processing" <?php selected($filter_status, 'processing'); ?>><?php _e('En traitement', 'twiga-commerce-donation'); ?></option>
                <option value="completed" <?php selected($filter_status, 'completed'); ?>><?php _e('Complété', 'twiga-commerce-donation'); ?></option>
                <option value="failed" <?php selected($filter_status, 'failed'); ?>><?php _e('Échoué', 'twiga-commerce-donation'); ?></option>
            </select>
            
            <button type="submit" class="button"><?php _e('Filtrer', 'twiga-commerce-donation'); ?></button>
        </form>
    </div>
    
    <?php if (empty($all_transactions)): ?>
        <div class="twigapaie-empty-state">
            <p><?php _e('Aucune transaction trouvée.', 'twiga-commerce-donation'); ?></p>
        </div>
    <?php else: ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('ID', 'twiga-commerce-donation'); ?></th>
                    <th><?php _e('Type', 'twiga-commerce-donation'); ?></th>
                    <th><?php _e('Client', 'twiga-commerce-donation'); ?></th>
                    <th><?php _e('Montant', 'twiga-commerce-donation'); ?></th>
                    <th><?php _e('Méthode', 'twiga-commerce-donation'); ?></th>
                    <th><?php _e('Fournisseur', 'twiga-commerce-donation'); ?></th>
                    <th><?php _e('Statut', 'twiga-commerce-donation'); ?></th>
                    <th><?php _e('Date', 'twiga-commerce-donation'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_transactions as $transaction): ?>
                    <tr>
                        <td><?php echo esc_html($transaction->id); ?></td>
                        <td>
                            <span class="twigapaie-badge twigapaie-badge-<?php echo esc_attr($transaction->type); ?>">
                                <?php echo esc_html($transaction->type === 'donation' ? __('Donation', 'twiga-commerce-donation') : __('Commande', 'twiga-commerce-donation')); ?>
                            </span>
                        </td>
                        <td>
                            <?php 
                            if ($transaction->type === 'donation') {
                                echo esc_html($transaction->donor_name);
                                echo '<br><small>' . esc_html($transaction->donor_email) . '</small>';
                            } else {
                                echo esc_html($transaction->customer_name);
                                echo '<br><small>' . esc_html($transaction->customer_email) . '</small>';
                            }
                            ?>
                        </td>
                        <td>
                            <strong>
                                <?php 
                                $amount = ($transaction->type === 'donation') ? $transaction->amount : $transaction->total_amount;
                                echo number_format($amount, 2); 
                                ?> <?php echo esc_html($transaction->currency); ?>
                            </strong>
                        </td>
                        <td><?php echo esc_html(ucfirst($transaction->payment_method)); ?></td>
                        <td>
                            <?php 
                            if (!empty($transaction->provider_name)) {
                                echo '<span class="twigapaie-provider">' . esc_html($transaction->provider_name) . '</span>';
                            } else {
                                echo '<span class="twigapaie-provider">-</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <span class="twigapaie-status twigapaie-status-<?php echo esc_attr($transaction->payment_status); ?>">
                                <?php 
                                $statuses = array(
                                    'pending' => __('En attente', 'twiga-commerce-donation'),
                                    'processing' => __('En traitement', 'twiga-commerce-donation'),
                                    'completed' => __('Complété', 'twiga-commerce-donation'),
                                    'failed' => __('Échoué', 'twiga-commerce-donation'),
                                );
                                echo esc_html(isset($statuses[$transaction->payment_status]) ? $statuses[$transaction->payment_status] : $transaction->payment_status);
                                ?>
                            </span>
                        </td>
                        <td><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($transaction->created_at))); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
