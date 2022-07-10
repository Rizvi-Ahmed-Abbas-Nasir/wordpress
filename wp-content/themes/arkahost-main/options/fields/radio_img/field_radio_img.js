/*
 *
 * king_options_radio_img function
 * Changes the radio select option, and changes class on images
 *
 */
function king_radio_img_select(relid, labelclass){
	jQuery(this).prev('input[type="radio"]').prop('checked');

	jQuery('.nhp-radio-img-'+labelclass).removeClass('nhp-radio-img-selected');	
	
	jQuery('label[for="'+relid+'"]').addClass('nhp-radio-img-selected');
}//function

jQuery( document ).ready(function( $ ){
	$('fieldset.radio-img').each(function(){
		$(this).find('label').each(function(){
			if( $(this).hasClass('nhp-radio-img-selected') ){
				$(this).closest('fieldset.radio-img').prepend(this);
			}
		});
	});
});