<?php
/** @var $this WPBakeryShortCode_VC_Tab */
$output = $title = $tab_id = $icon = $el_class = $bg ='';
$pre_atts = array(
	'tab_id' => '',
	'title' => '',
);
$pre_atts['el_class'] = '';
extract(shortcode_atts($pre_atts, $atts));
wp_enqueue_script('jquery_ui_tabs_rotate');
$style = '';
if( !empty( $atts['bg'] ) ){
	$img = wp_get_attachment_image_src( $atts['bg'], 'large' );
	$style = ' style="background:url('.esc_url($img[0]).') no-repeat center center;background-size: cover;" ';
}
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'king-tabs-pane king-clearfix ui-tabs-panel wpb_ui-tabs-hide ' , $this->settings['base'], $atts ) . $el_class;
$output .= "\n\t\t\t" . '<div id="tab-'. (empty($tab_id) ? sanitize_title( $title ) : $tab_id) .'" '.$style.' class="'.$css_class.'">';
$output .= ($content=='' || $content==' ') ? __("Empty tab. Edit page to add content here.", 'arkahost') : "\n\t\t\t\t" . wpb_js_remove_wpautop($content);
$output .= "\n\t\t\t" . '</div> ' . $this->endBlockComment('.wpb_tab');

global $king;
$king->ext['pr']( $output );