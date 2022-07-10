<?php
/**
 * Wishlist page template
 */
 
global $wpdb, $king_wishlist, $woocommerce, $king;


if( isset( $_GET['user_id'] ) && !empty( $_GET['user_id'] ) ) {
    $user_id = $_GET['user_id'];
} elseif( is_user_logged_in() ) {
    $user_id = get_current_user_id();
}

$current_page = 1;
$limit_sql = '';

$per_page = 100;


$count = array();

if( is_user_logged_in() ) {
    $count = $wpdb->get_results( $wpdb->prepare( 'SELECT COUNT(*) as `cnt` FROM `' . king_WISHLIST_TABLE . '` WHERE `user_id` = %d', $user_id  ), ARRAY_A );
} elseif( king_usecookies() ) {
    $count[0]['cnt'] = count( king_getcookie( 'king_wishlist_products' ) );
} else {
    $count[0]['cnt'] = count( $_SESSION['king_wishlist_products'] );
}
if( !empty($count[0]) ){
	if( !empty( $count[0]['cnt'] ) ){
		$count = $count[0]['cnt'];
	}else{
		$count = 0;
	}
}else{
	$count = 0;
} 

$total_pages = $count/$per_page;
if( $total_pages > 1 ) {
        $current_page = max( 1, get_query_var( 'page' ) );
        
        $page_links = paginate_links( array(
            'base' => get_pagenum_link( 1 ) . '%_%',
            'format' => '&page=%#%',
            'current' => $current_page,
            'total' => $total_pages,
            'show_all' => true
        ) );
    }

$limit_sql = "LIMIT " . ( $current_page - 1 ) * 1 . ',' . $per_page;


if( is_user_logged_in() )
    { $wishlist = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `" . king_WISHLIST_TABLE . "` WHERE `user_id` = %s" . $limit_sql, $user_id ), ARRAY_A ); }
elseif( king_usecookies() )
    { $wishlist = king_getcookie( 'king_wishlist_products' ); }
else
    { $wishlist = isset( $_SESSION['king_wishlist_products'] ) ? $_SESSION['king_wishlist_products'] : array(); }

// Start wishlist page printing
if( function_exists('wc_print_notice') ): wc_print_notices(); else: $woocommerce->show_messages(); endif; ?>

<div class="container">
<div id="king-wishlist-messages"></div>

<form id="king-wishlist-form" action="<?php echo esc_url( $king_wishlist->get_wishlist_url() ) ?>" method="post">
    <?php
    do_action( 'king_wishlist_before_wishlist_title' );
    
    $wishlist_title = $king->cfg['wl_title'];
    if( !empty( $wishlist_title ) )
        { echo apply_filters( 'king_wishlist_title', '<h2>' . $wishlist_title . '</h2>' ); }
    
    do_action( 'king_wishlist_before_wishlist' );
    ?>
    <table class="shop_table cart wishlist_table" cellspacing="0">
    	<thead>
    		<tr>
    			<th class="product-remove"></th>
    			<th class="product-thumbnail"></th>
    			<th class="product-name"><span class="nobr"><?php _e( 'Product Name', 'arkahost' ) ?></span></th>
    			<th class="product-price"><span class="nobr"><?php _e( 'Unit Price', 'arkahost' ) ?></span></th>
    			<th><span class="nobr"><?php _e( 'Stock Status', 'arkahost' ) ?></span></th>
                <th><span class="nobr"></th>
    		</tr>
    	</thead>
        <tbody>
            <?php            
            if( count( $wishlist ) > 0 ) :
                foreach( $wishlist as $values ) :   
                    if( !is_user_logged_in() ) {
        				if( isset( $values['add-to-wishlist'] ) && is_numeric( $values['add-to-wishlist'] ) ) {
        					$values['prod_id'] = $values['add-to-wishlist'];
        					$values['ID'] = $values['add-to-wishlist'];
        				} else {
        					$values['prod_id'] = $values['product_id'];
        					$values['ID'] = $values['product_id'];
        				}
        			}
                                     
                    $product_obj = get_product( $values['prod_id'] );
                    
                    if( $product_obj !== false && $product_obj->exists() ) : ?>
                    <tr id="king-wishlist-row-<?php echo esc_attr( $values['ID'] ); ?>">
                        <td class="product-remove"><div><a href="javascript:void(0)" onclick="remove_item_from_wishlist( '<?php echo esc_url( $king_wishlist->get_remove_url( $values['ID'] ) )?>', 'king-wishlist-row-<?php echo esc_attr( $values['ID'] ); ?>');" class="remove" title="<?php _e( 'Remove this product', 'arkahost' ) ?>">&times;</a></td>
                        <td class="product-thumbnail">
                            <a href="<?php echo esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product', $values['prod_id'] ) ) ) ?>">
                                <?php $king->ext['pr']( $product_obj->get_image() ); ?>
    						</a>
						</td>
                        <td class="product-name">
                            <a href="<?php echo esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product', $values['prod_id'] ) ) ) ?>"><?php echo apply_filters( 'woocommerce_in_cartproduct_obj_title', $product_obj->get_title(), $product_obj ) ?></a>
                        </td>
                        <td class="product-price">
                            <?php
                            if(function_exists('wc_price')){
                                if( get_option( 'woocommerce_display_cart_prices_excluding_tax' ) == 'yes' )
                                { echo apply_filters( 'woocommerce_cart_item_price_html', wc_price( $product_obj->get_price_excluding_tax() ), $values, '' ); }
                                else
                                { echo apply_filters( 'woocommerce_cart_item_price_html', wc_price( $product_obj->get_price() ), $values, '' ); }
                            }else{
                                if( get_option( 'woocommerce_display_cart_prices_excluding_tax' ) == 'yes' )
                                { echo apply_filters( 'woocommerce_cart_item_price_html', woocommerce_price( $product_obj->get_price_excluding_tax() ), $values, '' ); }
                                else
                                { echo apply_filters( 'woocommerce_cart_item_price_html', woocommerce_price( $product_obj->get_price() ), $values, '' ); }
                            }

                            ?>
                        </td>
                        <td class="product-stock-status">
                            <?php
                            $availability = $product_obj->get_availability();
                            $stock_status = $availability['class'];
                            
                            if( $stock_status == 'out-of-stock' ) {
                                $stock_status = "Out";
                                echo '<span class="wishlist-out-of-stock">' . __( 'Out of Stock', 'arkahost' ) . '</span>';   
                            } else {
                                $stock_status = "In";
                                echo '<span class="wishlist-in-stock">' . __( 'In Stock', 'arkahost' ) . '</span>';
                            }
                            ?>
                        </td>
                        <td class="product-add-to-cart">
                            <?php echo king_WISHLIST_UI::add_to_cart_button( $values['prod_id'], $availability['class'] ) ?>
                        </td>
                    </tr>
                    <?php
                    endif;
                endforeach;
            else: ?>
                <tr>
                    <td colspan="6" class="wishlist-empty"><?php _e( 'No products were added to the wishlist', 'arkahost' ) ?></td>
                </tr>       
            <?php
            endif;
            
            if( isset( $page_links ) ) : ?>
            <tr>
                <td colspan="6"><?php $king->ext['pr']( $page_links ); ?></td>
            </tr>
            <?php endif ?>
        </tbody>
     </table>
     <?php
     do_action( 'king_wishlist_after_wishlist' );
     
     king_wishlist_get_template( 'share.php' );
     
     do_action( 'king_wishlist_after_wishlist_share' );
     ?>
</form>
</div>