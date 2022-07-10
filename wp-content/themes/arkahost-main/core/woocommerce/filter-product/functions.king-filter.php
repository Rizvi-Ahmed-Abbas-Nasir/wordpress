<?php
/**
 * Functions
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly


/**
 * Return a dropdown with Woocommerce attributes
 */
function king_filter_dropdown_attributes( $selected, $echo = true ) {
	
	global $king;
	
    $attributes = king_filter_Helper::attribute_taxonomies();
    $options    = "";

    foreach ( $attributes as $attribute ) {
        $options .= "<option name='{$attribute}'" . selected( $attribute, $selected, false ) . ">{$attribute}</option>";
    }

    if ( $echo ) {
        $king->ext['pr']( $options );
    }
    else {
        $king->ext['pr']($options);
    }
}


/**
 * Print the widgets options already filled
 *
 * @param $type      string list|colors|label
 * @param $attribute woocommerce taxonomy
 * @param $id        id used in the <input />
 * @param $name      base name used in the <input />
 * @param $values    array of values (could be empty if this is an ajax call)
 *
 * @return string
 */
function king_filter_attributes_table( $type, $attribute, $id, $name, $values = array(), $echo = true ) {
   
   	global $king;
   
    $return = '';

    $terms = get_terms( 'pa_' . $attribute, array( 'hide_empty' => '0' ) );

    if ( 'list' == $type ) {
        $return = '<input type="hidden" name="' . $name . '[colors]" value="" /><input type="hidden" name="' . $name . '[labels]" value="" />';
    }
    elseif ( 'color' == $type ) {
        if ( ! empty( $terms ) ) {
            $return = sprintf( '<table><tr><th>%s</th><th>%s</th></tr>', __( 'Term', 'arkahost' ), __( 'Color', 'arkahost' ) );

            foreach ( $terms as $term ) {
                $return .= "<tr><td><label for='{$id}{$term->term_id}'>{$term->name}</label></td><td><input type='text' id='{$id}{$term->term_id}' name='{$name}[colors][{$term->term_id}]' value='" . ( isset( $values[$term->term_id] ) ? $values[$term->term_id] : '' ) . "' size='3' class='king-colorpicker' /></td></tr>";
            }

            $return .= '</table>';
        }

        $return .= '<input type="hidden" name="' . $name . '[labels]" value="" />';
    }
    elseif ( 'label' == $type ) {
        if ( ! empty( $terms ) ) {
            $return = sprintf( '<table><tr><th>%s</th><th>%s</th></tr>', __( 'Term', 'arkahost' ), __( 'Labels', 'arkahost' ) );

            foreach ( $terms as $term ) {
                $return .= "<tr><td><label for='{$id}{$term->term_id}'>{$term->name}</label></td><td><input type='text' id='{$id}{$term->term_id}' name='{$name}[labels][{$term->term_id}]' value='" . ( isset( $values[$term->term_id] ) ? $values[$term->term_id] : '' ) . "' size='3' /></td></tr>";
            }

            $return .= '</table>';
        }

        $return .= '<input type="hidden" name="' . $name . '[colors]" value="" />';
    }

    if ( $echo ) {
        $king->ext['pr']( $return );
    }

    return $return;
}


/**
 * Can the widget be displayed?
 *
 * @return bool
 */
function king_filter_displayed() {

    global $woocommerce, $king_attributes_array;

    if ( is_active_widget( false, false, 'king-filter-ajax-navigation', true ) ) {
        return true;
    }
    else {
        return false;
    }
}


if ( ! function_exists( 'king_curPageURL' ) ) {
    /**
     * Retrieve the current complete url
     */
    function king_curPageURL() {
	    global $king;
        $pageURL = 'http';
        if ( isset( $king->ext['sv']('HTTPS') ) AND $king->ext['sv']('HTTPS') == "on" ) {
            $pageURL .= "s";
        }

        $pageURL .= "://";

        if ( isset( $king->ext['sv']('SERVER_PORT') ) AND $king->ext['sv']('SERVER_PORT') != "80" ) {
            $pageURL .= $king->ext['sv']('SERVER_NAME') . ":" . $king->ext['sv']('SERVER_PORT') . $king->ext['sv']('REQUEST_URI');
        }
        else {
            $pageURL .= $king->ext['sv']('SERVER_NAME') . $king->ext['sv']('REQUEST_URI');
        }

        return $pageURL;
    }
}

