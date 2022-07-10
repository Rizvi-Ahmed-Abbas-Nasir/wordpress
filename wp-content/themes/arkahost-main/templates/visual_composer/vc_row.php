<?php

$output = $king_id = $king_bg_image = $king_bg_repeat = $king_class = $king_class_container = $king_row_type = '';
extract(shortcode_atts(array(
    'king_id'        		=> '',
    'bg_image'       	=> '',
    'king_bg_repeat'      => '',
    'bg_color' 			=> '',
    'king_padding_top'    => '',
    'king_padding_bottom' => '',
    'king_class'   		=> '',
    'king_class_container'   => '',
    'king_row_type'       => '',
    'css' => '',
    'equal_height'       => 0
), $atts));

wp_enqueue_script( 'wpb_composer_front_js' );


$king_class = $this->getExtraClass($king_class);

$style = '';

$has_image = false;
if((int)$bg_image > 0 && ($image_url = wp_get_attachment_url( $bg_image, 'large' )) !== false) {
    $has_image = true;
    $style .= "background-image: url(".$image_url.");";
}
if(!empty($king_bg_repeat) && $has_image) {
    if($king_bg_repeat === 'no-repeat') {
        $style .= 'background-repeat: no-repeat;';
    } elseif($king_bg_repeat === 'repeat-x') {
        $style .= "background-repeat:repeat-x;";
    } elseif($king_bg_repeat === 'repeat-y') {
        $style .= 'background-repeat: repeat-y;';
    } elseif($king_bg_repeat === 'repeat') {
        $style .= 'background-repeat: repeat;';
    } elseif($king_bg_repeat === 'cover') {
        $style .= 'background-repeat: no-repeat;background-size: cover;';
    } elseif($king_bg_repeat === 'center') {
        $style .= 'background-repeat: no-repeat;background-position: center;';
    }
}

if( !empty( $bg_color ) ){
	$style .= 'background-color: '.$bg_color.';';	
}

if($king_padding_top  !='') {
     $style .= 'padding-top: '.$king_padding_top.'px!important;';
}
if($king_padding_bottom !='') {
    $style .= 'padding-bottom: '.$king_padding_bottom.'px!important;';
}

if ($king_id=='') {
    $king_id_rand = rand(100000,900000);
    $king_id = 'king-'.$king_id_rand;
}



$css_class =  	apply_filters( 
					VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG,
					'wpb_row '.$king_class.' '. vc_shortcode_custom_css_class( $css, ' '). ' '.
					( $king_row_type!='container' ? ' '.$king_row_type : '' ), $this->settings['base']
				);	

						 
$output .= '<div id="'.$king_id.'" class="'.$css_class.'"';
if( !empty($style) )$output .= ' style="'.$style.'"';

if ( $equal_height) {
    $output .= ' data-equal-height="true"';
}

$output .= '>';
	
if( $king_row_type != 'container_full' || !empty( $king_class_container ) ){
	$output .= '<div class="'.($king_row_type!='container_full'?'container':'').' '.$king_class_container.'">';
	$output .= wpb_js_remove_wpautop($content);
	$output .= '<div class="clear"></div></div>';
}else{
	$output .= wpb_js_remove_wpautop($content);
}

$output .= '</div>';

global $king;
$king->ext['pr']( $output );