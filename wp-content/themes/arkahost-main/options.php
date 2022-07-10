<?php


/*
 *
 * Thanks for Leemason-NHP
 * Copyright (c) Options by Leemason-NHP
 *
 *
 * Require the framework class before doing anything else, so we can use the defined urls and dirs
 * Also if running on windows you may have url problems, which can be fixed by defining the framework url first
 *
 */
//define('king_options_URL', site_url('path the options folder'));
if(!class_exists('king_options')){
	locate_template( 'options'.DS.'options.php', true );
}

/*
 *
 * Custom function for filtering the sections array given by theme, good for child themes to override or add to the sections.
 * Simply include this function in the child themes functions.php file.
 *
 * NOTE: the defined constansts for urls, and dir will NOT be available at this point in a child theme, so you must use
 * get_template_directory_uri() if you want to use any of the built in icons
 *
 */
function add_another_section($sections){

	//$sections = array();
	$sections[] = array(
				'title' => __('A Section added by hook', 'arkahost'),
				'desc' => wp_kses( __('<p class="description">This is a section created by adding a filter to the sections array, great to allow child themes, to add/remove sections from the options.</p>', 'arkahost'), array('p'=>array())),
				//all the glyphicons are included in the options folder, so you can hook into them, or link to your own custom ones.
				//You dont have to though, leave it blank for default.
				'icon' => trailingslashit(get_template_directory_uri()).'options/img/glyphicons/glyphicons_062_attach.png',
				//Lets leave this as a blank section, no options just some intro text set above.
				'fields' => array()
				);

	return $sections;

}//function

/*
 *
 * Custom function for filtering the args array given by theme, good for child themes to override or add to the args array.
 *
 */
function change_framework_args($args){

	//$args['dev_mode'] = false;

	return $args;

}//function

/*
 * This is the meat of creating the optons page
 *
 * Override some of the default values, uncomment the args and change the values
 * - no $args are required, but there there to be over ridden if needed.
 *
 *
 */

