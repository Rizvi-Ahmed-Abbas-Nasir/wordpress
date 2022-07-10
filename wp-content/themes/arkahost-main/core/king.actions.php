<?php
/*
*	This is private registration with WP
* 	(c) king-theme.com
*	
*/


global $king;

add_action( "wp_head", 'king_meta', 0 ); 
add_action( "get_header", 'king_set_header' ); 
add_action( "wp_head", 'king_custom_header', 99999 );
add_action( "wp_footer", 'king_custom_footer' );
add_action("admin_footer", 'remove_link_fontawesome_vc');



function remove_link_fontawesome_vc(){
	wp_deregister_style('font-awesome');
	wp_dequeue_style('font-awesome');
}



function king_set_header( $name ){
	
	global $king;
	
	if( !empty( $name ) ){
		$file = ( strpos( $name, '.php' ) === false ) ? $name.'.php' : $name;
		if( file_exists( THEME_PATH.DS.'templates/header/'.$file ) ){	
			$king->cfg[ 'header' ] = $file;
			$king->cfg[ 'header_autoLoaded' ] = 1;
		}	
	}
	
}

/*-----------------------------------------------------------------------------------*/
# Setup custom header from theme panel
/*-----------------------------------------------------------------------------------*/

function king_custom_header(){		
	global $king;
	echo '<script type="text/javascript">var site_uri = "'.SITE_URI.'";var SITE_URI = "'.SITE_URI.'";var theme_uri = "'.THEME_URI.'";</script>';	
	
	$options_css = get_option( 'king_'.strtolower( THEME_NAME ).'_options_css', true ); 
	if( !empty( $options_css ) ){
		echo '<style type="text/css">';
		echo str_replace( array( '%SITE_URI%', '<style', '</style>', '%HOME_URL%' ), array( SITE_URI, '&lt;', '', SITE_URI ), $options_css );
		echo '</style>';
	}

}

/*-----------------------------------------------------------------------------------*/
# setup footer from theme panel
/*-----------------------------------------------------------------------------------*/


function king_custom_footer( ){
	
	global $king;	
	
	echo '<a href="#" class="scrollup" id="scrollup" style="display: none;">Scroll</a>'."\n";
	
	if( !empty( $king->cfg['GAID'] ) ){
		/*
		*	
		* Add google analytics in footer
		*	
		*/
		echo "<script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');ga('create', '".esc_attr($king->cfg['GAID'])."', 'auto');ga('send', 'pageview');</script>";
		
	}

	$king_sticky = true;
	if(isset($king->cfg[ 'stickymenu' ]) && $king->cfg[ 'stickymenu' ] ==1){
		$king_sticky = false;
	}
		
	echo '<script type="text/javascript">
	jQuery(document).ready(function($) {
		var king_sticky	= '.(($king_sticky)?'true':'false').';
		$(window).scroll(function () {

			if ($(window).scrollTop() > 50 ) {
				$("#scrollup").show();
				if(king_sticky)
					document.mainMenu.addClass("compact");
			} else {
				$("#scrollup").hide();
				if(king_sticky)
					document.mainMenu.removeClass("compact");
			}
		});
	});
	</script>';
	if(is_array($king->carousel) && count($king->carousel) >0){
		echo '<script type="text/javascript">
		jQuery(document).ready(function($) {
		';
		foreach($king->carousel as $car_js){
			echo "\n".$car_js."\n";
		}
		echo '
		});
		</script>';
	}
}


/* Add box select layouts into page|post editting */
add_action( 'save_post', 'king_save_post_process', 10, 2 );
function king_save_post_process( $post_id, $post ) {

	if( $post->post_type != 'page' && !empty( $_POST['king'] ) ){

		if( !empty( $_POST['king']['_type'] ) ){
			if( !add_post_meta( $post->ID , 'king_'.$_POST['king']['_type'] , $_POST['king'], true ) ){
				update_post_meta( $post->ID , 'king_'.$_POST['king']['_type'] , $_POST['king'] );
			}
		}
		
	}	
	
	if( $post->post_type == 'page' && !empty( $_POST['king'] ) ){
		
		if( !empty( $_POST['king'] ) ){
			foreach( $_POST['king'] as $key => $value ){
				if( !empty( $value ) ){
					if( !add_post_meta( $post->ID, '_king_page_'.$key, $value, true ) ){
						update_post_meta( $post->ID, '_king_page_'.$key, $value );
					}
				}else{
					delete_post_meta( $post->ID, '_king_page_'.$key );
				}
			}
		}
		
	}

}

function king_post_save_regexp($m){
		
	return str_replace('"',"'",$m[0]);
	
}

add_action("after_switch_theme", "king_activeTheme", 1000 ,  1);
/*----------------------------------------------------------*/
#	Active theme -> import some data
/*----------------------------------------------------------*/
function king_activeTheme( $oldname, $oldtheme=false ) {
 	global $king;
	#Check to import base of settings
	$opname = strtolower( THEME_NAME) .'_import';
	$king_opimp  = get_option( $opname, true );

	if($king_opimp == 1){
		
		get_template_part( 'core/import' );
	}

	
	# Make sure all images & icons are readable
	king_check_filesReadable( ABSPATH.'wp-content'.DS.'themes'.DS.$king->stylesheet );
	
	if( $king->template == $king->stylesheet ){
		
		?>
		<style type="text/css">
			body{display:none;}
		</style>
		<script type="text/javascript">
			/*Redirect to install required plugins after active theme*/
			window.location = '<?php echo esc_url( 'admin.php?page='.strtolower( THEME_NAME ).'-importer' ); ?>';
		</script>
		
		<?php	
	
	}
}

/*-----------------------------------------------------------------------------------*/
# 	Check un-readable files, and change chmod to readable
/*-----------------------------------------------------------------------------------*/

function king_check_filesReadable( $dir = '' ){

	if( $dir != '' && is_dir( $dir ) ){
		
		if ( $handle = opendir( $dir ) ){
			
			@chmod( $dir, 0755 );
			
			while ( false !== ( $entry = readdir($handle) ) ) {
				if( $entry != '.' && $entry != '..' && strpos($entry, '.php') === false && is_file( $dir.DS.$entry ) ){
					
					$perm = substr(sprintf('%o', fileperms( $dir.DS.$entry )), -1 );

					if( $perm == '0' ){
						@chmod( $dir.DS.$entry, 0644 );
					}	
				}
				if( $entry != '.' && $entry != '..' && is_dir( $dir.DS.$entry ) ){
					king_check_filesReadable( $dir.DS.$entry );
				}
			}
		}
		
	}
}

/*-----------------------------------------------------------------------------------*/
# 	Register Menus in NAV-ADMIN
/*-----------------------------------------------------------------------------------*/


add_action('admin_menu', 'king_settings_menu');

function king_settings_menu() {

	add_theme_page( THEME_NAME.' Panel', THEME_NAME.' - Options', 'edit_theme_options', THEME_SLUG.'-panel', 'king_theme_panel');
	add_theme_page( THEME_NAME.' Import', THEME_NAME.' - Demos', 'edit_theme_options', THEME_SLUG.'-importer', 'king_theme_import');
}

function king_theme_panel(){
	
	global $king, $king_options;

	$king->assets(array(
		array('js' => THEME_URI.'/core/assets/jscolor/jscolor')
	));
	
	$king_options->_options_page_html();
	
}

function king_theme_import() {
	
	global $king;

	$king->assets(array(
		array('css' => THEME_URI.'/core/assets/css/bootstrap.min'),
		array('css' => THEME_URI.'/options/css/theme-pages')
	));
	king_incl_core( 'core'.DS.'sample.php' );

}


add_action('add'.'_'.'meta'.'_'.'boxes','king_page_layout_template_metabox');
/*----------------------------------------------------------*/
#	Add select layout on page edit
/*----------------------------------------------------------*/
function king_page_layout_template_metabox() {
	global $king;
	$king->ext['amb']('KingFeildsPage', THEME_NAME.' Theme - Page Settings', 'king_page_fields_meta_box', 'page', 'normal', 'core');
    $king->ext['amb']('KingFeildsTesti', __('Testimonial Options','arkahost'), 'king_testi_fields_meta_box', 'testimonials', 'normal', 'high');
    $king->ext['amb']('KingFeildsTeam', __('Staff Profiles','arkahost'), 'king_staff_fields_meta_box', 'our-team', 'normal', 'high');
    $king->ext['amb']('KingFeildsWork', __('Project\'s Link','arkahost'), 'king_work_fields_meta_box', 'our-works', 'normal', 'high');
    $king->ext['amb']('KingFeildsPricing', __('Pricing Tables Fields','arkahost'), 'king_pricing_fields_meta_box', 'pricing-tables', 'normal', 'high');
    $king->ext['amb']('KingFeildsMegaMenu', __('Extra Setting','arkahost'), 'megamenu_meta_box', 'mega_menu', 'normal', 'high');
}

