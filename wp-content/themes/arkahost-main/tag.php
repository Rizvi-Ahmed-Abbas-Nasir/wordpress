<?php
/**
 * (c) king-theme.com
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $king;

get_header();

?>

	<?php $king->breadcrumb(); ?>

	<div id="primary" class="site-content container-content content ">
		<div id="content" class="row row-content container">
			<div class="col-md-9">

				<?php if ( have_posts() ) : ?>
	
					<?php $king->content_nav( 'nav-above' ); ?>
				
					<?php /* Start the Loop */ ?>
					<?php while ( have_posts() ) : the_post(); ?>
	
						<?php
						
							get_template_part( 'content' ); 
							
						?>
	
					<?php endwhile; ?>
	
					<?php $king->content_nav( 'nav-below' ); ?>
	
				<?php else : ?>
	
					<article id="post-0" class="post no-results not-found">
						<header class="entry-header">
							<h1 class="entry-title">Nothing Found</h1>
						</header><!-- .entry-header -->
	
						<div class="entry-content">
							<p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'arkahost' ); ?></p>
							<?php get_search_form(); ?>
						</div><!-- .entry-content -->
					</article><!-- #post-0 -->
	
				<?php endif; ?>
				
			</div>
			<div class="col-md-3">
				<?php if ( is_active_sidebar( 'sidebar' ) ) : ?>
					<div id="sidebar" class="widget-area king-sidebar">
						<?php dynamic_sidebar( 'sidebar' ); ?>
					</div><!-- #secondary -->
				<?php endif; ?>
			</div>
		</div>
	</div>
				
<?php get_footer(); ?>					
