<?php
/*
Plugin Name: ArkaHost Theme Helper
Plugin URI: http://king-theme.com/plugins
Description: Help Themes of King-Theme works correctly.
Version: 1.0
Author: King-Theme
Author URI: http://king-theme.com

*/



/********************************************************/
/*                        Actions                       */
/********************************************************/


if( !function_exists( 'king_arkahost_helper_init' ) ){
	/* Add post type */

	function king_arkahost_helper_init() {
	
		foreach( king_arkahost_register_type( 'taxanomy' ) as $postType => $cofg ){
			if( !taxonomy_exists( $postType.'-category' ) ){
		    	register_taxonomy( $postType.'-category' , $postType, $cofg );
		    }	
	    }
	    	
	    foreach( king_arkahost_register_type( 'post' ) as $postType => $cofg ){
		    if( !post_type_exists( $postType ) ){
		    	register_post_type( $postType, $cofg );
		    }	
	    }
		
		if( !post_type_exists( 'mega_menu' ) ){
			$labels = array(
		        'name' => __('King - Mega Menu', 'king'),
		        'singular_name' => __('King - Mega Menu', 'king'),
		        'add_new' => __('Add New', 'king'),
		        'add_new_item' => __('Add New king Mega Menu Item', 'king'),
		        'edit_item' => __('Edit king Mega Menu Item', 'king'),
		        'new_item' => __('New king Mega Menu Item', 'king'),
		        'view_item' => __('View king Mega Menu Item', 'king'),
		        'search_items' => __('Search king Mega Menu Items', 'king'),
		        'not_found' => __('No king Mega Menu Items found', 'king'),
		        'not_found_in_trash' => __('No king Mega Menu Items found in Trash', 'king'),
		        'parent_item_colon' => __('Parent king Mega Menu Item:', 'king'),
		        'menu_name' => __('Mega Menu', 'king'),
		    );
		
		    $args = array(
		        'labels' => $labels,
		        'hierarchical' => false,
		        'description' => __('Mega Menus entries for Slowave.', 'king'),
		        'supports' => array('title', 'editor'),
		        'public' => true,
		        'show_ui' => true,
		        'show_in_menu' => true,
		        'menu_position' => 40,
		
		        'show_in_nav_menus' => true,
		        'publicly_queryable' => false,
		        'exclude_from_search' => true,
		        'has_archive' => false,
		        'query_var' => true,
		        'can_export' => true,
		        'rewrite' => false,
		        'capability_type' => 'post'
		    );
			
		    register_post_type('mega_menu', $args);
		}
		
	}
	add_action( 'init', 'king_arkahost_helper_init',0 );
	
	
	function king_arkahost_register_type( $type = 'post' ){
		global $king;
		$KING_DOMAIN = 'king';
		
		//our works title, slug
		$our_works_title = (isset($king->cfg['our_works_title']) && !empty($king->cfg['our_works_title']))?$king->cfg['our_works_title']:__('Our Works', 'arkahost' );
		$our_works_slug = (isset($king->cfg['our_works_slug']) && !empty($king->cfg['our_works_slug']))?$king->cfg['our_works_slug']:'our-works';

		//our team title, slug
		$our_team_title = (isset($king->cfg['our_team_title']) && !empty($king->cfg['our_team_title']))?$king->cfg['our_team_title']:__('Our Team', $KING_DOMAIN );
		$our_team_slug = (isset($king->cfg['our_team_slug']) && !empty($king->cfg['our_team_slug']))?$king->cfg['our_team_slug']:'our-team';
		
		//faq title, slug
		$faq_title = (isset($king->cfg['faq_title']) && !empty($king->cfg['faq_title']))?$king->cfg['faq_title']:__('FAQ', $KING_DOMAIN );
		$faq_slug = (isset($king->cfg['faq_slug']) && !empty($king->cfg['faq_slug']))?$king->cfg['faq_slug']:'faq';
		
		$args = array(
			array( $our_team_title, 'our-team', 'Staff', 'dashicons-groups', array('title','editor','thumbnail','page-attributes'), $our_team_slug ),
			array( $our_works_title, 'our-works', 'Project', 'dashicons-book', array('title','editor','author','thumbnail','excerpt','page-attributes'), $our_works_slug ),
			array( __('Testimonials', $KING_DOMAIN ), 'testimonials', 'Testimonial', 'dashicons-admin-comments', array('title','editor','thumbnail','page-attributes') ),
			array( $faq_title, 'faq', 'FAQ', 'dashicons-editor-help', array('title','editor','page-attributes'), $faq_slug ),
			array( __('Pricing Tables', $KING_DOMAIN ), 'pricing-tables', 'Pricing', 'dashicons-slides', array('title','page-attributes') ),
			array( __('Newsletter Subscribers', $KING_DOMAIN ), 'subcribers', 'Subcriber', 'dashicons-email-alt', array('title','page-attributes') ),
		);
		
		$arg_return = array();
		
		if( $type == 'post' ){
		
			foreach( $args as $arg ){
			
				$arg_return[ $arg[1] ] = array(
					'menu_icon' => $arg[3],
				    'labels' => array(
					    'name' => $arg[0],
					    'singular_name' => $arg[1],
					    'add_new' => 'Add new '.$arg[2],
					    'edit_item' => 'Edit '.$arg[2],
					    'new_item' => 'New '.$arg[2],
					    'add_new_item' => 'New '.$arg[2],
					    'view_item' => 'View '.$arg[2],
					    'search_items' => 'Search '.$arg[2].'s',
					    'not_found' => 'No '.$arg[2].' found',
					    'not_found_in_trash' => 'No '.$arg[2].' found in Trash'
				    ),
				    'public' => true,
				    'supports' => $arg[4],
				    'taxonomies' => array( $arg[1].'-category' )
			    );
				if(!empty($arg[5])){
					$arg_return[ $arg[1] ][ 'rewrite' ] = array('slug' => $arg[5], 'with_front' => false);
				}
			}
		}else if( $type == 'taxanomy' ){
			
			foreach( $args as $arg ){
			
				$arg_return[ $arg[1] ] = array(
					'hierarchical'          => false,
					'labels'                => array(
							'name'                       => _x( $arg[2].' Categories', 'taxonomy general name' ),
							'singular_name'              => _x( $arg[2].' Category', 'taxonomy singular name' ),
							'search_items'               => 'Search '.$arg[2].' Categories',
							'popular_items'              => 'Popular '.$arg[2].' Categories',
							'all_items'                  => 'All '.$arg[2].' Categories',
							'parent_item'                => null,
							'parent_item_colon'          => null,
							'edit_item'                  => 'Edit '.$arg[2].' Category',
							'update_item'                => 'Update '.$arg[2].' Category',
							'add_new_item'               => 'Add New '.$arg[2].' Category',
							'new_item_name'              => 'New '.$arg[2].' Category Name',
							'separate_items_with_commas' => 'Separate '.$arg[2].' Category with commas',
							'add_or_remove_items'        => 'Add or remove '.$arg[2].' Category',
							'choose_from_most_used'      => 'Choose from the most used '.$arg[2].' Category',
							'not_found'                  => 'No '.$arg[2].' Category found.',
							'menu_name'                  => $arg[2].' Categories',
						),
					'show_ui'               => true,
					'show_admin_column'     => true,
					'update_count_callback' => '_update_post_term_count',
					'query_var'             => true,
					'rewrite'               => array( 'slug' => $arg[1].'-category' ),
				);
			}
		}
		flush_rewrite_rules(true);
		return $arg_return;
	
	}
	
	
	
	
	if( !function_exists( 'king_mega_menu' ) ){
	
		function king_add_sc_select() {
		
		    global $post;
		    if(isset($post -> ID)) {
		        if (!(get_post_type($post->ID) == 'mega_menu'))
		            return false;
		    } else {
		        return false;
		    }
		
		    echo '<select id="sc_select"><option>Insert Mega Menu</option>';
		    $menus = get_terms('nav_menu');
		    foreach($menus as $menu) {
		        echo '<option value="[mega_menu col=\'3\' title=\''.$menu->name.
		        '\' menu=\''.$menu->slug.
		        '\']">'.$menu->name.
		        '</option>';
		    }
		    echo '</select>';
		}
		add_action('media_buttons', 'king_add_sc_select', 1003);
	
	
		function king_mega_menu($atts, $content = null) {
			
			$_server = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			
		    extract( shortcode_atts( array('menu' => '', 'title' => '', 'class' =>'', 'col' => 12 ), $atts ) );
		    
			global $wpdb;
			
			$menuID = $wpdb->get_results('SELECT `term_id` FROM `'.$wpdb->prefix.'terms` WHERE `'.$wpdb->prefix.'terms`.`slug` = "'.esc_attr($menu).'"');
			
			
			if( empty( $menuID[0] ) ){
				return;
			}
			if( empty( $menuID[0]->term_id ) ){
				return;
			}
			
			$menu = $menuID[0]->term_id;
		    $items = wp_get_nav_menu_items( $menu );
		
		    $output = '<ul class="col-md-'.$col.' col-sm-'.($col*2).' list-unstyled '.$class.'">';
			if ($title)$output.= '<li><p>'.$title.'</p></li>';
		    if ($items) {
		        foreach($items as $item) {
		        	
		        	if( $item->url == 'http://'.$_server || $item->url == 'https://'.$_server ){
			        	$_class = ' class="active"';
		        	}else{
			        	$_class = '';
		        	}
		            $output .= '<li><a href="'.$item->url.'" '.$_class.'>';
		            if( strpos( $item->description, 'icon:') !== false ){
						$output .= ' <i class="'.trim(str_replace( 'icon:', '', $item->description )).'"></i> ';	
					}else{
						$output .= ' <i class="fa fa-angle-right"></i> ';
					}
		            $output .= $item->title.'</a></li>';
		        }
		    }
		
		    $output.= '</ul>';
		
		    return $output;
		    
		}
		
		add_shortcode('mega_menu', 'king_mega_menu');
	
	}
	  
	/*
	*
	* interfere plugins update
	*
	*/

	class king_updater{

		public $plugins = array( 'js_composer/js_composer.php', 'revslider/revslider.php' );

		function __construct(){
			add_filter( 'pre_set_site_transient_update_plugins', array( &$this, 'check_update_plugins' ), 9999 );
		}

		function check_update_plugins ( $transient ){
			
			if( isset( $transient->response ) )
			{
				$response = $transient->response;
			
				foreach( $response as $name => $args )
				{
					if( in_array( $name, $this->plugins ) )
					{
						unset( $transient->response[ $name ] );
					}
				}
				
			    return $transient;
			}
			
		}

	}
	new king_updater();
}	 
