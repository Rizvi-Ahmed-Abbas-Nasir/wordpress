<?php
/**
 * Init class
 */

if ( !defined( 'king_WISHLIST' ) ) { exit; } // Exit if accessed directly

if( !class_exists( 'king_WISHLIST_INIT' ) ) {
   
    class king_WISHLIST_INIT {
	
        private $_king_wishlist_install, $options;
		
       
        public function __construct() {
			
            $this->_positions = apply_filters( 'king_wishlist_positions', array(
                'after-cart' => array( 'hook' => 'woocommerce_single_product_summary', 'priority' => 31 ),
                'after-thumbnails' => array( 'hook' => 'woocommerce_product_thumbnails', 'priority' => 21 ),
                'after-summary'   => array( 'hook' => 'woocommerce_after_single_product_summary', 'priority' => 21 )
            ) );
            $this->_king_wishlist_install = new king_WISHLIST_INSTALL();
            
            if ( is_admin() && ! defined( 'DOING_AJAX' ) ) $this->install();
            
            add_action( 'init', array( $this, 'init' ), 0 );
            add_filter( 'woocommerce_product_settings', array( $this, 'add_page_setting_woocommerce' ) );
 
            if( king_wishlist_actived() ) {
			
                add_action( 'wp_head', array( $this, 'add_button' ) );
                add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_and_stuffs' ) );
                add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
                    
                
                do_action( 'king_wishlist_loaded' );
            }
        }
        
        public function init() {
        
            global $king_wishlist;
            
            if( is_user_logged_in() ) {
                $king_wishlist->details['user_id'] = get_current_user_id();
                
                //check whether any products are added to wishlist, then after login add to the wishlist if not added
                if( king_usecookies() ) {
                    $cookie = king_getcookie( 'king_wishlist_products' );
                    foreach( $cookie as $details ) {
                        $king_wishlist->details = $details;
                        $king_wishlist->details['user_id'] = get_current_user_id();

                        $ret_val = $king_wishlist->add();
                    }
                    
                    king_destroycookie( 'king_wishlist_products' );
                } else {
                    if( isset( $_SESSION['king_wishlist_products'] ) ) {
                        foreach( $_SESSION['king_wishlist_products'] as $details ) {
                            $king_wishlist->details = $details;
                            $king_wishlist->details['user_id'] = get_current_user_id();
    
                            $ret_val = $king_wishlist->add();
                        }
                        
                        unset( $_SESSION['king_wishlist_products'] );
                    }
                }
            }
            
           
        }
     
        /**
         * Installation
         */
        public function install() {
            if( !$this->_king_wishlist_install->is_installed() ) {
                register_activation_hook( WISHLIST_DIR, array( $this->_king_wishlist_install, 'init' ) );
                $this->_king_wishlist_install->init();
                $this->_king_wishlist_install->default_options( $this->options );
                
                
                do_action( 'king_wishlist_installed' );
            }
        }
        
        /**
         * Add the "Add to Wishlist" button. Needed to use in wp_head hook.
         */
        public function add_button() {
            global $post, $king;
            
            if( !isset( $post ) || !is_object( $post ) )
                { return; }
            
            // Add the link "Add to wishlist"
            $position = !empty($king->cfg['wl_position'])?$king->cfg['wl_position']:'use-shortcode';
            $position = empty( $position ) ? 'add-to-cart' : $position;
            
            if( $position != 'use-shortcode' )
            { 
                	add_action( $this->_positions[$position]['hook'], 
                				 $king->ext['cf']( '', 'echo do_shortcode( "[king_add_to_wishlist]" );' ), 
                				 $this->_positions[$position]['priority'] 
                			  ); 
            }
        }
        
        /**
         * Enqueue styles, scripts and other stuffs needed in the <head>.
         */
        public function enqueue_styles_and_stuffs() {
	        global $king;
            $located = locate_template( array(
                'woocommerce/wishlist.css',
                'wishlist.css'
            ) );
            
            if( !$located ) 
                { wp_enqueue_style( 'king-wishlist-main', WISHLIST_URL . 'css/wishlist.css' ); }
            else
                { wp_enqueue_style( 'king-wishlist-user-main', str_replace( get_template_directory(), get_template_directory_uri(), $located ) ); }
           
            
                
            ?>
           
            <script type="text/javascript">
            var king_wishlist_ajax_web_url = '<?php echo admin_url('admin-ajax.php') ?>';
            var login_redirect_url= '<?php echo wp_login_url() . '?redirect_to=' . urlencode( $king->ext['sv']('REQUEST_URI') ) ?>';
            </script>
            <?php
        }
        
        /**
         * Enqueue scripts.
         */
        public function enqueue_scripts() {
            wp_register_script( 'king-wishlist', WISHLIST_URL . 'js/king-wishlist.js', array( 'jquery' ), '1.0', true );
            wp_enqueue_script( 'king-wishlist' );
            
            $king_wishlist_outofstock = array(
                'out_of_stock' => __( 'Sorry! This product is Out of Stock.', 'arkahost' ),
            );
            wp_localize_script( 'jquery-king-wishlist', 'king_wishlist_outofstock', $king_wishlist_outofstock );
        }
        
      
        /**
         * Add the select for the Wishlist page in WooCommerce > Settings > Pages
         */
        public function add_page_setting_woocommerce( $settings ) {
            unset( $settings[count( $settings ) - 1] );
            
            $settings[] = array(
                'name' => __( 'Wishlist Page', 'arkahost' ),
        		'desc' 		=> __( 'Page contents: [king_wishlist]', 'arkahost' ),
        		'id' 		=> 'king_wcwl_wishlist_page_id',
        		'type' 		=> 'single_select_page',
        		'std' 		=> '',         // for woocommerce < 2.0
        		'default' 	=> '',         // for woocommerce >= 2.0
        		'class'		=> 'chosen_select_nostd',
        		'css' 		=> 'min-width:300px;',
        		'desc_tip'	=>  false,
            );
            
            $settings[] = array( 'type' => 'sectionend', 'id' => 'page_options');
            
            return $settings;
        }
    }
}