if ( ! function_exists( 'dev_reorder_terms_by_parent' ) ) {
    /**
     * Sort the array of terms associating the child to the parent terms
     *
     * @param $terms mixed|array
     *
     * @return mixed!array
     */
    function dev_reorder_terms_by_parent( $terms ) {

        /* Extract Child Terms */
        $child_terms = array();
        $terms_count = 0;

        foreach ( $terms as $array_key => $term ) {

            if ( $term->parent != 0 ) {

                if ( isset( $child_terms[$term->parent] ) && $child_terms[$term->parent] != null ) {
                    $child_terms[$term->parent] = array_merge( $child_terms[$term->parent], array( $term ) );
                }
                else {
                    $child_terms[$term->parent] = array( $term );
                }

            }
            else {
                $parent_terms[$terms_count] = $term;
            }
            $terms_count ++;
        }

        /* Reorder Therms */
        $terms_count = 0;
        $terms       = array();

        foreach ( $parent_terms as $term ) {

            $terms[$terms_count] = $term;

            /* The term as child */
            if ( array_key_exists( $term->term_id, $child_terms ) ) {

                foreach ( $child_terms[$term->term_id] as $child_term ) {
                    $terms_count ++;
                    $terms[$terms_count] = $child_term;
                }
            }
            $terms_count ++;
        }

        return $terms;
    }
}

if ( ! function_exists( 'king_get_terms' ) ) {
    /**
     * Get the array of objects terms
     *
     * @param $type A type of term to display
     *
     * @return $terms mixed|array
     */
    function king_get_terms( $case, $taxonomy ) {

        $exclude = apply_filters( 'king_filter_exclude_terms', array() );

        switch ( $case ) {

            case 'all':
                $terms = get_terms( $taxonomy, array( 'hide_empty' => true, 'exclude' => $exclude ) );
                break;

            case 'hierarchical':
                $terms = dev_reorder_terms_by_parent( get_terms( $taxonomy, array( 'hide_empty' => true, 'exclude' => $exclude ) ) );
                break;

            case 'parent' :
                $terms = get_terms( $taxonomy, array( 'hide_empty' => true, 'parent' => false, 'exclude' => $exclude ) );
                break;

            default:
                $terms = get_terms( $taxonomy, array( 'hide_empty' => true, 'exclude' => $exclude ) );
                break;
        }

        return $terms;
    }
}

if ( ! function_exists( 'king_term_is_child' ) ) {
    /**
     * Return true if the term is a child, false otherwise
     *
     * @param $term The term object
     */
    function king_term_is_child( $term ) {

        return ( isset( $term->parent ) && $term->parent != 0 ) ? true : false;
    }
}

if ( ! function_exists( 'dev_term_is_parent' ) ) {
    /**
     * Return true if the term is a parent, false otherwise
     *
     * @param $term The term object
     *
     * @return bool
     *
     */
    function dev_term_is_parent( $term ) {

        return ( isset( $term->parent ) && $term->parent == 0 ) ? true : false;
    }
}

if ( ! function_exists( 'king_term_has_child' ) ) {
    /**
     * Return true if the term has a child, false otherwise
     *
     * @param $term     The term object
     * @param $taxonomy the taxonomy to search
     */
    function king_term_has_child( $term, $taxonomy ) {
        
        global $woocommerce;

        $count       = 0;
        $child_terms = get_terms( $taxonomy, array( 'child_of' => $term->term_id ) );
        foreach ( $child_terms as $term ) {
            $_products_in_term = get_objects_in_term( $term->term_id, $taxonomy );
            $count += sizeof( array_intersect( $_products_in_term, $woocommerce->query->filtered_product_ids ) );
        }

        return empty( $count ) ? false : true;
    }
}