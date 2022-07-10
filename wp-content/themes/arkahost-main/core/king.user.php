<?php
/***
 * King User class
 * For login and register
 ***/
 
global $king;

class King_User{
	
	public $author = 'King Theme Team';
	public $version = '1.0';
	public $countries = array();
	
	function __construct() {
		if ( is_user_logged_in() === false ) {
		    add_action('init',  array($this, 'init' ));
		}
		
		add_action( 'show_user_profile', array( $this, 'show_addition_field_profile' ) );
		add_action( 'edit_user_profile', array( $this, 'show_addition_field_profile' ) );
		
		add_action( 'personal_options_update', array( $this, 'save_addition_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_addition_fields' ) );
	}
	
	public function init(){
		wp_register_script('king-user-script', THEME_URI.'/assets/js/king.user.js', array('jquery') ); 
    	wp_enqueue_script('king-user-script');
		
		$url_redirect = home_url().'/wp-admin/profile.php';
		
    	wp_localize_script( 'king-user-script', 'ajax_user_object', array( 
	        'ajaxurl' => admin_url( 'admin-ajax.php' ),
	        'redirecturl' => $url_redirect,
	        'loadingmessage' => __('Sending user info, please wait...', 'arkahost')
	    ));

	    add_action( 'wp_ajax_nopriv_king_user_login', array($this, 'login') );
	    add_action( 'wp_ajax_nopriv_king_user_register', array($this, 'register') );
	    add_action( 'wp_ajax_nopriv_king_user_forgot', array($this, 'forgot_password') );		
	}

	
	public function addition_fields(){
		$show_fields = apply_filters('aloom_addition_fields', array(
			'aloom_addition_fields' => array(
				'title' => THEME_NAME.' Addition fields',
				'fields' => array(
					'sex' => array(
						'label' => 'Gender',
						'description' => '',
						'type' => 'radio',
						'options' => array(
							'male' 		=> 'Male',
							'female' 	=> 'Female'
						)
					),
					'bd_day' => array(
						'label' => 'Birth Day',
						'description' => ''
					),
					
					'bd_month' => array(
						'label' => 'Birth Month',
						'description' => ''
					),
					
					'bd_year' => array(
						'label' => 'Birth Year',
						'description' => ''
					),
					
					'address' => array(
						'label' => 'Address',
						'description' => ''
					),
					
					'city' => array(
						'label' => 'City',
						'description' => ''
					),
					
					'country' => array(
						'label' => 'Country',
						'description' => '',
						'class'       => 'js_field-country',
						'type'        => 'select',
						'options'     => array( '' => __( 'Select a country&hellip;', 'arkahost' ) ) + $this->get_countries()
					)
				)				
			)			
		));
		
		return $show_fields;
	}
	
