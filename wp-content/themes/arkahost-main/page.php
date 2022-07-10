<?php
/**
 #		(c) king-theme.com
 */
get_header(); ?>

	<?php $king->breadcrumb(); ?>
	
	<div id="primary" class="site-content">
		<div class="container">
			<div id="content" class="row">
				
				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'content', 'page' ); ?>
					
					<?php					
						if ( comments_open() ) {
							echo '<div class="container">';
							comments_template();
							echo '</div>';
						}
					?>
				<?php endwhile; // end of the loop. ?>
			</div>
		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>