<?php

add_action( 'widgets_init', 'flickr_photos_widget' );
function flickr_photos_widget() {
	global $king;
	$king->ext['rw']( 'flickr_photos' );
}
class flickr_photos extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'flickr-widget' );
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'flickr' );
		parent::__construct( 'flickr','Flickr Photos', $widget_ops, $control_ops );
	}

	function flickr_photos() {
		$widget_ops = array( 'classname' => 'flickr-widget' );
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'flickr' );
		parent::__construct( 'flickr','Flickr Photos', $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		
		global $king;
		
		extract( $args );

		$flickr_title = apply_filters('widget_flickr_title', $instance['flickr_title'] );

		$id 	= empty( $instance['flickr_id'] ) ? '' : $instance['flickr_id'];
		$class 	= empty( $instance['class'] ) ? '' : $instance['class'];
		$amount = empty( $instance['no_of_photos'] ) ? '' : $instance['no_of_photos'];

		if( !empty( $instance['class'] ) ){
			$before_widget = str_replace( 'class="', 'class="'.$class.' ', $before_widget );
		}

		$king->ext['pr']( $before_widget );
		if ( $flickr_title )$king->ext['pr']( $before_title.$flickr_title.$after_title );
		echo '<div id="flickr_badge_wrapper" class="king-preload flickr_badge_wrapper" data-option="flickr|'.str_replace( array("'",'"','|'), array('','',''), $id ).'|'.$amount.'"><i class="fa fa-spinner fa-spin"></i>  Initializing...</div>';
		$king->ext['pr']( $after_widget );
		
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['flickr_title'] = strip_tags( $new_instance['flickr_title'] );
		$instance['no_of_photos'] = strip_tags( $new_instance['no_of_photos'] );
		$instance['flickr_id'] = strip_tags( $new_instance['flickr_id'] );
		$instance['flickr_display'] = strip_tags( $new_instance['flickr_display'] );
		$instance['class'] = strip_tags( $new_instance['class'] );
		return $instance;
	}

	function form( $instance ) {
		$defaults = array( 'flickr_title' =>__( 'Flickr' , 'arkahost'), 'no_of_photos' => '6' , 'flickr_display' => 'latest', 'flickr_id'=>'', 'class' => '' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<!--label class="notice" style="color:red">Notice: Not work on localhost</label-->
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'flickr_title' ) ); ?>">Title : </label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'flickr_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'flickr_title' ) ); ?>" value="<?php echo esc_attr( $instance['flickr_title'] ); ?>" class="widefat" type="text" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'flickr_id' ) ); ?>">Flickr ID : </label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'flickr_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'flickr_id' ) ); ?>" value="<?php echo esc_attr( $instance['flickr_id'] ); ?>" class="widefat" type="text" />
			Find Your ID at ( <a target="_blank" href="<?php echo esc_url('idgettr.com'); ?>">idGettr</a> )
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'no_of_photos' ) ); ?>">Number of photos to show : </label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'no_of_photos' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'no_of_photos' ) ); ?>" value="<?php echo esc_attr( $instance['no_of_photos'] ); ?>" type="text" size="3" />
		</p>		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'class' ) ); ?>">Custom Class: </label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'class' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'class' ) ); ?>" value="<?php echo esc_attr( $instance['class'] ); ?>" class="widefat" type="text" />
		</p>

	<?php
	}
}
?>