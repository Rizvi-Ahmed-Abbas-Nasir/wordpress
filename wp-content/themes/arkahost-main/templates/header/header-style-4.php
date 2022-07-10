<?php
/**
*	This file has been preloaded, so you can wp_enqueue_style to out in wp_head();
*/	

	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	global $king, $king_whmcs;
	
	wp_enqueue_style('king-menu-4');
	
?>
<!--Header 4-->
<div class="top_nav">
	<div class="container">
	    <div class="left">
		   	<?php  if( !empty( $king->cfg['topSocials'] ) && $king->cfg['topSocials'] =='show' ){
		    	$king->socials( 'topsocial', 5 );
	        } ?>
			<?php
			if( isset( $king->cfg['wpml_top'] ) && $king->cfg['wpml_top'] =='show' )
			{
				do_action('wpml_add_language_selector');
			}
			?>			
	    </div><!-- end left -->
	    
	    <div class="right<?php if( isset( $king->cfg['topInfoLogin'] ) &&  $king->cfg['topInfoLogin'] == 'hide' ) echo " nologin";?>">
	       
	    <?php
			if( !empty( $king->cfg['topInfoLogin'] ) ){ ?>
				<?php if($king->cfg['topInfoLogin'] == 'wp'): ?>
					<?php if ( is_user_logged_in() ) { ?>
						<a href="<?php echo get_edit_profile_url(); ?>" class="tpbut">
							<i class="fa fa-user"></i>&nbsp; <?php _e( 'My Profile', 'arkahost' ); ?>
						</a>
					<?php }else{ ?>
						<a href="<?php echo SITE_URI; ?>/?action=login" class="tpbut">
							<i class="fa fa-user"></i>&nbsp; <?php _e( 'Login', 'arkahost' ); ?>
						</a>
					<?php } ?>
				<?php elseif($king->cfg['topInfoLogin'] == 'whmpress'):
						//check WHMPRESS is installed or not
						if(function_exists("whmpress_url_function")){
						?>
							<a href="<?php echo whmpress_url_function(array('type' => 'client_area')); ?>" class="tpbut bridge_link">
								<i class="fa fa-user"></i>&nbsp; <?php _e( 'Client Area', 'arkahost' ); ?>
							</a>
						<?php
						}else{
						?>
						<a href="#" class="tpbut bridge_link">
							<?php _e( 'WHMPRESS not found!', 'arkahost' );?>
						</a>
						<?php
						}
						
					elseif($king->cfg['topInfoLogin'] == 'whmcs'): ?>
					<?php if($king_whmcs->is_client_loggedin()): ?>
						<a href="<?php echo get_permalink($king_whmcs->get_bridge_page_id()); ?>?ccce=clientarea" class="tpbut bridge_link">
							<i class="fa fa-user"></i>&nbsp; <?php _e( 'Client Area', 'arkahost' ); ?>
						</a>
					<?php else: ?>
						<a href="<?php echo get_permalink($king_whmcs->get_bridge_page_id()); ?>?ccce=clientarea" class="tpbut bridge_link">
							<i class="fa fa-user"></i>&nbsp; <?php _e( 'Login', 'arkahost' ); ?>
						</a>
					<?php
						endif;
					elseif($king->cfg['topInfoLogin'] == 'custom'):
							$login_link = isset($king->cfg['login_link_custom'])?$king->cfg['login_link_custom']:'';
							$login_target = isset($king->cfg['login_link_target'])?$king->cfg['login_link_target']:'_blank';
						?>
						<a href="<?php echo esc_url($login_link); ?>" target="<?php echo esc_attr($login_target); ?>" class="tpbut">
							<i class="fa fa-user"></i>&nbsp; <?php _e( 'Login', 'arkahost' ); ?>
						</a>
				<?php endif; ?>
	    <?php } ?>
			<?php 
			if ( has_nav_menu( 'top_nav' ) ){
					wp_nav_menu( array( 
						'theme_location'  => 'top_nav', 
						'menu_class'   => 'tplinks',
						'menu_id'   => '',
						'walker'    => new king_Walker_Top_Nav_Menu()
						)
					);
				}
			?>
	        <ul class="tplinks">
		        <?php if( !empty( $king->cfg['topInfoPhone'] ) ){ ?>
	            <li>
	            	<strong>
	            		<i class="fa fa-phone"></i> <?php echo esc_html( $king->cfg['topInfoPhone'] ); ?>
	            	</strong>
	            </li>
	            <?php }if( !empty( $king->cfg['topInfoEmail'] ) ){ ?>
	            <li>
	            	<a href="mailto:<?php echo esc_attr( $king->cfg['topInfoEmail'] ); ?>">
		            	<img src="<?php echo THEME_URI; ?>/assets/images/site-icon1.png" alt="ArkaHost" /> 
		            	<?php _e( 'WebMail', 'arkahost' ); ?>
		            </a>
		        </li>
		        <?php }if( !empty( $king->cfg['topInfoLiveChat'] ) ){ ?>
	            <li>
	            	<a href="<?php echo esc_url( $king->cfg['topInfoLiveChat'] ); ?>">
		            	<img src="<?php echo THEME_URI; ?>/assets/images/site-icon2.png" alt="ArkaHost"> 
		            	<?php _e( 'LiveChat', 'arkahost' ); ?>
		            </a>
		        </li>
		        <?php }if( !empty( $king->cfg['topInfoSupport'] ) ){ ?>
	            <li>
	            	<a href="<?php echo esc_url( $king->cfg['topInfoSupport'] ); ?>">
		            	<img src="<?php echo THEME_URI; ?>/assets/images/site-icon3.png" alt="ArkaHost"> 
		            	<?php _e( 'Support', 'arkahost' ); ?>
		            </a>
		        </li>
		        <?php } ?>
	        </ul>    
	    </div><!-- end right --> 
	</div>
</div>
<div class="clearfix"></div>
<!--Header default-->
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
