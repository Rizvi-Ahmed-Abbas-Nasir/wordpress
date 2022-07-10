<?php
// DEVN WISHLIST STARTUP
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

define( 'king_WISHLIST', true );
    if( !defined( 'king_WISHLIST_URL' ) ) { define( 'king_WISHLIST_URL', get_template_directory_uri() . '/core/woocommerce' ); }
    define( 'WISHLIST_URL', king_WISHLIST_URL . '/wishlist/' );
    define( 'WISHLIST_DIR', dirname( __FILE__ ) . '/' );

global $woocommerce;


if( isset($woocommerce) ) {
    // Load necessary files
    locate_template( 'core'.DS.'woocommerce'.DS.'wishlist'.DS.'functions-wishlist.php', true );
    locate_template( 'core'.DS.'woocommerce'.DS.'wishlist'.DS.'wishlist.php', true );
    locate_template( 'core'.DS.'woocommerce'.DS.'wishlist'.DS.'wishlist-init.php', true );
    locate_template( 'core'.DS.'woocommerce'.DS.'wishlist'.DS.'wishlist-install.php', true );
    
    if( king_wishlist_actived() ) {
        locate_template( 'core'.DS.'woocommerce'.DS.'wishlist'.DS.'wishlist-ui.php', true );
        locate_template( 'core'.DS.'woocommerce'.DS.'wishlist'.DS.'wishlist-shco.php', true );
    }
    
    // ============
    global $king_wishlist;
    $king_wishlist = new king_WISHLIST( $_REQUEST );
}