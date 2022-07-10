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
		else if($atts['amount'] == 2)
		{
		    $classes .= 'one_half_less';
		}
		else if($atts['amount'] == 5)
		{
		    $classes .= 'one_fifth_less';
		}
		else
		{
			$classes .= 'pacdetails';
		}

		if( ($i == 4 && $atts['amount'] ==4) || ($i == 3 && $atts['amount'] ==3)  || ($i == 5 && $atts['amount'] ==5)  || ($i == 2 && $atts['amount'] ==2) ){
			$classes .= ' last';
		}

		if( !empty( $atts['class'] ) ){
			$classes .= ' '.$atts['class'];
		}
		$classes .= ' pricing-layout-2';
		?>

		<div class="<?php echo esc_attr( $classes ); ?> <?php if( $pricing['best_seller'] == 'yes' )echo ' highlight'; ?>">
			<div class="planbox">

				<div class="title">
					<?php if( $pricing['best_seller'] == 'yes' ){ ?>
						<h6><?php echo _e('Most Popular', 'arkahost') ?></h6>
					<?php } ?>
					<h2>
						<?php echo esc_html( $prc->post_title ); ?>
					</h2>
					<strong>
						<?php echo esc_html( $pricing['currency'].$pricing['price'] ); ?><sub>
						<?php
							if(!empty($pricing['per'])){
								echo '/'. esc_html( $pricing['per'] );
							}
						?>
						</sub>
						<b>
						<?php if( !empty( $pricing['regularly_price'] ) ){ ?>

							<?php _e( 'Regularly', 'arkahost') ?>
							<em><?php echo esc_html( $pricing['currency'].$pricing['regularly_price'] ); ?></em>

						<?php } ?></b>
					</strong>
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
				<?php if( !empty( $pricing['linksubmit'] ) ){ ?>
				<div class="bottom">
					<a href="<?php echo esc_url( $pricing['linksubmit'] ); ?>">
			            <?php echo esc_html( $pricing['textsubmit'] ); ?>
			        </a>
				</div>
				<?php } ?>
	        </div>
        </div>
      <?php
	}

}else {
	echo 'No pricing table, <a href="'.admin_url('post-new.php?post_type=pricing-tables').'" target="_blank">Add Pricing</a>';
}
