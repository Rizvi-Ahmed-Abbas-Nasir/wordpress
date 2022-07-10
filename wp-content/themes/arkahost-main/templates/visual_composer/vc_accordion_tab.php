<?php
global $king;
$output = $title = '';

extract(shortcode_atts(array(
	'title' => __("Section", 'arkahost')
), $atts));

$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'wpb_accordion_section king-spoiler-content acc-container king-spoiler group', $this->settings['base'], $atts );
$output .= "\n\t\t\t" . '<div class="'.$css_class.'">';
    $output .= "\n\t\t\t\t" . '<h3 class="ui-accordion-header king-spoiler-title acc-trigger"><a href="#'.sanitize_title($title).'"><span class="king-spoiler-icon"></span>'.$title.'</a></h3>';
   		$output .= "\n\t\t\t\t" . '<div class="content ui-accordion-content king-clearfix">';
   		 	$output .= ($content=='' || $content==' ') ? __("Empty section. Edit page to add content here.", 'arkahost') : "\n\t\t\t\t" . wpb_js_remove_wpautop($content);
        $output .= "\n\t\t\t\t" . '</div>';  
    $output .= "\n\t\t\t" . '</div> ' . $this->endBlockComment('.wpb_accordion_section') . "\n";

$king->ext['pr']( $output );