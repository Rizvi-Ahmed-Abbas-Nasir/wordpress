<?php

global $king, $post;
extract( $king->bag );

echo '<div id="breadcrumb"';
if( !empty($page_bread_bg) ){
	echo ' style="background-image:url('.esc_url($page_bread_bg).')" ';
}
echo ' class="'.esc_attr($breadcrumb).'">';
echo '<div class="container">';
$breadcrumb_tag = empty($breadcrumb_tag)? 'h1' :$breadcrumb_tag;

echo '<'. $breadcrumb_tag .'>';

	if( !empty( $page_title ) ){
		echo king::esc_js( $page_title );
	}else{
	    
	    
		if( is_home() ){

			if(  get_option('page_for_posts') ){
				$curPost = get_post( get_option('page_for_posts') );
				echo esc_html( $curPost->post_title );
			}else echo esc_html( get_bloginfo( 'name' ) );

			if( !empty( $_GET['layout'] ) ){
				echo ' '.esc_html($_GET['layout']);
			}

		}else if(is_single()){
			
			if(isset($king->cfg['our_works_page_title']) && $king->cfg['our_works_page_title'] != 'global' && $post_type == 'our-works'){
				echo esc_html( $post->post_title );
			}elseif(isset($king->cfg['post_bred_title']) && $king->cfg['post_bred_title'] != 'global'){
				echo esc_html( $post->post_title );
			}else{
				echo esc_html( $post->post_title );
			}
		}else if( is_shop() && is_product_category() && is_product() ){
            echo single_cat_title();
        }else{
			if( is_category() ){

				if( $post_type == 'post'){
					echo __('Category Archives: ', 'arkahost');
					echo "<br />";
				}
				
			}else if( is_tag() ){

				echo __('Tag Archives:', 'arkahost');

			}else if( is_archive() ){

				if ( is_day() ) :

					_e( 'Daily Archives', 'arkahost' );

				elseif ( is_month() ) :

					_e( 'Monthly Archives', 'arkahost' );

				elseif ( is_year() ) :

					_e( 'Yearly Archives', 'arkahost' );

				else :
					if( $post_type == 'post'){

						_e( 'Blog Archives', 'arkahost' );
						
						echo '<br />';
					}

				endif;
				
			}else if( is_search() ){
				echo __('Search Results:', 'arkahost');
				echo "<br />";
				if( !empty(  $_GET['s'] ) ){
					$title = '"'.esc_html($_GET['s']).'"';
				}
			}
			echo esc_html( $title );
		}
	}

echo '</h1>';
echo '<div class="pagenation">';

echo '<a href="'. home_url() .'">'.__('Home','arkahost')."</a> ";

if( is_object( $post ) && !empty( $post_type ) ){
	if( !in_array($post_type, array('post', 'page', 'product')) && (function_exists('is_shop') && !is_shop()) ){
		$type = $post_type;
		if( $type == 'our-works' && !empty( $king->cfg['our_works_title'] ) )
		{
			echo wp_kses($breadeli, array('i'=>array())).' '.ucwords( str_replace( '-', ' ', $king->cfg['our_works_title'] ) ).' ';
		}
		else if( $type == 'our-team'  && !empty( $king->cfg['our_team_title'] ) )
		{
			echo wp_kses($breadeli, array('i'=>array())).' '.ucwords( str_replace( '-', ' ', $king->cfg['our_team_title'] ) ).' ';
		}
		else if( $type == 'faq'  && !empty( $king->cfg['faq_title'] ) )
		{
			echo wp_kses($breadeli, array('i'=>array())).' '.ucwords( str_replace( '-', ' ', $king->cfg['faq_title'] ) ).' ';
		}
		else
		{
			echo wp_kses($breadeli, array('i'=>array())).' '.ucwords( str_replace( '-', ' ', $type ) ).' ';
		}				
	}

	$blog_page_id = get_option('page_for_posts');
	if( !empty( $blog_page_id ) )
	{
		$curPost = get_post( get_option('page_for_posts') );
		if( $post_type == 'post' && !is_home() && !empty($curPost)){
			echo wp_kses($breadeli, array('i'=>array())).' <a href="'.get_permalink( $curPost->ID ).'">'.esc_html( $curPost->post_title ).'</a> ';
		}
	}

}

if( is_home() ){
	if(  get_option('page_for_posts') ){
		$curPost = get_post( get_option('page_for_posts') );
		echo wp_kses($breadeli, array('i'=>array())).' '.$curPost->post_title.' ';
	}else{
		echo wp_kses($breadeli, array('i'=>array())). __(' Front Page ', 'arkahost');
	}
}


if(function_exists('is_woocommerce') && is_woocommerce()){
	$woo_page_id = get_option( 'woocommerce_shop_page_id' );
	if($woo_page_id > 0){
		$woo_page = get_post($woo_page_id);
		echo wp_kses($breadeli, array('i'=>array())).' <a href="'.get_permalink( $woo_page->ID ).'">'.$woo_page->post_title.'</a> ';
	}
	if(function_exists('is_product_category') && is_product_category()){
		$cate = get_queried_object();
		echo wp_kses($breadeli, array('i'=>array())).' <span>'.$cate->name.'</span>';
	}
}

if(function_exists('is_product') && is_product()){
	$cate = get_queried_object();
	//echo wp_kses($breadeli, array('i'=>array())).' <span>'.$cate->name.'</span>';
	global $post;
	$terms = get_the_terms( $post->ID, 'product_cat' );
	$nterms = get_the_terms( $post->ID, 'product_tag'  );
	foreach ($terms  as $term  ) {
		$product_cat_id = $term->term_id;
		$product_cat_name = $term->name;
		break;
	}
	echo wp_kses($breadeli, array('i'=>array())).' <a href="'.get_category_link( $product_cat_id ).'">'.$product_cat_name.'</a> ';
}


if ( is_category() ) {
	echo wp_kses($breadeli, array('i'=>array())).' '.single_cat_title( '', false ).' ';
}

if( is_page() ){

	if( $post->post_parent ){
		$parent = get_post( $post->post_parent );
		echo wp_kses($breadeli, array('i'=>array())).' <a href="'.get_permalink( $post->post_parent ).'">'.$parent->post_title.'</a> ';
	}
}

if( ( is_single() || is_page() ) && !is_front_page() ) {
	echo wp_kses($breadeli, array('i'=>array()))." <span>";
	the_title();
	echo "</span>";
}

if(is_tag()){ echo wp_kses($breadeli, array('i'=>array()))." <span>Tag: ".single_tag_title('',FALSE).'</span>'; }
if(is_404()){ echo wp_kses($breadeli, array('i'=>array()))." <span>404 - Page not Found</span>"; }
if(is_search()){ echo wp_kses($breadeli, array('i'=>array()))." <span>Search</span>"; }
if(is_year()){ echo wp_kses($breadeli, array('i'=>array())).' '.get_the_time('Y'); }

echo "</div></div></div>";
