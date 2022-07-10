<?php
global $king;	
$atts = $king->bag['atts'];
$height_box = str_replace('px','',$atts['height_box']);
?>
<div class="flips2" <?php if(!empty($height_box)) echo 'style="height:'.esc_attr($height_box).'px!important;"'?>>

    <div class="flips2_front flipscont2">
        <?php echo wp_get_attachment_image( $atts['img'], $atts['img_size']); ?>
    </div>

    <div class="flips2_back flipscont1" <?php if(!empty($height_box)) echo 'style="height:'.esc_attr($height_box).'px!important;"'?>>
        <?php echo king::esc_js($atts['des']);?>
    </div>

</div>

