<?php

global $posts, $king;

if ( $posts->have_posts() ){

?>
<div class="feature_section6_elm">
	<div class="less5">
		<div class="owl-demo20 owl-carousel">
			<?php

			while ( $posts->have_posts() ) :
				$posts->the_post();
				global $post;
				$options = get_post_meta( $post->ID, 'king_testi' );
				$options = shortcode_atts( array(
					'website'	=> 'www.yourwebsite.com',
					'rate'	=> 5
				), $options[0], false );

			?>

			<div class="item">
				<p class="bigtfont"><?php echo get_the_content($post->ID); ?></p>
				<br />
				<strong>- <?php the_title(); ?> -</strong> &nbsp; 
				<?php if( isset( $options['website'] ) && !empty( $options['website'] )):?>
				<em><?php echo esc_html( $options['website'] ); ?></em>
				<?php endif; ?>
				<p class="clearfix margin_bottom1"></p>
			</div><!-- end slide -->
			<?php endwhile; ?>
		</div>  
	</div>  
</div>  

<script type="text/javascript">
	jQuery(document).ready(function($){
		if( !document.owl_demo20 ){
			$(".owl-demo20").owlCarousel({
				autoPlay : 18000,
				stopOnHover : true,
				navigation: false,
				paginationSpeed : 1000,
				goToFirstSpeed : 2000,
				singleItem : true,
			});
			document.owl_demo20 = true;
		}
	});
</script>	     
<?php
	
}else {
	echo '<h4>' . __( 'Testimonials not found', 'arkahost' ) . '</h4> <a href="'.admin_url('post-new.php?post_type=testimonials').'"><i class="fa fa-plus"></i> Add New Testimonial</a>';
}	
	
?>