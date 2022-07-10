<?php
/**
 * (c) www.king-theme.com
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $more, $king;

get_header();

?>

<div id="primary" class="site-content">
	<div id="content" class="container">
		<div class="entry-content blog_postcontent">
			<div class="margin_top1"></div>
			
			<div class="logregform">        
				<div class="title">        
					<h3><?php _e('Forgot your password', 'arkahost' ); ?></h3>        		
					<p>
						<?php _e('Back to login', 'arkahost'); ?> 
						<a href="<?php echo esc_url( home_url('/?action=login') ); ?>">
							<?php _e('Login', 'arkahost'); ?>
						</a>
					</p>            
				</div>
				
				<div class="feildcont">        
					<form id="king-form" method="post" name="loginform" action="" class="king-form" novalidate="novalidate">      
						<label><i class="fa fa-user"></i> <?php _e('Enter your Email', 'arkahost' ); ?></label>       
						<input type="text" name="email" value="" />
						
						<p class="status"></p>
						
						<button type="button" class="fbut btn-resetpwd"><?php _e('Reset password!', 'arkahost' ); ?></button>  

						<input type="hidden" name="action" value="king_user_forgot" />
						<?php wp_nonce_field( 'ajax-forgotpw-nonce', 'security_fgpw' ); ?>
					</form>        
				</div>  
			</div>

		</div>
	</div>
</div>



<?php get_footer(); ?> 