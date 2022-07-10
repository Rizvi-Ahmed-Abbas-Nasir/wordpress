<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive.
 *
 * Override this template by copying it to yourtheme/woocommerce/archive-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $king_blog_id, $king, $woocommerce_loop;

get_header();

if( !king_woo_gate( 'archive-product.php' , true ) ){return;}

$cookie_shop_layout = 'king_' . get_template() . ( is_multisite() ? '_' . $king_blog_id : '' ) . '_shop_layout';
$woocommerce_loop['view'] = isset( $_COOKIE[ $cookie_shop_layout ] ) ? $_COOKIE[ $cookie_shop_layout ] : (!empty($king->cfg['woo_product_display'])?$king->cfg['woo_product_display']:'grid');
$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

if( empty( $king->cfg['woo_layout'] ) ){
	$king->cfg['woo_layout'] = 'right';
}
if( !empty($_GET['layout']) ){
	$king->cfg['woo_layout'] = esc_attr( $_GET['layout'] );
}

?>


	<?php $king->breadcrumb(); ?>

	<div id="primary" class="archive-product-content content site-content">
		<div class="container" id="content">
			<div class="row">
				<?php
					
					switch( $king->cfg['woo_layout'] ){
						
						case 'left' :
						?>
							<div class="col-md-3">
								<?php if ( is_active_sidebar( 'sidebar-woo' ) ) : ?>
									<div id="sidebar" class="widget-area king-sidebar">
										<?php dynamic_sidebar( 'sidebar-woo' ); ?>
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
			
					<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
			
						<h1 class="page-title"><?php woocommerce_page_title(); ?></h1>
			
					<?php endif; ?>
			
					<?php do_action( 'woocommerce_archive_description' ); ?>
			
					<?php if ( have_posts() ) : ?>
			
						<?php
							/**
							 * woocommerce_before_shop_loop hook
							 *
							 * @hooked woocommerce_result_count - 20
							 * @hooked woocommerce_catalog_ordering - 30
							 */
							do_action( 'woocommerce_before_shop_loop' );
						?>
			
						<?php woocommerce_product_loop_start(); ?>
			
							<?php woocommerce_product_subcategories(); ?>
			
							<?php while ( have_posts() ) : the_post(); ?>
			
								<?php wc_get_template_part( 'content', 'product' ); ?>
			
							<?php endwhile; // end of the loop. ?>
			
						<?php woocommerce_product_loop_end(); ?>
			
						<?php
							/**
							 * woocommerce_after_shop_loop hook
							 *
							 * @hooked woocommerce_pagination - 10
							 */
							do_action( 'woocommerce_after_shop_loop' );
						?>
			
					<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>
			
						<?php wc_get_template( 'loop/no-products-found.php' ); ?>
			
					<?php endif; ?>
			
					<script type='text/javascript'>
			        /* <![CDATA[ */
			        var king_shop_view_cookie = '<?php echo esc_attr( $cookie_shop_layout ); ?>';
			        /* ]]> */
			        </script>
				<?php
					/**
					 * woocommerce_after_main_content hook
					 *
					 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
					 */
					do_action( 'woocommerce_after_main_content' );
				?>
		
				</div>
				
				<?php if( $king->cfg['woo_layout'] != 'full' && $king->cfg['woo_layout'] != 'left' ){ ?>
					<div class="col-md-3">
						<?php if ( is_active_sidebar( 'sidebar-woo' ) ) : ?>
							<div id="sidebar" class="widget-area king-sidebar">
								<?php dynamic_sidebar( 'sidebar-woo' ); ?>
							</div><!-- #secondary -->
						<?php endif; ?>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
				
<?php get_footer(); ?>	
