<?php
/**
    * Template Name: Page Full
	* (c) king-theme.com
*/
get_header(); ?>

	<?php $king->breadcrumb(); ?>
	
	<div id="container_full" class="site-content">
		<div id="content" class="row">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'page' ); ?>
			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>