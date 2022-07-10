<?php 

global $king;

$atts = $king->bag['atts'];

//extract all variables

extract( $atts );

$vps_items = urldecode($items);
$items     = json_decode($vps_items);
$max       = count( $items );

if( $start_item > count( $items ) )
	$value = $max;
else
	$value = $start_item;

if( !$max )
	return '';



//get labels
$labels = array();
$ticks  = array();
$i      = 1;

foreach ($items as $key => $item) {
	$labels[] = $item->title;
	$ticks[]  = $i;
	$i++;
}

$vps_option = array(
	'ticks' => $ticks,
	'ticks_labels' => $labels,
	'min' => 1,
	'max' => $max,
	'step' => 1,
	'value' => (int) $value,
	'tooltip' => 'hide',
);


$vps_option = json_encode( $vps_option );

ob_start();
?>
<div class="vps-wrapper">
	<div class="vps_top_part">
		<input class="vps-slides" type="text" data-vps-options="<?php echo esc_attr( $vps_option );?>" data-vps-items="<?php echo esc_attr( $vps_items );?>" data-per="<?php echo esc_attr( $per_label );?>"/>
	</div>
	<div class="vps_bot_part">
		<div class="row vps_display">
			<div class="one_fifth">
				<div class="cpu">
					<h4><?php _e('CPU', 'arkahost');?></h4>
					<div class="vps_value"><?php echo esc_attr( $items[ $value - 1 ]->cpu);?></div>
				</div>
			</div>
			<div class="one_fifth">
				<div class="disk_space">
					<h4><?php _e('Disk Space', 'arkahost');?></h4>
					<div class="vps_value"><?php echo esc_attr( $items[ $value - 1 ]->disk_space);?></div>
				</div>
			</div>
			<div class="one_fifth">
				<div class="bandwidth">
					<h4><?php _e('Bandwidth', 'arkahost');?></h4>
					<div class="vps_value"><?php echo esc_attr( $items[ $value - 1 ]->bandwidth);?></div>
				</div>
			</div>
			<div class="one_fifth">
				<div class="ram">
					<h4><?php _e('RAM', 'arkahost');?></h4>
					<div class="vps_value"><?php echo esc_attr( $items[ $value - 1 ]->ram);?></div>
				</div>
			</div>
			<div class="one_fifth aliright last">
				<div class="pricing">
					<h4 class="vps_value"><?php echo esc_attr( $items[ $value - 1 ]->cost );?><?php echo ( !empty( $per_label ) )? '<span class="per_text">' . $per_label . '</span>' : '';?></h4>
					<a href="<?php echo esc_attr( $items[ $value - 1 ]->link);?>" class="but_small1 vps-order"><?php echo esc_attr( $btn_label );?></a>
				</div>
			</div>
		</div>
	</div>
</div>

<?php

$output = ob_get_clean();
global $king;
$king->ext['pr']( $output );