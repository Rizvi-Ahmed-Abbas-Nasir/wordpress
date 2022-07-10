<?php

global $posts, $king;

if ( $posts->have_posts() ){

?>
<div class="feature_section8_elm">
	<div id="owl-demo13" class="owl-demo13">
		<div  class="owl-carousel">
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

			<div class="slidesec">
				<?php 
					$thumbn = get_the_post_thumbnail_url($post);
					if (!empty($thumbn)) {
				?>
		        <div class="imgbox one">
			        <img src="<?php echo esc_url($thumbn); ?>" alt="<?php the_title(); ?>" />
			    </div>
			    <?php } ?>
				<h4>- <?php the_title(); ?></h4>
				<p class="bigtfont"><?php echo get_the_content($post->ID); ?></p>
				<br />
				<?php if( isset( $options['website'] ) && !empty( $options['website'] )):?>
				<strong>website:</strong> <?php echo esc_html( $options['website'] ); ?>
				<?php endif; ?>
				<br />
				<?php if( !empty( $options['rate'] ) ){
					for( $j = 0 ; $j < $options['rate']; $j++  ){
						echo '<i class="fa fa-star"></i>';
					}		
				} ?>
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