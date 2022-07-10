<?php
/**
 * (c) king-theme.com
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $king;

get_header();

$sidebar = false;
$column = 2;

if( isset( $king->cfg[ 'our_works_listing_layout' ] ) && !empty( $king->cfg[ 'our_works_listing_layout' ] ))
	$column = $king->cfg[ 'our_works_listing_layout' ];

if( isset( $king->cfg[ 'our_works_sidebar' ] ) && !empty( $king->cfg['our_works_sidebar'] ) )
	$sidebar = true;
?>

	<?php $king->breadcrumb(); ?>
	<div id="primary" class="site-content container-content content ">
		<div id="content" class="row row-content container archive-our-works">
			<div class="<?php $king->ext['pr'] ($sidebar)? 'col-md-9' : 'col-md-12';?>">

				<?php
				if ( have_posts() ) :
					if( $column < 5 ):

						get_template_part( 'templates/our-works-columns' );

					else:

						get_template_part( 'templates/our-works-masonry' );

					endif;

					$king->pagination();

	
				endif; ?>
				
			</div>
			<?php if ($sidebar):?>
			<div class="col-md-3">
				<?php if ( is_active_sidebar( $king->cfg[ 'our_works_sidebar' ] ) ) : ?>
					<div id="sidebar" class="widget-area king-sidebar king-our-works-sidebar">
						<?php dynamic_sidebar( $king->cfg[ 'our_works_sidebar' ] ); ?>
					</div><!-- #secondary -->
				<?php endif; ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
				
<?php get_footer(); ?>					