function king_page_fields_meta_box( $post ){
	
	global $king, $king_options;

	locate_template( 'options'.DS.'options.php', true );
		
	$listHeaders = array();
	if ( $handle = opendir( THEME_PATH.DS.'templates'.DS.'header' ) ){
		
		$listHeaders[ 'default' ] = array('title' => '.Use Global Setting', 'img' => THEME_URI.'/core/assets/images/load-default.jpg' );
		
		while ( false !== ( $entry = readdir($handle) ) ) {
			if( $entry != '.' && $entry != '..' && strpos($entry, '.php') !== false  ){
				$title  = ucwords( str_replace( '-', ' ', basename( $entry, '.php' ) ) );
				$listHeaders[ $entry ] = array('title' => $title, 'img' => THEME_URI.'/templates/header/thumbnails/'.basename( $entry, '.php' ).'.jpg' );
			}
		}
	}
	
	$listFooters = array();
	if ( $handle = opendir( THEME_PATH.DS.'templates'.DS.'footer' ) ){
		$listFooters[ 'default' ] = array('title' => '.Use Global Setting', 'img' => THEME_URI.'/core/assets/images/load-default.jpg' );
		while ( false !== ( $entry = readdir($handle) ) ) {
			if( $entry != '.' && $entry != '..' && strpos($entry, '.php') !== false  ){
				$title  = ucwords( str_replace( '-', ' ', basename( $entry, '.php' ) ) );
				$listFooters[ $entry ] = array('title' => $title, 'img' => THEME_URI.'/templates/footer/thumbnails/'.basename( $entry, '.php' ).'.jpg' );
			}
		}
	}

	$sidebars = array( '' => '--Select Sidebar--' );
	
	if( !empty( $king->cfg['sidebars'] ) ){
		foreach( $king->cfg['sidebars'] as $sb ){
			$sidebars[ sanitize_title_with_dashes( $sb ) ] = esc_html( $sb );
		}
	}
	
	$fields = array(
		array(
			'id' => 'logo',
			'type' => 'upload',
			'title' => __('Upload Logo', 'arkahost'), 
			'sub_desc' => __('This will be display as logo at header of only this page', 'arkahost'),
			'desc' => __('Upload new or from media library to use as your logo. We recommend that you use images without borders and throughout.', 'arkahost'),
			'std' => ''
		),		
		array(
			'id' => 'modal',
			'type' => 'upload',
			'title' => __('Upload Image Modal', 'arkahost'), 
			'sub_desc' => __('Image to show on Modal Window', 'arkahost'),
			'std' => ''
		),
		array(
			'id' => 'modal_action',
			'type' => 'textarea',
			'title' => __('Modal Actions', 'arkahost'),
			'std'	=> '',
			'sub_desc' => 'If you want to more action when Modal Window displays',
			'desc' => wp_kses( __( 'Your HTML code (allows shortcode) will be display into Modal Window, Use shortcode <strong><i>[image]</i></strong> into your code to display the photos you upload above.', 'arkahost' ), array('i'=>array(),'strong'=>array())).'<br />'.esc_html('Example: <a href="#"> [image] </a>')
		),
		array(
			'id' => 'page_title',
			'type' => 'textarea',
			'title' => __('Page Title', 'arkahost'),
			'std'	=> '',
			'sub_desc' => __( 'Page Title will display on Breadcrumn instead default title.', 'arkahost' ),
			'desc' => ''
		),	
		array(
			'id' => 'breadcrumb',
			'type' => 'select',
			'title' => __('Display Breadcrumb', 'arkahost'), 
			'options' => array( 
				'global' => 'Use Global Settings',
				'no' => 'No, Thanks!',
				'page_title1' => 'Style 1', 
				'page_title1 sty2' => 'Style 2', 
				'page_title1 sty3' => 'Style 3', 
				'page_title1 sty4' => 'Style 4', 
				'page_title1 sty5' => 'Style 5', 
				'page_title1 sty6' => 'Style 6', 
				'page_title1 sty7' => 'Style 7', 
				'page_title1 sty8' => 'Style 8', 
				'page_title1 sty9' => 'Style 9', 
				'page_title1 sty10' => 'Style 10', 
				'page_title1 sty11' => 'Style 11', 
				'page_title1 sty12' => 'Style 12',
				'page_title1 sty13' => 'Style 13',
			),
			'std' => '',
			'sub_desc' => __( 'Set for show or dont show breadcrumb for this page.', 'arkahost' )
		),
		
		array(
			'id' => 'breadcrumb_bg',
			'type' => 'upload',
			'title' => __('Upload Breadcrumb Background Image', 'arkahost'), 
			'std' => '',
			'sub_desc' => __( 'Upload your Breadcrumb background image for this page.', 'arkahost' )
		),		
		array(
			'id' => 'breadcrumb_tag',
			'type' => 'select',
			'title' => __('Breadcrumb Title Tag', 'arkahost'),
			'desc' => __('The html tag for title content. Default is H1', 'arkahost'),
			'options' => array(
				'h1' => 'H1',
				'h2' => 'H2',
				'h3' => 'H3',
				'h4' => 'H4',
				'h5' => 'H5',
				'h6' => 'H6',
				'p' => 'P',
				'span' => 'SPAN',				
			),
			'std' => 'h1'
		),
		array(
			'id' => 'sidebar',
			'type' => 'select',
			'title' => __('Select Sidebar', 'arkahost'), 
			'options' => $sidebars,
			'std' => '',
			'sub_desc' => __( 'Select template from Page Attributes at right side', 'arkahost' ),
			'desc' => '<br /><br />'.__( 'Select a dynamic sidebar what you created in theme-panel to display under page layout.', 'arkahost' )
		),
		array(
			'id' => 'description',
			'type' => 'textarea',
			'title' => __('Description', 'arkahost'),
			'std'	=> '',
			'sub_desc' => __( 'The description will show in content of meta tag for SEO + Sharing purpose', 'arkahost' ),
		),
		array(
			'id' => 'header',
			'type' => 'radio_img',
			'title' => __('Select Header', 'arkahost'),
			'sub_desc' => __('Overlap: The header will cover up anything beneath it.', 'arkahost'),
			'options' => $listHeaders,
			'std' => ''
		),
		array(
			'id' => 'footer',
			'type' => 'radio_img',
			'title' => __('Select Footer', 'arkahost'),
			'sub_desc' => __('Select footer to display for only this page. This path has located /templates/footer/{-file-}', 'arkahost'),
			'options' => $listFooters,
			'std' => ''
		)
	);
	
	echo '<textarea name="king[vc_cache]" id="king_vc_cache" style="display:none">'.esc_html( get_post_meta( $post->ID, '_king_page_vc_cache', true) ).'</textarea>';
	
	echo '<div class="nhp-opts-group-tab single-page-settings" style="display:block;padding:0px;">';
	echo '<table class="form-table" style="display:inline-block;border:none;"><tbody>';
	foreach( $fields as $key => $field ){
		
		$field['std'] = get_post_meta( $post->ID,'_king_page_'.$field['id'] , true );
		
		if( empty( $field['std'] ) ){
			if( $field['id'] == 'header' ){
				$field['std'] = 'default';
			}
			if( $field['id'] == 'footer' ){
				$field['std'] = 'default';
			}
			if(  $field['id'] == 'breadcrumb' ){
				$field['std'] = 'global';
			}
		}
		
		locate_template( 'options'.DS.'fields'.DS.$field['type'].'/field_'.$field['type'].'.php', true );
		
		$field_class = 'king_options_'.$field['type'];
		
		if( class_exists( $field_class ) ){
			
			$render = '';
			$obj = new stdClass();
			$obj->extra_tabs = '';
			$obj->sections = '';
			$obj->args = '';
			$render = new $field_class($field, $field['std'], $obj );
			
			echo '<tr><th scope="row">'.esc_html($field['title']).'<span class="description">';
			echo (isset($field['sub_desc']))?  esc_html($field['sub_desc']) : '';
			echo '</span></th>';
			echo '<td>';
			
			$render->render();
			
			if( method_exists( $render, 'enqueue' ) ){
				$render->enqueue();
			}	
			
			echo '</td></tr>';
		}
	}
	echo '</tbody></table></div>';
	
}

function king_testi_fields_meta_box( $post ) {

	$testi = get_post_meta( $post->ID , 'king_testi' );
	if( !empty( $testi ) ){
		$testi  = $testi[0];
	}else{
		$testi = array();
	}	
	
?>

	<table>
		<tr>
			<td>
				<label><?php _e('Website','arkahost'); ?>: </label>
			</td>
			<td>	
				<input type="text" name="king[website]" value="<?php echo esc_attr( isset($testi['website'])?$testi['website']:'' );	?>" />
			</td>
		</tr>
		<tr>
			<td>
				<br />
				<label><?php _e('Rate','arkahost'); ?>: </label>
			</td>
			<td>
				<br />
				<i class="fa fa-star"></i> 
				<input type="radio" name="king[rate]" <?php if(isset($testi['rate'])){if($testi['rate']==1)echo 'checked';} ?> value="1" />
				&nbsp; 
				<i class="fa fa-star"></i>
				<i class="fa fa-star"></i> 
				<input type="radio" <?php if(isset($testi['rate'])){if($testi['rate']==2)echo 'checked';} ?> name="king[rate]" value="2" />
				&nbsp; 
				<i class="fa fa-star"></i>
				<i class="fa fa-star"></i>
				<i class="fa fa-star"></i> 
				<input type="radio" name="king[rate]" <?php if(isset($testi['rate'])){if($testi['rate']==3)echo 'checked';} ?> value="3" />
				&nbsp; 
				<i class="fa fa-star"></i>
				<i class="fa fa-star"></i>
				<i class="fa fa-star"></i>
				<i class="fa fa-star"></i>
				<input type="radio" name="king[rate]" <?php if(isset($testi['rate'])){if($testi['rate']==4)echo 'checked';} ?> value="4" />
				&nbsp; 
				<i class="fa fa-star"></i>
				<i class="fa fa-star"></i>
				<i class="fa fa-star"></i>
				<i class="fa fa-star"></i>
				<i class="fa fa-star"></i> 
				<input type="radio" name="king[rate]" <?php if(isset($testi['rate'])){if($testi['rate']==5)echo 'checked';} ?> value="5" />
			</td>
		</tr>
	</table>
	
	
	<input type="hidden" name="king[_type]" value="testi" />
	
<?php
}

function king_staff_fields_meta_box( $post ) {

	$staff = get_post_meta( $post->ID , 'king_staff' );
	if( !empty( $staff ) ){
		$staff  = $staff[0];
	}else{
		$staff = array();
	}	
	
?>

	<table>
		<tr>
			<td>
				<label><?php _e('Position','arkahost'); ?>: </label>
			</td>
			<td>	
				<input type="text" name="king[position]" value="<?php echo esc_attr( isset($staff['position'])?$staff['position']:'' );	?>" />
			</td>
		</tr>
		<tr>
			<td>
				<label><?php _e('Facebook','arkahost'); ?>: </label>
			</td>
			<td>
				<input type="text" name="king[facebook]" value="<?php echo esc_attr( isset($staff['facebook'])?$staff['facebook']:'' );	?>" />
			</td>
		</tr>
		<tr>
			<td>
				<label><?php _e('Twitter','arkahost'); ?>: </label>
			</td>
			<td>
				<input type="text" name="king[twitter]" value="<?php echo esc_attr( isset($staff['twitter'])?$staff['twitter']:'' );	?>" />
			</td>
		</tr>
		<tr>
			<td>
				<label><?php _e('Google+','arkahost'); ?>: </label>
			</td>
			<td>
				<input type="text" name="king[gplus]" value="<?php echo esc_attr( isset($staff['gplus'])?$staff['gplus']:'' );	?>" />
			</td>
		</tr>
		<tr>
			<td>
				<label><?php _e('LinkedIn','arkahost'); ?>: </label>
			</td>
			<td>
				<input type="text" name="king[linkedin]" value="<?php echo esc_attr( isset($staff['linkedin'])?$staff['linkedin']:'' );	?>" />
			</td>
		</tr>
	</table>
	
	<input type="hidden" name="king[_type]" value="staff" />

<?php
}


function king_work_fields_meta_box( $post ) {

	$work = get_post_meta( $post->ID , 'king_work', true );
	if( empty( $work ) ){


		$work = array();
	}	
	
?>

	<input type="text" name="king[link]" value="<?php echo esc_attr( isset($work['link'])?$work['link']:'' ); ?>" style="width: 100%;" />
	
	<input type="hidden" name="king[_type]" value="work" />
	
<?php
}



function king_pricing_fields_meta_box( $post ) {

	$pricing = get_post_meta( $post->ID , 'king_pricing' );
	if( !empty( $pricing ) ){
		$pricing  = $pricing[0];
	}else{
		$pricing = array();
	}	
	
?>

	<table>
		<tr>
			<td>
				<label><?php _e('Price','arkahost'); ?>: </label>
			</td>
			<td>	
				<input type="text" name="king[price]" value="<?php echo esc_attr( isset( $pricing['price'] ) ? $pricing['price'] : '' );	?>" /> / 
				<input type="text" name="king[per]" value="<?php echo esc_attr( isset( $pricing['per'] ) ? $pricing['per'] : '' );	?>" />
			</td>
		</tr>
		<tr>
			<td>
				<label><?php _e('Regularly Price','arkahost'); ?>: </label>
			</td>
			<td>
				<input type="text" name="king[regularly_price]" value="<?php 
					echo esc_attr( isset( $pricing['regularly_price'] ) ? $pricing['regularly_price'] :'' );
				?>" /> 
			</td>
		</tr>
		<tr>
			<td>
				<label><?php _e('Currency','arkahost'); ?>: </label>
			</td>
			<td>
				<input type="text" name="king[currency]" value="<?php 
					echo esc_attr( isset( $pricing['currency'] ) ? $pricing['currency'] : '' );
				?>" /> 
			</td>
		</tr>
		<tr>
			<td>
				<label><?php _e('Best Seller','arkahost'); ?>: </label>
			</td>
			<td>
				<input type="radio" name="king[best_seller]" value="yes" <?php 
					if( isset( $pricing['best_seller'] ) ){
						if( $pricing['best_seller'] == 'yes' ){
							echo 'checked';
						}
					}	
				?> /> Yes  
				<input type="radio" name="king[best_seller]" value="no" <?php 
					if( isset($pricing['best_seller']) ){
						if( $pricing['best_seller'] == 'no' ){
							echo 'checked';
						}
					}	
				?> /> No
			</td>
		</tr>
		<tr>
			<td>
				<label><?php _e('Attributes','arkahost'); ?>: </label>
			</td>
			<td>
				<textarea rows="8" cols="80" name="king[attr]"><?php 
					echo esc_html( isset($pricing['attr'])?$pricing['attr']:'' );	
				?></textarea>
			</td>
		</tr>
		<tr>
			<td>
				<label><?php _e('Text button submit','arkahost'); ?>: </label>
			</td>
			<td>
				<input type="text" name="king[textsubmit]" value="<?php echo esc_attr( isset($pricing['textsubmit'])?$pricing['textsubmit']:'' );	?>" />
			</td>
		</tr>
		<tr>
			<td>
				<label><?php _e('Link submit','arkahost'); ?>: </label>
			</td>
			<td>
				<input type="text" name="king[linksubmit]" value="<?php echo esc_attr( isset($pricing['linksubmit'])?$pricing['linksubmit']:'' );	?>" />
			</td>
		</tr>
	</table>
	
	<input type="hidden" name="king[_type]" value="pricing" />

<?php
}


