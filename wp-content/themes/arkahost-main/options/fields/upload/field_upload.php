<?php
class king_options_upload extends king_options{	
	
	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @since king_options 1.0
	*/
	function __construct($field = array(), $value ='', $parent = ''){
		
		parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);
		$this->field = $field;
		$this->value = $value;
		
	}//function
	
	
	
	/**
	 * Field Render Function.
	 *
	 * Takes the vars and outputs the HTML for the field in the settings
	 *
	 * @since king_options 1.0
	*/
	function render(){
		
		$class = (isset($this->field['class']))?$this->field['class']:'regular-text';
		
		
		?>
		<div class="king-upload-wrp">
			<input type="hidden" id="<?php echo esc_attr( $this->field['id'] ); ?>" name="<?php echo esc_attr( $this->args['opt_name'].'['.$this->field['id'] ); ?>]" value="<?php echo esc_attr( $this->value ); ?>" class="<?php echo esc_attr( $class ); ?> king-upload-input" />
	
			<img style="max-width: 100%; cursor: pointer;<?php if( empty($this->value) ){echo 'display: none;';} ?>" src="<?php echo esc_url( !empty($this->value)?(str_replace(array('%SITE_URI%', '%HOME_URL%') , array(SITE_URI, SITE_URI), $this->value )):'' ); ?>" class="king-upload-image" />
			<p>
				<button class="button button-large button-primary king-upload-button">
					<i class="fa fa-cloud-upload"></i> Upload Image
				</button>
				&nbsp; 
				<button <?php if( empty($this->value) ){echo ' style="display: none;" ';} ?> class="button button-large king-upload-button-remove">
					<i class="fa fa-times"></i> Remove Image
				</button>
			</p>
		</div>	
		<?php
		
		echo (isset($this->field['desc']) && !empty($this->field['desc']))?'<br/><span class="description">'.$this->field['desc'].'</span>':'';
		
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
			'nhp-opts-field-upload-js', 
			king_options_URL.'fields/upload/field_upload.js', 
			array('jquery', 'thickbox', 'media-upload'),
			time(),
			true
		);
		
		wp_enqueue_style('thickbox');// thanks to https://github.com/rzepak
		wp_enqueue_media();
		wp_localize_script('nhp-opts-field-upload-js', 'king_upload', array('url' => $this->url.'fields/upload/blank.png'));
		
	}//function
	
}//class
?>