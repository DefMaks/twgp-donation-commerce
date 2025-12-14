<?php
/**
 * Classe principale du plugin TwigaPaie
 */

if (!defined('ABSPATH')) {
    exit;
}

class TwigaPaie_Core {
    
    public function __construct() {
        // Enregistrer les scripts et styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        
        // Enregistrer les shortcodes
        add_action('init', array($this, 'register_shortcodes'));
        
        // Ajouter les endpoints personnalisés
        add_action('init', array($this, 'add_rewrite_rules'));
        add_action('template_redirect', array($this, 'handle_custom_endpoints'));
        
        // Gérer les templates pour les post types
        add_filter('single_template', array($this, 'load_custom_template'));
        add_filter('archive_template', array($this, 'load_custom_archive_template'));
    }
    
    /**
     * Enregistrer les assets publics
     */
    public function enqueue_public_assets() {
        wp_enqueue_style(
            'twigapaie-public-style',
            TWIGAPAIE_PLUGIN_URL . 'public/css/public-style.css',
            array(),
            TWIGAPAIE_VERSION
        );
        
        wp_enqueue_script(
            'twigapaie-public-script',
            TWIGAPAIE_PLUGIN_URL . 'public/js/public-script.js',
            array('jquery'),
            TWIGAPAIE_VERSION,
            true
        );
        
        // Passer des variables PHP à JavaScript
        wp_localize_script('twigapaie-public-script', 'twigapaieData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('twigapaie_nonce'),
            'currency' => get_option('twigapaie_currency', 'CDF'),
            'strings' => array(
                'processing' => __('Traitement en cours...', 'twiga-commerce-donation'),
                'error' => __('Une erreur est survenue', 'twiga-commerce-donation'),
            )
        ));
    }
    
    /**
     * Enregistrer les assets admin
     */
    public function enqueue_admin_assets($hook) {
        global $post_type;
        
        // Charger sur les pages du plugin ET sur les pages d'édition des post types
        $load_assets = (
            strpos($hook, 'twigapaie') !== false ||
            $post_type === 'twigapaie_product' ||
            $post_type === 'twigapaie_campaign'
        );
        
        if (!$load_assets) {
            return;
        }
        
        wp_enqueue_style(
            'twigapaie-admin-style',
            TWIGAPAIE_PLUGIN_URL . 'admin/css/admin-style.css',
            array(),
            TWIGAPAIE_VERSION
        );
        
        // Enqueue media uploader
        wp_enqueue_media();
        
        wp_enqueue_script(
            'twigapaie-admin-script',
            TWIGAPAIE_PLUGIN_URL . 'admin/js/admin-script.js',
            array('jquery', 'jquery-ui-datepicker'),
            TWIGAPAIE_VERSION,
            true
        );
        
        wp_localize_script('twigapaie-admin-script', 'twigapaieAdminData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('twigapaie_admin_nonce'),
        ));
    }
    
    /**
     * Enregistrer les types de post personnalisés
     */
    public function register_post_types() {
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
            'supports' => array('title', 'editor', 'thumbnail'),
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
            'supports' => array('title', 'editor', 'thumbnail'),
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
    
    /**
     * Enregistrer les shortcodes
     */
    public function register_shortcodes() {
        add_shortcode('twigapaie_donation_form', array($this, 'donation_form_shortcode'));
        add_shortcode('twigapaie_products', array($this, 'products_shortcode'));
        add_shortcode('twigapaie_cart', array($this, 'cart_shortcode'));
        add_shortcode('twigapaie_checkout', array($this, 'checkout_shortcode'));
    }
    
    /**
     * Shortcode pour le formulaire de donation
     */
    public function donation_form_shortcode($atts) {
        $atts = shortcode_atts(array(
            'campaign_id' => 0,
        ), $atts);
        
        ob_start();
        include TWIGAPAIE_PLUGIN_DIR . 'public/templates/donation-form.php';
        return ob_get_clean();
    }
    
    /**
     * Shortcode pour afficher les produits
     */
    public function products_shortcode($atts) {
        $atts = shortcode_atts(array(
            'limit' => 12,
        ), $atts);
        
        ob_start();
        $products = get_posts(array(
            'post_type' => 'twigapaie_product',
            'posts_per_page' => intval($atts['limit']),
        ));
        
        echo '<div class="twigapaie-products-grid">';
        foreach ($products as $product) {
            setup_postdata($product);
            include TWIGAPAIE_PLUGIN_DIR . 'public/templates/product-single.php';
        }
        echo '</div>';
        wp_reset_postdata();
        
        return ob_get_clean();
    }
    
    /**
     * Shortcode pour le panier
     */
    public function cart_shortcode($atts) {
        ob_start();
        include TWIGAPAIE_PLUGIN_DIR . 'public/templates/cart.php';
        return ob_get_clean();
    }
    
    /**
     * Shortcode pour le checkout
     */
    public function checkout_shortcode($atts) {
        ob_start();
        include TWIGAPAIE_PLUGIN_DIR . 'public/templates/checkout.php';
        return ob_get_clean();
    }
    
    /**
     * Ajouter les règles de réécriture personnalisées
     */
    public function add_rewrite_rules() {
        add_rewrite_rule('^twigapaie-webhook/?$', 'index.php?twigapaie_webhook=1', 'top');
        add_rewrite_tag('%twigapaie_webhook%', '([^&]+)');
    }
    
    /**
     * Gérer les endpoints personnalisés
     */
    public function handle_custom_endpoints() {
        if (get_query_var('twigapaie_webhook')) {
            $handler = new TwigaPaie_Payment_Handler();
            $handler->handle_webhook();
            exit;
        }
    }
}
