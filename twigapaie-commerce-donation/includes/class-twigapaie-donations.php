<?php
/**
 * Classe pour gérer les donations
 */

if (!defined('ABSPATH')) {
    exit;
}

class TwigaPaie_Donations {
    
    public function __construct() {
        // AJAX pour traiter les donations
        add_action('wp_ajax_twigapaie_process_donation', array($this, 'process_donation'));
        add_action('wp_ajax_nopriv_twigapaie_process_donation', array($this, 'process_donation'));
        
        // Métaboxes pour les campagnes
        add_action('add_meta_boxes', array($this, 'add_campaign_meta_boxes'));
        add_action('save_post_twigapaie_campaign', array($this, 'save_campaign_meta'));
    }
    
    /**
     * Ajouter les métaboxes pour les campagnes
     */
    public function add_campaign_meta_boxes() {
        add_meta_box(
            'twigapaie_campaign_settings',
            __('Paramètres de la campagne', 'twiga-commerce-donation'),
            array($this, 'render_campaign_meta_box'),
            'twigapaie_campaign',
            'normal',
            'high'
        );
    }
    
    /**
     * Rendre la métabox des paramètres de campagne
     */
    public function render_campaign_meta_box($post) {
        wp_nonce_field('twigapaie_campaign_meta', 'twigapaie_campaign_meta_nonce');
        
        $goal_amount = get_post_meta($post->ID, '_campaign_goal', true);
        $currency = get_post_meta($post->ID, '_campaign_currency', true) ?: 'CDF';
        $preset_amounts = get_post_meta($post->ID, '_campaign_preset_amounts', true) ?: '1000,5000,10000';
        $end_date = get_post_meta($post->ID, '_campaign_end_date', true);
        
        ?>
        <div class="twigapaie-meta-box">
            <p>
                <label for="campaign_goal"><?php _e('Objectif de la campagne:', 'twiga-commerce-donation'); ?></label><br>
                <input type="number" id="campaign_goal" name="campaign_goal" value="<?php echo esc_attr($goal_amount); ?>" step="0.01" class="regular-text">
            </p>
            <p>
                <label for="campaign_currency"><?php _e('Devise:', 'twiga-commerce-donation'); ?></label><br>
                <select id="campaign_currency" name="campaign_currency">
                    <option value="CDF" <?php selected($currency, 'CDF'); ?>>CDF</option>
                    <option value="USD" <?php selected($currency, 'USD'); ?>>USD</option>
                </select>
            </p>
            <p>
                <label for="campaign_preset_amounts"><?php _e('Montants prédéfinis (séparés par des virgules):', 'twiga-commerce-donation'); ?></label><br>
                <input type="text" id="campaign_preset_amounts" name="campaign_preset_amounts" value="<?php echo esc_attr($preset_amounts); ?>" class="regular-text">
            </p>
            <p>
                <label for="campaign_end_date"><?php _e('Date de fin:', 'twiga-commerce-donation'); ?></label><br>
                <input type="date" id="campaign_end_date" name="campaign_end_date" value="<?php echo esc_attr($end_date); ?>" class="regular-text">
            </p>
        </div>
        <?php
    }
    
