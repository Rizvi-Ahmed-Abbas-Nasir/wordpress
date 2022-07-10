<?php

global $king;
$atts = $king->bag;

$domain = $atts['domain'];
?>
<div class="arkahost-result">
    <div class="arkahost-search-actions">
        <?php
        if($atts['status'] == 'available'):
        ?>
            <a href="#" class="arkahost-adv-search-btn" data-action="buynow" data-domain="<?php $king->ext['pr']( $atts['domain'] );?>"><?php echo __( 'Add to cart', 'arkahost' ); ?></a>
        <?php else:?>
            <a class="arkahost-adv-search-btn" href="#" data-action="whois" data-domain="<?php echo esc_attr($domain);?>"><?php echo __( 'Whois', 'arkahost' ); ?></a>
            <a class="arkahost-adv-search-btn" href="#" data-action="transfer" data-domain="<?php echo esc_attr($domain);?>"><?php echo __( 'Transfer', 'arkahost' ); ?></a>
        <?php endif;?>
    </div>
    <span class="arkahost-domainame"><?php echo esc_attr($domain);?></span>
    <span class="arkahost-status arkahost-domain-<?php echo esc_attr($atts['status']);?>">
        <?php
        if($atts['status'] == 'available'):
        ?>
            <?php echo __( 'Yes! Domain is available.', 'arkahost' ); ?>
        <?php else:?>
            <?php echo __( 'Sorry! Domain is taken.', 'arkahost' ); ?>
        <?php endif;?>
    </span>
</div>