/*Add post type*/
add_action( 'init', 'king_init' );
function king_init() {

	global $king;

    if( is_admin() ){
   		$king->sysInOut();
   	}
   		
}

/*Add Custom Sidebar*/
function king_widgets_init() {
		
	global $king;
	
	$sidebars = array(
		
		'sidebar' => array( 
			__( 'Main Sidebar', 'arkahost' ), 
			__( 'Appears on posts and pages at left-side or right-side except the optional Front Page template.', 'arkahost' )
		),
		
		'sidebar-woo' => array( 
			__( 'Archive Products Sidebar', 'arkahost' ), 
			__( 'Appears on Archive Products.', 'arkahost' )
		),	
		'sidebar-woo-single' => array( 
			__( 'Single Product Sidebar', 'arkahost' ), 
			__( 'Appears on Single Product detail page', 'arkahost' )
		),
						
		'footer_1' => array( 
			__( 'Footer Column 1', 'arkahost' ), 
			__( 'Appears on column 1 at Footer', 'arkahost' )
		),		
		
		'footer_2' => array( 
			__( 'Footer Column 2', 'arkahost' ), 
			__( 'Appears on column 2 at Footer', 'arkahost' )
		),		
		
		'footer_3' => array( 
			__( 'Footer Column 3', 'arkahost' ), 
			__( 'Appears on column 3 at Footer', 'arkahost' )
		),		
		
		'footer_4' => array( 
			__( 'Footer Column 4', 'arkahost' ), 
			__( 'Appears on column 4 at Footer', 'arkahost' )
		),
		
	);
	
	if( !empty( $king->cfg['sidebars'] ) ){
		foreach( $king->cfg['sidebars'] as $sb ){
			$sidebars[ sanitize_title_with_dashes( $sb ) ] = array(
				esc_html( $sb ), 
				__( 'Dynamic Sidebar - Manage via theme-panel', 'arkahost' )
			);
		}
	}
	
	foreach( $sidebars as $k => $v ){
	
		register_sidebar( array(
			'name' => $v[0],
			'id' => $k,
			'description' => $v[1],
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3 class="widget-title"><span>',
			'after_title' => '</span></h3>',
		));	
		
	}
	
}
add_action( 'widgets_init', 'king_widgets_init' );


add_filter( 'image_size_names_choose', 'king_custom_sizes' );
function king_custom_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'large-small' => __('Large Small', 'arkahost'),
    ) );
}

add_filter( 'wp_nav_menu_items','king_mainnav_last_item', 10, 2 ); 
function king_mainnav_last_item( $items, $args ) {
	if( $args->theme_location == 'primary' || $args->theme_location == 'onepage' ){
		
		global $king, $woocommerce;

		if( empty( $king->cfg['searchNav'] ) ){
			$king->cfg['searchNav'] = 'show';
		}	
		/*
		*	Display Search Box
		*/
		if( $king->cfg['searchNav'] == 'show' ){
			$items .= '<li class="dropdown yamm ext-nav search-nav">'.
						  '<a href="#"><i class="icon icon-magnifier"></i></a>'.
						  '<ul class="dropdown-menu">'.
						  '<li>'.get_search_form( false ).'</li>'.
						  '</ul>'.
					  '</li>'; 
		}	
		
	}
	return $items; 
}

/*-----------------------------------------------------------------------------------*/
# Load layout from system before theme loads
/*-----------------------------------------------------------------------------------*/

function king_load_layout( $file ){
	
	global $king, $post;
	
	if( is_home() ){
	
		$cfg = ''; $_file = '';
	
		if( !empty( $king->cfg['blog_layout'] ) ){
			$cfg = $king->cfg['blog_layout'];
		}
		
		if( file_exists( THEME_PATH.DS.'templates'.DS.'blog-'.$cfg.'.php' ) ){
			$_file =  'templates'.DS.'blog-'.$cfg.'.php';
		}
		
		if( get_option('show_on_front',true) == 'page' && $_file === '' ){
			$id = get_option('page_for_posts',true);
			if( !empty( $id ) ){
				$get_page_tem = get_page_template_slug( $id );
			    if( !empty( $get_page_tem ) ){
					$_file = $get_page_tem;
				}	
			}
		}
	
		if( !empty( $_GET['layout'] ) ){
			if( file_exists( THEME_PATH.DS.'templates'.DS.'blog-'.$_GET['layout'].'.php' ) ){
				$_file = 'templates'.DS.'blog-'.$_GET['layout'].'.php';
			}	
		}
		
		if( !empty( $_file ) ){
			return locate_template( $_file );
		}
	}
	
	if( $king->vars( 'action', 'login' ) ){
		return locate_template( 'templates'.DS.'king.login.php' );
	}
	if( $king->vars( 'action', 'register' ) ){
		return locate_template( 'templates'.DS.'king.register.php' );
	}
	if( $king->vars( 'action', 'forgot' ) ){
		return locate_template( 'templates'.DS.'king.forgot.php' );
	}
	
	$king->tp_mode( basename( $file, '.php' ) );
	
	return $file;

}
add_action( "template_include", 'king_load_layout', 99 );

function king_exclude_category( $query ) {
    if ( $query->is_home() && $query->is_main_query() ) {
    	global $king;
    	if( !empty( $king->cfg['timeline_categories'] ) ){
	    	if( $king->cfg['timeline_categories'][0] != 'default' ){
		    	 $query->set( 'cat', implode( ',', $king->cfg['timeline_categories'] ) );	
	    	}
    	}
    }
}
add_action( 'pre_get_posts', 'king_exclude_category' );

function king_admin_notice() {
	if ( get_option('permalink_structure', true) === false ) {
    ?>
    <div class="updated">
        <p>
	        <?php sprintf( wp_kses( __('You have not yet enabled permalink, the 404 page and some functions will not work. To enable, please <a href="%s">Click here</a> and choose "Post name"', 'arkahost' ), array('a'=>array()) ), SITE_URI.'/wp-admin/options-permalink.php' ); ?>
        </p>
    </div>
    <?php
    }
}
add_action( 'admin_notices', 'king_admin_notice' );

// Add slide menu CSS to body tag
add_filter( 'body_class', 'king_body_classes' );
function king_body_classes( $classes ) {
	global $king;
	if(isset($king->cfg['slide_menu']) && $king->cfg['slide_menu'] == 1)
		$classes[] = 'slide-menu';
	return $classes;
}

function kingtheme_the_excerpt($text){
	global $post;
	$pagedes = get_post_meta( $post->ID, '_king_page_description', true );
	if( !empty( $pagedes ) ){
		return esc_attr( $pagedes );
	}
	return $text;
}
add_filter('get_the_excerpt', 'kingtheme_the_excerpt');


//define new ways for our-work item pagination
add_filter('previous_post_link', 'kingtheme_adjacent_post_link', 10, 5);
add_filter('next_post_link', 'kingtheme_adjacent_post_link', 10, 5);


function kingtheme_adjacent_post_link($output, $format, $link, $post, $adjacent){

	if ( !$post ) {
        $output = '';
    }
    
	if( empty( $post->post_type ) || $post->post_type != 'our-works')
		return $output;

	$title = $post->post_title;
    $title = apply_filters( 'the_title', $title, $post->ID );
    $rel = ($adjacent == 'previous')  ? 'prev' : 'next';

    $icon_class = ($adjacent == 'previous')  ? 'fa-chevron-left' : 'fa-chevron-right';
	
	$output = '<a class="our-works-nav our-works-nav-' . $rel . '" href="' . get_permalink( $post ) . '" rel="'. $rel .'" title="' . $title .'"><i class="fa ' . $icon_class .'"></i></a>';

 	return $output;
}


