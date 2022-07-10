<?php
/**
 * (c) king-theme.com
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( is_user_logged_in() ){
	echo '<p>'.__( 'You are logged in', 'arkahost' ).'</p>';
	return;
}

$king_user = new King_User();
$countries = $king_user->get_countries();

?>


<div class="logregform two">       
	<div class="title">	
		<h3><?php _e('REGISTRATION', 'arkahost' ); ?></h3>			
		<p>
			<?php _e('Already Registered?', 'arkahost' ); ?> 
			&nbsp;<a href="<?php echo esc_url( home_url('/?action=login') ); ?>"><?php _e('Log In', 'arkahost' ); ?>.</a></p>		
	</div>
	
	<div class="feildcont">	
		<form id="king_form" class="king-form" name="registerform" method="post" action="" novalidate="novalidate">
		
			<label><?php _e('Username', 'arkahost' ); ?> <em>*</em></label>
			<input type="text" name="user_login" />
			
			<label><?php _e('Email', 'arkahost' ); ?> <em>*</em></label>
			<input type="email" name="user_email" />
			
			<div class="one_half">
				<label><?php _e('Password', 'arkahost' ); ?> <em>*</em></label>
				<input type="password" name="password" id="password" />
			</div>
			
			<div class="one_half last">
				<label><?php _e('Confirm Password', 'arkahost' ); ?> <em>*</em></label>
				<input type="password" name="passwordConfirm" />
			</div>
			
			<label><?php _e('Name', 'arkahost' ); ?></label>
			<input type="text">
			
			<div class="one_third radiobut">
				<label><?php _e('Gender', 'arkahost' ); ?></label>
				<input class="one" type="radio" name="sex" value="male" checked>
				<span class="onelb"><?php _e('Male', 'arkahost' ); ?></span>
				<input class="two" type="radio" name="sex" value="female">
				<span class="onelb"><?php _e('Female', 'arkahost' ); ?></span>
			</div>
			
			<div class="two_third last">
				<label>Date of Birth</label>
				
				<div class="one_third">
					<select name="bd_day">
						<option value="0"><?php _e('Day', 'arkahost' ); ?></option>
						<?php 
							for($i=1;$i<=31;$i++){
								echo "<option value='$i'>$i</option>";
							}
						?>						
					</select>
				</div>
				
				<div class="one_third">
					<select name="bd_month">
						<option value="0"><?php _e('Month', 'arkahost' ); ?></option>
						<?php 
							for($m=1;$m<=12;$m++){
								$dateObj   = DateTime::createFromFormat('!m', $m);
								$monthName = $dateObj->format('F'); // March
								echo "<option value='$m'>$monthName</option>";
							}
						?>							
					</select>
				</div>
				
				<div class="one_third last">
					<select name="bd_year">
						<option value="0"><?php _e('Year', 'arkahost' ); ?></option>
						<?php 
							for($i=2001;$i>=1980;$i--){
								echo "<option value='$i'>$i</option>";
							}
						?>							
					</select>
				</div>
				
			</div>
			
			<div class="clearfix"></div>
			<div class="margin_bottom2"></div>
			
			<div class="one_half">
				<label><?php _e('City', 'arkahost' ); ?></label>
				<input type="text" name="city" value="" />
			</div>
			
			<div class="one_half last">
				<label><?php _e('Country', 'arkahost' ); ?></label>
				
				<select name="country">
					<option value="0">- Select -</option>
					<?php
						foreach($countries as $code => $country_name){
							echo "<option value='$code'>$country_name</option>";
						}
					?>
				</select>
			</div>
			
			<label><?php _e('Address', 'arkahost' ); ?></label>
			<input type="text" name="address" value="">
			
			<div class="checkbox">
				<input type="checkbox" name="argee" checked="checked">
				<label><?php _e('I agree the User Agreement and<a href="#">Terms &amp; Condition.</a>', 'arkahost' ); ?></label>
			</div>
			
			<p class="status"></p>
			
			<button type="button" class="fbut btn-register"><?php _e('Create Account', 'arkahost' ); ?></button>	
			
			<input type="hidden" name="action" value="king_user_register" />
			<?php wp_nonce_field( 'ajax-register-nonce', 'security_reg' ); ?>
		</form>	
	</div>
</div>
