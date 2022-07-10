<?php
class king_options_import_data extends king_options{

	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @since king_options 5.0
	*/
	function __construct($field = array(), $value ='', $parent){

		parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);
		$this->field = $field;
		$this->value = $value;
		//$this->render();

	}//function



	/**
	 * Field Render Function.
	 *
	 * Takes the vars and outputs the HTML for the field in the settings
	 *
	 * @since king_options 5.0
	*/
	function render(){

		?>

		<div class="king-file-upload">
			<p>
				<input type="file" name="file-upload-to-import" id="file-upload-to-import">
			</p>
			<p style="margin: 20px 0;">
				<input type="radio" checked name="import_type" value="all" />
				<?php _e('All theme options + widgets', 'arkahost'); ?>
				<br />
				<input type="radio" name="import_type" value="opt" />
				<?php _e('Only theme options', 'arkahost'); ?>
				<br />
				<input type="radio" name="import_type" value="wid" />
				<?php _e('Only widgets data', 'arkahost'); ?>
			</p>
			<p>
	    		<button class="button" type="button" id="theme-import-button">
	    			<i class="fa fa-cloud-upload"></i>
	    			<?php _e('Import Now', 'arkahost'); ?>
	    		</button>
				<br />
				<br />
	    		<span class="import-warning" id="import-warning-msg"><?php _e('WARNING! This will overwrite all existing option values, please proceed with caution!', 'arkahost'); ?></span>
    		</p>

    		<p>
    			<?php echo (isset($this->field['desc']) && !empty($this->field['desc']))?' <span class="import-warning">'.$this->field['desc'].'</span>':''; ?>
    		</p>
		</div>
		<div class="msg-notice verify-stt  active" style="margin-top: 30px;">
			<i class="fa fa-warning"></i>
			<?php _e('If you are looking for importing Sample Demos, Please go to section: ', 'arkahost'); ?>
			<a href="<?php echo admin_url('/themes.php?page='.strtolower( THEME_NAME ).'-importer'); ?>">
				<?php echo THEME_NAME.' Demos'; ?>
			</a>
		</div>
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

		wp_enqueue_script(
			'nhp-opts-field-import-data-js',
			king_options_URL.'fields/import_data/import_data.js',
			array('jquery'),
			time(),
			true
		);

	}//function

}//class
?>
