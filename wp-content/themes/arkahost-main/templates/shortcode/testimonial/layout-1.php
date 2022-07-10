<?php

global $posts, $king;

if ( $posts->have_posts() ){

?>
<div class="feature_section6_elm">
<div class="less6">
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
	        <?php 
				$thumbn = get_the_post_thumbnail_url($post);
				if (!empty($thumbn)) {
			?>
	        <div class="climg">
		        <img src="<?php echo esc_url($thumbn); ?>" alt="<?php the_title(); ?>" />
		    </div>
		    <?php } ?>
        	<p class="bigtfont dark"><?php echo get_the_content($post->ID); ?></p>
			<br />
        	<strong>- <?php the_title(); ?> -</strong> &nbsp; 
            <br />
			<em><?php echo esc_html( $options['website'] ); ?></em>
            <p class="clearfix margin_bottom1"></p>
        </div><!-- end slide -->
        <?php endwhile; ?>
	</div>  
</div>  
</div>	     
<?php
	
}else {
	echo '<h4>' . __( 'Testimonials not found', 'arkahost' ) . '</h4> <a href="'.admin_url('post-new.php?post_type=testimonials').'"><i class="fa fa-plus"></i> Add New Testimonial</a>';
}	
	
?>