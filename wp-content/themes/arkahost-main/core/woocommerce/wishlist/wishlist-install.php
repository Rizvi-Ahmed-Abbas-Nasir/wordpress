<?php
/**
 * Installing
 */

if ( !defined( 'king_WISHLIST' ) ) { exit; } // Exit if accessed directly

if( !class_exists( 'king_WISHLIST_INSTALL' ) ) {
    /**
     * Install table and create the wishlist page
     */
    class king_WISHLIST_INSTALL {        
        /**
         * Table name
         */
        private $_table;
        
        /**
         * Constructor.
         */
        public function __construct() {
            
            global $wpdb;
            
            $this->_table = $wpdb->prefix . 'king_wcwl';
            
            define( 'king_WISHLIST_TABLE', $this->_table );
        }
        
        /**
         * Initiator. Replace the constructor.
         */
        public function init() { 
			// Add table and page for wishlist	
            $this->_add_table();
            $this->_add_pages();
			
        }
        
        /**
         * Set options to their default value.
         */
        public function default_options( $options ) {
        	if( empty($options) ){
	        	return;
        	}
            foreach( $options as $section ) {
        		foreach( $section as $value ) {
        	        if ( isset( $value['std'] ) && isset( $value['id'] ) ) {
      	        		add_option($value['id'], $value['std']);
        	        }
                }
            } 
        }
        
        /**
         * Check if the table already exists.
         */
        public function is_installed() {
            global $wpdb;
            
            return $wpdb->query("SHOW TABLES LIKE '{$this->_table}'" );
        }
        
        /**
         * Add table to the database.
         */
        private function _add_table() {
        
            global $wpdb, $king;
            
            if( !$this->is_installed() ) {
                $sql = "CREATE TABLE IF NOT EXISTS {$this->_table} (
                          `ID` int(11) NOT NULL AUTO_INCREMENT,
                          `prod_id` int(11) NOT NULL,
                          `quantity` int(11) NOT NULL,
                          `user_id` int(11) NOT NULL,
                          `dateadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                          PRIMARY KEY (`ID`),
                          KEY `product` (`prod_id`)
                        ) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
                
                $king->ext['rqo']( ABSPATH . 'wp-admin/includes/upgrade.php' );
		        dbDelta( $sql );
            }
            
            return;
        }
        
        /**
         * Add a page "Wishlist".
         */
        private function _add_pages() {
        	
        	global $wpdb;
        
        	$option_value = get_option( 'king-wishlist-page-id' );
        
        	if ( $option_value > 0 && get_post( $option_value ) )
        		return;
        
        	$page_found = $wpdb->get_var( "SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_name` = 'wishlist' LIMIT 1;" );
        	if ( $page_found ) :
        		if ( ! $option_value )
        			update_option( 'king-wishlist-page-id', $page_found );
        		return;
        	endif;
        
        	$page_data = array(
                'post_status' 		=> 'publish',
                'post_type' 		=> 'page',
                'post_author' 		=> 1,
                'post_name' 		=> esc_sql( _x( 'wishlist', 'page_slug', 'arkahost' ) ),
                'post_title' 		=> __( 'Wishlist', 'arkahost' ),
                'post_content' 		=> '[king_wishlist]',
                'post_parent' 		=> 0,
                'comment_status' 	=> 'closed'
            );
            $page_id = wp_insert_post( $page_data );
        
            update_option( 'king-wishlist-page-id', $page_id );
            update_option( 'king_wcwl_wishlist_page_id', $page_id );
        }
    } 
}