<?php
/**
 * Shortcodes
 */
 
if ( !defined( 'king_WISHLIST' ) ) { exit; } // Exit if accessed directly

if( !class_exists( 'king_WISHLIST_UI' ) ) {
    class king_WISHLIST_UI {
        
        /**
         * Build the popup message HTML/jQuery.
         */
        public static function popup_message() {
            ob_start() ?>
            <script type="text/javascript">
            if( !jQuery( '#king-wishlist-popup-message' ).length ) {
                jQuery( 'body' ).prepend(
                    '<div id="king-wishlist-popup-message" style="display:none;">' +
                        '<div id="king-wishlist-message"></div>' +
                    '</div>'
                );
            }
            </script>
            <?php
            return ob_get_clean();
        }
        
        /**
         * Build the "Add to Wishlist" HTML
         */
        public static function add_to_wishlist_button( $url, $product_type, $exists ) { 
          
            global $king_wishlist, $product, $king;
        
            $icon = '<i class="fas fa-heart"></i>';         
            $label = apply_filters( 'king_wishlist_button_label', $king->cfg['wl_w_label'] );
            $classes = 'class="add_to_wishlist"';
            $html  = '<div class="king-wishlist-add-to-wishlist">'; 
                $html .= '<div class="king-wishlist-add-button';  // the class attribute is closed in the next row
                
                $html .= $exists ? ' disappear" style="display:none;"' : ' displaying"';
                
                $html .= '><a href="' . esc_url( $king_wishlist->get_addtowishlist_url() ) . '" data-product-id="' . $product->get_id() . '" data-product-type="' . $product_type . '" ' . $classes . ' >' . $icon . $label . '</a>';
                $html .= '<img src="' . esc_url( THEME_URI.'/assets/images/loading.gif' ) . '" class="ajax-loading" id="add-items-ajax-loading" alt="ArkaHost" width="16" height="16" style="visibility:hidden" />';
                $html .= '</div>';
            
            $html .= '<div class="king-wishlist-wishlistaddedbrowse disappear" style="display:none;"><span class="feedback">' . __( 'Product added!','arkahost' ) . '</span> <a href="' . esc_url( $url ) . '">' . apply_filters( 'king-wishlist-browse-wishlist-label', __( 'Browse Wishlist', 'arkahost' ) ) . '</a></div>';
            $html .= '<div class="king-wishlist-wishlistexistsbrowse ' . ( $exists ? 'displaying' : 'disappear' ) . '" style="display:' . ( $exists ? 'block' : 'none' ) . '"><span class="feedback">' . __( 'This product was added to wishlist!', 'arkahost' ) . '</span> <a href="' . esc_url( $url ) . '">' . apply_filters( 'king-wishlist-browse-wishlist-label', __( 'Go to Wishlist', 'arkahost' ) ) . '</a></div>';
            $html .= '<div class="king-wishlist-wishlistaddresponse"></div>';
            
            $html .= '</div>';
            
            return $html;
        }
        
        /**
         * Build the "Add to cart" HTML.
         */
        public static function add_to_cart_button( $product_id, $stock_status ) {
            
            global $king_wishlist, $king;
            
            $icon = '<i class="fas fa-shopping-cart"></i> ';
            
            if ( function_exists( 'get_product' ) )
                $product = get_product( $product_id );
            else
                $product = new WC_Product( $product_id );

            $url = $product->get_type() == 'external' ? $king_wishlist->get_affiliate_product_url( $product_id ) : $king_wishlist->get_addtocart_url( $product_id );
    		$label = $product->get_type() == 'variable' ? apply_filters( 'variable_add_to_cart_text', __('Select options', 'arkahost') ) : apply_filters( 'king_wishlist_add_to_cart_label', $king->cfg['wl_label'] );
            
            
    		$cartlink = '';
    		$redirect_to_cart = $king->cfg['wl_redirect'] == '1' && $product->get_type() != 'variable' ? 'true' : 'false';
    		$style = ''; 
                if( $product->get_type() == 'external' ) {
                    $cartlink .= '<a target="_blank" class="add_to_cart button alt" href="' . $url . '">' . $icon . $label . '</a>';
                } else {
                    $cartlink .= '<a class="add_to_cart button alt" href="javascript:void(0);" onclick="check_for_stock(\'' . $url . '\',\'' . $stock_status . '\',\'' . $redirect_to_cart . '\');">' . $icon . $label . '</a>';
                }
            return $cartlink;
    	}
        
        /**
         * Build share HTML.
         */
        public static function get_share_links( $url ) {
			
			global $king;
			
            $normal_url = $url;
            $url = urlencode( $url );
            $title = urlencode( $king->cfg['wl_stitle'] );
            $twitter_summary = str_replace( '%wishlist_url%', '', $king->cfg['wl_stext'] );
            $summary = urlencode( str_replace( '%wishlist_url%', $normal_url, $king->cfg['wl_stext'] ) );
            $imageurl = urlencode( $king->cfg['wl_simage'] );
            
            $html  = '<div class="sharepost woo-social-share">';
            $html .= apply_filters( 'king_wishlist_socials_share_title', '<h4>' . __( 'Share on:', 'arkahost' ) . '</h4>' );
                $html .= '<ul>';
                
                if( $king->cfg['wl_facebook'] == 1 )
                    { $html .= '<li class="globalBgColor"><a target="_blank" href="https://www.facebook.com/sharer.php?s=100&amp;p[title]=' . $title . '&amp;p[url]=' . $url . '&amp;p[summary]=' . $summary . '&amp;p[images][0]=' . $imageurl . '" title="' . __( 'Facebook', 'arkahost' ) . '">&nbsp;<i class="fab fa-facebook-f fa-lg"></i>&nbsp;</a></li>'; }
                
                if( $king->cfg['wl_twitter'] == 1 )
                    { $html .= '<li class="globalBgColor"><a target="_blank" href="https://twitter.com/share?url=' . $url . '&amp;text=' . $twitter_summary . '" title="' . __( 'Twitter', 'arkahost' ) . '"><i class="fab fa-twitter fa-lg"></i></a></li>'; }
                if( $king->cfg['wl_google'] == 1 )
                    { $html .= '<li class="globalBgColor"><a target="_blank" href="https://plus.google.com/share?url=' . $url . '&amp;title="' . $title . '" onclick=\'javascript:window.open(this.href, "", "menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600");return false;\'><i class="fab fa-google-plus-g fa-lg"></i></a></li>'; }
					
				 if( $king->cfg['wl_pinterest'] == 1 )
                    { $html .= '<li class="globalBgColor"><a target="_blank" href="http://pinterest.com/pin/create/button/?url=' . $url . '&amp;description=' . $summary . '&media=' . $imageurl . '" onclick="window.open(this.href); return false;"><i class="fab fa-pinterest fa-lg"></i></a></li>'; }	
                
                $html .= '</ul>';
            $html .= '</div>';	
            
            return $html;			
    	}
    }
}