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

		if( $atts['amount'] ==3  ){
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
		else{
			$classes .= 'pricing-tables';
		}
		if( $i ==  $atts['amount']){
			$classes .= ' last';
		}
		if( !empty( $atts['class'] ) ){
			$classes .= ' '.$atts['class'];
		}
		$classes .= ' pricing-layout-3';
		?>

		<div class="<?php echo esc_attr( $classes ); ?> <?php if(isset($pricing['best_seller']) && $pricing['best_seller'] == 'yes' ) echo ' pricing-tables-helight'; ?>">
			<div class="title">
				<?php echo esc_html( strtoupper( $prc->post_title ) ); ?>
			</div>
			<div class="price">
				<?php echo esc_html( $pricing['currency'].$pricing['price'] ); ?>
				<i>
				<?php
					if(!empty($pricing['per'])){
						echo '/'. esc_html( $pricing['per'] );
					}
				?>
				</i>
				<?php if( !empty( $pricing['regularly_price'] ) ){ ?>
				<div class="regularly_price">
					<?php _e( 'Regularly', 'arkahost') ?>
					<em><?php echo esc_html( $pricing['currency'].$pricing['regularly_price'] ); ?></em>
				</div>
				<?php } ?>

			</div>
			<div class="cont-list">
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
			<?php if( !empty( $pricing['linksubmit'] ) ){ ?>
			<div class="ordernow">
				<a class="but_small3 <?php if( $pricing['best_seller'] != 'yes' ) echo ' gray'; ?>" href="<?php echo esc_url( $pricing['linksubmit'] ); ?>">
					<span><i class="fa fa-shopping-cart"></i></span> <?php echo esc_html( $pricing['textsubmit'] ); ?>
				</a>
			</div>
			<?php }?>
        </div>
      <?php
	}

}else {
	echo 'No pricing table, <a href="'.admin_url('post-new.php?post_type=pricing-tables').'" target="_blank">Add Pricing</a>';
}
