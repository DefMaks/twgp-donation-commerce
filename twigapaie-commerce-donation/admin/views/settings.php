<?php
if (!defined('ABSPATH')) {
    exit;
}

// Sauvegarder les paramètres
if (isset($_POST['twigapaie_save_settings'])) {
    check_admin_referer('twigapaie_settings_nonce');
    
    update_option('twigapaie_api_key', sanitize_text_field($_POST['twigapaie_api_key']));
    update_option('twigapaie_supabase_url', esc_url_raw($_POST['twigapaie_supabase_url']));
    update_option('twigapaie_supabase_key', sanitize_text_field($_POST['twigapaie_supabase_key']));
    update_option('twigapaie_currency', sanitize_text_field($_POST['twigapaie_currency']));
    update_option('twigapaie_aggregator_fee', floatval($_POST['twigapaie_aggregator_fee']));
    update_option('twigapaie_defmaks_fee', floatval($_POST['twigapaie_defmaks_fee']));
    update_option('twigapaie_test_mode', isset($_POST['twigapaie_test_mode']) ? 1 : 0);
    
    echo '<div class="notice notice-success is-dismissible"><p>' . __('Paramètres sauvegardés avec succès !', 'twiga-commerce-donation') . '</p></div>';
}

$api_key = get_option('twigapaie_api_key', '');
$supabase_url = get_option('twigapaie_supabase_url', '');
$supabase_key = get_option('twigapaie_supabase_key', '');
$currency = get_option('twigapaie_currency', 'CDF');
$aggregator_fee = get_option('twigapaie_aggregator_fee', 2.5);
$defmaks_fee = get_option('twigapaie_defmaks_fee', 3.5);
$test_mode = get_option('twigapaie_test_mode', true);

?>

<div class="wrap twigapaie-settings">
    <h1><?php _e('Paramètres TwigaPaie', 'twiga-commerce-donation'); ?></h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('twigapaie_settings_nonce'); ?>
        
        <div class="twigapaie-settings-section">
            <h2><?php _e('Configuration API TwigaPaie', 'twiga-commerce-donation'); ?></h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="twigapaie_api_key"><?php _e('Clé API TwigaPaie', 'twiga-commerce-donation'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="twigapaie_api_key" name="twigapaie_api_key" value="<?php echo esc_attr($api_key); ?>" class="regular-text" />
                        <p class="description"><?php _e('Votre clé API TwigaPaie (Authorization Bearer)', 'twiga-commerce-donation'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="twigapaie_test_mode"><?php _e('Mode test', 'twiga-commerce-donation'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" id="twigapaie_test_mode" name="twigapaie_test_mode" value="1" <?php checked($test_mode, 1); ?> />
                            <?php _e('Activer le mode test', 'twiga-commerce-donation'); ?>
                        </label>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="twigapaie-settings-section">
            <h2><?php _e('Configuration Supabase', 'twiga-commerce-donation'); ?></h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="twigapaie_supabase_url"><?php _e('URL Supabase', 'twiga-commerce-donation'); ?></label>
                    </th>
                    <td>
                        <input type="url" id="twigapaie_supabase_url" name="twigapaie_supabase_url" value="<?php echo esc_attr($supabase_url); ?>" class="regular-text" />
                        <p class="description"><?php _e('Exemple: https://hcpogyjdbtcxndzpyjvd.supabase.co', 'twiga-commerce-donation'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="twigapaie_supabase_key"><?php _e('Clé Anon Supabase', 'twiga-commerce-donation'); ?></label>
                    </th>
                    <td>
                        <textarea id="twigapaie_supabase_key" name="twigapaie_supabase_key" rows="3" class="large-text"><?php echo esc_textarea($supabase_key); ?></textarea>
                        <p class="description"><?php _e('Votre clé publique (anon) Supabase', 'twiga-commerce-donation'); ?></p>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="twigapaie-settings-section">
            <h2><?php _e('Configuration des frais', 'twiga-commerce-donation'); ?></h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="twigapaie_currency"><?php _e('Devise par défaut', 'twiga-commerce-donation'); ?></label>
                    </th>
                    <td>
                        <select id="twigapaie_currency" name="twigapaie_currency">
                            <option value="CDF" <?php selected($currency, 'CDF'); ?>>CDF - Franc Congolais</option>
                            <option value="USD" <?php selected($currency, 'USD'); ?>>USD - Dollar Américain</option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="twigapaie_aggregator_fee"><?php _e('Frais agrégateur (%)', 'twiga-commerce-donation'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="twigapaie_aggregator_fee" name="twigapaie_aggregator_fee" value="<?php echo esc_attr($aggregator_fee); ?>" step="0.1" min="0" max="100" class="small-text" /> %
                        <p class="description"><?php _e('Par défaut: 2.5%', 'twiga-commerce-donation'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="twigapaie_defmaks_fee"><?php _e('Frais DefMaks (%)', 'twiga-commerce-donation'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="twigapaie_defmaks_fee" name="twigapaie_defmaks_fee" value="<?php echo esc_attr($defmaks_fee); ?>" step="0.1" min="0" max="100" class="small-text" /> %
                        <p class="description"><?php _e('Par défaut: 3.5%', 'twiga-commerce-donation'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <?php _e('Total des frais', 'twiga-commerce-donation'); ?>
                    </th>
                    <td>
                        <strong><?php echo number_format($aggregator_fee + $defmaks_fee, 1); ?>%</strong>
                        <p class="description"><?php _e('Frais total prélevé sur chaque transaction', 'twiga-commerce-donation'); ?></p>
                    </td>
                </tr>
            </table>
        </div>
        
        <p class="submit">
            <button type="submit" name="twigapaie_save_settings" class="button button-primary">
                <?php _e('Enregistrer les paramètres', 'twiga-commerce-donation'); ?>
            </button>
        </p>
    </form>
    
    <div class="twigapaie-settings-section">
        <h2><?php _e('Shortcodes disponibles', 'twiga-commerce-donation'); ?></h2>
        
        <div class="twigapaie-shortcodes">
            <div class="twigapaie-shortcode-item">
                <code>[twigapaie_donation_form campaign_id="123"]</code>
                <p><?php _e('Afficher un formulaire de donation pour une campagne spécifique', 'twiga-commerce-donation'); ?></p>
            </div>
            
            <div class="twigapaie-shortcode-item">
                <code>[twigapaie_products limit="12"]</code>
                <p><?php _e('Afficher une grille de produits', 'twiga-commerce-donation'); ?></p>
            </div>
            
            <div class="twigapaie-shortcode-item">
                <code>[twigapaie_cart]</code>
                <p><?php _e('Afficher le panier d\'achat', 'twiga-commerce-donation'); ?></p>
            </div>
            
            <div class="twigapaie-shortcode-item">
                <code>[twigapaie_checkout]</code>
                <p><?php _e('Afficher la page de paiement', 'twiga-commerce-donation'); ?></p>
            </div>
        </div>
    </div>
</div>
