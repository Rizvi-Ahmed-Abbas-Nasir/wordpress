<?php

class TwitterWidget extends WP_Widget
{
    function __construct(){
		$widget_ops = array('description' => 'Displays Your Twitter Updates');
		$control_ops = array('width' => 300, 'height' => 300);
		parent::__construct('twi7er',$name='Twitter',$widget_ops,$control_ops);
    }

    function TwitterWidget(){
		$widget_ops = array('description' => 'Displays Your Twitter Updates');
		$control_ops = array('width' => 300, 'height' => 300);
		parent::__construct('twi7er',$name='Twitter',$widget_ops,$control_ops);
    }

	
	
  /*Saves the settings. */
    function update($new_instance, $old_instance){
		
		$instance = $old_instance;
		
		$instance['title'] 			= stripslashes($new_instance['title']);
		$instance['class'] 			= stripslashes($new_instance['class']);
		$instance['TwitterCount'] 	= stripslashes($new_instance['TwitterCount']);
		$instance['TwitterID'] 		= stripslashes($new_instance['TwitterID']);
		return $instance;
	}

  /*Creates the form for the widget in the back-end. */
    function form($instance){

		$instance = wp_parse_args( (array) $instance, array('title'=>'Twitter Feed!', 'class' => '', 'TwitterCount'=>'', 'TwitterID'=>'') );

		$title = htmlspecialchars($instance['title']);
		$class = htmlspecialchars($instance['class']);
		$TwitterCount = htmlspecialchars($instance['TwitterCount']);
		$TwitterID = htmlspecialchars($instance['TwitterID']);

		?>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('title') ); ?>">
				<?php echo esc_html( $title ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('TwitterID') ); ?>">Twitter Username (yourname only):</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('TwitterID') ); ?>" name="<?php echo esc_attr( $this->get_field_name('TwitterID') ); ?>" type="text" value="<?php echo esc_attr( $TwitterID ); ?>" />
		</p>
			
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('TwitterCount') ); ?>">Update Count (ex: 3):</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('TwitterCount') ); ?>" name="<?php echo esc_attr( $this->get_field_name('TwitterCount') ); ?>" type="text" value="<?php echo esc_attr( $TwitterCount ); ?>" />
		</p>	
				
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('class') ); ?>">Custom Class:</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('class') ); ?>" name="<?php echo esc_attr( $this->get_field_name('class') ); ?>" type="text" value="<?php echo esc_attr( $class ); ?>" />
		</p>
		
	<?php
		
	}

    function widget($args, $instance){
    
		global $TwitterID, $TwitterCount, $king;
		extract($args);
	
		$title 	= apply_filters('widget_title', empty($instance['title']) ? 'Twitter Updates' : $instance['title']);
		$id 	= empty($instance['TwitterID']) ? '' : $instance['TwitterID'];
		$class 	= empty($instance['class']) ? '' : $instance['class'];
		$amount = empty($instance['TwitterCount']) ? '' : $instance['TwitterCount'];
		
		if( !empty( $instance['class'] ) ){
			$before_widget = str_replace( 'class="', 'class="'.$class.' ', $before_widget );
		}
		
		$king->ext['pr']( $before_widget.$before_title.$title.$after_title );
		echo '<div class="king-preload" data-option="twitter|'.str_replace( array("'",'"','|'), array('','',''), $id ).'|'.$amount.'"><i class="fa fa-spinner fa-spin"></i>  Initializing...</div>';
		$king->ext['pr']( $after_widget );
	}
	
	
	public static function buildBaseString($baseURI, $method, $params) {
	
		$r = array();
		ksort($params);
		foreach($params as $key=>$value){
			$r[] = "$key=" . rawurlencode($value);
		}
		return $method."&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
	}
	
	public static function buildAuthorizationHeader($oauth) {
	
		$r = 'Authorization: OAuth ';
		$values = array();
		foreach($oauth as $key=>$value)
			$values[] = "$key=\"" . rawurlencode($value) . "\"";
		$r .= implode(', ', $values);
		return $r;
	}
	
	public static function returnTweet( $TwitterID, $TwitterCount, $_return = false ){

		global $king;
		
		$oauth_access_token         = "2438810168-QSjSfwcOYFqi2oEUfB4338asyBun28wasGFa8jS";
		$oauth_access_token_secret  = "qa6KIF3JpCPX7CXEDgE76MGvVDw3jAAbNvBRYhvw89Rp8";
		$consumer_key               = "F4RJ5DaO0BxGdlnYyctUEhfIT";
		$consumer_secret            = "FAyGunQfoifYbmz1nXs10pK2Trt0xw2IUWqBNmOehVK9UoutI0";
	
		$twitter_timeline           = "user_timeline"; 

		$request = array(
			'screen_name' => $TwitterID,
			'count'       => $TwitterCount
		);
	
		$oauth = array(
			'oauth_consumer_key'      => $consumer_key,
			'oauth_nonce'             => time(),
			'oauth_signature_method'  => 'HMAC-SHA1',
			'oauth_token'             => $oauth_access_token,
			'oauth_timestamp'         => time(),
			'oauth_version'           => '1.0'
		);
	

		$oauth = array_merge($oauth, $request);

		$base_info		= self::buildBaseString("https://api.twitter.com/1.1/statuses/$twitter_timeline.json", 'GET', $oauth);
		$composite_key	= rawurlencode($consumer_secret) . '&' . rawurlencode($oauth_access_token_secret);
		$oauth_signature= $king->ext['be'](hash_hmac('sha1', $base_info, $composite_key, true));
		$oauth['oauth_signature']   = $oauth_signature;

		$header = array( self::buildAuthorizationHeader($oauth), 'Expect:');
		$options = array( CURLOPT_HTTPHEADER => $header,
						  CURLOPT_HEADER => false,
						  CURLOPT_URL => "https://api.twitter.com/1.1/statuses/$twitter_timeline.json?". http_build_query($request),
						  CURLOPT_RETURNTRANSFER => true,
						  CURLOPT_SSL_VERIFYPEER => false);

		$feed = $king->ext['ci']();
		curl_setopt_array($feed, $options);
		$tweet = $king->ext['ce']( $feed );
		curl_close($feed);
	
		
		if( empty( $tweet ) ){
			echo 'Error: Can not load tweets at this moment.';
			return;
		}else{	
			$tweet = json_decode( $tweet );
		}	

		if( !empty( $tweet->errors ) ){
			echo 'Error: '.$tweet->errors[0]->message;
			return;
		}
		
		if( $_return == true ){
			return $tweet;
		}
		
		$current_time = new DateTime();

		echo '<ul class="twitter_feeds_three">';
				
		for( $i=0 ; $i <= $TwitterCount - 1 ; $i++){
		
			$time = $tweet[$i]->created_at;
			$time = date_parse($time);
			$uTime = mktime($time['hour'], $time['minute'], $time['second'], $time['month'], $time['day'], $time['year']);
			
			$timeDisplay = self::twitter_time_diff( $uTime, time() );
			
			?>
			
				<li<?php if( $i + 1 < $TwitterCount )echo ' class="bhline"'; ?>>
				
					<i class="fa fa-twitter fa-lg"></i>
					
					<a href="https://twitter.com/<?php echo esc_attr( $tweet[$i]->user->screen_name ); ?>" target="_blank">
						<?php echo esc_html( $tweet[$i]->user->name ); ?>:
					</a>
					 <?php echo wp_trim_words( esc_html( $tweet[$i]->text ), 9); ?>
				
					<em>
						<a href="https://twitter.com/<?php echo esc_attr( $tweet[$i]->user->screen_name ); ?>/status/<?php echo esc_attr( $tweet[$i]->id_str ); ?>" target="_blank" class="small"><?php echo esc_html( $timeDisplay ); ?> ago </a>
						.
						<a href="https://twitter.com/intent/tweet?in_reply_to=<?php echo esc_attr( $tweet[$i]->id_str ); ?>" target="_blank" class="small">reply </a>
						.
						<a href="https://twitter.com/intent/retweet?tweet_id=<?php echo esc_attr( $tweet[$i]->id_str ); ?>" target="_blank" class="small">retweet </a>
						.
						<a href="https://twitter.com/intent/favorite?tweet_id=<?php echo esc_attr( $tweet[$i]->id_str ); ?>" target="_blank" class="small">favorite</a>
					</em>
				</li>
		<?php 
		
			}
			
		echo '</ul>';	
		
		
	}

	
	public static function twitter_time_diff( $from, $to = '' ) {
	
		$diff = human_time_diff($from,$to);
		$replace = array(
				' hour' => 'h',
				' hours' => 'h',
				' day' => 'd',
				' days' => 'd',
				' minute' => 'm',
				' minutes' => 'm',
				' second' => 's',
				' seconds' => 's',
		);
		return $diff;
	}	
	
}

function TwitterWidgetInit() {
	global $king;
	$king->ext['rw']("TwitterWidget");
}

add_action('widgets_init', 'TwitterWidgetInit');