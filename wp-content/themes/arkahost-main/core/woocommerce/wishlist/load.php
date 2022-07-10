<?php

/**
 * @author Tarchini Maurizio
 * @copyright 2011
 */

$wlpath = ABSPATH.'wp-content'.DS.'themes'.DS.THEME_SLUG.DS.'core'.DS.'woocommerce'.DS.'wishlist'.DS;

for($i=0; $i<10; $i++)
{                  
	if( file_exists( $wlpath.DS.'wp-load.php') )
	{                      
		locate_template( 'core'.DS.'woocomerce'.DS.'wishlist'.DS.'wp-load.php', true ); 
		break;
	}
	else
	{
		$wp_load = dirname($wp_load);
	}
}

?>