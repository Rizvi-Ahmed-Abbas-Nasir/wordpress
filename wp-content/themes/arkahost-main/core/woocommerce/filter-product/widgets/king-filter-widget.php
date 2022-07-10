<?php
/**
 * Main class
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'king_filter' ) ) {
 
class king_filter_Widget extends WP_Widget {

    function __construct() {
        $widget_ops  = array( 'classname' => 'king-filter-ajax-navigation woocommerce widget_layered_nav', 'description' => __( 'Filter products ajax widget!', 'arkahost' ) );
        $control_ops = array( 'width' => 400, 'height' => 350 );
        parent::__construct( 'king-filter-ajax-navigation', __( 'DEVN Woo Filter Widget', 'arkahost' ), $widget_ops, $control_ops );
    }


    function widget( $args, $instance ) {
        
        global $king_chosen_attributes, $woocommerce, $king_attributes_array, $king;

        extract( $args );

        if ( ! is_post_type_archive( 'product' ) && ! is_tax( array_merge( $king_attributes_array, array( 'product_cat', 'product_tag' ) ) ) ) {
            return;
        }

        $current_term    = $king_attributes_array && is_tax( $king_attributes_array ) ? get_queried_object()->term_id : '';
        $current_tax     = $king_attributes_array && is_tax( $king_attributes_array ) ? get_queried_object()->taxonomy : '';
        $title           = apply_filters( 'widget_title', ( isset( $instance['title'] ) ? $instance['title'] : ''), $instance, $this->id_base );
        $query_type      = isset( $instance['query_type'] ) ? $instance['query_type'] : 'and';
        $display_type    = isset( $instance['type'] ) ? $instance['type'] : 'list';
        $is_child_class  = 'dev-filter-child-terms';
        $is_chosen_class = 'chosen';
        $terms_type_list = ( isset( $instance['display'] ) && ( $display_type == 'list' || $display_type == 'select' ) ) ? $instance['display'] : 'all';

        /* FIX TO WOOCOMMERCE 2.1 */
        if ( function_exists( 'wc_attribute_taxonomy_name' ) ) {
            $taxonomy = wc_attribute_taxonomy_name( $instance['attribute'] );
        }
        else {
            $taxonomy = $woocommerce->attribute_taxonomy_name( $instance['attribute'] );
        }

        if ( ! taxonomy_exists( $taxonomy ) ) {
            return;
        }

        $terms = king_get_terms( $terms_type_list, $taxonomy );

        if ( count( $terms ) > 0 ) {

            ob_start();

            $found = false;

            $king->ext['pr']( $before_widget . $before_title . $title . $after_title );

            // Force found when option is selected - do not force found on taxonomy attributes
            if ( ! $king_attributes_array || ! is_tax( $king_attributes_array ) ) {
                if ( is_array( $king_chosen_attributes ) && array_key_exists( $taxonomy, $king_chosen_attributes ) ) {
                    $found = true;
                }
            }

            if ( $display_type == 'list' ) {
                // List display
                echo "<ul class='king-filter-list king-filter'>";

                foreach ( $terms as $term ) {

                    // Get count based on current view - uses transients
                    $transient_name = 'wc_ln_count_' . md5( sanitize_key( $taxonomy ) . sanitize_key( $term->term_id ) );

                    //if ( false === ( $_products_in_term = get_transient( $transient_name ) ) ) {

                        $_products_in_term = get_objects_in_term( $term->term_id, $taxonomy );

                        set_transient( $transient_name, $_products_in_term );
                    //}

                    $option_is_set = ( isset( $king_chosen_attributes[$taxonomy] ) && in_array( $term->term_id, $king_chosen_attributes[$taxonomy]['terms'] ) );

                    // If this is an AND query, only show options with count > 0
                    if ( $query_type == 'and' ) {

                        $count = sizeof( array_intersect( $_products_in_term, $woocommerce->query->filtered_product_ids ) );

                        // skip the term for the current archive
                        if ( $current_term == $term->term_id ) {
                            continue;
                        }

                        if ( $count > 0 && $current_term !== $term->term_id ) {
                            $found = true;
                        }

                        if ( ( $terms_type_list != 'hierarchical' || ! king_term_has_child( $term, $taxonomy ) ) && $count == 0 && ! $option_is_set ) {
                            continue;
                        }

                        // If this is an OR query, show all options so search can be expanded
                    }
                    else {

                        // skip the term for the current archive
                        if ( $current_term == $term->term_id ) {
                            continue;
                        }

                        $count = sizeof( array_intersect( $_products_in_term, $woocommerce->query->unfiltered_product_ids ) );

                        if ( $count > 0 ) {
                            $found = true;
                        }

                    }

                    $arg = 'filter_' . sanitize_title( $instance['attribute'] );

                    $current_filter = ( isset( $_GET[$arg] ) ) ? explode( ',', $_GET[$arg] ) : array();

                    if ( ! is_array( $current_filter ) ) {
                        $current_filter = array();
                    }

                    $current_filter = array_map( 'esc_attr', $current_filter );

                    if ( ! in_array( $term->term_id, $current_filter ) ) {
                        $current_filter[] = $term->term_id;
                    }

                    // Base Link decided by current page
                    if ( defined( 'SHOP_IS_ON_FRONT' ) ) {
                        $link = home_url();
                    }
                    elseif ( is_post_type_archive( 'product' ) || is_page( function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'shop' ) : woocommerce_get_page_id( 'shop' ) ) ) {
                        $link = get_post_type_archive_link( 'product' );
                    }
                    else {
                        $link = get_term_link( get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
                    }

                    // All current filters
                    if ( $king_chosen_attributes ) {
                        foreach ( $king_chosen_attributes as $name => $data ) {
                            if ( $name !== $taxonomy ) {

                                // Exclude query arg for current term archive term
                                while ( in_array( $current_term, $data['terms'] ) ) {
                                    $key = array_search( $current_term, $data );
                                    unset( $data['terms'][$key] );
                                }

                                // Remove pa_ and sanitize
                                $filter_name = sanitize_title( str_replace( 'pa_', '', $name ) );

                                if ( ! empty( $data['terms'] ) ) {
                                    $link = add_query_arg( 'filter_' . esc_attr( $filter_name ), esc_attr( implode( ',', $data['terms'] ) ), $link );
                                }

                                if ( $data['query_type'] == 'or' ) {
                                    $link = add_query_arg( 'query_type_' . esc_attr( $filter_name ), 'or', $link );
                                }
                            }
                        }
                    }

                    // Min/Max
                    if ( isset( $_GET['min_price'] ) ) {
                        $link = add_query_arg( 'min_price', esc_attr( $_GET['min_price'] ), $link );
                    }

                    if ( isset( $_GET['max_price'] ) ) {
                        $link = add_query_arg( 'max_price', esc_attr( $_GET['max_price'] ), $link );
                    }

                    // Current Filter = this widget
                    if ( isset( $king_chosen_attributes[$taxonomy] ) && is_array( $king_chosen_attributes[$taxonomy]['terms'] ) && in_array( $term->term_id, $king_chosen_attributes[$taxonomy]['terms'] ) ) {

                        $class = ( $terms_type_list == 'hierarchical' && king_term_is_child( $term ) ) ? "class='{$is_chosen_class}  {$is_child_class}'" : "class='{$is_chosen_class}'";

                        // Remove this term is $current_filter has more than 1 term filtered
                        if ( sizeof( $current_filter ) > 1 ) {
                            $current_filter_without_this = array_diff( $current_filter, array( $term->term_id ) );
                            $link                        = add_query_arg( $arg, esc_attr( implode( ',', $current_filter_without_this ) ), $link );
                        }

                    }
                    else {

                        $class = ( $terms_type_list == 'hierarchical' && king_term_is_child( $term ) ) ? "class='{$is_child_class}'" : '';

                        $link = add_query_arg( $arg, esc_attr( implode( ',', $current_filter ) ) , $link );

                    }

                    // Search Arg
                    if ( get_search_query() ) {
                        $link = add_query_arg( 's', esc_attr( get_search_query() ), $link );
                    }

                    // Post Type Arg
                    if ( isset( $_GET['post_type'] ) ) {
                        $link = add_query_arg( 'post_type', esc_attr( $_GET['post_type'] ), $link );
                    }

                    // Query type Arg
                    if ( $query_type == 'or' && ! ( sizeof( $current_filter ) == 1 && isset( $king_chosen_attributes[$taxonomy]['terms'] ) && is_array( $king_chosen_attributes[$taxonomy]['terms'] ) && in_array( $term->term_id, $king_chosen_attributes[$taxonomy]['terms'] ) ) ) {
                        $link = add_query_arg( 'query_type_' . sanitize_title( $instance['attribute'] ), 'or', $link );
                    }


                    echo '<li ' . $class . '>';

                    $king->ext['pr'] ( $count > 0 || $option_is_set ) ? '<a href="' . esc_url( apply_filters( 'woocommerce_layered_nav_link', $link ) ) . '">' : '<span>';

                    echo esc_html( $term->name );

                    $king->ext['pr'] ( $count > 0 || $option_is_set ) ? '</a>' : '</span>';

                    if ( $count != 0 ) {
                        echo ' <small class="count">' . esc_attr( $count ) . '</small><div class="clear"></div></li>';
                    }

                }

                echo "</ul>";

            }
            elseif ( $display_type == 'select' ) {
                ?>

                <a class="dev-filter-select-open" href="#"><?php _e( 'Filters List:', 'arkahost' ) ?></a>

                <?php
                // Select display
                echo "<div class='king-filter-select-wrapper'>";

                echo "<ul class='king-filter-select king-filter'>";

                foreach ( $terms as $term ) {

                    // Get count based on current view - uses transients
                    $transient_name = 'wc_ln_count_' . md5( sanitize_key( $taxonomy ) . sanitize_key( $term->term_id ) );

                    //if ( false === ( $_products_in_term = get_transient( $transient_name ) ) ) {

                        $_products_in_term = get_objects_in_term( $term->term_id, $taxonomy );

                        set_transient( $transient_name, $_products_in_term );
                    //}

                    $option_is_set = ( isset( $king_chosen_attributes[$taxonomy] ) && in_array( $term->term_id, $king_chosen_attributes[$taxonomy]['terms'] ) );

                    // If this is an AND query, only show options with count > 0
                    if ( $query_type == 'and' ) {

                        $count = sizeof( array_intersect( $_products_in_term, $woocommerce->query->filtered_product_ids ) );

                        // skip the term for the current archive
                        if ( $current_term == $term->term_id ) {
                            continue;
                        }

                        if ( $count > 0 && $current_term !== $term->term_id ) {
                            $found = true;
                        }

                        if ( ( $terms_type_list != 'hierarchical' || ! king_term_has_child( $term, $taxonomy ) ) && $count == 0 && ! $option_is_set ) {
                            continue;
                        }

                        // If this is an OR query, show all options so search can be expanded
                    }
                    else {

                        // skip the term for the current archive
                        if ( $current_term == $term->term_id ) {
                            continue;
                        }

                        $count = sizeof( array_intersect( $_products_in_term, $woocommerce->query->unfiltered_product_ids ) );

                        if ( $count > 0 ) {
                            $found = true;
                        }

                    }

                    $arg = 'filter_' . urldecode( sanitize_title( $instance['attribute'] ) );

                    $current_filter = ( isset( $_GET[$arg] ) ) ? explode( ',', $_GET[$arg] ) : array();

                    if ( ! is_array( $current_filter ) ) {
                        $current_filter = array();
                    }

                    $current_filter = array_map( 'esc_attr', $current_filter );

                    if ( ! in_array( $term->term_id, $current_filter ) ) {
                        $current_filter[] = $term->term_id;
                    }

                    // Base Link decided by current page
                    if ( defined( 'SHOP_IS_ON_FRONT' ) ) {
                        $link = home_url();
                    }
                    elseif ( is_post_type_archive( 'product' ) || is_page( woocommerce_get_page_id( 'shop' ) ) ) {
                        $link = get_post_type_archive_link( 'product' );
                    }
                    else {
                        $link = get_term_link( get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
                    }

                    // All current filters
                    if ( $king_chosen_attributes ) {
                        foreach ( $king_chosen_attributes as $name => $data ) {
                            if ( $name !== $taxonomy ) {

                                // Exclude query arg for current term archive term
                                while ( in_array( $current_term, $data['terms'] ) ) {
                                    $key = array_search( $current_term, $data );
                                    unset( $data['terms'][$key] );
                                }

                                // Remove pa_ and sanitize
                                $filter_name = urldecode( sanitize_title( str_replace( 'pa_', '', $name ) ) );

                                if ( ! empty( $data['terms'] ) ) {
                                    $link = add_query_arg( 'filter_' . esc_attr( $filter_name ), esc_attr( implode( ',', $data['terms'] ) ), $link );
                                }

                                if ( $data['query_type'] == 'or' ) {
                                    $link = add_query_arg( 'query_type_' . esc_attr( $filter_name ), 'or', $link );
                                }
                            }
                        }
                    }

                    // Min/Max
                    if ( isset( $_GET['min_price'] ) ) {
                        $link = add_query_arg( 'min_price', esc_attr( $_GET['min_price'] ), $link );
                    }

                    if ( isset( $_GET['max_price'] ) ) {
                        $link = add_query_arg( 'max_price', esc_attr( $_GET['max_price'] ), $link );
                    }

                    // Current Filter = this widget
                    if ( isset( $king_chosen_attributes[$taxonomy] ) && is_array( $king_chosen_attributes[$taxonomy]['terms'] ) && in_array( $term->term_id, $king_chosen_attributes[$taxonomy]['terms'] ) ) {

                        $class = ( $terms_type_list == 'hierarchical' && king_term_is_child( $term ) ) ? "class='{$is_chosen_class}  {$is_child_class}'" : "class='{$is_chosen_class}'";

                        // Remove this term is $current_filter has more than 1 term filtered
                        if ( sizeof( $current_filter ) > 1 ) {
                            $current_filter_without_this = array_diff( $current_filter, array( $term->term_id ) );
                            $link                        = add_query_arg( $arg, esc_attr( implode( ',', $current_filter_without_this ) ), $link );
                        }

                    }
                    else {

                        $class = ( $terms_type_list == 'hierarchical' && king_term_is_child( $term ) ) ? "class='{$is_child_class}'" : '';
                        $link  = add_query_arg( $arg, esc_attr( implode( ',', $current_filter ) ), $link );

                    }

                    // Search Arg
                    if ( get_search_query() ) {
                        $link = add_query_arg( 's', esc_attr( get_search_query() ), $link );
                    }

                    // Post Type Arg
                    if ( isset( $_GET['post_type'] ) ) {
                        $link = add_query_arg( 'post_type', esc_attr( $_GET['post_type'] ), $link );
                    }

                    // Query type Arg
                    if ( $query_type == 'or' && ! ( sizeof( $current_filter ) == 1 && isset( $king_chosen_attributes[$taxonomy]['terms'] ) && is_array( $king_chosen_attributes[$taxonomy]['terms'] ) && in_array( $term->term_id, $king_chosen_attributes[$taxonomy]['terms'] ) ) ) {
                        $link = add_query_arg( 'query_type_' . sanitize_title( $instance['attribute'] ), 'or', $link );
                    }

                    echo '<li ' . $class . '>';

                    $king->ext['pr'] ( $count > 0 || $option_is_set ) ? '<a data-type="select" href="' . esc_url( apply_filters( 'woocommerce_layered_nav_link', $link ) ) . '">' : '<span>';

                    echo esc_html( $term->name );

                    $king->ext['pr'] ( $count > 0 || $option_is_set ) ? '</a>' : '</span>';

                    echo '</li>';

                }

                echo "</ul>";

                echo "</div>";

            }
            elseif ( $display_type == 'color' ) {
                // List display
                echo "<ul class='king-filter-color king-filter king-filter-group'>";

                foreach ( $terms as $term ) {

                    // Get count based on current view - uses transients
                    $transient_name = 'wc_ln_count_' . md5( sanitize_key( $taxonomy ) . sanitize_key( $term->term_id ) );

                    //if ( false === ( $_products_in_term = get_transient( $transient_name ) ) ) {

                        $_products_in_term = get_objects_in_term( $term->term_id, $taxonomy );

                        set_transient( $transient_name, $_products_in_term );
                    //}

                    $option_is_set = ( isset( $king_chosen_attributes[$taxonomy] ) && in_array( $term->term_id, $king_chosen_attributes[$taxonomy]['terms'] ) );

                    // If this is an AND query, only show options with count > 0
                    if ( $query_type == 'and' ) {

                        $count = sizeof( array_intersect( $_products_in_term, $woocommerce->query->filtered_product_ids ) );

                        // skip the term for the current archive
                        if ( $current_term == $term->term_id ) {
                            continue;
                        }

                        if ( $count > 0 && $current_term !== $term->term_id ) {
                            $found = true;
                        }

                        if ( $count == 0 && ! $option_is_set ) {
                            continue;
                        }

                        // If this is an OR query, show all options so search can be expanded
                    }
                    else {

                        // skip the term for the current archive
                        if ( $current_term == $term->term_id ) {
                            continue;
                        }

                        $count = sizeof( array_intersect( $_products_in_term, $woocommerce->query->unfiltered_product_ids ) );

                        if ( $count > 0 ) {
                            $found = true;
                        }

                    }

                    $arg = 'filter_' . sanitize_title( $instance['attribute'] );

                    $current_filter = ( isset( $_GET[$arg] ) ) ? explode( ',', $_GET[$arg] ) : array();

                    if ( ! is_array( $current_filter ) ) {
                        $current_filter = array();
                    }

                    $current_filter = array_map( 'esc_attr', $current_filter );

                    if ( ! in_array( $term->term_id, $current_filter ) ) {
                        $current_filter[] = $term->term_id;
                    }

                    // Base Link decided by current page
                    if ( defined( 'SHOP_IS_ON_FRONT' ) ) {
                        $link = home_url();
                    }
                    elseif ( is_post_type_archive( 'product' ) || is_page( function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'shop' ) : woocommerce_get_page_id( 'shop' ) ) ) {
                        $link = get_post_type_archive_link( 'product' );
                    }
                    else {
                        $link = get_term_link( get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
                    }

                    // All current filters
                    if ( $king_chosen_attributes ) {
                        foreach ( $king_chosen_attributes as $name => $data ) {
                            if ( $name !== $taxonomy ) {

                                // Exclude query arg for current term archive term
                                while ( in_array( $current_term, $data['terms'] ) ) {
                                    $key = array_search( $current_term, $data );
                                    unset( $data['terms'][$key] );
                                }

                                // Remove pa_ and sanitize
                                $filter_name = sanitize_title( str_replace( 'pa_', '', $name ) );

                                if ( ! empty( $data['terms'] ) ) {
                                    $link = add_query_arg( 'filter_' . $filter_name, esc_attr( implode( ',', $data['terms'] ) ), $link );
                                }

                                if ( $data['query_type'] == 'or' ) {
                                    $link = add_query_arg( 'query_type_' . $filter_name, 'or', $link );
                                }
                            }
                        }
                    }

                    // Min/Max
                    if ( isset( $_GET['min_price'] ) ) {
                        $link = add_query_arg( 'min_price', esc_attr($_GET['min_price']), $link );
                    }

                    if ( isset( $_GET['max_price'] ) ) {
                        $link = add_query_arg( 'max_price', esc_attr($_GET['max_price']), $link );
                    }

                    // Current Filter = this widget
                    if ( isset( $king_chosen_attributes[$taxonomy] ) && is_array( $king_chosen_attributes[$taxonomy]['terms'] ) && in_array( $term->term_id, $king_chosen_attributes[$taxonomy]['terms'] ) ) {

                        $class = ( $terms_type_list == 'hierarchical' && king_term_is_child( $term ) ) ? "class='{$is_chosen_class}  {$is_child_class}'" : "class='{$is_chosen_class}'";

                        // Remove this term is $current_filter has more than 1 term filtered
                        if ( sizeof( $current_filter ) > 1 ) {
                            $current_filter_without_this = array_diff( $current_filter, array( $term->term_id ) );
                            $link                        = add_query_arg( $arg, esc_attr( implode( ',', $current_filter_without_this ) ), $link );
                        }

                    }
                    else {

                        $class = ( $terms_type_list == 'hierarchical' && king_term_is_child( $term ) ) ? "class='{$is_child_class}'" : '';
                        $link  = add_query_arg( $arg, esc_attr( implode( ',', $current_filter ) ), $link );

                    }

                    // Search Arg
                    if ( get_search_query() ) {
                        $link = add_query_arg( 's', esc_attr( get_search_query() ), $link );
                    }

                    // Post Type Arg
                    if ( isset( $_GET['post_type'] ) ) {
                        $link = add_query_arg( 'post_type', esc_attr( $_GET['post_type'] ), $link );
                    }

                    // Query type Arg
                    if ( $query_type == 'or' && ! ( sizeof( $current_filter ) == 1 && isset( $king_chosen_attributes[$taxonomy]['terms'] ) && is_array( $king_chosen_attributes[$taxonomy]['terms'] ) && in_array( $term->term_id, $king_chosen_attributes[$taxonomy]['terms'] ) ) ) {
                        $link = add_query_arg( 'query_type_' . sanitize_title( $instance['attribute'] ), 'or', $link );
                    }

                    if ( $instance['colors'][$term->term_id] != '' ) {
                        echo '<li ' . $class . '>';

                        $king->ext['pr'] ( $count > 0 || $option_is_set ) ? '<a style="background-color:' . $instance['colors'][$term->term_id] . ';" href="' . esc_url( apply_filters( 'woocommerce_layered_nav_link', $link ) ) . '" title="' . $term->name . '" >' : '<span style="background-color:' . $instance['colors'][$term->term_id] . ';" >';

                        echo esc_html( $term->name );

                        $king->ext['pr'] ( $count > 0 || $option_is_set ) ? '</a>' : '</span>';
                    }
                }

                echo "</ul>";

            }
            elseif ( $display_type == 'label' ) {
                // List display
                echo "<ul class='king-filter-label king-filter king-filter-group'>";

                foreach ( $terms as $term ) {

                    // Get count based on current view - uses transients
                    $transient_name = 'wc_ln_count_' . md5( sanitize_key( $taxonomy ) . sanitize_key( $term->term_id ) );

                    //if ( false === ( $_products_in_term = get_transient( $transient_name ) ) ) {

                        $_products_in_term = get_objects_in_term( $term->term_id, $taxonomy );

                        set_transient( $transient_name, $_products_in_term );
                    //}

                    $option_is_set = ( isset( $king_chosen_attributes[$taxonomy] ) && in_array( $term->term_id, $king_chosen_attributes[$taxonomy]['terms'] ) );

                    // If this is an AND query, only show options with count > 0
                    if ( $query_type == 'and' ) {

                        $count = sizeof( array_intersect( $_products_in_term, $woocommerce->query->filtered_product_ids ) );

                        // skip the term for the current archive
                        if ( $current_term == $term->term_id ) {
                            continue;
                        }

                        if ( $count > 0 && $current_term !== $term->term_id ) {
                            $found = true;
                        }

                        if ( $count == 0 && ! $option_is_set ) {
                            continue;
                        }

                        // If this is an OR query, show all options so search can be expanded
                    }
                    else {

                        // skip the term for the current archive
                        if ( $current_term == $term->term_id ) {
                            continue;
                        }

                        $count = sizeof( array_intersect( $_products_in_term, $woocommerce->query->unfiltered_product_ids ) );

                        if ( $count > 0 ) {
                            $found = true;
                        }

                    }

                    $arg = 'filter_' . sanitize_title( $instance['attribute'] );

                    $current_filter = ( isset( $_GET[$arg] ) ) ? explode( ',', $_GET[$arg] ) : array();

                    if ( ! is_array( $current_filter ) ) {
                        $current_filter = array();
                    }

                    $current_filter = array_map( 'esc_attr', $current_filter );

                    if ( ! in_array( $term->term_id, $current_filter ) ) {
                        $current_filter[] = $term->term_id;
                    }

                    // Base Link decided by current page
                    if ( defined( 'SHOP_IS_ON_FRONT' ) ) {
                        $link = home_url();
                    }
                    elseif ( is_post_type_archive( 'product' ) || is_page( function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'shop' ) : woocommerce_get_page_id( 'shop' ) ) ) {
                        $link = get_post_type_archive_link( 'product' );
                    }
                    else {
                        $link = get_term_link( get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
                    }

                    // All current filters
                    if ( $king_chosen_attributes ) {
                        foreach ( $king_chosen_attributes as $name => $data ) {
                            if ( $name !== $taxonomy ) {

                                // Exclude query arg for current term archive term
                                while ( in_array( $current_term, $data['terms'] ) ) {
                                    $key = array_search( $current_term, $data );
                                    unset( $data['terms'][$key] );
                                }

                                // Remove pa_ and sanitize
                                $filter_name = sanitize_title( str_replace( 'pa_', '', $name ) );

                                if ( ! empty( $data['terms'] ) ) {
                                    $link = add_query_arg( 'filter_' . $filter_name, esc_attr( implode( ',', $data['terms'] ) ), $link );
                                }

                                if ( $data['query_type'] == 'or' ) {
                                    $link = add_query_arg( 'query_type_' . $filter_name, 'or', $link );
                                }
                            }
                        }
                    }

                    // Min/Max
                    if ( isset( $_GET['min_price'] ) ) {
                        $link = add_query_arg( 'min_price', esc_attr( $_GET['min_price'] ), $link );
                    }

                    if ( isset( $_GET['max_price'] ) ) {
                        $link = add_query_arg( 'max_price', esc_attr( $_GET['max_price'] ), $link );
                    }

                    // Current Filter = this widget
                    if ( isset( $king_chosen_attributes[$taxonomy] ) && is_array( $king_chosen_attributes[$taxonomy]['terms'] ) && in_array( $term->term_id, $king_chosen_attributes[$taxonomy]['terms'] ) ) {

                        $class = ( $terms_type_list == 'hierarchical' && king_term_is_child( $term ) ) ? "class='{$is_chosen_class}  {$is_child_class}'" : "class='{$is_chosen_class}'";

                        // Remove this term is $current_filter has more than 1 term filtered
                        if ( sizeof( $current_filter ) > 1 ) {
                            $current_filter_without_this = array_diff( $current_filter, array( $term->term_id ) );
                            $link                        = add_query_arg( $arg, esc_attr( implode( ',', $current_filter_without_this ) ), $link );
                        }

                    }
                    else {

                        $class = ( $terms_type_list == 'hierarchical' && king_term_is_child( $term ) ) ? "class='{$is_child_class}'" : '';
                        $link  = add_query_arg( $arg, esc_attr( implode( ',', $current_filter )), $link );

                    }

                    // Search Arg
                    if ( get_search_query() ) {
                        $link = add_query_arg( 's', esc_attr( get_search_query() ), $link );
                    }

                    // Post Type Arg
                    if ( isset( $_GET['post_type'] ) ) {
                        $link = add_query_arg( 'post_type', esc_attr( $_GET['post_type'] ), $link );
                    }

                    // Query type Arg
                    if ( $query_type == 'or' && ! ( sizeof( $current_filter ) == 1 && isset( $king_chosen_attributes[$taxonomy]['terms'] ) && is_array( $king_chosen_attributes[$taxonomy]['terms'] ) && in_array( $term->term_id, $king_chosen_attributes[$taxonomy]['terms'] ) ) ) {
                        $link = add_query_arg( 'query_type_' . sanitize_title( $instance['attribute'] ), 'or', $link );
                    }

                    if ( $instance['labels'][$term->term_id] != '' ) {

                        echo '<li ' . $class . '>';

                        $king->ext['pr'] ( $count > 0 || $option_is_set ) ? '<a title="' . $term->name . '" href="' . esc_url( apply_filters( 'woocommerce_layered_nav_link', $link ) ) . '">' : '<span>';

                        echo esc_html( $instance['labels'][$term->term_id] );

                        $king->ext['pr'] ( $count > 0 || $option_is_set ) ? '</a>' : '</span>';
                    }
                }
                echo "</ul>";

            } // End display type conditional

            $king->ext['pr']( $after_widget );

            if ( ! $found ) {
                ob_end_clean();
                echo substr( $before_widget, 0, strlen( $before_widget ) - 1 ) . ' style="display:none">' . $after_widget;
            }
            else {
                echo ob_get_clean();
            }
        }
    }


