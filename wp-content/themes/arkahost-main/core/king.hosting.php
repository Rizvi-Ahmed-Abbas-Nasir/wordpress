<?php
/*
	Author: King Theme
*/

class King_Hosting{

    protected $whois = array();

	//Max request search domain in ($time_for_request) second
	public $max_request = 10;
	public $time_for_request = 10;
	protected $request_count;

	/*-----------------------------
	 function __construct()
	 Run for default
	 ----------------------------*/
    public function __construct($debug = false){

		global $king;

		$this->init();
		$this->whois = $this->whois();
    }


	/*-----------------------------
	 function init()
	 ----------------------------*/
	public function init(){

		global $king;

		add_action('wp_head', array(&$this, 'vars_js'));
		add_action('wp_ajax_nopriv_king_search_domain', array(&$this, 'ajax_search_domain'));
		add_action('wp_ajax_king_search_domain', array(&$this, 'ajax_search_domain'));
        add_action('wp_ajax_nopriv_king_adv_search_domain', array(&$this, 'adv_ajax_search'));
		add_action('wp_ajax_king_adv_search_domain', array(&$this, 'adv_ajax_search'));

		add_action('wp_ajax_nopriv_king_get_whois', array(&$this, 'ajax_get_whois'));
		add_action('wp_ajax_king_get_whois', array(&$this, 'ajax_get_whois'));

		add_action( 'wp_enqueue_scripts', array(&$this, 'enqueue_script'), 1 );




	}


	public function vars_js(){

        global $king;

		$js_dir = THEME_URI.'/assets/js/';

        $url_action = rtrim(get_permalink(get_option("cc_whmcs_bridge_pages")),"/")."/?ccce=cart&a=add&domain=register";
		$url_verifyimage = rtrim(get_permalink(get_option("cc_whmcs_bridge_pages")),"/")."/?ccce=verifyimage";

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


		echo '<script type="text/javascript">
		/* <![CDATA[ */
		var king_hosting_params = {"adv_custom_field" : "' .$custom_param. '", "arkahost_adv_url":"' .$url_action. '", "home_url":"'. home_url() .'", "ajax_url":"'. admin_url('admin-ajax.php') .'", "hosting_js":"'.$js_dir.'king.hosting.js"};
		/* ]]> */
		</script>';
	}

	/*-----------------------------
	 Add new script and style for hosting function
	 ----------------------------*/
	public function enqueue_script(){

		global $king;

		$css_dir = THEME_URI.'/assets/css/';
		$js_dir = THEME_URI.'/assets/js/';


		wp_enqueue_style('king-hosting', king_child_theme_enqueue( $css_dir.'king-hosting.css'  ), false, KING_VERSION );

		wp_register_script('king-hosting', king_child_theme_enqueue( $js_dir.'king.hosting.js' ), false, KING_VERSION, true );
		wp_enqueue_script('king-hosting');

	}

	/*------------------------------------
	 Get whois server
	 -----------------------------------*/
	public function whois(){
		if ( empty( $this->whois ) ) {
			locate_template( 'core'.DS.'inc'.DS.'whois-server.php', true  );
			$this->whois = apply_filters( 'king_theme_whois_server', king_get_whois_server() );
		}
		return $this->whois;
	}

	/*------------------------------------
	Get whois info
	-----------------------------------*/
	public function get_whois_info($domain, $echo = false){
		global $king;
		if($echo){
			$king->ext['pr']($this->server_response($domain));
		}else{
			return $this->server_response($domain);
		}
	}

	/*------------------------------------
	Check is tld supported
	Return @true|false
	-----------------------------------*/
	public function is_tld_supported($domain){
		$_tld = $this->getTld($domain);

		$tld_supported = array();
		foreach($this->whois as $tld => $info){
			$tld_supported[] = $tld;
		}

		if(in_array($_tld, $tld_supported))
			return true;
		return false;
	}

	/*------------------------------------
	 Get response from server
	 -----------------------------------*/
	public function server_response($domain){
		global $king;

		$domain = strtolower($domain);

		$server = $this->whois[$this->getTld($domain)][0];

		$connection = $king->ext['fso']($server, 43, $errno, $errstr, 30);
		if (!$connection) return false;

		fputs($connection, $domain."\r\n");

		$response_text = '';
		while(!feof($connection)) {
			$response_text .= @fgets($connection,128).'<br />';
		}

		$king->ext['fc']($connection);

		return $response_text;
	}

