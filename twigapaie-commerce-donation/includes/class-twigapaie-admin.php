<?php
/**
 * Classe pour gérer l'interface d'administration
 */

if (!defined('ABSPATH')) {
    exit;
}

class TwigaPaie_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    /**
     * Ajouter le menu d'administration
     */
    public function add_admin_menu() {
        add_menu_page(
            __('TwigaPaie', 'twiga-commerce-donation'),
            __('TwigaPaie', 'twiga-commerce-donation'),
            'manage_options',
            'twigapaie-dashboard',
            array($this, 'render_dashboard'),
            TWIGAPAIE_PLUGIN_URL . 'assets/images/logo.png',
            30
        );
        
        add_submenu_page(
            'twigapaie-dashboard',
            __('Tableau de bord', 'twiga-commerce-donation'),
            __('Tableau de bord', 'twiga-commerce-donation'),
            'manage_options',
            'twigapaie-dashboard',
            array($this, 'render_dashboard')
        );
        
        add_submenu_page(
            'twigapaie-dashboard',
            __('Transactions', 'twiga-commerce-donation'),
            __('Transactions', 'twiga-commerce-donation'),
            'manage_options',
            'twigapaie-transactions',
            array($this, 'render_transactions')
        );
        
        add_submenu_page(
            'twigapaie-dashboard',
            __('Campagnes', 'twiga-commerce-donation'),
            __('Campagnes', 'twiga-commerce-donation'),
            'manage_options',
            'edit.php?post_type=twigapaie_campaign'
        );
        
        add_submenu_page(
            'twigapaie-dashboard',
            __('Produits', 'twiga-commerce-donation'),
            __('Produits', 'twiga-commerce-donation'),
            'manage_options',
            'edit.php?post_type=twigapaie_product'
        );
        
        add_submenu_page(
            'twigapaie-dashboard',
            __('Paramètres', 'twiga-commerce-donation'),
            __('Paramètres', 'twiga-commerce-donation'),
            'manage_options',
            'twigapaie-settings',
            array($this, 'render_settings')
        );
    }
    
    /**
     * Enregistrer les paramètres
     */
    public function register_settings() {
        register_setting('twigapaie_settings', 'twigapaie_api_key');
        register_setting('twigapaie_settings', 'twigapaie_supabase_url');
        register_setting('twigapaie_settings', 'twigapaie_supabase_key');
        register_setting('twigapaie_settings', 'twigapaie_currency');
        register_setting('twigapaie_settings', 'twigapaie_aggregator_fee');
        register_setting('twigapaie_settings', 'twigapaie_defmaks_fee');
        register_setting('twigapaie_settings', 'twigapaie_test_mode');
    }
    
    /**
     * Rendre le tableau de bord
     */
    public function render_dashboard() {
        include TWIGAPAIE_PLUGIN_DIR . 'admin/views/dashboard.php';
    }
    
    /**
     * Rendre la page des transactions
     */
    public function render_transactions() {
        include TWIGAPAIE_PLUGIN_DIR . 'admin/views/transactions.php';
    }
    
    /**
     * Rendre la page des paramètres
     */
    public function render_settings() {
        include TWIGAPAIE_PLUGIN_DIR . 'admin/views/settings.php';
    }
}
