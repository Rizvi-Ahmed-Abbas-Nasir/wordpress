<?php
/**
 * 
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     4.1.7
 */
 ?>
 <form role="search" method="get" class="woocommerce-product-search" action="<?php echo esc_url( home_url( '/'  ) ); ?>">
	<div class="input-group">
	<input type="search" class="form-control search-field" placeholder="<?php echo esc_attr_x( 'Search Products&hellip;', 'placeholder', 'arkahost' ); ?>" value="<?php echo get_search_query(); ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label', 'arkahost' ); ?>" />
	<span class="input-group-btn">
		<button type="submit" class="btn btn-default" value="<?php echo esc_attr_x( 'Search', 'submit button', 'arkahost' ); ?>"><i class="fa fa-search"></i></button>
	</span>
	</div>
	<input type="hidden" name="post_type" value="product" />
</form>