    /*------------------------------------
	 Check available domain
	 @return true|false
	 -----------------------------------*/
    public function is_available( $domain ){
        global $king;

		$response_text = $whois = $this->server_response($domain);

		$response_text = preg_replace(
			array(
				'/ {2,}/',
				'/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s'
			),
			array(
				' ',
				''
			),
			$response_text
		);

		$tld = $this->getTld($domain);
		$response_text = strtolower(trim(strip_tags( $response_text )));
		$match_text = strtolower( trim($this->whois[ $tld ][1] ) );

		if( isset($this->whois[ $tld ][2]) ){
			$not_match_text = strtolower( trim($this->whois[ $tld ][2]) );

			if ( $response_text == $not_match_text ){
				$status = false;
			}
		}else if ( $response_text ==  $match_text)
			$status = true;
		else if (strpos($response_text, $match_text) > -1){
			$status = true;
		}
		else{
			$status = false;
		}

        return array(
            'status' => $status,
            'whois' => $king->ext['bd']($whois)
        );
    }

	/*------------------------------------
	 Check available domain
	 @return true|false
	 -----------------------------------*/
    public function is_domain_available( $domain ){
        global $king;

		$response_text = $this->server_response($domain);

		$response_text = preg_replace(
			array(
				'/ {2,}/',
				'/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s'
			),
			array(
				' ',
				''
			),
			$response_text
		);
		$tld = $this->getTld($domain);
		$response_text = strtolower(trim(strip_tags( $response_text )));
		$match_text = strtolower( trim($this->whois[ $tld ][1] ) );

		if( isset($this->whois[ $tld ][2]) ){
			$not_match_text = strtolower( trim($this->whois[ $tld ][2]) );

			if ( $response_text == $not_match_text ){
				return false;
			}
		}


		if ( $response_text ==  $match_text)
			return true;

		if (strpos($response_text, $match_text) > -1){

			return true;
		}

		else{
			return false;
		}
    }


	/*------------------------------------
	 Get Tld
	 -----------------------------------*/
    public function getTld($domain)
    {
		preg_match("/[a-z0-9\-]{1,63}\.([a-z\.]{2,6}\.[a-z\.]{2,6}|[a-z\.]{2,6})$/", $domain, $_domain_tld);
        return $_domain_tld[1];
    }


	public function ajax_get_whois(){
		$domain = $_POST['domain'];

		$results_html = $this->get_whois_info($domain);

		$output = array(
			'status' 		=> 'ok',
			'results_html' 	=> $results_html
		);

		wp_send_json($output);
	}

    public function adv_ajax_search(){

        global $king;
		//check_ajax_referer( 'ajax-check-domain-nonce', 'security' );

		$domain = strtolower($_POST['domain']);
		$suggestion = isset($_POST['suggestion']) ? true : false;

		//Protected request check domain
		$this->protected_search_domain();

		if(!$this->is_tld_supported($domain)){

			$output = array(
				'status' 		=> 'no_support',
				'suggestion' 		=> $suggestion,
				'basename' 			=> $this->get_basename($domain),
				'results_html' 			=> 'ok',
                'domain' => $domain
			);

			wp_send_json($output);

			die();
		}

		$results_html = '';
		$url_action = rtrim(get_permalink(get_option("cc_whmcs_bridge_pages")),"/")."/?ccce=cart&a=add&domain=register";
		$url_verifyimage = rtrim(get_permalink(get_option("cc_whmcs_bridge_pages")),"/")."/?ccce=verifyimage";

		$available = $this->is_available($domain);
        $available['status'] = ($available['status'])? 'available' : 'taken';
        $available['basename'] = $this->get_basename($domain);

        $available['suggestion'] = $suggestion;
        $available['domain'] = $domain;

        $king->bag = $available;

        ob_start();

            if ( locate_template( 'templates'.DS.'shortcode' . DS. 'adv_domain_checker' . DS . 'result.php' ) != '' ){
                get_template_part( 'templates'.DS.'shortcode' . DS. 'adv_domain_checker' . DS . 'result' );
            }else
                echo '<p class="king-error">Domain search : adv_domain_checker/result.php ' . __( 'template not found', 'arkahost' ) . '</p>';

        $available['results_html'] = ob_get_contents();
        ob_end_clean();

		wp_send_json($available);

    }

