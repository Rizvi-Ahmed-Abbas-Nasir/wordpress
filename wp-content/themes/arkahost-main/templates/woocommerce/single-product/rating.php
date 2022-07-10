<?php
/**
 * Single Product Rating
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     4.3.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $king;

if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' )
	return;

$count   = $product->get_rating_count();
$average = $product->get_average_rating();

if ( $count > 0 ) : ?>

	<div class="woocommerce-product-rating" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
		<div class="star-rating" title="<?php printf( __( 'Rated %s out of 5', 'arkahost' ), $average ); ?>">
			<span style="width:<?php echo ( ( $average / 5 ) * 100 ); ?>%">
				<strong itemprop="ratingValue" class="rating"><?php $king->ext['pr']( $average ); ?></strong> <?php _e( 'out of 5', 'arkahost' ); ?>
			</span>
		</div>
		<a href="#reviews" class="woocommerce-review-link" rel="nofollow"><?php printf( _n( '%s Review', '%s Reviews', $count, 'arkahost' ), '<span itemprop="ratingCount" class="count">' . $count . '</span>' ); ?></a>|
		<a href="#reviews" class="go-to-review-form woocommerce-review-link" rel="nofollow"><span>Add review</span></a>
	</div>

<?php endif; ?>
