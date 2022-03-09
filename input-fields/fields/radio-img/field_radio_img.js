/*
 *
 * anony_Options_radio_img function
 * Changes the radio select option, and changes class on images
 *
 */

function anony_radio_img_select(relid, labelclass){
	jQuery( this ).prev( 'input[type="radio"]' ).prop( 'checked' );
	jQuery( '.anony-radio-img-' + labelclass ).removeClass( 'anony-radio-img-selected' );
	jQuery( 'label[for="' + relid + '"]' ).addClass( 'anony-radio-img-selected' );
}