	/*------------------------------------
	 ajax_search_domain()
	 -----------------------------------*/
	public function ajax_search_domain(){
		global $king;
		check_ajax_referer( 'ajax-check-domain-nonce', 'security' );

		$domain = strtolower($_POST['domain']);

		//Protected request check domain
		$this->protected_search_domain();

		if(!$this->is_tld_supported($domain)){
			$results_html = '<div class="content-result">
				<strong class="_dm-r00 domain-not-support">' . __('Sorry, that name is not available for registration. Please try again.', 'arkahost') . '</strong>
			</div>';

			$output = array(
				'status' 		=> __('Not support', 'arkahost'),
				'tld' 			=> $this->getTld($domain),
				'results_html'	=> $results_html
			);

			wp_send_json($output);

			die();
		}

		$results_html = '';
		$url_action = rtrim(get_permalink(get_option("cc_whmcs_bridge_pages")),"/")."/?ccce=cart&a=add&domain=register";
		$url_verifyimage = rtrim(get_permalink(get_option("cc_whmcs_bridge_pages")),"/")."/?ccce=verifyimage";

		$available = $this->is_domain_available($domain);

		if( $available ){
			$king->bag = array(
				'domain' => $domain,
				'url_action' => $url_action,
				'url_verifyimage' => $url_verifyimage,
			);
			$status = 'available';

			ob_start();

				if ( locate_template( 'templates'.DS.'domains' . DS . 'ajax_result_available.php' ) != '' ){
					get_template_part( 'templates'.DS.'domains' . DS . 'ajax_result_available' );
				}else
					echo '<p class="king-error">Domain search : ajax_result_available.php ' . __( 'template not found', 'arkahost' ) . '</p>';

			$results_html = ob_get_contents();

			ob_end_clean();
		}else{
			$status = 'taken';
			$suggest_domains = $this->suggest_domain($domain);
			$king->bag = array(
				'domain' => $domain,
				'url_action' => $url_action,
				'url_verifyimage' => $url_verifyimage,
				'suggest_domains' => $suggest_domains,
			);
			ob_start();

				if ( locate_template( 'templates'.DS.'domains' . DS . 'ajax_result_taken.php' ) != '' ){
					get_template_part( 'templates'.DS.'domains' . DS . 'ajax_result_taken' );
				}else echo '<p class="king-error">Domain search : ajax_result_taken.php ' . __( 'template not found', 'arkahost' ) . '</p>';

			$results_html = ob_get_contents();

			ob_end_clean();
		}

		$output = array(
			'status' 		=> $status,
			'results_html' 	=> $results_html,
			'request_count' => $_SESSION['domain_request_count']
		);

		wp_send_json($output);
	}


	/*------------------------------------
	 suggest_domain()
	 -----------------------------------*/
	public function suggest_domain($domain){
	    global $king;
		$current_tld = '.'.$this->getTld($this->url_to_domain($domain));
		$basename = $this->get_basename($domain);

        $sg_tld_arr = array('.com', '.net', '.org', '.info', '.us', '.biz');

        if( isset( $king->cfg['search_sg_tld'] )  && $king->cfg['search_sg_tld'] != '' )
        {
            $domain_strs = $king->cfg['search_sg_tld'];
            $domain_strs = preg_replace('/\s+/', '', $domain_strs);
            $sg_tld_arr = explode(',', $domain_strs);
        }


		$suggest_domain_arr = array();

		foreach($sg_tld_arr as $tld){
			if($current_tld != $tld){
				$sg_domain = $basename.$tld;

				if($this->is_domain_available($sg_domain)){
					$suggest_status = 'available';
				}else{
					$suggest_status = 'taken';
				}

				$suggest_domain_arr[] = array(
					'domain' => $sg_domain,
					'status' => $suggest_status
				);
			}
		}

		return $suggest_domain_arr;
	}


	/*------------------------------------
	 url_to_domain()
	 -----------------------------------*/
	public function url_to_domain($url){
		$host = @parse_url($url, PHP_URL_HOST);
		if (!$host)
			$host = $url;
		if (substr($host, 0, 4) == "www.")
			$host = substr($host, 4);
		if (strlen($host) > 50)
			$host = substr($host, 0, 47) . '...';
		return $host;
	}


