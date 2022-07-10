<?php


class widget_tabs extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'description' => 'Most Popular, Recent, Comments, Tags' , 'id_base' => 'tabbed'  );
		parent::__construct( 'tabbed','Tabbed  ', $widget_ops );
	}

	function widget_tabs() {
		$widget_ops = array( 'description' => 'Most Popular, Recent, Comments, Tags' , 'id_base' => 'tabbed'  );
		parent::__construct( 'tabbed','Tabbed  ', $widget_ops );
	}
	function widget( $args, $instance ) {
		
		global $king;
		
		extract($args);
		$amount = empty( $instance['no_of_posts'] ) ? 5 : $instance['no_of_posts'];
	
	?>
		<div id="tabs">
		
			<ul class="tabs">
				<li class="active"><a href="#tab1"><?php _e( 'Popular' , 'arkahost' ) ?></a></li>
				<li><a href="#tab2"><?php _e( 'Recent' , 'arkahost' ) ?></a></li>
				<li><a href="#tab3"><?php _e( 'Tags' , 'arkahost' ) ?></a></li>
			</ul>

			<div id="tab1" class="tab_container" style="display: block;">
				<ul class="recent_posts_list">
					<?php $king->popular_posts($amount) ?>	
				</ul>
			</div>
			<div id="tab2" class="tab_container">
				<ul class="recent_posts_list">
					<?php $king->last_posts($amount)?>	
				</ul>
			</div>
			<div id="tab3" class="tab_container tagcloud">
				<ul class="tags">
				<?php 
					$tags = get_tags(array('largest' => 8,'number' => 25,'orderby'=> 'count', 'order' => 'DESC' ));
					foreach( $tags as $tag ){
				?>
										
					<li>
						<a href="<?php echo get_tag_link($tag->term_id); ?>">
							<?php echo esc_attr( $tag->name ); ?> (<?php echo esc_attr( $tag->count ); ?>)
						</a>
					</li>
					
				<?php	
					}
				?>
					</ul>
			</div>

		</div><!-- .widget /-->
<?php
	
		$king->ext['pr']( $after_widget );
	
	}
	
	function form( $instance ) {
		$defaults =  array('no_of_posts' => '5');
		$instance = wp_parse_args( (array) $instance, $defaults ); 
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'no_of_posts' ) ); ?>">Number of post on each tab (Default: 5) : </label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'no_of_posts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'no_of_posts' ) ); ?>" value="<?php echo esc_attr( $instance['no_of_posts'] ); ?>" type="text" size="3" />
		</p>
		
	<?php	
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['no_of_posts'] = strip_tags( $new_instance['no_of_posts'] );
		return $instance;
	}	
}
function tabsWidgetInit() {
	global $king;
	$king->ext['rw']("widget_tabs");
}
add_action('widgets_init', 'tabsWidgetInit');


?>
