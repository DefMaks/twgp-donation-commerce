<?php
/**
 * Plugin Name: TwigaPaie - Commerce & Donation
 * Plugin URI: https://defmaks.com
 * Description: Acceptez des dons et vendez des contenus directement depuis WordPress avec TwigaPaie — la passerelle de paiement africaine qui supporte Orange Money, Airtel Money, M-Pesa et les cartes bancaires via FlexPay. Toutes les transactions sont automatiquement enregistrées dans Supabase pour une répartition transparente des revenus entre créateurs et la plateforme. Conçu pour les éditeurs, médias et plateformes de contenu en Afrique francophone.
 * Version: 1.0.0
 * Author: DefMaks
 * Author URI: https://defmaks.com
 * License: GPL-3.0+
 * Text Domain: twiga-commerce-donation
 * Domain Path: /languages
 */

// Si ce fichier est appelé directement, on abandonne
if (!defined('ABSPATH')) {
    exit;
}

// Définir les constantes du plugin
define('TWIGAPAIE_VERSION', '1.0.0');
define('TWIGAPAIE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TWIGAPAIE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TWIGAPAIE_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Autoloader pour les classes
spl_autoload_register(function ($class) {
    $prefix = 'TwigaPaie_';
    $base_dir = TWIGAPAIE_PLUGIN_DIR . 'includes/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . 'class-twigapaie-' . strtolower(str_replace('_', '-', $relative_class)) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Fonction d'activation du plugin
function twigapaie_activate() {
    require_once TWIGAPAIE_PLUGIN_DIR . 'includes/class-twigapaie-database.php';
    TwigaPaie_Database::create_tables();
    
    // Créer les options par défaut
    add_option('twigapaie_api_key', '');
    add_option('twigapaie_supabase_url', '');
    add_option('twigapaie_supabase_key', '');
    add_option('twigapaie_currency', 'CDF');
    add_option('twigapaie_aggregator_fee', 2.5);
    add_option('twigapaie_defmaks_fee', 3.5);
    add_option('twigapaie_test_mode', true);
    
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'twigapaie_activate');

// Fonction de désactivation du plugin
function twigapaie_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'twigapaie_deactivate');

// Initialiser le plugin
function twigapaie_init() {
    // Charger les traductions
    load_plugin_textdomain('twiga-commerce-donation', false, dirname(TWIGAPAIE_PLUGIN_BASENAME) . '/languages');
    
    // Initialiser les classes principales
    new TwigaPaie_Core();
    new TwigaPaie_Donations();
    new TwigaPaie_Commerce();
    new TwigaPaie_Admin();
    new TwigaPaie_Payment_Handler();
    new TwigaPaie_Download();
}
add_action('plugins_loaded', 'twigapaie_init');

// Ajouter le lien des paramètres dans la liste des plugins
function twigapaie_add_settings_link($links) {
    $settings_link = '<a href="admin.php?page=twigapaie-settings">' . __('Paramètres', 'twiga-commerce-donation') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . TWIGAPAIE_PLUGIN_BASENAME, 'twigapaie_add_settings_link');
