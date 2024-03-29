<?php
global $king;	
wp_enqueue_script('jquery-ui-accordion');
$output = $title = $interval = $style = $el_class = $collapsible = $disable_keyboard = $active_tab = $icon ='';
//
extract(shortcode_atts(array(
    'title' => '',
    'style' => 1,
    'icon' => 'icon-plus',
    'interval' => 0,
    'el_class' => '',
    'collapsible' => 'no',
    'disable_keyboard' => 'no',
    'active_tab' => '1'
), $atts));

$el_class.= ' king-spoiler-style'.$style;

$el_class = $this->getExtraClass($el_class);
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'wpb_accordion' . $el_class . ' not-column-inherit', $this->settings['base'], $atts );

$output .= "\n\t".'<div class="'.$css_class.'" data-collapsible="'.$collapsible.'" data-vc-disable-keydown="' . ( esc_attr( ( 'yes' == $disable_keyboard ? 'true' : 'false' ) ) ) . '" data-active-tab="'.$active_tab.'">'; //data-interval="'.$interval.'"
$output .= "\n\t\t".'<div class="wpb_wrapper wpb_accordion_wrapper ui-accordion king-accordion-toggle king-spoiler-'.$icon.' king-spoiler-style1">';
$output .= wpb_widget_title(array('title' => $title, 'extraclass' => 'wpb_accordion_heading'));

$output .= "\n\t\t\t".wpb_js_remove_wpautop($content);
$output .= "\n\t\t".'</div> '.$this->endBlockComment('.wpb_wrapper');
$output .= "\n\t".'</div> '.$this->endBlockComment('.wpb_accordion');

$king->ext['pr']( $output );