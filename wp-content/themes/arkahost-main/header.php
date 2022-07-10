<?php
/**
 *
 * (c) king-theme.com
 *
 * The Header of theme.
 *
 */
 
 
global $king;

if ( ! isset( $content_width ) ) $content_width = 1170;

?><!DOCTYPE HTML>
<html <?php language_attributes(); ?>>
<head>
<?php 
	wp_head();
?>
</head>
<body <?php body_class('bg-cover'); ?>>
	<div id="main" class="layout-<?php 	
		$mclass =  $king->cfg['layout']; 
		if( !empty( $post->post_name ) ){
			if( $post->post_type == 'page' )$mclass .= ' page-'.$post->post_name; 
		}
		echo esc_attr( $mclass ); 
		echo ' '.esc_attr( $king->main_class ); ?> site_wrapper">
	<?php

		/**
		* Load header path, change header via theme panel, files location themes/aloom/templates/header/
		*/
		
		king::path( 'header' ); 
		
	?>
	
