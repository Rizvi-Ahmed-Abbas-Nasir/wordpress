<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $king;

$whmp_re_shortcodes_list = array(
	"whmpress_announcements" => "king_whmpress_announcements_function",
	//"whmpress_pricing_table" => "king_whmpress_pricing_table_function"
);

foreach($whmp_re_shortcodes_list as $shortcode=>$func) {
	$rmSC = 'remove'.'_short'.'code';
	$rmSC( $shortcode );
    $king->ext['asc']($shortcode, $func);
}

function king_whmpress_announcements_function($atts, $content=null){
	extract( shortcode_atts( array(
        'count' => '3',
        'words' => '25',
	), $atts ) );
	
	if (!is_numeric($count)) $count = "3";
    if ($count<1) $count = "3";
    
    if (!is_numeric($words)) $words = "25";
    if ($words<1) $words = "25";
    
    global $wpdb;
    $table_name = $wpdb->prefix."whmpress_announcements";
    $Q = "SELECT * FROM `{$table_name}` WHERE published='1' ORDER BY date DESC LIMIT 0,{$count}";
    $rows = $wpdb->get_results($Q);
    
    $url = rtrim(get_permalink(get_option("cc_whmcs_bridge_pages")),"/")."/?ccce=announcements&id={{id}}";
    
    $return_string = '<div class="whmpress_announcements_wrap">';
    foreach($rows as $row) {
        $row->announcement = explode(" ", $row->announcement);
        array_splice($row->announcement, $words);
        $row->announcement = implode(" ", $row->announcement);
        
        $url = str_replace("{{id}}", $row->id, $url);
        
        $return_string .= "<div class=\"whmpress_announcement one_half\">
          <span class=\"announcement-date\">".mysql2date('M j, Y', $row->date)."</span>
          <a class=\"announcement-id\" href=\"{$url}\"><b>{$row->title}</b></a>
          <p class=\"announcement-summary\">" . $row->announcement ."</p>
        </div>\n";
    }
    $return_string .= '</div>';
	
    return $return_string;
}



function king_whmpress_pricing_table_function( $atts, $content = null ) {
/**
 * Shows pricing table
 * 
 * List of parameters
 * html_template = HTML template file path
 * html_class = HTML class for wrapper
 * html_id = HTML id for wrapper
 * id = relid match from whmcs mysql table
 * billingcycle = Billing cycle e.g. annually, monthly etc.
 * show_price = Display price or not.
 * show_combo = Show combo or not, No, Yes
 * show_button = Show submit button or not
 * currency = Currency for price
 */
 
    extract( shortcode_atts( array(
        'html_template' => '',
        'image' => '',
		'id' => '0',
		'html_class' => 'whmpress whmpress_pricing_table',
		'html_id' => '',
		'billingcycle' => whmpress_get_option("billingcycle"),
        'show_price' => 'Yes',
        'show_combo' => 'No',
        'show_button' => 'Yes',
        'currency' => whmp_get_default_currency_id(),
        "button_text" => "Order",
	), $atts ) );
    
    # Getting data from MySQL
    global $wpdb;
    $Q = "SELECT `name`,`description` FROM `".whmp_get_products_table_name()."` WHERE `id`=$id";
    $row = $wpdb->get_row($Q,ARRAY_A);
    
    # Getting price
    $price = whmpress_price_function( array( "id"=> $id, "billingcycle" => $billingcycle, "currency" => $currency ) );
    
    # Getting description
    $description = trim(strip_tags($row["description"]),"\n");
    $description = explode("\n",$description);
    $description = "<ul>\n<li>". implode("</li><li>",$description). "</li>\n</ul>";
    
    # Getting combo
    $combo = whmpress_order_combo_function( array("id"=>$id, "show_button"=>"No", "currency"=>$currency,) );
    
    # Getting button
    $button = whmpress_order_button_function( array("id"=>$id,"button_text"=>$button_text,"billingcycle"=>$billingcycle, "currency"=>$currency) );
    
    # Check if template file exists in theme folder
    $WHMPress = new WHMPress;
    
    if ( is_file($html_template) ) {
        $OutputString = $WHMPress->read_local_file($html_template);
        
        $decimal_sperator = get_option('decimal_replacement',".");
        $amount = whmpress_price_function( array( "id"=> $id, "billingcycle" => $billingcycle, "currency" => $currency, "prefix"=>"no", "suffix"=>"no", "show_duration"=>"no" ) );
        $totay = explode($decimal_sperator,strip_tags($amount));
        $amount1 = $totay[0];
        $fraction = isset($totay[1])?$totay[1]:"";
        $totay = explode("/", strip_tags($price));
        $duration = $totay[1];
        
        $ReplaceSearch = array(
            "{{product_name}}",
            "{{product_price}}",
            "{{product_description}}",
            "{{product_order_combo}}",
            "{{product_order_button}}",
            "{{image}}",
            "{{prefix}}",
            "{{suffix}}",
            "{{amount}}",
            "{{fraction}}",
            "{{duration}}",
            "{{decimal}}",
        );
        $ReplaceWith = array(
            $row["name"],
            $price,
            $description,
            $combo,
            $button,
            $image,
            whmp_get_currency_prefix($currency),
            whmp_get_currency_suffix($currency),
            $amount1,
            $fraction,
            $duration,
            $decimal_sperator,
        );
        
        # Getting custom fields and adding in output
        $TemplateArray = $WHMPress->get_template_array("whmpress_pricing_table");
        
        foreach($TemplateArray as $custom_field) {
            $ReplaceSearch[] = "{{".$custom_field."}}";
            $val = isset($atts[$custom_field])?$atts[$custom_field]:"";
            #if ( $WHMPress->is_json($val) ) $val = json_decode($val, true);
            $ReplaceWith[] = $val;
        }
        
        $OutputString = str_ireplace($ReplaceSearch, $ReplaceWith, $OutputString);
        return $OutputString;
    } else {
        # Generating OutputString
        $OutputString = "<h3>".$row["name"]."</h3>";
                
        # Check if price is requested or not
        if (strtolower($show_price)=="yes") {
            $OutputString .= '<div class="prices">'.$price.'</div>';
        }
		
		$OutputString .= $description;
        
        # Check if combo is requested or not
        if (strtolower($show_combo)=="yes") {
            $OutputString .= $combo;
        }
        
        # Check if button is requested or not
        if (strtolower($show_button)=="yes") {
            $OutputString .= $button;
        }
        
        # Returning output string with wrapper div
        return "<div id='$html_id' class='$html_class'>".$OutputString."</div>";
    }
}