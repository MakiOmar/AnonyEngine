jQuery(document).ready(function(e){

    e('.anony-radio-selected').next('.anony-for-hidden-radio').addClass('anony-hidden-radio-selected');
    e(".anony-for-hidden-radio").click( function(){
        var radioId = e(this).data('id');

        e( '#' + radioId ).prop( 'checked', true );       

        e('input[name="customer_gender"]').trigger('change');
    

        e(".anony-for-hidden-radio").removeClass('anony-hidden-radio-selected');

        e(this).addClass('anony-hidden-radio-selected');
    });
});