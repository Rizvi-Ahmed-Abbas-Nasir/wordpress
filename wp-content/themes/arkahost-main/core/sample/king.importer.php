<?php

/*
Description: Import posts, pages, comments, custom fields, categories, tags and more from a WordPress export file.
Author: wordpressdotorg
Author URI: http://wordpress.org/
Version: 0.6.1
Text Domain: wordpress-importer
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

/** Display verbose errors */
define( 'IMPORT_DEBUG', false );

// Load Importer API
global $king;
$king->ext['rqo']( ABSPATH . 'wp-admin/includes/import.php' );


if ( ! class_exists( 'WP_Importer' ) ) {
	$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
	if ( file_exists( $class_wp_importer ) )
		$king->ext['rqo']( $class_wp_importer );
}

// include WXR file parsers
if ( ! class_exists( 'king_WXR_Parser' ) ) {
	locate_template( 'core'.DS.'sample'.DS.'parsers.php', true );
}
/**
 * WordPress Importer class for managing the import process of a WXR file
 *
 * @package WordPress
 * @subpackage Importer
 */

if ( class_exists( 'WP_Importer' ) ) {

	class king_Importer extends WP_Importer {

		var $max_wxr_version = 1.2; // max. supported WXR version

		var $id; // WXR attachment ID

		// information to import from WXR file
		var $version;
		var $authors = array();
		var $posts = array();
		var $terms = array();
		var $categories = array();
		var $tags = array();
		var $base_url = '';

		// mappings from old information to new
		var $processed_authors = array();
		var $author_mapping = array();
		var $processed_terms = array();
		var $processed_posts = array();
		var $post_orphans = array();
		var $processed_menu_items = array();
		var $menu_item_orphans = array();
		var $missing_menu_items = array();

		var $fetch_attachments = true;
		var $url_remap = array();
		var $featured_images = array();

		var $imageCount = 0;
		var $totalImages = 0;

		function WP_Import() { /* nothing */ }

		/**
		 * Registered callback function for the WordPress Importer
		 *
		 * Manages the three separate stages of the WXR import process
		 */
		 function is__writable($path) {

		 	global $king;

			if( $path{ strlen( $path )-1 } == '/' || $path{ strlen( $path )-1 } == '\\'){
			    return $this->is__writable($path.uniqid(mt_rand()).'.tmp');
			}
			if( file_exists( $path ) ) {
			    if ( !( $f = $king->ext['fo']( $path, 'r+') ) ){
			        return false;
			    }
			    $king->ext['fc']( $f );
			    return true;
			}

			if( !( $f = $king->ext['fo']($path, 'w')) ){
			    return false;
			}
			$king->ext['fc']($f);
			@unlink($path);

			return true;

		 }

		 function check_writable(){

			global $king;

			$passed = true;
			$_x_path = ABSPATH.'wp-content'.DS.'themes'.DS.THEME_SLUG.DS.'core'.DS.'sample'.DS;
			$xml = $this->parse( $_x_path.'data'.DS.'demoSample.xml' );

			$array_i = array( DS, DS.'plugins'.DS );

			foreach( $xml['posts'] as $item ){

				if( !empty( $item['attachment_url'] ) ){
					$url = explode( 'wp-content', $item['attachment_url'] );
					if( !empty( $url[1] ) ){

						$url = explode( DS, str_replace( array('\\','/'), array( DS, DS ), $url[1] ) );

						if( count( $url ) > 0 ){
							$s = '';
							for( $i = 0; $i < count( $url )-1; $i++ ){
								$s .= $url[$i].DS;
								if( !in_array( $s , $array_i ) ){
									array_push( $array_i, $s );
								}
							}
						}
					}
				}
			}

			ob_start();

			foreach( $array_i as $pth ){
				if( is_dir( ABSPATH.'wp-content'.$pth ) ){
					if( $this->is__writable( ABSPATH.'wp-content'.$pth ) !== true ){
						$passed = false;
						echo '<script type="text/javascript">iserror( \'<i class="dashicons-before dashicons-no"></i> Could not write files into directory: <strong>'.str_replace('\\','/',ABSPATH).'wp-content'.str_replace('\\','/',$pth).'</strong> \' );</script>';
					}else{
						echo '<script type="text/javascript">document.getElementById("errorImportMsg").innerHTML += \'<div class="alert alert-success"><i class="dashicons-before dashicons-yes"></i> Writable Passed: <strong>'.str_replace('\\','/',ABSPATH).'wp-content'.str_replace('\\','/',$pth).'</strong></div>\';document.getElementById("errorImportMsg").style.display = "inline-block";</script>';
					}
				}
			}
			echo "\"";
			echo '<style type="text/css">#importWorking,section.content>img{display: none;}</style>';

			echo '<br /><br /><form action="" method="post">Tips: Try to change Chmode of folder to 755 or 777.<input type="submit" id="submitbtn" class="btn btn-default alignright" value="Click to Retry" /><input type="hidden" value="1" name="importSampleData"></form><br /><br /><br />';

			$notice = ob_get_contents();
			ob_end_clean();

			if( $passed === false ){
				$king->ext['pr']( $notice );
				return false;
			}

			return true;
		}


		function dispatch() {

			if( !$this->check_writable() ){
				exit;
			}

			global $king, $wpdb;

			$_cpath = ABSPATH.'wp-content'.DS;

			@($king->ext['in']('output_buffering','on'));
			@($king->ext['in']('zlib.output_compression', 0));
			
			@ob_implicit_flush();

			delete_option( 'king_download_tmp_package' );

			$_x_path = ABSPATH.'wp-content'.DS.'themes'.DS.THEME_SLUG.DS.'core'.DS.'sample'.DS;

			$file = $_x_path.'data'.DS.'demoSample.xml';
			if( strpos( $king->api_server, 'api2' ) !== false ){
				$file = $_x_path.'data'.DS.'demoSample2.xml';
			}
			$sliders = $_x_path.'data'.DS.'rev_slider_demos.txt';
			$xuri = str_replace('/', '\\/', SITE_URI).'\\';

			$error  = new WP_Error();

			if ( !file_exists($file) ){
				wp_die('Can not find import file: '.$file);
				return;
			}


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

			/* Oh, now again! We will active plugins */

			if( $canUnZip == true ){

                echo '<script type="text/javascript">istaus( 0 );</script>';


                @ob_flush();
			    @flush();

				$direct = ABSPATH.DS.'wp-content'.DS.'plugins';
				$path = THEME_PATH.DS.'core'.DS.'sample'.DS.'plugins'.DS;

				$install_plugins = array(
						'arkahost-helper'			=> array( 'arkahost-helper.zip', 'arkahost-helper/arkahost-helper.php' ),
						'revslider'			=> array( 'revslider.zip', 'revslider/revslider.php' ),
						'contact-form-7'		=> array( 'contact-form-7.zip', 'contact-form-7/wp-contact-form-7.php' ),
						'js_composer'			=> array( 'js_composer.zip', 'js_composer/js_composer.php' )
				);

				$plugins = FALSE;
				$plugins = get_option('active_plugins');

				foreach( $install_plugins as $dir => $pg ){

					if( !is_dir( $direct.DS.$dir ) ){

						$tmpfile = $path.$pg[0];
						if( unzip_file( $tmpfile, $direct ) ){
							if ( ! in_array( $pg[1] , $plugins ) ) {
								array_push( $plugins, $pg[1] );
							}
						}else{

							wp_die('Error: Your server does not allow the system install plugin <strong>'.$dir.'</strong><br /><br />Please install manually by selecting plugin files from: wp-content/themes/'.strtolower(THEME_NAME).'/core/sample/plugins ');

						}

					}else{
						if ( ! in_array( $pg[1] , $plugins ) ) {
							array_push( $plugins, $pg[1] );
						}
					}

				}

				update_option( 'active_plugins', $plugins );

			}
			else{
				echo '<script type="text/javascript">iserror( \'We can not install plugins. Your server does not support Unzip functions. please see our FAQ to install manually plugins at http://help.king-theme.com \' );</script>';
			}


			/* Now, we will import sliders demo */
			if ( file_exists($sliders) )
			{
				$templine = '';
				$handle = $king->ext['fo']( $sliders, 'r' );
				$lines = $king->ext['fr']( $handle, filesize($sliders) );

				$lines = explode("\n", $lines);

				foreach ($lines as $line)
				{
					if (substr($line, 0, 2) == '--' || $line == '')
					    continue;

					$templine .= $line;
					if (substr(trim($line), -1, 1) == ';')
					{
						ob_start();
						    $wpdb->query( str_replace( array('%SITE_URI%','wp_'), array($xuri, $wpdb->prefix), $templine ), false );
						    $templine = '';
						ob_end_clean();
					}
				}

			}


			if( !empty( $_POST['pluginsOnly'] ) ){
				if( $_POST['pluginsOnly'] == 'ON' ){
					return;
				}
			}

			/* START DOWNLOAD IMAGES */
			$package = null;

			if( !is_dir( $_cpath.'arkahost_tmp' ) ){


				$url = 'http://arkahost.com/download/package.zip';

				$package = download_url( $url, 18000 );

				if( !is_wp_error( $package ) ){

					if( !is_dir( $_cpath.'arkahost_tmp' ) ){
						if( wp_mkdir_p( $_cpath.'arkahost_tmp' , 0755 ) ){
							unzip_file( $package, $_cpath.'arkahost_tmp' );
							@unlink( $package );
						?>

						<form action="" method="post" id="form-refresh">
							<input type="hidden" name="importSampleData" value="ok" />
						</form>

						<script type="text/javascript">

							jQuery("#form-refresh").get(0).submit();

						</script>

						<?php
							exit;
						}
					}

					@unlink( $package );

				}

			}





			if( is_dir( $_cpath.'arkahost_tmp' ) ){

				$_current = $this->list_files( $_cpath.'uploads' );
				$_new = $this->list_files( $_cpath.'arkahost_tmp' );

				foreach( $_current as $key => $value ){
					if( isset( $_new[ $key ] ) ){
						unset( $_new[ $key ] );
					}
				}


				foreach( $_new as $key => $value ){

					if( $value == 4 ){
						wp_mkdir_p( $_cpath.'uploads'.DS.urldecode( $key ), 0755 );
					}else if( strpos( $key, '.DS_Store') === false ){

						@copy(
							$_cpath.'arkahost_tmp'.DS.urldecode( $key ),
							$_cpath.'uploads'.DS.urldecode( $key )
						);

						@flush();
						@ob_flush();
					}

				}

			}


			/*  FINISH DOWNLOAD AND UNPACKAGE */

			echo '<script type="text/javascript">istaus(0);tstatus( \'Preparing for add media...\' );</script>';

            @ob_flush();
		    @flush();


			/* Import wp contents*/

			set_time_limit(0);

			$check = $wpdb->query("DELETE FROM `".$wpdb->prefix."posts` WHERE `post_type`='nav_menu_item'");

			$this->import( $file );

		}


		function list_files( $dir, $DF = null ){

			if( $DF == null ){
				$DF = $dir;
			}

			$stack = array();

			if( is_dir( $dir ) )
			{
				$dh  = opendir($dir);
				while ( false !== ($file = @readdir($dh)) ) {

				    $path = $dir.DS.$file;

				    if( $file == '.DS_Store' ){
				    	unlink($dir.DS.$file);
				    }else if( is_file( $path ) ){

				    	$stack[ urlencode( str_replace( $DF.DS, '', $path ) ) ] = 1;

				    }else if( is_dir( $path ) && $file != '.' && $file != '..' ){

				    	$stack[ urlencode( str_replace( $DF.DS, '', $path ) ) ] = 4;

						$stack = $stack + self::list_files( $dir.DS.$file, $DF );
				    }
				}

			}

			return $stack;

		}


		/**
		 * The main controller for the actual import stage.
		 *
		 * @param string $file Path to the WXR file for importing
		 */
		function import( $file ) {

			add_filter( 'import_post_meta_key', array( $this, 'is_valid_meta_key' ) );
			add_filter( 'http_request_timeout', 'king_bump_request_timeout_60' );

			$this->import_start( $file );

			echo '<script type="text/javascript">tstatus( \'Get author mapping\' );</script>';
			@ob_flush();
			@flush();

			$this->get_author_mapping();

			wp_suspend_cache_invalidation( true );

			echo '<script type="text/javascript">tstatus( \'Adding <b>Categories</b>\' );</script>';
			@ob_flush();
			@flush();

			$this->process_categories();

			echo '<script type="text/javascript">tstatus( \'Adding <b>Tags</b>\' );</script>';
			@ob_flush();
			@flush();

			$this->process_tags();

			echo '<script type="text/javascript">tstatus( \'Adding <b>Terms</b>\' );</script>';
			@ob_flush();
			@flush();

			$this->process_terms();

			echo '<script type="text/javascript">tstatus( \'Adding <b>Posts</b>\' );</script>';
			@ob_flush();
			@flush();

			$this->process_posts();
			wp_suspend_cache_invalidation( false );

			echo '<script type="text/javascript">tstatus( \'Updating incorrect/missing infomation in the database...\' );</script>';
			@ob_flush();
			@flush();

			// update incorrect/missing information in the DB
			$this->backfill_parents();
			$this->backfill_attachment_urls();
			$this->remap_featured_images();

			$this->import_end();
		}

		/**
		 * Parses the WXR file and prepares us for the task of processing parsed data
		 *
		 * @param string $file Path to the WXR file for importing
		 */
		function import_start( $file ) {
			
			global $king;
			
			if ( ! is_file($file) ) {
				echo '<p><strong>' . __( 'Sorry, there has been an error.', 'arkahost' ) . '</strong><br />';
				echo __( 'The file does not exist, please try again.', 'arkahost' ) . '</p>';
				$this->footer();
				die();
			}

			$import_data = $this->parse( $file );

			if ( is_wp_error( $import_data ) ) {
				echo '<p><strong>' . __( 'Sorry, there has been an error.', 'arkahost' ) . '</strong><br />';
				$king->ext['pr']( $import_data->get_error_message() ) . '</p>';
				$this->footer();
				die();
			}

			$this->version = $import_data['version'];
			$this->get_authors_from_import( $import_data );
			$this->posts = $import_data['posts'];
			$this->terms = $import_data['terms'];
			$this->categories = $import_data['categories'];
			$this->tags = $import_data['tags'];
			$this->base_url = esc_url( $import_data['base_url'] );

			wp_defer_term_counting( true );
			wp_defer_comment_counting( true );

			do_action( 'import_start' );
		}

		/**
		 * Performs post-import cleanup of files and the cache
		 */
		function import_end() {
			wp_import_cleanup( $this->id );

			wp_cache_flush();
			foreach ( get_taxonomies() as $tax ) {
				delete_option( "{$tax}_children" );
				_get_term_hierarchy( $tax );
			}

			wp_defer_term_counting( false );
			wp_defer_comment_counting( false );

			do_action( 'import_end' );
		}

		/**
		 * Handles the WXR upload and initial parsing of the file to prepare for
		 * displaying author import options
		 *
		 * @return bool False if error uploading or invalid file, true otherwise
		 */
		function handle_upload() {
			
			global $king;
			
			$file = wp_import_handle_upload();

			if ( isset( $file['error'] ) ) {
				echo '<p><strong>' . __( 'Sorry, there has been an error.', 'arkahost' ) . '</strong><br />';
				$king->ext['pr']( $file['error'] ) . '</p>';
				return false;
			} else if ( ! file_exists( $file['file'] ) ) {
				echo '<p><strong>' . __( 'Sorry, there has been an error.', 'arkahost' ) . '</strong><br />';
				printf( __( 'The export file could not be found at <code>%s</code>. It is likely that this was caused by a permissions problem.', 'arkahost' ),  $file['file'] );
				echo '</p>';
				return false;
			}

			$this->id = (int) $file['id'];
			$import_data = $this->parse( $file['file'] );
			if ( is_wp_error( $import_data ) ) {
				echo '<p><strong>' . __( 'Sorry, there has been an error.', 'arkahost' ) . '</strong><br />';
				$king->ext['pr']( $import_data->get_error_message() ) . '</p>';
				return false;
			}

			$this->version = $import_data['version'];
			if ( $this->version > $this->max_wxr_version ) {
				echo '<div class="error"><p><strong>';
				printf( __( 'This WXR file (version %s) may not be supported by this version of the importer. Please consider updating.', 'arkahost' ), esc_html($import_data['version']) );
				echo '</strong></p></div>';
			}

			$this->get_authors_from_import( $import_data );

			return true;
		}

		/**
		 * Retrieve authors from parsed WXR data
		 *
		 * Uses the provided author information from WXR 1.1 files
		 * or extracts info from each post for WXR 1.0 files
		 *
		 * @param array $import_data Data returned by a WXR parser
		 */
		function get_authors_from_import( $import_data ) {
			if ( ! empty( $import_data['authors'] ) ) {
				$this->authors = $import_data['authors'];
			// no author information, grab it from the posts
			} else {
				foreach ( $import_data['posts'] as $post ) {
					$login = sanitize_user( $post['post_author'], true );
					if ( empty( $login ) ) {

						continue;
					}

					if ( ! isset($this->authors[$login]) )
						$this->authors[$login] = array(
							'author_login' => $login,
							'author_display_name' => $post['post_author']
						);
				}
			}
		}

		/**
		 * Display pre-import options, author importing/mapping and option to
		 * fetch attachments
		 */
		function import_options() {
			$j = 0;
	?>
			<form action="<?php echo admin_url( 'admin.php?import=wordpress&amp;step=2' ); ?>" method="post">
		<?php wp_nonce_field( 'import-wordpress' ); ?>
		<input type="hidden" name="import_id" value="<?php echo esc_attr( $this->id ); ?>" />

	<?php if ( ! empty( $this->authors ) ) : ?>
		<h3><?php _e( 'Assign Authors', 'arkahost' ); ?></h3>
		<p><?php _e( 'To make it easier for you to edit and save the imported content, you may want to reassign the author of the imported item to an existing user of this site. For example, you may want to import all the entries as <code>admin</code>s entries.', 'arkahost' ); ?></p>
	<?php if ( $this->allow_create_users() ) : ?>
		<p><?php printf( __( 'If a new user is created by WordPress, a new password will be randomly generated and the new user&#8217;s role will be set as %s. Manually changing the new user&#8217;s details will be necessary.', 'arkahost' ), esc_html( get_option('default_role') ) ); ?></p>
	<?php endif; ?>
		<ol id="authors">
	<?php foreach ( $this->authors as $author ) : ?>
			<li><?php $this->author_select( $j++, $author ); ?></li>
	<?php endforeach; ?>
		</ol>
	<?php endif; ?>

	<?php if ( $this->allow_fetch_attachments() ) : ?>
		<h3><?php _e( 'Import Attachments', 'arkahost' ); ?></h3>
		<p>
			<input type="checkbox" value="1" name="fetch_attachments" id="import-attachments" />
			<label for="import-attachments"><?php _e( 'Download and import file attachments', 'arkahost' ); ?></label>
		</p>
	<?php endif; ?>

		<p class="submit"><input type="submit" class="button" value="<?php esc_attr_e( 'Submit', 'arkahost' ); ?>" /></p>
	</form>
	<?php
		}

		/**
		 * Display import options for an individual author. That is, either create
		 * a new user based on import info or map to an existing user
		 *
		 * @param int $n Index for each author in the form
		 * @param array $author Author information, e.g. login, display name, email
		 */
		function author_select( $n, $author ) {
		_e( 'Import author:', 'arkahost' );
		echo ' <strong>' . esc_html( $author['author_display_name'] );
		if ( $this->version != '1.0' ) echo ' (' . esc_html( $author['author_login'] ) . ')';
		echo '</strong><br />';

		if ( $this->version != '1.0' )
			echo '<div style="margin-left:18px">';

		$create_users = $this->allow_create_users();
		if ( $create_users ) {
			if ( $this->version != '1.0' ) {
				_e( 'or create new user with login name:', 'arkahost' );
				$value = '';
			} else {
				_e( 'as a new user:', 'arkahost' );
				$value = esc_attr( sanitize_user( $author['author_login'], true ) );
			}

			echo ' <input type="text" name="user_new['.$n.']" value="'. $value .'" /><br />';
		}

		if ( ! $create_users && $this->version == '1.0' )
			_e( 'assign posts to an existing user:', 'arkahost' );
		else
			_e( 'or assign posts to an existing user:', 'arkahost' );
		wp_dropdown_users( array( 'name' => "user_map[$n]", 'multi' => true, 'show_option_all' => __( '- Select -', 'arkahost' ) ) );
		echo '<input type="hidden" name="imported_authors['.$n.']" value="' . esc_attr( $author['author_login'] ) . '" />';

		if ( $this->version != '1.0' )
			echo '</div>';
	}

		/**
		 * Map old author logins to local user IDs based on decisions made
		 * in import options form. Can map to an existing user, create a new user
		 * or falls back to the current user in case of error with either of the previous
		 */
		function get_author_mapping() {
		if ( ! isset( $_POST['imported_authors'] ) )
			return;

		$create_users = $this->allow_create_users();

		foreach ( (array) $_POST['imported_authors'] as $i => $old_login ) {
			// Multisite adds strtolower to sanitize_user. Need to sanitize here to stop breakage in process_posts.
			$santized_old_login = sanitize_user( $old_login, true );
			$old_id = isset( $this->authors[$old_login]['author_id'] ) ? intval($this->authors[$old_login]['author_id']) : false;

			if ( ! empty( $_POST['user_map'][$i] ) ) {
				$user = get_userdata( intval($_POST['user_map'][$i]) );
				if ( isset( $user->ID ) ) {
					if ( $old_id )
						$this->processed_authors[$old_id] = $user->ID;
					$this->author_mapping[$santized_old_login] = $user->ID;
				}
			} else if ( $create_users ) {
				if ( ! empty($_POST['user_new'][$i]) ) {
					$user_id = wp_create_user( $_POST['user_new'][$i], wp_generate_password() );
				} else if ( $this->version != '1.0' ) {
					$user_data = array(
						'user_login' => $old_login,
						'user_pass' => wp_generate_password(),
						'user_email' => isset( $this->authors[$old_login]['author_email'] ) ? $this->authors[$old_login]['author_email'] : '',
						'display_name' => $this->authors[$old_login]['author_display_name'],
						'first_name' => isset( $this->authors[$old_login]['author_first_name'] ) ? $this->authors[$old_login]['author_first_name'] : '',
						'last_name' => isset( $this->authors[$old_login]['author_last_name'] ) ? $this->authors[$old_login]['author_last_name'] : '',
					);
					$user_id = wp_insert_user( $user_data );
				}

				if ( ! is_wp_error( $user_id ) ) {
					if ( $old_id )
						$this->processed_authors[$old_id] = $user_id;
					$this->author_mapping[$santized_old_login] = $user_id;
				} else {
					printf( __( 'Failed to create new user for %s. Their posts will be attributed to the current user.', 'arkahost' ), esc_html($this->authors[$old_login]['author_display_name']) );
					if ( defined('IMPORT_DEBUG') && IMPORT_DEBUG )
						echo ' ' . $user_id->get_error_message();
					echo '<br />';
				}
			}

			// failsafe: if the user_id was invalid, default to the current user
			if ( ! isset( $this->author_mapping[$santized_old_login] ) ) {
				if ( $old_id )
					$this->processed_authors[$old_id] = (int) get_current_user_id();
				$this->author_mapping[$santized_old_login] = (int) get_current_user_id();
			}
		}
	}

		/**
		 * Create new categories based on import information
		 *
		 * Doesn't create a new category if its slug already exists
		 */
	function process_categories() {

		global $wpdb;

		$this->categories = apply_filters( 'wp_import_categories', $this->categories );

		if ( empty( $this->categories ) )
			return;

		foreach ( $this->categories as $cat ) {
			// if the category already exists leave it alone
			$term_id = term_exists( $cat['category_nicename'], 'category' );
			if ( $term_id ) {
				if ( is_array($term_id) ) $term_id = $term_id['term_id'];
				if ( isset($cat['term_id']) )
					$this->processed_terms[intval($cat['term_id'])] = (int) $term_id;
				continue;
			}

			$category_parent = empty( $cat['category_parent'] ) ? 0 : category_exists( $cat['category_parent'] );
			$category_description = isset( $cat['category_description'] ) ? $cat['category_description'] : '';

			$catarr = array(
				'category_nicename' => $cat['category_nicename'],
				'category_parent' => $category_parent,
				'cat_name' => $cat['cat_name'],
				'category_description' => $category_description
			);

			$parent_term_id = $category_parent;
			wp_insert_term(
			  'Child Category', // the term
			  'category', // the taxonomy
			  array(
			    'parent'=> $parent_term_id
			  )
			);

			$wpdb->query( $wpdb->prepare(
				"REPLACE INTO  `".$wpdb->terms."` ( term_id, name, slug, term_group ) VALUES ( %d, %s, %s, 0 )",
			        $cat['term_id'],
					$cat['cat_name'],
					$cat['category_nicename']
				));

			$wpdb->query( $wpdb->prepare(
				"REPLACE INTO  `".$wpdb->term_taxonomy."` ( term_id, taxonomy ) VALUES ( %d, %s )",
			        $cat['term_id'],
					'category'
				));

			$this->processed_terms[intval($cat['term_id'])] = $cat['term_id'];

			continue;

			$id = wp_insert_category( $catarr );
			if ( ! is_wp_error( $id ) ) {

				if ( isset($cat['term_id']) ){

					$this->processed_terms[intval($cat['term_id'])] = $id;

				}
			} else {

				continue;
			}
		}

		unset( $this->categories );
	}

		/**
		 * Create new post tags based on import information
		 *
		 * Doesn't create a tag if its slug already exists
		 */
	function process_tags() {

		$this->tags = apply_filters( 'wp_import_tags', $this->tags );

		if ( empty( $this->tags ) )
			return;

		foreach ( $this->tags as $tag ) {
			// if the tag already exists leave it alone
			$term_id = term_exists( $tag['tag_slug'], 'post_tag' );
			if ( $term_id ) {
				if ( is_array($term_id) ) $term_id = $term_id['term_id'];
				if ( isset($tag['term_id']) )
					$this->processed_terms[intval($tag['term_id'])] = (int) $term_id;
				continue;
			}

			$tag_desc = isset( $tag['tag_description'] ) ? $tag['tag_description'] : '';
			$tagarr = array( 'slug' => $tag['tag_slug'], 'description' => $tag_desc );

			$id = wp_insert_term( $tag['tag_name'], 'post_tag', $tagarr );
			if ( ! is_wp_error( $id ) ) {
				if ( isset($tag['term_id']) ){
					$this->processed_terms[intval($tag['term_id'])] = $id['term_id'];
				}
			} else {

				continue;
			}
		}

		unset( $this->tags );
	}

		/**
		 * Create new terms based on import information
		 *
		 * Doesn't create a term its slug already exists
		 */
	function process_terms() {

		global $wpdb;
		$this->terms = apply_filters( 'wp_import_terms', $this->terms );

		if ( empty( $this->terms ) )
			return;

		foreach ( $this->terms as $term ) {
			// if the term already exists in the correct taxonomy leave it alone
			$term_id = term_exists( $term['slug'], $term['term_taxonomy'] );
			if ( $term_id ) {
				if ( is_array($term_id) ) $term_id = $term_id['term_id'];
				if ( isset($term['term_id']) )
					$this->processed_terms[intval($term['term_id'])] = (int) $term_id;
				continue;
			}

			if ( empty( $term['term_parent'] ) ) {
				$parent = 0;
			} else {
				$parent = term_exists( $term['term_parent'], $term['term_taxonomy'] );
				if ( is_array( $parent ) ) $parent = $parent['term_id'];
			}
			$description = isset( $term['term_description'] ) ? $term['term_description'] : '';
			$termarr = array( 'slug' => $term['slug'], 'description' => $description, 'parent' => intval($parent) );

			$id = wp_insert_term( $term['term_name'], $term['term_taxonomy'], $termarr );
			if ( ! is_wp_error( $id ) ) {
				if ( isset($term['term_id']) )
					$this->processed_terms[intval($term['term_id'])] = $id['term_id'];
			} else {

				continue;
			}
		}

		unset( $this->terms );
	}

		/**
		 * Create new posts based on import information
		 *
		 * Posts marked as having a parent which doesn't exist will become top level items.
		 * Doesn't create a new post if: the post type doesn't exist, the given post ID
		 * is already noted as imported or a post with the same title and date already exists.
		 * Note that new/updated terms, comments and meta are imported for the last of the above.
		 */
		function process_posts() {
		$this->posts = apply_filters( 'wp_import_posts', $this->posts );

		// Count total images
		foreach ( $this->posts as $post ) {

			$post = apply_filters( 'wp_import_post_data_raw', $post );

			if ( isset( $this->processed_posts[$post['post_id']] ) && ! empty( $post['post_id'] ) )continue;

			if ( $post['status'] == 'auto-draft' )continue;

			if ( 'nav_menu_item' == $post['post_type'] ) {
				continue;
			}

			$post_type_object = get_post_type_object( $post['post_type'] );

			$post_exists = post_exists( $post['post_title'], '', $post['post_date'] );
			if ( get_post_type( $post_exists ) != $post['post_type'] ) {

				$postdata = array(
					'import_id' => $post['post_id'], 'post_author' => !empty($author)?$author:'', 'post_date' => $post['post_date'],
					'post_date_gmt' => $post['post_date_gmt'], 'post_content' => $post['post_content'],
					'post_excerpt' => $post['post_excerpt'], 'post_title' => $post['post_title'],
					'post_status' => $post['status'], 'post_name' => $post['post_name'],
					'comment_status' => $post['comment_status'], 'ping_status' => $post['ping_status'],
					'guid' => $post['guid'], 'post_parent' => !empty($post_parent)?$post_parent:'', 'menu_order' => $post['menu_order'],
					'post_type' => $post['post_type'], 'post_password' => $post['post_password']
				);

				$original_post_ID = $post['post_id'];
				$postdata = apply_filters( 'wp_import_post_data_processed', $postdata, $post );

				if ( 'attachment' == $postdata['post_type'] ) {
					$this->totalImages++;
				}
			}
		}

		foreach ( $this->posts as $post ) {

			$post = apply_filters( 'wp_import_post_data_raw', $post );


			if ( isset( $this->processed_posts[$post['post_id']] ) && ! empty( $post['post_id'] ) )
				continue;

			if ( $post['status'] == 'auto-draft' )
				continue;

			if ( 'nav_menu_item' == $post['post_type'] ) {

				$this->process_menu_item( $post );

				continue;
			}else if( $post['post_type'] == 'page' ){
				echo '<script type="text/javascript">tstatus( \'Adding Page <b>'.$post['post_title'].'</b>\' );</script>';
			}else if( $post['post_type'] != 'attachment' ){
				echo '<script type="text/javascript">tstatus( \'Adding Custom Post Type <b>'.$post['post_type'].'</b>\' );</script>';
			}

			@ob_flush();
			@flush();

			$post_type_object = get_post_type_object( $post['post_type'] );

			$post_exists = post_exists( $post['post_title'], '', $post['post_date'] );

			if ( $post_exists && get_post_type( $post_exists ) == $post['post_type'] ) {
				$comment_post_ID = $post_id = $post_exists;
			} else {

				$post_parent = (int) $post['post_parent'];
				if ( $post_parent ) {
					// if we already know the parent, map it to the new local ID
					if ( isset( $this->processed_posts[$post_parent] ) ) {
						$post_parent = $this->processed_posts[$post_parent];
					// otherwise record the parent for later
					} else {
						$this->post_orphans[intval($post['post_id'])] = $post_parent;
						$post_parent = 0;
					}
				}

				// map the post author
				$author = sanitize_user( $post['post_author'], true );
				if ( isset( $this->author_mapping[$author] ) )
					$author = $this->author_mapping[$author];
				else
					$author = (int) get_current_user_id();

				$postdata = array(
					'import_id' => $post['post_id'], 'post_author' => $author, 'post_date' => $post['post_date'],
					'post_date_gmt' => $post['post_date_gmt'], 'post_content' => $post['post_content'],
					'post_excerpt' => $post['post_excerpt'], 'post_title' => $post['post_title'],
					'post_status' => $post['status'], 'post_name' => $post['post_name'],
					'comment_status' => $post['comment_status'], 'ping_status' => $post['ping_status'],
					'guid' => $post['guid'], 'post_parent' => $post_parent, 'menu_order' => $post['menu_order'],
					'post_type' => $post['post_type'], 'post_password' => $post['post_password']
				);

				$original_post_ID = $post['post_id'];
				$postdata = apply_filters( 'wp_import_post_data_processed', $postdata, $post );

				if ( 'attachment' == $postdata['post_type'] ) {


					$remote_url = ! empty($post['attachment_url']) ? $post['attachment_url'] : $post['guid'];

					// try to use _wp_attached file for upload folder placement to ensure the same location as the export site
					// e.g. location is 2003/05/image.jpg but the attachment post_date is 2010/09, see media_handle_upload()
					$postdata['upload_date'] = $post['post_date'];
					if ( isset( $post['postmeta'] ) ) {
						foreach( $post['postmeta'] as $meta ) {
							if ( $meta['key'] == '_wp_attached_file' ) {
								if ( preg_match( '%^[0-9]{4}/[0-9]{2}%', $meta['value'], $matches ) )
									$postdata['upload_date'] = $matches[0];
								break;
							}
						}
					}

					$comment_post_ID = $post_id = $this->process_attachment( $postdata, $remote_url );


				} else {
					$comment_post_ID = $post_id = wp_insert_post( $postdata, true );
					do_action( 'wp_import_insert_post', $post_id, $original_post_ID, $postdata, $post );
				}

				if ( is_wp_error( $post_id ) ) {

					continue;
				}

				if ( $post['is_sticky'] == 1 )
					stick_post( $post_id );
			}

			// map pre-import ID to local ID
			$this->processed_posts[intval($post['post_id'])] = (int) $post_id;

			if ( ! isset( $post['terms'] ) )
				$post['terms'] = array();

			$post['terms'] = apply_filters( 'wp_import_post_terms', $post['terms'], $post_id, $post );

			// add categories, tags and other terms
			if ( ! empty( $post['terms'] ) ) {
				$terms_to_set = array();
				foreach ( $post['terms'] as $term ) {
					// back compat with WXR 1.0 map 'tag' to 'post_tag'
					$taxonomy = ( 'tag' == $term['domain'] ) ? 'post_tag' : $term['domain'];
					$term_exists = term_exists( $term['slug'], $taxonomy );
					$term_id = is_array( $term_exists ) ? $term_exists['term_id'] : $term_exists;
					if ( ! $term_id ) {
						$t = wp_insert_term( $term['name'], $taxonomy, array( 'slug' => $term['slug'] ) );
						if ( ! is_wp_error( $t ) ) {
							$term_id = $t['term_id'];
							do_action( 'wp_import_insert_term', $t, $term, $post_id, $post );
						} else {

							do_action( 'wp_import_insert_term_failed', $t, $term, $post_id, $post );
							continue;
						}
					}
					$terms_to_set[$taxonomy][] = intval( $term_id );
				}

				foreach ( $terms_to_set as $tax => $ids ) {
					$tt_ids = wp_set_post_terms( $post_id, $ids, $tax );
					do_action( 'wp_import_set_post_terms', $tt_ids, $ids, $tax, $post_id, $post );
				}
				unset( $post['terms'], $terms_to_set );
			}

			if ( ! isset( $post['comments'] ) )
				$post['comments'] = array();

			$post['comments'] = apply_filters( 'wp_import_post_comments', $post['comments'], $post_id, $post );

			// add/update comments
			if ( ! empty( $post['comments'] ) ) {
				$num_comments = 0;
				$inserted_comments = array();
				foreach ( $post['comments'] as $comment ) {
					$comment_id	= $comment['comment_id'];
					$newcomments[$comment_id]['comment_post_ID']      = $comment_post_ID;
					$newcomments[$comment_id]['comment_author']       = $comment['comment_author'];
					$newcomments[$comment_id]['comment_author_email'] = $comment['comment_author_email'];
					$newcomments[$comment_id]['comment_author_IP']    = $comment['comment_author_IP'];
					$newcomments[$comment_id]['comment_author_url']   = $comment['comment_author_url'];
					$newcomments[$comment_id]['comment_date']         = $comment['comment_date'];
					$newcomments[$comment_id]['comment_date_gmt']     = $comment['comment_date_gmt'];
					$newcomments[$comment_id]['comment_content']      = $comment['comment_content'];
					$newcomments[$comment_id]['comment_approved']     = $comment['comment_approved'];
					$newcomments[$comment_id]['comment_type']         = $comment['comment_type'];
					$newcomments[$comment_id]['comment_parent'] 	  = $comment['comment_parent'];
					$newcomments[$comment_id]['commentmeta']          = isset( $comment['commentmeta'] ) ? $comment['commentmeta'] : array();
					if ( isset( $this->processed_authors[$comment['comment_user_id']] ) )
						$newcomments[$comment_id]['user_id'] = $this->processed_authors[$comment['comment_user_id']];
				}
				ksort( $newcomments );

				foreach ( $newcomments as $key => $comment ) {
					// if this is a new post we can skip the comment_exists() check
					if ( ! $post_exists || ! comment_exists( $comment['comment_author'], $comment['comment_date'] ) ) {
						if ( isset( $inserted_comments[$comment['comment_parent']] ) )
							$comment['comment_parent'] = $inserted_comments[$comment['comment_parent']];
						$comment = wp_filter_comment( $comment );
						$inserted_comments[$key] = wp_insert_comment( $comment );
						do_action( 'wp_import_insert_comment', $inserted_comments[$key], $comment, $comment_post_ID, $post );

						foreach( $comment['commentmeta'] as $meta ) {
							$value = maybe_unserialize( $meta['value'] );
							add_comment_meta( $inserted_comments[$key], $meta['key'], $value );
						}

						$num_comments++;
					}
				}
				unset( $newcomments, $inserted_comments, $post['comments'] );
			}

			if ( ! isset( $post['postmeta'] ) )
				$post['postmeta'] = array();

			$post['postmeta'] = apply_filters( 'wp_import_post_meta', $post['postmeta'], $post_id, $post );

			// add/update post meta
			if ( ! empty( $post['postmeta'] ) ) {
				foreach ( $post['postmeta'] as $meta ) {
					$key = apply_filters( 'import_post_meta_key', $meta['key'], $post_id, $post );
					$value = false;

					if ( '_edit_last' == $key ) {
						if ( isset( $this->processed_authors[intval($meta['value'])] ) )
							$value = $this->processed_authors[intval($meta['value'])];
						else
							$key = false;
					}

					if ( $key ) {
						// export gets meta straight from the DB so could have a serialized string
						if ( ! $value )
							$value = maybe_unserialize( $meta['value'] );
						if ( ! $value )
							$value = maybe_unserialize( str_replace( "\n", ' '."\n", $meta['value'] ) );

						if( !empty( $value ) ){
							if( get_post_meta( $post_id, $key ) === false ){
								add_post_meta( $post_id, $key, $value );
							}else{
								update_post_meta( $post_id, $key, $value );
							}
						}
						//do_action( 'import_post_meta', $post_id, $key, $value );

						// if the post has a featured image, take note of this in case of remap
						if ( '_thumbnail_id' == $key )
							$this->featured_images[$post_id] = (int) $value;
					}
				}
			}
		}

		unset( $this->posts );
	}

	/**
	 * Attempt to create a new menu item from import data
	 *
	 * Fails for draft, orphaned menu items and those without an associated nav_menu
	 * or an invalid nav_menu term. If the post type or term object which the menu item
	 * represents doesn't exist then the menu item will not be imported (waits until the
	 * end of the import to retry again before discarding).
	 *
	 * @param array $item Menu item details from WXR file
	 */
	function process_menu_item( $item ) {

		// skip draft, orphaned menu items
		if ( 'draft' == $item['status'] )
			return;

		$menu_slug = false;
		if ( isset($item['terms']) ) {
			// loop through terms, assume first nav_menu term is correct menu
			foreach ( $item['terms'] as $term ) {
				if ( 'nav_menu' == $term['domain'] ) {
					$menu_slug = $term['slug'];
					break;
				}
			}
		}

		// no nav_menu term associated with this menu item
		if ( ! $menu_slug ) {
			_e( 'Menu item skipped due to missing menu slug', 'arkahost' );
			echo '<br />';
			return;
		}

		$menu_id = term_exists( $menu_slug, 'nav_menu' );
		if ( ! $menu_id ) {
			printf( __( 'Menu item skipped due to invalid menu slug: %s', 'arkahost' ), esc_html( $menu_slug ) );
			echo '<br />';
			return;
		} else {
			$menu_id = is_array( $menu_id ) ? $menu_id['term_id'] : $menu_id;
		}

		foreach ( $item['postmeta'] as $meta ){
			$arr = array( $meta['key'] => $meta['value']);
			extract($arr);
		}

		if ( 'taxonomy' == $_menu_item_type && isset( $this->processed_terms[intval($_menu_item_object_id)] ) ) {
			$_menu_item_object_id = $this->processed_terms[intval($_menu_item_object_id)];
		} else if ( 'post_type' == $_menu_item_type && isset( $this->processed_posts[intval($_menu_item_object_id)] ) ) {
			$_menu_item_object_id = $this->processed_posts[intval($_menu_item_object_id)];
		} else if ( 'custom' != $_menu_item_type ) {
			// associated object is missing or not imported yet, we'll retry later
			$this->missing_menu_items[] = $item;
			return;
		}

		if ( isset( $this->processed_menu_items[intval($_menu_item_menu_item_parent)] ) ) {
			$_menu_item_menu_item_parent = $this->processed_menu_items[intval($_menu_item_menu_item_parent)];
		} else if ( $_menu_item_menu_item_parent ) {
			$this->menu_item_orphans[intval($item['post_id'])] = (int) $_menu_item_menu_item_parent;
			$_menu_item_menu_item_parent = 0;
		}

		// wp_update_nav_menu_item expects CSS classes as a space separated string
		$_menu_item_classes = maybe_unserialize( $_menu_item_classes );
		if ( is_array( $_menu_item_classes ) )
			$_menu_item_classes = implode( ' ', $_menu_item_classes );

		$args = array(
			'menu-item-object-id' => $_menu_item_object_id,
			'menu-item-object' => $_menu_item_object,
			'menu-item-parent-id' => $_menu_item_menu_item_parent,
			'menu-item-position' => intval( $item['menu_order'] ),
			'menu-item-type' => $_menu_item_type,
			'menu-item-title' => $item['post_title'],
			'menu-item-url' => $_menu_item_url,
			'menu-item-description' => $item['post_content'],
			'menu-item-attr-title' => $item['post_excerpt'],
			'menu-item-target' => $_menu_item_target,
			'menu-item-classes' => $_menu_item_classes,
			'menu-item-xfn' => $_menu_item_xfn,
			'menu-item-status' => $item['status']
		);

		$id = wp_update_nav_menu_item( $menu_id, 0, $args );
		if ( $id && ! is_wp_error( $id ) )
			$this->processed_menu_items[intval($item['post_id'])] = (int) $id;
	}

		/**
		 * If fetching attachments is enabled then attempt to create a new attachment
		 *
		 * @param array $post Attachment post details from WXR
		 * @param string $url URL to fetch attachment from
		 * @return int|WP_Error Post ID on success, WP_Error otherwise
		 */
		function process_attachment( $post, $url ) {

			$this->imageCount++;

			$guidraw = explode( 'wp-content', $post['guid'] );
			if( !empty( $guidraw[1] ) ){
				$guidraw = SITE_URI.'/wp-content'.$guidraw[1];
			}else{
				$guidraw = SITE_URI.'/wp-content'.$guidraw[0];
			}

			echo '<script type="text/javascript">istaus( '.($this->imageCount/$this->totalImages).' );tstatus( \'Adding Media '.$guidraw.'\' );</script>';

            @ob_flush();
		    @flush();

			if ( ! $this->fetch_attachments )
				return new WP_Error( 'attachment_processing_error',
					__( 'Fetching attachments is not enabled', 'arkahost' ) );

			// if the URL is absolute, but does not contain address, then upload it assuming base_HOME_URL
			if ( preg_match( '|^/[\w\W]+$|', $url ) )
				$url = rtrim( $this->base_url, '/' ) . $url;
			
			$_urlxz = explode( 'wp-content', $url );
			$_urlxc = ABSPATH.'wp-content'.$_urlxz[1];

			if( file_exists( $_urlxc ) ){
				$upload = array(
					'file' => $_urlxc,
					'url' => SITE_URI.'/wp-content'.$_urlxz[1]
				);

			}else{
				$upload = $this->fetch_remote_file( $url, $post );
			}

			if ( is_wp_error( $upload ) )
				return $upload;

			if ( $info = wp_check_filetype( $upload['file'] ) )
				$post['post_mime_type'] = $info['type'];
			else
				return new WP_Error( 'attachment_processing_error', __('Invalid file type', 'arkahost') );

			$post['guid'] = $upload['url'];

			// as per wp-admin/includes/upload.php
			$post_id = wp_insert_attachment( $post, $upload['file'] );
			wp_update_attachment_metadata( $post_id, wp_generate_attachment_metadata( $post_id, $upload['file'] ) );

			// remap resized image URLs, works by stripping the extension and remapping the URL stub.
			if ( preg_match( '!^image/!', $info['type'] ) ) {
				$parts = pathinfo( $url );
				$name = basename( $parts['basename'], ".{$parts['extension']}" ); // PATHINFO_FILENAME in PHP 5.2

				$parts_new = pathinfo( $upload['url'] );
				$name_new = basename( $parts_new['basename'], ".{$parts_new['extension']}" );

				$this->url_remap[$parts['dirname'] . '/' . $name] = $parts_new['dirname'] . '/' . $name_new;
			}

			return $post_id;
		}

		/**
		 * Attempt to download a remote file attachment
		 *
		 * @param string $url URL of item to fetch
		 * @param array $post Attachment details
		 * @return array|WP_Error Local file location details on success, WP_Error otherwise
		 */
		function fetch_remote_file( $url, $post ) {

			// extract the file name and extension from the url
			$file_name = basename( $url );

			// get placeholder file in the upload dir with a unique, sanitized filename
			$upload = wp_upload_bits( $file_name, 0, '', $post['upload_date'] );
			if ( $upload['error'] )
				return new WP_Error( 'upload_dir_error', $upload['error'] );
			
			$WP_Http = new WP_Http();
			
			// fetch the remote url and write it to the placeholder file
			$headers = $WP_Http->request( $url, $upload['file'] );

			// request failed
			if ( ! $headers ) {
				@unlink( $upload['file'] );
				return new WP_Error( 'import_file_error', __('Remote server did not respond', 'arkahost') );
			}
			
			// make sure the fetch was successful
			if ( $headers['response']['code'] != '200' ) {
				@unlink( $upload['file'] );
				return new WP_Error( 'import_file_error', sprintf( __('Remote server returned error response %1$d %2$s', 'arkahost'), esc_html($headers['response']['message']), get_status_header_desc($headers['response']['message']) ) );
			}

			$filesize = filesize( $upload['file'] );

			if ( isset( $headers['content-length'] ) && $filesize != $headers['content-length'] ) {
				@unlink( $upload['file'] );
				return new WP_Error( 'import_file_error', __('Remote file is incorrect size', 'arkahost') );
			}

			if ( 0 == $filesize ) {
				@unlink( $upload['file'] );
				return new WP_Error( 'import_file_error', __('Zero size file downloaded', 'arkahost') );
			}

			$max_size = (int) $this->max_attachment_size();
			if ( ! empty( $max_size ) && $filesize > $max_size ) {
				@unlink( $upload['file'] );
				return new WP_Error( 'import_file_error', sprintf(__('Remote file is too large, limit is %s', 'arkahost'), size_format($max_size) ) );
			}

			// keep track of the old and new urls so we can substitute them later
			$this->url_remap[$url] = $upload['url'];
			$this->url_remap[$post['guid']] = $upload['url']; // r13735, really needed?
			// keep track of the destination if the remote url is redirected somewhere else
			if ( isset($headers['x-final-location']) && $headers['x-final-location'] != $url )
				$this->url_remap[$headers['x-final-location']] = $upload['url'];

			return $upload;
		}
		/**
		 * Attempt to associate posts and menu items with previously missing parents
		 *
		 * An imported post's parent may not have been imported when it was first created
		 * so try again. Similarly for child menu items and menu items which were missing
		 * the object (e.g. post) they represent in the menu
		 */
		function backfill_parents() {
			global $wpdb;

			// find parents for post orphans
			foreach ( $this->post_orphans as $child_id => $parent_id ) {
				$local_child_id = $local_parent_id = false;
				if ( isset( $this->processed_posts[$child_id] ) )
					$local_child_id = $this->processed_posts[$child_id];
				if ( isset( $this->processed_posts[$parent_id] ) )
					$local_parent_id = $this->processed_posts[$parent_id];

				if ( $local_child_id && $local_parent_id )
					$wpdb->update( $wpdb->posts, array( 'post_parent' => $local_parent_id ), array( 'ID' => $local_child_id ), '%d', '%d' );
			}

			// all other posts/terms are imported, retry menu items with missing associated object
			$missing_menu_items = $this->missing_menu_items;
			foreach ( $missing_menu_items as $item ){

				$this->process_menu_item( $item );

			}

			// find parents for menu item orphans
			foreach ( $this->menu_item_orphans as $child_id => $parent_id ) {
				$local_child_id = $local_parent_id = 0;
				if ( isset( $this->processed_menu_items[$child_id] ) )
					$local_child_id = $this->processed_menu_items[$child_id];
				if ( isset( $this->processed_menu_items[$parent_id] ) )
					$local_parent_id = $this->processed_menu_items[$parent_id];

				if ( $local_child_id && $local_parent_id )
					update_post_meta( $local_child_id, '_menu_item_menu_item_parent', (int) $local_parent_id );
			}
		}

		/**
		 * Use stored mapping information to update old attachment URLs
		 */
		function backfill_attachment_urls() {
			global $wpdb;
			// make sure we do the longest urls first, in case one is a substring of another
			uksort( $this->url_remap, array(&$this, 'cmpr_strlen') );

			foreach ( $this->url_remap as $from_url => $to_url ) {
				// remap urls in post_content
				$wpdb->query( $wpdb->prepare("UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)", $from_url, $to_url) );
				// remap enclosure urls
				$result = $wpdb->query( $wpdb->prepare("UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_key='enclosure'", $from_url, $to_url) );
			}
		}

		/**
		 * Update _thumbnail_id meta to new, imported attachment IDs
		 */
		function remap_featured_images() {
			// cycle through posts that have a featured image
			foreach ( $this->featured_images as $post_id => $value ) {
				if ( isset( $this->processed_posts[$value] ) ) {
					$new_id = $this->processed_posts[$value];
					// only update if there's a difference
					if ( $new_id != $value )
						update_post_meta( $post_id, '_thumbnail_id', $new_id );
				}
			}
		}

		/**
		 * Parse a WXR file
		 *
		 * @param string $file Path to WXR file for parsing
		 * @return array Information gathered from the WXR file
		 */
		function parse( $file ) {
			$parser = new king_WXR_Parser();
			return $parser->parse( $file );
		}

		// Display import page title
		function header() {

		}

		// Close div.wrap
		function footer() {
			echo '</div>';
		}

		/**
		 * Display introductory text and file upload form
		 */
		function greet() {
			echo '<div class="narrow">';
			echo '<p>'.__( 'Howdy! Upload your WordPress eXtended RSS (WXR) file and we&#8217;ll import the posts, pages, comments, custom fields, categories, and tags into this site.', 'arkahost' ).'</p>';
			echo '<p>'.__( 'Choose a WXR (.xml) file to upload, then click Upload file and import.', 'arkahost' ).'</p>';
			wp_import_upload_form( 'admin.php?import=wordpress&amp;step=1' );
			echo '</div>';
		}

		/**
		 * Decide if the given meta key maps to information we will want to import
		 *
		 * @param string $key The meta key to check
		 * @return string|bool The key if we do want to import, false if not
		 */
		function is_valid_meta_key( $key ) {
			// skip attachment metadata since we'll regenerate it from scratch
			// skip _edit_lock as not relevant for import
			if ( in_array( $key, array( '_wp_attached_file', '_wp_attachment_metadata', '_edit_lock' ) ) )
				return false;
			return $key;
		}

		/**
		 * Decide whether or not the importer is allowed to create users.
		 * Default is true, can be filtered via import_allow_create_users
		 *
		 * @return bool True if creating users is allowed
		 */
		function allow_create_users() {
			return apply_filters( 'import_allow_create_users', true );
		}

		/**
		 * Decide whether or not the importer should attempt to download attachment files.
		 * Default is true, can be filtered via import_allow_fetch_attachments. The choice
		 * made at the import options screen must also be true, false here hides that checkbox.
		 *
		 * @return bool True if downloading attachments is allowed
		 */
		function allow_fetch_attachments() {
			return apply_filters( 'import_allow_fetch_attachments', true );
		}

		/**
		 * Decide what the maximum file size for downloaded attachments is.
		 * Default is 0 (unlimited), can be filtered via import_attachment_size_limit
		 *
		 * @return int Maximum attachment file size to import
		 */
		function max_attachment_size() {
			return apply_filters( 'import_attachment_size_limit', 0 );
		}

		/**
		 * Added to http_request_timeout filter to force timeout at 60 seconds during import
		 * @return int 60
		 */

		// return the difference in length between two strings
		function cmpr_strlen( $a, $b ) {
			return strlen($b) - strlen($a);
		}
	}

}

