<?php
class king_options_typography extends king_options{	
	
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
		
	}//function
	
	
	
	/**
	 * Field Render Function.
	 *
	 * Takes the vars and outputs the HTML for the field in the settings
	 *
	 * @since king_options 1.0
	*/
	function render(){
		
		if( !is_array($this->value) )
			$this->value = array("color"=>'',"font"=>'','size'=>'','weight'=>'','style'=>'');
		if( !isset($this->value['color']) )
			$this->value['color'] = '';		
		if( !isset($this->value['font']) )
			$this->value['font'] = '';		
		if( !isset($this->value['size']) )
			$this->value['size'] = '';		
		if( !isset($this->value['weight']) )
			$this->value['weight'] = '';		
		if( !isset($this->value['style']) )
			$this->value['style'] = '';
		$class = (isset($this->field['class']))?$this->field['class']:'';

		
		echo '<div class="farb-popup-wrapper typography-wrapper">';

		echo '<input type="text" id="'.$this->field['id'].'_color" name="'.$this->args['opt_name'].'['.$this->field['id'].'][color]" value="'.$this->value['color'].'" class="'.$class.' popup-colorpicker color" style="width:140px;"/>';
		
		king_options::_fonts($this->args['opt_name'].'['.$this->field['id'].'][font]', $this->value['font']);
		
		?>
		
			<select name="<?php echo esc_attr( $this->args['opt_name'].'['.$this->field['id'].'][size]' ); ?>" style="width:80px;">
				<option value=""></option>
				<?php
					
					for($i=8; $i< 101; $i++)
					{
						echo '<option';
						if( $i == $this->value['size'] )
							echo ' selected ';
						echo ' value="'.$i.'">'.$i.'px</option>';
					}
				?>
			</select>
		
			<select name="<?php echo esc_attr( $this->args['opt_name'].'['.$this->field['id'].'][weight]' ); ?>" style="width:80px;">
				<option value="" <?php if($this->value['weight'] == '')echo 'selected="selected"'; ?>></option>
				<option value="normal" <?php if($this->value['weight'] == 'normal')echo 'selected="selected"'; ?>>Normal</option>
				<option value="bold" <?php if($this->value['weight'] == 'bold')echo 'selected="selected"'; ?>>Bold</option>
				<option value="lighter" <?php if($this->value['weight'] == 'lighter')echo 'selected="selected"'; ?>>Lighter</option>
				<option value="bolder" <?php if($this->value['weight'] == 'bolder')echo 'selected="selected"'; ?>>Bolder</option>
				<option value="100" <?php if($this->value['weight'] == '100')echo 'selected="selected"'; ?>>100</option>
				<option value="200" <?php if($this->value['weight'] == '200')echo 'selected="selected"'; ?>>200</option>
				<option value="300" <?php if($this->value['weight'] == '300')echo 'selected="selected"'; ?>>300</option>
				<option value="400" <?php if($this->value['weight'] == '400')echo 'selected="selected"'; ?>>400</option>
				<option value="500" <?php if($this->value['weight'] == '500')echo 'selected="selected"'; ?>>500</option>
				<option value="600" <?php if($this->value['weight'] == '600')echo 'selected="selected"'; ?>>600</option>
				<option value="700" <?php if($this->value['weight'] == '700')echo 'selected="selected"'; ?>>700</option>
				<option value="800" <?php if($this->value['weight'] == '800')echo 'selected="selected"'; ?>>800</option>
				<option value="900" <?php if($this->value['weight'] == '900')echo 'selected="selected"'; ?>>900</option>
			</select>
		
			<select name="<?php echo esc_attr( $this->args['opt_name'].'['.$this->field['id'].'][style]' ); ?>" style="width:90px;">
				<option value=""  <?php if($this->value['style'] == '')echo 'selected="selected"'; ?>></option>
				<option value="normal" <?php if($this->value['style'] == 'normal')echo 'selected="selected"'; ?>>Normal</option>
				<option value="italic" <?php if($this->value['style'] == 'italic')echo 'selected="selected"'; ?>>Italic</option>
				<option value="oblique" <?php if($this->value['style'] == 'oblique')echo 'selected="selected"'; ?>>oblique</option>
			</select>
		
		<?php

		
		echo '</div>';
		
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