<?php
class king_options_export_data extends king_options{

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
		<button type="button" class="button btn-export-data" id="theme-export-button">
			<i class="fas fa-cloud-download-alt"></i> <?php _e('Download theme options + widgets', 'arkahost' ); ?>
		</button>
		<p style="margin-bottom: 20px;">
			<?php echo (isset($this->field['desc']) && !empty($this->field['desc']))?' <span class="description">'.$this->field['desc'].'</span>':''; ?>
		</p>

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