	public function show_addition_field_profile( $user ){
		$show_fields = $this->addition_fields();

		foreach ( $show_fields as $fieldset ) :
			?>
			<h3><?php echo king::esc_js( $fieldset['title'] ); ?></h3>
			<table class="form-table">
				<?php
				foreach ( $fieldset['fields'] as $key => $field ) :
					$king_key = '_king_user_'.$key;
					?>
					<tr>
						<th><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
						<td>
							<?php if ( ! empty( $field['type'] ) && 'radio' == $field['type'] ) : ?>
								<?php
									$saved_value = esc_attr( get_user_meta( $user->ID, $king_key, true ) );									
									foreach ( $field['options'] as $option_key => $option_value ) : ?>																		
										<input type="radio" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $option_key ); ?>" <?php if($option_key == $saved_value) echo ' checked'; ?> />
										<span><?php echo esc_attr( $option_value ); ?></span>
								<?php endforeach; ?>
							<?php elseif ( ! empty( $field['type'] ) && 'select' == $field['type'] ) : ?>
								<select name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" class="<?php echo ( ! empty( $field['class'] ) ? $field['class'] : '' ); ?>" style="width: 25em;">
									<?php
										$selected = esc_attr( get_user_meta( $user->ID, $king_key, true ) );
										foreach ( $field['options'] as $option_key => $option_value ) : ?>
										<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $selected, $option_key, true ); ?>><?php echo esc_attr( $option_value ); ?></option>
									<?php endforeach; ?>
								</select>
							<?php else : ?>
							<input type="text" name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( get_user_meta( $user->ID, $king_key, true ) ); ?>" class="<?php echo ( ! empty( $field['class'] ) ? $field['class'] : 'regular-text' ); ?>" />
							<?php endif; ?>
							<br/>
							<span class="description"><?php echo wp_kses_post( $field['description'] ); ?></span>
						</td>
					</tr>
					<?php
				endforeach;
				?>
			</table>
			<?php
		endforeach;		
	}
	
	/**
	 * Save addition info
	 *
	 * @param mixed $user_id User ID of the user being saved
	 */
	public function save_addition_fields( $user_id ) {
		$save_fields = $this->addition_fields();

		foreach( $save_fields as $fieldset ) {

			foreach( $fieldset['fields'] as $key => $field ) {

				if ( isset( $_POST[ $key ] ) ) {
					$king_key = '_king_user_'.$key;
					update_user_meta( $user_id, $king_key, sanitize_text_field( $_POST[ $key ] ) );
				}
			}
		}
	}
	
		
	/**
	 * Get all countries.
	 * @return array
	 */
	public function get_countries() {
		if ( empty( $this->countries ) ) {
			
			get_template_part( 'core'.DS.'countries'.DS.'i18n' );
			
			$this->countries = apply_filters( 'king_theme_countries', get_country_i18n() );
			if ( apply_filters( 'king_theme_sort_countries', true ) ) {
				asort( $this->countries );
			}
		}
		return $this->countries;
	}	
	
	/**
	 * login().
	 * @return json data
	 */	
	public function login(){
	    check_ajax_referer( 'ajax-login-nonce', 'security' );
				
	    $info = array();
	    $info['user_login'] = $_POST['log'];
	    $info['user_password'] = $_POST['pwd'];
		
		if(isset($_POST['rememberme']) && $_POST['rememberme'] == 'on'){
			$info['remember'] = 'true';
		}else{
			$info['remember'] = 'false';
		}	    

	    $user_signon = wp_signon( $info, true );
	    if ( is_wp_error($user_signon) ){

			$output = array(
				'loggedin' => false, 
				'message' => __('Wrong username or password.', 'arkahost')
			);			
	    } else {
	    	wp_set_current_user($user_signon->ID);
	    	wp_set_auth_cookie( $user_signon->ID, true, false );
			$output = array(
				'loggedin' => true, 
				'message' => __('Login successful, redirecting...', 'arkahost')
			);			     
	    }
		wp_send_json($output);

	    die();
	}

	/**
	 * register().
	 * @return json data
	 */	
	public function register(){
		check_ajax_referer( 'ajax-register-nonce', 'security_reg' );

		if ( ! get_option( 'users_can_register' ) ) {
			$output = array(
				'status' => false, 
				'message' => __('Disabled user register.', 'arkahost')
			);
			wp_send_json($output);	
			die();
		}
		
		$username 	= $_POST['user_login'];
		$email 		= $_POST['user_email'];
		$password1 	= $_POST['password'];
		$password2 	= $_POST['passwordConfirm'];

		$exception_fields 	= array('user_login', 'user_email', 'password', 'passwordConfirm');
		$save_fields 		= array('sex', 'bd_day', 'bd_month', 'bd_year', 'city', 'country', 'address');
				
		if(!$this->mail_check($email)){
			$output = array(
				'loggedin' => false, 
				'message' => __('Enter a correct email.', 'arkahost')
			);
			wp_send_json($output);	
			die();
		}

		if($password1 != $password2){
			$output = array(
				'loggedin' => false, 
				'message'=>__('Password does not match.', 'arkahost')
			);
			wp_send_json($output);				
			die();
		}

		if(strlen($password1) < 6){
			$output = array(
				'loggedin' => false, 
				'message'=>__('Password too short.', 'arkahost')
			);
			wp_send_json($output);
			die();
		}

	    $info = array();
	   	$info['user_nicename'] = $info['nickname'] = $info['display_name'] = $info['first_name'] = $info['user_login'] = sanitize_user($username) ;
	    $info['user_pass'] = sanitize_text_field($password1);
		$info['user_email'] = sanitize_email($email);
		
		// Register the user
	    $user_register = wp_insert_user( $info );
		if ( is_wp_error($user_register) ){
			$error  = $user_register->get_error_codes() ;
			if(in_array('empty_user_login', $error))
				wp_send_json(
					array(
						'loggedin' => false, 
						'message' => __('Empty user login', 'arkahost')
					)
				);
			elseif(in_array('existing_user_login',$error))
				wp_send_json(
					array(
						'loggedin' => false, 
						'message' => __('This username is already registered.', 'arkahost')
					)
				);
			elseif(in_array('existing_user_email',$error))
		        wp_send_json(
					array(
						'loggedin' => false, 
						'message' => __('This email address is already registered.', 'arkahost')
					)
				);
		} else {
			
			//Save meta field
			foreach($_POST as $key => $value){
				if(!in_array($key, $exception_fields)){
					if(in_array($key, $save_fields) && !empty($value)){
						$user_id = $user_register;
						$meta_key = '_king_user_'.$key;
						$meta_value = $value;
						add_user_meta( $user_id, $meta_key, $meta_value );
					}					
				}
			}
			
		  	$this->auth_user_login($info['user_login'], $info['user_pass'], 'Registration');      
		}

		die();
	}


	public function forgot_password(){
		check_ajax_referer( 'ajax-forgotpw-nonce', 'security_fgpw' );
		$email = $_POST['email'];

		if($this->mail_check($email)){
			if(email_exists($email)){
				$user = get_user_by('email', $email);

				$request_pw = get_user_meta($user->ID, 'user_reset_password', true);

				$key_val = wp_generate_password(50, false, false);

				if($request_pw){
					update_user_meta( $user->ID, 'user_reset_password', $key_val);	
					wp_send_json(
						array(
							'status' => true, 
							'message' => __( "Please check your email and reset your password.", 'arkahost' )
						)
					);	
				}else{
					add_user_meta( $user->ID, 'user_reset_password', $key_val);
					wp_send_json(
						array(
							'status' => true, 
							'message' => __( "Please check your email and reset your password.", 'arkahost' )
						)
					);	
				}
				
				/** Sent email **/
				$to = $email;
				$subject = 'Password Reset form King Theme ['. time() .']' ;
				
				$message = '<p>Hi '. $user->user_login. ', Someone (probably you) has requested a new password for your account on King Theme <br /></p>';
				$message .= '<p>To confirm this and have a new password sent to you via e-mail, go to the following web address: '. home_url('/?key_reset='.$key_val ).'</p>';
				$message .= '<p>In most mail problems, this should appear as a blue link which you can just click on. If that doesn\'t work, then copy and paste the address into the address line at the top of your web browser window.</p>';
				$message .= '<p>If you need help, please contact <a href="mailto:contact@king-theme.com">contact@king-theme.com</a></p>';
				
				$headers = 'From: King Theme <contact@king-theme.com>' . "\r\n";
				$attachments = null;
				
				king_mail( $to, $subject, $message, $headers, $attachments );
				/** End **/
				
				
			}else{
				wp_send_json(
					array(
						'status'=>false, 
						'message'=>__('This email not exist in my system.', 'arkahost')
					)
				);
			}
		}else{
			wp_send_json(
				array(
					'status'=>false, 
					'message'=>__('Enter a validate email.', 'arkahost')
				)
			);
		}

		die();

	}

	public function auth_user_login($user_login, $password, $login){
		$info = array();
	    $info['user_login'] = $user_login;
	    $info['user_password'] = $password;
	    $info['remember'] = true;
		
		$user_signon = wp_signon( $info, false );
	    if ( is_wp_error($user_signon) ){
			wp_send_json(
				array(
					'loggedin' => false, 
					'message' => __('Wrong username or password.', 'arkahost')
				)
			);
	    } else {
			wp_set_current_user($user_signon->ID);
	        wp_send_json(
				array(
					'loggedin'=>true, 
					'message'=>__("login successful, redirecting...", 'arkahost')
				)
			);
	    }
		
		die();
	}

	public function mail_check($email){
		if (filter_var($email, FILTER_VALIDATE_EMAIL))
			return true;
		else
			return false;   
	}

}

new King_User();


?>