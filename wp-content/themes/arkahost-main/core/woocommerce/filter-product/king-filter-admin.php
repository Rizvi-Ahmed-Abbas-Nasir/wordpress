<?php
/**
 * Admin class

 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if( !class_exists( 'king_filter_Admin' ) ) {
    /**
     * Admin class. 
	 * The class manage all the admin behaviors.
     */
    class king_filter_Admin {
       
    	/**
		 * Constructor
		 * 
		 * @access public
		 */
		public function __construct() {
            

			//Actions
			add_action( 'init', array( $this, 'init' ) );
            add_action('wp_ajax_king_filter_select_type', array( $this, 'ajax_print_terms') );

            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );

          
            do_action( 'king_filter_loaded' );
		}
		
		
		/**
		 * Init method:
		 *  - default options
		 * 
		 * @access public
		 */
		public function init() {}
		

		/**
		 * Enqueue admin styles and scripts
		 * 
		 * @access public
		 * @return void 
		 */
		public function enqueue_styles_scripts() {

                wp_enqueue_style( 'wp-color-picker' );
                wp_enqueue_style( 'king_filter_admin', king_filter_URL . 'assets/css/admin.css', false, '1.0.0' );

                wp_enqueue_script( 'wp-color-picker' );
                wp_enqueue_script( 'king_filter_admin', king_filter_URL . 'assets/js/king-filter-admin.js', array('jquery', 'wp-color-picker'), '1.0.0', true );
           
		}

        /**
         * Print terms for the element selected
         *
         * @access public
         * @return void
         */
        public function ajax_print_terms() {
            $type = $_POST['value'];
            $attribute = $_POST['attribute'];
            $return = array('message' => '', 'content' => $_POST);

            $terms = get_terms( 'pa_' . $attribute, array('hide_empty'=>'0') );

            $return['content'] = king_filter_attributes_table(
                $type,
                $attribute,
                $_POST['id'],
                $_POST['name'],
                json_decode($_POST['value']),
                false
            );

            echo json_encode($return);
            die();
        }

    }
}
