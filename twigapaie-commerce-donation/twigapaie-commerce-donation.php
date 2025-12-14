<?php
/**
 * Plugin Name: TwigaPaie - Commerce & Donation
 * Plugin URI: https://defmaks.com
 * Description: Acceptez des dons et vendez des contenus directement depuis WordPress avec TwigaPaie — la passerelle de paiement africaine qui supporte Orange Money, Airtel Money, M-Pesa et les cartes bancaires via FlexPay. Toutes les transactions sont automatiquement enregistrées dans Supabase pour une répartition transparente des revenus entre créateurs et la plateforme. Conçu pour les éditeurs, médias et plateformes de contenu en Afrique francophone.
 * Version: 1.0.2
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
define('TWIGAPAIE_VERSION', '1.0.2');
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
    
    // Enregistrer les post types et taxonomies pour le flush
    twigapaie_register_post_types_and_taxonomies();
    
    // Flush rewrite rules
    flush_rewrite_rules();
    
    // Définir un flag pour afficher un message
    set_transient('twigapaie_activation_notice', true, 5);
}
register_activation_hook(__FILE__, 'twigapaie_activate');

// Fonction de désactivation du plugin
function twigapaie_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'twigapaie_deactivate');

// Fonction pour enregistrer les post types et taxonomies (réutilisable)
function twigapaie_register_post_types_and_taxonomies() {
    // Type de post pour les campagnes de donation
    register_post_type('twigapaie_campaign', array(
        'labels' => array(
            'name' => __('Campagnes', 'twiga-commerce-donation'),
            'singular_name' => __('Campagne', 'twiga-commerce-donation'),
            'add_new' => __('Ajouter une campagne', 'twiga-commerce-donation'),
            'add_new_item' => __('Ajouter une nouvelle campagne', 'twiga-commerce-donation'),
            'edit_item' => __('Modifier la campagne', 'twiga-commerce-donation'),
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_menu' => false,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'rewrite' => array('slug' => 'campagnes'),
        'taxonomies' => array('campaign_category', 'campaign_tag'),
    ));
    
    // Type de post pour les produits numériques
    register_post_type('twigapaie_product', array(
        'labels' => array(
            'name' => __('Produits', 'twiga-commerce-donation'),
            'singular_name' => __('Produit', 'twiga-commerce-donation'),
            'add_new' => __('Ajouter un produit', 'twiga-commerce-donation'),
            'add_new_item' => __('Ajouter un nouveau produit', 'twiga-commerce-donation'),
            'edit_item' => __('Modifier le produit', 'twiga-commerce-donation'),
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_menu' => false,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'rewrite' => array('slug' => 'produits'),
        'taxonomies' => array('product_category', 'product_tag'),
    ));
    
    // Taxonomie Catégories pour Campagnes
    register_taxonomy('campaign_category', 'twigapaie_campaign', array(
        'labels' => array(
            'name' => __('Catégories de Campagnes', 'twiga-commerce-donation'),
            'singular_name' => __('Catégorie', 'twiga-commerce-donation'),
            'add_new_item' => __('Ajouter une nouvelle catégorie', 'twiga-commerce-donation'),
        ),
        'hierarchical' => true,
        'show_admin_column' => true,
        'rewrite' => array('slug' => 'categorie-campagne'),
    ));
    
    // Taxonomie Mots-clés pour Campagnes
    register_taxonomy('campaign_tag', 'twigapaie_campaign', array(
        'labels' => array(
            'name' => __('Mots-clés Campagnes', 'twiga-commerce-donation'),
            'singular_name' => __('Mot-clé', 'twiga-commerce-donation'),
            'add_new_item' => __('Ajouter un nouveau mot-clé', 'twiga-commerce-donation'),
        ),
        'hierarchical' => false,
        'show_admin_column' => true,
        'rewrite' => array('slug' => 'mot-cle-campagne'),
    ));
    
    // Taxonomie Catégories pour Produits
    register_taxonomy('product_category', 'twigapaie_product', array(
        'labels' => array(
            'name' => __('Catégories de Produits', 'twiga-commerce-donation'),
            'singular_name' => __('Catégorie', 'twiga-commerce-donation'),
            'add_new_item' => __('Ajouter une nouvelle catégorie', 'twiga-commerce-donation'),
        ),
        'hierarchical' => true,
        'show_admin_column' => true,
        'rewrite' => array('slug' => 'categorie-produit'),
    ));
    
    // Taxonomie Mots-clés pour Produits
    register_taxonomy('product_tag', 'twigapaie_product', array(
        'labels' => array(
            'name' => __('Mots-clés Produits', 'twiga-commerce-donation'),
            'singular_name' => __('Mot-clé', 'twiga-commerce-donation'),
            'add_new_item' => __('Ajouter un nouveau mot-clé', 'twiga-commerce-donation'),
        ),
        'hierarchical' => false,
        'show_admin_column' => true,
        'rewrite' => array('slug' => 'mot-cle-produit'),
    ));
}

// Initialiser le plugin
function twigapaie_init() {
    // Charger les traductions
    load_plugin_textdomain('twiga-commerce-donation', false, dirname(TWIGAPAIE_PLUGIN_BASENAME) . '/languages');
    
    // Enregistrer les post types et taxonomies
    twigapaie_register_post_types_and_taxonomies();
    
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

// Message après activation
function twigapaie_activation_notice() {
    if (get_transient('twigapaie_activation_notice')) {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><strong><?php _e('TwigaPaie activé avec succès !', 'twiga-commerce-donation'); ?></strong></p>
            <p><?php _e('Allez dans TwigaPaie > Paramètres pour configurer votre clé API et Supabase.', 'twiga-commerce-donation'); ?></p>
            <p><em><?php _e('Si vous rencontrez des erreurs 404, allez dans Réglages > Permaliens et cliquez sur Enregistrer.', 'twiga-commerce-donation'); ?></em></p>
        </div>
        <?php
        delete_transient('twigapaie_activation_notice');
    }
}
add_action('admin_notices', 'twigapaie_activation_notice');
