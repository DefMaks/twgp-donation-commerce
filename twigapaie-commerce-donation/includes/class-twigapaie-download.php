<?php
/**
 * Classe pour gérer les téléchargements sécurisés
 */

if (!defined('ABSPATH')) {
    exit;
}

class TwigaPaie_Download {
    
    public function __construct() {
        // Endpoint pour le téléchargement
        add_action('init', array($this, 'add_download_endpoint'));
        add_action('template_redirect', array($this, 'handle_download'));
    }
    
    /**
     * Ajouter l'endpoint de téléchargement
     */
    public function add_download_endpoint() {
        add_rewrite_rule('^twigapaie-download/([^/]+)/?$', 'index.php?twigapaie_download=$matches[1]', 'top');
        add_rewrite_tag('%twigapaie_download%', '([^&]+)');
    }
    
    /**
     * Gérer le téléchargement
     */
    public function handle_download() {
        $token = get_query_var('twigapaie_download');
        
        if (empty($token)) {
            return;
        }
        
        global $wpdb;
        
        // Vérifier le token
        $download = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}twigapaie_downloads WHERE token = %s AND expires_at > NOW() AND (download_count < download_limit OR download_limit = -1)",
            $token
        ));
        
        if (!$download) {
            wp_die(__('Lien de téléchargement invalide ou expiré.', 'twiga-commerce-donation'));
        }
        
        // Récupérer l'URL du fichier
        $product_data = $wpdb->get_row($wpdb->prepare(
            "SELECT file_url FROM {$wpdb->prefix}twigapaie_products WHERE post_id = %d",
            $download->product_id
        ));
        
        if (!$product_data || empty($product_data->file_url)) {
            wp_die(__('Fichier non trouvé.', 'twiga-commerce-donation'));
        }
        
        // Incrémenter le compteur
        $wpdb->update(
            $wpdb->prefix . 'twigapaie_downloads',
            array('download_count' => $download->download_count + 1),
            array('id' => $download->id)
        );
        
        // Rediriger vers le fichier
        wp_redirect($product_data->file_url);
        exit;
    }
    
    /**
     * Créer un token de téléchargement
     */
    public static function create_download_token($order_id, $product_id, $days = 30) {
        global $wpdb;
        
        // Récupérer les infos de la commande
        $order = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}twigapaie_orders WHERE id = %d",
            $order_id
        ));
        
        if (!$order) {
            return false;
        }
        
        // Récupérer les infos du produit
        $product_data = $wpdb->get_row($wpdb->prepare(
            "SELECT download_limit FROM {$wpdb->prefix}twigapaie_products WHERE post_id = %d",
            $product_id
        ));
        
        $download_limit = $product_data ? $product_data->download_limit : -1;
        
        // Générer un token unique
        $token = wp_generate_password(32, false);
        
        // Insérer dans la table des téléchargements
        $wpdb->insert($wpdb->prefix . 'twigapaie_downloads', array(
            'token' => $token,
            'order_id' => $order_id,
            'product_id' => $product_id,
            'customer_email' => $order->customer_email,
            'download_limit' => $download_limit,
            'download_count' => 0,
            'expires_at' => date('Y-m-d H:i:s', strtotime('+' . $days . ' days')),
            'created_at' => current_time('mysql'),
        ));
        
        return $token;
    }
    
    /**
     * Obtenir l'URL de téléchargement
     */
    public static function get_download_url($token) {
        return home_url('/twigapaie-download/' . $token);
    }
}
