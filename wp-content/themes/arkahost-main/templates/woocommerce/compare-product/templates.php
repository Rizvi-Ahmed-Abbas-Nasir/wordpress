<?php
/**
 * Compare template
 */
if( defined('WOOCOMMERCE_USE_CSS') && WOOCOMMERCE_USE_CSS ) wp_dequeue_style('woocommerce_frontend_styles');
global $king;
$is_iframe = (bool)( isset( $_REQUEST['iframe'] ) && $_REQUEST['iframe'] );

wp_enqueue_script( 'jquery-fixedheadertable', king_WOOCOMPARE_URL . 'assets/js/jquery.dataTables.min.js', array('jquery'), '1.3', true );
wp_enqueue_script( 'jquery-fixedcolumns', king_WOOCOMPARE_URL . 'assets/js/FixedColumns.min.js', array('jquery', 'jquery-fixedheadertable'), '1.3', true );

$widths = array();
foreach( $products as $_product ) $widths[] = '{ "sWidth": "205px", resizeable:true }';

/** FIX WOO 2.1 */
$wc_get_template = function_exists('wc_get_template') ? 'wc_get_template' : 'woocommerce_get_template';

?><!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" class="ie"<?php language_attributes() ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" class="ie"<?php language_attributes() ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" class="ie"<?php language_attributes() ?>>
<![endif]-->
<!--[if IE 9]>
<html id="ie9" class="ie"<?php language_attributes() ?>>
<![endif]-->
<!--[if gt IE 9]>
<html class="ie"<?php language_attributes() ?>>
<![endif]-->
<!--[if !IE]>
<html <?php language_attributes() ?>>
<![endif]-->
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width" />
    <link rel="profile" href="http://gmpg.org/xfn/11" />
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" />
    <link rel="stylesheet" href="<?php echo esc_url( $this->stylesheet_url() ) ?>" type="text/css" />
    <link rel="stylesheet" href="<?php echo king_WOOCOMPARE_URL ?>assets/css/colorbox.css"/>
    <link rel="stylesheet" href="<?php echo king_WOOCOMPARE_URL ?>assets/css/jquery.dataTables.css"/>

    <?php wp_head() ?>

    <style type="text/css">
        body.loading {
            background: url("<?php echo king_WOOCOMPARE_URL ?>assets/images/colorbox/loading.gif") no-repeat scroll center center transparent;
        }
    </style>
</head>

<?php global $product; ?>

<!-- START BODY -->
<body <?php body_class('woocommerce') ?>>

<h1>
	
    <?php 
		$table_text = $king->cfg['woo_comp_table_title'];
		echo esc_html( $table_text ); 
	?>
    <?php if ( ! $is_iframe ) : ?><a class="close" href="#"><?php _e( 'Close window [X]', 'arkahost' ) ?></a><?php endif; ?>
</h1>

