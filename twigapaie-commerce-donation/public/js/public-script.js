jQuery(document).ready(function($) {
    'use strict';
    
    // Gérer les boutons de montant prédéfinis
    $('.twigapaie-amount-btn').on('click', function() {
        $('.twigapaie-amount-btn').removeClass('active');
        $(this).addClass('active');
        $('#donation-amount').val($(this).data('amount'));
    });
    
    // Désactiver les boutons prédéfinis si on saisit un montant personnalisé
    $('#donation-amount').on('input', function() {
        if ($(this).val()) {
            $('.twigapaie-amount-btn').removeClass('active');
        }
    });
    
    // Soumettre le formulaire de donation
    $('#twigapaie-donation-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $message = $form.find('.twigapaie-form-message');
        var $button = $form.find('button[type="submit"]');
        
        // Désactiver le bouton
        $button.prop('disabled', true).addClass('twigapaie-loading');
        $message.hide();
        
        var formData = {
            action: 'twigapaie_process_donation',
            nonce: twigapaieData.nonce,
            campaign_id: $form.find('input[name="campaign_id"]').val(),
            amount: $form.find('input[name="amount"]').val(),
            currency: $form.find('input[name="currency"]').val(),
            payment_method: $form.find('input[name="payment_method"]:checked').val(),
            donor_name: $form.find('input[name="donor_name"]').val(),
            donor_email: $form.find('input[name="donor_email"]').val(),
            donor_phone: $form.find('input[name="donor_phone"]').val(),
        };
        
        $.ajax({
            url: twigapaieData.ajaxUrl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    if (response.data.redirect_url) {
                        // Redirection pour paiement par carte
                        window.location.href = response.data.redirect_url;
                    } else {
                        // Afficher le message de succès pour E-Money
                        $message
                            .removeClass('error')
                            .addClass('success')
                            .html('<strong>' + twigapaieData.strings.processing + '</strong><br>' + response.data.message)
                            .show();
                        
                        // Réinitialiser le formulaire après 3 secondes
                        setTimeout(function() {
                            $form[0].reset();
                            $message.fadeOut();
                        }, 5000);
                    }
                } else {
                    $message
                        .removeClass('success')
                        .addClass('error')
                        .text(response.data.message || twigapaieData.strings.error)
                        .show();
                }
            },
            error: function() {
                $message
                    .removeClass('success')
                    .addClass('error')
                    .text(twigapaieData.strings.error)
                    .show();
            },
            complete: function() {
                $button.prop('disabled', false).removeClass('twigapaie-loading');
            }
        });
    });
    
    // Ajouter au panier
    $('.twigapaie-add-to-cart').on('click', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var productId = $button.data('product-id');
        
        $button.prop('disabled', true).text('Ajout...');
        
        $.ajax({
            url: twigapaieData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'twigapaie_add_to_cart',
                nonce: twigapaieData.nonce,
                product_id: productId,
            },
            success: function(response) {
                if (response.success) {
                    $button.text('✓ Ajouté !').addClass('success');
                    
                    // Mettre à jour le compteur du panier si existant
                    $('.twigapaie-cart-count').text(response.data.cart_count);
                    
                    setTimeout(function() {
                        $button.prop('disabled', false).text('Ajouter au panier').removeClass('success');
                    }, 2000);
                } else {
                    alert(response.data.message || twigapaieData.strings.error);
                    $button.prop('disabled', false).text('Ajouter au panier');
                }
            },
            error: function() {
                alert(twigapaieData.strings.error);
                $button.prop('disabled', false).text('Ajouter au panier');
            }
        });
    });
    
    // Retirer du panier
    $('.twigapaie-remove-from-cart').on('click', function() {
        var $button = $(this);
        var productId = $button.data('product-id');
        var $item = $button.closest('.twigapaie-cart-item');
        
        if (!confirm('Voulez-vous retirer ce produit du panier ?')) {
            return;
        }
        
        $.ajax({
            url: twigapaieData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'twigapaie_remove_from_cart',
                nonce: twigapaieData.nonce,
                product_id: productId,
            },
            success: function(response) {
                if (response.success) {
                    $item.fadeOut(function() {
                        $(this).remove();
                        
                        // Recharger si le panier est vide
                        if ($('.twigapaie-cart-item').length === 0) {
                            location.reload();
                        } else {
                            // Recalculer le total
                            updateCartTotal();
                        }
                    });
                }
            }
        });
    });
    
    // Soumettre le checkout
    $('#twigapaie-checkout-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $message = $form.find('.twigapaie-form-message');
        var $button = $form.find('button[type="submit"]');
        
        $button.prop('disabled', true).addClass('twigapaie-loading');
        $message.hide();
        
        var formData = {
            action: 'twigapaie_process_checkout',
            nonce: twigapaieData.nonce,
            customer_name: $form.find('input[name="customer_name"]').val(),
            customer_email: $form.find('input[name="customer_email"]').val(),
            customer_phone: $form.find('input[name="customer_phone"]').val(),
            currency: $form.find('input[name="currency"]').val(),
            payment_method: $form.find('input[name="payment_method"]:checked').val(),
        };
        
        $.ajax({
            url: twigapaieData.ajaxUrl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    if (response.data.redirect_url) {
                        window.location.href = response.data.redirect_url;
                    } else {
                        $message
                            .removeClass('error')
                            .addClass('success')
                            .text(response.data.message)
                            .show();
                    }
                } else {
                    $message
                        .removeClass('success')
                        .addClass('error')
                        .text(response.data.message || twigapaieData.strings.error)
                        .show();
                }
            },
            error: function() {
                $message
                    .removeClass('success')
                    .addClass('error')
                    .text(twigapaieData.strings.error)
                    .show();
            },
            complete: function() {
                $button.prop('disabled', false).removeClass('twigapaie-loading');
            }
        });
    });
    
    // Fonction pour recalculer le total du panier
    function updateCartTotal() {
        var total = 0;
        $('.twigapaie-cart-item').each(function() {
            var price = parseFloat($(this).find('.twigapaie-cart-item-price').text().replace(/[^0-9.]/g, ''));
            if (!isNaN(price)) {
                total += price;
            }
        });
        $('.twigapaie-total-amount').text(total.toFixed(2) + ' ' + twigapaieData.currency);
    }
});
