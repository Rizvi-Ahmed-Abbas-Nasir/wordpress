<?php
class king_options_colorStyle extends king_options{	
	
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
		
		
		$args = array(
			array( 'Red', 'pre-color-skin1', 'none' ),
			array( 'Blue', 'pre-color-skin2', '3183d7' ),
			array( 'Green', 'pre-color-skin3', '3fc35f' ),
			array( 'Cyan', 'pre-color-skin4', '35d3b7' ),
			array( 'Orange', 'pre-color-skin5', 'ff6e41' ),
			array( 'Light Blue', 'pre-color-skin6', '37c6f5' ),
			array( 'Pink', 'pre-color-skin7', 'fa3aab' ),
			array( 'Purple', 'pre-color-skin8', 'c762cb' ),
			//array( 'Bridge', 'pre-color-skin9', 'a5d549' ),
			array( 'Slate', 'pre-color-skin10', '6b798f' ),
			array( 'Yellow', 'pre-color-skin11', 'f2d438' ),
			array( 'Dark Red', 'pre-color-skin12', '970001' ),
		);
		
		$df = !empty( $this->value ) ? $this->value : '';
		
	?>
	<div id="style-selector" class="inOptions">
		<ul class="styles" id="list-style-colors">     
		    <li>
		    	<a href="#" title="Default"><span class="pre-color-skin0 pre-color-skin9">Bridge</span></a>
		    	<br />
		    	<input type="radio" id="customColorStyleDefault" name="king[colorStyle]" value="" <?php if( $this->value == '' )echo ' checked '; ?> />
		    </li>
			
			<?php
				
				foreach( $args as $arg ){
			?>
				<li>
			    	<a href="#" title="<?php echo esc_attr($arg[0]); ?>">
			    		<span class="<?php echo esc_attr($arg[1]); ?>"></span>
			    	</a>
			    	<br />
			    	<input type="radio" <?php if($df == $arg[2]){echo 'checked="checked"';$df = '';} ?> name="king[colorStyle]" value="<?php echo esc_attr($arg[2]); ?>" />
			    </li>
			<?php		
				}
				
			?>
			
			<li class="customLi">
		    	<a href="#" title="Custom color">
			    	<input type="text" class="color" id="customColorStyle" value="<?php echo esc_attr($df); ?>" />
		    	</a>
		    	<span>
		    		Custom 
		    		<input placeholder="Select Color" <?php if($df!='')echo 'checked="checked"'; ?> id="targetCustomStyle" type="radio" name="king[colorStyle]" value="<?php echo esc_attr($df); ?>" />
		    	</span>
		    </li>
		</ul>
		<br />
		<?php
			
			printf( __( 'Primary css file has been located at: /wp_content/themes/%s/assets/css/colors/color-primary.css', 'arkahost' ), get_stylesheet() );
			
		?>
	</div>	

	<script type="text/javascript">
		(function($){
			$('#list-style-colors li').on( 'click', function(e){
				if( e.target.nodeName == 'INPUT' ){
					e.target.checked = true;
					return true;	
				}
				$(this).find('input').attr({ checked : true });
				e.preventDefault();
			});
			$('#customColorStyle').change(function(){
				$('#targetCustomStyle').val( this.value );
				$('#targetCustomStyle').attr({'checked':'checked'});
			});
			$('#targetCustomStyle').on('click', function(){
				$('#customColorStyle').trigger('focus');
			});
			$('#customColorStyleDefault').on( 'click', function(){
				$('#customColorStyle').val('');	
			});
		})(jQuery);
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
		
		wp_enqueue_style('styleSwitcher');
		
	}//function
	
}//class
?>