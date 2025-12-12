<?php
/**
 * Classe pour gérer l'intégration Supabase
 */

if (!defined('ABSPATH')) {
    exit;
}

class TwigaPaie_Supabase {
    
    private $supabase_url;
    private $supabase_key;
    
    public function __construct() {
        $this->supabase_url = get_option('twigapaie_supabase_url', '');
        $this->supabase_key = get_option('twigapaie_supabase_key', '');
    }
    
    /**
     * Effectuer une requête à l'API Supabase
     */
    private function make_request($table, $method = 'POST', $data = array(), $params = '') {
        if (empty($this->supabase_url) || empty($this->supabase_key)) {
            return array(
                'success' => false,
                'error' => __('Configuration Supabase manquante', 'twiga-commerce-donation'),
            );
        }
        
        $url = rtrim($this->supabase_url, '/') . '/rest/v1/' . $table . $params;
        
        $args = array(
            'method' => $method,
            'headers' => array(
                'apikey' => $this->supabase_key,
                'Authorization' => 'Bearer ' . $this->supabase_key,
                'Content-Type' => 'application/json',
                'Prefer' => 'return=representation',
            ),
            'timeout' => 30,
        );
        
        if (!empty($data)) {
            $args['body'] = json_encode($data);
        }
        
        $response = wp_remote_request($url, $args);
        
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'error' => $response->get_error_message(),
            );
        }
        
        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $decoded = json_decode($body, true);
        
        return array(
            'success' => in_array($code, array(200, 201)),
            'data' => $decoded,
            'code' => $code,
        );
    }
    
    /**
     * Enregistrer une transaction dans Supabase
     */
    public function record_transaction($transaction_data) {
        // Calculer les frais
        $aggregator_fee = floatval(get_option('twigapaie_aggregator_fee', 2.5));
        $defmaks_fee = floatval(get_option('twigapaie_defmaks_fee', 3.5));
        $total_fee_percent = $aggregator_fee + $defmaks_fee;
        
        $amount = floatval($transaction_data['amount']);
        $currency = $transaction_data['currency'];
        
        // Calculer les revenus DefMaks
        $defmaks_revenue = ($amount * $defmaks_fee) / 100;
        
        // Préparer les données de la transaction
        $data = array(
            'wallet_id' => isset($transaction_data['wallet_id']) ? $transaction_data['wallet_id'] : null,
            'amount' => $amount,
            'currency' => $currency,
            'transaction_type' => $transaction_data['type'], // 'donation' ou 'purchase'
            'description' => isset($transaction_data['description']) ? $transaction_data['description'] : '',
            'external_reference' => $transaction_data['order_id'],
            'defmaks_revenue_cdf' => ($currency === 'CDF') ? $defmaks_revenue : 0,
            'defmaks_revenue_usd' => ($currency === 'USD') ? $defmaks_revenue : 0,
            'transaction_date' => current_time('mysql', true),
        );
        
        return $this->make_request('transactions', 'POST', $data);
    }
    
    /**
     * Créer ou récupérer un profil utilisateur
     */
    public function get_or_create_profile($user_data) {
        // Vérifier si le profil existe
        $params = '?email=eq.' . urlencode($user_data['email']);
        $result = $this->make_request('profiles', 'GET', array(), $params);
        
        if ($result['success'] && !empty($result['data'])) {
            return array(
                'success' => true,
                'profile' => $result['data'][0],
            );
        }
        
        // Créer un nouveau profil
        $profile_data = array(
            'first_name' => $user_data['first_name'],
            'last_name' => $user_data['last_name'],
            'email' => $user_data['email'],
            'phone' => isset($user_data['phone']) ? $user_data['phone'] : null,
            'client_id' => isset($user_data['client_id']) ? $user_data['client_id'] : null,
        );
        
        return $this->make_request('profiles', 'POST', $profile_data);
    }
    
    /**
     * Créer ou récupérer un wallet
     */
    public function get_or_create_wallet($profile_id) {
        // Vérifier si le wallet existe
        $params = '?profile_id=eq.' . urlencode($profile_id);
        $result = $this->make_request('wallets', 'GET', array(), $params);
        
        if ($result['success'] && !empty($result['data'])) {
            return array(
                'success' => true,
                'wallet' => $result['data'][0],
            );
        }
        
        // Créer un nouveau wallet
        $wallet_data = array(
            'profile_id' => $profile_id,
            'wallet_address' => 'TWIGA_' . strtoupper(wp_generate_password(20, false)),
            'balance_cdf' => 0,
            'balance_usd' => 0,
        );
        
        return $this->make_request('wallets', 'POST', $wallet_data);
    }
    
    /**
     * Récupérer les statistiques des transactions
     */
    public function get_transaction_stats($start_date = null, $end_date = null) {
        $params = '?select=*';
        
        if ($start_date) {
            $params .= '&transaction_date=gte.' . $start_date;
        }
        
        if ($end_date) {
            $params .= '&transaction_date=lte.' . $end_date;
        }
        
        return $this->make_request('transactions', 'GET', array(), $params);
    }
}
