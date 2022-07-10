<?php

/*
*	Register extend component for Visual Composer
*	king-theme.com
*/


if (function_exists('vc_map')) {

	if(!function_exists('king_extend_visual_composer')){
		//define new array width columns. Fixing for Visual Composer 4.9.2
		global $vc_column_width_list;
		$vc_column_width_list = array(
			__( '1 column - 1/12', 'arkahost' )    => '1/12',
			__( '2 columns - 1/6', 'arkahost' )    => '1/6',
			__( '3 columns - 1/4', 'arkahost' )    => '1/4',
			__( '4 columns - 1/3', 'arkahost' )    => '1/3',
			__( '5 columns - 5/12', 'arkahost' )   => '5/12',
			__( '6 columns - 1/2', 'arkahost' )    => '1/2',
			__( '7 columns - 7/12', 'arkahost' )   => '7/12',
			__( '8 columns - 2/3', 'arkahost' )    => '2/3',
			__( '9 columns - 3/4', 'arkahost' )    => '3/4',
			__( '10 columns - 5/6', 'arkahost' )   => '5/6',
			__( '11 columns - 11/12', 'arkahost' ) => '11/12',
			__( '12 columns - 1/1', 'arkahost' )   => '1/1',
		);

		add_action( 'init', 'king_extend_visual_composer' );
		function king_extend_visual_composer(){

			global $vc_column_width_list, $king;
			$vc_is_wp_version_3_6_more = version_compare( preg_replace( '/^([\d\.]+)(\-.*$)/', '$1', get_bloginfo( 'version' ) ), '3.6' ) >= 0;

			vc_map( array(

		        "name" => __("Row", 'arkahost'),
		        "base" => "vc_row",
		        "is_container" => true,
		        "icon" => "icon-wpb-row",
		        "show_settings_on_create" => true,
		        "category" => THEME_NAME.' Theme',
		        "description" => __('Place content elements inside the row', 'arkahost'),
		        "params" => array(
		          array(
		            "type" => "textfield",
		            "heading" => __("ID Name for Navigation", 'arkahost'),
		            "param_name" => "king_id",
		            "description" => __("If this row wraps the content of one of your sections, set an ID. You can then use it for navigation. Ex: work", 'arkahost')
		          ),
				   array(
						'type' => 'radio',
						'heading' => __( 'Equal Height', 'arkahost' ),
						'param_name' => 'equal_height',
						'value' => array(
							'Yes' => 1,
							'No' => 0,
						),
						'std' => '0',
						'description' => ''
					),
		           array(
		            "type" => "attach_image",
		            "heading" => __("Background Image", 'arkahost'),
		            "param_name" => "bg_image",
		            "description" => __("Select backgound color for the row.", 'arkahost')
		          ),
		          array(
		            "type" => "dropdown",
		            "heading" => __('Background Repeat', 'arkahost'),
		            "param_name" => "king_bg_repeat",
		            "value" => array(
		              __("Repeat-Y", 'arkahost') => 'repeat-y',
		              __("Repeat", 'arkahost') => 'repeat',
		              __('No Repeat', 'arkahost') => 'no-repeat',
		              __('Background Size Cover', 'arkahost') => 'cover',
		              __('Background Center', 'arkahost') => 'center',
		              __('Repeat-X', 'arkahost') => 'repeat-x'
		            )
		          ),

		          array(
		            "type" => "colorpicker",
		            "heading" => __('Background Color', 'arkahost'),
		            "param_name" => "bg_color",
		            "description" => __("You can set a color over the background image. You can make it more or less opaque, by using the next setting. Default: white ", 'arkahost')
		          ),
		          array(
		            "type" => "textfield",
		            "heading" => __('Background Color Opacity', 'arkahost'),
		            "param_name" => "king_color_opacity",
		            "description" => __("Set an opacity value for the color(values between 0-100). 0 means no color while 100 means solid color. Default: 70 ", 'arkahost')
		          ),
		          array(
		            "type" => "textfield",
		            "heading" => __("Padding Top", 'arkahost'),
		            "param_name" => "king_padding_top",
		            "description" => __("Enter a value and it will be used for padding-top(px). As an alternative, use the 'Space' element.", 'arkahost')
		          ),
		          array(
		            "type" => "textfield",
		            "heading" => __("Padding Bottom", 'arkahost'),
		            "param_name" => "king_padding_bottom",
		            "description" => __("Enter a value and it will be used for padding-bottom(px). As an alternative, use the 'Space' element.", 'arkahost')
		          ),
		          array(
		            "type" => "textfield",
		            "heading" => __("Container class name", 'arkahost'),
		            "param_name" => "king_class_container",
		            "description" => __("Custom class name for container of this row", 'arkahost')
		          ),
		          array(
		            "type" => "textfield",
		            "heading" => __("Section class name", 'arkahost'),
		            "param_name" => "king_class",
		            "description" => __("Custom class for outermost wrapper.", 'arkahost')
		          ),
		          array(
		            "type" => "dropdown",
		            "heading" => __('Type', 'arkahost'),
		            "param_name" => "king_row_type",
		            "description" => __("Select template full-width if you want to background full of screen", 'arkahost'),
		            "value" => array(
		              __("Content In Container", 'arkahost') => 'container',
		              __("Fullwidth All", 'arkahost')    => 'container_full',
		              __("Parallax", 'arkahost')     => 'parallax'
		            )
		          ),
		          array(
						'type' => 'css_editor',
						'heading' => __( 'Css', 'arkahost' ),
						'param_name' => 'css',
						'group' => __( 'Design options', 'arkahost' )
					),
		        ),
		        "js_view" => 'VcRowView'
		      ) );


		      vc_map( array(
					'name' => 'Row Inner', //Inner Row
					'base' => 'vc_row_inner',
					'content_element' => false,
					'is_container' => true,
					'icon' => 'icon-wpb-row',
					'weight' => 1000,
					'show_settings_on_create' => false,
					'description' => __( 'Place content elements inside the row', 'arkahost' ),
					'params' => array(
						array(
							'type' => 'textfield',
							'heading' => __( 'Extra class name', 'arkahost' ),
							'param_name' => 'king_class',
							'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'arkahost' )
						),
						array(
							'type' => 'textfield',
							'heading' => __( 'Extra class name container', 'arkahost' ),
							'param_name' => 'king_class_container',
						)
					),
					'js_view' => 'VcRowView'
				));


		      vc_map( array(
				'name' => __( 'Column', 'arkahost' ),
				'base' => 'vc_column',
				'is_container' => true,
				'content_element' => false,
				'params' => array(
					array(
						'type' => 'textfield',
						'heading' => __( 'Extra class name', 'arkahost' ),
						'param_name' => 'el_class',
						'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'arkahost' )
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Animate Effect', 'arkahost' ),
						'param_name' => 'el_animate',
						'value' => array(
							'---Select an animate---' => '',
							'Fade In' => 'animated eff-fadeIn',
							'From bottom up' => 'animated eff-fadeInUp',
							'From top down' => 'animated eff-fadeInDown',
							'From left' => 'animated eff-fadeInLeft',
							'From right' => 'animated eff-fadeInRight',
							'Zoom In' => 'animated eff-zoomIn',
							'Bounce In' => 'animated eff-bounceIn',
							'Bounce In Up' => 'animated eff-bounceInUp',
							'Bounce In Down' => 'animated eff-bounceInDown',
							'Bounce In Out' => 'animated eff-bounceInOut',
							'Flip In X' => 'animated eff-flipInX',
							'Flip In Y' => 'animated eff-flipInY',
						),
						'description' => __( 'Select animate effects to show this column when port-viewer scroll over', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Animate Delay', 'arkahost' ),
						'param_name' => 'el_delay',
						'description' => __( 'Delay animate effect after number of mili seconds, e.g: 200 ', 'arkahost' )
					),
					array(
						'type' => 'css_editor',
						'heading' => __( 'Css', 'arkahost' ),
						'param_name' => 'css',
						'group' => __( 'Design options', 'arkahost' )
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Width', 'arkahost' ),
						'param_name' => 'width',
						'value' => $vc_column_width_list,
						'group' => __( 'Width & Responsiveness', 'arkahost' ),
						'description' => __( 'Select column width.', 'arkahost' ),
						'std' => '1/1'
					),
					array(
						'type' => 'column_offset',
						'heading' => __( 'Responsiveness', 'arkahost' ),
						'param_name' => 'offset',
						'group' => __( 'Width & Responsiveness', 'arkahost' ),
						'description' => __( 'Adjust column for different screen sizes. Control width, offset and visibility settings.', 'arkahost' )
					)
				),
				'js_view' => 'VcColumnView'
			) );


			vc_map( array(
				"name" => __( "Column", 'arkahost' ),
				"base" => "vc_column_inner",
				"class" => "",
				"icon" => "",
				"wrapper_class" => "",
				"controls" => "full",
				"allowed_container_element" => false,
				"content_element" => false,
				"is_container" => true,
				"params" => array(
					array(
						"type" => "textfield",
						"heading" => __( "Extra class name", 'arkahost' ),
						"param_name" => "el_class",
						"value" => "",
						"description" => __( "If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'arkahost' )
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Animate Effect', 'arkahost' ),
						'param_name' => 'el_animate',
						'value' => array(
							'---Select an animate---' => '',
							'Fade In' => 'animated eff-fadeIn',
							'From bottom up' => 'animated eff-fadeInUp',
							'From top down' => 'animated eff-fadeInDown',
							'From left' => 'animated eff-fadeInLeft',
							'From right' => 'animated eff-fadeInRight',
							'Zoom In' => 'animated eff-zoomIn',
							'Bounce In' => 'animated eff-bounceIn',
							'Bounce In Up' => 'animated eff-bounceInUp',
							'Bounce In Down' => 'animated eff-bounceInDown',
							'Bounce In Out' => 'animated eff-bounceInOut',
							'Flip In X' => 'animated eff-flipInX',
							'Flip In Y' => 'animated eff-flipInY',
						),
						'description' => __( 'Select animate effects to show this column when port-viewer scroll over', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Animate Delay', 'arkahost' ),
						'param_name' => 'el_delay',
						'description' => __( 'Delay animate effect after number of mili seconds, e.g: 200 ', 'arkahost' )
					),
					array(
						"type" => "css_editor",
						"heading" => __( 'Css', 'arkahost' ),
						"param_name" => "css",
						// "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'arkahost'),
						"group" => __( 'Design options', 'arkahost' )
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Width', 'arkahost' ),
						'param_name' => 'width',
						'value' => $vc_column_width_list,
						'group' => __( 'Width & Responsiveness', 'arkahost' ),
						'description' => __( 'Select column width.', 'arkahost' ),
						'std' => '1/1'
					)
				),
				"js_view" => 'VcColumnView'
			) );

		    vc_map( array(
				'name' => __( 'Codes', 'arkahost' ),
				'base' => 'vc_raw_html',
				'icon' => 'icon-wpb-raw-html',
				'category' => THEME_NAME.' Theme',
				'wrapper_class' => 'clearfix',
				'description' => __( 'Custom code php, html, javascript, css, shortcodes', 'arkahost' ),
				'params' => array(
					array(
						'type' => 'textfield',
						'heading' => __( 'Title', 'arkahost' ),
						'param_name' => 'title',
						'holder' => 'i',
						'description' => __( 'Label will display at VisualComposer admin', 'arkahost' ),
						'admin_label' => true,
					),
					array(
						'type' => 'textarea_raw_html',
						'heading' => __( 'X-Code - PHP, HTML, Javascript, CSS, ShortCodes', 'arkahost' ),
						'param_name' => 'content',
						'holder' => 'div',
						'value' => $king->ext['be']( '<p>This can be run with php, html, css, js, shortcode</p>' ),
						'description' => __( 'Enter your HTML, PHP, JavaScript, Css, Shortcodes.', 'arkahost' ),
						'value' => ''
					),
				)
			));

		    vc_map( array(
				'name' => __( 'FAQs', 'arkahost' ),
				'base' => 'faq',
				'icon' => 'fas fa-question-circle',
				'category' => THEME_NAME.' Theme',
				'wrapper_class' => 'clearfix',
				'description' => __( 'Output FAQs as accordion from faqs post type.', 'arkahost' ),
				'params' => array(
					array(
						'type' => 'multiple',
						'heading' => __( 'Select Categories ( hold ctrl or shift to select multiple )', 'arkahost' ),
						'param_name' => 'category',
						'values' => Aka_Su_Tools::get_terms( 'faq-category', 'slug' ),
						'admin_label' => true,
						'description' => __( 'Select category which you chosen for FAQs', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Amount', 'arkahost' ),
						'param_name' => 'amount',
						'value' => 20,
						'admin_label' => true,
						'description' => __( 'Enter number of FAQs that you want to display. To edit FAQs, go to ', 'arkahost' ).'/wp-admin/edit.php?post_type=faq'
					),
				)
			));


			/* Empty Space Element
			---------------------------------------------------------- */
			$mrt = array( '---Select Margin Top---' => '');
			$mrb = array( '---Select Margin Bottom---' => '');
			for( $i=1; $i <=15; $i++ ){
				$mrt[ $i.'0px'] =  $i.'0px';
				$mrb[ $i.'0px'] =  $i.'0px';
			}
			vc_map( array(
				'name' => __( 'Margin Spacing', 'arkahost' ),
				'base' => 'margin',
				'icon' => 'fas fa-expand-arrows-alt',
				'show_settings_on_create' => true,
				'category' => THEME_NAME.' Theme',
				'description' => __( 'Blank spacing', 'arkahost' ),
				'params' => array(
					array(
						'type' => 'dropdown',
						'heading' => __( 'Margin Top', 'arkahost' ),
						'param_name' => 'margin_top',
						'admin_label' => true,
						'value' => $mrt
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Margin Bottom', 'arkahost' ),
						'param_name' => 'margin_bottom',
						'admin_label' => true,
						'value' => $mrb
					),
				),
			) );

		    vc_map( array(
				'name' => __( 'King Loop', 'arkahost' ),
				'base' => 'king_loop',
				'icon' => 'fas fa-star',
				'category' => THEME_NAME.' Theme',
				'wrapper_class' => 'clearfix',
				'description' => __( 'Output list of item template', 'arkahost' ),
				'params' => array(
					array(
						'type' => 'taxonomy',
						'heading' => __( 'Categories', 'arkahost' ),
						'param_name' => 'category',
						'values' => '',
						'admin_label' => true,
						'description' => __( 'Select Post type & categories  (Hold ctrl or command to select multiple)', 'arkahost' )
					),
					array(
						'type' => 'radio',
						'heading' => __( 'How showing', 'arkahost' ),
						'param_name' => 'showing',
						'value' => array(
							'Normal as Grids &nbsp; &nbsp; ' => 'grid',
							'Showing As Sliders' => 'slider',
						),
						'description' => ''
					),
					array(
						'type' => 'textarea_raw_html',
						'heading' => __( 'Item Format', 'arkahost' ),
						'param_name' => 'format',
						'description' => __( 'Available params: {title}, {position}, {img}, {des}, {link}, {social}, {date}, {category}, {author}, {comment}, {price}, {per}, {submit-link}, {submit-text}, {des-li}, {des-br}, {day}, {month}', 'arkahost' ),
						'value' => ''
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Number of items', 'arkahost' ),
						'param_name' => 'items',
						'value' => 20,
						'admin_label' => true,
						'description' => __( 'Enter number of people to show', 'arkahost' )
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Number per row', 'arkahost' ),
						'param_name' => 'per_row',
						'value' => array(
							'Four' => 4,
							'One' => 1,
							'Two' => 2,
							'Three' => 3,
							'Five' => 5,
						),
						'admin_label' => true,
						'description' => 'Number people display on 1 row'
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Class of Wrapper', 'arkahost' ),
						'param_name' => 'class',
						'value' => '',
						'description' => __( 'Custom class name for wrapper', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Class of Odd Columns', 'arkahost' ),
						'param_name' => 'odd_class',
						'value' => '',
						'description' => __( 'Custom class name for odd columns', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Class of Even Columns', 'arkahost' ),
						'param_name' => 'even_class',
						'value' => '',
						'description' => __( 'Custom class name for even columns', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Image Size ( width x height )', 'arkahost' ),
						'param_name' => 'img_size',
						'value' => '245x245',
						'description' => __( 'Set thumbnail size e.g: 245x245', 'arkahost' )
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Hightlight Column', 'arkahost' ),
						'param_name' => 'highlight',
						'value' => array(
							'Three' => 3,
							'None' => 0,
							'One' => 1,
							'Two' => 2,
							'Four' => 4,
							'Five' => 5,
						),
						'description' => 'Select column to set highlight (using for pricing table)'
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Words Limit', 'arkahost' ),
						'param_name' => 'words',
						'value' => 20,
						'description' => __( 'Limit words you want show as short description', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Offset', 'arkahost' ),
						'param_name' => 'offset',
						'value' => 0,
						'description' => __( 'Set offset to start select sql from', 'arkahost' )
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Order By', 'arkahost' ),
						'param_name' => 'order',
						'value' => array(
							'Descending' => 'DESC',
							'Ascending' => 'ASC'
						),
						'description' => ' &nbsp; '
					)
				)
			));


			 vc_map( array(
				'name' => __( 'Our Team', 'arkahost' ),
				'base' => 'team',
				'icon' => 'fas fa-users',
				'category' => THEME_NAME.' Theme',
				'wrapper_class' => 'clearfix',
				'description' => __( 'Output our team template', 'arkahost' ),
				'params' => array(
					array(
						'type' => 'multiple',
						'heading' => __( 'Select Category ( hold ctrl or shift to select multiple )', 'arkahost' ),
						'param_name' => 'category',
						'values' => Aka_Su_Tools::get_terms( 'our-team-category', 'slug' ),
						'height' => '150px',
						'description' => __( 'Select category to display team', 'arkahost' )
					),
					array(
						'type' => 'select',
						'heading' => __( 'Select Template', 'arkahost' ),
						'param_name' => 'template',
						'admin_label' => true,
						'values' => Aka_Su_Tools::get_templates( 'team' )
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Amount', 'arkahost' ),
						'param_name' => 'items',
						'value' => 20,
						'description' => __( 'Enter number of people to show', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Words Limit', 'arkahost' ),
						'param_name' => 'words',
						'value' => 20,
						'description' => __( 'Limit words you want show as short description', 'arkahost' )
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Order By', 'arkahost' ),
						'param_name' => 'order',
						'value' => array(
							'Descending' => 'desc',
							'Ascending' => 'asc'
						),
						'description' => ' &nbsp; '
					)
				)
			));

			vc_map( array(
				'name' => __( 'Our Work (Portfolio)', 'arkahost' ),
				'base' => 'work',
				'icon' => 'fas fa-location-arrow',
				'category' => THEME_NAME.' Theme',
				'wrapper_class' => 'clearfix',
				'description' => __( 'Our work for portfolio template.', 'arkahost' ),
				'params' => array(
					array(
						'type' => 'multiple',
						'heading' => __( 'Select Categories ( hold ctrl or shift to select multiple )', 'arkahost' ),
						'param_name' => 'tax_term',
						'values' => Aka_Su_Tools::get_terms( 'our-works-category', 'slug' ),
						'height' => '120px',
						'admin_label' => true,
						'description' => __( 'Select category which you chosen for Team items', 'arkahost' )
					),
					array(
						'type' => 'select',
						'heading' => __( 'Select Template', 'arkahost' ),
						'param_name' => 'template',
						'admin_label' => true,
						'values' => Aka_Su_Tools::get_templates( 'work' )
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Show Filter', 'arkahost' ),
						'param_name' => 'filter',
						'value' => array(
							'No'	=> 'No',
							'Yes'	=> 'Yes',
						),
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Item Margin', 'arkahost' ),
						'param_name' => 'margin',
						'value' => array(
							'Yes'	=> 'Yes',
							'No'	=> 'No',
						),
					),
					array(
						'type' => 'select',
						'heading' => __( 'Items on Row', 'arkahost' ),
						'param_name' => 'column',
						'values' => array(
							'2' => 'two',
							'3' => 'three',
							'4' => 'four',
							//'5' => 'five',
						),
						'description' => __( 'Choose number of items display on a row', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Items Limit', 'arkahost' ),
						'param_name' => 'items',
						'value' => get_option( 'posts_per_page' ),
						'description' => __( 'Specify number of team that you want to show. Enter -1 to get all team', 'arkahost' )
					),
					array(
						'type' => 'select',
						'heading' => __( 'Order By', 'arkahost' ),
						'param_name' => 'order',
						'values' => array(
								'desc' => __( 'Descending', 'arkahost' ),
								'asc' => __( 'Ascending', 'arkahost' )
						),
						'description' => ' &nbsp; '
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Class Css', 'arkahost' ),
						'param_name' => 'class',
						'value' => '',
					),
				)
			));

			vc_map( array(
				'name' => __( 'Testimonials', 'arkahost' ),
				'base' => 'testimonials',
				'icon' => 'fas fa-layer-group',
				'category' => THEME_NAME.' Theme',
				'wrapper_class' => 'clearfix',
				'description' => __( 'Out testimonians post type.', 'arkahost' ),
				'params' => array(
					array(
						'type' => 'multiple',
						'heading' => __( 'Select Categories ( hold ctrl or shift to select multiple )', 'arkahost' ),
						'param_name' => 'category',
						'values' => Aka_Su_Tools::get_terms( 'testimonials-category', 'slug' ),
						'height' => '120px',
						'admin_label' => true,
						'description' => __( 'Select category which you chosen for Team items', 'arkahost' )
					),
					array(
						'type' => 'select',
						'heading' => __( 'Select Template', 'arkahost' ),
						'param_name' => 'template',
						'admin_label' => true,
						'values' => Aka_Su_Tools::get_templates( 'testimonial' )
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Items Limit', 'arkahost' ),
						'param_name' => 'items',
						'value' => get_option( 'posts_per_page' ),
						'description' => __( 'Specify number of team that you want to show. Enter -1 to get all', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Limit Words', 'arkahost' ),
						'param_name' => 'words',
						'value' => 20,
						'description' => __( 'Limit words you want show as short description', 'arkahost' )
					),
					array(
						'type' => 'select',
						'heading' => __( 'Order By', 'arkahost' ),
						'param_name' => 'order',
						'values' => array(
								'desc' => __( 'Descending', 'arkahost' ),
								'asc' => __( 'Ascending', 'arkahost' )
						),
						'description' => ' &nbsp; '
					)
				)
			));

			vc_map( array(

				'name' => __( 'Pie Chart', 'arkahost' ),
				'base' => 'piechart',
				'icon' => 'fas fa-chart-pie',
				'category' => THEME_NAME.' Theme',
				'wrapper_class' => 'clearfix',
				'description' => __( 'Out testimonians post type.', 'arkahost' ),
				'params' => array(

					array(
						'type' => 'select',
						'param_name' => 'size',
						'values' => array(
							'1' => '1',
							'2' => '2',
							'3' => '3',
							'4' => '4',
							'5' => '5',
							'6' => '6',
							'7' => '7',
							'8' => '8',
						),
						'value' => 7,
						'heading' => __( 'Size', 'arkahost' ),
						'description' => __( 'Size of chart', 'arkahost' )
					),

					array(
						'type' => 'select',
						'param_name' => 'style',
						'values' => array(
							'piechart1' => 'Pie Chart 1',
							'piechart2' => 'Pie Chart 2 (auto width by size)',
							'piechart3' => 'Pie Chart 3 (white color)'
						),
						'value' => 7,
						'heading' => __( 'Size', 'arkahost' ),
						'description' => __( 'Size of chart', 'arkahost' )
					),
					array(
						'param_name' => 'percent',
						'type' 	=> 'textfield',
						'value' => 75,
						'admin_label' => true,
						'heading' => __( 'Percent', 'arkahost' ),
						'description' => __( 'Percent value of chart', 'arkahost' )
					),
					array(
			            "type" => "colorpicker",
			            "heading" => __('Color', 'arkahost'),
			            "param_name" => "color",
			            "description" => __("Color of chart", 'arkahost')
			        ),
					array(
						'param_name' => 'text',
						'type' 	=> 'textfield',
						'heading' => __( 'Text', 'arkahost' ),
						'description' => __( 'The text bellow chart', 'arkahost' ),
						'admin_label' => true,
					),
					array(
						'param_name' => 'class',
						'type' 	=> 'textfield',
						'heading' => __( 'Class', 'arkahost' ),
						'description' => __( 'Extra CSS class', 'arkahost' )
					)

				)
			));

			vc_map( array(

				'name' => __( 'Pricing Table', 'arkahost' ),
				'base' => 'pricing',
				'icon' => 'fas fa-table',
				'category' => THEME_NAME.' Theme',
				'wrapper_class' => 'clearfix',
				'description' => __( 'Display Pricing Plan Table', 'arkahost' ),
				'params' => array(
					array(
						'type' => 'select',
						'heading' => __( 'Select Categories ( hold ctrl or shift to select multiple )', 'arkahost' ),
						'param_name' => 'category',
						'values' => Aka_Su_Tools::get_terms( 'pricing-tables-category', 'slug', null, '---Select Category---' ),
						'admin_label' => true,
						'description' => __( 'Select category which you chosen for Pricing Table', 'arkahost' )
					),
					array(
						'type' => 'select',
						'param_name' => 'amount',
						'values' => array(
								'1' => '1',
								'2' => '2',
								'3' => '3',
								'4' => '4',
								'5' => '5',
						),
						'value' => 4,
						'heading' => __( 'Amount', 'arkahost' ),
						'description' => __( 'Number of columns', 'arkahost' )
					),
					array(
						'type' => 'select',
						'heading' => __( 'Select Template', 'arkahost' ),
						'param_name' => 'template',
						'admin_label' => true,
						'values' => Aka_Su_Tools::get_templates( 'pricing' )
					),
					array(
						'param_name' => 'icon',
						'type' 	=> 'icon',
						'heading' => __( 'Icon', 'arkahost' ),
						'description' => __( 'the icon display on per row', 'arkahost' )
					),
					array(
						'param_name' => 'currency',
						'type' 	=> 'textfield',
						'heading' => __( 'Currency', 'arkahost' ),
						'description' => __( 'The currency icon displayed next to the price', 'arkahost' )
					),
					array(
						'param_name' => 'class',
						'type' 	=> 'textfield',
						'heading' => __( 'Class', 'arkahost' ),
						'description' => __( 'Extra CSS class', 'arkahost' )
					)

				)
			));

			vc_map( array(

				'name' => __( 'Progress Bars', 'arkahost' ),
				'base' => 'progress',
				'icon' => 'fas fa-chart-line',
				'category' => THEME_NAME.' Theme',
				'wrapper_class' => 'clearfix',
				'description' => __( 'Display Progress Bars', 'arkahost' ),
				'params' => array(

					array(
						'type' => 'select',
						'param_name' => 'style',
						'values' => array(
								'1' => '1',
								'2' => '2',
								'3' => '3',
								'4' => '4',
						),
						'heading' => __( 'Style', 'arkahost' ),
						'description' => __( 'Style of progress bar', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'param_name' => 'percent',
						'value' => 75,
						'admin_label' => true,
						'heading' => __( 'Percent', 'arkahost' ),
						'description' => __( 'Percent value of progress bar', 'arkahost' )
					),
					array(
						'type' => 'colorpicker',
						'param_name' => 'color',
						'value' => '#333333',
						'heading' => __( 'Color', 'arkahost' ),
						'description' => __( 'Color of progress bar', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'param_name' => 'text',
						'admin_label' => true,
						'heading' => __( 'Text', 'arkahost' ),
						'description' => __( 'The text bellow chart', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'param_name' => 'class',
						'heading' => __( 'Class', 'arkahost' ),
						'description' => __( 'Extra CSS class', 'arkahost' )
					)

				)
			));

			vc_map( array(

				'name' => __( 'Divider', 'arkahost' ),
				'base' => 'divider',
				'icon' => 'icon-wpb-ui-separator',
				'category' => THEME_NAME.' Theme',
				'wrapper_class' => 'clearfix',
				'description' => __( 'List of horizontal divider line', 'arkahost' ),
				'params' => array(

					array(
						'type' => 'select',
						'param_name' => 'style',
						'values' => array(
								'1' => 'Style 1',
								'2' => 'Style 2',
								'3' => 'Style 3',
								'4' => 'Style 4',
								'5' => 'Style 5',
								'6' => 'Style 6',
								'7' => 'Style 7',
								'8' => 'Style 8',
								'9' => 'Style 9',
								'10' => 'Style 10',
								'11' => 'Style 11',
								'12' => 'Style 12',
								'13' => 'Style 13',
								' ' => 'Divider Line',
						),
						'admin_label' => true,
						'heading' => __( 'Style', 'arkahost' ),
						'description' => __( 'Style of divider', 'arkahost' )
					),
					array(
						'type' => 'icon',
						'param_name' => 'icon',
						'heading' => __( 'Icon', 'arkahost' ),
						'description' => __( 'Select icon on divider', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'param_name' => 'class',
						'heading' => __( 'Class', 'arkahost' ),
						'description' => __( 'Extra CSS class', 'arkahost' )
					)

				)
			));

			vc_map( array(

				'name' => __( 'Title Styles', 'arkahost' ),
				'base' => 'titles',
				'category' => THEME_NAME.' Theme',
				'wrapper_class' => 'clearfix',
				'icon' => 'fas fa-university',
				'description' => __( 'List of Title Styles', 'arkahost' ),
				'params' => array(

					array(
						'type' => 'select',
						'param_name' => 'type',
						'values' => array(
								'h1' => 'H1',
								'h2' => 'H2',
								'h3' => 'H3',
								'h4' => 'H4',
								'h5' => 'H5',
								'h6' => 'H6',
								'strong' => 'Strong',
						),
						'admin_label' => true,
						'heading' => __( 'Head Tag', 'arkahost' ),
						'description' => __( 'Select Header Tag', 'arkahost' )
					),
					array(
						'type' => 'textarea_raw_html',
						'param_name' => 'text',
						'heading' => __( 'Title Text', 'arkahost' ),
						'holder' => 'div',
						'value' => ''
					),
					array(
						'type' => 'select',
						'param_name' => 'text_align',
						'values' => array(
							'' => '--Select Align--',
							'left' =>'Left',
							'right' =>'Right',
							'center' =>'Center',
							'justify' =>'Justify',
						),
						'heading' => __( 'Text Align', 'arkahost' ),
						'description' => __( 'Select text alignment.', 'arkahost' )
					),
					array(
						'type' => 'font_container',
						'param_name' => 'font_container',
						'value' => '',
						'settings' => array(
							'fields' => array(
								'color',
								'font_size',
								'line_height',
								'font_size_description' => __( 'Enter font size.', 'arkahost' ),
								'line_height_description' => __( 'Enter line height.', 'arkahost' ),
								'color_description' => __( 'Select color for your element.', 'arkahost' ),
							),
						)
					),
					array(
						'type' => 'select',
						'param_name' => 'effect',
						'values' => array(
							'' => '--Select Effect--',
							' animated eff-fadeIn delay-200ms' =>'Fade In',
							' animated eff-fadeInUp delay-200ms' =>'From bottom up',
							' animated eff-fadeInUp delay-300ms' =>'From bottom up 300ms',
							' animated eff-fadeInDown delay-200ms' =>'From top down',
							' animated eff-fadeInLeft delay-200ms' =>'From left',
							' animated eff-fadeInRight delay-200ms' =>'From right',
							' animated eff-zoomIn delay-200ms' =>'Zoom In',
							' animated eff-bounceIn delay-200ms' =>'Bounce In',
							' animated eff-bounceInUp delay-200ms' =>'Bounce In Up',
							' animated eff-bounceInDown delay-200ms' =>'Bounce In Down',
							' animated eff-bounceInOut delay-200ms' =>'Bounce In Out',
							' animated eff-flipInX delay-200ms' =>'Flip In X',
							' animated eff-flipInY delay-200ms' =>'Flip In Y',
						),
						'heading' => __( 'Effect', 'arkahost' ),
						'description' => __( 'The effect showing when scroll over', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'param_name' => 'class',
						'heading' => __( 'Class', 'arkahost' ),
						'description' => __( 'CSS class', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'param_name' => 'wrpclass',
						'heading' => __( 'Wrapper Class', 'arkahost' ),
						'description' => __( 'Extra CSS class of wrapper', 'arkahost' )
					)

				)
			));

			vc_map( array(

				'name' => __( 'Flip Clients', 'arkahost' ),
				'base' => 'flip_clients',
				'icon' => 'fab fa-apple',
				'category' => THEME_NAME.' Theme',
				'wrapper_class' => 'clearfix',
				'description' => __( 'Display clients with flip styles', 'arkahost' ),
				'params' => array(

					array(
						'type' => 'attach_image',
						'param_name' => 'img',
						'heading' => __( 'Logo Image', 'arkahost' ),
						'description' => __( 'Upload the client\'s logo', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'param_name' => 'title',
						'heading' => __( 'Title', 'arkahost' ),
						'admin_label' => true,
						'description' => __( 'The name of client', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'param_name' => 'link',
						'heading' => __( 'Link', 'arkahost' ),
						'description' => __( 'Link to client website', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'param_name' => 'des',
						'heading' => 'Description',
						'description' => __( 'Short Descript will show when hover', 'arkahost' ),
						'admin_label' => true,
					),
					array(
						'type' => 'textfield',
						'param_name' => 'class',
						'heading' => __( 'Class', 'arkahost' ),
						'description' => __( 'Extra CSS class', 'arkahost' )
					)

				)
			));
			vc_map( array(

				'name' => __( 'Flip Content', 'arkahost' ),
				'base' => 'flip_content',
				'icon' => 'fab fa-apple',
				'category' => THEME_NAME.' Theme',
				'wrapper_class' => 'clearfix',
				'description' => __( 'Display image & content with flip styles', 'arkahost' ),
				'params' => array(
					array(
						'type' => 'select',
						'heading' => __( 'Type', 'arkahost' ),
						'param_name' => 'template',
						'admin_label' => true,
						'values' => Aka_Su_Tools::get_templates( 'flip' )
					),
					array(
						'type' => 'attach_image',
						'param_name' => 'img',
						'heading' => __( 'Front Image', 'arkahost' ),
						'description' => __( 'Upload the front logo', 'arkahost' ),
						'admin_label' => true,
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Image size', 'arkahost' ),
						'param_name' => 'img_size',
						'value' => 'thumbnail',
						'description' => __( 'Enter image size. Example: thumbnail, medium, large, full or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use "thumbnail" size. If used slides per view, this will be used to define carousel wrapper size.', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Height of box', 'arkahost' ),
						'param_name' => 'height_box',
						'value' => '',
						'description' => __( 'Enter height of box, to make your image display well. Recommend value equal height of image. Default : 320px', 'arkahost' )
					),
					array(
						'type' => 'textarea_raw_html',
						'param_name' => 'des',
						'heading' => 'Description',
						'description' => __( 'Description will show after hover. Support HTML Tag', 'arkahost' ),
					),
					array(
						'type' => 'textfield',
						'param_name' => 'class',
						'heading' => __( 'Class', 'arkahost' ),
						'description' => __( 'Extra CSS class', 'arkahost' )
					)

				)
			));

			vc_map( array(
				'name' => __( 'Posts - '.THEME_NAME, 'arkahost' ),
				'base' => 'posts',
				'icon' => 'fas fa-th-list',
				'category' => THEME_NAME.' Theme',
				'wrapper_class' => 'clearfix',
				'description' => __( 'List posts by other layouts of theme', 'arkahost' ),
				'params' => array(
					array(
						'type' => 'multiple',
						'heading' => __( 'Select Category ( hold ctrl or shift to select multiple )', 'arkahost' ),
						'param_name' => 'category',
						'values' => Aka_Su_Tools::get_terms( 'category'),
						'height' => '150px',
						'description' => __( 'Select category to display Posts', 'arkahost' )
					),
					array(
						'type' => 'select',
						'heading' => __( 'Select Template', 'arkahost' ),
						'param_name' => 'template',
						'admin_label' => true,
						'values' => Aka_Su_Tools::get_templates( 'post' )
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Amount', 'arkahost' ),
						'param_name' => 'items',
						'value' => 20,
						'description' => __( 'Enter number of people to show', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Words Limit', 'arkahost' ),
						'param_name' => 'words',
						'value' => 20,
						'description' => __( 'Limit words you want show as short description', 'arkahost' )
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Order By', 'arkahost' ),
						'param_name' => 'order',
						'value' => array(
							'Descending' => 'desc',
							'Ascending' => 'asc'
						),
						'description' => ' &nbsp; '
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Class Css', 'arkahost' ),
						'param_name' => 'class'
					),
				)
			));

			vc_map( array(
				'name' => __( 'Accordion', 'arkahost' ),
				'base' => 'vc_accordion',
				'show_settings_on_create' => false,
				'is_container' => true,
				'icon' => 'icon-wpb-ui-accordion',
				'category' => THEME_NAME.' Theme',
				'description' => __( 'Collapsible content panels', 'arkahost' ),
				'params' => array(
					array(
						'type' => 'textfield',
						'heading' => __( 'Widget title', 'arkahost' ),
						'param_name' => 'title',
						'description' => __( 'Enter text which will be used as widget title. Leave blank if no title is needed.', 'arkahost' )
					),
					array(
						'type' => 'select',
						'heading' => __( 'Style', 'arkahost' ),
						'param_name' => 'style',
						'values' => array(
							'1' => '1',
							'2' => '2',
							'3' => '3',
							'2 white' => 'white color',
						),
						'description' => __( 'Select style of accordion.', 'arkahost' )
					),
					array(
						'type' => 'select',
						'heading' => __( 'Icon', 'arkahost' ),
						'param_name' => 'icon',
						'values' => array(
							'icon-plus' => 'Icon Plus',
							'icon-plus-circle' => 'Plus Circle',
							'icon-plus-square-1' => 'Plus Square 1',
							'icon-plus-square-2' => 'Plus Square 2',
							'icon-arrow' => 'Icon Arrow',
							'icon-arrow2' => 'Icon Arrow2',
							'icon-arrow-circle-1' => 'Arrow Circle 1',
							'icon-arrow-circle-2' => 'Arrow Circle 2',
							'icon-chevron' => 'Icon Chevron',
							'icon-chevron-circle' => 'Icon Chevron Circle',
							'icon-caret' => 'Icon Caret',
							'icon-caret-square' => 'Icon Caret Square',
							'icon-folder-1' => 'Icon Folder 1',
							'icon-folder-2' => 'Icon Folder 2',
						),
						'description' => __( 'Select icon display on each spoiler', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Active section', 'arkahost' ),
						'param_name' => 'active_tab',
						'description' => __( 'Enter section number to be active on load or enter false to collapse all sections.', 'arkahost' )
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Allow collapsible all', 'arkahost' ),
						'param_name' => 'collapsible',
						'description' => __( 'Select checkbox to allow all sections to be collapsible.', 'arkahost' ),
						'value' => array( __( 'Allow', 'arkahost' ) => 'yes' )
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Disable keyboard interactions', 'arkahost' ),
						'param_name' => 'disable_keyboard',
						'description' => __( 'Disables keyboard arrows interactions LEFT/UP/RIGHT/DOWN/SPACES keys.', 'arkahost' ),
						'value' => array( __( 'Disable', 'arkahost' ) => 'yes' )
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Extra class name', 'arkahost' ),
						'param_name' => 'el_class',
						'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'arkahost' )
					)
				),
				'custom_markup' => '
					<div class="wpb_accordion_holder wpb_holder clearfix vc_container_for_children">
					%content%
					</div>
					<div class="tab_controls">
					    <a class="add_tab" title="' . __( 'Add section', 'arkahost' ) . '"><span class="vc_icon"></span> <span class="tab-label">' . __( 'Add section', 'arkahost' ) . '</span></a>
					</div>
				',
					'default_content' => '
				    [vc_accordion_tab title="' . __( 'Section 1', 'arkahost' ) . '"][/vc_accordion_tab]
				    [vc_accordion_tab title="' . __( 'Section 2', 'arkahost' ) . '"][/vc_accordion_tab]
				',
				'js_view' => 'VcAccordionView'
			));


			$tab_id_1 = 'def' . time() . '-1-' . rand( 0, 100 );
			$tab_id_2 = 'def' . time() . '-2-' . rand( 0, 100 );
			vc_map( array(
				"name" => __( 'Tabs - Sliders', 'arkahost' ),
				'base' => 'vc_tabs',
				'show_settings_on_create' => false,
				'is_container' => true,
				'icon' => 'icon-wpb-ui-tab-content',
				'category' => THEME_NAME.' Theme',
				'description' => __( 'Custom Tabs, Sliders', 'arkahost' ),
				'params' => array(
					array(
						'type' => 'select',
						'heading' => __( 'Display as', 'arkahost' ),
						'values' => array(
							'tabs' => 'Display as Tabs',
							'vertical' => 'Vertical Style',
							'owl' => 'Display as Owl Carousel',
							'outline' => 'Display as Outline Sliders'
						),
						'admin_label' => true,
						'param_name' => 'type',
						'description' => __( 'You can choose to display as tabs or sliders', 'arkahost' )
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Auto rotate tabs', 'arkahost' ),
						'param_name' => 'interval',
						'value' => array( __( 'Disable', 'arkahost' ) => 0, 3, 5, 10, 15 ),
						'std' => 0,
						'description' => __( 'Auto rotate tabs each X seconds.', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Extra class name', 'arkahost' ),
						'param_name' => 'el_class',
						'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'arkahost' )
					)
				),
				'custom_markup' => '
			<div class="wpb_tabs_holder wpb_holder vc_container_for_children">
			<ul class="tabs_controls">
			</ul>
			%content%
			</div>'
			,
			'default_content' => '
			[vc_tab title="' . __( 'Tab 1', 'arkahost' ) . '" tab_id="' . $tab_id_1 . '"][/vc_tab]
			[vc_tab title="' . __( 'Tab 2', 'arkahost' ) . '" tab_id="' . $tab_id_2 . '"][/vc_tab]
			',
				'js_view' => 'VcTabsView'
			) );


			vc_map( array(
				'name' => __( 'Tab', 'arkahost' ),
				'base' => 'vc_tab',
				'allowed_container_element' => 'vc_row',
				'is_container' => true,
				'content_element' => false,
				'params' => array(
					array(
						'type' => 'textfield',
						'heading' => __( 'Title', 'arkahost' ),
						'param_name' => 'title',
						'description' => __( 'Tab title.', 'arkahost' )
					),
					array(
						'type' => 'attach_image',
						'heading' => __( 'Background Image', 'arkahost'  ),
						'param_name' => 'bg',
						'description' => __( 'Upload image to display as background of tab', 'arkahost'  )
					),
					array(
						'type' => 'icon',
						'param_name' => 'icon',
						'heading' => __( 'Awesome Icon ', 'arkahost' ),
						'description' => __( 'Select Icon for element', 'arkahost' ),
						'admin_label' => true,
					),
					array(
						'type' => 'icon-simple',
						'param_name' => 'icon_simple_line',
						'heading' => __( 'Simple-line Icon ', 'arkahost' ),
						'description' => __( 'Select Icon for element', 'arkahost' ),
						'admin_label' => true,
					),
					array(
						'type' => 'icon-etline',
						'param_name' => 'icon_etline',
						'heading' => __( 'Etline Icon ', 'arkahost' ),
						'description' => __( 'Select Icon for element', 'arkahost' ),
						'admin_label' => true,
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Extra Class', 'arkahost' ),
						'param_name' => 'el_class',
					),
					array(
						'type' => 'tab_id',
						'heading' => __( 'Tab ID', 'arkahost' ),
						'param_name' => "tab_id"
					)
				),
				'js_view' => 'VcTabView'
			) );

			vc_map( array(
				'name' => __( 'Video Background', 'arkahost' ),
				'base' => 'videobg',

				'allowed_container_element' => 'vc_row',
				'content_element' => true,
				'is_container' => true,
				'show_settings_on_create' => false,

				'icon' => 'fas fa-file-video',
				'category' => THEME_NAME.' Theme',

				'description' => __( 'Background video for sections', 'arkahost' ),
				'params' => array(
					array(
						'type' => 'textfield',
						'heading' => __( 'Background Video ID', 'arkahost' ),
						'param_name' => 'id',
						'admin_label' => true,
						'description' => __( 'Input video id from youtube, E.g: cUhPA5qIxDQ', 'arkahost' )
					),
					array(
						'type' => 'select',
						'heading' => __( 'Sound', 'arkahost' ),
						'param_name' => 'sound',
						'values' => array(
							'no' => 'No, Thanks!',
							'yes' => 'Yes, Please!',
						),
						'admin_label' => true,
						'description' => __( 'Play sound or mute mode when video playing', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'admin_label' => true,
						'heading' => __( 'Height', 'arkahost' ),
						'param_name' => "height",
						'description' => __( 'Height of area video. E.g: 500', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Extra class name', 'arkahost' ),
						'param_name' => 'class',
						'description' => __( 'Use this field to add a class name and then refer to it in your css file.', 'arkahost' )
					)
				),
				'js_view' => 'VcColumnView'

			) );

			vc_map( array(
				'name' => __( 'Video Play', 'arkahost' ),
				'base' => 'videoplay',

				'allowed_container_element' => 'vc_row',
				'show_settings_on_create' => false,

				'icon' => 'fab fa-youtube',
				'category' => THEME_NAME.' Theme',

				'description' => __( 'Video Play', 'arkahost' ),
				'params' => array(
					array(
						'type' => 'textfield',
						'heading' => __( 'Video Link', 'arkahost' ),
						'param_name' => 'url',
						'admin_label' => true,
						'description' => __( 'Input video url from youtube or vimeo, E.g: https://www.youtube.com/watch?v=FcCSN859xR8', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'admin_label' => true,
						'heading' => __( 'Height', 'arkahost' ),
						'param_name' => "height",
						'description' => __( 'Height of area video. E.g: 500', 'arkahost' )
					),
					array(
						'type' => 'textarea_raw_html',
						'heading' => __( 'Left Description', 'arkahost' ),
						'param_name' => 'left',
						'value' => ''
					),
					array(
						'type' => 'textarea_raw_html',
						'heading' => __( 'Right Description', 'arkahost' ),
						'param_name' => 'right',
						'value' => ''
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Extra class name', 'arkahost' ),
						'param_name' => 'class',
						'description' => __( 'Use this field to add a class name and then refer to it in your css file.', 'arkahost' )
					)
				)

			) );

			vc_map( array(

				'name' => THEME_NAME.' Elements',
				'base' => 'elements',
				'icon' => 'fas fa-graduation-cap',
				'category' => THEME_NAME.' Theme',
				'description' => __( 'All elements use in theme', 'arkahost' ),
				'params' => array(

					array(
						'type' => 'attach_image',
						'param_name' => 'image',
						'heading' => __( 'Image ', 'arkahost' ),
						'description' => __( 'Select image for element', 'arkahost' ),
						'admin_label' => true,
					),
					array(
						'type' => 'select',
						'param_name' => 'retina',
						'heading' => __( 'High Resolution Support ', 'arkahost' ),
						'description' => __( 'Display 50% size of image to support high resolution such as Apple Retina', 'arkahost' ),
						'values' => array(
							'no' => 'No, Thanks!',
							'yes' => 'Yes, Please!',
						),
					),
					array(
						'type' => 'icon',
						'param_name' => 'icon_awesome',
						'heading' => __( 'Awesome Icon ', 'arkahost' ),
						'description' => __( 'Select Icon for element', 'arkahost' ),
						'admin_label' => true,
					),
					array(
						'type' => 'icon-simple',
						'param_name' => 'icon_simple_line',
						'heading' => __( 'Simple-line Icon ', 'arkahost' ),
						'description' => __( 'Select Icon for element', 'arkahost' ),
						'admin_label' => true,
					),
					array(
						'type' => 'icon-etline',
						'param_name' => 'icon_etline',
						'heading' => __( 'Etline Icon ', 'arkahost' ),
						'description' => __( 'Select Icon for element', 'arkahost' ),
						'admin_label' => true,
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Icon Class name', 'arkahost' ),
						'param_name' => "icon_class"
					),
					array(
						'type' => 'textarea_raw_html',
						'heading' => __( 'Short Description', 'arkahost' ),
						'param_name' => 'des',
						'value' => ''
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'External link', 'arkahost' ),
						'param_name' => 'link',
						'description' => __( 'External link read more', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'External link class name', 'arkahost' ),
						'param_name' => 'linkclass'
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Button Label', 'arkahost' ),
						'param_name' => 'readmore_text'
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Extra class name', 'arkahost' ),
						'param_name' => 'class',
						'description' => __( 'Use this field to add a class name and then refer to it in your css file.', 'arkahost' )
					)
				)
			) );

			/* Owl Image Carousel
			---------------------------------------------------------- */

			$target_arr = array(
				__( 'Same window', 'arkahost' ) => '_self',
				__( 'New window', 'arkahost' ) => '_blank'
			);
			vc_map( array(
				'name' => __( 'Carousel Photos', 'arkahost' ),
				'base' => 'king_carousel',
				'icon' => 'icon-wpb-images-carousel',
				'category' => THEME_NAME.' Theme',
				'description' => __( 'Animated carousel with images', 'arkahost' ),
				'params' => array(
					array(
						'type' => 'attach_images',
						'heading' => __( 'Images', 'arkahost' ),
						'param_name' => 'images',
						'value' => '',
						'description' => __( 'Select images from media library.', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Carousel size', 'arkahost' ),
						'param_name' => 'img_size',
						'value' => 'thumbnail',
						'description' => __( 'Enter image size. Example: thumbnail, medium, large, full or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use "thumbnail" size. If used slides per view, this will be used to define carousel wrapper size.', 'arkahost' )
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'On click action', 'arkahost' ),
						'param_name' => 'onclick',
						'value' => array(
							__( 'Open prettyPhoto', 'arkahost' ) => 'link_image',
							__( 'None', 'arkahost' ) => 'link_no',
							__( 'Open custom links', 'arkahost' ) => 'custom_link'
						),
						'description' => __( 'Select action for click event.', 'arkahost' )
					),
					array(
						'type' => 'exploded_textarea',
						'heading' => __( 'Custom links', 'arkahost' ),
						'param_name' => 'custom_links',
						'description' => __( 'Enter links for each slide (Note: divide links with linebreaks (Enter)).', 'arkahost' ),
						'dependency' => array(
							'element' => 'onclick',
							'value' => array( 'custom_link' )
						)
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Custom link target', 'arkahost' ),
						'param_name' => 'custom_links_target',
						'description' => __( 'Select how to open custom links.', 'arkahost' ),
						'dependency' => array(
							'element' => 'onclick',
							'value' => array( 'custom_link' )
						),
						'value' => $target_arr
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Slider autoplay', 'arkahost' ),
						'param_name' => 'autoplay',
						'description' => __( 'Enable autoplay mode.', 'arkahost' ),
						'value' => array( __( 'Yes', 'arkahost' ) => 'yes' ),
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Loop', 'arkahost' ),
						'param_name' => 'loop',
						'description' => __( 'Enable loop mode.', 'arkahost' ),
						'value' => array( __( 'Yes', 'arkahost' ) => 'yes' ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Slider autoplay speed', 'arkahost' ),
						'param_name' => 'speed',
						'value' => '5000',
						'description' => __( 'Duration of animation between slides in Auto play mode (ms).', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Slides per view', 'arkahost' ),
						'param_name' => 'slides_per_view',
						'value' => '1',
						'description' => __( 'Enter number of slides to display at the same time.', 'arkahost' )
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Auto Height', 'arkahost' ),
						'param_name' => 'autoheight',
						'description' => __( 'Enable autoplay mode.', 'arkahost' ),
						'value' => array( __( 'Yes', 'arkahost' ) => 'yes' ),
					),

					array(
						'type' => 'checkbox',
						'heading' => __( 'Lazyload', 'arkahost' ),
						'param_name' => 'lazyload',
						'description' => __( 'If checked, images will be load late.', 'arkahost' ),
						'value' => array( __( 'Yes', 'arkahost' ) => 'yes' )
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Hide pagination control', 'arkahost' ),
						'param_name' => 'hide_pagination_control',
						'description' => __( 'If checked, pagination controls will be hidden.', 'arkahost' ),
						'value' => array( __( 'Yes', 'arkahost' ) => 'yes' )
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Hide prev/next buttons', 'arkahost' ),
						'param_name' => 'hide_prev_next_buttons',
						'description' => __( 'If checked, prev/next buttons will be hidden.', 'arkahost' ),
						'value' => array( __( 'Yes', 'arkahost' ) => 'yes' )
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Progress Bar', 'arkahost' ),
						'param_name' => 'progressbar',
						'description' => __( 'Enable progress bar.', 'arkahost' ),
						'value' => array( __( 'Yes', 'arkahost' ) => 'yes' )
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Sync With Class', 'arkahost' ),
						'param_name' => 'sync_class',
						'description' => __( 'Sync slider with other owl carousel.', 'arkahost' )
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Use as thumbnails', 'arkahost' ),
						'param_name' => 'as_thumbnail',
						'description' => __( 'Use this slider as other thumbnails.', 'arkahost' ),
						'value' => array( __( 'Yes', 'arkahost' ) => 'yes' )
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Extra class name', 'arkahost' ),
						'param_name' => 'el_class',
						'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'arkahost' )
					),
				)
			) );


			/* Domain checker
			---------------------------------------------------------- */
			vc_map( array(
				'name' => __( 'Form Domain Checker', 'arkahost' ),
				'base' => 'domain_checker',
				'icon' => 'fas fa-globe',
				'category' => THEME_NAME.' Theme',
				'wrapper_class' => 'clearfix',
				'description' => __( 'Display form domain checker', 'arkahost' ),
				'params' => array(
					array(
						'type' => 'textfield',
						'param_name' => 'title',
						'heading' => __( 'Title', 'arkahost' ),
						'admin_label' => true,
						'description' => __( 'The name of client', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'param_name' => 'search_placeholder',
						'heading' => __( 'Text placeholder input', 'arkahost' ),
						'description' => __( 'Text search placeholder input', 'arkahost' )
					),

					array(
						'type' => 'textfield',
						'param_name' => 'text_button',
						'heading' => 'Text button',
						'description' => __( 'Short Description will show when hover', 'arkahost' ),
						'admin_label' => true,
					),
					array(
						'type' => 'textarea_raw_html',
						'param_name' => 'html_before',
						'heading' => 'Html before form',
						'description' => __( 'Html will show before form', 'arkahost' ),
						'admin_label' => false,
						'value' => ''
					),
					array(
						'type' => 'textarea_raw_html',
						'param_name' => 'html_after',
						'heading' => 'Html after form',
						'description' => __( 'Html will show after form', 'arkahost' ),
						'admin_label' => false,
						'value' => ''
					),
					array(
						'type' => 'textfield',
						'param_name' => 'class',
						'heading' => __( 'Class', 'arkahost' ),
						'description' => __( 'Extra CSS class', 'arkahost' )
					)

				)
			));

			vc_map( array(
				'name' => __( 'Advance Domain Checker', 'arkahost' ),
				'base' => 'adv_domain_checker',
				'icon' => 'fas fa-globe',
				'category' => THEME_NAME.' Theme',
				'wrapper_class' => 'clearfix',
				'description' => __( 'Display form domain checker in advance mode', 'arkahost' ),
				'params' => array(
					array(
						'type' => 'textfield',
						'param_name' => 'title',
						'heading' => __( 'Title', 'arkahost' ),
						'admin_label' => true,
						'description' => __( 'The name of client', 'arkahost' ),
						'value' => __('Find your Perfect Domain Name', 'arkahost')
					),
					// array(
					// 	'type' => 'dropdown',
					// 	'heading' => __( 'Layout', 'arkahost' ),
					// 	'param_name' => 'layout',
					// 	'value' => array(
					// 		__( 'Layout 1', 'arkahost' ) => '1',
					// 		__( 'Layout 2', 'arkahost' ) => '2',
					// 		__( 'Layout 3', 'arkahost' ) => '3',
					// 	),
					// 	'description' => __( 'The layout of form, view more at http://arkahost.com/advance-search.', 'arkahost' )
					// ),
					array(
						'type' => 'textfield',
						'param_name' => 'search_placeholder',
						'heading' => __( 'Text placeholder input', 'arkahost' ),
						'description' => __( 'Text search placeholder input', 'arkahost' )
					),
					array(
						'type' => 'textfield',
						'param_name' => 'suggestions',
						'heading' => __( 'Suggest TLDs domains', 'arkahost' ),
						'description' => __( 'The list of domains suggestion if result is taken. Exp: .com, .net, .biz, .us, .info', 'arkahost' ),
						'value' => '.com, .net, .biz, .us, .info'
					),

					array(
						'type' => 'textfield',
						'param_name' => 'text_button',
						'heading' => 'Button Text',
						'description' => __( 'The text for the button', 'arkahost' ),
						'admin_label' => true,
					),
					array(
						'type' => 'textarea_raw_html',
						'param_name' => 'html_before',
						'heading' => 'Html before form',
						'description' => __( 'Html will show before form', 'arkahost' ),
						'admin_label' => false,
						'value' => ''
					),
					array(
						'type' => 'textfield',
						'param_name' => 'default',
						'heading' => __( 'Default Domain', 'arkahost' ),
						'description' => __( 'The search will use this domain instead of empty data when user enter nothing on input serach.', 'arkahost' )
					),
					array(
						'type' => 'textarea_raw_html',
						'param_name' => 'html_after',
						'heading' => 'Html after form',
						'description' => __( 'Html will show after form', 'arkahost' ),
						'admin_label' => false,
						'value' => ''
					),
					array(
						'type' => 'textfield',
						'param_name' => 'class',
						'heading' => __( 'Class', 'arkahost' ),
						'description' => __( 'Extra CSS class', 'arkahost' )
					),
					array(
			            'type' => 'css_editor',
			            'heading' => __( 'Css', 'arkahost' ),
			            'param_name' => 'css',
			            'group' => __( 'Design options', 'arkahost' ),
			        )

				)
			));


			//VPS Plan
			vc_map( array(
				"name"                    => __("VPS Plans", "arkahost"),
				"base"                    => "vps_slides",
				"icon"                    => "fas fa-server",
				"show_settings_on_create" => true,
				'category' => THEME_NAME.' Theme',
				"params"                  => array(
			        array(
						"type"       => "textfield",
						"heading"    => __("Label Button Order", "arkahost"),
						"param_name" => "btn_label",
						"value"      => __("Order Now", "arkahost"),
			        ),
			        array(
						"type"       => "textfield",
						"heading"    => __("Per Text", "arkahost"),
						"param_name" => "per_label",
						"value"      => "",
			        ),
			         array(
						"type"       => "textfield",
						"heading"    => __("Start Item", "arkahost"),
						"param_name" => "start_item",
						"value"      => 1
			        ),

		         	array(
			       		'type' => 'param_group',
						'heading' => __( 'VPS Items', 'arkahost' ),
						'param_name' => 'items',
						'value' => urlencode(
							json_encode(
								array(
									array(
										'title' => __( 'VPS 1', 'arkahost' ),
										'cpu' => '1 Core',
										'disk_space' => '50GB',
										'bandwidth' => '100GB',
										'ram' => '1GB',
										'cost' => '$19.00',
										'link' => '#',
									),
									array(
										'title' => __( 'VPS 2', 'arkahost' ),
										'cpu' => '2 Core',
										'disk_space' => '100GB',
										'bandwidth' => '200GB',
										'ram' => '2GB',
										'cost' => '$29.00',
										'link' => '#',
									),
									array(
										'title' => __( 'VPS 3', 'arkahost' ),
										'cpu' => '4 Core',
										'disk_space' => '200GB',
										'bandwidth' => '500GB',
										'ram' => '4GB',
										'cost' => '$49.00',
										'link' => '#',
									),
									array(
										'title' => __( 'VPS 4', 'arkahost' ),
										'cpu' => '4 Core',
										'disk_space' => '600GB',
										'bandwidth' => '700GB',
										'ram' => '8GB',
										'cost' => '$79.00',
										'link' => '#',
									),
									array(
										'title' => __( 'VPS 5', 'arkahost' ),
										'cpu' => '8 Core',
										'disk_space' => '800GB',
										'bandwidth' => '10.000GB',
										'ram' => '16GB',
										'cost' => '$109.00',
										'link' => '#',
									),
								)
							)
						),
						'params' => array(
							array(
								'type' => 'textfield',
								'heading' => __( 'Title', 'arkahost' ),
								'param_name' => 'title',
								'description' => __( 'Enter title for VPS item.', 'arkahost' ),
								'admin_label' => true,
							),
							array(
								'type' => 'textfield',
								'heading' => __( 'CPU', 'arkahost' ),
								'param_name' => 'cpu',
								'description' => __( 'Enter value for CPU of this VPS item.', 'arkahost' ),
							),
							array(
								'type' => 'textfield',
								'heading' => __( 'Disk Space', 'arkahost' ),
								'param_name' => 'disk_space',
								'description' => __( 'Enter value for Disk Space of this VPS item.', 'arkahost' ),
							),
							array(
								'type' => 'textfield',
								'heading' => __( 'Bandwidth', 'arkahost' ),
								'param_name' => 'bandwidth',
								'description' => __( 'Enter value for Band-Width of this VPS item.', 'arkahost' ),
							),
							array(
								'type' => 'textfield',
								'heading' => __( 'Ram', 'arkahost' ),
								'param_name' => 'ram',
								'description' => __( 'Enter value for RAM of this VPS item.', 'arkahost' ),
							),
							array(
								'type' => 'textfield',
								'heading' => __( 'Cost', 'arkahost' ),
								'param_name' => 'cost',
								'description' => __( 'Enter value for Price of this VPS item.', 'arkahost' ),
								'admin_label' => true,
							),
							array(
								'type' => 'textfield',
								'heading' => __( 'Button URL', 'arkahost' ),
								'param_name' => 'link',
								'description' => __( 'Enter the URL for button of this VPS item.', 'arkahost' ),
							),
						),
					),
			        array(
			            "type"        => "textfield",
			            "heading"     => __("Extra class name", "arkahost"),
			            "param_name"  => "el_class",
			            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "arkahost")
			        )
			    ),
			) );


		}
	}

}
