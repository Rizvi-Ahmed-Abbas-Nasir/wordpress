<?php
/**
 * Single Product Image
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     4.3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !king_woo_gate( 'single-product'.DS.'product-image.php' ) ){return;}

global $post, $woocommerce, $product, $king;

$image_title = esc_attr( get_the_title( get_post_thumbnail_id() ) );
$image_link  = wp_get_attachment_url( get_post_thumbnail_id() );
$image       = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_catalog' ), array( 'title' => $image_title ) );

?>
<div class="images">
	<?php
		if ( has_post_thumbnail() ) {

			$attachment_count = count( $product->get_gallery_image_ids() );

			if ( $attachment_count > 0 ) {
				$gallery = '[product-gallery]';
			} else {
				$gallery = '';
			} ?>

			<?php if( !king_magnifier_active() ): ?>

				<!-- Default Woocommerce Template -->

				<?php echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" itemprop="image" class="woocommerce-main-image zoom" title="%s" data-rel="prettyPhoto' . $gallery . '">%s</a>', $image_link, $image_title, $image ), $post->ID ); ?>

			<?php else: ?>

				<!-- Custom Magnifier Template -->

				<?php echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" itemprop="image" class="king_magnifier_zoom" title="%s" rel="thumbnails">%s</a>', $image_link, $image_title, $image ), $post->ID ); ?>
			<?php endif ?>

			<?php } else {

			echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="Placeholder" />', wc_placeholder_img_src() ), $post->ID );

		}
	?>

	<?php do_action( 'woocommerce_product_thumbnails' ); ?>

</div>


<?php

if( king_magnifier_active() ):

?>

<script type="text/javascript" charset="utf-8">
var king_magnifier_options = {
	enableSlider: <?php echo esc_attr( $king->cfg['mg_thumbnail_slider'] ) == 1 ? 'true' : 'false' ?>,

	<?php if( $king->cfg['mg_thumbnail_slider'] == 1 ): ?>
	sliderOptions: {
		responsive: false,
		circular: <?php if( $king->cfg['mg_thumbnail_circular'] == 1 ){echo 'true'; }else{ echo 'false'; } ?>,
		infinite: <?php if( $king->cfg['mg_thumbnail_infinite'] == 1 ){echo 'true'; }else{ echo 'false'; } ?>,
		direction: 'left',
		debug: false,
		auto: false,
		align: 'left',
		prev	: {
    		button	: "#slider-prev",
    		key		: "left"
    	},
    	next	: {
    		button	: "#slider-next",
    		key		: "right"
    	},
    	//width   : 470,
	    scroll : {
	    	items     : 1,
	    	pauseOnHover: true
	    }
//     	items   : {
//             width: 20
//         }
	},
	<?php endif ?>

	showTitle: false,
	zoomWidth: '<?php echo esc_attr( $king->cfg['mg_zoom_width'] ); ?>',
	zoomHeight: '<?php echo esc_attr( $king->cfg['mg_zoom_height'] ); ?>',
	position: '<?php echo esc_attr( $king->cfg['mg_zoom_position'] ); ?>',
	lensOpacity: <?php echo esc_attr( $king->cfg['mg_lens_opacity'] ); ?>,
	softFocus: <?php echo esc_attr( $king->cfg['mg_blur'] ) == 1 ? 'true' : 'false' ?>,
	adjustY: 0,
	disableRightClick: false,
	phoneBehavior: '<?php echo esc_attr( $king->cfg['mg_zoom_position_mobile'] ); ?>',
	loadingLabel: '<?php echo stripslashes($king->cfg['mg_loading_label']); ?>'
};
</script>
<script>
jQuery(document).ready(function($){

    var king_wcmg = $('.images');
    var king_wcmg_zoom  = $('.king_magnifier_zoom');
    var king_wcmg_image = $('.king_magnifier_zoom img');

    var king_wcmg_default_zoom = king_wcmg.find('.king_magnifier_zoom').attr('href');
    var king_wcmg_default_image = king_wcmg.find('.king_magnifier_zoom img').attr('src');

    king_wcmg.king_magnifier(king_magnifier_options);

    $( document ).on( 'found_variation', 'form.variations_form', function( event, variation ) {
        var image_magnifier = variation.image_magnifier ? variation.image_magnifier : king_wcmg_default_zoom;
        var image_src       = variation.image_src ? variation.image_src : king_wcmg_default_image;
		console.log(image_magnifier);
		console.log(image_src);
        king_wcmg_zoom.attr('href', image_magnifier);
        king_wcmg_image.attr('src', image_src);

        if( king_wcmg.data('king_magnifier') ) {
            king_wcmg.king_magnifier('destroy');
        }

        king_wcmg.king_magnifier(king_magnifier_options);
    }).on( 'reset_image', function( event ) {
            king_wcmg_zoom.attr('href', king_wcmg_default_zoom);
            //king_wcmg_image.attr('src', king_wcmg_default_image);

            if( king_wcmg.data('king_magnifier') ) {
                king_wcmg.king_magnifier('destroy');
            }

            king_wcmg.king_magnifier(king_magnifier_options);
        })

    $( 'form.variations_form .variations select').trigger('change');
});

</script>
<?php endif ?>
