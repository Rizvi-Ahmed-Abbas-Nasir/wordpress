<?php
/**
 * (c) King-Theme.com
 */
 
 global $post;
 $sidebar = get_post_meta( $post->ID,'_king_page_sidebar' , true );
 if( empty( $sidebar ) ){
	 $sidebar = 'sidebar';
 }
?>

<?php if ( is_active_sidebar( $sidebar ) ) : ?>
	<div id="secondary" class="widget-area" role="complementary">
		<?php dynamic_sidebar( $sidebar ); ?>
	</div><!-- #secondary -->
<?php endif; ?>