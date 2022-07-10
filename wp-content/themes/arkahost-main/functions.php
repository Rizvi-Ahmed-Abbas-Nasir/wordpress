<?php

/*
*
*	(c) king-theme.com
*
*/

###Load core of theme###
function king_incl_core( $file ){
	
	$path = trailingslashit( get_template_directory() ).$file;
	
	if( file_exists( $path ) ){
		require_once( $path );
	}else{
		wp_die( 'Could not load theme file: '.$path );
	}
	
}
king_incl_core( trailingslashit('core').'king.define.php' );
#
#
#	End Load
#
#
add_image_size('single-post-thumbnail', 848, 300, true);

// Disable Gutenburg
add_filter('use_block_editor_for_post', '__return_false');

//remove_action( 'init','cc_whmcs_bridge_header', 30, 1);
