<?php
/**
 * Install file
 */

if ( !defined( 'king_WISHLIST' ) ) { exit; } // Exit if accessed directly

if( !function_exists( 'king_wishlist_locate_template' ) ) {
    /**
     * Locate the templates and return the path of the file found
     */
    function king_wishlist_locate_template( $path, $var = NULL ){
    	$template_woocommerce_path = '/woocommerce/' . $path;
        $template_path = '/' . $path;
    	
    	$located = locate_template( array(
            $template_woocommerce_path, // Search in <theme>/woocommerce/
            $template_path              // Search in <theme>/
        ) );
        
        if( !$located )
            { $located = WISHLIST_DIR . 'templates/' . $path; }
                               
        return $located;
    }
}

if( !function_exists( 'king_wishlist_get_template' ) ) {
    /**
     * Retrieve a template file.
     */
    function king_wishlist_get_template( $path, $var = null, $return = false ) {
    	
    	global $king;
        $located = king_wishlist_locate_template( $path, $var );      
        
        if ( $var && is_array( $var ) ) 
    		extract( $var );
                               
        if( $return )
            { ob_start(); }   
                                                                     
        // include file located
        $king->ext['icl']( $located );
        
        if( $return )
            { return ob_get_clean(); }
    }
}

if( !function_exists( 'king_wishlist_count_products' ) ) {
    /**
     * Retrieve the number of products in the wishlist.
     */
    function king_wishlist_count_products() {
        global $king_wishlist;
        return $king_wishlist->count_products();
    }
}

if( !function_exists( 'king_frontend_css_color_picker' ) ) {
    /**
     * Output a colour picker input box.

     */
    function king_frontend_css_color_picker( $name, $id, $value, $desc = '' ) {
    	
    	global $woocommerce;
    
    	echo '<div class="color_box"><strong>' . $name . '</strong>
       		<input name="' . esc_attr( $id ). '" id="' . $id . '" type="text" value="' . esc_attr( $value ) . '" class="colorpick" /> <div id="colorPickerDiv_' . esc_attr( $id ) . '" class="colorpickdiv"></div>
        </div>';
    
    }
}

if( !function_exists( 'king_setcookie' ) ) {
    /**
     * Create a cookie.
     */
    function king_setcookie( $name, $value = array(), $time = null ) {
        $time = $time != null ? $time : time() + 60 * 60 * 24 * 30;
        
        $value = maybe_serialize( stripslashes_deep( $value ) );
        $expiration = apply_filters( 'king_wishlist_cookie_expiration_time', $time ); // Default 30 days
        
        return setcookie( $name, $value, $expiration, '/' );
    }
}

if( !function_exists( 'king_getcookie' ) ) {
    /**
     * Retrieve the value of a cookie.
     */
    function king_getcookie( $name ) {
        if( isset( $_COOKIE[$name] ) )
            { return maybe_unserialize( stripslashes( $_COOKIE[$name] ) ); }
        
        return array();
    }
}

if( !function_exists( 'king_usecookies' ) ) {
    /**
     * Check if the user want to use cookies or not.
     */
    function king_usecookies() {
        global $king;
		$king->cfg['wl_cookies'] = 1;
		return true;
    }
}

if( !function_exists ( 'king_destroycookie' ) ) {
    /**
     * Destroy a cookie.
     */
    function king_destroycookie( $name ) {
        king_setcookie( $name, array(), time() - 3600 );
    }
}