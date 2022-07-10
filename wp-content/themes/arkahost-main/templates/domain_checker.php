<?php

global $king;
$atts = $king->bag['atts'];
$search_method = $search_whmcs_url = '';

if( isset( $king->cfg['search_method'] ) && $king->cfg['search_method'] == '2')
{
	$data_form = 'direct';
	$search_whmcs_url = rtrim( $king->cfg['search_whmcs_url'] ,'/').'/domainchecker.php';
}
else
{
	$search_whmcs_url = rtrim(get_permalink(get_option("cc_whmcs_bridge_pages")),"/")."/?ccce=cart&a=add&domain=register";
	$data_form = '';
}

?>
<div class="serch_area"><div class="check-domain-from animated eff-fadeIn delay-200ms">
	<?php echo rawurldecode($king->ext['bd'](strip_tags($atts['html_before'])));?>
	<form method="POST" class="search_domain_form" data-form="<?php echo esc_attr( $data_form );?>" action="<?php echo esc_attr( $search_whmcs_url );?>">
		<input class="enter_email_input domain_input" name="domain_input" id="domain_input" value="" placeholder="<?php echo esc_attr($atts['search_placeholder']);?>" type="text"/>
		<input name="search_domain" value="<?php echo esc_attr( $atts['text_button']) ;?>" class="input_submit" type="button"/>
		<!-- mfunc DOMAIN_SEARCH -->
		<?php echo wp_nonce_field( 'ajax-check-domain-nonce', 'security', true, false );?>
		<!-- /mfunc DOMAIN_SEARCH -->
		<input type="hidden" class="domain" value="" name="domain">
		<input type="hidden" class="domains" name="domains[]" value="">
        <input type="hidden" class="domainsregperiod" name="domainsregperiod[]" value="1">

	</form>
	<?php echo  rawurldecode($king->ext['bd'](strip_tags($atts['html_after'])));?>
</div>
<div id="domain_search_results"></div></div>
