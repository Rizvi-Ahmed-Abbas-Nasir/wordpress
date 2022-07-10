<?php
/**
 * The Template for displaying all single products.
 *
 * Override this template by copying it to yourtheme/woocommerce/single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header();

if( !king_woo_gate( 'single-product.php', true ) ){return;}

if( empty( $king->cfg['woo_product_layout'] ) ){
	$king->cfg['woo_product_layout'] = 'right';
}
if( !empty($_GET['layout']) ){
	$king->cfg['woo_product_layout'] = esc_attr( $_GET['layout'] );
}

?>


	<?php $king->breadcrumb(); ?>

	<div id="primary" class="single-product-content content site-content">
		<div class="container" id="content">		
			<?php
				switch( $king->cfg['woo_product_layout'] ){
					
					case 'left' :
					?>
						<div class="col-md-3 sdfsdf">
							<?php if ( is_active_sidebar( 'sidebar-woo-single' ) ) : ?>
								<div id="sidebar" class="widget-area king-sidebar king-single-sidebar">
									<?php dynamic_sidebar( 'sidebar-woo-single' ); ?>
								</div><!-- #secondary -->
							<?php endif; ?>
						</div>
						<div class="col-md-9">
					<?php
					break;
					case 'full' : echo '<div class="col-md-12">'; break;
					default : echo '<div class="col-md-9">'; break;
					
				}

				/**
				 * woocommerce_before_main_content hook
				 *
				 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
				 * @hooked woocommerce_breadcrumb - 20
				 */
				do_action( 'woocommerce_before_main_content' );
			?>
		
				<?php while ( have_posts() ) : the_post(); ?>
		
					<?php wc_get_template_part( 'content', 'single-product' ); ?>
		
				<?php endwhile; // end of the loop. ?>

			<?php
				
				/**
				 * woocommerce_after_main_content hook
				 *
				 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
				 */
				do_action( 'woocommerce_after_main_content' );
				
			?>
					
			</div>
			
			<?php if( $king->cfg['woo_product_layout'] != 'full' && $king->cfg['woo_product_layout'] != 'left' ){ ?>
				<div class="col-md-3">
					<?php if ( is_active_sidebar( 'sidebar-woo-single' ) ) : ?>
						<div id="sidebar" class="widget-area king-sidebar">
							<?php dynamic_sidebar( 'sidebar-woo-single' ); ?>
						</div><!-- #secondary -->
					<?php endif; ?>
				</div>
			 <?php } ?>
			
		</div>
	</div>
				
<?php get_footer(); ?>	
		
