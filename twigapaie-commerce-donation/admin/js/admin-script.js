jQuery(document).ready(function($) {
    'use strict';
    
    // Gestionnaire de téléchargement de fichiers pour les produits
    $(document).on('click', '#upload_file_button', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var fileFrame;
        
        // Si le media frame existe déjà, l'ouvrir
        if (fileFrame) {
            fileFrame.open();
            return;
        }
        
        // Créer le media frame
        fileFrame = wp.media({
            title: 'Sélectionner un fichier',
            button: {
                text: 'Utiliser ce fichier',
            },
            multiple: false
        });
        
        // Quand un fichier est sélectionné
        fileFrame.on('select', function() {
            var attachment = fileFrame.state().get('selection').first().toJSON();
            $('#product_file_url').val(attachment.url);
            button.text('Fichier sélectionné ✓').css('color', '#00a32a');
            
            setTimeout(function() {
                button.text('Télécharger un fichier').css('color', '');
            }, 3000);
        });
        
        // Ouvrir le media frame
        fileFrame.open();
    });
    
    // Confirmation de suppression
    $('.twigapaie-delete-transaction').on('click', function(e) {
        if (!confirm('Voulez-vous vraiment supprimer cette transaction ?')) {
            e.preventDefault();
            return false;
        }
    });
    
    // Mise à jour automatique du total des frais
    $('#twigapaie_aggregator_fee, #twigapaie_defmaks_fee').on('input', function() {
        var aggregatorFee = parseFloat($('#twigapaie_aggregator_fee').val()) || 0;
        var defmaksFee = parseFloat($('#twigapaie_defmaks_fee').val()) || 0;
        var totalFee = aggregatorFee + defmaksFee;
        
        // Mettre à jour l'affichage si un élément d'affichage existe
        if ($('.total-fee-display').length) {
            $('.total-fee-display').text(totalFee.toFixed(1) + '%');
        }
    });
});