	/*------------------------------------
	 get_basename()
	 -----------------------------------*/
	public function get_basename($url_domain){
		$domain = $this->url_to_domain($url_domain);
		return basename($domain, '.'.$this->getTld($domain));
	}

	/*------------------------------------
	 protected_search_domain()
	 -----------------------------------*/
	private function protected_search_domain(){
		global $king;

		if(!isset($_SESSION['domain_request_count'])){
			$_SESSION['domain_request_count'] = 0;
		}

		if( !isset($_SESSION['search_first_time']) ){
			$_SESSION['search_first_time'] = time();
		}else{
			if(time() - $_SESSION['search_first_time'] < $this->time_for_request){
				$_SESSION['domain_request_count'] = intval($_SESSION['domain_request_count']) + 1;
			}else{
				$_SESSION['domain_request_count'] = 0;
				$_SESSION['search_first_time'] = time();
			}
		}

		if($_SESSION['domain_request_count'] > $this->max_request){
			//Code ban ip and exit

			$output = array(
				'status' 		=> 'your ip banned!',
				'reason' 		=> 'too much request in short time.',
				'request_count' => $_SESSION['domain_request_count'].'/'.$this->time_for_request.'s'
			);

			wp_send_json($output);

			die();
		}
	}

}


class King_Whmcs{

	public $session;

	public function __construct($debug = false){
		
		global $king;
		
		if( session_id() == '' )
			session_start();
		
		$this->session = $_SESSION;

		$cc_whmcs_bridge_version=get_option("cc_whmcs_bridge_version");
		if ($cc_whmcs_bridge_version) {
			$king->ext['rf']('the_content', 'cc_whmcs_bridge_content', 10);
			add_filter('the_content', array($this, 'whmcs_bridge_content'), 10, 3);
		}
	}


	public function whmcs_bridge_content($content) {
		global $cc_whmcs_bridge_content,$post;

		if (!is_page()) return $content;

		$_content = $content;

		$cf=get_post_custom($post->ID);
		if (isset($_REQUEST['ccce']) || (isset($cf['cc_whmcs_bridge_page']) && $cf['cc_whmcs_bridge_page'][0]==WHMCS_BRIDGE_PAGE)) {
			if (!$cc_whmcs_bridge_content) { //support Gantry framework
				$cc_whmcs_bridge_content=cc_whmcs_bridge_parser();
			}
			if ($cc_whmcs_bridge_content) {
				$content='';
				ob_start();
				if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('whmcs-top-page') ) :
				endif;
				$content.=ob_get_clean();
				$content.='<div id="bridge">';
				$content.=$cc_whmcs_bridge_content['main'];
				$content.='</div><!--end bridge-->';
				ob_start();
				if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('whmcs-bottom-page') ) :
				endif;
				$content.=ob_get_clean();
				if (get_option('cc_whmcs_bridge_footer')=='Page') $content.=cc_whmcs_bridge_footer(true);
			}
			if(!isset($_REQUEST['ccce'])) $content .= $_content;
		}

		return $content;
	}


	public function is_client_loggedin(){
		if($this->is_bridge_actived() && $this->is_bridge_sso_actived()){
			$_session = $this->session;

			if(isset($_session['whmcs-bridge-sso']['cookie-array'])){
				$whmcs_cookie = $_session['whmcs-bridge-sso']['cookie-array'];
			}else{
				$whmcs_cookie = array('WHMCSUser' => '');
			}

			if(isset($whmcs_cookie['WHMCSUser'])){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	public function get_bridge_page_id(){
		$whmcs_page_id = get_option("cc_whmcs_bridge_pages");

		if(isset($whmcs_page_id)) return $whmcs_page_id;
		else return false;
	}

	public function is_bridge_actived(){
		return $this->isPluginActive('whmcs-bridge/bridge.php');
	}

	public function is_bridge_sso_actived(){
		return $this->isPluginActive('whmcs-bridge-sso/sso.php');
	}

	public function isPluginActive($plugin){
		if ( in_array( $plugin , apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return true;
		}
		else {
			return false;
		}
	}

}

new King_Hosting();
global $king_whmcs;
$king_whmcs = new King_Whmcs();
