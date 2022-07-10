<?php
/**
*	This file has been preloaded, so you can wp_enqueue_style to out in wp_head();
*/	

	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	global $king, $king_whmcs;
	
	wp_enqueue_style('king-menu-3');
	
?>
<!--Header Style 2-->
<div class="top_header header-top3">
	<div class="container">
	        
	    <div class="left">	
		<!-- Logo -->
	    	<div class="logo">
	    		<a href="<?php echo esc_url(home_url('/')); ?>" id="logo">
	    			<img src="<?php echo esc_url( $king->cfg['logo'] ); ?>" alt="<?php bloginfo('description'); ?>" />
				</a>
	    	</div>
	          
	    </div><!-- end left -->
	    
	    <div class="right">
	    	<?php if( !empty( $king->cfg['topInfoPhone'] ) ){ ?>
				<span class="call-us-phone"><?php _e('CAll Us:', 'arkahost' ); ?> <strong><?php echo esc_attr( $king->cfg['topInfoPhone'] ); ?></strong></span>
			<?php }if( !empty( $king->cfg['topInfoLiveChat'] ) ){ ?>	
		        <a href="<?php echo esc_attr( $king->cfg['topInfoLiveChat'] ); ?>" class="chat">
			        <i class="fa fa-comments-o"></i> <?php _e('Live Chat', 'arkahost' ); ?>
			    </a>
		    <?php }if( !empty( $king->cfg['topInfoSupport'] ) ){ ?>	
		        <a href="<?php echo esc_url( $king->cfg['topInfoSupport'] ); ?>" class="chat">
			        <i class="fa fa-question-circle"></i> <?php _e('Submit Ticket', 'arkahost' ); ?>
			    </a>
		    <?php }

		    if( !empty( $king->cfg['topInfoLogin'] ) ){ ?>	        					
				<?php if($king->cfg['topInfoLogin'] == 'wp'): ?>
					<?php if ( is_user_logged_in() ) { ?>
						<a href="<?php echo get_edit_profile_url(); ?>" class="but">
							<i class="fa fa-user"></i>&nbsp; <?php _e( 'My Profile', 'arkahost' ); ?>
						</a>
					<?php }else{ ?>
						<a href="<?php echo SITE_URI; ?>/?action=login" class="but">
							<i class="fa fa-user"></i>&nbsp; <?php _e( 'Login', 'arkahost' ); ?>
						</a>
					<?php } ?>
				<?php elseif($king->cfg['topInfoLogin'] == 'whmcs'): ?>
					<?php if($king_whmcs->is_client_loggedin()): ?>
						<a href="<?php echo get_permalink($king_whmcs->get_bridge_page_id()); ?>?ccce=clientarea" class="but bridge_link">
							<i class="fa fa-user"></i>&nbsp; <?php _e( 'Client Area', 'arkahost' ); ?>
						</a>
					<?php else: ?>
						<a href="<?php echo get_permalink($king_whmcs->get_bridge_page_id()); ?>?ccce=clientarea" class="but bridge_link">
							<i class="fa fa-user"></i>&nbsp; <?php _e( 'Login', 'arkahost' ); ?>
						</a>
					<?php endif;
				elseif($king->cfg['topInfoLogin'] == 'custom'):
							$login_link = isset($king->cfg['login_link_custom'])?$king->cfg['login_link_custom']:'';
							$login_target = isset($king->cfg['login_link_target'])?$king->cfg['login_link_target']:'_blank';
						?>
						<a href="<?php echo esc_url($login_link); ?>" target="<?php echo esc_attr($login_target); ?>" class="but">
							<i class="fa fa-user"></i>&nbsp; <?php _e( 'Login', 'arkahost' ); ?>
						</a>
				<?php endif; ?>
				
	        <?php } ?>     
	    </div><!-- end right -->
	        
	</div>
</div>
<div class="clearfix"></div>
<header class="header sty3">
	<div class="menu_main">
		<div class="container">
		<!-- Navigation Menu -->    
	      <div class="navbar yamm navbar-default">
				<div class="navbar-header">
					<div class="navbar-toggle .navbar-collapse .pull-right " data-toggle="collapse" data-target="#navbar-collapse-1"> 
					  <span><?php _e( 'Menu', 'arkahost' ); ?></span>
					  <button type="button"> <i class="fa fa-bars"></i></button>
					</div>
				</div>
				<div id="navbar-collapse-1" class="navbar-collapse collapse pull-left">
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
						        <span class="cart-items"><?php echo king::esc_js( WC()->cart->cart_contents_count ); ?></span>
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
<div class="clearfix margin_top6 res_margin_top47"></div>