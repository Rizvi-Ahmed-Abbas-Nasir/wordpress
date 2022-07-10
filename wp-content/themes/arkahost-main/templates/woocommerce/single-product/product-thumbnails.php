<?php
/**
 * Single Product Thumbnails
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     4.3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !king_woo_gate( 'single-product'.DS.'product-thumbnails.php' ) ){return;}

global $post, $product, $woocommerce, $king;

if( empty( $king->cfg['mg_thumbnail_slider'] ) ){
	$king->cfg['mg_thumbnail_slider'] = 1;
}
$slider_active = $king->cfg['mg_thumbnail_slider'] == 1 ? true : false ;

$slider_item = !empty( $king->cfg['mg_slider_item'] )?$king->cfg['mg_slider_item']:1;
if(!empty($slider_item)){
	$slider_items = $slider_item;
} else {
	$slider_items = 3;
}
$attachment_ids = $product->get_gallery_image_ids();

if ( $attachment_ids ) {
	?>

	<div class="thumbnails <?php if( $slider_active ){ echo 'slider'; }else{ echo 'noslider'; } ?>">
	<ul class="king_magnifier_gallery">
        <?php
        $loop = 0;
        $columns = apply_filters( 'woocommerce_product_thumbnails_columns', $slider_items );

        foreach ( $attachment_ids as $attachment_id ) {
            $classes = array( 'king_magnifier_thumbnail' );

            if ( $loop == 0 || $loop % $columns == 0 ) {
                $classes[] = 'first';
            }

            if ( ( $loop + 1 ) % $columns == 0 ) {
                $classes[] = 'last';
            }
			$datarel = '';
			if( !king_magnifier_active() ):
				$datarel = ' data-rel="prettyPhoto[product-gallery]"';
			endif;
            $image       = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
            $image_class = esc_attr( implode( ' ', $classes ) );
            $image_title = esc_attr( get_the_title( $attachment_id ) );

            list( $thumbnail_url, $thumbnail_width, $thumbnail_height ) = wp_get_attachment_image_src( $attachment_id, "shop_catalog" );
            list( $magnifier_url, $magnifier_width, $magnifier_height ) = wp_get_attachment_image_src( $attachment_id, "shop_magnifier" );

            echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<li class="%s"><a href="%s" class="%s" title="%s" data-small="%s" %s>%s</a></li>', $image_class, $magnifier_url, $image_class, $image_title, $thumbnail_url, $datarel,$image ), $attachment_id, $post->ID, $image_class );

            $loop++;
        }
        ?>
        </ul>
	<?php if ( $slider_active ) : ?>
		<div id="slider-prev"></div>
		<div id="slider-next"></div>
	<?php endif; ?>
	</div>
	<?php
}
