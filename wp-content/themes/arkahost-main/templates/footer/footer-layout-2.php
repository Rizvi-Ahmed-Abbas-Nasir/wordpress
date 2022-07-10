<?php
/*
*	(c) king-theme.com
*/	
	
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	global $king;
?>
<!--Footer Default-->
<footer>
	<div class="footer">
		<div class="secarea sty2">
		    <div class="container">
			    
			    <div class="one_fourth alileft animated eff-fadeInUp delay-100ms">
					<?php if ( is_active_sidebar( 'footer_4' ) ) : ?>
						<div id="footer_column-4" class="widget-area">
							<?php dynamic_sidebar( 'footer_4' ); ?>
						</div><!-- #secondary -->
					<?php endif; ?>
		        </div>
		        
		        <div class="one_fourth animated eff-fadeInUp delay-200ms">
		            <?php if ( is_active_sidebar( 'footer_1' ) ) : ?>
						<div id="footer_column-1" class="widget-area">
							<?php dynamic_sidebar( 'footer_1' ); ?>
						</div>
					<?php endif; ?>
		        </div>
		
		        <div class="one_fourth animated eff-fadeInUp delay-300ms">
		            <?php if ( is_active_sidebar( 'footer_2' ) ) : ?>
						<div id="footer_column-2" class="widget-area">
							<?php dynamic_sidebar( 'footer_2' ); ?>
						</div>
					<?php endif; ?>
		       </div>
		
		        <div class="one_fourth last animated eff-fadeInUp delay-400ms">
		        	<?php if ( is_active_sidebar( 'footer_3' ) ) : ?>
						<div id="footer_column-3" class="widget-area">
							<?php dynamic_sidebar( 'footer_3' ); ?>
						</div>
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
			    <div class="one_half last aliright">
				    <?php if( !empty( $king->cfg['footerTerms'] ) ){ ?>
				    <a href="<?php echo esc_url( $king->cfg['footerTerms'] ); ?>">
					    <?php _e( 'Terms of Service', 'arkahost' ); ?>
					</a>
				    |
				    <?php } ?>
				    <?php if( !empty( $king->cfg['footerPrivacy'] ) ){ ?>
				    <a href="<?php echo esc_url( $king->cfg['footerPrivacy'] ); ?>">
					    <?php _e( 'Privacy Policy', 'arkahost' ); ?>
					</a>
					<?php } ?>
					<?php if( !empty( $king->cfg['footerSiteMap'] ) ){ ?>
				    |
				    <a href="<?php echo esc_url( $king->cfg['footerSiteMap'] ); ?>">
					    <?php _e( 'Site Map', 'arkahost' ); ?>
					</a>
				    <?php } ?>
				</div>
			
			</div>
		</div>
		
	</div><!--end class footer-->
</footer>