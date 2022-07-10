jQuery( function( $ ) {	
	// wc_cart_fragments_params is required to continue, ensure the object exists
	if ( typeof wc_cart_fragments_params === 'undefined' ) {
		return false;
	}
	
	$fragment_refresh = {
		url: wc_cart_fragments_params.ajax_url,
		type: 'POST',
		data: { action: 'woocommerce_get_refreshed_fragments' },
		success: function( data ) {
			if ( data && data.fragments ) {

				$.each( data.fragments, function( key, value ) {
					$( key ).replaceWith( value );
				});
			
				$( 'body' ).trigger( 'wc_fragments_refreshed' );
			}
		}
	};

	$.ajax( $fragment_refresh );
	
});
