<?php
/**
 * Classe pour gérer l'API TwigaPaie
 */

if (!defined('ABSPATH')) {
    exit;
}

class TwigaPaie_API {
    
    private $api_key;
    private $base_url = 'https://api-gateway-production-9ad5.up.railway.app';
    
    public function __construct() {
        $this->api_key = get_option('twigapaie_api_key', '');
    }
    
    /**
     * Effectuer une requête API
     */
    private function make_request($endpoint, $method = 'POST', $data = array()) {
        $url = $this->base_url . $endpoint;
        
        $args = array(
            'method' => $method,
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->api_key,
                'Content-Type' => 'application/json',
            ),
            'timeout' => 30,
        );
        
        if (!empty($data) && $method !== 'GET') {
            $args['body'] = json_encode($data);
        }
        
        $response = wp_remote_request($url, $args);
        
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'error' => $response->get_error_message(),
            );
        }
        
        $body = wp_remote_retrieve_body($response);
        $decoded = json_decode($body, true);
        
        return array(
            'success' => wp_remote_retrieve_response_code($response) === 200,
            'data' => $decoded,
        );
    }
    
    /**
     * Initier un paiement E-Money
     */
    public function initiate_emoney_payment($customer_phone, $amount, $currency, $client_order_id) {
        $data = array(
            'customer_phone' => $customer_phone,
            'amount' => strval($amount),
            'currency' => $currency,
            'client_order_id' => $client_order_id,
        );
        
        return $this->make_request('/api/payments/payment-service', 'POST', $data);
    }
    
    /**
     * Vérifier le statut d'un paiement E-Money
     */
    public function check_emoney_payment_status($order_id) {
        $data = array(
            'order_id' => $order_id,
        );
        
        return $this->make_request('/api/payments/payment-check', 'POST', $data);
    }
    
    /**
     * Initier un paiement par carte (FlexPay/E-Card)
     */
    public function initiate_card_payment($amount, $currency, $description, $callback_url, $approve_url, $cancel_url, $decline_url) {
        $data = array(
            'amount' => strval($amount),
            'currency' => $currency,
            'description' => $description,
            'callback_url' => $callback_url,
            'approve_url' => $approve_url,
            'cancel_url' => $cancel_url,
            'decline_url' => $decline_url,
        );
        
        return $this->make_request('/api/flexpay/payment-service', 'POST', $data);
    }
    
    /**
     * Vérifier le statut d'un paiement par carte
     */
    public function check_card_payment_status($order_number) {
        $data = array(
            'order_number' => $order_number,
        );
        
        return $this->make_request('/api/flexpay/payment-check', 'GET', $data);
    }
    
    /**
     * Effectuer des virements groupés (Bulk Pay-Out)
     */
    public function bulk_payout($currency, $client_bulk_id, $items) {
        $data = array(
            'currency' => $currency,
            'client_bulk_id' => $client_bulk_id,
            'items' => $items,
        );
        
        return $this->make_request('/api/bulk-payments/payment-pay-out', 'POST', $data);
    }
    
    /**
     * Vérifier le statut d'un virement groupé
     */
    public function check_bulk_payout_status($bulk_id) {
        return $this->make_request('/api/bulk-payments/' . $bulk_id . '/details', 'GET');
    }
}
