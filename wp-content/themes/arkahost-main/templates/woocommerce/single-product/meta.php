<?php
/**
 * Single Product Meta
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     4.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product, $king;

$cat_count = sizeof( get_the_terms( $post->ID, 'product_cat' ) );
$tag_count = sizeof( get_the_terms( $post->ID, 'product_tag' ) );

?>

<div class="product_meta posted-in">
<hr class="mt-25 mb-30">
	<?php do_action( 'woocommerce_product_meta_start' ); ?>

	<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

		<span class="sku_wrapper"><?php _e( 'SKU:', 'arkahost' ); ?> <span class="sku" itemprop="sku"><?php $king->ext['pr'] ( $sku = $product->get_sku() ) ? $sku : __( 'N/A', 'arkahost' ); ?></span>.</span>

	<?php endif; ?>

	<?php print( wc_get_product_category_list($post->ID, ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', $cat_count, 'arkahost' ) . ' ', '.</span>' ) ); ?>

	<?php print( wc_get_product_tag_list($post->ID,  ', ', '<span class="tagged_as">' . _n( 'Tag:', 'Tags:', $tag_count, 'arkahost' ) . ' ', '.</span>' ) ); ?>

	<?php do_action( 'woocommerce_product_meta_end' ); ?>

</div>
