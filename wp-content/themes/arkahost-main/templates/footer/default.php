<?php
/*
*	(c) king-theme.com
*/	
	
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	global $king;
?>
<!--Footer Default-->
<footer class="footer">
	<div class="footer">
		<div class="ftop">
			<div class="container">
			
			    <div class="left">
			    	<h4 class="caps light">
				    	<strong><?php if( isset( $king->cfg['need_help_text'] )  && !empty( $king->cfg['need_help_text'] ) ){echo king::esc_js($king->cfg['need_help_text']);}else{_e( 'Need Help?', 'arkahost' );} ?></strong> 
				    	<?php if( isset( $king->cfg['call_us_text'] )  && !empty( $king->cfg['call_us_text'] ) ){echo king::esc_js($king->cfg['call_us_text']);}else{_e( 'Call Us 24/7:', 'arkahost' );} ?>
				    </h4>
				    <?php if( !empty( $king->cfg['topInfoPhone'] ) ){ ?>
			        <h1><?php echo esc_html( $king->cfg['topInfoPhone'] ); ?></h1>
			        <?php } ?>
			    </div><!-- end left -->
			    
			    <div class="right">
			    	<p><?php 
					if( isset( $king->cfg['newsletter_desc'] )  && !empty( $king->cfg['newsletter_desc'] ) ){
						echo king::esc_js($king->cfg['newsletter_desc']);
					}else{
						_e( 'Sign up to Newsletter to get special offers', 'arkahost' );
					}
					?></p>
			    	<form method="post" id="king_newsletter">
			        	<input class="newsle_eminput" name="king_email" id="king_email" value="" placeholder="<?php
						if( isset( $king->cfg['newsletter_text_input'] )  && !empty( $king->cfg['newsletter_text_input'] ) ){
							echo king::esc_js($king->cfg['newsletter_text_input']);
						}else{
							_e( 'Please enter your email...', 'arkahost' );
						}
						?>" type="text" />
			            <input name="submit" id="king_newsletter_submit" value="<?php _e( 'Sign Up', 'arkahost' ); ?>" class="input_submit" type="submit" />
						
			        </form>
					<div id="king_newsletter_status">&nbsp;</div>
			        <script language="javascript" type="text/javascript">
						jQuery(document).ready(function($) {
							
							$("#king_newsletter").submit(function(){
								king_submit_newsletter();
								return false;
							});
							
							function king_submit_newsletter(){
								
								var email = jQuery("#king_email").val();
								
								if( email.length < 8 || email.indexOf('@') == -1 || email.indexOf('.') == -1 ){
									$('#king_email').
									animate({marginLeft:-10, marginRight:10},100).
									animate({marginLeft:0, marginRight:0},100).
									animate({marginLeft:-10, marginRight:10},100).
									animate({marginLeft:0, marginRight:0},100);
									return false;
								}
								$('#king_newsletter_status').html('<i style="color:#ccc" class="fa fa-spinner fa-pulse fa-2x"></i> Sending...');
								$.ajax({
									type:'POST',
									data:{	
										"action" : "king_newsletter",
										"king_newsletter" : "subcribe",
										"king_email" : email 
									},
									url: "<?php echo admin_url( 'admin-ajax.php?t='.time() ); ?>",
									success: function( data ) {
										$(".king-newsletter-preload").fadeOut( 500 );
										var obj = $.parseJSON( data );
										if( obj.status === 'success' ){
											var txt = '<div id="king_newsletter_status" style="color:green;">'+obj.messages+'</div>';
										}else{
											var txt = '<div id="king_newsletter_status" style="color:red;">'+obj.messages+'</div>';
										}	
											
										$('#king_newsletter_status').after( txt ).remove();

									}
					
								});	
							}
							
						 });
					</script>
			    </div><!-- end right -->
			    
			</div>
		</div>
		
		<div class="clearfix"></div>
		
		<div class="secarea">
		    <div class="container">
			    
		        <div class="one_fourth animated eff-fadeInUp delay-100ms">
		            <?php if ( is_active_sidebar( 'footer_1' ) ) : ?>
						<div id="footer_column-1" class="widget-area">
							<?php dynamic_sidebar( 'footer_1' ); ?>
						</div>
					<?php endif; ?>
		        </div>
		
		        <div class="one_fourth animated eff-fadeInUp delay-200ms">
		            <?php if ( is_active_sidebar( 'footer_2' ) ) : ?>
						<div id="footer_column-2" class="widget-area">
							<?php dynamic_sidebar( 'footer_2' ); ?>
						</div>
					<?php endif; ?>
		       </div>
		
		        <div class="one_fourth animated eff-fadeInUp delay-300ms">
		        	<?php if ( is_active_sidebar( 'footer_3' ) ) : ?>
						<div id="footer_column-3" class="widget-area">
							<?php dynamic_sidebar( 'footer_3' ); ?>
						</div>
					<?php endif; ?>    
		        </div>
	
		        <div class="one_fourth last aliright animated eff-fadeInUp delay-400ms">
					<?php if ( is_active_sidebar( 'footer_4' ) ) : ?>
						<div id="footer_column-4" class="widget-area">
							<?php dynamic_sidebar( 'footer_4' ); ?>
						</div><!-- #secondary -->
					<?php endif; ?>
		        </div>
		        
		    </div><!--end class container-->
		</div><!--end class secarea-->
		
		<div class="clearfix"></div>
		
		<div class="copyrights">
			<div class="container">
			
				<div class="one_half">
					<?php 
						if( !empty( $king->cfg['footerText'] ) ){
							echo king::esc_js( $king->cfg['footerText'] );
						}else{
							echo '<a href="'.admin_url('admin.php?page=arkahost-panel#tab-2').'">Click Here</a> ';
							_e( 'to add your copyrights text', 'arkahost' );
						}	
					?>
				</div>
				<?php
				$target = '_self';
				if( isset( $king->cfg['target_footer_links'] ) && !empty( $king->cfg['target_footer_links'] ) && $king->cfg['target_footer_links'] != 'no' ){
					$target = '_blank';
				}
				?>
			    <div class="one_half last aliright">
				    <?php if( !empty( $king->cfg['footerTerms'] ) ){ ?>
				    <a href="<?php echo esc_url( $king->cfg['footerTerms'] ); ?>">
					    <?php _e( 'Terms of Service', 'arkahost' ); ?>
					</a>
				    |
				    <?php } ?>
				    <?php if( !empty( $king->cfg['footerPrivacy'] ) ){ ?>
				    <a href="<?php echo esc_url( $king->cfg['footerPrivacy'] ); ?>" target="<?php echo esc_attr( $target );?>">
					    <?php _e( 'Privacy Policy', 'arkahost' ); ?>
					</a>
					<?php } ?>
					<?php if( !empty( $king->cfg['footerSiteMap'] ) ){ ?>
				    |
				    <a href="<?php echo esc_url( $king->cfg['footerSiteMap'] ); ?>" target="<?php echo esc_attr( $target );?>">
					    <?php _e( 'Site Map', 'arkahost' ); ?>
					</a>
				    <?php } ?>
				</div>
			
			</div>
		</div>
		
	</div><!--end class footer-->
</footer>