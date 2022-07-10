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

					<header>
						<?php
	
							$category_description = category_description();
							if ( ! empty( $category_description ) ){
								echo apply_filters( 'category_archive_meta', '<div class="category-archive-meta">' . $category_description . '</div>' );
							}
									
						?>
					</header>
					
					<?php /* Start the Loop */ ?>
					<?php while ( have_posts() ) : the_post(); ?>
	
						<?php
	
							get_template_part( 'content' );  
							
						?>
	
					<?php endwhile; ?>
	
					<?php $king->pagination(); ?>
	
				<?php else : ?>
	
					<article id="post-0" class="post no-results not-found">
						<header class="entry-header">
							<h1 class="entry-title"><?php _e( 'Nothing Found', 'arkahost' ); ?></h1>
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
