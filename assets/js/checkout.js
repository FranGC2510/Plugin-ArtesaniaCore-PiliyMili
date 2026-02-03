jQuery(document).ready(function($){
    var nifField = $('.billing-nif-field');
    var checkbox = $('#billing_wants_invoice');

    // Estado inicial
    if ( ! checkbox.is(':checked') ) {
        nifField.hide();
    }

    // Evento cambio
    checkbox.change(function(){
        if ( $(this).is(':checked') ) {
            nifField.slideDown();
        } else {
            nifField.slideUp();
            $('#billing_nif').val(''); // Limpiar valor al ocultar
        }
    });
});