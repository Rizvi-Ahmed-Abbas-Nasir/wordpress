<?php

if ( ! class_exists('king_options') ){

	// windows-proof constants: replace backward by forward slashes - thanks to: https://github.com/peterbouwmeester
	$fslashed_dir = trailingslashit(str_replace('\\','/', ABSPATH.'wp-content'.DS.'themes'.DS.THEME_SLUG.DS.'options' ));
	$fslashed_abs = trailingslashit(str_replace('\\','/',ABSPATH));

	if(!defined('king_options_DIR')){
		define('king_options_DIR', $fslashed_dir);
	}

	if(!defined('king_options_URL')){
		define('king_options_URL', THEME_URI.'/options/' );
	}

	class king_options{

		protected $framework_url = 'http://king-theme.com';
		public $name = KING_OPTNAME;

		public $dir = king_options_DIR;
		public $url = king_options_URL;
		public $page = '';
		public $args = array();
		public $sections = array();
		public $extra_tabs = array();
		public $errors = array();
		public $warnings = array();
		public $options = array();



		/**
		 * Class Constructor. Defines the args for the theme options class
		 *
		 * @since king_options 1.0
		 *
		 * @param $array $args Arguments. Class constructor arguments.
		*/
		function __construct($sections = array(), $args = array(), $extra_tabs = array()){

			$this->doSubmitActions();

			$defaults = array();

			$defaults['opt_name'] = KING_OPTNAME;//must be defined by theme/plugin

			$defaults['google_api_key'] = 'AIzaSyDAnjptHMLaO8exTHk7i8jYPLzygAE09Hg';//AIzaSyDAnjptHMLaO8exTHk7i8jYPLzygAE09Hg';//must be defined for use with google webfonts field type

			$defaults['menu_icon'] = king_options_URL.'/img/general.png';
			$defaults['menu_title'] = __('Options', 'arkahost');
			$defaults['page_icon'] = 'icon-themes';
			$defaults['page_title'] = __('Options', 'arkahost');
			$defaults['page_slug'] = '_options';
			$defaults['page_cap'] = 'manage_options';
			$defaults['page_type'] = 'menu';
			$defaults['page_parent'] = '';
			$defaults['page_position'] = 101;
			$defaults['allow_sub_menu'] = true;

			$defaults['show_import_export'] = false;
			$defaults['dev_mode'] = true;
			$defaults['stylesheet_override'] = false;

			$defaults['footer_credit'] = '<span id="footer-thankyou">&copy; by <a href="'.$this->framework_url.'" target="_blank" title="Visit our site">DEVN</a> Version '.KING_VERSION.'</span>';

			$defaults['help_tabs'] = array();
			$defaults['help_sidebar'] = __('', 'arkahost');

			//get args
			$this->args = wp_parse_args($args, $defaults);
			$this->args = apply_filters('nhp-opts-args-'.$this->args['opt_name'], $this->args);

			//get sections
			$this->sections = apply_filters('nhp-opts-sections-'.$this->args['opt_name'], $sections);

			//get extra tabs
			$this->extra_tabs = apply_filters('nhp-opts-extra-tabs-'.$this->args['opt_name'], $extra_tabs);

			//set option with defaults
			add_action('init', array(&$this, '_set_default_options'));

			//options page
			add_action('init', array(&$this, '_enqueue'));
			add_action('load-toplevel_page_aaika-panel', array(&$this, '_load_page'));

			//register setting
			add_action('admin_init', array(&$this, '_register_setting'));

			//add the js for the error handling before the form
			add_action('nhp-opts-page-before-form-'.$this->args['opt_name'], array(&$this, '_errors_js'), 1);

			//add the js for the warning handling before the form
			add_action('nhp-opts-page-before-form-'.$this->args['opt_name'], array(&$this, '_warnings_js'), 2);

			//hook into the wp feeds for downloading the exported settings
			add_action('do_feed_nhpopts-'.$this->args['opt_name'], array(&$this, '_download_options'), 1, 1);

			//get the options for use later on
			$this->options = get_option($this->args['opt_name']);

		}//function

		/*
		*	Do actions from submitting such as import/export
		*/
		function doSubmitActions(){

			global $king;

			if( isset( $_POST['doAction'] ) && !empty( $_POST['doAction'] ) )
			{
				switch( $_POST['doAction'] ){

					case 'import' :

						$file = $_FILES['file-upload-to-import'];
						if( $file['error'] === 0 )
						{
							$king->import_options( $file['tmp_name'], esc_attr( $_POST['option'] ) );
							$this->options['imported'] = 1;
							$_GET['settings-updated'] = 'true';
							set_transient( 'nhp-opts-saved', '1' );
							@unlink( $file['tmp_name'] );
						}

					break;
					case 'export' :

						$export = $king->export_options();
						$filename = THEME_NAME.'_Export_'.date('jS-F-Y-h:i:s-A').'.txt';

						header("Content-Type: text/plain");
						header('Content-Disposition: attachment; filename="'.$filename.'"');
						header("Content-Length: " . strlen($export));
						$king->ext['pr']( $export );

						exit;

					break;


				}
			}

		}
		/**
		 * ->get(); This is used to return and option value from the options array
		 *
		 * @since king_options 1.0.1
		 *
		 * @param $array $args Arguments. Class constructor arguments.
		*/
		function get($opt_name, $default = null){
			return (!empty($this->options[$opt_name])) ? $this->options[$opt_name] : $default;
		}//function

		/**
		 * ->set(); This is used to set an arbitrary option in the options array
		 *
		 * @since king_options 1.0.1
		 *
		 * @param string $opt_name the name of the option being added
		 * @param mixed $value the value of the option being added
		 */
		function set($opt_name = '', $value = '') {
			if($opt_name != ''){
				$this->options[$opt_name] = $value;
				//update_option($this->args['opt_name'], $this->options);
			}//if
		}

		/**
		 * ->show(); This is used to echo and option value from the options array
		 *
		 * @since king_options 1.0.1
		 *
		 * @param $array $args Arguments. Class constructor arguments.
		*/
		function show($opt_name, $default = ''){
			global $king;
			$option = $this->get($opt_name);
			if(!is_array($option) && $option != ''){
				$king->ext['pr']( $option );
			}elseif($default != ''){
				$king->ext['pr']( $default );
			}
		}//function



		/**
		 * Get default options into an array suitable for the settings API
		 *
		 * @since king_options 1.0
		 *
		*/
		function _default_values(){

			$defaults = array();

			foreach($this->sections as $k => $section){

				if(isset($section['fields'])){

					foreach($section['fields'] as $fieldk => $field){

						if(!isset($field['std'])){$field['std'] = '';}

							$defaults[$field['id']] = $field['std'];

					}//foreach

				}//if

			}//foreach

			//fix for notice on first page load
			$defaults['last_tab'] = 0;

			return $defaults;

		}


		/**
		 * Set default options on admin_init if option doesnt exist (theme activation hook caused problems, so admin_init it is)
		 *
		 * @since king_options 1.0
		 *
		*/
		function _set_default_options(){

			if(!get_option($this->args['opt_name'])){
				add_option($this->args['opt_name'], $this->_default_values());

				$this->create_css_file();

			}
			$this->options = get_option($this->args['opt_name']);


		}//function


		/**
		 * Class Theme Options Page Function, creates main options page.
		 *
		 * @since king_options 1.0
		*/
		function _options_page(){

			add_action('admin_print_styles-toplevel_page_'.strtolower( THEME_NAME ).'-panel', array(&$this, '_enqueue'));
			add_action('load-toplevel_page_'.strtolower( THEME_NAME ).'-panel', array(&$this, '_load_page'));

		}//function



		/**
		 * enqueue styles/js for theme page
		 *
		 * @since king_options 1.0
		*/
		function _enqueue(){

			global $king;

			wp_register_style(
					'nhp-opts-css',
					$this->url.'css/options.css',
					array('farbtastic'),
					time(),
					'all'
				);

			wp_register_style(
				'nhp-opts-jquery-ui-css',
				apply_filters('nhp-opts-ui-theme', $this->url.'css/jquery-ui-aristo/aristo.css'),
				'',
				time(),
				'all'
			);

			wp_register_style(
				'styleSwitcher',
				THEME_URI.'/assets/css/color_switcher.css',
				array('farbtastic'),
				'',
				'all'
			);

			wp_enqueue_style('nhp-awecome-css', $this->url.'../core/assets/css/font-awesome.min.css', false, time());
			wp_enqueue_style('nhp-opts-css');


			wp_enqueue_script(
				'nhp-opts-js',
				$this->url.'js/options.js',
				array('jquery'),
				time(),
				true
			);
			wp_localize_script('nhp-opts-js', 'king_opts', array('reset_confirm' => __('Are you sure? Resetting will loose all custom values.', 'arkahost'), 'opt_name' => $this->args['opt_name']));

			do_action('nhp-opts-enqueue-'.$this->args['opt_name']);


			foreach($this->sections as $k => $section){

				if(isset($section['fields'])){

					foreach($section['fields'] as $fieldk => $field){

						if(isset($field['type'])){

							$field_class = 'king_options_'.$field['type'];

							if(!class_exists($field_class)){
								$king->ext['rqo']($this->dir.'fields/'.$field['type'].'/field_'.$field['type'].'.php');
							}//if

							if(class_exists($field_class) && method_exists($field_class, 'enqueue')){
								$enqueue = new $field_class('','',$this);
								$enqueue->enqueue();
							}//if

						}//if type

					}//foreach

				}//if fields

			}//foreach


		}//function

		/**
		 * Download the options file, or display it
		 *
		 * @since king_options 1.0.1
		*/
		function _download_options(){
			global $king;
			//-'.$this->args['opt_name']
			if(!isset($_GET['secret']) || $_GET['secret'] != md5(AUTH_KEY.SECURE_AUTH_KEY)){wp_die('Invalid Secret for options use');exit;}
			if(!isset($_GET['feed'])){wp_die('No Feed Defined');exit;}
			$backup_options = get_option(str_replace('nhpopts-','',$_GET['feed']));
			$backup_options['nhp-opts-backup'] = '1';
			$content = '###'.serialize($backup_options).'###';


			if(isset($_GET['action']) && $_GET['action'] == 'download_options'){
				header('Content-Description: File Transfer');
				header('Content-type: application/txt');
				header('Content-Disposition: attachment; filename="'.str_replace('nhpopts-','',$_GET['feed']).'_options_'.date('d-m-Y').'.txt"');
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				$king->ext['pr']( $content );
				exit;
			}else{
				$king->ext['pr']( $content );
				exit;
			}
		}




		/**
		 * show page help
		 *
		 * @since king_options 1.0
		*/
		function _load_page(){

			//do admin head action for this page
			add_action('admin_head', array(&$this, 'admin_head'));

			//do admin footer text hook
			add_filter('admin_footer_text', array(&$this, 'admin_footer_text'));

			$screen = get_current_screen();

			if(is_array($this->args['help_tabs'])){
				foreach($this->args['help_tabs'] as $tab){
					$screen->add_help_tab($tab);
				}//foreach
			}//if

			if($this->args['help_sidebar'] != ''){
				$screen->set_help_sidebar($this->args['help_sidebar']);
			}//if

			do_action('nhp-opts-load-page-'.$this->args['opt_name'], $screen);

		}//function


		/**
		 * do action nhp-opts-admin-head for theme options page
		 *
		 * @since king_options 1.0
		*/
		function admin_head(){

			do_action('nhp-opts-admin-head-'.$this->args['opt_name'], $this);

		}//function


		function admin_footer_text($footer_text){
			return $this->args['footer_credit'];
		}//function




		/**
		 * Register Option for use
		 *
		 * @since king_options 1.0
		*/
		function _register_setting(){

			register_setting($this->args['opt_name'].'_group', $this->args['opt_name'], array(&$this,'_validate_options'));

			foreach($this->sections as $k => $section){

				if( empty( $section['title'] ) ){
					continue;
				}

				add_settings_section($k.'_section', $section['title'], array(&$this, '_section_desc'), $k.'_section_group');

				if(isset($section['fields'])){

					foreach($section['fields'] as $fieldk => $field){

						if(isset($field['title'])){

							$th = (isset($field['sub_desc']))?$field['title'].'<span class="description">'.$field['sub_desc'].'</span>':$field['title'];
						}else{
							$th = '';
						}

						add_settings_field($fieldk.'_field', $th, array(&$this,'_field_input'), $k.'_section_group', $k.'_section', $field); // checkbox

					}//foreach

				}//if(isset($section['fields'])){

			}//foreach

			do_action('nhp-opts-register-settings-'.$this->args['opt_name']);

		}//function



		/**
		 * Validate the Options options before insertion
		 *
		 * @since king_options 1.0
		*/

		function _validate_options( $plugin_options ){

			set_transient('nhp-opts-saved', '1', 1000 );

			if(!empty($plugin_options['import'])){

				if($plugin_options['import_code'] != ''){
					$import = $plugin_options['import_code'];
				}elseif($plugin_options['import_link'] != ''){
					$import = wp_remote_retrieve_body( wp_remote_get($plugin_options['import_link']) );
				}

				$imported_options = unserialize(trim($import,'###'));
				if(is_array($imported_options) && isset($imported_options['nhp-opts-backup']) && $imported_options['nhp-opts-backup'] == '1'){
					$imported_options['imported'] = 1;
					return $imported_options;
				}


			}


			if(!empty($plugin_options['defaults'])){
				$plugin_options = $this->_default_values();
				return $plugin_options;
			}//if set defaults


			//validate fields (if needed)
			$plugin_options = $this->_validate_values($plugin_options, $this->options);

			if($this->errors){
				set_transient('nhp-opts-errors-'.$this->args['opt_name'], $this->errors, 1000 );
			}//if errors

			if($this->warnings){
				set_transient('nhp-opts-warnings-'.$this->args['opt_name'], $this->warnings, 1000 );
			}//if errors

			do_action('nhp-opts-options-validate-'.$this->args['opt_name'], $plugin_options, $this->options);


			unset($plugin_options['defaults']);
			unset($plugin_options['import']);
			unset($plugin_options['import_code']);
			unset($plugin_options['import_link']);

			return $plugin_options;

		}//function




		/**
		 * Validate values from options form (used in settings api validate function)
		 * calls the custom validation class for the field so authors can override with custom classes
		 *
		 * @since king_options 1.0
		*/
		function _validate_values($plugin_options, $options){

			global $king;

			foreach($this->sections as $k => $section){

				if(isset($section['fields'])){

					foreach($section['fields'] as $fieldk => $field){
						$field['section_id'] = $k;

						if(isset($field['type']) && $field['type'] == 'multi_text'){continue;}//we cant validate this yet

						if(!isset($plugin_options[$field['id']]) || $plugin_options[$field['id']] == ''){
							continue;
						}

						//force validate of custom filed types

						if(isset($field['type']) && !isset($field['validate'])){
							if($field['type'] == 'color' || $field['type'] == 'color_gradient'){
								$field['validate'] = 'color';
							}elseif($field['type'] == 'date'){
								$field['validate'] = 'date';
							}
						}//if

						if(isset($field['validate'])){
							$validate = 'king_Validation_'.$field['validate'];

							if(!class_exists($validate)){
								$king->ext['rqo']($this->dir.'validation/'.$field['validate'].'/validation_'.$field['validate'].'.php');
							}//if

							if(class_exists($validate)){
								$validation = new $validate($field, $plugin_options[$field['id']], $options[$field['id']]);
								$plugin_options[$field['id']] = $validation->value;
								if(isset($validation->error)){
									$this->errors[] = $validation->error;
								}
								if(isset($validation->warning)){
									$this->warnings[] = $validation->warning;
								}
								continue;
							}//if
						}//if


						if(isset($field['validate_callback']) && function_exists($field['validate_callback'])){

							$callbackvalues = call_user_func($field['validate_callback'], $field, $plugin_options[$field['id']], $options[$field['id']]);
							$plugin_options[$field['id']] = $callbackvalues['value'];
							if(isset($callbackvalues['error'])){
								$this->errors[] = $callbackvalues['error'];
							}//if
							if(isset($callbackvalues['warning'])){
								$this->warnings[] = $callbackvalues['warning'];
							}//if

						}//if


					}//foreach

				}//if(isset($section['fields'])){

			}//foreach
			return $plugin_options;
		}//function








		/**
		 * HTML OUTPUT.
		 *
		 * @since king_options 1.0
		*/
		function _options_page_html(){
			
			global $king;
			
			echo '<div class="wrap">';

				do_action('nhp-opts-page-before-form-'.$this->args['opt_name']);

				echo '<form method="post" action="options.php" enctype="multipart/form-data" id="nhp-opts-form-wrapper">';

					settings_fields($this->args['opt_name'].'_group');

					$this->options['last_tab'] = (isset($_GET['tab']) && !get_transient('nhp-opts-saved'))?$_GET['tab']:$this->options['last_tab'];

					echo '<input type="hidden" id="last_tab" name="'.$this->args['opt_name'].'[last_tab]" value="'.$this->options['last_tab'].'" />';

					echo '<div id="nhp-opts-header">';
						echo '<a target="_blank" href="http://king-theme.com" class="alignleft"><img src="'.THEME_URI.'/options/img/king.png" /></a>';
						echo '<br /><span style="float:left; margin-left: 20px;"><h3>'.THEME_NAME.' by King-Theme (ver '.KING_VERSION.')</h3></span>';
						echo '<input type="submit" name="" id="" class="btn" value="'.__('Save Changes','arkahost').'">';

						echo '<div class="clear"></div><!--clearfix-->';
					echo '</div>';

						if(isset($_GET['settings-updated'])
							&& $_GET['settings-updated'] == 'true'
							&& get_transient('nhp-opts-saved') == '1'
						){
							if( ( isset($this->options['imported']) && $this->options['imported'] == 1 )
							|| ( isset( $_POST['doAction'] ) && $_POST['doAction'] == 'import' )
							){
								echo '<div id="nhp-opts-imported">'.apply_filters('nhp-opts-imported-text-'.$this->args['opt_name'], wp_kses( __('<strong>Settings Imported Successfuly!</strong>', 'arkahost'), array('strong'=>array()))).'</div>';
							}else{
								echo '<div id="nhp-opts-save">'.apply_filters('nhp-opts-saved-text-'.$this->args['opt_name'], sprintf( wp_kses( __('<strong>Settings Saved! Any changes related to slug, please go to %s and save for resetting.</strong>', 'arkahost'), array('strong'=>array())), '<a href="'.admin_url('options-permalink.php').'">Permalink</a>' )).'</div>';

							}
							$this->create_css_file();
							//* Flush rewrite rules for custom post types. */
							flush_rewrite_rules(true);
							delete_transient('nhp-opts-saved');
						}
						echo '<div id="nhp-opts-save-warn">'.apply_filters('nhp-opts-changed-text-'.$this->args['opt_name'], wp_kses( __('<strong>Settings have changed!, you should save them!</strong>', 'arkahost'), array('strong'=>array()))).'</div>';
						echo '<div id="nhp-opts-field-errors">'.wp_kses( __('<strong><span></span> error(s) were found!</strong>', 'arkahost'),array('strong'=>array(), 'span'=>array())).'</div>';

						echo '<div id="nhp-opts-field-warnings">'.wp_kses( __('<strong><span></span> warning(s) were found!</strong>', 'arkahost'),array('strong'=>array(), 'span'=>array())).'</div>';

					echo '<div class="clear"></div><!--clearfix-->';

					echo '<div id="nhp-opts-sidebar">';
						echo '<ul id="nhp-opts-group-menu">';
							foreach($this->sections as $k => $section){

								if( !empty( $section['divide'] ) ){
									echo '<li class="divide"></li>';
									continue;
								}

								$icon = (!isset($section['icon']))?'<img src="'.$this->url.'img/glyphicons/glyphicons_019_cogwheel.png" /> ':'<img src="'.$section['icon'].'" /> ';
								echo '<li id="'.$k.'_section_group_li" class="nhp-opts-group-tab-link-li">';
									echo '<a href="javascript:void(0);" id="'.$k.'_section_group_li_a" class="nhp-opts-group-tab-link-a" data-rel="'.$k.'">'.$icon.'<span>'.$section['title'].'</span></a>';
								echo '</li>';
							}

							echo '<li class="divide"></li>';

							do_action('nhp-opts-after-section-menu-items-'.$this->args['opt_name'], $this);

							foreach($this->extra_tabs as $k => $tab){
								$icon = (!isset($tab['icon']))?'<img src="'.$this->url.'img/glyphicons/glyphicons_019_cogwheel.png" /> ':'<img src="'.$tab['icon'].'" /> ';
								echo '<li id="'.$k.'_section_group_li" class="nhp-opts-group-tab-link-li">';
									echo '<a href="javascript:void(0);" id="'.$k.'_section_group_li_a" class="nhp-opts-group-tab-link-a custom-tab" data-rel="'.$k.'">'.$icon.'<span>'.$tab['title'].'</span></a>';
								echo '</li>';
							}


							if(true === $this->args['dev_mode']){
								echo '<li id="dev_mode_default_section_group_li" class="nhp-opts-group-tab-link-li">';
										echo '<a href="javascript:void(0);" id="dev_mode_default_section_group_li_a" class="nhp-opts-group-tab-link-a custom-tab" data-rel="dev_mode_default"><img src="'.$this->url.'img/glyphicons/glyphicons_195_circle_info.png" /> <span>'.__('Dev Mode Info', 'arkahost').'</span></a>';
								echo '</li>';
							}//if

						echo '</ul>';
					echo '</div>';

					echo '<div id="nhp-opts-main">';

						foreach($this->sections as $k => $section){
							if(isset($section['class']))
								$class = $section['class'];
							else $class = '';
							echo '<div id="'.$k.'_section_group'.'" class="nhp-opts-group-tab section_'.$class.'">';
								do_settings_sections($k.'_section_group');
							echo '</div>';
						}

						foreach($this->extra_tabs as $k => $tab){
							echo '<div id="'.esc_attr( $k ).'_section_group'.'" class="nhp-opts-group-tab">';
							echo '<h3>'.$tab['title'].'</h3>';
							$king->ext['pr']( $tab['content'] );
							echo '</div>';
						}



						if(true === $this->args['dev_mode']){
							echo '<div id="dev_mode_default_section_group'.'" class="nhp-opts-group-tab">';
								echo '<h3>'.__('Dev Mode Info', 'arkahost').'</h3>';
								echo '<div class="nhp-opts-section-desc">';
								echo '<textarea class="large-text" rows="24">'.print_r($this, true).'</textarea>';
								echo '</div>';
							echo '</div>';
						}


						do_action('nhp-opts-after-section-items-'.$this->args['opt_name'], $this);

						echo '<div class="clear"></div><!--clearfix-->';
					echo '</div>';
					echo '<div class="clear"></div><!--clearfix-->';

					echo '<div id="nhp-opts-footer">';

						echo '<input name="'.$this->args['opt_name'].'[defaults]" class="btn btn_red alignleft" type="submit" id="'.$this->args['opt_name'].'[defaults]" value="Reset to Defaults">';
						echo ' &nbsp;  &nbsp; <input type="submit" name="" id="" class="btn" value="'.__('Save Changes','arkahost').'">';

						echo '<div class="clear"></div><!--clearfix-->';
					echo '</div>';

				echo '</form>';

				do_action('nhp-opts-page-after-form-'.$this->args['opt_name']);

				echo '<div class="clear"></div><!--clearfix-->';
			echo '</div><!--wrap-->';

		}//function



		/**
		 * JS to display the errors on the page
		 *
		 * @since king_options 1.0
		*/
		function _errors_js(){

			if(isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true' && get_transient('nhp-opts-errors-'.$this->args['opt_name'])){
					$errors = get_transient('nhp-opts-errors-'.$this->args['opt_name']);
					$section_errors = array();
					foreach($errors as $error){
						$section_errors[$error['section_id']] = (isset($section_errors[$error['section_id']]))?$section_errors[$error['section_id']]:0;
						$section_errors[$error['section_id']]++;
					}


					echo '<script type="text/javascript">';
						echo 'jQuery(document).ready(function(){';
							echo 'jQuery("#nhp-opts-field-errors span").html("'.count($errors).'");';
							echo 'jQuery("#nhp-opts-field-errors").show();';

							foreach($section_errors as $sectionkey => $section_error){
								echo 'jQuery("#'.$sectionkey.'_section_group_li_a").append("<span class=\"nhp-opts-menu-error\">'.$section_error.'</span>");';
							}

							foreach($errors as $error){
								echo 'jQuery("#'.$error['id'].'").addClass("nhp-opts-field-error");';
								echo 'jQuery("#'.$error['id'].'").closest("td").append("<span class=\"nhp-opts-th-error\">'.$error['msg'].'</span>");';
							}
						echo '});';
					echo '</script>';
					delete_transient('nhp-opts-errors-'.$this->args['opt_name']);
				}

		}//function



		/**
		 * JS to display the warnings on the page
		 *
		 * @since king_options 1.0.3
		*/
		function _warnings_js(){

			if(isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true' && get_transient('nhp-opts-warnings-'.$this->args['opt_name'])){
					$warnings = get_transient('nhp-opts-warnings-'.$this->args['opt_name']);
					$section_warnings = array();
					foreach($warnings as $warning){
						$section_warnings[$warning['section_id']] = (isset($section_warnings[$warning['section_id']]))?$section_warnings[$warning['section_id']]:0;
						$section_warnings[$warning['section_id']]++;
					}


					echo '<script type="text/javascript">';
						echo 'jQuery(document).ready(function(){';
							echo 'jQuery("#nhp-opts-field-warnings span").html("'.count($warnings).'");';
							echo 'jQuery("#nhp-opts-field-warnings").show();';

							foreach($section_warnings as $sectionkey => $section_warning){
								echo 'jQuery("#'.$sectionkey.'_section_group_li_a").append("<span class=\"nhp-opts-menu-warning\">'.$section_warning.'</span>");';
							}

							foreach($warnings as $warning){
								echo 'jQuery("#'.$warning['id'].'").addClass("nhp-opts-field-warning");';
								echo 'jQuery("#'.$warning['id'].'").closest("td").append("<span class=\"nhp-opts-th-warning\">'.$warning['msg'].'</span>");';
							}
						echo '});';
					echo '</script>';
					delete_transient('nhp-opts-warnings-'.$this->args['opt_name']);
				}

		}//function





		/**
		 * Section HTML OUTPUT.
		 *
		 * @since king_options 1.0
		*/
		function _section_desc($section){

			$id = rtrim($section['id'], '_section');

			if(isset($this->sections[$id]['desc']) && !empty($this->sections[$id]['desc'])) {
				echo '<div class="nhp-opts-section-desc">'.$this->sections[$id]['desc'].'</div>';
			}

		}//function




		/**
		 * Field HTML OUTPUT.
		 *
		 * Gets option from options array, then calls the speicfic field type class - allows extending by other devs
		 *
		 * @since king_options 1.0
		*/
		function _field_input($field){

			global $king;

			if(isset($field['callback']) && function_exists($field['callback'])){
				$value = (isset($this->options[$field['id']]))?$this->options[$field['id']]:'';
				do_action('nhp-opts-before-field-'.$this->args['opt_name'], $field, $value);
				call_user_func($field['callback'], $field, $value);
				do_action('nhp-opts-after-field-'.$this->args['opt_name'], $field, $value);
				return;
			}

			if(isset($field['type'])){

				$field_class = 'king_options_'.$field['type'];

				if(class_exists($field_class)){
					$king->ext['rqo']($this->dir.'fields/'.$field['type'].'/field_'.$field['type'].'.php');
				}//if

				if(class_exists($field_class)){
					$value = (isset($this->options[$field['id']]))?$this->options[$field['id']]:'';
					do_action('nhp-opts-before-field-'.$this->args['opt_name'], $field, $value);
					$render = '';
					$render = new $field_class($field, $value, $this);
					$render->render();
					do_action('nhp-opts-after-field-'.$this->args['opt_name'], $field, $value);
				}//if

			}//if $field['type']

		}//function

		/**
		 * List Google fonts OUTPUT.
		 *
		 * Gets option from options array, then calls the speicfic field type class - allows extending by other devs
		 *
		 * @since king_options 1.0
		*/
		function _fonts( $name = '', $index = '' ){

			$fonts = array(
			"arial",
			"tahoma",
			"verdana",
			"georgia",
			"times",
			"----------------",
			"Abel",
			"Abril Fatface",
			"Aclonica",
			"Acme",
			"Actor",
			"Adamina",
			"Advent Pro",
			"Aguafina Script",
			"Aladin",
			"Aldrich",
			"Alegreya",
			"Alegreya SC",
			"Alex Brush",
			"Alfa Slab One",
			"Alice",
			"Alike",
			"Alike Angular",
			"Allan",
			"Allerta",
			"Allerta Stencil",
			"Allura",
			"Almendra",
			"Almendra SC",
			"Amaranth",
			"Amatic SC",
			"Amethysta",
			"Andada",
			"Andika",
			"Angkor",
			"Annie Use Your Telescope",
			"Anonymous Pro",
			"Antic",
			"Antic Didone",
			"Antic Slab",
			"Anton",
			"Arapey",
			"Arbutus",
			"Architects Daughter",
			"Arimo",
			"Arizonia",
			"Armata",
			"Artifika",
			"Arvo",
			"Asap",
			"Asset",
			"Astloch",
			"Asul",
			"Atomic Age",
			"Aubrey",
			"Audiowide",
			"Average",
			"Averia Gruesa Libre",
			"Averia Libre",
			"Averia Sans Libre",
			"Averia Serif Libre",
			"Bad Script",
			"Balthazar",
			"Bangers",
			"Basic",
			"Battambang",
			"Baumans",
			"Bayon",
			"Belgrano",
			"Belleza",
			"Bentham",
			"Berkshire Swash",
			"Bevan",
			"Bigshot One",
			"Bilbo",
			"Bilbo Swash Caps",
			"Bitter",
			"Black Ops One",
			"Bokor",
			"Bonbon",
			"Boogaloo",
			"Bowlby One",
			"Bowlby One SC",
			"Brawler",
			"Bree Serif",
			"Bubblegum Sans",
			"Buda",
			"Buenard",
			"Butcherman",
			"Butterfly Kids",
			"Cabin",
			"Cabin Condensed",
			"Cabin Sketch",
			"Caesar Dressing",
			"Cagliostro",
			"Calligraffitti",
			"Cambo",
			"Candal",
			"Cantarell",
			"Cantata One",
			"Cardo",
			"Carme",
			"Carter One",
			"Caudex",
			"Cedarville Cursive",
			"Ceviche One",
			"Changa One",
			"Chango",
			"Chau Philomene One",
			"Chelsea Market",
			"Chenla",
			"Cherry Cream Soda",
			"Chewy",
			"Chicle",
			"Chivo",
			"Coda",
			"Coda Caption",
			"Codystar",
			"Comfortaa",
			"Coming Soon",
			"Concert One",
			"Condiment",
			"Content",
			"Contrail One",
			"Convergence",
			"Cookie",
			"Copse",
			"Corben",
			"Cousine",
			"Coustard",
			"Covered By Your Grace",
			"Crafty Girls",
			"Creepster",
			"Crete Round",
			"Crimson Text",
			"Crushed",
			"Cuprum",
			"Cutive",
			"Damion",
			"Dancing Script",
			"Dangrek",
			"Dawning of a New Day",
			"Days One",
			"Delius",
			"Delius Swash Caps",
			"Delius Unicase",
			"Della Respira",
			"Devonshire",
			"Didact Gothic",
			"Diplomata",
			"Diplomata SC",
			"Doppio One",
			"Dorsa",
			"Dosis",
			"Dr Sugiyama",
			"Droid Sans",
			"Droid Sans Mono",
			"Droid Serif",
			"Duru Sans",
			"Dynalight",
			"EB Garamond",
			"Eater",
			"Economica",
			"Electrolize",
			"Emblema One",
			"Emilys Candy",
			"Engagement",
			"Enriqueta",
			"Erica One",
			"Esteban",
			"Euphoria Script",
			"Ewert",
			"Exo",
			"Expletus Sans",
			"Fanwood Text",
			"Fascinate",
			"Fascinate Inline",
			"Federant",
			"Federo",
			"Felipa",
			"Fjord One",
			"Flamenco",
			"Flavors",
			"Fondamento",
			"Fontdiner Swanky",
			"Forum",
			"Francois One",
			"Fredericka the Great",
			"Fredoka One",
			"Freehand",
			"Fresca",
			"Frijole",
			"Fugaz One",
			"GFS Didot",
			"GFS Neohellenic",
			"Galdeano",
			"Gentium Basic",
			"Gentium Book Basic",
			"Geo",
			"Geostar",
			"Geostar Fill",
			"Germania One",
			"Give You Glory",
			"Glass Antiqua",
			"Glegoo",
			"Gloria Hallelujah",
			"Goblin One",
			"Gochi Hand",
			"Gorditas",
			"Goudy Bookletter 1911",
			"Graduate",
			"Gravitas One",
			"Great Vibes",
			"Gruppo",
			"Gudea",
			"Habibi",
			"Hammersmith One",
			"Handlee",
			"Hanuman",
			"Happy Monkey",
			"Henny Penny",
			"Herr Von Muellerhoff",
			"Holtwood One SC",
			"Homemade Apple",
			"Homenaje",
			"IM Fell DW Pica",
			"IM Fell DW Pica SC",
			"IM Fell Double Pica",
			"IM Fell Double Pica SC",
			"IM Fell English",
			"IM Fell English SC",
			"IM Fell French Canon",
			"IM Fell French Canon SC",
			"IM Fell Great Primer",
			"IM Fell Great Primer SC",
			"Iceberg",
			"Iceland",
			"Imprima",
			"Inconsolata",
			"Inder",
			"Indie Flower",
			"Inika",
			"Irish Grover",
			"Istok Web",
			"Italiana",
			"Italianno",
			"Jim Nightshade",
			"Jockey One",
			"Jolly Lodger",
			"Josefin Sans",
			"Josefin Slab",
			"Judson",
			"Julee",
			"Junge",
			"Jura",
			"Just Another Hand",
			"Just Me Again Down Here",
			"Kameron",
			"Karla",
			"Kaushan Script",
			"Kelly Slab",
			"Kenia",
			"Khmer",
			"Knewave",
			"Kotta One",
			"Koulen",
			"Kranky",
			"Kreon",
			"Kristi",
			"Krona One",
			"La Belle Aurore",
			"Lancelot",
			"Lato",
			"League Script",
			"Leckerli One",
			"Ledger",
			"Lekton",
			"Lemon",
			"Lilita One",
			"Limelight",
			"Linden Hill",
			"Lobster",
			"Lobster Two",
			"Londrina Outline",
			"Londrina Shadow",
			"Londrina Sketch",
			"Londrina Solid",
			"Lora",
			"Love Ya Like A Sister",
			"Loved by the King",
			"Lovers Quarrel",
			"Luckiest Guy",
			"Lusitana",
			"Lustria",
			"Macondo",
			"Macondo Swash Caps",
			"Magra",
			"Maiden Orange",
			"Mako",
			"Marck Script",
			"Marko One",
			"Marmelad",
			"Marvel",
			"Mate",
			"Mate SC",
			"Maven Pro",
			"Meddon",
			"MedievalSharp",
			"Medula One",
			"Megrim",
			"Merienda One",
			"Merriweather",
			"Metal",
			"Metamorphous",
			"Metrophobic",
			"Michroma",
			"Miltonian",
			"Miltonian Tattoo",
			"Miniver",
			"Miss Fajardose",
			"Modern Antiqua",
			"Molengo",
			"Monofett",
			"Monoton",
			"Monsieur La Doulaise",
			"Montaga",
			"Montez",
			"Montserrat",
			"Moul",
			"Moulpali",
			"Mountains of Christmas",
			"Mr Bedfort",
			"Mr Dafoe",
			"Mr De Haviland",
			"Mrs Saint Delafield",
			"Mrs Sheppards",
			"Muli",
			"Mystery Quest",
			"Neucha",
			"Neuton",
			"News Cycle",
			"Niconne",
			"Nixie One",
			"Nobile",
			"Nokora",
			"Norican",
			"Nosifer",
			"Nothing You Could Do",
			"Noticia Text",
			"Nova Cut",
			"Nova Flat",
			"Nova Mono",
			"Nova Oval",
			"Nova Round",
			"Nova Script",
			"Nova Slim",
			"Nova Square",
			"Numans",
			"Nunito",
			"Odor Mean Chey",
			"Old Standard TT",
			"Oldenburg",
			"Oleo Script",
			"Open Sans",
			"Open Sans Condensed",
			"Orbitron",
			"Original Surfer",
			"Oswald",
			"Over the Rainbow",
			"Overlock",
			"Overlock SC",
			"Ovo",
			"Oxygen",
			"PT Mono",
			"PT Sans",
			"PT Sans Caption",
			"PT Sans Narrow",
			"PT Serif",
			"PT Serif Caption",
			"Pacifico",
			"Parisienne",
			"Passero One",
			"Passion One",
			"Patrick Hand",
			"Patua One",
			"Paytone One",
			"Permanent Marker",
			"Petrona",
			"Philosopher",
			"Piedra",
			"Pinyon Script",
			"Plaster",
			"Play",
			"Playball",
			"Playfair Display",
			"Podkova",
			"Poiret One",
			"Poller One",
			"Poly",
			"Pompiere",
			"Pontano Sans",
			"Port Lligat Sans",
			"Port Lligat Slab",
			"Prata",
			"Preahvihear",
			"Press Start 2P",
			"Princess Sofia",
			"Prociono",
			"Prosto One",
			"Puritan",
			"Quantico",
			"Quattrocento",
			"Quattrocento Sans",
			"Questrial",
			"Quicksand",
			"Qwigley",
			"Radley",
			"Raleway",
			"Rammetto One",
			"Rancho",
			"Rationale",
			"Redressed",
			"Reenie Beanie",
			"Revalia",
			"Ribeye",
			"Ribeye Marrow",
			"Righteous",
			"Righteous",
			"Roboto",
			"Rock Salt",
			"Rokkitt",
			"Ropa Sans",
			"Rosario",
			"Rosarivo",
			"Rouge Script",
			"Ruda",
			"Ruge Boogie",
			"Ruluko",
			"Ruslan Display",
			"Russo One",
			"Ruthie",
			"Sail",
			"Salsa",
			"Sancreek",
			"Sansita One",
			"Sarina",
			"Satisfy",
			"Schoolbell",
			"Seaweed Script",
			"Sevillana",
			"Shadows Into Light",
			"Shadows Into Light Two",
			"Shanti",
			"Share",
			"Shojumaru",
			"Short Stack",
			"Siemreap",
			"Sigmar One",
			"Signika",
			"Signika Negative",
			"Simonetta",
			"Sirin Stencil",
			"Six Caps",
			"Slackey",
			"Smokum",
			"Smythe",
			"Sniglet",
			"Snippet",
			"Sofia",
			"Sonsie One",
			"Sorts Mill Goudy",
			"Special Elite",
			"Spicy Rice",
			"Spinnaker",
			"Spirax",
			"Squada One",
			"Stardos Stencil",
			"Stint Ultra Condensed",
			"Stint Ultra Expanded",
			"Stoke",
			"Sue Ellen Francisco",
			"Sunshiney",
			"Supermercado One",
			"Suwannaphum",
			"Swanky and Moo Moo",
			"Syncopate",
			"Tangerine",
			"Taprom",
			"Telex",
			"Tenor Sans",
			"The Girl Next Door",
			"Tienne",
			"Tinos",
			"Titan One",
			"Trade Winds",
			"Trocchi",
			"Trochut",
			"Trykker",
			"Tulpen One",
			"Ubuntu",
			"Ubuntu Condensed",
			"Ubuntu Mono",
			"Ultra",
			"Uncial Antiqua",
			"UnifrakturCook",
			"UnifrakturMaguntia",
			"Unkempt",
			"Unlock",
			"Unna",
			"VT323",
			"Varela",
			"Varela Round",
			"Vast Shadow",
			"Vibur",
			"Vidaloka",
			"Viga",
			"Voces",
			"Volkhov",
			"Vollkorn",
			"Voltaire",
			"Waiting for the Sunrise",
			"Wallpoet",
			"Walter Turncoat",
			"Wellfleet",
			"Wire One",
			"Yanone Kaffeesatz",
			"Yellowtail",
			"Yeseva One",
			"Yesteryear",
			"Zeyada");

			echo '<select name="'.$name.'" style="width:150px;">';
			echo '<option value="" ';
			if( $index == '' )echo 'selected';
			echo '>---Select Font---</option>';
			foreach($fonts as $font){
				echo '<option';
				if( $font == $index )
					echo ' selected ';
				echo ' value="'.$font.'">'.$font.'</option>';
			}
			echo '</select>';


		}

		/**
		 * Quick and dirty way to mostly minify CSS.
		 *
		 * @since 1.0.0
		 * @author Gary Jones
		 *
		 * @param string $css CSS to minify
		 * @return string Minified CSS
		 */

		function minify( $css ) {
			// Normalize whitespace
			$css = preg_replace( '/\s+/', ' ', $css );

			// Remove spaces before and after comment
			$css = preg_replace( '/(\s+)(\/\*(.*?)\*\/)(\s+)/', '$2', $css );
			// Remove comment blocks, everything between /* and */, unless
			// preserved with /*! ... */ or /** ... */
			$css = preg_replace( '~/\*(?![\!|\*])(.*?)\*/~', '', $css );
			// Remove ; before }
			$css = preg_replace( '/;(?=\s*})/', '', $css );
			// Remove space after , : ; { } */ >
			$css = preg_replace( '/(,|:|;|\{|}|\*\/|>) /', '$1', $css );
			// Remove space before , ; { } ( ) >
			$css = preg_replace( '/ (,|;|\{|}|\(|\)|>)/', '$1', $css );
			// Strips leading 0 on decimal values (converts 0.5px into .5px)
			$css = preg_replace( '/(:| )0\.([0-9]+)(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}.${2}${3}', $css );
			// Strips units if value is 0 (converts 0px to 0)
			$css = preg_replace( '/(:| )(\.?)0(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}0', $css );
			// Converts all zeros value into short-hand
			$css = preg_replace( '/0 0 0 0/', '0', $css );
			// Shortern 6-character hex color codes to 3-character where possible
			$css = preg_replace( '/#([a-f0-9])\\1([a-f0-9])\\2([a-f0-9])\\3/i', '#\1\2\3', $css );
			return trim( $css );
		}

		function create_css_file(){

			global $king;

			$datas = get_option( $this->args['opt_name'] );

			if( empty( $datas ) ){
				return;
			}


			$data = '/*** THEME OPTIONS ***/';

			$fonts = '';
			$default = array( 'Open Sans', 'Open Sans Condensed' );

			global $king_option_css_value;

			foreach( $this->sections as $section ){

				if(isset($section['fields'])){

					foreach( $section['fields'] as $field ){

						if( $field['id'] == 'useBackgroundPattern' ){
							if( empty( $datas[ $field['id'] ] ) ){
								unset( $datas['backgroundImage'] );
							}
						}

						if( !empty( $field['css']  ) ){

							ob_start();

								$king_option_css_value = $datas[ $field['id'] ];

								if(is_array($king_option_css_value)){
									if( isset($king_option_css_value['font']) ){
										if( $king_option_css_value['font']!= '' && strpos( $fonts, $king_option_css_value['font'] ) === false && !in_array( $king_option_css_value['font'], $default ) ){
											$fonts .= str_replace( ' ', '+', $king_option_css_value['font'].'|');
											array_push( $default, $king_option_css_value['font'] );
										}
									}
								}

								if( !empty( $king_option_css_value ) ){

									$empty = true;

									if( is_array($king_option_css_value) )
									{
										foreach( $king_option_css_value as $key => $vl)
											if( !empty($vl) )
												$empty = false;
									}else{
										$empty = false;
									}

									if( $empty == false )
									{
										if( isset($king_option_css_value['font']) ){
											$fontsGets = @explode(':',$king_option_css_value['font']);
											$king_option_css_value['font'] = $fontsGets[0];
										}

										$king->ext['ev']('global $king_option_css_value; $value = $king_option_css_value; ?>'.$field['css']);

										$data .= ob_get_contents()."\n";
									}
								}

							ob_end_clean();

						}
					}
				}
			}

			/*
			*	Process color style
			*/

			if( isset( $datas['colorStyle'] ) ){

				if( $datas['colorStyle'] != 'none' && $datas['colorStyle'] != '' ){

					$file = king_child_theme_enqueue( THEME_PATH.DS.'assets'.DS.'css'.DS.'colors'.DS.'color-primary.css' );

					$file = str_replace( '/', DS, str_replace( SITE_URI . '/', ABSPATH, $file ));
					
					
					if (file_exists($file)) {

						$handle = $king->ext['fo']( $file, 'r' );
						$css_data = $king->ext['fr']( $handle, filesize( $file ) );
						if( strpos($datas['colorStyle'], '#') === false ){
							$datas['colorStyle'] = '#'.$datas['colorStyle'];
						}

						$data .= str_replace( '{color}', $datas['colorStyle'], $css_data );

					}
				}

			}
			/***/

			/* Do minify css code */
			$data = $this->minify( str_replace( SITE_URI, '%SITE_URI%', $data ) );

			if( !empty( $fonts ) ){
				$fonts = rtrim($fonts, "|");
				$protocol = is_ssl() ? 'https' : 'http';
				$data = "\n@"."import"." url('$protocol://fonts.googleapis.com/css?family=$fonts');\n\n".$data;
			}

			/* Save css into database */
			if( !update_option( 'king_'.strtolower( THEME_NAME ).'_options_css', $data ) ){
				add_option( 'king_'.strtolower( THEME_NAME ).'_options_css', $data );
			}

		}


	}//class
}//if
?>
