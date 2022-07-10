<?php
/**
 * Add product from the wishlist to the cart.
 *
 */
 
// Handles all ajax requests pertaining to this plugin

global $woocommerce, $king_wishlist, $king;

locate_template( 'core'.DS.'woocomerce'.DS.'wishlist'.DS.'load.php', true );
locate_template( 'core'.DS.'woocomerce'.DS.'wishlist'.DS.'functions-wishlist.php', true );

//determine error link redirect url
$error_link_url = $king_wishlist->get_wishlist_url();

//determine to success link redirect url
//handle redirect option chosen by admin
if( isset( $_GET['redirect_to_cart'] ) && $_GET['redirect_to_cart'] == 'true' )
    { $redirect_url = $woocommerce->cart->get_cart_url(); }
else
    { $redirect_url = $king_wishlist->get_wishlist_url(); }

//get the details of the product
$details = $king_wishlist->get_product_details( $_GET['wishlist_item_id'] );

//add to the cart
if( $woocommerce->cart->add_to_cart( $details[0]['prod_id'], 1 ) ) {
    if(function_exists('wc_add_to_cart_message')){
        wc_add_to_cart_message( $details[0]['prod_id'] );
    }else{
        woocommerce_add_to_cart_message( $details[0]['prod_id'] );
    }

    if( $king->cfg['wl_remove'] == 1 )
        { $king_wishlist->remove( $details[0]['ID'] ); }
    
	header( "Location: $redirect_url" );
	
} else { //if failed, redirect to wishlist page with errors
    if(function_exists('wc_get_notices')){
        $_SESSION['errors'] = wc_get_notices('error');
    }else{
        $_SESSION['errors'] = $woocommerce->get_errors();
    }
	header( "Location: $error_link_url" );
}