<?php

global $posts, $king;

$atts = $king->bag['atts'];
$class = $king->bag['class'];

if ( $posts->have_posts() ) {
	$i = 1;
	
?>
<div class="<?php echo esc_attr( $class ); ?> feature_section3_elm owl-carousel owl-demo13">
<?php	
	while ( $posts->have_posts() ) :
	
		$posts->the_post();
		global $post;

?>	
   	<div class="lstblogs">
    	<div>
			<a href="<?php the_permalink(); ?>">
			<?php
				$img = king::get_featured_image( $post );
				echo '<img src="'.king_createLinkImage( $img, '560x250xc' ).'" alt="ArkaHost"  />';
			?>
			</a>
			<a href="<?php the_permalink(); ?>" class="date">
				<strong><?php the_time( 'd' ); ?></strong> <?php the_time( 'M' ); ?>
			</a>
			<h4 class="white light">
				<a href="<?php the_permalink(); ?>" class="tcont" title="<?php echo esc_attr($post->post_title); ?>">
					<?php echo wp_trim_words($post->post_content, $atts['words'] ); ?>
				</a> 
				
			</h4>
			<div class="hline"></div>
    	</div>
    </div>
<?php
	endwhile;
?>
</div>
<script type="text/javascript">
	jQuery(document).ready(function($){
		if( !document.owl_demo13 ){
			$(".owl-demo13").owlCarousel({
				autoPlay : 18000,
				stopOnHover : true,
				navigation: false,
				paginationSpeed : 1000,
				goToFirstSpeed : 2000,
				singleItem : true,
			});
			document.owl_demo13 = true;
		}
	});
</script>
<?php
}
else {
	echo '<h4>' . __( 'Posts not found', 'arkahost' ) . '</h4>';
}
?>
