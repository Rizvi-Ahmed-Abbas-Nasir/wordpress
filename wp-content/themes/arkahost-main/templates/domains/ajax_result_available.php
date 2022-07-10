<?php
global $king;
$domain = $king->bag['domain'];
$url_action = $king->bag['url_action'];
$custom_param = 'domains[]';

if( isset($king->cfg['custom_param']) && $king->cfg['custom_param'] !='' )
	$custom_param = $king->cfg['custom_param'];

$search_result_method = '';

if( isset( $king->cfg['search_select_method'] ) )
{
	if( $king->cfg['search_select_method'] == '2' ){
		$url_action = rtrim( $king->cfg['search_whmcs_url'] ,'/').'/domainchecker.php';
	}else if ( $king->cfg['search_select_method'] == '3'){
		$url_action = $king->cfg['search_custom_url'];
	}
		
}

?>
<div class="content-result">
	<strong class="_dm-r00 domain-available">
	<?php
		/* translators: 1: comment author, 2: date and time */
		printf( 
			__( 'Yes! %1$s is available. Buy it before someone else does.', 'arkahost' ),
			esc_attr($domain)
		);
	?>
	
	</strong>
	<form class="select_domain" method="POST" action="<?php echo esc_attr($url_action);?>">
		<input type="hidden" value="<?php echo esc_attr($domain);?>" name="domain" class="bigfield">
		<input type="hidden" name="<?php echo esc_attr($custom_param);?>" value="<?php echo esc_attr($domain);?>">
        <input type="hidden" name="domainsregperiod[<?php echo esc_attr($domain);?>]" value="1">
		<input class="input_select" type="submit" name="select_domain" value="<?php _e('Select domain','arkahost');?>" />
	</form>
</div>
