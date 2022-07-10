<?php
/**
 * Main class
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'king_filter' ) ) {

    class king_filter {

        public $obj = null;

        /**
         * Constructor
         *
         * @return mixed|king_filter_Admin|king_filter_Frontend
         */
        public function __construct() {

            // actions
            add_action( 'init', array( $this, 'init' ) );
            add_action( 'widgets_init', array( $this, 'registerWidgets' ) );


            if ( is_admin() ) {
                $this->obj = new king_filter_Admin();
            }
            else {
                $this->obj = new king_filter_Frontend();
            }

            return $this->obj;
        }


        /**
         * Init method
         *
         * @access public
         */
        public function init() {
		
        }

        /**
         * Load and register widgets
         *
         * @access public
         */
        public function registerWidgets() {
            global $king;
			$king->ext['rw']( 'king_filter_Widget' );
            $king->ext['rw']( 'king_RESET_FILTER_Widget' );
        }

    }
}