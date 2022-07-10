<?php
$output = $title = $type = $interval = $el_class = $style = '';
extract( shortcode_atts( array(
	'title' => '',
	'type' => '',
	'interval' => 0,
	'el_class' => ''
), $atts ) );

if( $type == 'owl' ){
	return king_elements::owl_carousel( $content, $el_class );
}else if( $type == 'outline' ){
	return king_elements::outline_slider( $content, $el_class );
}

wp_enqueue_script( 'jquery-ui-tabs' );

$el_class = $this->getExtraClass( $el_class );

$element = 'wpb_tabs';
if ( 'vc_tour' == $this->shortcode ) $element = 'wpb_tour';

// Extract tab titles
preg_match_all( '/vc_tab([^\]]+)/i', $content, $matches, PREG_OFFSET_CAPTURE );
$tab_titles = array();
/**
 * vc_tabs
 *
 */
if ( isset( $matches[1] ) ) {
	$tab_titles = $matches[1];
}
$tabs_nav = '';
$tabs_nav .= '<ul class="ui-tabs-nav king-tabs-nav vc_clearfix">';
foreach ( $tab_titles as $tab ) {
	$tab_atts = shortcode_parse_atts($tab[0]);
	if(isset($tab_atts['title'])) {
		
		$icon = '';
		if( isset( $tab_atts['icon'] ) ){
			$icon = '<i class="'.$tab_atts['icon'].' element-icon"></i> ';
		}else if( isset( $tab_atts['icon_simple_line'] ) ){
			$icon = '<i class="icon-'.$tab_atts['icon_simple_line'].' element-icon"></i> ';
		}else if( isset( $tab_atts['icon_etline'] ) ){
			$icon = '<i class="et-'.$tab_atts['icon_etline'].' element-icon"></i> ';
		}
		
		$tabs_nav .= '<li><a href="#tab-' . ( isset( $tab_atts['tab_id'] ) ? $tab_atts['tab_id'] : sanitize_title( $tab_atts['title'] ) ) . '">' . $icon.$tab_atts['title'] . '</a></li>';
	}
}
$tabs_nav .= '</ul>' . "\n";

$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, trim( $element . ' wpb_content_element ' . $el_class ), $this->settings['base'], $atts );

$output .= "\n\t" . '<div class="king-tabs king-tabs-'.$type.' ' . $css_class . '" data-interval="' . $interval . '">';
$output .= "\n\t\t" . '<div class="wpb_wrapper wpb_tour_tabs_wrapper ui-tabs vc_clearfix">';
$output .= wpb_widget_title( array( 'title' => $title, 'extraclass' => $element . '_heading' ) );
$output .= "\n\t\t\t" . $tabs_nav;

$output .= "\n\t\t\t<div class=\"king-tabs-panes\">" . wpb_js_remove_wpautop( $content ).'</div>';

if ( 'vc_tour' == $this->shortcode ) {
	$output .= "\n\t\t\t" . '<div class="wpb_tour_next_prev_nav vc_clearfix"> <span class="wpb_prev_slide"><a href="#prev" title="' . __( 'Previous tab', 'arkahost' ) . '">' . __( 'Previous tab', 'arkahost' ) . '</a></span> <span class="wpb_next_slide"><a href="#next" title="' . __( 'Next tab', 'arkahost' ) . '">' . __( 'Next tab', 'arkahost' ) . '</a></span></div>';
}

$output .= "\n\t\t" . '</div> ' . $this->endBlockComment( '.wpb_wrapper' );
$output .= "\n\t" . '</div> ' . $this->endBlockComment( $element );

global $king;
$king->ext['pr']( $output );