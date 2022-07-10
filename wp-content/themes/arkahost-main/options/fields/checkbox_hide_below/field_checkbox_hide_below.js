jQuery(document).ready(function(){
	
	jQuery('.nhp-opts-checkbox-hide-below').each(function(){
		if(!jQuery(this).is(':checked')){
			jQuery(this).closest('tr').next('tr').hide();
		}
	});
	
	jQuery('.nhp-opts-checkbox-hide-below').on( 'click', function(){
		jQuery(this).closest('tr').next('tr').fadeToggle('slow');
	});
	
});