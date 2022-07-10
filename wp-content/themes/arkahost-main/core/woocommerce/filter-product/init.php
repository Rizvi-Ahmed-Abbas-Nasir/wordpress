<?php
/**
* Init
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

    define( 'king_filter_URL', get_template_directory_uri() . '/core/woocommerce/filter-product/' );
    define( 'king_filter_DIR', get_template_directory_uri() . '/core/woocommerce/filter-product/' );
	
	global $king_filter;
	
	$path = 'core'.DS.'woocommerce'.DS.'filter-product'.DS;

    locate_template( $path.'functions.king-filter.php', true );
    locate_template( $path.'king-filter-admin.php', true );
    locate_template( $path.'king-filter-frontend.php', true );
    locate_template( $path.'king-filter-helper.php', true );
    locate_template( $path.'widgets'.DS.'king-filter-widget.php', true );
    locate_template( $path.'widgets'.DS.'king-filter-reset-widget.php', true );
    locate_template( $path.'king-filter.php', true );

    $king_filter = new king_filter();