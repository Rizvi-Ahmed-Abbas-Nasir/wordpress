<?php

/*
*	(c) king-theme.com
*/

class PHP_Code_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array(	
						'classname'		=> 'widget_execphp', 
						'description'	=> __('Arbitrary text, HTML, Javascript, CSS and PHP Code','arkahost')
					);
		$control_ops = array('width' => 400, 'height' => 350);
		parent::__construct('execphp', __('Code','arkahost'), $widget_ops, $control_ops);
	}
	function PHP_Code_Widget() {
		$widget_ops = array(	
						'classname'		=> 'widget_execphp', 
						'description'	=> __('Arbitrary text, HTML, Javascript, CSS and PHP Code','arkahost')
					);
		$control_ops = array('width' => 400, 'height' => 350);
		parent::__construct('execphp', __('Code','arkahost'), $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
	
		global $king;
		extract($args);
	
		$title		= apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance );
		$showTitle	= empty($instance['showTitle']) ? 'true' : $instance['showTitle'];
		$class		= empty($instance['class']) ? '' : $instance['class'];
		$text		= empty($instance['data']) ? '' : $instance['data'];
		
		//WMPL
        /**
         * retreive translations
         */
        if (function_exists ( 'icl_translate' )){
            $text 			= icl_translate('Widgets', 'Code Widget - textarea field_'.$this->get_field_id('data'), $instance['data']);
        }
        //\WMPL
		
		if( empty( $instance['filter'] ) ){
			$instance['filter'] = '';
		}
		
		if( !empty( $instance['class'] ) ){
			$before_widget = str_replace( 'class="', 'class="'.$class.' ', $before_widget );
		}
		
		$king->ext['pr']( $before_widget );

		if( $showTitle == 'true' ){
			$king->ext['pr']( $before_title.$title.$after_title );
		}
		
		ob_start();
		
			$testExe = $king->ext['ev']( '?>'.stripslashes( $text ) );
			
			if( $testExe === false ){
				echo '<div style="color:#F00;">PHP Parse Error</div>';
			}	
			$text =  do_shortcode( ob_get_contents() );

		ob_end_clean();
		?>			
			<div class="execphpwidget">
				<?php print( !empty($text) ? $text : '' ) ?>
			</div>
		<?php
		
		$king->ext['pr']( $after_widget );
		
	}

	function update( $new_instance, $old_instance ) {
	
		$instance = $old_instance;
		
		$instance['title']		= strip_tags($new_instance['title']);
		$instance['showTitle']	= strip_tags(!empty($new_instance['showTitle'])?$new_instance['showTitle']:'');
		$instance['class']		= strip_tags(!empty($new_instance['class'])?$new_instance['class']:'');
		$instance['data']		= !empty($new_instance['data'])?$new_instance['data']:'';
		//WMPL
		/**
		 * register strings for translation
		 */
		if (function_exists ( 'icl_register_string' )){
			icl_register_string('Widgets', 'Code Widget - textarea field_'.$this->get_field_id('data'), $instance['data']);
		}
		//\WMPL
		return $instance;
		
	}

	function form( $instance ) {
				
		$instance	= wp_parse_args( (array) $instance, array('title'=>'','id'=>'','showTitle'=>'true','class' =>'','data'=>''));
		$title		= strip_tags($instance['title']);
		$showTitle	= strip_tags($instance['showTitle']);
		$class		= strip_tags($instance['class']);
		$data		= $instance['data'];

	?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('title') ); ?>">
				<?php _e( 'Title:', 'arkahost' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('showTitle') ); ?>">
				<?php _e( 'Show Title:', 'arkahost' ); ?>
			</label>
			Yes <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('showTitle') ); ?>" name="<?php echo esc_attr( $this->get_field_name('showTitle') ); ?>" type="radio" value="true" <?php if( $showTitle == 'true' )echo 'checked'; ?> /> &nbsp; 
			No <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('showTitle') ); ?>" name="<?php echo esc_attr( $this->get_field_name('showTitle') ); ?>" type="radio" value="false" <?php if( $showTitle == 'false' )echo ' checked'; ?> />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('class') ); ?>">
				<?php _e( 'Custom Class:', 'arkahost' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('class') ); ?>" name="<?php echo esc_attr( $this->get_field_name('class') ); ?>" type="text" value="<?php echo esc_attr($class); ?>" />
		</p>
		<textarea class="widefat phpxcode" rows="16" cols="20" id="<?php echo esc_attr( $this->get_field_id('data') ); ?>" name="<?php echo esc_attr( $this->get_field_name('data') ); ?>"><?php echo htmlspecialchars( $data ); ?></textarea>

		
<?php
	}
}
function xCodeWidgetInit() {
	global $king;
	$king->ext['rw']("PHP_Code_Widget");
}
add_action('widgets_init', 'xCodeWidgetInit');

