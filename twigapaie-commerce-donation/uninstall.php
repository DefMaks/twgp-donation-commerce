<?php
/**
 * Fichier de désinstallation du plugin
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

// Supprimer les tables personnalisées
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}twigapaie_donations");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}twigapaie_products");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}twigapaie_orders");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}twigapaie_transactions");

// Supprimer les options
delete_option('twigapaie_api_key');
delete_option('twigapaie_supabase_url');
delete_option('twigapaie_supabase_key');
delete_option('twigapaie_currency');
delete_option('twigapaie_aggregator_fee');
delete_option('twigapaie_defmaks_fee');
delete_option('twigapaie_test_mode');

// Supprimer les posts personnalisés
$campaigns = get_posts(array('post_type' => 'twigapaie_campaign', 'numberposts' => -1));
foreach ($campaigns as $campaign) {
    wp_delete_post($campaign->ID, true);
}

$products = get_posts(array('post_type' => 'twigapaie_product', 'numberposts' => -1));
foreach ($products as $product) {
    wp_delete_post($product->ID, true);
}
