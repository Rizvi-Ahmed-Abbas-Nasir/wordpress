<?php
/**
 * @var $this WPBakeryShortCode_VC_Column
 */
$output = $font_color = $el_class = $width = $offset = '';
extract( shortcode_atts( array(
	'font_color' => '',
	'el_class' => '',
	'el_animate' => '',
	'el_delay' => '',
	'width' => '1/1',
	'css' => '',
	'offset' => ''
), $atts ) );

$el_class = $this->getExtraClass( $el_class );

if( $el_animate != '' ){
	$el_class .= ' '.$el_animate;
}
if( $el_delay != '' ){
	$el_class .= ' delay-'.$el_delay.'ms';
}

$width = wpb_translateColumnWidthToSpan( $width );
$width = vc_column_offset_class_merge( $offset, $width );
$el_class .= ' wpb_column vc_column_container';
$style = $this->buildStyle( $font_color );

$css_classes = array(
	$this->getExtraClass( $el_class ),
	'wpb_column',
	'vc_column_container',
	$width,
	vc_shortcode_custom_css_class( $css, ' '),
);
$css_class = preg_replace( '/\s+/', ' ', apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, implode( ' ', array_filter( $css_classes ) ), $this->settings['base'], $atts ) );


$output .= "\n\t" . '<div class="' . $css_class . ' ' . vc_shortcode_custom_css_class( $css, ' ' ) . '"' . $style . '>';
$output .= "\n\t\t" . '<div class="wpb_wrapper">';
$output .= "\n\t\t\t" . wpb_js_remove_wpautop( $content );
$output .= "\n\t\t" . '</div> ' . $this->endBlockComment( '.wpb_wrapper' );
$output .= "\n\t" . '</div> ' . $this->endBlockComment( $el_class ) . "\n";

global $king;
$king->ext['pr']( $output );