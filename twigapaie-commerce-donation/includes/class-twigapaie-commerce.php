<?php
/**
 * Classe pour gérer le système e-commerce
 */

if (!defined('ABSPATH')) {
    exit;
}

class TwigaPaie_Commerce {
    
    public function __construct() {
        // Métaboxes pour les produits
        add_action('add_meta_boxes', array($this, 'add_product_meta_boxes'));
        add_action('save_post_twigapaie_product', array($this, 'save_product_meta'));
        
        // AJAX pour le panier
        add_action('wp_ajax_twigapaie_add_to_cart', array($this, 'add_to_cart'));
        add_action('wp_ajax_nopriv_twigapaie_add_to_cart', array($this, 'add_to_cart'));
        add_action('wp_ajax_twigapaie_remove_from_cart', array($this, 'remove_from_cart'));
        add_action('wp_ajax_nopriv_twigapaie_remove_from_cart', array($this, 'remove_from_cart'));
        add_action('wp_ajax_twigapaie_process_checkout', array($this, 'process_checkout'));
        add_action('wp_ajax_nopriv_twigapaie_process_checkout', array($this, 'process_checkout'));
        
        // Initialiser la session
        add_action('init', array($this, 'init_session'));
    }
    
    /**
     * Initialiser la session pour le panier
     */
    public function init_session() {
        if (!session_id() && !headers_sent()) {
            session_start();
        }
    }
    
    /**
     * Ajouter les métaboxes pour les produits
     */
    public function add_product_meta_boxes() {
        add_meta_box(
            'twigapaie_product_settings',
            __('Paramètres du produit', 'twiga-commerce-donation'),
            array($this, 'render_product_meta_box'),
            'twigapaie_product',
            'normal',
            'high'
        );
    }
    
    /**
     * Rendre la métabox des paramètres de produit
     */
    public function render_product_meta_box($post) {
        wp_nonce_field('twigapaie_product_meta', 'twigapaie_product_meta_nonce');
        
        global $wpdb;
        $table = $wpdb->prefix . 'twigapaie_products';
        $product = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE post_id = %d", $post->ID));
        
        $price_cdf = $product ? $product->price_cdf : '';
        $price_usd = $product ? $product->price_usd : '';
        $file_url = $product ? $product->file_url : '';
        $download_limit = $product ? $product->download_limit : -1;
        $is_active = $product ? $product->is_active : 1;
        
        ?>
        <div class="twigapaie-meta-box">
            <p>
                <label for="product_price_cdf"><?php _e('Prix en CDF:', 'twiga-commerce-donation'); ?></label><br>
                <input type="number" id="product_price_cdf" name="product_price_cdf" value="<?php echo esc_attr($price_cdf); ?>" step="0.01" class="regular-text">
            </p>
            <p>
                <label for="product_price_usd"><?php _e('Prix en USD:', 'twiga-commerce-donation'); ?></label><br>
                <input type="number" id="product_price_usd" name="product_price_usd" value="<?php echo esc_attr($price_usd); ?>" step="0.01" class="regular-text">
            </p>
            <p>
                <label for="product_file_url"><?php _e('URL du fichier:', 'twiga-commerce-donation'); ?></label><br>
                <input type="url" id="product_file_url" name="product_file_url" value="<?php echo esc_attr($file_url); ?>" class="regular-text">
                <button type="button" class="button" id="upload_file_button"><?php _e('Télécharger un fichier', 'twiga-commerce-donation'); ?></button>
            </p>
            <p>
                <label for="product_download_limit"><?php _e('Limite de téléchargement (-1 pour illimité):', 'twiga-commerce-donation'); ?></label><br>
                <input type="number" id="product_download_limit" name="product_download_limit" value="<?php echo esc_attr($download_limit); ?>" class="regular-text">
            </p>
            <p>
                <label>
                    <input type="checkbox" id="product_is_active" name="product_is_active" value="1" <?php checked($is_active, 1); ?>>
                    <?php _e('Produit actif', 'twiga-commerce-donation'); ?>
                </label>
            </p>
        </div>
        <?php
    }
    