<table class="compare-list" width="100%" cellpadding="0" cellspacing="0"<?php if ( empty( $products ) ) echo ' style="width:100%"' ?>>
    <thead>
    <tr>
        <th>&nbsp;</th>
        <?php foreach( $products as $i => $_product ) : ?>
            <td></td>
        <?php endforeach; ?>
    </tr>
    </thead>
    <tfoot>
    <tr>
        <th>&nbsp;</th>
        <?php foreach( $products as $i => $_product ) : ?>
            <td></td>
        <?php endforeach; ?>
    </tr>
    </tfoot>
    <tbody>

    <?php if ( empty( $products ) ) : ?>

        <tr class="no-products">
            <td><?php _e( 'No products added in the compare table.', 'arkahost' ) ?></td>
        </tr>

    <?php else : ?>
        <tr class="remove">
            <th>&nbsp;</th>
            <?php foreach( $products as $i => $_product ) : $product_class = ( $i % 2 == 0 ? 'odd' : 'even' ) . ' product_' . $_product->get_id(); ?>
                <td class="<?php echo esc_attr( $product_class ); ?>">
                    <a href="<?php echo add_query_arg( 'redirect', 'view', $this->remove_product_url( $_product->id ) ) ?>" data-product_id="<?php echo esc_attr( $_product->id ); ?>"><?php _e( 'Remove', 'arkahost' ) ?> <span class="remove">x</span></a>
                </td>
            <?php endforeach ?>
        </tr>
		
        <?php 
		
		foreach ( $fields as $field => $name ) : ?>
		
			<!-- removed price, add-to-cart fields in comparision table -->
			<?php if($field == 'price' or $field == 'add-to-cart') { 
				echo "";
			} else {
			?>
			
            <tr class="<?php echo esc_attr( $field ); ?>">
			
                <th>
                    <?php echo esc_html( $name ); ?>
                    <?php if ( $field == 'image' ) echo '<div class="fixed-th"></div>'; ?>
                </th>

                <?php foreach( $products as $i => $_product ) : $product_class = ( $i % 2 == 0 ? 'odd' : 'even' ) . ' product_' . $_product->get_id() ?>
                    <td class="<?php echo esc_attr( $product_class ); ?>"><?php
                        switch( $field ) {

                            case 'image':
                                echo '<div class="image-wrap">' . wp_get_attachment_image( $_product->fields[$field], 'king-woocompare-image' ) . '</div>';
                                break;

                            case 'add-to-cart':
                                $wc_get_template( 'loop/add-to-cart.php' );
                                break;

                            default:
                                echo empty( $_product->fields[$field] ) ? '&nbsp;' : $_product->fields[$field];
                                break;
                        }
                        ?>
                    </td>
                <?php endforeach ?>

            </tr>
			<?php } ?>
        <?php endforeach; ?>
		<!-- repeat price, add-to-cart fields in comparision table -->
        <?php if ( $repeat_price == 'yes' && isset( $fields['price'] ) ) : ?>
            <tr class="price repeated">
                <th><?php echo esc_html( $fields['price'] ); ?></th>

                <?php foreach( $products as $i => $_product ) : $product_class = ( $i % 2 == 0 ? 'odd' : 'even' ) . ' product_' . $_product->get_id() ?>
                    <td class="<?php echo esc_attr( $product_class ); ?>">
	                    <?php echo esc_html($_product->fields['price'] ); ?>
	                </td>
                <?php endforeach; ?>

            </tr>
        <?php endif; ?>

        <?php if ( $repeat_add_to_cart == 'yes' && isset( $fields['add-to-cart'] ) ) : ?>
            <tr class="add-to-cart repeated">
                <th><?php $king->ext['pr']( $fields['add-to-cart'] ); ?></th>

                <?php 
				global $product;
				$tmp = $product;
				foreach( $products as $i => $_product ) : 
					$product_class = ( $i % 2 == 0 ? 'odd' : 'even' ) . ' product_' . $_product->get_id();
					$product = $_product;
				?>
                    <td class="<?php echo esc_attr( $product_class ); ?>"><?php $wc_get_template( 'loop/add-to-cart.php' ); ?></td>
                <?php endforeach; 
				$product = $tmp;
				?>

            </tr>
        <?php endif; ?>

    <?php endif; ?>

    </tbody>
</table>

<?php do_action('wp_print_footer_scripts'); ?>
<?php do_action('wp_footer'); ?>
<script type="text/javascript">

    jQuery(document).ready(function($){
        <?php if ( $is_iframe ) : ?>$('a').attr('target', '_parent');<?php endif; ?>

        var oTable;
        $('body').on( 'king_woocompare_render_table', function(){
            if( $( window ).width() > 767 ) {
                oTable = $('table.compare-list').dataTable( {
                    "sScrollX": "100%",
                    //"sScrollXInner": "150%",
                    "bScrollInfinite": true,
                    "bScrollCollapse": true,
                    "bPaginate": false,
                    "bSort": false,
                    "bInfo": false,
                    "bFilter": false,
                    "bAutoWidth": false
                } );

                new FixedColumns( oTable );
                $('<table class="compare-list" />').insertAfter( $('h1') ).hide();
            }
        }).trigger('king_woocompare_render_table');

        // add to cart
        var button_clicked;
        $(document).on('click', 'a.add_to_cart_button', function(){
            button_clicked = $(this);
            button_clicked.block({message: null, overlayCSS: {background: '#fff no-repeat center', backgroundSize: '16px 16px', opacity: 0.6}});
        });

        // remove add to cart button after added
        $('body').on('added_to_cart', function(){
            button_clicked.hide();

            <?php if ( $is_iframe ) : ?>
            $('a').attr('target', '_parent');

            // Replace fragments
            if ( fragments ) {
                $.each(fragments, function(key, value) {console.log( key, window.parent.document );
                    $(key, window.parent.document).replaceWith(value);
                });
            }
            <?php endif; ?>
        });

        // close window
        $(document).on( 'click', 'a.close', function(e){
            e.preventDefault();
            window.close();
        });

        $(window).on( 'king_woocompare_product_removed', function(){
            if( $( window ).width() > 767 ) {
                oTable.fnDestroy(true);
            }
            $('body').trigger('king_woocompare_render_table');
        });

    });

</script>

</body>
</html>