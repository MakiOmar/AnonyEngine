/*
 *
 * anony_radio_select function
 * Changes the radio select option, and changes class on label
 *
 */

function anony_radio_select(relid, labelclass){
	jQuery(this).prev('input[type="radio"]').prop('checked');
	jQuery('.anony-radio-'+labelclass).removeClass('anony-radio-img-selected');	
	jQuery('label[for="'+relid+'"]').addClass('anony-radio-img-selected');
}// JavaScript Document