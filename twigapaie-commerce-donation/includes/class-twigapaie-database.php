<?php
/**
 * Classe pour gérer la base de données du plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class TwigaPaie_Database {
    
    /**
     * Créer les tables de la base de données
     */
    public static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // Table des donations
        $table_donations = $wpdb->prefix . 'twigapaie_donations';
        $sql_donations = "CREATE TABLE $table_donations (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            campaign_id bigint(20) DEFAULT NULL,
            donor_name varchar(255) NOT NULL,
            donor_email varchar(255) NOT NULL,
            donor_phone varchar(50) DEFAULT NULL,
            amount decimal(10,2) NOT NULL,
            currency varchar(10) NOT NULL DEFAULT 'CDF',
            payment_method varchar(50) NOT NULL,
            payment_status varchar(50) NOT NULL DEFAULT 'pending',
            twigapaie_order_id varchar(255) DEFAULT NULL,
            supabase_transaction_id varchar(255) DEFAULT NULL,
            provider_id varchar(10) DEFAULT NULL,
            provider_name varchar(100) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            completed_at datetime DEFAULT NULL,
            PRIMARY KEY (id),
            KEY campaign_id (campaign_id),
            KEY payment_status (payment_status),
            KEY donor_email (donor_email)
        ) $charset_collate;";
        dbDelta($sql_donations);
        
        // Table des produits (métadonnées)
        $table_products = $wpdb->prefix . 'twigapaie_products';
        $sql_products = "CREATE TABLE $table_products (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            price_cdf decimal(10,2) DEFAULT NULL,
            price_usd decimal(10,2) DEFAULT NULL,
            file_url text DEFAULT NULL,
            download_limit int(11) DEFAULT -1,
            is_active tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY post_id (post_id)
        ) $charset_collate;";
        dbDelta($sql_products);
        
        // Table des commandes
        $table_orders = $wpdb->prefix . 'twigapaie_orders';
        $sql_orders = "CREATE TABLE $table_orders (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            order_number varchar(50) NOT NULL UNIQUE,
            customer_name varchar(255) NOT NULL,
            customer_email varchar(255) NOT NULL,
            customer_phone varchar(50) DEFAULT NULL,
            total_amount decimal(10,2) NOT NULL,
            currency varchar(10) NOT NULL DEFAULT 'CDF',
            payment_method varchar(50) NOT NULL,
            payment_status varchar(50) NOT NULL DEFAULT 'pending',
            twigapaie_order_id varchar(255) DEFAULT NULL,
            supabase_transaction_id varchar(255) DEFAULT NULL,
            provider_id varchar(10) DEFAULT NULL,
            provider_name varchar(100) DEFAULT NULL,
            items text NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            completed_at datetime DEFAULT NULL,
            PRIMARY KEY (id),
            KEY order_number (order_number),
            KEY payment_status (payment_status),
            KEY customer_email (customer_email)
        ) $charset_collate;";
        dbDelta($sql_orders);
        
        // Table des transactions locales (cache)
        $table_transactions = $wpdb->prefix . 'twigapaie_transactions';
        $sql_transactions = "CREATE TABLE $table_transactions (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            order_id bigint(20) NOT NULL,
            order_type varchar(50) NOT NULL,
            transaction_type varchar(50) NOT NULL,
            amount decimal(10,2) NOT NULL,
            currency varchar(10) NOT NULL,
            aggregator_fee decimal(10,2) NOT NULL,
            defmaks_fee decimal(10,2) NOT NULL,
            net_amount decimal(10,2) NOT NULL,
            twigapaie_order_id varchar(255) DEFAULT NULL,
            supabase_transaction_id varchar(255) DEFAULT NULL,
            status varchar(50) NOT NULL DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY order_id (order_id),
            KEY order_type (order_type),
            KEY status (status)
        ) $charset_collate;";
        dbDelta($sql_transactions);
        
        // Table des téléchargements sécurisés
        $table_downloads = $wpdb->prefix . 'twigapaie_downloads';
        $sql_downloads = "CREATE TABLE $table_downloads (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            token varchar(255) NOT NULL UNIQUE,
            order_id bigint(20) NOT NULL,
            product_id bigint(20) NOT NULL,
            customer_email varchar(255) NOT NULL,
            download_limit int(11) DEFAULT -1,
            download_count int(11) DEFAULT 0,
            expires_at datetime NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY token (token),
            KEY order_id (order_id),
            KEY product_id (product_id),
            KEY customer_email (customer_email)
        ) $charset_collate;";
        dbDelta($sql_downloads);
    }
}
