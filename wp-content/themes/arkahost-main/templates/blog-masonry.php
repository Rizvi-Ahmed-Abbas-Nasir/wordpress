<?php
	/**
	*
	* @author king-theme.com
	*
	*/
	
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
	global $post, $more, $king;

	function king_masonry_assets() {
		wp_enqueue_style('king-masonry');
		wp_enqueue_script('king-masonry');
	}
	add_action('wp_print_styles', 'king_masonry_assets');

	get_header();
	
?>

<?php  $king->breadcrumb(); ?>

<div class="content_fullwidth">
	<div class="container">
		<div id="grid-container" class="cbp-l-grid-masonry-projects" data-cols="4" data-gaph="25">        
			<?php 
			
				while ( have_posts() ) : the_post();
				
					$height = 700;
					$cap = 'two';
					$heighClass = ' cbp-l-grid-masonry-height4';
					if( rand(0,10) >= 5 ){
						$height = 540;
						$heighClass = ' cbp-l-grid-masonry-height3';
						$cap = 'three';
					}
					
					$cats = get_the_category( $post->ID );
					$catsx = array();
					for( $i=0; $i<2; $i++ ){
						if( !empty($cats[$i]) ){
							array_push($catsx, $cats[$i]->name);
						}
					}
			 ?>
	 		<div class="cbp-item<?php echo esc_attr( $heighClass ); ?>">
		       <div class="cbp-caption">
		            <div class="cbp-caption-defaultWrap <?php echo esc_attr( $cap ); ?>">
		            	 <a href="<?php echo get_permalink( $post->ID ); ?>">
				            <?php
				                
								$img = $king->get_featured_image( $post, true );
								if( !empty( $img ) )
								{
									if( strpos( $img , 'youtube') !== false )
									{
										$img = THEME_URI.'/assets/images/default.jpg';
									}
									$img = king_createLinkImage( $img, '570x'.$height.'xc' );
										
									echo '<img alt="'.get_the_title().'" class="featured-image" src="'.$img.'" />';
								}
			
							?>
		            	 </a>
		            </div>
		            <a href="<?php echo get_permalink( $post->ID ); ?>" class="cbp-l-grid-masonry-projects-title"><?php echo wp_trim_words( $post->post_title, 4 ); ?></a>
		            <div class="cbp-l-grid-masonry-projects-desc"><?php echo implode( ' / ', $catsx ); ?></div>
		       </div>
	 		</div>   	            
			<?php endwhile; ?>
	
		  </div><!-- #grid-container -->
		  <div id="loadMore-container" class="cbp-l-loadMore-button hiddenf">
	        <a href="<?php echo SITE_URI; ?>/wp-admin/admin-ajax.php?action=loadPostsMasonry" class="cbp-l-loadMore-link">
	            <span class="cbp-l-loadMore-defaultText">LOAD MORE</span>
	            <span class="cbp-l-loadMore-loadingText">LOADING...</span>
	            <span class="cbp-l-loadMore-noMoreLoading">NO MORE ARTICLES</span>
	        </a>
		</div>
    </div>
</div><!-- #primary -->

<?php get_footer(); ?>   