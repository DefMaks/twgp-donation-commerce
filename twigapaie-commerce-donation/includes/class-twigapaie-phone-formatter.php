<?php
/**
 * Classe pour formater les numéros de téléphone selon les règles RDC
 */

if (!defined('ABSPATH')) {
    exit;
}

class TwigaPaie_Phone_Formatter {
    
    /**
     * Déduit l'ID du fournisseur (pour la RDC) et formate le numéro selon ses règles.
     * Assumes que le numéro saisi est un numéro congolais à 9 chiffres (e.g., 81XXXXXXX ou 90XXXXXXX).
     * @param string $raw_phone Le numéro de téléphone saisi (e.g., "822032855").
     * @return array Un objet contenant le numéro formaté et l'ID du fournisseur déduit.
     * @throws Exception Si le numéro est invalide
     */
    public static function format_phone_and_deduce_provider($raw_phone) {
        // Nettoyer le numéro
        $phone = preg_replace('/[\s\-\(\)]/', '', $raw_phone);
        
        // Si le numéro commence par un code pays, on le retire pour la déduction par préfixe local.
        $normalized_phone = $phone;
        if (strpos($normalized_phone, '+243') === 0) {
            $normalized_phone = substr($normalized_phone, 4);
        } elseif (strpos($normalized_phone, '243') === 0) {
            $normalized_phone = substr($normalized_phone, 3);
        }
        
        if (strlen($normalized_phone) < 8) {
            throw new Exception(__('Numéro de téléphone trop court pour la déduction.', 'twiga-commerce-donation'));
        }
        
        $prefix = substr($normalized_phone, 0, 2);
        
        $formatted_phone = '';
        $provider_id = '';
        $provider_name = '';
        
        // Déduction basée sur les préfixes RDC et la documentation TwigaPaie
        if (in_array($prefix, array('80', '84', '85', '89'))) {
            // OrangeMoney (ID 10): Client's phone should start from 0: 080XXXXXXX
            $provider_id = '10';
            $provider_name = 'Orange Money';
            $formatted_phone = (strpos($normalized_phone, '0') === 0) ? $normalized_phone : '0' . $normalized_phone;
        } elseif (in_array($prefix, array('81', '82', '83'))) {
            // Vodacom (ID 9): Client's phone number with country code. Example: "243000000000"
            $provider_id = '9';
            $provider_name = 'Vodacom M-Pesa';
            // Le format international sans 0 initial (24381XXXXXXX)
            $formatted_phone = '243' . $normalized_phone;
        } elseif (in_array($prefix, array('97', '98', '99'))) {
            // Airtel (ID 17): Client's phone shouldn't start from 0: 999000000
            $provider_id = '17';
            $provider_name = 'Airtel Money';
            // Le numéro est déjà dans le bon format (pas de 0 initial)
            $formatted_phone = $normalized_phone;
        } elseif ($prefix === '90') {
            // Africell (ID 19): Client's phone should start from 0: 0900000000
            $provider_id = '19';
            $provider_name = 'Africell Money';
            $formatted_phone = (strpos($normalized_phone, '0') === 0) ? $normalized_phone : '0' . $normalized_phone;
        } else {
            // Par défaut, Vodacom (9) et format international si le préfixe n'est pas reconnu.
            error_log('⚠️ Préfixe de numéro non reconnu: ' . $prefix . '. Utilisation de Vodacom (9) par défaut.');
            $provider_id = '9';
            $provider_name = 'Vodacom M-Pesa (par défaut)';
            $formatted_phone = '243' . $normalized_phone;
        }
        
        return array(
            'formatted_phone' => $formatted_phone,
            'provider_id' => $provider_id,
            'provider_name' => $provider_name,
            'original_phone' => $raw_phone,
        );
    }
    
    /**
     * Valider un numéro de téléphone RDC
     */
    public static function validate_rdc_phone($phone) {
        try {
            $result = self::format_phone_and_deduce_provider($phone);
            return !empty($result['formatted_phone']);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Obtenir le nom du fournisseur depuis son ID
     */
    public static function get_provider_name($provider_id) {
        $providers = array(
            '9' => 'Vodacom M-Pesa',
            '10' => 'Orange Money',
            '17' => 'Airtel Money',
            '19' => 'Africell Money',
        );
        
        return isset($providers[$provider_id]) ? $providers[$provider_id] : __('Fournisseur inconnu', 'twiga-commerce-donation');
    }
}