function king_bump_request_timeout_60() {
	return 600;
}

if( !empty( $_POST['importSampleData'] ) ){

	$devnImperter = new king_Importer();

	$devnImperter->dispatch();
	echo '<script type="text/javascript">document.title="Initializing Data";</script>';

	global $wpdb;
	/* We need to reset primary menu */
	/*Reset homepage -> index_php*/
	update_option( 'show_on_front', 'posts' );

	/* We need to reset primary menu */
	$thememods = 'theme_mods_'.get_option('stylesheet', true);
	$mod = get_option( $thememods );
	$menuID = $wpdb->get_results('SELECT `term_id` FROM `'.$wpdb->prefix.'terms` WHERE `'.$wpdb->prefix.'terms`.`slug` = "main-menu"');
	$menuOnepageID = $wpdb->get_results('SELECT `term_id` FROM `'.$wpdb->prefix.'terms` WHERE `'.$wpdb->prefix.'terms`.`slug` = "menu-onepage"');

	$oid = 0;

	if( isset( $menuOnepageID[0] ) ){
		$oid = $menuOnepageID[0]->term_id;
	}

	if( isset( $menuID[0] ) ){
		if( !isset( $mod ) ){
			$mod = array( 'nav_menu_locations' => array( 'primary' => $menuID[0]->term_id, 'onepage' => $oid ) );
		}else{
			$mod['nav_menu_locations']['primary'] = $menuID[0]->term_id;
			$mod['nav_menu_locations']['onepage'] = $oid;
		}
		add_option( $thememods , $mod ) || update_option( $thememods , $mod );
	}

	$checkPage = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = 'home-version-1' AND post_type = 'page' AND post_status = 'publish'");
	if( isset( $checkPage ) ){
		add_option( 'show_on_front', 'page' ) || update_option( 'show_on_front', 'page' );
		add_option( 'page_on_front' , $checkPage ) || update_option( 'page_on_front' , $checkPage );
	}
	$checkPage = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = 'blog' AND post_type = 'page' AND post_status = 'publish'");
	if( isset( $checkPage ) ){
		add_option( 'page_for_posts' , $checkPage ) || update_option( 'page_for_posts' , $checkPage );
	}
	$wpdb->query("UPDATE `".$wpdb->posts."` SET `post_status` = 'unpublished' WHERE `post_name` = 'hello-world'");
	
	$options = get_option(KING_OPTNAME);
	$options['topInfoEmail'] = 'contact@arkahost.com';
	$options['topInfoPhone'] = '(+84) 123-4567-890';
	$options['topInfoLiveChat'] = '#';
	$options['feed'] = 'feed';
	$options['facebook'] = 'arkahost';
	$options['twitter'] = 'arkahost';
	$options['youtube'] = 'arkahost';
	$options['pinterest'] = 'arkahost';
	$options['footer'] = 'default.php';
	update_option(KING_OPTNAME, $options, true);
}
