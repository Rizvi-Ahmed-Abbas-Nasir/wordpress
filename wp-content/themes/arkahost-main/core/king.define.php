<?php
/**
*
* (C) King-Theme.com
*
*/

/********************************************************/
/*                        Actions                       */
/********************************************************/

	// Constants

	$theme = wp_get_theme();
	if( !empty( $theme['Template'] ) ){
		$theme = wp_get_theme($theme['Template']);
	}
	define('THEME_NAME', $theme['Name'] );
	define('THEME_SLUG', $theme['Template'] );
	define('KING_VERSION', $theme['Version'] );
	if( !defined("DS"))
		define( 'DS', DIRECTORY_SEPARATOR );

	define('HOME_URL', home_url() );
	define('SITE_URI', site_url() );
	define('THEME_URI', get_template_directory_uri() );
	define('THEME_PATH', get_template_directory() );
	define('THEME_CPATH', get_template_directory().DS.'core'.DS );
	define('THEME_SPATH', get_stylesheet_directory() );
	define('KING_OPTNAME', 'king');
	define('KING_ARKAHOST', 'http://arkahost.com');

	if( !class_exists( 'king' ) ){
		king_incl_core( 'core'.DS.'king.class.php' );
	}

	### Start Run FrameWork ###
	global $king;
	$king = new king();
	$king->init();
	### End FrameWork ###
