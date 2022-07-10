<?php
class king_options_blog extends king_options{	
	
	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @since king_options 1.0
	*/
	function __construct($field = array(), $value ='', $parent){
		
		parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);
		$this->field = $field;
		$this->value = $value;
		//$this->render();
		
	}
	
	
	
	/**
	 * Field Render Function.
	 *
	 * Takes the vars and outputs the HTML for the field in the settings
	 *
	 * @since king_options 1.0
	*/
	function render(){
		

	if( isset( $_REQUEST['settings-updated'] ) && isset( $_REQUEST['page'] ) ){
	
		if( $_REQUEST['settings-updated'] == 'true' && $_REQUEST['page'] == strtolower(THEME_NAME).'-panel' ){
		
			$options = get_option($this->args['opt_name']);
			update_option('show_on_front',isset($options['show_on_front'])?$options['show_on_front']:get_option('show_on_front'));
			update_option('page_on_front',isset($options['page_on_front'])?$options['page_on_front']:get_option('page_on_front'));
			update_option('page_for_posts',isset($options['page_for_posts'])?$options['page_for_posts']:get_option('page_for_posts'));
			update_option('posts_per_page',isset($options['posts_per_page'])?$options['posts_per_page']:get_option('posts_per_page'));
			update_option('posts_per_rss',isset($options['posts_per_rss'])?$options['posts_per_rss']:get_option('posts_per_rss'));
			update_option('rss_use_excerpt',isset($options['rss_use_excerpt'])?$options['rss_use_excerpt']:get_option('rss_use_excerpt'));	
		}	
	}

?>

	<?php if ( ! get_pages() ) : ?>
	<table class="form-table" id="blog-table-opt">
	<?php
		if ( 'posts' != get_option( 'show_on_front' ) ) :
			update_option( 'show_on_front', 'posts' );
		endif;
	
	else :
		if ( 'page' == get_option( 'show_on_front' ) && ! get_option( 'page_on_front' ) && ! get_option( 'page_for_posts' ) ){
			update_option( 'show_on_front', 'posts' );
		}
			
	?>
	<table class="form-table" id="blog-table-opt" style="border: none">
		<tr>
			<th scope="row"><?php _e( 'Front page displays', 'arkahost' ); ?></th>
			<td id="front-static-pages">
				<fieldset>
					<legend class="screen-reader-text"><span><?php _e( 'Front page displays', 'arkahost' ); ?></span></legend>
					<p><label>
						<input name="<?php echo esc_attr( $this->args['opt_name'] ); ?>[show_on_front]" type="radio" value="posts" class="tog" <?php checked( 'posts', get_option( 'show_on_front' ) ); ?> />
						<?php _e( 'Your latest posts', 'arkahost' ); ?>
					</label>
					</p>
					<p><label>
						<input name="<?php echo esc_attr( $this->args['opt_name'] ); ?>[show_on_front]" type="radio" value="page" class="tog" <?php checked( 'page', get_option( 'show_on_front' ) ); ?> />
						<?php printf( __( 'A <a href="%s">static page</a> (select below)', 'arkahost' ), 'edit.php?post_type=page' ); ?>
					</label>
					</p>
					<ul>
						<li><label for="page_on_front"><?php printf( __( 'Front page: %s' , 'arkahost'), wp_dropdown_pages( array( 'name' => $this->args['opt_name'].'[page_on_front]', 'echo' => 0, 'show_option_none' => __( '&mdash; Select &mdash;', 'arkahost' ), 'option_none_value' => '0', 'selected' => get_option( 'page_on_front' ) ) ) ); ?></label></li>
						<li><label for="page_for_posts"><?php printf( __( 'Posts page: %s' , 'arkahost'), wp_dropdown_pages( array( 'name' => $this->args['opt_name'].'[page_for_posts]', 'echo' => 0, 'show_option_none' => __( '&mdash; Select &mdash;' , 'arkahost'), 'option_none_value' => '0', 'selected' => get_option( 'page_for_posts' ) ) ) ); ?></label></li>
					</ul>
				<?php if ( 'page' == get_option( 'show_on_front' ) && get_option( 'page_for_posts' ) == get_option( 'page_on_front' ) ) : ?>
				<div id="front-page-warning" class="error inline"><p><?php _e( '<strong>Warning:</strong> these pages should not be the same!' , 'arkahost'); ?></p>
				</div>
				<?php endif; ?>
			</fieldset></td>
			</tr>
			<?php endif; ?>
			<tr>
			<th scope="row"><label for="blog-layout"><?php _e( 'Blog Layout' , 'arkahost'); ?></label></th>
			<td>
				<?php
					global $king;
					$std = 'default';
					if(  !empty( $king->cfg['blog_layout'] ) ){
						$std = $king->cfg['blog_layout'];
					}
				?>
				
				<select name="<?php echo esc_attr( $this->args['opt_name'] ); ?>[blog_layout]">
					<option <?php if( $std == 'default' )echo 'selected'; ?> value="default">Default</option>
					<option <?php if( $std == 'large' )echo 'selected'; ?> value="large">Large Image</option>
					<option <?php if( $std == 'medium' )echo 'selected'; ?> value="medium">2 Columns </option>
					<option <?php if( $std == 'masonry' )echo 'selected'; ?> value="masonry">Masonry</option>
					<option <?php if( $std == 'timeline' )echo 'selected'; ?> value="timeline">Time line </option>
				</select>
			</td>
			</tr>
			<tr>			
			<tr>
				<th scope="row"><label for="blog-layout"><?php _e( 'Categories for blog' , 'arkahost'); ?></label></th>
				<td>
					<?php
						global $king;
						$std = array();
						if(  !empty( $king->cfg['timeline_categories'] ) ){
							$std = $king->cfg['timeline_categories'];
						}
						
						$cates = Aka_Su_Tools::get_terms( 'category' );
						
					?>
					
					<select style="width: 450px;height: 200px;" multiple="" name="<?php echo esc_attr( $this->args['opt_name'] ); ?>[timeline_categories][]">
						<option <?php if( $std == 'default' )echo 'selected'; ?> value="default">All Categories</option>
						<?php
							
							foreach( $cates as $k => $v ){
							
								echo '<option';
								if( in_array( $k, $std ) ){
									echo ' selected';
								}
								echo ' value="'.esc_attr($k).'">'.esc_html($v).'</option>';
							}
							
						?>
					</select>
				</td>
			</tr>
			<tr>
			<th scope="row"><label for="posts_per_page"><?php _e( 'Blog pages show at most' , 'arkahost'); ?></label></th>
			<td>
			<input name="<?php echo esc_attr( $this->args['opt_name'] ); ?>[posts_per_page]" type="number" step="1" min="1" id="posts_per_page" value="<?php form_option( 'posts_per_page' ); ?>" class="small-text regular-text" /> <?php _e( 'posts' , 'arkahost'); ?>
			</td>
			</tr>
			<tr>
			<th scope="row"><label for="posts_per_rss"><?php _e( 'Syndication feeds show the most recent', 'arkahost' ); ?></label></th>
			<td><input name="<?php echo esc_attr( $this->args['opt_name'] ); ?>[posts_per_rss]" type="number" step="1" min="1" id="posts_per_rss" value="<?php form_option( 'posts_per_rss' ); ?>" class="small-text regular-text" /> <?php _e( 'items', 'arkahost' ); ?></td>
			</tr>
			<tr>
			<th scope="row"><?php _e( 'For each article in a feed, show' , 'arkahost'); ?> </th>
			<td><fieldset><legend class="screen-reader-text regular-text"><span><?php _e( 'For each article in a feed, show', 'arkahost' ); ?> </span></legend>
			<p><label><input name="<?php echo esc_attr( $this->args['opt_name'] ); ?>[rss_use_excerpt]" type="radio" value="0" <?php checked( 0, get_option( 'rss_use_excerpt' ) ); ?>	/> <?php _e( 'Full text', 'arkahost' ); ?></label><br />
			<label><input name="<?php echo esc_attr( $this->args['opt_name'] ); ?>[rss_use_excerpt]" type="radio" value="1" <?php checked( 1, get_option( 'rss_use_excerpt' ) ); ?> /> <?php _e( 'Summary', 'arkahost' ); ?></label></p>
			</fieldset></td>
		</tr>
	</table>
	<input name="<?php echo esc_attr( $this->args['opt_name'] ); ?>[opt_version]" type="hidden" value="<?php echo time(); ?>" />
	<script type="text/javascript">
	//<![CDATA[
		jQuery(document).ready(function($){
			var section = $('#front-static-pages'),
				staticPage = section.find('input:radio[value="page"]'),
				selects = section.find('select'),
				check_disabled = function(){
					selects.prop( 'disabled', ! staticPage.prop('checked') );
				};
			check_disabled();
	 		section.find('input:radio').change(check_disabled);
		});
		jQuery('#blog-table-opt').parent().prev().hide();
	//]]>
	</script>

			
	<?php	
			
	}//function
	
	
	
	/**
	 * Enqueue Function.
	 *
	 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
	 *
	 * @since king_options 1.0
	*/
	function enqueue(){

		
	}//function
	
}//class
?>