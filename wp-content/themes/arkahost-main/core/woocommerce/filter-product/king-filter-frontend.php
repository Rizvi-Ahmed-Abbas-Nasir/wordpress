<?php
/**
 * Frontend class
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'king_filter_Frontend' ) ) {


    class king_filter_Frontend {

        public function __construct() {
          
            //Actions
            add_action( 'init', array( $this, 'init' ) );
            add_action( 'init', array( $this, 'woocommerce_filter_product_init' ), 99 );

            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );

            
            do_action( 'king_filter_loaded' );
        }
        public function init() {
		
        }

        public function enqueue_styles_scripts() {
            if ( king_filter_displayed() ) {
                $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

                wp_enqueue_style( 'king_filter_admin', king_filter_URL . 'assets/css/frontend.css', false, '1.0.0' );
                wp_enqueue_script( 'king_filter_frontend', king_filter_URL . 'assets/js/king-filter-frontend' . $suffix . '.js', array( 'jquery' ), '1.0.0' , true );

                $args = array(
                    'container'    => '.products',
                    'pagination'   => 'nav.woocommerce-pagination',
                    'result_count' => '.woocommerce-result-count'
                );
                wp_localize_script( 'king_filter_frontend', 'king_filter', apply_filters( 'king-filter-frontend-args', $args ) );
            }
        }


        public function woocommerce_filter_product_init() {

            if ( is_active_widget( false, false, 'king-filter-ajax-navigation', true ) && ! is_admin() ) {

                global $king_chosen_attributes, $woocommerce, $king_attributes_array;

                $king_chosen_attributes = $king_attributes_array = array();

                /* FIX TO WOOCOMMERCE 2.1 */
                if ( function_exists( 'wc_get_attribute_taxonomies' ) ) {
                    $attribute_taxonomies = wc_get_attribute_taxonomies();
                }
                else {
                    $attribute_taxonomies = $woocommerce->get_attribute_taxonomies();
                }


                if ( $attribute_taxonomies ) {
                    foreach ( $attribute_taxonomies as $tax ) {

                        $attribute = sanitize_title( $tax->attribute_name );

                        /* FIX TO WOOCOMMERCE 2.1 */
                        if ( function_exists( 'wc_attribute_taxonomy_name' ) ) {
                            $taxonomy = wc_attribute_taxonomy_name( $attribute );
                        }
                        else {
                            $taxonomy = $woocommerce->attribute_taxonomy_name( $attribute );
                        }


                        // create an array of product attribute taxonomies
                        $king_attributes_array[] = $taxonomy;

                        $name            = 'filter_' . $attribute;
                        $query_type_name = 'query_type_' . $attribute;

                        if ( ! empty( $_GET[$name] ) && taxonomy_exists( $taxonomy ) ) {

                            $king_chosen_attributes[$taxonomy]['terms'] = explode( ',', $_GET[$name] );

                            if ( empty( $_GET[$query_type_name] ) || ! in_array( strtolower( $_GET[$query_type_name] ), array( 'and', 'or' ) ) ) {
                                $king_chosen_attributes[$taxonomy]['query_type'] = apply_filters( 'woocommerce_layered_nav_default_query_type', 'and' );
                            }
                            else {
                                $king_chosen_attributes[$taxonomy]['query_type'] = strtolower( $_GET[$query_type_name] );
                            }

                        }
                    }
                }

                /* if ( version_compare( preg_replace( '/-beta-([0-9]+)/', '', $woocommerce->version ), '2.1', '<' ) ) {
                    add_filter( 'loop_shop_post_in', 'woocommerce_layered_nav_query' );
                }
                else {
                    add_filter( 'loop_shop_post_in', array( WC()->query, 'layered_nav_query' ) );
                }
 */

            }
        }


    }
}
