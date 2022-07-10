<?php
/**
    * Template Name: Page Left Sidebar
	* (c) king-theme.com
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $king;
if( is_page() ){
	$sidebar = get_post_meta( $post->ID,'_king_page_sidebar' , true );
}else if( is_home() ){
	if( get_option( 'page_for_posts', true ) ){
		$sidebar = get_post_meta( get_option( 'page_for_posts', true ),'_king_page_sidebar' , true );
	}
}	
if( empty( $sidebar ) ){
	$sidebar = 'sidebar';
}
get_header();

?>

	<?php $king->breadcrumb(); ?>
	
	<div id="primary" class="site-content container-content content ">
		<div id="content" class="row row-content container">
			<div class="col-md-3">
				<?php if ( is_active_sidebar( $sidebar ) ) : ?>
					<div id="sidebar" class="widget-area king-sidebar">
						<?php dynamic_sidebar( $sidebar ); ?>
					</div><!-- #secondary -->
				<?php endif; ?>
			</div>
			<div class="col-md-9">
				<?php if ( have_posts() ) : ?>

					<?php while ( have_posts() ) : the_post(); ?>
						<?php get_template_part( 'content', get_post_format() ); ?>
					<?php endwhile; ?>
			
					<?php $king->pagination(); ?>
					
				<?php else : ?>
			
					<article id="post-0" class="post no-results not-found">
			
					<?php if ( current_user_can( 'edit_posts' ) ) :
						// Show a different message to a logged-in user who can add posts.
					?>
						<header class="entry-header">
							<h1 class="entry-title"><?php _e( 'No posts to display', 'arkahost' ); ?></h1>
						</header>
			
						<div class="entry-content">
							<p><?php printf( __( 'Ready to publish your first post? <a href="%s">Get started here</a>.', 'arkahost' ), admin_url( 'post-new.php' ) ); ?></p>
						</div><!-- .entry-content -->
			
					<?php else :
						// Show the default message to everyone else.
					?>
						<header class="entry-header">
							<h1 class="entry-title"><?php _e( 'Nothing Found', 'arkahost' ); ?></h1>
						</header>
			
						<div class="entry-content">
							<p><?php _e( 'Apologies, but no results were found. Perhaps searching will help find a related post.', 'arkahost' ); ?></p>
							<?php get_search_form(); ?>
						</div><!-- .entry-content -->
					<?php endif; // end current_user_can() check ?>
			
					</article><!-- #post-0 -->
			
				<?php endif; // end have_posts() check ?>
			</div>
		</div>
	</div>
				
<?php get_footer(); ?>		
