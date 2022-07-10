<?php
/**
 * Wishlist Shortcodes
 */

if ( !defined( 'king_WISHLIST' ) ) { exit; } // Exit if accessed directly

if( !class_exists( 'king_WISHLIST_SHORTCODE' ) ) {
    /**
     * 
     */
    class king_WISHLIST_SHORTCODE {
        /**
         * Print the wishlist HTML.
         */
        static function wishlist( $atts, $content = null ) {
            $atts = shortcode_atts( array(
                'per_page' => 10,
                'pagination' => 'no' 
            ), $atts );
            
            ob_start();
            king_wishlist_get_template( 'wishlist.php', $atts );
            
            return apply_filters( 'king_wishlist_html', ob_get_clean() );
        }
        
        /**
         * Return "Add to Wishlist" button.
         */
        static function add_to_wishlist( $atts, $content = null ) {
            global $product, $king_wishlist;
            
            $html = king_WISHLIST_UI::add_to_wishlist_button( $king_wishlist->get_wishlist_url(), $product->get_type(), $king_wishlist->is_product_in_wishlist( $product->get_id() ) ); 
            
            $html .= king_WISHLIST_UI::popup_message();
            
            return $html;
        }
    }
}
global $king;
$king->ext['asc']( 'king_wishlist', array( 'king_WISHLIST_SHORTCODE', 'wishlist' ) );
$king->ext['asc']( 'king_add_to_wishlist', array( 'king_WISHLIST_SHORTCODE', 'add_to_wishlist' ) );