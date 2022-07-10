<?php
global $king;
$domain = $king->bag['domain'];
$url_action = $king->bag['url_action'];
$suggest_domains = $king->bag['suggest_domains'];

$search_result_method = '';

if( isset( $king->cfg['search_select_method'] ) )
{
	if( $king->cfg['search_select_method'] == '2' ){
		$url_action = rtrim( $king->cfg['search_whmcs_url'] ,'/').'/domainchecker.php';
	}else if ( $king->cfg['search_select_method'] == '3'){
		$url_action = $king->cfg['search_custom_url'];
	}
}
$custom_param = 'domains[]';

if( isset($king->cfg['custom_param']) && $king->cfg['custom_param'] !='' )
	$custom_param = $king->cfg['custom_param'];

?>
<div class="content-result">
	<strong class="_dm-r00 domain-taken">
	<?php
		/* translators: 1: comment author, 2: date and time */
		printf( 
			__( 'Sorry, <span>%1$s</span> is taken.', 'arkahost' ),
			esc_attr($domain)
		);
	?>
	 <a class="whois_view" href="javascript:;" data-domain="<?php echo esc_attr($domain);?>">Whois</a>
	</strong>
	<div class="suggest_domain">
		<div class="title"><h3><?php echo __('You may want to check:', 'arkahost');?></h3></div>
		<?php
		if(count($suggest_domains)){
			foreach($suggest_domains as $d){
				$_whois = '';
				$status_text = __('Taken','arkahost');
				if($d['status'] == 'taken'){
					$_whois = '<a class="whois_view" href="javascript:;" data-domain="'.$d['domain'].'">Whois</a>';
				}else{
					$status_text = __('Available','arkahost');
					$_whois = '<a class="select_this_domain" href="javascript:;" data-domain="'.$d['domain'].'">'. __('Select','arkahost') . '</a>';
				}
				
				$king->ext['pr']( $king->esc_js('<div class="domain domain-'. esc_attr($d['status']) .'">
					<strong>'. $d['domain'] .'</strong> <span class="status '.$d['status'].'">'. $status_text .'</span> <div class="view_whois">'. $_whois .'</div>
				</div>') );
			}
		}?>
	</div>
	<form id="select_this_domain" class="select_domain" method="POST" action="<?php echo esc_attr($url_action);?>">
		<input type="hidden" class="domains_val" name="<?php echo esc_attr($custom_param);?>" value="<?php echo esc_attr($domain);?>">
        <input type="hidden" class="domainsregperiod_val" name="domainsregperiod[<?php echo esc_attr($domain);?>]" value="1">
		<input type="hidden" value="<?php echo esc_attr($domain);?>" name="domain" class="bigfield">
	</form>
</div>
