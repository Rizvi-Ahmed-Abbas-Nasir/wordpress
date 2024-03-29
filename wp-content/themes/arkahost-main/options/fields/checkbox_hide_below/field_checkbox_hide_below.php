<?php
class king_options_checkbox_hide_below extends king_options{	
	
	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @since king_options 1.0.1
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
	 * @since king_options 1.0.1
	*/
	function render(){
		global $king;
		$class = (isset($this->field['class']))?$this->field['class']:'';
		
		$king->ext['pr'] ($this->field['desc'] != '')?' <label for="'.$this->field['id'].'">':'';
		
		echo '<input type="checkbox" id="'.$this->field['id'].'" name="'.$this->args['opt_name'].'['.$this->field['id'].']" value="1" class="'.$class.' nhp-opts-checkbox-hide-below" '.checked($this->value, '1', false).' />';
		
		echo (isset($this->field['desc']) && !empty($this->field['desc']))?' '.$this->field['desc'].'</label>':'';
		
	}//function
	
	
	/**
	 * Enqueue Function.
	 *
	 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
	 *
	 * @since king_options 1.0.1
	*/
	function enqueue(){
		
		wp_enqueue_script(
			'nhp-opts-checkbox-hide-below-js', 
			king_options_URL.'fields/checkbox_hide_below/field_checkbox_hide_below.js', 
			array('jquery'),
			time(),
			true
		);
		
	}//function
	
}//class
?>