    /**
     * Sauvegarder les métadonnées de la campagne
     */
    public function save_campaign_meta($post_id) {
        if (!isset($_POST['twigapaie_campaign_meta_nonce']) || !wp_verify_nonce($_POST['twigapaie_campaign_meta_nonce'], 'twigapaie_campaign_meta')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        if (isset($_POST['campaign_goal'])) {
            update_post_meta($post_id, '_campaign_goal', sanitize_text_field($_POST['campaign_goal']));
        }
        
        if (isset($_POST['campaign_currency'])) {
            update_post_meta($post_id, '_campaign_currency', sanitize_text_field($_POST['campaign_currency']));
        }
        
        if (isset($_POST['campaign_preset_amounts'])) {
            update_post_meta($post_id, '_campaign_preset_amounts', sanitize_text_field($_POST['campaign_preset_amounts']));
        }
        
        if (isset($_POST['campaign_end_date'])) {
            update_post_meta($post_id, '_campaign_end_date', sanitize_text_field($_POST['campaign_end_date']));
        }
    }
    
    /**
     * Traiter une donation via AJAX
     */
    public function process_donation() {
        check_ajax_referer('twigapaie_nonce', 'nonce');
        
        global $wpdb;
        
        // Récupérer les données
        $campaign_id = isset($_POST['campaign_id']) ? intval($_POST['campaign_id']) : 0;
        $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
        $currency = isset($_POST['currency']) ? sanitize_text_field($_POST['currency']) : 'CDF';
        $payment_method = isset($_POST['payment_method']) ? sanitize_text_field($_POST['payment_method']) : 'emoney';
        $donor_name = isset($_POST['donor_name']) ? sanitize_text_field($_POST['donor_name']) : '';
        $donor_email = isset($_POST['donor_email']) ? sanitize_email($_POST['donor_email']) : '';
        $donor_phone = isset($_POST['donor_phone']) ? sanitize_text_field($_POST['donor_phone']) : '';
        
        // Validation
        if ($amount <= 0 || empty($donor_name) || empty($donor_email)) {
            wp_send_json_error(array(
                'message' => __('Veuillez remplir tous les champs obligatoires.', 'twiga-commerce-donation'),
            ));
        }
        
        // Insérer la donation dans la base de données
        $table = $wpdb->prefix . 'twigapaie_donations';
        $inserted = $wpdb->insert($table, array(
            'campaign_id' => $campaign_id,
            'donor_name' => $donor_name,
            'donor_email' => $donor_email,
            'donor_phone' => $donor_phone,
            'amount' => $amount,
            'currency' => $currency,
            'payment_method' => $payment_method,
            'payment_status' => 'pending',
        ));
        
        if (!$inserted) {
            wp_send_json_error(array(
                'message' => __('Erreur lors de l\'enregistrement de la donation.', 'twiga-commerce-donation'),
            ));
        }
        
        $donation_id = $wpdb->insert_id;
        
        // Initier le paiement
        if ($payment_method === 'emoney' && !empty($donor_phone)) {
            $this->initiate_emoney_payment($donation_id, $donor_phone, $amount, $currency);
        } elseif ($payment_method === 'ecard') {
            $this->initiate_card_payment($donation_id, $amount, $currency);
        }
    }
    
    /**
     * Initier un paiement E-Money
     */
    private function initiate_emoney_payment($donation_id, $phone, $amount, $currency) {
        global $wpdb;
        
        try {
            // Formater le numéro de téléphone
            $phone_data = TwigaPaie_Phone_Formatter::format_phone_and_deduce_provider($phone);
            
            // Appeler l'API TwigaPaie
            $api = new TwigaPaie_API();
            $result = $api->initiate_emoney_payment(
                $phone_data['formatted_phone'],
                $amount,
                $currency,
                'DONATION_' . $donation_id . '_' . time()
            );
            
            if ($result['success']) {
                $order_id = isset($result['data']['order_id']) ? $result['data']['order_id'] : '';
                
                // Mettre à jour la donation
                $wpdb->update(
                    $wpdb->prefix . 'twigapaie_donations',
                    array(
                        'twigapaie_order_id' => $order_id,
                        'provider_id' => $phone_data['provider_id'],
                        'provider_name' => $phone_data['provider_name'],
                        'payment_status' => 'processing',
                    ),
                    array('id' => $donation_id)
                );
                
                wp_send_json_success(array(
                    'message' => __('Paiement initié. Veuillez vérifier votre téléphone pour confirmer.', 'twiga-commerce-donation'),
                    'provider' => $phone_data['provider_name'],
                    'order_id' => $order_id,
                ));
            } else {
                wp_send_json_error(array(
                    'message' => isset($result['error']) ? $result['error'] : __('Erreur lors de l\'initiation du paiement.', 'twiga-commerce-donation'),
                ));
            }
        } catch (Exception $e) {
            wp_send_json_error(array(
                'message' => $e->getMessage(),
            ));
        }
    }
    
    /**
     * Initier un paiement par carte
     */
    private function initiate_card_payment($donation_id, $amount, $currency) {
        $api = new TwigaPaie_API();
        
        $site_url = get_site_url();
        $result = $api->initiate_card_payment(
            $amount,
            $currency,
            'Donation #' . $donation_id,
            $site_url . '/?twigapaie_webhook=1',
            $site_url . '/?twigapaie_success=1&donation_id=' . $donation_id,
            $site_url . '/?twigapaie_cancel=1',
            $site_url . '/?twigapaie_decline=1'
        );
        
        if ($result['success'] && isset($result['data']['url'])) {
            global $wpdb;
            $wpdb->update(
                $wpdb->prefix . 'twigapaie_donations',
                array(
                    'twigapaie_order_id' => $result['data']['orderNumber'],
                    'payment_status' => 'processing',
                ),
                array('id' => $donation_id)
            );
            
            wp_send_json_success(array(
                'redirect_url' => $result['data']['url'],
            ));
        } else {
            wp_send_json_error(array(
                'message' => isset($result['error']) ? $result['error'] : __('Erreur lors de l\'initiation du paiement par carte.', 'twiga-commerce-donation'),
            ));
        }
    }
}
