<?php
/**
 * Main class
 *
 */

if ( !defined( 'king_WISHLIST' ) ) { exit; } // Exit if accessed directly

if( !class_exists( 'king_WISHLIST' ) ) {    


    class king_WISHLIST {
      
	  
        public $errors;
        
     
        public $details;
    
	
        public $messages;
        
      
        public function __construct( $details ) {              

  
    		//@session_start();
    			
            $this->details = $details;                
            $king_wishlist_init = new king_WISHLIST_INIT();
            
            add_action( 'wp_ajax_add_to_wishlist', array( $this, 'add_to_wishlist_ajax' ) );
            add_action( 'wp_ajax_nopriv_add_to_wishlist', array( $this, 'add_to_wishlist_ajax' ) );
            
            add_action( 'wp_ajax_remove_from_wishlist', array( $this, 'remove_from_wishlist_ajax' ) );
            add_action( 'wp_ajax_nopriv_remove_from_wishlist', array( $this, 'remove_from_wishlist_ajax' ) );
        }
        
        /**
         * Check if the product exists in the wishlist.
         */
        public function is_product_in_wishlist( $product_id ) {
            
            global $wpdb;
                
            $exists = false;
                
    		if( is_user_logged_in() ) {		
    			
    			$sql = "SELECT COUNT(*) as `cnt` FROM `" . king_WISHLIST_TABLE . "` WHERE `prod_id` = " . intval( $product_id ) . " AND `user_id` = " . intval( $this->details['user_id'] );
    			
    			$results = $wpdb->get_results( $sql );
    			$exists = $results[0]->cnt > 0 ? true : false;
    		} else {
                if( king_usecookies() ) {
                    $tmp_products = king_getcookie( 'king_wishlist_products' );
                } elseif( isset( $_SESSION['king_wishlist_products'] ) ) {
                    $tmp_products = $_SESSION['king_wishlist_products'];
                } else {
                    $tmp_products = array();
                }
                
                    if( isset( $tmp_products[ $product_id ] ) )
                        { $exists = 1; }
                    else
                        { $exists = 0; }
    		}
            
            return $exists;
        }
        
        /**
         * Add a product in the wishlist.
         * 
         * @return string "error", "true" or "exists"
         */
        public function add() {
            
            global $wpdb, $woocommerce;
            
            if ( isset( $this->details['add_to_wishlist'] ) && is_numeric( $this->details['add_to_wishlist'] ) ) {
                //single product
                $quantity = ( isset( $this->details['quantity'] ) ) ? ( int ) $this->details['quantity'] : 1;
                
                //check for existence,  product ID, variation ID, variation data, and other cart item data
                if( $this->is_product_in_wishlist( $this->details['add_to_wishlist'] ) ) {
                    return "exists";   
                }
                
                $return = "error";
                
                if( is_user_logged_in() ) {
                    $sql = "INSERT INTO `" . king_WISHLIST_TABLE . "` ( `prod_id`, `quantity`, `user_id`, `dateadded` ) VALUES ( " . $this->details['add_to_wishlist'] . " , $quantity, " . $this->details['user_id'] . ", now() )";
                    $return = $wpdb->query( $sql );
                } elseif( king_usecookies() ) {
                    $cookie[$this->details['add_to_wishlist']]['add-to-wishlist'] = $this->details['add_to_wishlist'];
                    $cookie[$this->details['add_to_wishlist']]['quantity'] = $quantity;
                    
                    $cookie = $cookie + king_getcookie( 'king_wishlist_products' );
                    
                    king_setcookie( 'king_wishlist_products', $cookie );
                    $return = true;  
                } else {
                    $_SESSION['king_wishlist_products'][$this->details['add_to_wishlist']]['add-to-wishlist'] = $this->details['add_to_wishlist'];
				    $_SESSION['king_wishlist_products'][$this->details['add_to_wishlist']]['quantity'] = $quantity;
				    $return = true; 
                }
                
                if( $return ) {
                    return "true";
                } else {
                    $this->errors[] = __( 'Error occurred while adding product to wishlist.', 'arkahost' );
                    return "error";
                }
            } 
        }
        
        /**
         * Remove an entry from the wishlist.
         */
        public function remove( $id ) {
            
            global $wpdb;

            if( is_user_logged_in() ) {
                $sql = "DELETE FROM `" . king_WISHLIST_TABLE . "` WHERE `ID` = " . $id . " AND `user_id` = " . $this->details['user_id'];
                
                if( $wpdb->query( $sql ) ) {
                    return true;
                } else {
                    $this->errors[] = __( 'Error occurred while removing product from wishlist', 'arkahost' );
                    return false;
                }
            } elseif( king_usecookies() ) {
                $cookie = king_getcookie( 'king_wishlist_products' );
                unset( $cookie[$id] );
                
                king_destroycookie( 'king_wishlist_products' );
                king_setcookie( 'king_wishlist_products', $cookie );
                
                return true;
            } else {
                unset( $_SESSION['king_wishlist_products'][$id] );
			    return true;
            }
        }
        
        /**
         * Get all errors in HTML mode or simple string.

         */
        public function get_errors( $html = true ) {
            return implode( ( $html ? '<br />' : ', ' ), $this->errors );
        }
        
        /**
         * Retrieve the number of products in the wishlist.
         */
        public function count_products() {
            
            global $wpdb;
            
            if( is_user_logged_in() ) {
                $sql = "SELECT COUNT(*) as `cnt` FROM `" . king_WISHLIST_TABLE . "` WHERE `user_id` = " . get_current_user_id();
                $results = $wpdb->get_results( $sql );
                return $results[0]->cnt;
            } elseif( king_usecookies() ) {
                $cookie = king_getcookie( 'king_wishlist_products' );
                return count( $cookie );
            } else {
                if( isset( $_SESSION['king_wishlist_products'] ) ) 
        			{ return count( $_SESSION['king_wishlist_products'] ); }
            }
            
            return 0;
        }
        
        /**
         * Retrieve details of a product in the wishlist.
         */
        public function get_product_details( $id ) {
            
            global $wpdb;
            
            if( is_user_logged_in() ) {
                return $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `' . king_WISHLIST_TABLE . '` WHERE `prod_id` = %d', $id ), ARRAY_A );
            } elseif( king_usecookies() ) {
                $cookie = king_getcookie( 'king_wishlist_products' );
                $temp_arr[0] = $cookie[$id];
                $temp_arr[0]['prod_id'] = $id;
    			return $temp_arr;
            } else {
                $temp_arr[0] = $_SESSION['king_wishlist_products'][$id];
    			$temp_arr[0]['prod_id'] = $id;
    			return $temp_arr;
            }
            
            return array();
        }
        
        /**
         * Build wishlist page URL.
         */
        public function get_wishlist_url() {
            return get_permalink( get_option( 'king_wcwl_wishlist_page_id' ) );
        }
        
        /**
         * Build wishlist page URL based on user ID.
         */
        public function get_public_wishlist_url( $user_id ) {
            return home_url() . '/?id=' . $user_id . '&page_id=' . $page_id;
        }
        
        /**
         * Build the URL used to remove an item from the wishlist.
         */
        public function get_remove_url( $item_id ) {
            return admin_url( 'admin-ajax.php?wishlist_item_id=' . $item_id );
             
        }
        
        /**
         * Build the URL used to add an item in the wishlist.
         */
        public function get_addtowishlist_url() {
            
            global $product;
            	
            $url = WISHLIST_URL . "ajax.php?action=add_to_wishlist&add_to_wishlist=" . $product->get_id();
            
            return $url;
        }
        
        /**
         * Build the URL used to add an item to the cart from the wishlist.
         */
        public function get_addtocart_url( $id, $user_id = '' ) {
            
            global $king_wishlist;
            
            
            if ( function_exists( 'get_product' ) )    
                $product = get_product( $id );
            else
                $product = new WC_Product( $id );
                
            if ( $product->get_type() == 'variable' ) {
                return get_permalink( $product->id );
            }
            
    		$url = WISHLIST_URL . 'add-to-cart.php?wishlist_item_id=' . rtrim( $id, '_' );
    		
    		if( $user_id != '' ) {
    			$url .= '&id=' . $user_id;
    		}
            
    		return $url;
    	}

        /**
         * Build the URL used for an external/affiliate product.
         */
        public function get_affiliate_product_url( $id ) {
            $product = get_product( $id );
            return get_post_meta( $product->id, '_product_url', true );
        }
        
        /**
         * Build an URL with the nonce added.
         */
        public function get_nonce_url( $action, $url = '' ) {
            return add_query_arg( '_n', wp_create_nonce( 'king-wishlist-' . esc_attr( $action ) ), $url );
        }             
        
        /**
         * AJAX: add to wishlist action
         */
        public function add_to_wishlist_ajax() {  
	        
	        global $king;
	          
            $return = $this->add();
    
            if( $return == 'true'  )
                { $king->ext['pr']( $return ) . '##' . __( 'Product added!', 'arkahost' ); }
            elseif( $return == 'exists' )
                { $king->ext['pr']( $return ) . '##' . __( 'Product already in the wishlist.', 'arkahost' ); }
            elseif( count( $this->errors ) > 0 )
                { $king->ext['pr']( $this->get_errors() ); }
            
            die();        
        }
        
        /**
         * AJAX: remove from wishlist action
         */
        public function remove_from_wishlist_ajax() {   
            $count = king_wishlist_count_products();
                
            if( $this->remove( $_GET['wishlist_item_id'] ) )
                { echo apply_filters( 'king_wishlist_product_removed_text', __( 'Product successfully removed.', 'arkahost' ) ); }
            else {
                echo '#' . $count . '#';
                _e( 'Error. Unable to remove the product from the wishlist.', 'arkahost' );
            }
            
            if( !$count )
                { _e( 'No products were added to the wishlist', 'arkahost' ); }
            
            die();
        }
    }   
}