/*
* Defind ajax for newsletter actions
*/
if( !function_exists( 'king_newsletter' ) ){
	
	add_action( 'wp_ajax_king_newsletter', 'king_newsletter' );
	add_action( 'wp_ajax_nopriv_king_newsletter', 'king_newsletter' );

	function king_newsletter () { 
		global $king;

		if( !empty( $_POST[ 'king_newsletter' ] ) ) 
		{
			
			if( $_POST[ 'king_newsletter' ] == 'subcribe' ){

				$email    = $_POST[ 'king_email' ];
				$hasError = false;
				$status   = array();
				
				if ( trim( $email ) === '' ) {
					$status = array( 
						'error',
						__( 'Error: Please enter your email', 'arkahost' )
					);
					$hasError = true;
				}

				if( !$hasError && !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {

					$status = array( 
						'error',
						__( 'Error: Your email is invalid', 'arkahost' )
					);
					$hasError = true;
				}

				if( !$hasError ){

					//check which method in use
					if( isset( $king->cfg['newsletter_method'] ) && $king->cfg['newsletter_method'] == 'mc' ){

						locate_template( 'core' . DS . 'inc' . DS . 'MCAPI.class.php', true);

											
						$apikey =  $king->cfg['mc_api'];	// grab an API Key from http://admin.mailchimp.com/account/api/			
						$list_id = $king->cfg['mc_list_id'];
						$mc_api  = new MCAPI($apikey);
						$mc_api->useSecure(true);

						//If one of config is empty => return error

						if( empty( $apikey ) || empty( $list_id ) ){

							$status = array( 
								'error',
								__('Error: Can not signup into list. Please contact administrator to solve issues.', 'arkahost' )
							);
							$hasError = true;
						}
						else
						{
							if( $mc_api->listSubscribe( $list_id, $email, '') === true && empty( $status) ) {

								$status    = array( 
									'success',
									__('Success! Check your email to confirm sign up.', 'arkahost' )
								);

							}else{

								$status = array( 
									'error',
									'Error: ' . $mc_api->errorMessage
								);

							}
						}
						
					}
					else /* Subcribe email to post type subcribe */
					{
						if ( !post_type_exists( 'subcribers' ) ){
							$status = array( 
								'error',
								__('Error: Can not signup into list. Please contact administrator to solve issues.', 'arkahost' )
							);
							king_return_ajax( $status);
						}

						if ( !get_page_by_title( $email, 'OBJECT', 'subcribers') )
						{
		
							$subcribe_data = array(
								'post_title'   => wp_strip_all_tags( $email ),
								'post_content' => '',
								'post_type'    => 'subcribers',
								'post_status'  => 'pending'
							);
							
							$subcribe_id = wp_insert_post( $subcribe_data );

							if ( is_wp_error( $subcribe_id ) ) {

								$errors = $id->get_error_messages();

								foreach ( $errors as $error ) {
									$error_msg .= "{$error}\n";
								}

							}else{

								$status    = array( 
									'success',
									__('Success! Your email is subcribed.', 'arkahost' )
								);

							}
		
						}else{

							$status    = array( 
								'error',
								__('Error: This email already is subcribed', 'arkahost' )
							);
						}
					}
					
				}

				king_return_ajax( $status);
			}
		}
	}
}
if( !function_exists( 'king_return_ajax' ) ){

	function king_return_ajax( $status){

		@ob_clean();

		echo '{"status":"' . $status[0] . '","messages":"' . $status[1] . '"}';

		wp_die();

	}
}



function megamenu_meta_box( $post ){
	global $king;

	locate_template( 'options'.DS.'options.php', true );
	$megabox = get_post_meta( $post->ID , 'king_megamenu' );
	if( !empty( $megabox ) ){
		$megabox  = $megabox[0];
	}else{
		$megabox = array();
	}
	?>
	<table>
		<tr>
			<td>
				<label><?php _e('Width Of Menu','arkahost'); ?>: </label>
			</td>
			<td>
				<input type="text" name="king[menu_width]" value="<?php echo esc_attr( isset($megabox['menu_width'])? $megabox['menu_width']:'' );	?>" />
			</td>
		</tr>
	</table>
	
	<input type="hidden" name="king[_type]" value="megamenu" />
	<?php
}

add_filter('gutenberg_can_edit_post_type', 'arkahost_disable_gutenberg', 99, 2);

function arkahost_disable_gutenberg($can_edit, $post_type){
	if ($post_type == 'page')
		return false;
	else return $can_edit;
}


/** Replace link and icons for Arkahost version 5.3 **/
/** Copyright : King - Theme **/
add_filter('the_content', 'replace_all_links', 30);
function replace_all_links($content){
	$link = array( 'http://business-theme.com/preview/arkahost/wp-content/');
	$replace = array( content_url('/') );

	return  str_replace($link, $replace, $content);

}


add_filter('the_content', 'replace_all_icons', 30);
function replace_all_icons($icons){
	$iconsv4 = array('fa fa-cloud-download', 'fa fa-clock-o', 'fa fa-cloud-upload', 'fa fa-glass', 'fa fa-support', 'fa fa-lightbulb-o', 'fa fa-warning', 'fa fa-pencil', 'fa fa-hand-o-right', 'fa fa-dollar', 'fa fa-comments-o', 'fa fa-check-square-o', 'fa fa-picture-o', 'fa fa-check', 'fa fa-times', 'fa fa-wordpress', 'fa fa-building-o', 'fa fa-paper-plane-o', 'fa fa-money');

	$replace = array('fas fa-cloud-download-alt', 'far fa-clock', 'fas fa-cloud-upload-alt', 'fas fa-glass-martini', 'far fa-life-ring', 'far fa-lightbulb', 'fas fa-exclamation-triangle', 'fas fa-pencil-alt', 'far fa-hand-point-right', 'fas fa-dollar-sign', 'fas fa-comments', 'fas fa-check-square', 'far fa-image', 'fas fa-check', 'fas fa-times', 'fab fa-wordpress', 'far fa-building', 'fas fa-paper-plane', 'far fa-money-bill-alt');

	return str_replace($iconsv4, $replace, $icons);
}


/* Change Library iconpicker WPbakery */
add_filter('vc_iconpicker-type-fontawesome', 'library_icons_vc', 40);
function library_icons_vc($icons){
	$icons = array(
		'New in 5.8' => array(
			array("fab fa-500px" => "fab fa-500px"),
			array("fab fa-accessible-icon" => "fab fa-accessible-icon"),
			array("fab fa-accusoft" => "fab fa-accusoft"),
			array("fas fa-address-book" => "fas fa-address-book"),
			array("far fa-address-book" => "far fa-address-book"),
			array("fas fa-address-card" => "fas fa-address-card"),
			array("far fa-address-card" => "far fa-address-card"),
			array("fas fa-adjust" => "fas fa-adjust"),
			array("fab fa-adn" => "fab fa-adn"),
			array("fab fa-adversal" => "fab fa-adversal"),
			array("fab fa-affiliatetheme" => "fab fa-affiliatetheme"),
			array("fab fa-algolia" => "fab fa-algolia"),
			array("fas fa-align-center" => "fas fa-align-center"),
			array("fas fa-align-justify" => "fas fa-align-justify"),
			array("fas fa-align-left" => "fas fa-align-left"),
			array("fas fa-align-right" => "fas fa-align-right"),
			array("fab fa-amazon" => "fab fa-amazon"),
			array("fas fa-ambulance" => "fas fa-ambulance"),
			array("fas fa-american-sign-language-interpreting" => "fas fa-american-sign-language-interpreting"),
			array("fab fa-amilia" => "fab fa-amilia"),
			array("fas fa-anchor" => "fas fa-anchor"),
			array("fab fa-android" => "fab fa-android"),
			array("fab fa-angellist" => "fab fa-angellist"),
			array("fas fa-angle-double-down" => "fas fa-angle-double-down"),
			array("fas fa-angle-double-left" => "fas fa-angle-double-left"),
			array("fas fa-angle-double-right" => "fas fa-angle-double-right"),
			array("fas fa-angle-double-up" => "fas fa-angle-double-up"),
			array("fas fa-angle-down" => "fas fa-angle-down"),
			array("fas fa-angle-left" => "fas fa-angle-left"),
			array("fas fa-angle-right" => "fas fa-angle-right"),
			array("fas fa-angle-up" => "fas fa-angle-up"),
			array("fab fa-angrycreative" => "fab fa-angrycreative"),
			array("fab fa-angular" => "fab fa-angular"),
			array("fab fa-app-store" => "fab fa-app-store"),
			array("fab fa-app-store-ios" => "fab fa-app-store-ios"),
			array("fab fa-apper" => "fab fa-apper"),
			array("fab fa-apple" => "fab fa-apple"),
			array("fab fa-apple-pay" => "fab fa-apple-pay"),
			array("fas fa-archive" => "fas fa-archive"),
			array("fas fa-arrow-alt-circle-down" => "fas fa-arrow-alt-circle-down"),
			array("far fa-arrow-alt-circle-down" => "far fa-arrow-alt-circle-down"),
			array("fas fa-arrow-alt-circle-left" => "fas fa-arrow-alt-circle-left"),
			array("far fa-arrow-alt-circle-left" => "far fa-arrow-alt-circle-left"),
			array("fas fa-arrow-alt-circle-right" => "fas fa-arrow-alt-circle-right"),
			array("far fa-arrow-alt-circle-right" => "far fa-arrow-alt-circle-right"),
			array("fas fa-arrow-alt-circle-up" => "fas fa-arrow-alt-circle-up"),
			array("far fa-arrow-alt-circle-up" => "far fa-arrow-alt-circle-up"),
			array("fas fa-arrow-circle-down" => "fas fa-arrow-circle-down"),
			array("fas fa-arrow-circle-left" => "fas fa-arrow-circle-left"),
			array("fas fa-arrow-circle-right" => "fas fa-arrow-circle-right"),
			array("fas fa-arrow-circle-up" => "fas fa-arrow-circle-up"),
			array("fas fa-arrow-down" => "fas fa-arrow-down"),
			array("fas fa-arrow-left" => "fas fa-arrow-left"),
			array("fas fa-arrow-right" => "fas fa-arrow-right"),
			array("fas fa-arrow-up" => "fas fa-arrow-up"),
			array("fas fa-arrows-alt" => "fas fa-arrows-alt"),
			array("fas fa-arrows-alt-h" => "fas fa-arrows-alt-h"),
			array("fas fa-arrows-alt-v" => "fas fa-arrows-alt-v"),
			array("fas fa-assistive-listening-systems" => "fas fa-assistive-listening-systems"),
			array("fas fa-asterisk" => "fas fa-asterisk"),
			array("fab fa-asymmetrik" => "fab fa-asymmetrik"),
			array("fas fa-at" => "fas fa-at"),
			array("fab fa-audible" => "fab fa-audible"),
			array("fas fa-audio-description" => "fas fa-audio-description"),
			array("fab fa-autoprefixer" => "fab fa-autoprefixer"),
			array("fab fa-avianex" => "fab fa-avianex"),
			array("fab fa-aviato" => "fab fa-aviato"),
			array("fab fa-aws" => "fab fa-aws"),
			array("fas fa-backward" => "fas fa-backward"),
			array("fas fa-balance-scale" => "fas fa-balance-scale"),
			array("fas fa-ban" => "fas fa-ban"),
			array("fab fa-bandcamp" => "fab fa-bandcamp"),
			array("fas fa-barcode" => "fas fa-barcode"),
			array("fas fa-bars" => "fas fa-bars"),
			array("fas fa-bath" => "fas fa-bath"),
			array("fas fa-battery-empty" => "fas fa-battery-empty"),
			array("fas fa-battery-full" => "fas fa-battery-full"),
			array("fas fa-battery-half" => "fas fa-battery-half"),
			array("fas fa-battery-quarter" => "fas fa-battery-quarter"),
			array("fas fa-battery-three-quarters" => "fas fa-battery-three-quarters"),
			array("fas fa-bed" => "fas fa-bed"),
			array("fas fa-beer" => "fas fa-beer"),
			array("fab fa-behance" => "fab fa-behance"),
			array("fab fa-behance-square" => "fab fa-behance-square"),
			array("fas fa-bell" => "fas fa-bell"),
			array("far fa-bell" => "far fa-bell"),
			array("fas fa-bell-slash" => "fas fa-bell-slash"),
			array("far fa-bell-slash" => "far fa-bell-slash"),
			array("fas fa-bicycle" => "fas fa-bicycle"),
			array("fab fa-bimobject" => "fab fa-bimobject"),
			array("fas fa-binoculars" => "fas fa-binoculars"),
			array("fas fa-birthday-cake" => "fas fa-birthday-cake"),
			array("fab fa-bitbucket" => "fab fa-bitbucket"),
			array("fab fa-bitcoin" => "fab fa-bitcoin"),
			array("fab fa-bity" => "fab fa-bity"),
			array("fab fa-black-tie" => "fab fa-black-tie"),
			array("fab fa-blackberry" => "fab fa-blackberry"),
			array("fas fa-blind" => "fas fa-blind"),
			array("fab fa-blogger" => "fab fa-blogger"),
			array("fab fa-blogger-b" => "fab fa-blogger-b"),
			array("fab fa-bluetooth" => "fab fa-bluetooth"),
			array("fab fa-bluetooth-b" => "fab fa-bluetooth-b"),
			array("fas fa-bold" => "fas fa-bold"),
			array("fas fa-bolt" => "fas fa-bolt"),
			array("fas fa-bomb" => "fas fa-bomb"),
			array("fas fa-book" => "fas fa-book"),
			array("fas fa-bookmark" => "fas fa-bookmark"),
			array("far fa-bookmark" => "far fa-bookmark"),
			array("fas fa-braille" => "fas fa-braille"),
			array("fas fa-briefcase" => "fas fa-briefcase"),
			array("fab fa-btc" => "fab fa-btc"),
			array("fas fa-bug" => "fas fa-bug"),
			array("fas fa-building" => "fas fa-building"),
			array("far fa-building" => "far fa-building"),
			array("fas fa-bullhorn" => "fas fa-bullhorn"),
			array("fas fa-bullseye" => "fas fa-bullseye"),
			array("fab fa-buromobelexperte" => "fab fa-buromobelexperte"),
			array("fas fa-bus" => "fas fa-bus"),
			array("fab fa-buysellads" => "fab fa-buysellads"),
			array("fas fa-calculator" => "fas fa-calculator"),
			array("fas fa-calendar" => "fas fa-calendar"),
			array("far fa-calendar" => "far fa-calendar"),
			array("fas fa-calendar-alt" => "fas fa-calendar-alt"),
			array("far fa-calendar-alt" => "far fa-calendar-alt"),
			array("fas fa-calendar-check" => "fas fa-calendar-check"),
			array("far fa-calendar-check" => "far fa-calendar-check"),
			array("fas fa-calendar-minus" => "fas fa-calendar-minus"),
			array("far fa-calendar-minus" => "far fa-calendar-minus"),
			array("fas fa-calendar-plus" => "fas fa-calendar-plus"),
			array("far fa-calendar-plus" => "far fa-calendar-plus"),
			array("fas fa-calendar-times" => "fas fa-calendar-times"),
			array("far fa-calendar-times" => "far fa-calendar-times"),
			array("fas fa-camera" => "fas fa-camera"),
			array("fas fa-camera-retro" => "fas fa-camera-retro"),
			array("fas fa-car" => "fas fa-car"),
			array("fas fa-caret-down" => "fas fa-caret-down"),
			array("fas fa-caret-left" => "fas fa-caret-left"),
			array("fas fa-caret-right" => "fas fa-caret-right"),
			array("fas fa-caret-square-down" => "fas fa-caret-square-down"),
			array("far fa-caret-square-down" => "far fa-caret-square-down"),
			array("fas fa-caret-square-left" => "fas fa-caret-square-left"),
			array("far fa-caret-square-left" => "far fa-caret-square-left"),
			array("fas fa-caret-square-right" => "fas fa-caret-square-right"),
			array("far fa-caret-square-right" => "far fa-caret-square-right"),
			array("fas fa-caret-square-up" => "fas fa-caret-square-up"),
			array("far fa-caret-square-up" => "far fa-caret-square-up"),
			array("fas fa-caret-up" => "fas fa-caret-up"),
			array("fas fa-cart-arrow-down" => "fas fa-cart-arrow-down"),
			array("fas fa-cart-plus" => "fas fa-cart-plus"),
			array("fab fa-cc-amex" => "fab fa-cc-amex"),
			array("fab fa-cc-apple-pay" => "fab fa-cc-apple-pay"),
			array("fab fa-cc-diners-club" => "fab fa-cc-diners-club"),
			array("fab fa-cc-discover" => "fab fa-cc-discover"),
			array("fab fa-cc-jcb" => "fab fa-cc-jcb"),
			array("fab fa-cc-mastercard" => "fab fa-cc-mastercard"),
			array("fab fa-cc-paypal" => "fab fa-cc-paypal"),
			array("fab fa-cc-stripe" => "fab fa-cc-stripe"),
			array("fab fa-cc-visa" => "fab fa-cc-visa"),
			array("fab fa-centercode" => "fab fa-centercode"),
			array("fas fa-certificate" => "fas fa-certificate"),
			array("fas fa-chart-area" => "fas fa-chart-area"),
			array("fas fa-chart-bar" => "fas fa-chart-bar"),
			array("far fa-chart-bar" => "far fa-chart-bar"),
			array("fas fa-chart-line" => "fas fa-chart-line"),
			array("fas fa-chart-pie" => "fas fa-chart-pie"),
			array("fas fa-check" => "fas fa-check"),
			array("fas fa-check-circle" => "fas fa-check-circle"),
			array("far fa-check-circle" => "far fa-check-circle"),
			array("fas fa-check-square" => "fas fa-check-square"),
			array("far fa-check-square" => "far fa-check-square"),
			array("fas fa-chevron-circle-down" => "fas fa-chevron-circle-down"),
			array("fas fa-chevron-circle-left" => "fas fa-chevron-circle-left"),
			array("fas fa-chevron-circle-right" => "fas fa-chevron-circle-right"),
			array("fas fa-chevron-circle-up" => "fas fa-chevron-circle-up"),
			array("fas fa-chevron-down" => "fas fa-chevron-down"),
			array("fas fa-chevron-left" => "fas fa-chevron-left"),
			array("fas fa-chevron-right" => "fas fa-chevron-right"),
			array("fas fa-chevron-up" => "fas fa-chevron-up"),
			array("fas fa-child" => "fas fa-child"),
			array("fab fa-chrome" => "fab fa-chrome"),
			array("fas fa-circle" => "fas fa-circle"),
			array("far fa-circle" => "far fa-circle"),
			array("fas fa-circle-notch" => "fas fa-circle-notch"),
			array("fas fa-clipboard" => "fas fa-clipboard"),
			array("far fa-clipboard" => "far fa-clipboard"),
			array("fas fa-clock" => "fas fa-clock"),
			array("far fa-clock" => "far fa-clock"),
			array("fas fa-clone" => "fas fa-clone"),
			array("far fa-clone" => "far fa-clone"),
			array("fas fa-closed-captioning" => "fas fa-closed-captioning"),
			array("far fa-closed-captioning" => "far fa-closed-captioning"),
			array("fas fa-cloud" => "fas fa-cloud"),
			array("fas fa-cloud-download-alt" => "fas fa-cloud-download-alt"),
			array("fas fa-cloud-upload-alt" => "fas fa-cloud-upload-alt"),
			array("fab fa-cloudscale" => "fab fa-cloudscale"),
			array("fab fa-cloudsmith" => "fab fa-cloudsmith"),
			array("fab fa-cloudversify" => "fab fa-cloudversify"),
			array("fas fa-code" => "fas fa-code"),
			array("fas fa-code-branch" => "fas fa-code-branch"),
			array("fab fa-codepen" => "fab fa-codepen"),
			array("fab fa-codiepie" => "fab fa-codiepie"),
			array("fas fa-coffee" => "fas fa-coffee"),
			array("fas fa-cog" => "fas fa-cog"),
			array("fas fa-cogs" => "fas fa-cogs"),
			array("fas fa-columns" => "fas fa-columns"),
			array("fas fa-comment" => "fas fa-comment"),
			array("far fa-comment" => "far fa-comment"),
			array("fas fa-comment-alt" => "fas fa-comment-alt"),
			array("far fa-comment-alt" => "far fa-comment-alt"),
			array("fas fa-comments" => "fas fa-comments"),
			array("far fa-comments" => "far fa-comments"),
			array("fas fa-compass" => "fas fa-compass"),
			array("far fa-compass" => "far fa-compass"),
			array("fas fa-compress" => "fas fa-compress"),
			array("fab fa-connectdevelop" => "fab fa-connectdevelop"),
			array("fab fa-contao" => "fab fa-contao"),
			array("fas fa-copy" => "fas fa-copy"),
			array("far fa-copy" => "far fa-copy"),
			array("fas fa-copyright" => "fas fa-copyright"),
			array("far fa-copyright" => "far fa-copyright"),
			array("fab fa-cpanel" => "fab fa-cpanel"),
			array("fab fa-creative-commons" => "fab fa-creative-commons"),
			array("fas fa-credit-card" => "fas fa-credit-card"),
			array("far fa-credit-card" => "far fa-credit-card"),
			array("fas fa-crop" => "fas fa-crop"),
			array("fas fa-crosshairs" => "fas fa-crosshairs"),
			array("fab fa-css3" => "fab fa-css3"),
			array("fab fa-css3-alt" => "fab fa-css3-alt"),
			array("fas fa-cube" => "fas fa-cube"),
			array("fas fa-cubes" => "fas fa-cubes"),
			array("fas fa-cut" => "fas fa-cut"),
			array("fab fa-cuttlefish" => "fab fa-cuttlefish"),
			array("fab fa-d-and-d" => "fab fa-d-and-d"),
			array("fab fa-dashcube" => "fab fa-dashcube"),
			array("fas fa-database" => "fas fa-database"),
			array("fas fa-deaf" => "fas fa-deaf"),
			array("fab fa-delicious" => "fab fa-delicious"),
			array("fab fa-deploydog" => "fab fa-deploydog"),
			array("fab fa-deskpro" => "fab fa-deskpro"),
			array("fas fa-desktop" => "fas fa-desktop"),
			array("fab fa-deviantart" => "fab fa-deviantart"),
			array("fab fa-digg" => "fab fa-digg"),
			array("fab fa-digital-ocean" => "fab fa-digital-ocean"),
			array("fab fa-discord" => "fab fa-discord"),
			array("fab fa-discourse" => "fab fa-discourse"),
			array("fab fa-dochub" => "fab fa-dochub"),
			array("fab fa-docker" => "fab fa-docker"),
			array("fas fa-dollar-sign" => "fas fa-dollar-sign"),
			array("fas fa-dot-circle" => "fas fa-dot-circle"),
			array("far fa-dot-circle" => "far fa-dot-circle"),
			array("fas fa-download" => "fas fa-download"),
			array("fab fa-draft2digital" => "fab fa-draft2digital"),
			array("fab fa-dribbble" => "fab fa-dribbble"),
			array("fab fa-dribbble-square" => "fab fa-dribbble-square"),
			array("fab fa-dropbox" => "fab fa-dropbox"),
			array("fab fa-drupal" => "fab fa-drupal"),
			array("fab fa-dyalog" => "fab fa-dyalog"),
			array("fab fa-earlybirds" => "fab fa-earlybirds"),
			array("fab fa-edge" => "fab fa-edge"),
			array("fas fa-edit" => "fas fa-edit"),
			array("far fa-edit" => "far fa-edit"),
			array("fas fa-eject" => "fas fa-eject"),
			array("fas fa-ellipsis-h" => "fas fa-ellipsis-h"),
			array("fas fa-ellipsis-v" => "fas fa-ellipsis-v"),
			array("fab fa-ember" => "fab fa-ember"),
			array("fab fa-empire" => "fab fa-empire"),
			array("fas fa-envelope" => "fas fa-envelope"),
			array("far fa-envelope" => "far fa-envelope"),
			array("fas fa-envelope-open" => "fas fa-envelope-open"),
			array("far fa-envelope-open" => "far fa-envelope-open"),
			array("fas fa-envelope-square" => "fas fa-envelope-square"),
			array("fab fa-envira" => "fab fa-envira"),
			array("fas fa-eraser" => "fas fa-eraser"),
			array("fab fa-erlang" => "fab fa-erlang"),
			array("fab fa-etsy" => "fab fa-etsy"),
			array("fas fa-euro-sign" => "fas fa-euro-sign"),
			array("fas fa-exchange-alt" => "fas fa-exchange-alt"),
			array("fas fa-exclamation" => "fas fa-exclamation"),
			array("fas fa-exclamation-circle" => "fas fa-exclamation-circle"),
			array("fas fa-exclamation-triangle" => "fas fa-exclamation-triangle"),
			array("fas fa-expand" => "fas fa-expand"),
			array("fas fa-expand-arrows-alt" => "fas fa-expand-arrows-alt"),
			array("fab fa-expeditedssl" => "fab fa-expeditedssl"),
			array("fas fa-external-link-alt" => "fas fa-external-link-alt"),
			array("fas fa-external-link-square-alt" => "fas fa-external-link-square-alt"),
			array("fas fa-eye" => "fas fa-eye"),
			array("fas fa-eye-dropper" => "fas fa-eye-dropper"),
			array("fas fa-eye-slash" => "fas fa-eye-slash"),
			array("far fa-eye-slash" => "far fa-eye-slash"),
			array("fab fa-facebook" => "fab fa-facebook"),
			array("fab fa-facebook-f" => "fab fa-facebook-f"),
			array("fab fa-facebook-messenger" => "fab fa-facebook-messenger"),
			array("fab fa-facebook-square" => "fab fa-facebook-square"),
			array("fas fa-fast-backward" => "fas fa-fast-backward"),
			array("fas fa-fast-forward" => "fas fa-fast-forward"),
			array("fas fa-fax" => "fas fa-fax"),
			array("fas fa-female" => "fas fa-female"),
			array("fas fa-fighter-jet" => "fas fa-fighter-jet"),
			array("fas fa-file" => "fas fa-file"),
			array("far fa-file" => "far fa-file"),
			array("fas fa-file-alt" => "fas fa-file-alt"),
			array("far fa-file-alt" => "far fa-file-alt"),
			array("fas fa-file-archive" => "fas fa-file-archive"),
			array("far fa-file-archive" => "far fa-file-archive"),
			array("fas fa-file-audio" => "fas fa-file-audio"),
			array("far fa-file-audio" => "far fa-file-audio"),
			array("fas fa-file-code" => "fas fa-file-code"),
			array("far fa-file-code" => "far fa-file-code"),
			array("fas fa-file-excel" => "fas fa-file-excel"),
			array("far fa-file-excel" => "far fa-file-excel"),
			array("fas fa-file-image" => "fas fa-file-image"),
			array("far fa-file-image" => "far fa-file-image"),
			array("fas fa-file-pdf" => "fas fa-file-pdf"),
			array("far fa-file-pdf" => "far fa-file-pdf"),
			array("fas fa-file-powerpoint" => "fas fa-file-powerpoint"),
			array("far fa-file-powerpoint" => "far fa-file-powerpoint"),
			array("fas fa-file-video" => "fas fa-file-video"),
			array("far fa-file-video" => "far fa-file-video"),
			array("fas fa-file-word" => "fas fa-file-word"),
			array("far fa-file-word" => "far fa-file-word"),
			array("fas fa-film" => "fas fa-film"),
			array("fas fa-filter" => "fas fa-filter"),
			array("fas fa-fire" => "fas fa-fire"),
			array("fas fa-fire-extinguisher" => "fas fa-fire-extinguisher"),
			array("fab fa-firefox" => "fab fa-firefox"),
			array("fab fa-first-order" => "fab fa-first-order"),
			array("fab fa-firstdraft" => "fab fa-firstdraft"),
			array("fas fa-flag" => "fas fa-flag"),
			array("far fa-flag" => "far fa-flag"),
			array("fas fa-flag-checkered" => "fas fa-flag-checkered"),
			array("fas fa-flask" => "fas fa-flask"),
			array("fab fa-flickr" => "fab fa-flickr"),
			array("fab fa-fly" => "fab fa-fly"),
			array("fas fa-folder" => "fas fa-folder"),
			array("far fa-folder" => "far fa-folder"),
			array("fas fa-folder-open" => "fas fa-folder-open"),
			array("far fa-folder-open" => "far fa-folder-open"),
			array("fas fa-font" => "fas fa-font"),
			array("fab fa-font-awesome" => "fab fa-font-awesome"),
			array("fab fa-font-awesome-alt" => "fab fa-font-awesome-alt"),
			array("fab fa-font-awesome-flag" => "fab fa-font-awesome-flag"),
			array("fab fa-fonticons" => "fab fa-fonticons"),
			array("fab fa-fonticons-fi" => "fab fa-fonticons-fi"),
			array("fab fa-fort-awesome" => "fab fa-fort-awesome"),
			array("fab fa-fort-awesome-alt" => "fab fa-fort-awesome-alt"),
			array("fab fa-forumbee" => "fab fa-forumbee"),
			array("fas fa-forward" => "fas fa-forward"),
			array("fab fa-foursquare" => "fab fa-foursquare"),
			array("fab fa-free-code-camp" => "fab fa-free-code-camp"),
			array("fab fa-freebsd" => "fab fa-freebsd"),
			array("fas fa-frown" => "fas fa-frown"),
			array("far fa-frown" => "far fa-frown"),
			array("fas fa-futbol" => "fas fa-futbol"),
			array("far fa-futbol" => "far fa-futbol"),
			array("fas fa-gamepad" => "fas fa-gamepad"),
			array("fas fa-gavel" => "fas fa-gavel"),
			array("fas fa-gem" => "fas fa-gem"),
			array("far fa-gem" => "far fa-gem"),
			array("fas fa-genderless" => "fas fa-genderless"),
			array("fab fa-get-pocket" => "fab fa-get-pocket"),
			array("fab fa-gg" => "fab fa-gg"),
			array("fab fa-gg-circle" => "fab fa-gg-circle"),
			array("fas fa-gift" => "fas fa-gift"),
			array("fab fa-git" => "fab fa-git"),
			array("fab fa-git-square" => "fab fa-git-square"),
			array("fab fa-github" => "fab fa-github"),
			array("fab fa-github-alt" => "fab fa-github-alt"),
			array("fab fa-github-square" => "fab fa-github-square"),
			array("fab fa-gitkraken" => "fab fa-gitkraken"),
			array("fab fa-gitlab" => "fab fa-gitlab"),
			array("fab fa-gitter" => "fab fa-gitter"),
			array("fas fa-glass-martini" => "fas fa-glass-martini"),
			array("fab fa-glide" => "fab fa-glide"),
			array("fab fa-glide-g" => "fab fa-glide-g"),
			array("fas fa-globe" => "fas fa-globe"),
			array("fab fa-gofore" => "fab fa-gofore"),
			array("fab fa-goodreads" => "fab fa-goodreads"),
			array("fab fa-goodreads-g" => "fab fa-goodreads-g"),
			array("fab fa-google" => "fab fa-google"),
			array("fab fa-google-drive" => "fab fa-google-drive"),
			array("fab fa-google-play" => "fab fa-google-play"),
			array("fab fa-google-plus" => "fab fa-google-plus"),
			array("fab fa-google-plus-g" => "fab fa-google-plus-g"),
			array("fab fa-google-plus-square" => "fab fa-google-plus-square"),
			array("fab fa-google-wallet" => "fab fa-google-wallet"),
			array("fas fa-graduation-cap" => "fas fa-graduation-cap"),
			array("fab fa-gratipay" => "fab fa-gratipay"),
			array("fab fa-grav" => "fab fa-grav"),
			array("fab fa-gripfire" => "fab fa-gripfire"),
			array("fab fa-grunt" => "fab fa-grunt"),
			array("fab fa-gulp" => "fab fa-gulp"),
			array("fas fa-h-square" => "fas fa-h-square"),
			array("fab fa-hacker-news" => "fab fa-hacker-news"),
			array("fab fa-hacker-news-square" => "fab fa-hacker-news-square"),
			array("fas fa-hand-lizard" => "fas fa-hand-lizard"),
			array("far fa-hand-lizard" => "far fa-hand-lizard"),
			array("fas fa-hand-paper" => "fas fa-hand-paper"),
			array("far fa-hand-paper" => "far fa-hand-paper"),
			array("fas fa-hand-peace" => "fas fa-hand-peace"),
			array("far fa-hand-peace" => "far fa-hand-peace"),
			array("fas fa-hand-point-down" => "fas fa-hand-point-down"),
			array("far fa-hand-point-down" => "far fa-hand-point-down"),
			array("fas fa-hand-point-left" => "fas fa-hand-point-left"),
			array("far fa-hand-point-left" => "far fa-hand-point-left"),
			array("fas fa-hand-point-right" => "fas fa-hand-point-right"),
			array("far fa-hand-point-right" => "far fa-hand-point-right"),
			array("fas fa-hand-point-up" => "fas fa-hand-point-up"),
			array("far fa-hand-point-up" => "far fa-hand-point-up"),
			array("fas fa-hand-pointer" => "fas fa-hand-pointer"),
			array("far fa-hand-pointer" => "far fa-hand-pointer"),
			array("fas fa-hand-rock" => "fas fa-hand-rock"),
			array("far fa-hand-rock" => "far fa-hand-rock"),
			array("fas fa-hand-scissors" => "fas fa-hand-scissors"),
			array("far fa-hand-scissors" => "far fa-hand-scissors"),
			array("fas fa-hand-spock" => "fas fa-hand-spock"),
			array("far fa-hand-spock" => "far fa-hand-spock"),
			array("fas fa-handshake" => "fas fa-handshake"),
			array("far fa-handshake" => "far fa-handshake"),
			array("fas fa-hashtag" => "fas fa-hashtag"),
			array("fas fa-hdd" => "fas fa-hdd"),
			array("far fa-hdd" => "far fa-hdd"),
			array("fas fa-heading" => "fas fa-heading"),
			array("fas fa-headphones" => "fas fa-headphones"),
			array("fas fa-heart" => "fas fa-heart"),
			array("far fa-heart" => "far fa-heart"),
			array("fas fa-heartbeat" => "fas fa-heartbeat"),
			array("fab fa-hire-a-helper" => "fab fa-hire-a-helper"),
			array("fas fa-history" => "fas fa-history"),
			array("fas fa-home" => "fas fa-home"),
			array("fab fa-hooli" => "fab fa-hooli"),
			array("fas fa-hospital" => "fas fa-hospital"),
			array("far fa-hospital" => "far fa-hospital"),
			array("fab fa-hotjar" => "fab fa-hotjar"),
			array("fas fa-hourglass" => "fas fa-hourglass"),
			array("far fa-hourglass" => "far fa-hourglass"),
			array("fas fa-hourglass-end" => "fas fa-hourglass-end"),
			array("fas fa-hourglass-half" => "fas fa-hourglass-half"),
			array("fas fa-hourglass-start" => "fas fa-hourglass-start"),
			array("fab fa-houzz" => "fab fa-houzz"),
			array("fab fa-html5" => "fab fa-html5"),
			array("fab fa-hubspot" => "fab fa-hubspot"),
			array("fas fa-i-cursor" => "fas fa-i-cursor"),
			array("fas fa-id-badge" => "fas fa-id-badge"),
			array("far fa-id-badge" => "far fa-id-badge"),
			array("fas fa-id-card" => "fas fa-id-card"),
			array("far fa-id-card" => "far fa-id-card"),
			array("fas fa-image" => "fas fa-image"),
			array("far fa-image" => "far fa-image"),
			array("fas fa-images" => "fas fa-images"),
			array("far fa-images" => "far fa-images"),
			array("fab fa-imdb" => "fab fa-imdb"),
			array("fas fa-inbox" => "fas fa-inbox"),
			array("fas fa-indent" => "fas fa-indent"),
			array("fas fa-industry" => "fas fa-industry"),
			array("fas fa-info" => "fas fa-info"),
			array("fas fa-info-circle" => "fas fa-info-circle"),
			array("fab fa-instagram" => "fab fa-instagram"),
			array("fab fa-internet-explorer" => "fab fa-internet-explorer"),
			array("fab fa-ioxhost" => "fab fa-ioxhost"),
			array("fas fa-italic" => "fas fa-italic"),
			array("fab fa-itunes" => "fab fa-itunes"),
			array("fab fa-itunes-note" => "fab fa-itunes-note"),
			array("fab fa-jenkins" => "fab fa-jenkins"),
			array("fab fa-joget" => "fab fa-joget"),
			array("fab fa-joomla" => "fab fa-joomla"),
			array("fab fa-js" => "fab fa-js"),
			array("fab fa-js-square" => "fab fa-js-square"),
			array("fab fa-jsfiddle" => "fab fa-jsfiddle"),
			array("fas fa-key" => "fas fa-key"),
			array("fas fa-keyboard" => "fas fa-keyboard"),
			array("far fa-keyboard" => "far fa-keyboard"),
			array("fab fa-keycdn" => "fab fa-keycdn"),
			array("fab fa-kickstarter" => "fab fa-kickstarter"),
			array("fab fa-kickstarter-k" => "fab fa-kickstarter-k"),
			array("fas fa-language" => "fas fa-language"),
			array("fas fa-laptop" => "fas fa-laptop"),
			array("fab fa-laravel" => "fab fa-laravel"),
			array("fab fa-lastfm" => "fab fa-lastfm"),
			array("fab fa-lastfm-square" => "fab fa-lastfm-square"),
			array("fas fa-leaf" => "fas fa-leaf"),
			array("fab fa-leanpub" => "fab fa-leanpub"),
			array("fas fa-lemon" => "fas fa-lemon"),
			array("far fa-lemon" => "far fa-lemon"),
			array("fab fa-less" => "fab fa-less"),
			array("fas fa-level-down-alt" => "fas fa-level-down-alt"),
			array("fas fa-level-up-alt" => "fas fa-level-up-alt"),
			array("fas fa-life-ring" => "fas fa-life-ring"),
			array("far fa-life-ring" => "far fa-life-ring"),
			array("fas fa-lightbulb" => "fas fa-lightbulb"),
			array("far fa-lightbulb" => "far fa-lightbulb"),
			array("fab fa-line" => "fab fa-line"),
			array("fas fa-link" => "fas fa-link"),
			array("fab fa-linkedin" => "fab fa-linkedin"),
			array("fab fa-linkedin-in" => "fab fa-linkedin-in"),
			array("fab fa-linode" => "fab fa-linode"),
			array("fab fa-linux" => "fab fa-linux"),
			array("fas fa-lira-sign" => "fas fa-lira-sign"),
			array("fas fa-list" => "fas fa-list"),
			array("fas fa-list-alt" => "fas fa-list-alt"),
			array("far fa-list-alt" => "far fa-list-alt"),
			array("fas fa-list-ol" => "fas fa-list-ol"),
			array("fas fa-list-ul" => "fas fa-list-ul"),
			array("fas fa-location-arrow" => "fas fa-location-arrow"),
			array("fas fa-lock" => "fas fa-lock"),
			array("fas fa-lock-open" => "fas fa-lock-open"),
			array("fas fa-long-arrow-alt-down" => "fas fa-long-arrow-alt-down"),
			array("fas fa-long-arrow-alt-left" => "fas fa-long-arrow-alt-left"),
			array("fas fa-long-arrow-alt-right" => "fas fa-long-arrow-alt-right"),
			array("fas fa-long-arrow-alt-up" => "fas fa-long-arrow-alt-up"),
			array("fas fa-low-vision" => "fas fa-low-vision"),
			array("fab fa-lyft" => "fab fa-lyft"),
			array("fab fa-magento" => "fab fa-magento"),
			array("fas fa-magic" => "fas fa-magic"),
			array("fas fa-magnet" => "fas fa-magnet"),
			array("fas fa-male" => "fas fa-male"),
			array("fas fa-map" => "fas fa-map"),
			array("far fa-map" => "far fa-map"),
			array("fas fa-map-marker" => "fas fa-map-marker"),
			array("fas fa-map-marker-alt" => "fas fa-map-marker-alt"),
			array("fas fa-map-pin" => "fas fa-map-pin"),
			array("fas fa-map-signs" => "fas fa-map-signs"),
			array("fas fa-mars" => "fas fa-mars"),
			array("fas fa-mars-double" => "fas fa-mars-double"),
			array("fas fa-mars-stroke" => "fas fa-mars-stroke"),
			array("fas fa-mars-stroke-h" => "fas fa-mars-stroke-h"),
			array("fas fa-mars-stroke-v" => "fas fa-mars-stroke-v"),
			array("fab fa-maxcdn" => "fab fa-maxcdn"),
			array("fab fa-medapps" => "fab fa-medapps"),
			array("fab fa-medium" => "fab fa-medium"),
			array("fab fa-medium-m" => "fab fa-medium-m"),
			array("fas fa-medkit" => "fas fa-medkit"),
			array("fab fa-medrt" => "fab fa-medrt"),
			array("fab fa-meetup" => "fab fa-meetup"),
			array("fas fa-meh" => "fas fa-meh"),
			array("far fa-meh" => "far fa-meh"),
			array("fas fa-mercury" => "fas fa-mercury"),
			array("fas fa-microchip" => "fas fa-microchip"),
			array("fas fa-microphone" => "fas fa-microphone"),
			array("fas fa-microphone-slash" => "fas fa-microphone-slash"),
			array("fab fa-microsoft" => "fab fa-microsoft"),
			array("fas fa-minus" => "fas fa-minus"),
			array("fas fa-minus-circle" => "fas fa-minus-circle"),
			array("fas fa-minus-square" => "fas fa-minus-square"),
			array("far fa-minus-square" => "far fa-minus-square"),
			array("fab fa-mix" => "fab fa-mix"),
			array("fab fa-mixcloud" => "fab fa-mixcloud"),
			array("fab fa-mizuni" => "fab fa-mizuni"),
			array("fas fa-mobile" => "fas fa-mobile"),
			array("fas fa-mobile-alt" => "fas fa-mobile-alt"),
			array("fab fa-modx" => "fab fa-modx"),
			array("fab fa-monero" => "fab fa-monero"),
			array("fas fa-money-bill-alt" => "fas fa-money-bill-alt"),
			array("far fa-money-bill-alt" => "far fa-money-bill-alt"),
			array("fas fa-moon" => "fas fa-moon"),
			array("far fa-moon" => "far fa-moon"),
			array("fas fa-motorcycle" => "fas fa-motorcycle"),
			array("fas fa-mouse-pointer" => "fas fa-mouse-pointer"),
			array("fas fa-music" => "fas fa-music"),
			array("fab fa-napster" => "fab fa-napster"),
			array("fas fa-neuter" => "fas fa-neuter"),
			array("fas fa-newspaper" => "fas fa-newspaper"),
			array("far fa-newspaper" => "far fa-newspaper"),
			array("fab fa-nintendo-switch" => "fab fa-nintendo-switch"),
			array("fab fa-node" => "fab fa-node"),
			array("fab fa-node-js" => "fab fa-node-js"),
			array("fab fa-npm" => "fab fa-npm"),
			array("fab fa-ns8" => "fab fa-ns8"),
			array("fab fa-nutritionix" => "fab fa-nutritionix"),
			array("fas fa-object-group" => "fas fa-object-group"),
			array("far fa-object-group" => "far fa-object-group"),
			array("fas fa-object-ungroup" => "fas fa-object-ungroup"),
			array("far fa-object-ungroup" => "far fa-object-ungroup"),
			array("fab fa-odnoklassniki" => "fab fa-odnoklassniki"),
			array("fab fa-odnoklassniki-square" => "fab fa-odnoklassniki-square"),
			array("fab fa-opencart" => "fab fa-opencart"),
			array("fab fa-openid" => "fab fa-openid"),
			array("fab fa-opera" => "fab fa-opera"),
			array("fab fa-optin-monster" => "fab fa-optin-monster"),
			array("fab fa-osi" => "fab fa-osi"),
			array("fas fa-outdent" => "fas fa-outdent"),
			array("fab fa-page4" => "fab fa-page4"),
			array("fab fa-pagelines" => "fab fa-pagelines"),
			array("fas fa-paint-brush" => "fas fa-paint-brush"),
			array("fab fa-palfed" => "fab fa-palfed"),
			array("fas fa-paper-plane" => "fas fa-paper-plane"),
			array("far fa-paper-plane" => "far fa-paper-plane"),
			array("fas fa-paperclip" => "fas fa-paperclip"),
			array("fas fa-paragraph" => "fas fa-paragraph"),
			array("fas fa-paste" => "fas fa-paste"),
			array("fab fa-patreon" => "fab fa-patreon"),
			array("fas fa-pause" => "fas fa-pause"),
			array("fas fa-pause-circle" => "fas fa-pause-circle"),
			array("far fa-pause-circle" => "far fa-pause-circle"),
			array("fas fa-paw" => "fas fa-paw"),
			array("fab fa-paypal" => "fab fa-paypal"),
			array("fas fa-pen-square" => "fas fa-pen-square"),
			array("fas fa-pencil-alt" => "fas fa-pencil-alt"),
			array("fas fa-percent" => "fas fa-percent"),
			array("fab fa-periscope" => "fab fa-periscope"),
			array("fab fa-phabricator" => "fab fa-phabricator"),
			array("fab fa-phoenix-framework" => "fab fa-phoenix-framework"),
			array("fas fa-phone" => "fas fa-phone"),
			array("fas fa-phone-square" => "fas fa-phone-square"),
			array("fas fa-phone-volume" => "fas fa-phone-volume"),
			array("fab fa-pied-piper" => "fab fa-pied-piper"),
			array("fab fa-pied-piper-alt" => "fab fa-pied-piper-alt"),
			array("fab fa-pied-piper-pp" => "fab fa-pied-piper-pp"),
			array("fab fa-pinterest" => "fab fa-pinterest"),
			array("fab fa-pinterest-p" => "fab fa-pinterest-p"),
			array("fab fa-pinterest-square" => "fab fa-pinterest-square"),
			array("fas fa-plane" => "fas fa-plane"),
			array("fas fa-play" => "fas fa-play"),
			array("fas fa-play-circle" => "fas fa-play-circle"),
			array("far fa-play-circle" => "far fa-play-circle"),
			array("fab fa-playstation" => "fab fa-playstation"),
			array("fas fa-plug" => "fas fa-plug"),
			array("fas fa-plus" => "fas fa-plus"),
			array("fas fa-plus-circle" => "fas fa-plus-circle"),
			array("fas fa-plus-square" => "fas fa-plus-square"),
			array("far fa-plus-square" => "far fa-plus-square"),
			array("fas fa-podcast" => "fas fa-podcast"),
			array("fas fa-pound-sign" => "fas fa-pound-sign"),
			array("fas fa-power-off" => "fas fa-power-off"),
			array("fas fa-print" => "fas fa-print"),
			array("fab fa-product-hunt" => "fab fa-product-hunt"),
			array("fab fa-pushed" => "fab fa-pushed"),
			array("fas fa-puzzle-piece" => "fas fa-puzzle-piece"),
			array("fab fa-python" => "fab fa-python"),
			array("fab fa-qq" => "fab fa-qq"),
			array("fas fa-qrcode" => "fas fa-qrcode"),
			array("fas fa-question" => "fas fa-question"),
			array("fas fa-question-circle" => "fas fa-question-circle"),
			array("far fa-question-circle" => "far fa-question-circle"),
			array("fab fa-quora" => "fab fa-quora"),
			array("fas fa-quote-left" => "fas fa-quote-left"),
			array("fas fa-quote-right" => "fas fa-quote-right"),
			array("fas fa-random" => "fas fa-random"),
			array("fab fa-ravelry" => "fab fa-ravelry"),
			array("fab fa-react" => "fab fa-react"),
			array("fab fa-rebel" => "fab fa-rebel"),
			array("fas fa-recycle" => "fas fa-recycle"),
			array("fab fa-red-river" => "fab fa-red-river"),
			array("fab fa-reddit" => "fab fa-reddit"),
			array("fab fa-reddit-alien" => "fab fa-reddit-alien"),
			array("fab fa-reddit-square" => "fab fa-reddit-square"),
			array("fas fa-redo" => "fas fa-redo"),
			array("fas fa-redo-alt" => "fas fa-redo-alt"),
			array("fas fa-registered" => "fas fa-registered"),
			array("far fa-registered" => "far fa-registered"),
			array("fab fa-rendact" => "fab fa-rendact"),
			array("fab fa-renren" => "fab fa-renren"),
			array("fas fa-reply" => "fas fa-reply"),
			array("fas fa-reply-all" => "fas fa-reply-all"),
			array("fab fa-replyd" => "fab fa-replyd"),
			array("fab fa-resolving" => "fab fa-resolving"),
			array("fas fa-retweet" => "fas fa-retweet"),
			array("fas fa-road" => "fas fa-road"),
			array("fas fa-rocket" => "fas fa-rocket"),
			array("fab fa-rocketchat" => "fab fa-rocketchat"),
			array("fab fa-rockrms" => "fab fa-rockrms"),
			array("fas fa-rss" => "fas fa-rss"),
			array("fas fa-rss-square" => "fas fa-rss-square"),
			array("fas fa-ruble-sign" => "fas fa-ruble-sign"),
			array("fas fa-rupee-sign" => "fas fa-rupee-sign"),
			array("fab fa-safari" => "fab fa-safari"),
			array("fab fa-sass" => "fab fa-sass"),
			array("fas fa-save" => "fas fa-save"),
			array("far fa-save" => "far fa-save"),
			array("fab fa-schlix" => "fab fa-schlix"),
			array("fab fa-scribd" => "fab fa-scribd"),
			array("fas fa-search" => "fas fa-search"),
			array("fas fa-search-minus" => "fas fa-search-minus"),
			array("fas fa-search-plus" => "fas fa-search-plus"),
			array("fab fa-searchengin" => "fab fa-searchengin"),
			array("fab fa-sellcast" => "fab fa-sellcast"),
			array("fab fa-sellsy" => "fab fa-sellsy"),
			array("fas fa-server" => "fas fa-server"),
			array("fab fa-servicestack" => "fab fa-servicestack"),
			array("fas fa-share" => "fas fa-share"),
			array("fas fa-share-alt" => "fas fa-share-alt"),
			array("fas fa-share-alt-square" => "fas fa-share-alt-square"),
			array("fas fa-share-square" => "fas fa-share-square"),
			array("far fa-share-square" => "far fa-share-square"),
			array("fas fa-shekel-sign" => "fas fa-shekel-sign"),
			array("fas fa-shield-alt" => "fas fa-shield-alt"),
			array("fas fa-ship" => "fas fa-ship"),
			array("fab fa-shirtsinbulk" => "fab fa-shirtsinbulk"),
			array("fas fa-shopping-bag" => "fas fa-shopping-bag"),
			array("fas fa-shopping-basket" => "fas fa-shopping-basket"),
			array("fas fa-shopping-cart" => "fas fa-shopping-cart"),
			array("fas fa-shower" => "fas fa-shower"),
			array("fas fa-sign-in-alt" => "fas fa-sign-in-alt"),
			array("fas fa-sign-language" => "fas fa-sign-language"),
			array("fas fa-sign-out-alt" => "fas fa-sign-out-alt"),
			array("fas fa-signal" => "fas fa-signal"),
			array("fab fa-simplybuilt" => "fab fa-simplybuilt"),
			array("fab fa-sistrix" => "fab fa-sistrix"),
			array("fas fa-sitemap" => "fas fa-sitemap"),
			array("fab fa-skyatlas" => "fab fa-skyatlas"),
			array("fab fa-skype" => "fab fa-skype"),
			array("fab fa-slack" => "fab fa-slack"),
			array("fab fa-slack-hash" => "fab fa-slack-hash"),
			array("fas fa-sliders-h" => "fas fa-sliders-h"),
			array("fab fa-slideshare" => "fab fa-slideshare"),
			array("fas fa-smile" => "fas fa-smile"),
			array("far fa-smile" => "far fa-smile"),
			array("fab fa-snapchat" => "fab fa-snapchat"),
			array("fab fa-snapchat-ghost" => "fab fa-snapchat-ghost"),
			array("fab fa-snapchat-square" => "fab fa-snapchat-square"),
			array("fas fa-snowflake" => "fas fa-snowflake"),
			array("far fa-snowflake" => "far fa-snowflake"),
			array("fas fa-sort" => "fas fa-sort"),
			array("fas fa-sort-alpha-down" => "fas fa-sort-alpha-down"),
			array("fas fa-sort-alpha-up" => "fas fa-sort-alpha-up"),
			array("fas fa-sort-amount-down" => "fas fa-sort-amount-down"),
			array("fas fa-sort-amount-up" => "fas fa-sort-amount-up"),
			array("fas fa-sort-down" => "fas fa-sort-down"),
			array("fas fa-sort-numeric-down" => "fas fa-sort-numeric-down"),
			array("fas fa-sort-numeric-up" => "fas fa-sort-numeric-up"),
			array("fas fa-sort-up" => "fas fa-sort-up"),
			array("fab fa-soundcloud" => "fab fa-soundcloud"),
			array("fas fa-space-shuttle" => "fas fa-space-shuttle"),
			array("fab fa-speakap" => "fab fa-speakap"),
			array("fas fa-spinner" => "fas fa-spinner"),
			array("fab fa-spotify" => "fab fa-spotify"),
			array("fas fa-square" => "fas fa-square"),
			array("far fa-square" => "far fa-square"),
			array("fab fa-stack-exchange" => "fab fa-stack-exchange"),
			array("fab fa-stack-overflow" => "fab fa-stack-overflow"),
			array("fas fa-star" => "fas fa-star"),
			array("far fa-star" => "far fa-star"),
			array("fas fa-star-half" => "fas fa-star-half"),
			array("far fa-star-half" => "far fa-star-half"),
			array("fab fa-staylinked" => "fab fa-staylinked"),
			array("fab fa-steam" => "fab fa-steam"),
			array("fab fa-steam-square" => "fab fa-steam-square"),
			array("fab fa-steam-symbol" => "fab fa-steam-symbol"),
			array("fas fa-step-backward" => "fas fa-step-backward"),
			array("fas fa-step-forward" => "fas fa-step-forward"),
			array("fas fa-stethoscope" => "fas fa-stethoscope"),
			array("fab fa-sticker-mule" => "fab fa-sticker-mule"),
			array("fas fa-sticky-note" => "fas fa-sticky-note"),
			array("far fa-sticky-note" => "far fa-sticky-note"),
			array("fas fa-stop" => "fas fa-stop"),
			array("fas fa-stop-circle" => "fas fa-stop-circle"),
			array("far fa-stop-circle" => "far fa-stop-circle"),
			array("fab fa-strava" => "fab fa-strava"),
			array("fas fa-street-view" => "fas fa-street-view"),
			array("fas fa-strikethrough" => "fas fa-strikethrough"),
			array("fab fa-stripe" => "fab fa-stripe"),
			array("fab fa-stripe-s" => "fab fa-stripe-s"),
			array("fab fa-studiovinari" => "fab fa-studiovinari"),
			array("fab fa-stumbleupon" => "fab fa-stumbleupon"),
			array("fab fa-stumbleupon-circle" => "fab fa-stumbleupon-circle"),
			array("fas fa-subscript" => "fas fa-subscript"),
			array("fas fa-subway" => "fas fa-subway"),
			array("fas fa-suitcase" => "fas fa-suitcase"),
			array("fas fa-sun" => "fas fa-sun"),
			array("far fa-sun" => "far fa-sun"),
			array("fab fa-superpowers" => "fab fa-superpowers"),
			array("fas fa-superscript" => "fas fa-superscript"),
			array("fab fa-supple" => "fab fa-supple"),
			array("fas fa-sync" => "fas fa-sync"),
			array("fas fa-sync-alt" => "fas fa-sync-alt"),
			array("fas fa-table" => "fas fa-table"),
			array("fas fa-tablet" => "fas fa-tablet"),
			array("fas fa-tablet-alt" => "fas fa-tablet-alt"),
			array("fas fa-tachometer-alt" => "fas fa-tachometer-alt"),
			array("fas fa-tag" => "fas fa-tag"),
			array("fas fa-tags" => "fas fa-tags"),
			array("fas fa-tasks" => "fas fa-tasks"),
			array("fas fa-taxi" => "fas fa-taxi"),
			array("fab fa-telegram" => "fab fa-telegram"),
			array("fab fa-telegram-plane" => "fab fa-telegram-plane"),
			array("fab fa-tencent-weibo" => "fab fa-tencent-weibo"),
			array("fas fa-terminal" => "fas fa-terminal"),
			array("fas fa-text-height" => "fas fa-text-height"),
			array("fas fa-text-width" => "fas fa-text-width"),
			array("fas fa-th" => "fas fa-th"),
			array("fas fa-th-large" => "fas fa-th-large"),
			array("fas fa-th-list" => "fas fa-th-list"),
			array("fab fa-themeisle" => "fab fa-themeisle"),
			array("fas fa-thermometer-empty" => "fas fa-thermometer-empty"),
			array("fas fa-thermometer-full" => "fas fa-thermometer-full"),
			array("fas fa-thermometer-half" => "fas fa-thermometer-half"),
			array("fas fa-thermometer-quarter" => "fas fa-thermometer-quarter"),
			array("fas fa-thermometer-three-quarters" => "fas fa-thermometer-three-quarters"),
			array("fas fa-thumbs-down" => "fas fa-thumbs-down"),
			array("far fa-thumbs-down" => "far fa-thumbs-down"),
			array("fas fa-thumbs-up" => "fas fa-thumbs-up"),
			array("far fa-thumbs-up" => "far fa-thumbs-up"),
			array("fas fa-thumbtack" => "fas fa-thumbtack"),
			array("fas fa-ticket-alt" => "fas fa-ticket-alt"),
			array("fas fa-times" => "fas fa-times"),
			array("fas fa-times-circle" => "fas fa-times-circle"),
			array("far fa-times-circle" => "far fa-times-circle"),
			array("fas fa-tint" => "fas fa-tint"),
			array("fas fa-toggle-off" => "fas fa-toggle-off"),
			array("fas fa-toggle-on" => "fas fa-toggle-on"),
			array("fas fa-trademark" => "fas fa-trademark"),
			array("fas fa-train" => "fas fa-train"),
			array("fas fa-transgender" => "fas fa-transgender"),
			array("fas fa-transgender-alt" => "fas fa-transgender-alt"),
			array("fas fa-trash" => "fas fa-trash"),
			array("fas fa-trash-alt" => "fas fa-trash-alt"),
			array("far fa-trash-alt" => "far fa-trash-alt"),
			array("fas fa-tree" => "fas fa-tree"),
			array("fab fa-trello" => "fab fa-trello"),
			array("fab fa-tripadvisor" => "fab fa-tripadvisor"),
			array("fas fa-trophy" => "fas fa-trophy"),
			array("fas fa-truck" => "fas fa-truck"),
			array("fas fa-tty" => "fas fa-tty"),
			array("fab fa-tumblr" => "fab fa-tumblr"),
			array("fab fa-tumblr-square" => "fab fa-tumblr-square"),
			array("fas fa-tv" => "fas fa-tv"),
			array("fab fa-twitch" => "fab fa-twitch"),
			array("fab fa-twitter" => "fab fa-twitter"),
			array("fab fa-twitter-square" => "fab fa-twitter-square"),
			array("fab fa-typo3" => "fab fa-typo3"),
			array("fab fa-uber" => "fab fa-uber"),
			array("fab fa-uikit" => "fab fa-uikit"),
			array("fas fa-umbrella" => "fas fa-umbrella"),
			array("fas fa-underline" => "fas fa-underline"),
			array("fas fa-undo" => "fas fa-undo"),
			array("fas fa-undo-alt" => "fas fa-undo-alt"),
			array("fab fa-uniregistry" => "fab fa-uniregistry"),
			array("fas fa-universal-access" => "fas fa-universal-access"),
			array("fas fa-university" => "fas fa-university"),
			array("fas fa-unlink" => "fas fa-unlink"),
			array("fas fa-unlock" => "fas fa-unlock"),
			array("fas fa-unlock-alt" => "fas fa-unlock-alt"),
			array("fab fa-untappd" => "fab fa-untappd"),
			array("fas fa-upload" => "fas fa-upload"),
			array("fab fa-usb" => "fab fa-usb"),
			array("fas fa-user" => "fas fa-user"),
			array("far fa-user" => "far fa-user"),
			array("fas fa-user-circle" => "fas fa-user-circle"),
			array("far fa-user-circle" => "far fa-user-circle"),
			array("fas fa-user-md" => "fas fa-user-md"),
			array("fas fa-user-plus" => "fas fa-user-plus"),
			array("fas fa-user-secret" => "fas fa-user-secret"),
			array("fas fa-user-times" => "fas fa-user-times"),
			array("fas fa-users" => "fas fa-users"),
			array("fab fa-ussunnah" => "fab fa-ussunnah"),
			array("fas fa-utensil-spoon" => "fas fa-utensil-spoon"),
			array("fas fa-utensils" => "fas fa-utensils"),
			array("fab fa-vaadin" => "fab fa-vaadin"),
			array("fas fa-venus" => "fas fa-venus"),
			array("fas fa-venus-double" => "fas fa-venus-double"),
			array("fas fa-venus-mars" => "fas fa-venus-mars"),
			array("fab fa-viacoin" => "fab fa-viacoin"),
			array("fab fa-viadeo" => "fab fa-viadeo"),
			array("fab fa-viadeo-square" => "fab fa-viadeo-square"),
			array("fab fa-viber" => "fab fa-viber"),
			array("fas fa-video" => "fas fa-video"),
			array("fab fa-vimeo" => "fab fa-vimeo"),
			array("fab fa-vimeo-square" => "fab fa-vimeo-square"),
			array("fab fa-vimeo-v" => "fab fa-vimeo-v"),
			array("fab fa-vine" => "fab fa-vine"),
			array("fab fa-vk" => "fab fa-vk"),
			array("fab fa-vnv" => "fab fa-vnv"),
			array("fas fa-volume-down" => "fas fa-volume-down"),
			array("fas fa-volume-off" => "fas fa-volume-off"),
			array("fas fa-volume-up" => "fas fa-volume-up"),
			array("fab fa-vuejs" => "fab fa-vuejs"),
			array("fab fa-weibo" => "fab fa-weibo"),
			array("fab fa-weixin" => "fab fa-weixin"),
			array("fab fa-whatsapp" => "fab fa-whatsapp"),
			array("fab fa-whatsapp-square" => "fab fa-whatsapp-square"),
			array("fas fa-wheelchair" => "fas fa-wheelchair"),
			array("fab fa-whmcs" => "fab fa-whmcs"),
			array("fas fa-wifi" => "fas fa-wifi"),
			array("fab fa-wikipedia-w" => "fab fa-wikipedia-w"),
			array("fas fa-window-close" => "fas fa-window-close"),
			array("far fa-window-close" => "far fa-window-close"),
			array("fas fa-window-maximize" => "fas fa-window-maximize"),
			array("far fa-window-maximize" => "far fa-window-maximize"),
			array("fas fa-window-minimize" => "fas fa-window-minimize"),
			array("fas fa-window-restore" => "fas fa-window-restore"),
			array("far fa-window-restore" => "far fa-window-restore"),
			array("fab fa-windows" => "fab fa-windows"),
			array("fas fa-won-sign" => "fas fa-won-sign"),
			array("fab fa-wordpress" => "fab fa-wordpress"),
			array("fab fa-wordpress-simple" => "fab fa-wordpress-simple"),
			array("fab fa-wpbeginner" => "fab fa-wpbeginner"),
			array("fab fa-wpexplorer" => "fab fa-wpexplorer"),
			array("fab fa-wpforms" => "fab fa-wpforms"),
			array("fas fa-wrench" => "fas fa-wrench"),
			array("fab fa-xbox" => "fab fa-xbox"),
			array("fab fa-xing" => "fab fa-xing"),
			array("fab fa-xing-square" => "fab fa-xing-square"),
			array("fab fa-y-combinator" => "fab fa-y-combinator"),
			array("fab fa-yahoo" => "fab fa-yahoo"),
			array("fab fa-yandex" => "fab fa-yandex"),
			array("fab fa-yandex-international" => "fab fa-yandex-international"),
			array("fab fa-yelp" => "fab fa-yelp"),
			array("fas fa-yen-sign" => "fas fa-yen-sign"),
			array("fab fa-yoast" => "fab fa-yoast"),
			array("fab fa-youtube" => "fab fa-youtube"),
		)
	);

	return $icons;

}