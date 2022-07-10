<?php
/**
 * Single Product Price, including microdata for SEO
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $king;

?>

	<div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="price_wrapper">

		<p class="price price size-lg"><?php $king->ext['pr']( $product->get_price_html() ); ?></p>

		<meta itemprop="price" content="<?php $king->ext['pr']( $product->get_price() ); ?>" />
		<meta itemprop="priceCurrency" content="<?php echo get_woocommerce_currency(); ?>" />
		<link itemprop="availability" href="http://schema.org/<?php $king->ext['pr']( $product->is_in_stock() ? 'InStock' : 'OutOfStock' ); ?>" />

	</div>