function form( $instance ) {
    
    global $woocommerce;

    $defaults = array(
        'title'      => '',
        'attribute'  => '',
        'query_type' => 'and',
        'type'       => 'list',
        'colors'     => '',
        'labels'     => '',
        'display'    => 'all'
    );

    $instance = wp_parse_args( (array) $instance, $defaults ); ?>

    <p>
        <label>
            <strong><?php _e( 'Title', 'arkahost' ) ?>:</strong><br />
            <input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
        </label>
    </p>

    <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'attribute' ) ); ?>"><strong><?php _e( 'Attribute:', 'arkahost' ) ?></strong></label>
        <select class="king_filter_attributes widefat" id="<?php echo esc_attr( $this->get_field_id( 'attribute' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'attribute' ) ); ?>">
            <?php king_filter_dropdown_attributes( $instance['attribute'] ); ?>
        </select></p>

    <p><label for="<?php echo esc_attr( $this->get_field_id( 'query_type' ) ); ?>"><?php _e( 'Query Type:', 'arkahost' ) ?></label>
        <select id="<?php echo esc_attr( $this->get_field_id( 'query_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'query_type' ) ); ?>">
            <option value="and" <?php selected( $instance['query_type'], 'and' ); ?>><?php _e( 'AND', 'arkahost' ); ?></option>
            <option value="or" <?php selected( $instance['query_type'], 'or' ); ?>><?php _e( 'OR', 'arkahost' ); ?></option>
        </select></p>

    <p><label for="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>"><strong><?php _e( 'Type:', 'arkahost' ) ?></strong></label>
        <select class="king_filter_type widefat" id="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'type' ) ); ?>">
            <option value="list" <?php selected( 'list', $instance['type'] ) ?>><?php _e( 'List', 'arkahost' ) ?></option>
            <option value="color" <?php selected( 'color', $instance['type'] ) ?>><?php _e( 'Color', 'arkahost' ) ?></option>
            <option value="label" <?php selected( 'label', $instance['type'] ) ?>><?php _e( 'Label', 'arkahost' ) ?></option>
            <option value="select" <?php selected( 'select', $instance['type'] ) ?>><?php _e( 'Dropdown', 'arkahost' ) ?></option>
        </select>
    </p>

    <p id="dev-filter-display" class="dev-filter-display-<?php echo esc_attr( $instance['type'] ); ?>">
        <label for="<?php echo esc_attr( $this->get_field_id( 'display' ) ); ?>"><strong><?php _e( 'Display (default All):', 'arkahost' ) ?></strong></label>
        <select class="king_filter_type widefat" id="<?php echo esc_attr( $this->get_field_id( 'display' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'display' ) ); ?>">
            <option value="all"          <?php selected( 'all', $instance['display'] ) ?>>          <?php _e( 'All (no hierarchical)', 'arkahost' ) ?></option>
            <option value="hierarchical" <?php selected( 'hierarchical', $instance['display'] ) ?>> <?php _e( 'All Hierarchical', 'arkahost' ) ?>   </option>
            <option value="parent"       <?php selected( 'parent', $instance['display'] ) ?>>       <?php _e( 'Only Parent', 'arkahost' ) ?>        </option>
        </select>
    </p>

    <div class="king_filter_placeholder">
        <?php king_filter_attributes_table(
            $instance['type'],
            $instance['attribute'],
            'widget-' . $this->id . '-',
            'widget-' . $this->id_base . '[' . $this->number . ']',
            $instance['type'] == 'color' ? $instance['colors'] : ( $instance['type'] == 'label' ? $instance['labels'] : array() ),
            $instance['display']
        );
        ?>
    </div>
    <span class="spinner" style="display: none;"></span>

<input type="hidden" name="widget_id" value="widget-<?php echo esc_attr( $this->id ); ?>-" />
<input type="hidden" name="widget_name" value="widget-<?php echo esc_attr( $this->id_base ); ?>[<?php echo esc_attr( $this->number ); ?>]" />

    <script>jQuery(document).trigger('king_colorpicker');</script>
<?php
}

    function update( $new_instance, $old_instance ) {
        
        global $woocommerce;

        $instance = $old_instance;

        if ( empty( $new_instance['title'] ) ) {
            $new_instance['title'] = function_exists( 'wc_attribute_label' ) ? wc_attribute_label( $new_instance['attribute'] ) : $woocommerce->attribute_label( $new_instance['attribute'] );
        }

        $instance['title']      = strip_tags( $new_instance['title'] );
        $instance['attribute']  = stripslashes( $new_instance['attribute'] );
        $instance['query_type'] = stripslashes( $new_instance['query_type'] );
        $instance['type']       = stripslashes( $new_instance['type'] );
        $instance['colors']     = $new_instance['colors'];
        $instance['labels']     = $new_instance['labels'];
        $instance['display']    = $new_instance['display'];

        return $instance;
    }

}
}