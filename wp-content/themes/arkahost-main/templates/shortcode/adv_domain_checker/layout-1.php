<?php

global $king;
$atts = $king->bag['atts'];
$css = $atts['css'];
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
$placeholder = ($atts['search_placeholder']!='') ? $atts['search_placeholder'] : __('Enter your domain name here...', 'arkahost');
$id_rnd = rand(0,9999);
?>
<div class="arkahost-advance-search <?php echo esc_attr( $css_class ); ?>">
    <h5 class="white caps"><?php echo esc_attr($atts['title']);?></h5>
    <div class="arkahost-adv-search-content">
        <form class="arkahost-advance-search-form" data-default="<?php echo esc_attr($atts['default']);?>" data-suggestions="<?php $king->ext['pr']( $atts['suggestions'] );?>" data-url="">
             <input name="domain" class="arkahost-domain" placeholder="<?php $king->ext['pr']( $placeholder);?>"/><input type="submit" name="submit" class="arkahost-submit" value="<?php $king->ext['pr']( $atts['text_button'] );?>"/>
             <!-- mfunc DOMAIN_SEARCH -->
     		<?php echo wp_nonce_field( 'ajax-check-domain-nonce', 'security', true, false );?>
     		<!-- /mfunc DOMAIN_SEARCH -->
        </form>
        <div class="arkahost-suggestions">
            <?php
                $suggestions = explode(',', $atts['suggestions']);
                $i = 0;
                foreach ($suggestions as $domain) {
                    $domain = trim($domain);
                    $checked = '';
                    if($i == 0)
                        $checked = ' checked="checked"';
                ?>
                <div class="arkahost-checkbox">
    				<input type="checkbox" name="suggestion" class="arkahost-action_check" value="<?php $king->ext['pr']( $domain ;?>" id="arkahost-suggestions-<?php $king->ext['pr']( $domain ); ?>-<?php echo esc_attr($id_rnd);?>" <?php $king->ext['pr']( $checked ); ?>>
    				<label for="arkahost-suggestions-<?php $king->ext['pr']( $domain ); ?>-<?php echo esc_attr($id_rnd);?>"><?php echo esc_attr($domain);?> <em class="check">
    					<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="14px" height="15px" viewBox="0 0 12 13" enable-background="new 0 0 12 13" xml:space="preserve">
    						<path fill="#86c724" d="M0.211,6.663C0.119,6.571,0.074,6.435,0.074,6.343c0-0.091,0.045-0.229,0.137-0.32l0.64-0.64 c0.184-0.183,0.458-0.183,0.64,0L1.538,5.43l2.515,2.697c0.092,0.094,0.229,0.094,0.321,0l6.13-6.358l0.032-0.026l0.039-0.037 c0.186-0.183,0.432-0.12,0.613,0.063l0.64,0.642c0.183,0.184,0.183,0.457,0,0.64l0,0l-7.317,7.592 c-0.093,0.092-0.184,0.139-0.321,0.139s-0.228-0.047-0.319-0.139L0.302,6.8L0.211,6.663z"/>
    						</svg></em>
    				</label>
    				<em></em>
    			</div>
                <?php
                $i++;
                }
             ?>
        </div>
        <div class="arkahost-search-results"></div>
    </div>
</div>
