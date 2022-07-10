<?php
/**
 #		(c) king-theme.com
 */
get_header(); ?>
	
	<?php $king->breadcrumb(); ?>

	<div id="primary" class="content_fullwidth">
		<div id="content" class="container">

			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'page' ); ?>
			<?php endwhile; // end of the loop. ?>

			<?php $king->pagination(); ?>
			
		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>