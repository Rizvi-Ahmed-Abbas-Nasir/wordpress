<?php
/**
*	This file has been preloaded, so you can wp_enqueue_style to out in wp_head();
*/	

	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	global $king;
	
	wp_enqueue_style('king-menu-2');
	
?>
<!--Header 2-->
<header class="header">
	<div class="container">
	    <!-- Logo -->
	    <div class="logo">
		    <a href="<?php echo esc_url(home_url('/')); ?>" id="logo">
	    		<img src="<?php echo esc_url( $king->cfg['logo'] ); ?>" alt="<?php bloginfo('description'); ?>" />
			</a>
	    </div>
		<!-- Navigation Menu -->
	    <div class="menu_main">
	      <div class="navbar yamm navbar-default">
				<div class="navbar-header">
					<div class="navbar-toggle .navbar-collapse .pull-right " data-toggle="collapse" data-target="#navbar-collapse-1"> 
					  <span><?php _e( 'Menu', 'arkahost' ); ?></span>
					  <button type="button"> <i class="fa fa-bars"></i></button>
					</div>
				</div>
				<div id="navbar-collapse-1" class="navbar-collapse collapse pull-right">
					<?php 
					    
					    global $woocommerce;
					    
					    if( empty( $king->cfg['topInfoCart'] ) ){
							$king->cfg['topInfoCart'] = 'show';
						}
					    
					    if( $king->cfg['topInfoCart'] == 'show' && !empty( $woocommerce ) ){ 
						    
					?>
				        <div  class="tpbut three minicart-li">
					        <a href="<?php echo esc_url( $woocommerce->cart->get_cart_url() ); ?>" class="minicart-nav">
						        <i class="et-basket et"></i>
						        <span class="cart-items"><?php echo WC()->cart->cart_contents_count; ?></span>
						    </a>    
					        <ul class="dropdown-menu">
								<li><?php  
									if( function_exists( 'king_cart_func' ) ){
										echo '<div class="minicart-wrp">'.king_cart_func( array() ).'</div>';
									}
								?></li>
							</ul>
				        </div>
				    <?php } ?>

		            <nav class="pull-right"><?php $king->mainmenu(); ?></nav>
	   	        </div>
	      </div>
	    </div>
	<!-- end Navigation Menu -->
	</div>    
</header>
<div class="clearfix margin_bottom11 resp_margin_bottom68"></div>
