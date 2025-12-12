jQuery(document).ready(function($) {
    'use strict';
    
    // Gestionnaire de téléchargement de fichiers
    $('#upload_file_button').on('click', function(e) {
        e.preventDefault();
        
        var fileFrame;
        
        if (fileFrame) {
            fileFrame.open();
            return;
        }
        
        fileFrame = wp.media({
            title: 'Sélectionner un fichier',
            button: {
                text: 'Utiliser ce fichier',
            },
            multiple: false
        });
        
        fileFrame.on('select', function() {
            var attachment = fileFrame.state().get('selection').first().toJSON();
            $('#product_file_url').val(attachment.url);
        });
        
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