    /**
     * Sauvegarder les métadonnées du produit
     */
    public function save_product_meta($post_id) {
        if (!isset($_POST['twigapaie_product_meta_nonce']) || !wp_verify_nonce($_POST['twigapaie_product_meta_nonce'], 'twigapaie_product_meta')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'twigapaie_products';
        
        $data = array(
            'post_id' => $post_id,
            'price_cdf' => isset($_POST['product_price_cdf']) ? floatval($_POST['product_price_cdf']) : null,
            'price_usd' => isset($_POST['product_price_usd']) ? floatval($_POST['product_price_usd']) : null,
            'file_url' => isset($_POST['product_file_url']) ? esc_url_raw($_POST['product_file_url']) : null,
            'download_limit' => isset($_POST['product_download_limit']) ? intval($_POST['product_download_limit']) : -1,
            'is_active' => isset($_POST['product_is_active']) ? 1 : 0,
        );
        
        $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE post_id = %d", $post_id));
        
        if ($exists) {
            $wpdb->update($table, $data, array('post_id' => $post_id));
        } else {
            $wpdb->insert($table, $data);
        }
    }
    
    /**
     * Ajouter un produit au panier
     */
    public function add_to_cart() {
        check_ajax_referer('twigapaie_nonce', 'nonce');
        
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        
        if ($product_id <= 0) {
            wp_send_json_error(array('message' => __('Produit invalide.', 'twiga-commerce-donation')));
        }
        
        if (!isset($_SESSION['twigapaie_cart'])) {
            $_SESSION['twigapaie_cart'] = array();
        }
        
        if (!in_array($product_id, $_SESSION['twigapaie_cart'])) {
            $_SESSION['twigapaie_cart'][] = $product_id;
        }
        
        wp_send_json_success(array(
            'message' => __('Produit ajouté au panier.', 'twiga-commerce-donation'),
            'cart_count' => count($_SESSION['twigapaie_cart']),
        ));
    }
    
    /**
     * Retirer un produit du panier
     */
    public function remove_from_cart() {
        check_ajax_referer('twigapaie_nonce', 'nonce');
        
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        
        if (isset($_SESSION['twigapaie_cart'])) {
            $_SESSION['twigapaie_cart'] = array_diff($_SESSION['twigapaie_cart'], array($product_id));
        }
        
        wp_send_json_success(array(
            'message' => __('Produit retiré du panier.', 'twiga-commerce-donation'),
            'cart_count' => count($_SESSION['twigapaie_cart']),
        ));
    }
    
    /**
     * Traiter le checkout
     */
    public function process_checkout() {
        check_ajax_referer('twigapaie_nonce', 'nonce');
        
        if (empty($_SESSION['twigapaie_cart'])) {
            wp_send_json_error(array('message' => __('Votre panier est vide.', 'twiga-commerce-donation')));
        }
        
        global $wpdb;
        
        $customer_name = isset($_POST['customer_name']) ? sanitize_text_field($_POST['customer_name']) : '';
        $customer_email = isset($_POST['customer_email']) ? sanitize_email($_POST['customer_email']) : '';
        $customer_phone = isset($_POST['customer_phone']) ? sanitize_text_field($_POST['customer_phone']) : '';
        $currency = isset($_POST['currency']) ? sanitize_text_field($_POST['currency']) : 'CDF';
        $payment_method = isset($_POST['payment_method']) ? sanitize_text_field($_POST['payment_method']) : 'emoney';
        
        if (empty($customer_name) || empty($customer_email)) {
            wp_send_json_error(array('message' => __('Veuillez remplir tous les champs obligatoires.', 'twiga-commerce-donation')));
        }
        
        // Calculer le total
        $total = 0;
        $items = array();
        $table_products = $wpdb->prefix . 'twigapaie_products';
        
        foreach ($_SESSION['twigapaie_cart'] as $product_id) {
            $product = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_products WHERE post_id = %d", $product_id));
            if ($product) {
                $price = ($currency === 'CDF') ? $product->price_cdf : $product->price_usd;
                $total += $price;
                $items[] = array(
                    'product_id' => $product_id,
                    'price' => $price,
                );
            }
        }
        
        if ($total <= 0) {
            wp_send_json_error(array('message' => __('Montant invalide.', 'twiga-commerce-donation')));
        }
        
        // Créer la commande
        $order_number = 'ORDER_' . time() . '_' . wp_rand(1000, 9999);
        $table_orders = $wpdb->prefix . 'twigapaie_orders';
        
        $inserted = $wpdb->insert($table_orders, array(
            'order_number' => $order_number,
            'customer_name' => $customer_name,
            'customer_email' => $customer_email,
            'customer_phone' => $customer_phone,
            'total_amount' => $total,
            'currency' => $currency,
            'payment_method' => $payment_method,
            'payment_status' => 'pending',
            'items' => json_encode($items),
        ));
        
        if (!$inserted) {
            wp_send_json_error(array('message' => __('Erreur lors de la création de la commande.', 'twiga-commerce-donation')));
        }
        
        $order_id = $wpdb->insert_id;
        
        // Vider le panier
        $_SESSION['twigapaie_cart'] = array();
        
        // Initier le paiement
        if ($payment_method === 'emoney' && !empty($customer_phone)) {
            $this->initiate_order_emoney_payment($order_id, $customer_phone, $total, $currency, $order_number);
        } elseif ($payment_method === 'ecard') {
            $this->initiate_order_card_payment($order_id, $total, $currency, $order_number);
        }
    }
    
    /**
     * Initier un paiement E-Money pour une commande
     */
    private function initiate_order_emoney_payment($order_id, $phone, $amount, $currency, $order_number) {
        global $wpdb;
        
        try {
            $phone_data = TwigaPaie_Phone_Formatter::format_phone_and_deduce_provider($phone);
            
            $api = new TwigaPaie_API();
            $result = $api->initiate_emoney_payment(
                $phone_data['formatted_phone'],
                $amount,
                $currency,
                $order_number
            );
            
            if ($result['success']) {
                $twigapaie_order_id = isset($result['data']['order_id']) ? $result['data']['order_id'] : '';
                
                $wpdb->update(
                    $wpdb->prefix . 'twigapaie_orders',
                    array(
                        'twigapaie_order_id' => $twigapaie_order_id,
                        'provider_id' => $phone_data['provider_id'],
                        'provider_name' => $phone_data['provider_name'],
                        'payment_status' => 'processing',
                    ),
                    array('id' => $order_id)
                );
                
                wp_send_json_success(array(
                    'message' => __('Commande créée. Veuillez vérifier votre téléphone pour confirmer le paiement.', 'twiga-commerce-donation'),
                    'provider' => $phone_data['provider_name'],
                ));
            } else {
                wp_send_json_error(array(
                    'message' => isset($result['error']) ? $result['error'] : __('Erreur lors de l\'initiation du paiement.', 'twiga-commerce-donation'),
                ));
            }
        } catch (Exception $e) {
            wp_send_json_error(array('message' => $e->getMessage()));
        }
    }
    
    /**
     * Initier un paiement par carte pour une commande
     */
    private function initiate_order_card_payment($order_id, $amount, $currency, $order_number) {
        $api = new TwigaPaie_API();
        
        $site_url = get_site_url();
        $result = $api->initiate_card_payment(
            $amount,
            $currency,
            'Commande ' . $order_number,
            $site_url . '/?twigapaie_webhook=1',
            $site_url . '/?twigapaie_success=1&order_id=' . $order_id,
            $site_url . '/?twigapaie_cancel=1',
            $site_url . '/?twigapaie_decline=1'
        );
        
        if ($result['success'] && isset($result['data']['url'])) {
            global $wpdb;
            $wpdb->update(
                $wpdb->prefix . 'twigapaie_orders',
                array(
                    'twigapaie_order_id' => $result['data']['orderNumber'],
                    'payment_status' => 'processing',
                ),
                array('id' => $order_id)
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