function setup_framework_options(){

	global $king;

	$args = array();

	$args['dev_mode'] = false;

	$args['google_api_key'] = 'AIzaSyDAnjptHMLaO8exTHk7i8jYPLzygAE09Hg';

	$args['intro_text'] = wp_kses( __('<p>This is the HTML which can be displayed before the form, it isnt required, but more info is always better. Anything goes in terms of markup here, any HTML.</p>', 'arkahost'), array('p'=>array()));

	$args['share_icons']['twitter'] = array(
											'link' => 'http://twitter.com/devnCo',
											'title' => 'Folow me on Twitter'
											);

	$args['show_import_export'] = false;

	$args['opt_name'] = KING_OPTNAME;

	$args['page_position'] = 1001;
	$args['allow_sub_menu'] = false;


	$import_file = ABSPATH.'wp-content'.DS.'themes'.DS.THEME_SLUG.DS.'core'.DS.'sample'.DS.'data.xml';
	$import_html = '';
	if ( file_exists($import_file) ){

		$import_html = '<h2></h2><br /><div class="nhp-opts-section-desc"><p class="description"><a style="font-style: normal;" href="admin.php?page=king-sample-data" class="btn btn_green">One-Click Importer Sample Data</a>  &nbsp; Just click and your website will look exactly our demo (posts, pages, menus, categories, tags, layouts, images, sliders, post-type) </p> <br /></div><hr style="background: #ccc;border: none;height: 1px;"/><br />';

	}

	$sections = array();

	$patterns = array();
	for( $i=1; $i<13; $i++ ){
		$patterns['pattern'.$i.'.png'] = array('title' => 'Background '.$i, 'img' => THEME_URI.'/assets/images/elements/pattern'.$i.'.png');
	}
	for( $i=13; $i<17; $i++ ){
		$patterns['pattern'.$i.'.jpg'] = array('title' => 'Background '.$i, 'img' => THEME_URI.'/assets/images/elements/pattern'.$i.'-small.jpg');
	}

	$listHeaders = array();
	if ( $handle = opendir( THEME_PATH.DS.'templates'.DS.'header' ) ){
		while ( false !== ( $entry = readdir($handle) ) ) {
			if( $entry != '.' && $entry != '..' && strpos($entry, '.php') !== false  ){
				$title  = ucwords( str_replace( '-', ' ', basename( $entry, '.php' ) ) );
				$listHeaders[ $entry ] = array('title' => $title, 'img' => THEME_URI.'/templates/header/thumbnails/'.basename( $entry, '.php' ).'.jpg');
			}
		}
	}

	$listFooters = array();
	if ( $handle = opendir( THEME_PATH.DS.'templates'.DS.'footer' ) ){
		while ( false !== ( $entry = readdir($handle) ) ) {
			if( $entry != '.' && $entry != '..' && strpos($entry, '.php') !== false  ){
				$title  = ucwords( str_replace( '-', ' ', basename( $entry, '.php' ) ) );
				$listFooters[ $entry ] = array('title' => $title, 'img' => THEME_URI.'/templates/footer/thumbnails/'.basename( $entry, '.php' ).'.jpg');
			}
		}
	}

	$sidebars = array( '' => '--Select Sidebar--' );
	
	if( !empty( $king->cfg['sidebars'] ) ){
		foreach( $king->cfg['sidebars'] as $sb ){
			$sidebars[ sanitize_title_with_dashes( $sb ) ] = esc_html( $sb );
		}
	}

$sections[] = array(
	'icon' => king_options_URL.'img/glyphicons/glyphicons_023_cogwheels.png',
	'title' => __('General Settings', 'arkahost'),
	'desc' => wp_kses( __('<p class="description">general configuration options for theme</p>', 'arkahost'), array('p'=>array())),
	'fields' => array(

		array(
			'id' => 'logo',
			'type' => 'upload',
			'title' => __('Upload Logo', 'arkahost'),
			'sub_desc' => __('This will be display as logo at header of every page', 'arkahost'),
			'desc' => __('Upload new or from media library to use as your logo. We recommend that you use images without borders and throughout.', 'arkahost'),
			'std' => THEME_URI.'/assets/images/logo.png'
		),
		array(
			'id' => 'compact_logo',
			'type' => 'upload',
			'title' => __('Upload Compact Logo', 'arkahost'),
			'sub_desc' => __('This will be display replace for first logo when scrolling down. For header 5 only.', 'arkahost'),
			'desc' => __('Upload new or from media library to use as your logo. We recommend that you use images without borders and throughout.', 'arkahost'),
			'std' => THEME_URI.'/assets/images/logo.png'
		),
		array(
			'id' => 'logo_height',
			'type' => 'text',
			'title' => __('Logo Max Height', 'arkahost'),
			'sub_desc' => __('Limit logo\'s size. Eg: 60', 'arkahost'),
			'std' => '45',
			'desc' => 'px',
			'css' => '<?php if($value!="")echo "html body #logo img{max-height: ".$value."px;} html body .navbar-brand img{max-height: ".$value."px;}"; ?>',
		),
		array(
			'id' => 'logo_top',
			'type' => 'text',
			'title' => __('Logo Top Spacing', 'arkahost'),
			'sub_desc' => __('The spacing from the logo to the edge of the page. Eg: 5', 'arkahost'),
			'std' => '5',
			'desc' => 'px',
			'css' => '<?php if($value!="")echo "html body .logo{margin-top: ".$value."px;}"; ?>',
		),
		array(
			'id' => 'favicon',
			'type' => 'upload',
			'title' => __('Upload Favicon', 'arkahost'),
			'std' => THEME_URI.'/favico.png',
			'sub_desc' => __('This will be display at title of browser', 'arkahost'),
			'desc' => __('Upload new or from media library to use as your favicon.', 'arkahost')
		),
		array(
			'id' => 'layout',
			'type' => 'button_set',
			'title' => __('Select Layout', 'arkahost'),
			'desc' => '',
			'options' => array('wide' => 'WIDE','boxed' => 'BOXED'),
			'std' => 'wide'
		),
		array(
			'id' => 'responsive',
			'type' => 'button_set',
			'title' => __('Responsive Support', 'arkahost'),
			'desc' => __('Help display well on all screen size (smartphone, tablet, laptop, desktop...)', 'arkahost'),
			'options' => array('1' => 'Enable','0' => 'Disable'),
			'std' => '1'
		),
		array(
			'id' => 'effects',
			'type' => 'button_set',
			'title' => __('Effects Lazy Load', 'arkahost'),
			'desc' => __('Sections\' effect displaying when scoll over.', 'arkahost'),
			'options' => array('1' => 'Enable','0' => 'Disable'),
			'std' => '1'
		),
		array(
			'id' => 'smoother_scroll',
			'type' => 'button_set',
			'title' => __('Smooth Scroll Effect', 'arkahost'),
			'desc' => __('smoother effect when scrolling pages', 'arkahost'),
			'options' => array('enable' => 'Enable','disable' => 'Disable'),
			'std' => 'enable'
		),
		array(
			'id' => 'breadcrumb',
			'type' => 'select',
			'title' => __('Show Breadcrumb', 'arkahost'),
			'desc' => __('The Breadcrumb on every page', 'arkahost'),
			'options' => array(
				'page_title1 sty13' => 'Yes, Please!',
				'no' => 'No, Thanks!',
				'page_title1' => 'Style 1',
				'page_title1 sty2' => 'Style 2',
				'page_title1 sty3' => 'Style 3',
				'page_title1 sty4' => 'Style 4',
				'page_title1 sty5' => 'Style 5',
				'page_title1 sty6' => 'Style 6',
				'page_title1 sty7' => 'Style 7',
				'page_title1 sty8' => 'Style 8',
				'page_title1 sty9' => 'Style 9',
				'page_title1 sty10' => 'Style 10',
				'page_title1 sty11' => 'Style 11',
				'page_title1 sty12' => 'Style 12',
			),
			'std' => 'page_title'
		),
		array(
			'id' => 'breadcrumb_tag',
			'type' => 'select',
			'title' => __('Breadcrumb Title Tag', 'arkahost'),
			'desc' => __('The html tag for title content. Default is H1', 'arkahost'),
			'options' => array(
				'h1' => 'H1',
				'h2' => 'H2',
				'h3' => 'H3',
				'h4' => 'H4',
				'h5' => 'H5',
				'h6' => 'H6',
				'p' => 'P',
				'span' => 'SPAN',				
			),
			'std' => 'h1'
		),
		
		array(
			'id' => 'breadeli',
			'type' => 'text',
			'title' => __('Breadcrumb Delimiter', 'arkahost'),
			'desc' => __('The symbol in beetwen your Breadcrumbs.', 'arkahost'),
			'std' => '/'
		),
		array(
			'id' => 'breadcrumb_bg',
			'type' => 'upload',
			'title' => __('Breadcrumb Background Image', 'arkahost'),
			'desc' => __('Upload your background image for Breadcrumb', 'arkahost'),
			'std' => '',
			'css' => '<?php if($value!="")echo "#breadcrumb.page_title1{background-image:url(".$value.");}"; ?>}'
		),
		array(
			'id' => 'breadcrumb_padding_top',
			'type' => 'text',
			'title' => __('Breadcrumb Padding Top', 'arkahost'),
			'desc' => __('The padding top of the breadcrumb (px). Examp: 10', 'arkahost'),
			'std' => '',
			'css' => '<?php if($value!="")echo "#breadcrumb{padding-top:".$value."px;"; ?>}'
		),
		array(
			'id' => 'breadcrumb_padding_bottom',
			'type' => 'text',
			'title' => __('Breadcrumb Padding Bottom', 'arkahost'),
			'desc' => __('The padding bottom of the breadcrumb (px). Examp: 10', 'arkahost'),
			'std' => '',
			'css' => '<?php if($value!="")echo ".page_title1 .container > h1{margin-bottom:0;}#breadcrumb{padding-bottom:".$value."px;}"; ?>}'
		),
		array(
			'id' => 'api_server',
			'type' => 'button_set',
			'title' => __('Select API Server', 'arkahost'),
			'desc' => __('Select API in case you have problems importing sample data or install sections', 'arkahost'),
			'options' => array('api.devn.co' => 'API Server 1','api2.devn.co' => 'API Server 2'),
			'std' => 'api.devn.co'
		),
	)
);

$sections[] = array(
	'icon' => king_options_URL.'img/glyphicons/glyphicons_263_bank.png',
	'title' => __('Header Settings', 'arkahost'),
	'desc' => wp_kses( __('<p class="description">Select header & footer layouts, Add custom meta tags, hrefs and scripts to header.</p>', 'arkahost'), array('p'=>array())),
	'fields' => array(

		array(
			'id' => 'header',
			'type' => 'radio_img',
			'title' => __('Select Header', 'arkahost'),
			'sub_desc' => '<br /><br />'.wp_kses( __('Overlap: The header will cover up anything beneath it. <br /> <br />Select header for all pages, You can also go to each page to select specific. This path has located /templates/header/{-file-}', 'arkahost'), array( 'br'=>array() )),
			'options' => $listHeaders,
			'std' => 'default.php'
		),
		array(
			'type' => 'color',
			'id' => 'header_bg',
			'title' =>  __('Header Background Color', 'arkahost'),
			'desc' =>  __(' Background color for header. Leave empty to use default color.', 'arkahost'),
			'css' => '<?php if($value!="")echo ".header,.fixednav3{background-color: ".$value."!important;}"; ?>',
			'std' => ''
		),
		array(
			'type' => 'color',
			'id' => 'compact_header_bg',
			'title' =>  __('Compact Header Background Color', 'arkahost'),
			'desc' =>  __(' Background color for header when scrolldown. Leave empty to use default color.', 'arkahost'),
			'css' => '<?php if($value!="")echo "body.compact .header,body.compact .fixednav3{background-color: ".$value."!important;}"; ?>',
			'std' => ''
		),
		array(
			'type' => 'color',
			'id' => 'header_top_bg',
			'title' =>  __('Top Navigation Background Color', 'arkahost'),
			'desc' =>  __(' Background color for top navigation of header default and header 4. Leave empty to use default color.', 'arkahost'),
			'css' => '<?php if($value!="")echo ".top_nav{background-color: ".$value.";}"; ?>',
			'std' => ''
		),
		array(
			'id' => 'topSocials',
			'type' => 'button_set',
			'title' => __('Display Top Socials', 'arkahost'),
			'options' => array( 'show' => 'Show', 'hide' => 'Hide' ),
			'std' => 'show'
		),
		array(
			'id' => 'topInfoEmail',
			'type' => 'text',
			'title' => __('Your Email', 'arkahost'),
			'sub_desc' => __('Display email at header & footer', 'arkahost'),
			'desc' => '<br />'.__('Leave empty if you do not want to display at front end', 'arkahost'),
			'std' => ''
		),
		array(
			'id' => 'topInfoPhone',
			'type' => 'text',
			'title' => __('Your Phone', 'arkahost'),
			'sub_desc' => __('Display phone at header & footer', 'arkahost'),
			'desc' => '<br />'.__('Leave empty if you do not want to display at front end', 'arkahost'),
			'std' => ''
		),
		array(
			'id' => 'topInfoLiveChat',
			'type' => 'text',
			'title' => __('Link LiveChat', 'arkahost'),
			'sub_desc' => __('Display the link to LiveChat at header', 'arkahost'),
			'desc' => '<br />'.__('Leave empty if you do not want to display at front end', 'arkahost'),
			'std' => ''
		),
		array(
			'id' => 'onclickLiveChat',
			'type' => 'text',
			'title' => __('On Click LiveChat', 'arkahost'),
			'sub_desc' => __('The custom javascript when click on Livechat link.', 'arkahost'),
			'std' => ''
		),
		array(
			'id' => 'topInfoSupport',
			'type' => 'text',
			'title' => __('Top Info Link Support', 'arkahost'),
			'sub_desc' => __('The link to Support at header', 'arkahost'),
			'desc' => '<br />'.__('Leave empty if you do not want to display at front end', 'arkahost'),
			'std' => ''
		),
		array(
			'id' => 'topInfoLogin',
			'type' => 'button_set',
			'title' => __('Login Link', 'arkahost'),
			'desc' => '<br />'.__('Display Link Login at header', 'arkahost'),
			'options' => array( 'wp' => 'Wordpress', 'whmcs' => 'WHMCS Bridge', 'whmpress' => 'WHMPRESS', 'custom' => 'Custom Link', 'hide' => 'Hide' ),
			'std' => 'hide'
		),
		array(
			'id' => 'login_link_custom',
			'type' => 'text',
			'title' => __('Custom Login Link', 'arkahost'),
			'sub_desc' => __('Custom Login Link to your ownload at header.', 'arkahost'),
			'desc' => '<br />'.__('Notice: Must select Login Link type is Custom Link above', 'arkahost'),
			'std' => ''
		),
		array(
			'id' => 'login_link_target',
			'type' => 'button_set',
			'title' => __('Custom Login Link Target', 'arkahost'),
			'desc' => '<br />'.__('Open custom link with new windown or not', 'arkahost'),
			'options' => array( '_blank' => 'New Tab', '_self' => 'Same Window' ),
			'std' => '_blank'
		),
		array(
			'id' => 'topInfoCart',
			'type' => 'button_set',
			'title' => __('Minicart', 'arkahost'),
			'desc' => '<br />'.__('Display minicart in right side of top navigation (Only when Woocommerce plugin has been activated)', 'arkahost'),
			'options' => array( 'show' => 'Show', 'hide' => 'Hide' ),
			'std' => 'hide'
		),
		array(
			'id' => 'searchNav',
			'type' => 'button_set',
			'title' => __('Search box in Menu', 'arkahost'),
			'desc' => '<br />'.__('Display search in right side of main menu', 'arkahost'),
			'options' => array( 'show' => 'Show', 'hide' => 'Hide' ),
			'std' => 'hide'
		),
		array(
			'id' => 'stickymenu',
			'type' => 'button_set',
			'title' => __('Disable Sticky Menu', 'arkahost'),
			'desc' => '<br />'.__('Disable sticket menu when scrolldown.', 'arkahost'),
			'options' => array( '1' => 'Yes', '0' => 'No' ),
			'css' => '<?php if($value=="1")echo "body .header, body.compact .header, body .fixednav3{position:absolute;}"; ?>',
			'std' => '0'
		),
		array(
			'id' => 'slide_menu',
			'type' => 'button_set',
			'title' => __('Enable Slide Menu Mobile', 'arkahost'),
			'desc' => '<br />'.__('Switch to use slide menu replace for dropdown style.', 'arkahost'),
			'options' => array( '1' => 'Yes', '0' => 'No' ),
			'icon' => king_options_URL.'img/slide-menu.png',
			'std' => '0'
		),

		array(
			'id' => 'wpml_top',
			'type' => 'button_set',
			'title' => __('WPML Language Switcher', 'arkahost'),
			'desc' => '<br />'.__('Display WMPL switcher beside social icons', 'arkahost'),
			'options' => array( 'show' => 'Show', 'hide' => 'Hide' ),
			'std' => 'hide'
		),
	)
);

$sections[] = array(
	'icon' => king_options_URL.'img/glyphicons/glyphicons_303_temple_islam.png',
	'title' => __('Footer Settings', 'arkahost'),
	'desc' => wp_kses( __('<p class="description">Select footer layouts, Add analytics embed..etc.. to footer</p>', 'arkahost'), array( 'p' =>array() )),
	'fields' => array(

		array(
			'id' => 'footer',
			'type' => 'radio_img',
			'title' => __('Select Footer', 'arkahost'),
			'sub_desc' => wp_kses( __('<br /><br />Select footer for all pages, You can also go to each page to select specific. This path has located /templates/footer/{-file-}', 'arkahost'), array( 'br'=>array() )),
			'options' => $listFooters,
			'std' => 'empty.php'
		),
		array(
			'id' => 'footerText',
			'type' => 'textarea',
			'title' => __('Footer Text Copyrights', 'arkahost'),
			'std' => 'Copyright &copy; 2018 <a href="'.KING_ARKAHOST.'">ArkaHost.Com</a> - All rights reserved.'
		),
		array(
			'id' => 'footerMap',
			'type' => 'textarea',
			'title' => __('Maps url', 'arkahost'),
			'std' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d99367.70628197653!2d-77.01937306855469!3d38.895607927030454!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89b7c6de5af6e45b%3A0xc2524522d4885d2a!2sWashington%2C+DC%2C+USA!5e0!3m2!1sen!2sin!4v1425716353976',
			'sub_desc' => __('Footer Maps', 'arkahost'),
		),
		array(
			'id' => 'footerTerms',
			'type' => 'text',
			'title' => __('Footer Terms\'s Link', 'arkahost'),
			'std' => '#',
			'desc' => '<br />'.__('Leave empty if you do not want to display at front end', 'arkahost'),
		),
		array(
			'id' => 'footerPrivacy',
			'type' => 'text',
			'title' => __('Footer Privacy\'s Link', 'arkahost'),
			'std' => '#',
			'desc' => '<br />'.__('Leave empty if you do not want to display at front end', 'arkahost'),
		),
		array(
			'id' => 'footerSiteMap',
			'type' => 'text',
			'title' => __('Footer Site Map\'s Link', 'arkahost'),
			'std' => '#',
			'desc' => '<br />'.__('Leave empty if you do not want to display at front end', 'arkahost'),
		),
		array(
			'id' => 'target_footer_links',
			'type' => 'button_set',
			'title' => __('Footer links open new tab', 'arkahost'),
			'options' => array( 'yes' => __('Yes', 'arkahost'), 'no' => __('No', 'arkahost' )),
			'std' => 'no',
		),
		array(
			'id' => 'GAID',
			'type' => 'text',
			'title' => __('Google Analytics ID', 'arkahost'),
			'sub_desc' => __( 'Example: UA-61147719-3', 'arkahost'),
			'desc' => '<br />'.__('Add the tracking code directly to your site', 'arkahost'),

		),
		array(
			'id' => 'need_help_text',
			'type' => 'text',
			'title' => __('Need Help Text', 'arkahost'),
			'desc' => '<br />'.__('Text replace for  "Need Help?" text', 'arkahost'),

		),
		array(
			'id' => 'call_us_text',
			'type' => 'text',
			'title' => __('Call Us 24/7:', 'arkahost'),
			'desc' => '<br />'.__('Text replace for  "Call Us 24/7:" text', 'arkahost'),

		),
		array(
			'id' => 'newsletter_desc',
			'type' => 'text',
			'title' => __('Newsletter Description', 'arkahost'),
			'desc' => '<br />'.__('Text replace for  "Sign up to Newsletter for get special offers" text', 'arkahost'),

		),
		array(
			'id' => 'newsletter_text_input',
			'type' => 'text',
			'title' => __('Newsletter Text Input Placeholder', 'arkahost'),
			'desc' => '<br />'.__('Text replace for  "Please enter your email..." text', 'arkahost'),

		),
		array(
			'id' => 'footer_phone_size',
			'type' => 'text',
			'title' => __('Phone Number Size', 'arkahost'),
			'desc' => '<br />'.__('Change font size of phone number at footer default. Default value: 45', 'arkahost'),
			'css' => '<?php if(!empty($value) && $value!="45")echo "body .footer .ftop .left h1{font-size:{$value}px;}"; ?>',
			'validate' => 'numeric',
			'std' => '45'
		),
		array(
			'type' => 'color',
			'id' => 'copyright_bg',
			'title' =>  __('Copyright Background Color', 'arkahost'),
			'desc' =>  __(' Background color for copyright box in footer. Leave empty to use default color.', 'arkahost'),
			'css' => '<?php if($value!="")echo ".copyrights{background-color: ".$value.";}"; ?>',
			'std' => ''
		),

	)
);

$sections[] = array(
	'icon' => king_options_URL.'img/glyphicons/glyphicons_027_search.png',
	'title' => __('Search Domain', 'arkahost'),
	'desc' => wp_kses( __('<p class="description">Help your choose the method for searching.</p>', 'arkahost'), array('p'=>array(),'strong'=>array(),'br'=>array())),
	'fields' => array(
		array(
			'id' => 'search_method',
			'type' => 'button_set',
			'title' => __('Search Method', 'arkahost'),
			'options' => array('1' => 'Theme Functions', '2' => 'Direct to WHMCS'),
			'std' => '1',
			'sub_desc' => __('If you want to use website without WHMCS Bridge or WHMPRESS just select Direct method', 'arkahost'),
		),
		array(
			'id' => 'search_select_method',
			'type' => 'button_set',
			'title' => __('Search Result Select Button', 'arkahost'),
			'options' => array('1' => 'Theme Functions', '2' => 'Direct to WHMCS', '3' => 'Custom Links'),
			'std' => '1',
			'sub_desc' => __('If you want to redirect customer to WHMCS after search result without WHMCS Bridge just select Direct WHMCS method', 'arkahost'),
		),
		array(
			'id' => 'search_whmcs_url',
			'type' => 'text',
			'title' => __('WHMCS URL', 'arkahost'),
			'desc' => wp_kses( __('The url of your WHMCS Site. Just use for Direct WHMCS method or Select button set to WHMCS directly', 'arkahost'), array('br'=>array())),
		),
		array(
			'id' => 'search_custom_url',
			'type' => 'text',
			'title' => __('Custom Search Result Submit URL', 'arkahost'),
			'desc' => wp_kses( __('The url of your third party in case you want to use other system', 'arkahost'), array('br'=>array())),
		),
		array(
			'id' => 'search_sg_tld',
			'type' => 'text',
			'title' => __('Suggest TLDs domains', 'arkahost'),
			'desc' => wp_kses( __('The list of domains suggestion if result is taken. Exp: .com, .net, .biz, .us, .info', 'arkahost'), array('br'=>array())),
		),
		array(
			'id' => 'whois_servers',
			'type' => 'textarea',
			'title' => __('Extra Whois Servers', 'arkahost'),
			'desc' => '<br />'.__('Add more or overwrite your whois server for search domain. Each line is one server with structure: tld|whois server adress|message when found<br>Example: <br>af|whois.nic.af|Domain Status: No Object Found<br>io|whois.nic.io|is available for purchase', 'arkahost'),

		),
		array(
			'id' => 'custom_param',
			'type' => 'text',
			'title' => __('Custom Param Name', 'arkahost'),
			'desc' => wp_kses( __('If you want to use other third-party order, you can change the param name to passing domain to that system.', 'arkahost'), array('br'=>array())),
		),
	)
);

$sections[] = array(
	'icon' => king_options_URL.'img/glyphicons/glyphicons_236_zoom_in.png',
	'title' => __('SEO', 'arkahost'),
	'desc' => wp_kses( __('<p class="description">Help your site more friendly with Search Engine<br /> After active theme, we will enable all <strong>permalinks</strong> and meta descriptions.</p>', 'arkahost'), array('p'=>array(),'strong'=>array(),'br'=>array())),
	'fields' => array(

		array(
			'id' => 'ogmeta',
			'type' => 'button_set',
			'title' => __('Open Graph Meta', 'arkahost'),
			'options' => array('1' => 'Enable','0' => 'Disable'),
			'std' => '1',
			'sub_desc' => __('elements that describe the object in different ways and are represented by meta tags included on the object page', 'arkahost'),
		),
		array(
			'id' => 'metatag',
			'type' => 'button_set',
			'title' => __('Meta Tag', 'arkahost'),
			'options' => array('1' => 'Enable','0' => 'Disable'),
			'std' => '1',
			'sub_desc' => __('Show meta tags into head of website.', 'arkahost'),
		),
		array(
			'id' => 'homeTitle',
			'type' => 'text',
			'title' => __('Homepage custom title', 'arkahost'),
			'desc' => wp_kses( __('<br />Default is:  <strong>%Site Title% - %Tagline%</strong> from General Settings', 'arkahost'), array('br'=>array())),
			'sub_desc' => __('The title will be displayed in homepage between &lt;title>&lt;/title> tags', 'arkahost'),
		),

		array(
			'id' => 'homeTitleFm',
			'type' => 'select',
			'title' => __('Home Title Format', 'arkahost'),
			'options' => array('1' => 'Blog Name | Blog description','2' => 'Blog description | Blog Name', '3' => 'Blog Name only'),
			'desc' => wp_kses( __('<br />If <b>Homepage custom title</b> not set', 'arkahost'),array('br'=>array(),'b'=>array())),
			'std' => '1'
		),

		array(
			'id' => 'postTitleFm',
			'type' => 'select',
			'title' => __('Single Post Page Title Format', 'arkahost'),
			'options' => array('1' => 'Post title | Blog Name','2' => 'Blog Name | Post title', '3' => 'Post title only'),
			'std' => '1'
		),
		array(
			'id' => 'blogTitle',
			'type' => 'text',
			'title' => __('Blog page custom title', 'arkahost'),
			'desc' => wp_kses( __('<br />Support tags:  <strong>%Site Title%, %Tagline%</strong>', 'arkahost'), array('br'=>array())),
			'sub_desc' => __('The title will be displayed in blog page between &lt;title>&lt;/title> tags', 'arkahost'),
		),
		array(
			'id' => 'archivesTitleFm',
			'type' => 'select',
			'title' => __('Archives Title Format', 'arkahost'),
			'options' => array('1' => 'Category name | Blog Name','2' => 'Blog Name | Category name', '3' => 'Category name only'),
			'std' => '1'
		),

		array(
			'id' => 'titleSeparate',
			'type' => 'text',
			'title' => __('Separate Character', 'arkahost'),
			'sub_desc' => __('a Character to separate BlogName and Post title', 'arkahost'),
			'std' => '|'
		),

		array(
			'id' => 'homeMetaKeywords',
			'type' => 'textarea',
			'title' => __('Home Meta Keywords', 'arkahost'),
			'sub_desc' => __('Add  tags for the search engines and especially Google', 'arkahost'),
		),
		array(
			'id' => 'homeMetaDescription',
			'type' => 'textarea',
			'title' => __('Home Meta Description', 'arkahost'),

		),
		array(
			'id' => 'authorMetaKeywords',
			'type' => 'textarea',
			'title' => __('Author Meta Description', 'arkahost'),
			'std' => 'king-theme.com'
		),
		array(
			'id' => 'contactMetaKeywords',
			'type' => 'textarea',
			'title' => __('Contact Meta Description', 'arkahost'),
			'std' => 'contact@king-theme.com'
		),
		array(
			'id' => 'otherMetaKeywords',
			'type' => 'textarea',
			'title' => __('Other Page Meta Keywords', 'arkahost'),

		),
		array(
			'id' => 'otherMetaDescription',
			'type' => 'textarea',
			'title' => __('Other Page Meta Description', 'arkahost'),

		),
	)

);


$sections[] = array(
	'icon' => king_options_URL.'img/glyphicons/glyphicons_087_log_book.png',
	'title' => __('Blog', 'arkahost'),
	'desc' => __('Blog Settings', 'arkahost'),
	'fields' => array(
		array(
			'id' => 'blog',
			'type' => 'blog'
		)
	)
);

$sections[] = array(
	'icon' => king_options_URL.'img/glyphicons/glyphicons_037_credit.png',
	'title' => __('Newsletter', 'arkahost'),
	'desc' => __('Select your newsletter method on website.','arkahost'),
	'fields' => array(
		array(
			'id' => 'newsletter_method',
			'type' => 'button_set',
			'title' => __('Method', 'arkahost'),
			'options' => array('self' => 'Theme Functions','mc' => 'Mailchimp'),
			'std' => 'self'
		),

		array(
			'id' => 'mc_api',
			'type' => 'text',
			'title' => __('Mailchimp API Key', 'arkahost'),
			'sub_desc' => __('Your API key which you can grab from http://admin.mailchimp.com/account/api/', 'arkahost'),
			'std' => ''
		),

		array(
			'id' => 'mc_list_id',
			'type' => 'text',
			'title' => __('Mailchimp List ID', 'arkahost'),
			'sub_desc' => __('The ID of list which you want to customers signup. You can grab your List Id by going to http://admin.mailchimp.com/lists/ click the "settings" link for the list - the Unique Id is at the bottom of that page. ', 'arkahost'),
			'std' => ''
		),

	)

);

$sections[] = array(
	'icon' => king_options_URL.'img/glyphicons/glyphicons_061_keynote.png',
	'title' => __('Article Settings', 'arkahost'),
	'desc' => wp_kses( __('<p class="description">Settings for Single post or Page</p>', 'arkahost'),array('p'=>array())),
	'fields' => array(
		array(
			'id' => 'display_single_sidebar',
			'type' => 'button_set',
			'title' => __('Display Sidebar', 'arkahost'),
			'options' => array('1' => 'Show','0' => 'Hide'),
			'std' => '1'
		),
		array(
			'id' => 'excerptImage',
			'type' => 'button_set',
			'title' => __('Featured Image', 'arkahost'),
			'sub_desc' => __('Display Featured image before of content', 'arkahost'),
			'options' => array('1' => 'Display','2' => 'Hide'),
			'std' => '1'
		),
		array(
			'id' => 'crop_image',
			'type' => 'button_set',
			'title' => __('Crop Featured Image', 'arkahost'),
			'sub_desc' => __('Crop featured image with size 848 x 300. Default : No - full image size.', 'arkahost'),
			'options' => array('1' => 'Yes','0' => 'No'),
			'std' => '0'
		),
		array(
			'id' => 'post_bred_title',
			'type' => 'button_set',
			'title' => __('Title Breadcrumb Article', 'arkahost'),
			'sub_desc' => __('Select type display of title on post single', 'arkahost'),
			'options' => array('global' => 'Use Default','title' => 'Show post title'),
			'std' => '1'
		),
		array(
			'id' => 'navArticle',
			'type' => 'button_set',
			'title' => __('Next/Prev Article Direction', 'arkahost'),
			'options' => array('1' => 'Show','0' => 'Hide'),
			'std' => '1'
		),
		array(
			'id' => 'showMeta',
			'type' => 'button_set',
			'title' => __('Meta Box', 'arkahost'),
			'options' => array('1' => 'Show','0' => 'Hide'),
			'std' => '1'
		),
		array(
			'id' => 'showAuthorMeta',
			'type' => 'button_set',
			'title' => __('Author Meta', 'arkahost'),
			'options' => array('1' => 'Show','0' => 'Hide'),
			'std' => '1'
		),
		array(
			'id' => 'showDateMeta',
			'type' => 'button_set',
			'title' => __('Date Meta', 'arkahost'),
			'options' => array('1' => 'Show','0' => 'Hide'),
			'std' => '1'
		),
		array(
			'id' => 'showCateMeta',
			'type' => 'button_set',
			'title' => __('Categories Meta', 'arkahost'),
			'options' => array('1' => 'Show','0' => 'Hide'),
			'std' => '1'
		),
		array(
			'id' => 'showCommentsMeta',
			'type' => 'button_set',
			'title' => __('Comments Meta', 'arkahost'),
			'options' => array('1' => 'Show','0' => 'Hide'),
			'std' => '1'
		),
		array(
			'id' => 'showTagsMeta',
			'type' => 'button_set',
			'title' => __('Tags Meta', 'arkahost'),
			'options' => array('1' => 'Show','0' => 'Hide'),
			'std' => '1'
		),
		array(
			'id' => 'showShareBox',
			'type' => 'button_set',
			'title' => __('Share Box', 'arkahost'),
			'sub_desc' => __('Display box socials button below', 'arkahost'),
			'options' => array('1' => 'Show','0' => 'Hide'),
			'std' => '1'
		),
		array(
			'id' => 'showShareFacebook',
			'type' => 'button_set',
			'title' => __('Facebook Button', 'arkahost'),
			'options' => array('1' => 'Show','0' => 'Hide'),
			'std' => '1'
		),
		array(
			'id' => 'showShareTwitter',
			'type' => 'button_set',
			'title' => __('Tweet Button', 'arkahost'),
			'options' => array('1' => 'Show','0' => 'Hide'),
			'std' => '1'
		),
		array(
			'id' => 'showShareGoogle',
			'type' => 'button_set',
			'title' => __('Google Button', 'arkahost'),
			'options' => array('1' => 'Show','0' => 'Hide'),
			'std' => '1'
		),
		array(
			'id' => 'showSharePinterest',
			'type' => 'button_set',
			'title' => __('Pinterest Button', 'arkahost'),
			'options' => array('1' => 'Show','0' => 'Hide'),
			'std' => '1'
		),
		array(
			'id' => 'showShareLinkedin',
			'type' => 'button_set',
			'title' => __('LinkedIn Button', 'arkahost'),
			'options' => array('1' => 'Show','0' => 'Hide'),
			'std' => '1'
		),
		array(
			'id' => 'archiveAboutAuthor',
			'type' => 'button_set',
			'title' => __('About Author', 'arkahost'),
			'options' => array('1' => 'Show','0' => 'Hide'),
			'sub_desc' => __('About author box with avatar and description', 'arkahost'),
			'std' => '1'
		),
		array(
			'id' => 'archiveRelatedPosts',
			'type' => 'button_set',
			'title' => __('Related Posts', 'arkahost'),
			'options' => array('1' => 'Show','0' => 'Hide'),
			'sub_desc' => __('List related posts after the content.', 'arkahost'),
			'std' => '1'
		),
		array(
			'id' => 'archiveNumberofPosts',
			'type' => 'text',
			'title' => __('Number of posts related to show', 'arkahost'),
			'validate' => 'numeric',
			'std' => '3'
		),
		array(
			'id' => 'archiveRelatedQuery',
			'type' => 'button_set',
			'title' => __('Related Query Type', 'arkahost'),
			'options' => array('category' => 'Category','tag' => 'Tag','author'=>'Author'),
			'std' => 'category'
		),

	)

);

$sections[] = array('divide'=>true);


$sections[] = array(
	'icon' => king_options_URL.'img/glyphicons/glyphicons_037_credit.png',
	'title' => __('Dynamic Sidebars', 'arkahost'),
	'desc' => __('You can create unlimited sidebars and use it in any page you want.','arkahost'),
	'fields' => array(
		array(
			'id' => 'sidebars',
			'type' => 'multi_text',
			'title' => __('List of Sidebars Created', 'arkahost'),
			'sub_desc' => __('Add name of sidebar', 'arkahost'),
			'std' => array('Nav Sidebar')
		),
	)

);

$sections[] = array(
	'icon' => king_options_URL.'img/glyphicons/glyphicons_273_drink.png',
	'title' => __('Styling', 'arkahost'),
	'desc' => wp_kses( __('<p class="description">Setting up global style and background</p>', 'arkahost'), array('p'=>array())),
	'fields' => array(
		array(
			'id' => 'colorStyle',
			'type' => 'colorStyle',
			'title' => __('Color Style', 'arkahost'),
			'sub_desc' => __('Predefined Color Skins', 'arkahost'),
			'desc' => __( 'Primary css file has been located at: /wp_content/themes/__name__/assets/css/colors/color-primary.css', 'arkahost' ),
			'std'	=> ''
		),
		array(
			'type' => 'color',
			'id' => 'backgroundColor',
			'title' =>  __('Background Color', 'arkahost'),
			'desc' =>  __(' Background body for layout wide and background box for layout boxed', 'arkahost'),
			'css' => '<?php if($value!="")echo "body{background-color: ".$value.";}"; ?>',
			'std' => '#ffffff'
		),
		array(
			'type' => 'upload',
			'id' => 'backgroundCustom',
			'title' =>  __('Custom Background Image', 'arkahost'),
			'sub_desc' => __('Only be used for Boxed Type.', 'arkahost'),
			'desc' =>  __(' Upload your custom background image, or you can also use the Pattern available below.', 'arkahost'),
			'std' => '',
			'css' => '<?php if($value!="")echo "body{background-image: url(".$value.") !important;}"; ?>'

		),
		array(
			'id' => 'useBackgroundPattern',
			'type' => 'checkbox_hide_below',
			'title' => __('Use Pattern for background', 'arkahost'),
			'sub_desc' => __('Tick on checkbox to show list of Patterns', 'arkahost'),
			'desc' => __('If you do not have background image, you can also use our Pattern.', 'arkahost'),
			'std' => 0,
		),
		array(
			'id' => 'backgroundImage',
			'type' => 'radio_img',
			'title' => __('Select background', 'arkahost'),
			'sub_desc' => __('Only be used for Boxed Type.', 'arkahost'),
			'options' => $patterns,
			'std' => '',
			'css' => '<?php if($value!="")echo "body{background-image: url('.THEME_URI.'/assets/images/elements/".$value.");}"; ?>'
		),
		array(
			'id' => 'linksDecoration',
			'type' => 'select',
			'title' => __('Links Decoration', 'arkahost'),
			'sub_desc' => __('Set decoration for all links.', 'arkahost'),
			'options' => array('default'=>'Default','none'=>'None','underline'=>'Underline','overline'=>'Overline','line-through'=>'Line through'),
			'std' => 'default',
			'css' => '<?php if($value!="")echo "a{text-decoration: ".$value.";}"; ?>'
		),
		array(
			'id' => 'linksHoverDecoration',
			'type' => 'select',
			'title' => __('Links Hover Decoration', 'arkahost'),
			'sub_desc' => __('Set decoration for all links when hover.', 'arkahost'),
			'options' => array('default'=>'Default','none'=>'None','underline'=>'Underline','overline'=>'Overline','line-through'=>'Line through'),
			'std' => 'default',
			'css' => '<?php if($value!="")echo "a:hover{text-decoration: ".$value.";}"; ?>'
		),
		array(
			'id' => 'cssGlobal',
			'type' => 'textarea',
			'title' => __('Global CSS', 'arkahost'),
			'sub_desc' => __('CSS for all screen size, only CSS without &lt;style&gt; tag', 'arkahost'),
			'css' => '<?php if($value!=""){ global $king; $king->ext[\'pr\']( $value ); } ?>'
		),
		array(
			'id' => 'cssTablets',
			'type' => 'textarea',
			'title' => __('Tablets CSS', 'arkahost'),
			'sub_desc' => __('Width from 768px to 985px, only CSS without &lt;style&gt; tag', 'arkahost'),
			'css' => '<?php if($value!="")echo "@media only screen and (min-width: 768px) and (max-width: 985px){".$value."}"; ?>'
		),
		array(
			'id' => 'cssPhones',
			'type' => 'textarea',
			'title' => __('Wide Phones CSS', 'arkahost'),
			'sub_desc' => __('Width from 480px to 767px, only CSS without &lt;style&gt; tag', 'arkahost'),
			'css' => '<?php if($value!="")echo "@media only screen and (min-width: 480px) and (max-width: 767px){".$value."}"; ?>'
		),

	)

);

$sections[] = array(
	'icon' => king_options_URL.'img/glyphicons/glyphicons_107_text_resize.png',
	'title' => __('Typography', 'arkahost'),
	'desc' => wp_kses( __('<p class="description">Set the color, font family, font size, font weight and font style.</p>', 'arkahost'),array('p'=>array())),
	'fields' => array(
		array(
			'id' => 'generalTypography',
			'type' => 'typography',
			'title' => __('General Typography', 'arkahost'),
			'std' => array(),
			'css' => 'body,.dropdown-menu,body p{<?php if($value[color]!="")echo "color:".$value[color].";"; ?><?php if($value[font]!="")echo "font-family:\'".$value[font]."\';"; ?><?php if($value[size]!="")echo "font-size:".$value[size]."px;"; ?><?php if($value[weight]!="")echo "font-weight:".$value[weight].";"; ?><?php if($value[style]!="")echo "font-style:".$value[style].";"; ?>}'
		),
		array(
			'id' => 'generalHoverTypography',
			'type' => 'typography',
			'title' => __('General Link Hover', 'arkahost'),
			'css' => 'body * a:hover, body * a:active, body * a:focus{<?php if($value[color]!="")echo "color:".$value[color]." !important;"; ?><?php if($value[font]!="")echo "font-family:\'".$value[font]."\';"; ?><?php if($value[size]!="")echo "font-size:".$value[size]."px;"; ?><?php if($value[weight]!="")echo "font-weight:".$value[weight].";"; ?><?php if($value[style]!="")echo "font-style:".$value[style].";"; ?>}'
		),
		array(
			'id' => 'mainMenuTypography',
			'type' => 'typography',
			'title' => __('Main Menu', 'arkahost'),
			'css' => 'body .navbar-default .navbar-nav>li>a{<?php if($value[color]!="")echo "color:".$value[color].";"; ?><?php if($value[font]!="")echo "font-family:\'".$value[font]."\' !important;"; ?><?php if($value[size]!="")echo "font-size:".$value[size]."px;"; ?><?php if($value[weight]!="")echo "font-weight:".$value[weight]." !important;"; ?><?php if($value[style]!="")echo "font-style:".$value[style].";"; ?>}'
		),
		array(
			'id' => 'mainMenuHoverTypography',
			'type' => 'typography',
			'title' => __('Main Menu Hover', 'arkahost'),
			'css' => 'body .navbar-default .navbar-nav>li>a:hover,.navbar-default .navbar-nav>li.current-menu-item>a{<?php if($value[color]!="")echo "color:".$value[color]." !important;"; ?><?php if($value[font]!="")echo "font-family:\'".$value[font]."\' !important;"; ?><?php if($value[size]!="")echo "font-size:".$value[size]."px;"; ?><?php if($value[weight]!="")echo "font-weight:".$value[weight].";"; ?><?php if($value[style]!="")echo "font-style:".$value[style].";"; ?>}'
		),
		array(
			'id' => 'mainMenuSubTypography',
			'type' => 'typography',
			'title' => __('Sub Main Menu', 'arkahost'),
			'css' => '.dropdown-menu>li>a{<?php if($value[color]!="")echo "color:".$value[color].";"; ?><?php if($value[font]!="")echo "font-family:\'".$value[font]."\';"; ?><?php if($value[size]!="")echo "font-size:".$value[size]."px;"; ?><?php if($value[weight]!="")echo "font-weight:".$value[weight].";"; ?><?php if($value[style]!="")echo "font-style:".$value[style].";"; ?>}'
		),
		array(
			'id' => 'mainMenuSubHoverTypography',
			'type' => 'typography',
			'title' => __('Sub Main Menu Hover', 'arkahost'),
			'css' => '.dropdown-menu>li>a:hover{<?php if($value[color]!="")echo "color:".$value[color]." !important;"; ?><?php if($value[font]!="")echo "font-family:\'".$value[font]."\';"; ?><?php if($value[size]!="")echo "font-size:".$value[size]."px;"; ?><?php if($value[weight]!="")echo "font-weight:".$value[weight].";"; ?><?php if($value[style]!="")echo "font-style:".$value[style].";"; ?>}'
		),
		array(
			'id' => 'postMetaTypography',
			'type' => 'typography',
			'title' => __('Post Meta', 'arkahost'),
			'std' => array(),
			'css' => '.post_meta_links{<?php if($value[color]!="")echo "color:".$value[color].";"; ?><?php if($value[font]!="")echo "font-family:\'".$value[font]."\';"; ?><?php if($value[size]!="")echo "font-size:".$value[size]."px;"; ?><?php if($value[weight]!="")echo "font-weight:".$value[weight].";"; ?><?php if($value[style]!="")echo "font-style:".$value[style].";"; ?>}'
		),
		array(
			'id' => 'postMatalinkTypography',
			'type' => 'typography',
			'title' => __('Post Meta Link', 'arkahost'),
			'css' => '.post_meta_links li a{<?php if($value[color]!="")echo "color:".$value[color].";"; ?><?php if($value[font]!="")echo "font-family:\'".$value[font]."\';"; ?><?php if($value[size]!="")echo "font-size:".$value[size]."px;"; ?><?php if($value[weight]!="")echo "font-weight:".$value[weight].";"; ?><?php if($value[style]!="")echo "font-style:".$value[style].";"; ?>}'
		),
		array(
			'id' => 'postTitleTypography',
			'type' => 'typography',
			'title' => __('Post Title', 'arkahost'),
			'css' => '.blog_post h3.entry-title a{<?php if($value[color]!="")echo "color:".$value[color].";"; ?><?php if($value[font]!="")echo "font-family:\'".$value[font]."\';"; ?><?php if($value[size]!="")echo "font-size:".$value[size]."px;"; ?><?php if($value[weight]!="")echo "font-weight:".$value[weight].";"; ?><?php if($value[style]!="")echo "font-style:".$value[style].";"; ?>}'
		),
		array(
			'id' => 'postEntryTypography',
			'type' => 'typography',
			'title' => __('Post Entry', 'arkahost'),
			'css' => 'article .blog_postcontent,article .blog_postcontent p{<?php if($value[color]!="")echo "color:".$value[color].";"; ?><?php if($value[font]!="")echo "font-family:\'".$value[font]."\';"; ?><?php if($value[size]!="")echo "font-size:".$value[size]."px;"; ?><?php if($value[weight]!="")echo "font-weight:".$value[weight].";"; ?><?php if($value[style]!="")echo "font-style:".$value[style].";"; ?>}'
		),
		array(
			'id' => 'widgetTitlesTypography',
			'type' => 'typography',
			'title' => __('Widget Titles', 'arkahost'),
			'css' => 'h3.widget-title,#reply-title,#comments-title{<?php if($value[color]!="")echo "color:".$value[color].";"; ?><?php if($value[font]!="")echo "font-family:\'".$value[font]."\';"; ?><?php if($value[size]!="")echo "font-size:".$value[size]."px;"; ?><?php if($value[weight]!="")echo "font-weight:".$value[weight].";"; ?><?php if($value[style]!="")echo "font-style:".$value[style].";"; ?>}'
		),
		array(
			'id' => 'footerWidgetTitlesTypography',
			'type' => 'typography',
			'title' => __('Footer Widgets Titles', 'arkahost'),
			'std'	=> array(),
			'css' => '.footer h3.widget-title{<?php if($value[color]!="")echo "color:".$value[color].";"; ?><?php if($value[font]!="")echo "font-family:\'".$value[font]."\';"; ?><?php if($value[size]!="")echo "font-size:".$value[size]."px;"; ?><?php if($value[weight]!="")echo "font-weight:".$value[weight].";"; ?><?php if($value[style]!="")echo "font-style:".$value[style].";"; ?>}'
		),
		array(
			'id' => 'h1Typography',
			'type' => 'typography',
			'title' => __('H1 Typography', 'arkahost'),
			'std' => array(),
			'css' => '.entry-content h1{<?php if($value[color]!="")echo "color:".$value[color].";"; ?><?php if($value[font]!="")echo "font-family:\'".$value[font]."\';"; ?><?php if($value[size]!="")echo "font-size:".$value[size]."px;"; ?><?php if($value[weight]!="")echo "font-weight:".$value[weight].";"; ?><?php if($value[style]!="")echo "font-style:".$value[style].";"; ?>}'
		),
		array(
			'id' => 'h2Typography',
			'type' => 'typography',
			'title' => __('H2 Typography', 'arkahost'),
			'std' => array(),
			'css' => '.entry-content h2{<?php if($value[color]!="")echo "color:".$value[color].";"; ?><?php if($value[font]!="")echo "font-family:\'".$value[font]."\';"; ?><?php if($value[size]!="")echo "font-size:".$value[size]."px;"; ?><?php if($value[weight]!="")echo "font-weight:".$value[weight].";"; ?><?php if($value[style]!="")echo "font-style:".$value[style].";"; ?>}'
		),
		array(
			'id' => 'h3Typography',
			'type' => 'typography',
			'title' => __('H3 Typography', 'arkahost'),
			'std' => array(),
			'css' => '.entry-content h3{<?php if($value[color]!="")echo "color:".$value[color].";"; ?><?php if($value[font]!="")echo "font-family:\'".$value[font]."\';"; ?><?php if($value[size]!="")echo "font-size:".$value[size]."px;"; ?><?php if($value[weight]!="")echo "font-weight:".$value[weight].";"; ?><?php if($value[style]!="")echo "font-style:".$value[style].";"; ?>}'
		),
		array(
			'id' => 'h4Typography',
			'type' => 'typography',
			'title' => __('H4 Typography', 'arkahost'),
			'std' => array(),
			'css' => '.entry-content h4{<?php if($value[color]!="")echo "color:".$value[color].";"; ?><?php if($value[font]!="")echo "font-family:\'".$value[font]."\';"; ?><?php if($value[size]!="")echo "font-size:".$value[size]."px;"; ?><?php if($value[weight]!="")echo "font-weight:".$value[weight].";"; ?><?php if($value[style]!="")echo "font-style:".$value[style].";"; ?>}'
		),
		array(
			'id' => 'h5Typography',
			'type' => 'typography',
			'title' => __('H5 Typography', 'arkahost'),
			'std' => array(),
			'css' => '.entry-content h5{<?php if($value[color]!="")echo "color:".$value[color].";"; ?><?php if($value[font]!="")echo "font-family:\'".$value[font]."\';"; ?><?php if($value[size]!="")echo "font-size:".$value[size]."px;"; ?><?php if($value[weight]!="")echo "font-weight:".$value[weight].";"; ?><?php if($value[style]!="")echo "font-style:".$value[style].";"; ?>}'
		),
		array(
			'id' => 'h6Typography',
			'type' => 'typography',
			'title' => __('H6 Typography', 'arkahost'),
			'std' => array(),
			'css' => '.entry-content h6{<?php if($value[color]!="")echo "color:".$value[color].";"; ?><?php if($value[font]!="")echo "font-family:\'".$value[font]."\';"; ?><?php if($value[size]!="")echo "font-size:".$value[size]."px;"; ?><?php if($value[weight]!="")echo "font-weight:".$value[weight].";"; ?><?php if($value[style]!="")echo "font-style:".$value[style].";"; ?>}'
		)

	)

);


$sections[] = array(
	'icon' => king_options_URL.'img/glyphicons/glyphicons_050_link.png',
	'title' => __('Social Accounts', 'arkahost'),
	'desc' => __('Set your socials and will be displayed icons at header and footer, Leave blank to hide icons from front-end', 'arkahost'),
	'fields' => array(
		array(
			'id' => 'feed',
			'type' => 'text',
			'title' => __('Your Feed RSS', 'arkahost'),
			'sub_desc' => __('Enter full link e.g: http://yoursite.com/feed', 'arkahost'),
			'std' => 'feed'
		),
		array(
			'id' => 'facebook',
			'type' => 'text',
			'title' => __('Your Facebook Account', 'arkahost'),
			'sub_desc' => __('Social icon will not display if you leave empty', 'arkahost'),
			'std' => 'arkahost'
		),
		array(
			'id' => 'twitter',
			'type' => 'text',
			'title' => __('Your Twitter Account', 'arkahost'),
			'sub_desc' => __('Social icon will not display if you leave empty', 'arkahost'),
			'std' => 'arkahost'
		),
		array(
			'id' => 'google',
			'type' => 'text',
			'title' => __('Your Google+ Account', 'arkahost'),
			'sub_desc' => __('Social icon will not display if you leave empty', 'arkahost'),
			'std' => 'arkahost'
		),
		array(
			'id' => 'linkedin',
			'type' => 'text',
			'title' => __('Your LinkedIn Account', 'arkahost'),
			'sub_desc' => __('Social icon will not display if you leave empty', 'arkahost'),
			'std' => 'arkahost'
		),
		array(
			'id' => 'flickr',
			'type' => 'text',
			'title' => __('Your Flickr Account', 'arkahost'),
			'sub_desc' => __('Social icon will display if you leave empty', 'arkahost'),
			'std' => 'arkahost'
		),
		array(
			'id' => 'pinterest',
			'type' => 'text',
			'title' => __('Your Pinterest Account', 'arkahost'),
			'sub_desc' => __('Social icon will not display if you leave empty', 'arkahost'),
			'std' => 'arkahost'
		),
		array(
			'id' => 'instagram',
			'type' => 'text',
			'title' => __('Your Instagram Account', 'arkahost'),
			'sub_desc' => __('Social icon will not display if you leave empty', 'arkahost'),
			'std' => 'king'
		),
		array(
			'id' => 'youtube',
			'type' => 'text',
			'title' => __('Your Youtube Chanel', 'arkahost'),
			'sub_desc' => __('Social icon will not display if you leave empty', 'arkahost'),
			'std' => 'arkahost'
		)

	)

);


//  Coming soon

$sections[] = array(
	'icon' => king_options_URL.'img/glyphicons/glyphicons_022_fire.png',
	'title' => __('Coming soon', 'arkahost'),
	'desc' => __('Set your socials and will be displayed icons at header and footer, Leave blank to hide icons from front-end', 'arkahost'),
	'fields' => array(
		array(
			'id' => 'cs_logo',
			'type' => 'upload',
			'title' => __('Upload Logo', 'arkahost'),
			'sub_desc' => __('This will be display as logo at header of every page', 'arkahost'),
			'desc' => __('Upload new or from media library to use as your logo. We recommend that you use images without borders and throughout.', 'arkahost'),
			'std' => THEME_URI.'/assets/images/logo.png'
		),
		array(
			'id' => 'cs_text_after_logo',
			'type' => 'text',
			'title' => __('Text after logo', 'arkahost'),
			'sub_desc' => __('Will show "We\'re Launching Soon" if you leave empty', 'arkahost'),
			'std' => 'We\'re Launching Soon'
		),
		array(
			'id' => 'cs_timedown',
			'type' => 'text',
			'title' => __('Date time for countdown', 'arkahost'),
			'sub_desc' => __('Format  "F d, Y H:i:s" for example "October 18, 2019 08:30:30"', 'arkahost'),
			'std' => 'October 18, 2025 08:30:30'
		),
		array(
			'id' => 'cs_description',
			'type' => 'textarea',
			'title' => __('Description', 'arkahost'),
			'std' => 'Our website is under construction. We\'ll be here soon with our new awesome site. Get best experience with this one.'
		),
		array(
			'id' => 'cs_slider1',
			'type' => 'upload',
			'title' => __('Background Slider image 1', 'arkahost'),
			'sub_desc' => __('This will be display as slide at coming soon slider ', 'arkahost'),
			'desc' => __('', 'arkahost'),
			'std' => ''
		),
		array(
			'id' => 'cs_slider2',
			'type' => 'upload',
			'title' => __('Background Slider image 2', 'arkahost'),
			'sub_desc' => __('This will be display as slide at coming soon slider ', 'arkahost'),
			'desc' => __('', 'arkahost'),
			'std' => ''
		),
		array(
			'id' => 'cs_slider3',
			'type' => 'upload',
			'title' => __('Background Slider image 3', 'arkahost'),
			'sub_desc' => __('This will be display as slide at coming soon slider ', 'arkahost'),
			'desc' => __('', 'arkahost'),
			'std' => ''
		),
		array(
			'id' => 'cs_slider4',
			'type' => 'upload',
			'title' => __('Background Slider image 4', 'arkahost'),
			'sub_desc' => __('This will be display as slide at coming soon slider ', 'arkahost'),
			'desc' => __('', 'arkahost'),
			'std' => ''
		),
		array(
			'id' => 'cs_slider5',
			'type' => 'upload',
			'title' => __('Background Slider image 5', 'arkahost'),
			'sub_desc' => __('This will be display as slide at coming soon slider ', 'arkahost'),
			'desc' => __('', 'arkahost'),
			'std' => ''
		),

	)

);



//  Post Types
$sections[] = array(
	'icon' => king_options_URL.'img/glyphicons/glyphicons_145_folder_plus.png',
	'title' => __('Custom Post Types', 'arkahost'),
	'desc' => __('Setting title, slugs for post types', 'arkahost'),
	'fields' => array(
		array(
			'id' => 'our_works_title',
			'type' => 'text',
			'title' => __('Our Works Title', 'arkahost'),
			'sub_desc' => __('This will replace \'Our Works\' menu, breadcrumb text', 'arkahost'),
			'desc' => __('', 'arkahost'),
			'std' => ''
		),
		array(
			'id' => 'our_works_slug',
			'type' => 'text',
			'title' => __('Our Works Slug', 'arkahost'),
			'sub_desc' => __('This will replace /our-works/ on url', 'arkahost'),
			'desc' => __('', 'arkahost'),
			'std' => ''
		),
		array(
			'id' => 'our_works_breadcrumb',
			'type' => 'select',
			'title' => __('Show Our Work Breadcrumb', 'arkahost'),
			'desc' => __('The Breadcrumb on Our Work page', 'arkahost'),
			'options' => array(
				'page_title1 sty13' => 'Yes, Please!',
				'no' => 'No, Thanks!',
				'page_title1' => 'Style 1',
				'page_title1 sty2' => 'Style 2',
				'page_title1 sty3' => 'Style 3',
				'page_title1 sty4' => 'Style 4',
				'page_title1 sty5' => 'Style 5',
				'page_title1 sty6' => 'Style 6',
				'page_title1 sty7' => 'Style 7',
				'page_title1 sty8' => 'Style 8',
				'page_title1 sty9' => 'Style 9',
				'page_title1 sty10' => 'Style 10',
				'page_title1 sty11' => 'Style 11',
				'page_title1 sty12' => 'Style 12',
			),
			'std' => 'page_title'
		),
		array(
			'id' => 'our_works_breadcrumb_bg',
			'type' => 'upload',
			'title' => __('Breadcrumb Our Work Background Image', 'arkahost'),
			'desc' => __('Upload your background image for Breadcrumb Our Work', 'arkahost'),
			'std' => '',
		),
		array(
			'id' => 'our_works_show_link',
			'type' => 'button_set',
			'title' => __('Our Work Read More Links', 'arkahost'),
			'sub_desc' => __('Show/Hiden read more link on projects page', 'arkahost'),
			'options' => array('1' => 'Show','0' => 'Hide'),
			'std' => '1'
		),
		array(
			'id' => 'our_works_show_category',
			'type' => 'button_set',
			'title' => __('Our Work Category Links', 'arkahost'),
			'sub_desc' => __('Show/Hiden categories link on project details page', 'arkahost'),
			'options' => array('1' => 'Show','0' => 'Hide'),
			'std' => '1'
		),
		array(
			'id' => 'our_works_visit_link',
			'type' => 'button_set',
			'title' => __('Our Work Visit Site Link', 'arkahost'),
			'sub_desc' => __('Show/Hiden Viset Site link on project detail page', 'arkahost'),
			'options' => array('1' => 'Show','0' => 'Hide'),
			'std' => '1'
		),
		array(
			'id' => 'our_works_page_title',
			'type' => 'button_set',
			'title' => __('Our Work Page Title', 'arkahost'),
			'sub_desc' => __('Select type display of Our Work page title', 'arkahost'),
			'options' => array('global' => 'Use Default','title' => 'Show Our Work post title'),
			'std' => '1'
		),
		array(
			'id' => 'our_works_main_page',
			'type' => 'pages_select',
			'title' => __('Our Work Main Page', 'arkahost'),
			'sub_desc' => __('Select the page which listing all portfolio items', 'arkahost'),
		),
		array(
			'id' => 'our_works_listing_layout',
			'type' => 'select',
			'title' => __('Our Work Listing Page Layout', 'arkahost'),
			'sub_desc' => __('Select the page which listing all portfolio items', 'arkahost'),
			'options' => array(
				'2' => '2 Columns',
				'3' => '3 Columns',
				'4' => '4 Columns',
				'5' => 'Masonry grid',
			),
			'std' => '2'
		),
		array(
			'id' => 'our_works_sidebar',
			'type' => 'select',
			'title' => __('Our Work Listing Page Sidebar', 'arkahost'),
			'sub_desc' => __('Show/Hiden sidebar for listing portfolios page', 'arkahost'),
			'options' => $sidebars,
			'std' => ''
		),
		array(
			'id' => 'our_team_title',
			'type' => 'text',
			'title' => __('Our Team Title', 'arkahost'),
			'sub_desc' => __('This will replace \'Our Team\' menu, breadcrumb text', 'arkahost'),
			'desc' => __('', 'arkahost'),
			'std' => ''
		),
		array(
			'id' => 'our_team_slug',
			'type' => 'text',
			'title' => __('Our Team Slug', 'arkahost'),
			'sub_desc' => __('This will replace /our-team/ on url', 'arkahost'),
			'desc' => __('', 'arkahost'),
			'std' => ''
		),
		array(
			'id' => 'faq_title',
			'type' => 'text',
			'title' => __('FAQ Title', 'arkahost'),
			'sub_desc' => __('This will replace \'FAQ\' menu, breadcrumb text', 'arkahost'),
			'desc' => __('', 'arkahost'),
			'std' => ''
		),
		array(
			'id' => 'faq_slug',
			'type' => 'text',
			'title' => __('FAQ Slug', 'arkahost'),
			'sub_desc' => __('This will replace /faq/ on url', 'arkahost'),
			'desc' => __('', 'arkahost'),
			'std' => ''
		),
	)

);

$sections[] = array('divide'=>true);

//  Woo Admin
$sections[] = array(
	'icon' => king_options_URL.'img/glyphicons/glyphicons_202_shopping_cart.png',
	'title' => __('WooEcommerce', 'arkahost'),
	'desc' => __('Setting for your Shop!', 'arkahost'),
	'fields' => array(
		array(
			'id' => 'product_number',
			'type' => 'text',
			'title' => __('Number of Products per Page', 'arkahost'),
			'desc' => __('Insert the number of products to display per page.', 'arkahost'),
			'std' => '12'
		),
		array(
			'id' => 'woo_grids',
			'type' => 'select',
			'title' => __('Products per row', 'arkahost'),
			'desc' => __('Set number products per row (for Grids layout)', 'arkahost'),
			'options' => array('4'=>'4 (Shop layout without sidebar)','3'=>'3 (Shop layout with sidebar)'),
			'std' => '3'
		),

		array(
			'id' => 'woo_layout',
			'type' => 'select',
			'title' => __('Shop Layout', 'arkahost'),
			'desc' => __('Set layout for your shop page.', 'arkahost'),
			'options' => array('full'=>'No sidebar - Full width', 'left'=>'With Sidebar on Left', 'right'=>'With Sidebar on Right'),
			'std' => 'left'
		),
		array(
			'id' => 'woo_product_layout',
			'type' => 'select',
			'title' => __('Product Layout', 'arkahost'),
			'desc' => __('Set layout for your product detail page.', 'arkahost'),
			'options' => array('full'=>'No sidebar - Full width', 'left'=>'With Sidebar on Left', 'right'=>'With Sidebar on Right'),
			'std' => 'right'
		),
		array(
			'id' => 'woo_product_display',
			'type' => 'select',
			'title' => __('Product Display', 'arkahost'),
			'desc' => __('Display products by grid or list.', 'arkahost'),
			'options' => array('grid'=>'Grid','list'=>'List'),
			'std' => 'grid'
		),
		array(
			'id' => 'woo_filter',
			'type' => 'button_set',
			'title' => __('Filter Products', 'arkahost'),
			'options' => array('1' => 'Enable','0' => 'Disable'),
			'desc' => __('Enable filter products by price, categories, attributes..', 'arkahost'),
			'std' => '1'
		),
		array(
			'id' => 'woo_related_columns',
			'type' => 'select',
			'title' => __('Product Related per row', 'arkahost'),
			'desc' => __('Set number products per row on related box', 'arkahost'),
			'options' => array('4'=>'4 products','3'=>'3 products'),
			'std' => '3'
		),
		array(
			'id' => 'woo_cart',
			'type' => 'button_set',
			'title' => __('Show Woocommerce Cart Icon in Top Menu', 'arkahost'),
			'options' => array('1' => 'Enable','0' => 'Disable'),
			'desc' => __('Enable Woocommerce Cart show on top menu', 'arkahost'),
			'std' => '1'
		),
		array(
			'id' => 'woo_social',
			'type' => 'button_set',
			'title' => __('Show Woocommerce Social Icons', 'arkahost'),
			'options' => array('1' => 'Enable','0' => 'Disable'),
			'desc' => __('Show Woocommerce Social Icons in Single Product Page', 'arkahost'),
			'std' => '1'
		),		
		array(
			'id' => 'woo_message_1',
			'type' => 'textarea',
			'title' => __('Account Message 1', 'arkahost'),
			'desc' => __('Insert your message to appear in the first message box on the acount page.', 'arkahost'),
			'std' => 'Call us in 000-000-000 If you need our support. Happy to help you !'
		),
		array(
			'id' => 'woo_message_2',
			'type' => 'textarea',
			'title' => __('Account Message 2', 'arkahost'),
			'desc' => __('Insert your message to appear in the second message box on the acount page.', 'arkahost'),
			'std' => 'Send us a email in devn@support.com'
		),

	)

);

//  Woo Breadcrumbs
$sections[] = array(
	'icon' => king_options_URL.'img/glyphicons/glyphicons_202_shopping_cart.png',
	'title' => __('Woo Breadcrumbs', 'arkahost'),
	'desc' => __('Setting for Shop breadcrumb!', 'arkahost'),
	'fields' => array(		
		array(
			'id' => 'woo_cat_breadcrumb',
			'type' => 'select',
			'title' => __('Category', 'arkahost'),
			'desc' => __('The Breadcrumb on products listing page', 'arkahost'),
			'options' => array(
				'' => 'Same as Woocommerce page',				
				'no' => 'No, Thanks!',
				'page_title1' => 'Style 1',
				'page_title1 sty2' => 'Style 2',
				'page_title1 sty3' => 'Style 3',
				'page_title1 sty4' => 'Style 4',
				'page_title1 sty5' => 'Style 5',
				'page_title1 sty6' => 'Style 6',
				'page_title1 sty7' => 'Style 7',
				'page_title1 sty8' => 'Style 8',
				'page_title1 sty9' => 'Style 9',
				'page_title1 sty10' => 'Style 10',
				'page_title1 sty11' => 'Style 11',
				'page_title1 sty12' => 'Style 12',
				'page_title1 sty13' => 'Style 13',
			),
		),
		array(
			'id' => 'woo_cat_breadcrumb_tag',
			'type' => 'select',
			'title' => __('Category Breadcrumb Title Tag', 'arkahost'),
			'desc' => __('The html tag for title content. Default is H1', 'arkahost'),
			'options' => array(
				'h1' => 'H1',
				'h2' => 'H2',
				'h3' => 'H3',
				'h4' => 'H4',
				'h5' => 'H5',
				'h6' => 'H6',
				'p' => 'P',
				'span' => 'SPAN',				
			),
			'std' => 'h1'
		),
		array(
			'id' => 'woo_cat_breadcrumb_bg',
			'type' => 'upload',
			'title' => __('Category Breadcrumb Background Image', 'arkahost'), 
			'std' => '',
			'sub_desc' => __( 'Upload your Breadcrumb background image for category product page.', 'arkahost' )
		),	
		array(
			'id' => 'woo_single_breadcrumb',
			'type' => 'select',
			'title' => __('Product Breadcrumb', 'arkahost'),
			'desc' => __('The Breadcrumb on product single page', 'arkahost'),
			'options' => array(
				'' => 'Same as Woocommerce page',				
				'no' => 'No, Thanks!',
				'page_title1' => 'Style 1',
				'page_title1 sty2' => 'Style 2',
				'page_title1 sty3' => 'Style 3',
				'page_title1 sty4' => 'Style 4',
				'page_title1 sty5' => 'Style 5',
				'page_title1 sty6' => 'Style 6',
				'page_title1 sty7' => 'Style 7',
				'page_title1 sty8' => 'Style 8',
				'page_title1 sty9' => 'Style 9',
				'page_title1 sty10' => 'Style 10',
				'page_title1 sty11' => 'Style 11',
				'page_title1 sty12' => 'Style 12',
				'page_title1 sty13' => 'Style 13',
			),
		),
		array(
			'id' => 'woo_single_breadcrumb_tag',
			'type' => 'select',
			'title' => __('Product Breadcrumb Title Tag', 'arkahost'),
			'desc' => __('The html tag for title content. Default is H1', 'arkahost'),
			'options' => array(
				'h1' => 'H1',
				'h2' => 'H2',
				'h3' => 'H3',
				'h4' => 'H4',
				'h5' => 'H5',
				'h6' => 'H6',
				'p' => 'P',
				'span' => 'SPAN',				
			),
			'std' => 'h1'
		),
		array(
			'id' => 'woo_single_breadcrumb_bg',
			'type' => 'upload',
			'title' => __('Product Breadcrumb Background Image', 'arkahost'), 
			'std' => '',
			'sub_desc' => __( 'Upload your Breadcrumb background image for single product page.', 'arkahost' )
		),	
		array(
			'id' => 'woo_search_breadcrumb',
			'type' => 'select',
			'title' => __('Search Breadcrumb', 'arkahost'),
			'desc' => __('The Breadcrumb on product single page', 'arkahost'),
			'options' => array(
				'' => 'Same as Woocommerce page',				
				'no' => 'No, Thanks!',
				'page_title1' => 'Style 1',
				'page_title1 sty2' => 'Style 2',
				'page_title1 sty3' => 'Style 3',
				'page_title1 sty4' => 'Style 4',
				'page_title1 sty5' => 'Style 5',
				'page_title1 sty6' => 'Style 6',
				'page_title1 sty7' => 'Style 7',
				'page_title1 sty8' => 'Style 8',
				'page_title1 sty9' => 'Style 9',
				'page_title1 sty10' => 'Style 10',
				'page_title1 sty11' => 'Style 11',
				'page_title1 sty12' => 'Style 12',
				'page_title1 sty13' => 'Style 13',
			),
		),
		array(
			'id' => 'woo_search_breadcrumb_tag',
			'type' => 'select',
			'title' => __('Search Breadcrumb Title Tag', 'arkahost'),
			'desc' => __('The html tag for search page. Default is H1', 'arkahost'),
			'options' => array(
				'h1' => 'H1',
				'h2' => 'H2',
				'h3' => 'H3',
				'h4' => 'H4',
				'h5' => 'H5',
				'h6' => 'H6',
				'p' => 'P',
				'span' => 'SPAN',				
			),
			'std' => 'h1'
		),
		array(
			'id' => 'woo_search_breadcrumb_bg',
			'type' => 'upload',
			'title' => __('Search Breadcrumb Background Image', 'arkahost'), 
			'std' => '',
			'sub_desc' => __( 'Upload your Breadcrumb background image for search page.', 'arkahost' )
		),	
	)

);

// Woo Magnifier
$sections[] = array(
	'icon' => king_options_URL.'img/glyphicons/glyphicons_027_search.png',
	'title' => __('Woo Magnifier', 'arkahost'),
	'desc' => __('Setting Magnifier effect for images product in single product page!', 'arkahost'),
	'fields' => array(
		array(
			'id' => 'mg_active',
			'type' => 'button_set',
			'title' => __('Magnifier Active', 'arkahost'),
			'options' => array('1' => 'Enable','0' => 'Disable'),
			'desc' => __('Enable magnifier for product images/ Disable magnifier to use default lightbox for product images', 'arkahost'),
			'std' => '1'
		),
		array(
			'id' => 'mg_zoom_width',
			'type' => 'text',
			'title' => __('Zoom Width', 'arkahost'),
			'desc' => __('Set width of magnifier box ( default: auto )', 'arkahost'),
			'std' => 'auto'
		),
		array(
			'id' => 'mg_zoom_height',
			'type' => 'text',
			'title' => __('Zoom Height', 'arkahost'),
			'desc' => __('Set height of magnifier box ( default: auto )', 'arkahost'),
			'std' => 'auto'
		),
		array(
			'id' => 'mg_zoom_position',
			'type' => 'select',
			'title' => __('Zoom Position', 'arkahost'),
			'desc' => __('Set magnifier position ( default: Right )', 'arkahost'),
			'options' => array('right'=>'Right','inside'=>'Inside'),
			'std' => 'right'
		),
		array(
			'id' => 'mg_zoom_position_mobile',
			'type' => 'select',
			'title' => __('Zoom Position on Mobile', 'arkahost'),
			'desc' => __('Set magnifier position on mobile devices (iPhone, Android, etc.)', 'arkahost'),
			'options' => array('default'=>'Default','inside'=>'Inside','disable'=>'Disable'),
			'std' => 'default'
		),
		array(
			'id' => 'mg_loading_label',
			'type' => 'text',
			'title' => __('Loading Label', 'arkahost'),
			'desc' => __('Set text for magnifier loading...', 'arkahost'),
			'std' => 'Loading...'
		),
		array(
			'id' => 'mg_lens_opacity',
			'type' => 'text',
			'title' => __('Lens Opacity', 'arkahost'),
			'desc' => __('Set opacity for Lens (0 - 1)', 'arkahost'),
			'std' => '0.5'
		),
		array(
			'id' => 'mg_blur',
			'type' => 'button_set',
			'title' => __('Blur Effect', 'arkahost'),
			'options' => array('1' => 'Enable','0' => 'Disable'),
			'desc' => __('Blur effect when Lens hover on product images', 'arkahost'),
			'std' => '1'
		),
		array(
			'id' => 'mg_thumbnail_slider',
			'type' => 'button_set',
			'title' => __('Active Slider', 'arkahost'),
			'options' => array('1' => 'Enable','0' => 'Disable'),
			'desc' => __('Enable slider for product thumbnail images', 'arkahost'),
			'std' => '1'
		),
		array(
			'id' => 'mg_slider_item',
			'type' => 'text',
			'title' => __('Items', 'arkahost'),
			'desc' => __('Number items of Slide', 'arkahost'),
			'default' => 3
		),
		array(
			'id' => 'mg_thumbnail_circular',
			'type' => 'button_set',
			'title' => __('Circular Thumbnail', 'arkahost'),
			'options' => array('1' => 'Enable','0' => 'Disable'),
			'desc' => __('Continue slide as a circle', 'arkahost'),
			'std' => '1'
		),
		array(
			'id' => 'mg_thumbnail_infinite',
			'type' => 'button_set',
			'title' => __('Infinite Thumbnail', 'arkahost'),
			'options' => array('1' => 'Enable','0' => 'Disable'),
			'desc' => __('Back to first image when end of list', 'arkahost'),
			'std' => '1'
		),




	)

);

// Woo Wishlist

$sections[] = array(
	'icon' => king_options_URL.'img/glyphicons/glyphicons_012_heart.png',
	'title' => __('Woo WishList', 'arkahost'),
	'desc' => __('Setting Wishlist features for your Shop page!', 'arkahost'),
	'fields' => array(
		array(
			'id' => 'wl_actived',
			'type' => 'button_set',
			'title' => __('WishList Active', 'arkahost'),
			'options' => array('1' => 'Enable','0' => 'Disable'),
			'desc' => __('Enable WishList features. Be sure that the wishlist page is selected in Admin > Pages Manager', 'arkahost'),
			'std' => '1',
			'default' => '1'
		),
		array(
			'id' => 'wl_cookies',
			'type' => 'button_set',
			'title' => __('Cookies Enable', 'arkahost'),
			'options' => array('1' => 'Enable','0' => 'Disable'),
			'desc' => __('Use cookies instead of sessions. If cookies actived, the wishlist will be available for each not logged user for 30 days. Use the filter king_wcwl_cookie_expiration_time to change the expiration time ( needs timestamp ).', 'arkahost'),
			'std' => '1'
		),
		array(
			'id' => 'wl_title',
			'type' => 'text',
			'title' => __('WishList Title', 'arkahost'),
			'desc' => __('Set WishList page Title for your Shop', 'arkahost'),
			'std' => 'My Wishlist on '.THEME_NAME.' Shop'
		),
		array(
			'id' => 'wl_label',
			'type' => 'text',
			'title' => __('Add to cart label', 'arkahost'),
			'desc' => __('Set label for add to cart button in WishList page.', 'arkahost'),
			'std' => 'Add to Cart'
		),
		array(
			'id' => 'wl_w_label',
			'type' => 'text',
			'title' => __('Add to wishlist label', 'arkahost'),
			'desc' => __('Set label for add to wishlist button in WishList page.', 'arkahost'),
			'std' => 'Add to wishlist'
		),
		array(
			'id' => 'wl_position',
			'type' => 'select',
			'title' => __('Position', 'arkahost'),
			'desc' => __('Set Wishlist position ( default: After Add to Cart )', 'arkahost'),
			'options' => array( 'after-cart' =>'After "Add to cart"','after-thumbnails'=>'After thumbnails', 'after-summary'=>'After summary', 'use-shortcode' => 'Use shortcode'),
			'std' => 'after-cart'
		),

		array(
			'id' => 'wl_redirect',
			'type' => 'button_set',
			'title' => __('Redirect to Cart page', 'arkahost'),
			'options' => array('1' => 'Enable','0' => 'Disable'),
			'desc' => __('Go to Cart page if user click "Add to cart" button in the Wishlist page.', 'arkahost'),
			'std' => '1'
		),
		array(
			'id' => 'wl_remove',
			'type' => 'button_set',
			'title' => __('Remove Wishlist items added to Cart', 'arkahost'),
			'options' => array('1' => 'Enable','0' => 'Disable'),
			'desc' => __('Remove the products from the wishlist if is been added to the Cart.', 'arkahost'),
			'std' => '1'
		),
		array(
			'id' => 'wl_facebook',
			'type' => 'button_set',
			'title' => __('Share on Facebook', 'arkahost'),
			'options' => array('1' => 'Enable','0' => 'Disable'),
			'desc' => __('Share your Wishlist products on Facebook.', 'arkahost'),
			'std' => '1'
		),
		array(
			'id' => 'wl_twitter',
			'type' => 'button_set',
			'title' => __('Tweet on Twitter', 'arkahost'),
			'options' => array('1' => 'Enable','0' => 'Disable'),
			'desc' => __('Tweet your Wishlist products on Twitter.', 'arkahost'),
			'std' => '1'
		),
		array(
			'id' => 'wl_pinterest',
			'type' => 'button_set',
			'title' => __('Pin on Pinterest', 'arkahost'),
			'options' => array('1' => 'Enable','0' => 'Disable'),
			'desc' => __('Pin your Wishlist products on Pinterest.', 'arkahost'),
			'std' => '1'
		),
		array(
			'id' => 'wl_google',
			'type' => 'button_set',
			'title' => __('Share on Google+', 'arkahost'),
			'options' => array('1' => 'Enable','0' => 'Disable'),
			'desc' => __('Share your Wishlist products on Google+.', 'arkahost'),
			'std' => '1'
		),
		array(
			'id' => 'wl_stitle',
			'type' => 'text',
			'title' => __('Socials title', 'arkahost'),
			'desc' => __('Set Social title when sharing.', 'arkahost'),
			'std' => 'My Wishlist on '.THEME_NAME.' Shop'
		),
		array(
			'id' => 'wl_stext',
			'type' => 'text',
			'title' => __('Socials text', 'arkahost'),
			'desc' => __('Facebook, Twitter and Pinterest. Use %wishlist_url% where you want the URL of your wishlist to appear.', 'arkahost'),
			'std' => ''
		),
		array(
			'id' => 'wl_simage',
			'type' => 'text',
			'title' => __('Socials image URL', 'arkahost'),
			'desc' => __('Set socials image URL when sharing.', 'arkahost'),
			'std' => ''
		)
	)

);



$sections[] = array('divide'=>true);
/*
$sections[] = array(
	'id' => 'license',
	'icon' => king_options_URL.'img/glyphicons/glyphicons_044_keys.png',
	'title' => __('Product License Key', 'arkahost'),
	'desc' => __('Submit Theme License Key to get auto-update ArkaHost Theme and Plugins', 'arkahost'),
	'fields' => array(
		array(
			'id' => 'license',
			'type' => 'license'
		),
	)
);
*/
$sections[] = array(
	'id' => 'import-export',
	'icon' => king_options_URL.'img/glyphicons/glyphicons_082_roundabout.png',
	'title' => __('Import / Export', 'arkahost'),
	'desc' => __('Import or Export theme options and widgets data', 'arkahost'),
	'fields' => array(
		array(
			'id' => 'import_data',
			'type' => 'import_data',
			'title' => __('Import From File', 'arkahost'),
			'warning_text' => __( 'WARNING! This will overwrite all existing option values, please proceed with caution!', 'arkahost' ),
			'desc' => __('', 'arkahost')
		),
		array(
			'id' => 'export_data',
			'type' => 'export_data',
			'title' => __('Export To File', 'arkahost'),
			'desc' => __('Here you can copy/download your current option settings. Keep this safe as you can use it as a backup should anything go wrong, or you can use it to restore your settings on this site (or any other site).', 'arkahost')
		),
	)
);


	$tabs = array();

	if (function_exists('wp_get_theme')){
		$theme_data = wp_get_theme();
		$theme_uri = $theme_data->get('ThemeURI');
		$description = $theme_data->get('Description');
		$author = $theme_data->get('Author');
		$version = $theme_data->get('Version');
		$tags = $theme_data->get('Tags');
	}else{
		$theme_data = wp_get_theme(trailingslashit(get_stylesheet_directory()).'style.css');
		$theme_uri = $theme_data['URI'];
		$description = $theme_data['Description'];
		$author = $theme_data['Author'];
		$version = $theme_data['Version'];
		$tags = $theme_data['Tags'];
	}



	if(file_exists(trailingslashit(get_stylesheet_directory()).'README.html')){
		$tabs['theme_docs'] = array(
						'icon' => king_options_URL.'img/glyphicons/glyphicons_071_book.png',
						'title' => __('Documentation', 'arkahost'),
						'content' => nl2br(devnExt::file( 'get', trailingslashit(get_stylesheet_directory()).'README.html'))
						);
	}//if

	global $king_options, $king;

	$king_options = new king_options($sections, $args, $tabs);
	$king->cfg = get_option( $args['opt_name'] );

}//function
add_action('init', 'setup_framework_options', 0);

/*
 *
 * Custom function for the callback referenced above
 *
 */
function video_get_start($field, $value){

	switch( $field['id'] ){
		case 'inspector':
		  echo '<ifr'.'ame width="560" height="315" src="http://www.youtube.com/embed/rO8HYqUUbL8?vq=hd720&rel=0&start=76" frameborder="0" allowfullscreen></ifr'.'ame>';
		break;
		case 'grid':
			echo '<ifr'.'ame width="560" height="315" src="http://www.youtube.com/embed/rO8HYqUUbL8?vq=hd720&rel=0" frameborder="0" allowfullscreen></ifr'.'ame>';
		break;
	}

}//function

/*
 *
 * Custom function for the callback validation referenced above
 *
 */
function validate_callback_function($field, $value, $existing_value){

	$error = false;
	$value =  'just testing';
	$return['value'] = $value;
	if($error == true){
		$return['error'] = $field;
	}
	return $return;

}//function
?>
