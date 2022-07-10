<?php

global $king;
$prcs = $king->bag['prcs'];
$atts = $king->bag['atts'];
$eff = $king->bag['eff'];
$i = 0;
if( count( $prcs  ) ){
	/*
	$centerAl = '';
	if( $atts['amount'] == 1 || (  $atts['amount'] == 2 &&  $atts['style'] == 3  ) ){
		$centerAl = 'float:left;height:1px;width: 35%;';
	}
	if( $atts['amount'] == 2 ){
		$centerAl = 'float:left;height:1px;width: 15%;';
	}
	if( $atts['amount'] == 3 && ( $atts['style'] == 4  ) ){
		$centerAl = 'float:left;height:1px;width: 12%;';
	}

	if( $centerAl != '' ){
		echo '<div style="'.$centerAl.'"></div>';
	}
*/

	foreach( $prcs as $prc ){

		$i++;

		$pricing = get_post_meta( $prc->ID , 'king_pricing' );
		if( !empty( $pricing ) ){
			$pricing  = $pricing[0];
		}else{
			$pricing = array( 'price' => '100', 'per' => 'M', 'regularly_price' => '150', 'attr' => "Option 1\nOption 2", 'currency' => '$', 'best_seller' => 'no', 'textsubmit' => 'Sign Up', 'linksubmit' => '#' );
		}

		$classes = 'animated '.$eff.' delay-'.($i+1).'00ms ';

		if( $atts['amount'] ==3  )
		{

			$classes .= 'one_third_less';

		}
		else if($atts['amount'] == 5)
		{
		    $classes .= 'one_fifth_less';
		}else
		{
			$classes .= 'one_fourth_less';
		}
		if( ($i == 4 && $atts['amount'] ==4) || ($i == 3 && $atts['amount'] ==3)  || ($i == 5 && $atts['amount'] ==5) ){
			$classes .= ' last';
		}
		if( !empty( $atts['class'] ) ){
			
			$classes .= ' '.$atts['class'];
		}
		
		$classes .= ' pricing-layout-1';

		?>

		<div class="<?php echo esc_attr( $classes ); ?>">
			<div class="planbox<?php if( $pricing['best_seller'] == 'yes' )echo ' highlight'; ?>">
				<?php if( isset($pricing['best_seller']) && $pricing['best_seller'] == 'yes' ){ ?>
					<img width="134" src="<?php echo THEME_URI; ?>/assets/images/best-seller.png" alt="ArkaHost" class="hiimg" />
				<?php } ?>
				<div class="title">
					<h4 class="caps">
						<strong><?php echo esc_html( strtoupper( $prc->post_title ) ); ?></strong>
					</h4>
				</div>
				<div class="prices">
		        	<strong>
		        		<?php echo esc_html( $pricing['currency'].$pricing['price'] ); ?>
						<i>
						<?php
							if(!empty($pricing['per'])){
								echo '/'. esc_html( $pricing['per'] );
							}
						?>
						</i>
		        	</strong>
		        	<b>
					<?php if( !empty( $pricing['regularly_price'] ) ){ ?>
		        		<?php _e( 'Regularly', 'arkahost') ?>
		        		<em><?php echo esc_html( $pricing['currency'].$pricing['regularly_price'] ); ?></em>
					<?php } ?>
		        	</b>
		        	<?php if( !empty( $pricing['linksubmit'] ) ){ ?>
		            <a href="<?php echo esc_url( $pricing['linksubmit'] ); ?>">
			            <?php echo esc_html( $pricing['textsubmit'] ); ?>
			        </a>
			        <?php } ?>
		        </div>
				<ul class="price_des">
					<?php
		            	$pros = explode( "\n", $pricing['attr'] );
		            	if( count( $pros ) ){
			            	foreach( $pros as $pro ){
				            	echo '<li>'.$atts['icon'].$pro.'</li>';
			            	}
		            	}
	            	?>
				</ul>
	        </div>
        </div>
      <?php
	}

}else {
	echo 'No pricing table, <a href="'.admin_url('post-new.php?post_type=pricing-tables').'" target="_blank">Add Pricing</a>';
}
