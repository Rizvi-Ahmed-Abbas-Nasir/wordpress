<?php
/**
 * Single Product tabs
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Filter tabs and allow third parties to add their own
 *
 * Each tab is an array containing title, callback and priority.
 * @see woocommerce_default_product_tabs()
 */
$tabs = apply_filters( 'woocommerce_product_tabs', array() );

if ( ! empty( $tabs ) ) : ?>
		<div class="row">
			<div class="col-lg-12">
				<div class="woocommerce-tabs">
					<ul class="tabs">
						<?php foreach ( $tabs as $key => $tab ) : ?>

							<li class="<?php echo esc_attr( $key ); ?>_tab">
								<a href="#tab-<?php echo esc_attr( $key ); ?>"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ) ?></a>
							</li>

						<?php endforeach; ?>
					</ul>
					<?php foreach ( $tabs as $key => $tab ) : ?>

						<div class="panel entry-content" id="tab-<?php echo esc_attr( $key ); ?>">
							<?php call_user_func( $tab['callback'], $key, $tab ) ?>
						</div>

					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="container"> 
			<hr class="mt-25 mb-30">
			</div>
		</div>
<?php endif; ?>
