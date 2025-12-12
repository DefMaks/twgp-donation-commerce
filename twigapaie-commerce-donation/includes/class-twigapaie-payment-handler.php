<?php
/**
 * Classe pour gérer les webhooks et callbacks de paiement
 */

if (!defined('ABSPATH')) {
    exit;
}

class TwigaPaie_Payment_Handler {
    
    public function __construct() {
        // Ajouter les handlers pour les redirections de succès/échec
        add_action('template_redirect', array($this, 'handle_payment_redirect'));
    }
    
    /**
     * Gérer le webhook TwigaPaie
     */
    public function handle_webhook() {
        $raw_data = file_get_contents('php://input');
        $data = json_decode($raw_data, true);
        
        // Logger le webhook
        error_log('TwigaPaie Webhook: ' . print_r($data, true));
        
        if (!$data) {
            http_response_code(400);
            echo json_encode(array('error' => 'Invalid JSON'));
            exit;
        }
        
        // Traiter selon le type de paiement
        if (isset($data['order_id'])) {
            $this->process_emoney_webhook($data);
        } elseif (isset($data['orderNumber'])) {
            $this->process_card_webhook($data);
        }
        
        http_response_code(200);
        echo json_encode(array('success' => true));
        exit;
    }
    
    /**
     * Traiter le webhook E-Money
     */
    private function process_emoney_webhook($data) {
        global $wpdb;
        
        $order_id = $data['order_id'];
        $status = isset($data['status']) ? $data['status'] : 'unknown';
        
        // Chercher dans les donations
        $donation = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}twigapaie_donations WHERE twigapaie_order_id = %s",
            $order_id
        ));
        
        if ($donation) {
            $this->complete_donation($donation, $status);
            return;
        }
        
        // Chercher dans les commandes
        $order = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}twigapaie_orders WHERE twigapaie_order_id = %s",
            $order_id
        ));
        
        if ($order) {
            $this->complete_order($order, $status);
        }
    }
    
    /**
     * Traiter le webhook carte
     */
    private function process_card_webhook($data) {
        global $wpdb;
        
        $order_number = $data['orderNumber'];
        $status = isset($data['status']) ? $data['status'] : 'unknown';
        
        // Chercher dans les donations
        $donation = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}twigapaie_donations WHERE twigapaie_order_id = %s",
            $order_number
        ));
        
        if ($donation) {
            $this->complete_donation($donation, $status);
            return;
        }
        
        // Chercher dans les commandes
        $order = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}twigapaie_orders WHERE twigapaie_order_id = %s",
            $order_number
        ));
        
        if ($order) {
            $this->complete_order($order, $status);
        }
    }
    
    /**
     * Compléter une donation
     */
    private function complete_donation($donation, $status) {
        global $wpdb;
        
        $payment_status = ($status === 'success' || $status === 'completed') ? 'completed' : 'failed';
        
        $wpdb->update(
            $wpdb->prefix . 'twigapaie_donations',
            array(
                'payment_status' => $payment_status,
                'completed_at' => current_time('mysql'),
            ),
            array('id' => $donation->id)
        );
        
        if ($payment_status === 'completed') {
            // Enregistrer dans Supabase
            $this->record_to_supabase(array(
                'type' => 'donation',
                'amount' => $donation->amount,
                'currency' => $donation->currency,
                'order_id' => $donation->twigapaie_order_id,
                'customer_email' => $donation->donor_email,
            ));
        }
    }
    
    /**
     * Compléter une commande
     */
    private function complete_order($order, $status) {
        global $wpdb;
        
        $payment_status = ($status === 'success' || $status === 'completed') ? 'completed' : 'failed';
        
        $wpdb->update(
            $wpdb->prefix . 'twigapaie_orders',
            array(
                'payment_status' => $payment_status,
                'completed_at' => current_time('mysql'),
            ),
            array('id' => $order->id)
        );
        
        if ($payment_status === 'completed') {
            // Enregistrer dans Supabase
            $this->record_to_supabase(array(
                'type' => 'purchase',
                'amount' => $order->total_amount,
                'currency' => $order->currency,
                'order_id' => $order->twigapaie_order_id,
                'customer_email' => $order->customer_email,
            ));
            
            // Envoyer l'email avec les liens de téléchargement
            $this->send_purchase_email($order);
        }
    }
    
    /**
     * Enregistrer dans Supabase
     */
    private function record_to_supabase($data) {
        $supabase = new TwigaPaie_Supabase();
        
        // Récupérer ou créer le profil
        $profile_result = $supabase->get_or_create_profile(array(
            'first_name' => 'Anonymous',
            'last_name' => 'User',
            'email' => $data['customer_email'],
        ));
        
        if (!$profile_result['success']) {
            error_log('Erreur Supabase profil: ' . print_r($profile_result, true));
            return;
        }
        
        $profile_id = isset($profile_result['profile']['id']) ? $profile_result['profile']['id'] : null;
        
        if (!$profile_id && isset($profile_result['data'][0]['id'])) {
            $profile_id = $profile_result['data'][0]['id'];
        }
        
        if (!$profile_id) {
            error_log('Impossible de récupérer le profile_id');
            return;
        }
        
        // Récupérer ou créer le wallet
        $wallet_result = $supabase->get_or_create_wallet($profile_id);
        
        if (!$wallet_result['success']) {
            error_log('Erreur Supabase wallet: ' . print_r($wallet_result, true));
            return;
        }
        
        $wallet_id = isset($wallet_result['wallet']['id']) ? $wallet_result['wallet']['id'] : null;
        
        if (!$wallet_id && isset($wallet_result['data'][0]['id'])) {
            $wallet_id = $wallet_result['data'][0]['id'];
        }
        
        // Enregistrer la transaction
        $transaction_data = array(
            'wallet_id' => $wallet_id,
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'type' => $data['type'],
            'order_id' => $data['order_id'],
        );
        
        $result = $supabase->record_transaction($transaction_data);
        
        if ($result['success']) {
            error_log('Transaction enregistrée dans Supabase avec succès');
        } else {
            error_log('Erreur lors de l\'enregistrement dans Supabase: ' . print_r($result, true));
        }
    }
    
    /**
     * Envoyer l'email de confirmation d'achat
     */
    private function send_purchase_email($order) {
        $to = $order->customer_email;
        $subject = __('Votre achat est confirmé', 'twiga-commerce-donation');
        
        $message = __('Bonjour', 'twiga-commerce-donation') . ' ' . $order->customer_name . ',<br><br>';
        $message .= __('Merci pour votre achat. Voici les liens de téléchargement de vos produits:', 'twiga-commerce-donation') . '<br><br>';
        
        $items = json_decode($order->items, true);
        global $wpdb;
        $table_products = $wpdb->prefix . 'twigapaie_products';
        
        foreach ($items as $item) {
            $product = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_products WHERE post_id = %d", $item['product_id']));
            if ($product && $product->file_url) {
                $post = get_post($item['product_id']);
                $message .= '- ' . $post->post_title . ': <a href="' . esc_url($product->file_url) . '">' . __('Télécharger', 'twiga-commerce-donation') . '</a><br>';
            }
        }
        
        $message .= '<br>' . __('Cordialement,', 'twiga-commerce-donation') . '<br>';
        $message .= get_bloginfo('name');
        
        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail($to, $subject, $message, $headers);
    }
    
    /**
     * Gérer les redirections de paiement
     */
    public function handle_payment_redirect() {
        if (isset($_GET['twigapaie_success'])) {
            // Afficher la page de succès
            include TWIGAPAIE_PLUGIN_DIR . 'public/templates/payment-success.php';
            exit;
        }
        
        if (isset($_GET['twigapaie_cancel'])) {
            // Afficher la page d'annulation
            include TWIGAPAIE_PLUGIN_DIR . 'public/templates/payment-cancel.php';
            exit;
        }
        
        if (isset($_GET['twigapaie_decline'])) {
            // Afficher la page de refus
            include TWIGAPAIE_PLUGIN_DIR . 'public/templates/payment-decline.php';
            exit;
        }
    }
}
