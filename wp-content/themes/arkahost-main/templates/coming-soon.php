<?php
/**
 * (c) www.devn.co
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $king;

?>
<link href="<?php echo THEME_URI; ?>/assets/js/comingsoon/animations.min.css" rel="stylesheet" type="text/css" media="all" />
<link rel="stylesheet" media="screen" href="<?php echo THEME_URI; ?>/assets/js/comingsoon/coming.css" type="text/css" />

<script type="text/javascript" src="<?php echo THEME_URI; ?>/assets/js/comingsoon/jquery.bcat.bgswitcher.js"></script>

<div id="bg-body"></div>
<!--end -->
<div class="site_wrapper">
	<div class="comingsoon_page">
		<div class="container">
			<div class="topcontsoon">
				<?php
					$logo = $king->cfg['cs_logo'];
					if($logo){
						$logo = str_replace(array('%SITE_URI%', '%HOME_URI%'), array(SITE_URI, SITE_URI), $logo);
					}else{
						$logo = THEME_URI. '/assets/images/logo-white.png';
					}					
				?>
				<img height="45" src="<?php echo esc_attr($logo); ?>" alt="Logo" />
				<div class="clearfix">
				</div>
				<h5>
					<?php 
						$sologan = $king->cfg['cs_text_after_logo'];
						if($sologan){
							echo esc_html($sologan);
						}else{
							_e('We\'re Launching Soon', 'arkahost' ); 
						}
					?>
				</h5>
			</div>
			
			<?php
				$timedown = $king->cfg['cs_timedown'];
				if( empty($king->cfg['cs_timedown'])){
					$timedown = date("F d, Y H:i:s",strtotime("+1 week"));
				}
			?>
			
			<div class="countdown_dashboard">
				<div class="flipTimer">
				
					<div class="days"></div>
					<div class="hours"></div>
					<div class="minutes"></div>
					<div class="seconds"></div>
					
					<div class="clearfix"></div>
					
					<div class="fttext">DAYS</div>
					<div class="fttext">HRS</div>
					<div class="fttext">MIN</div>
					<div class="fttext">SEC</div>
				</div>
			</div>
			<div class="clearfix"></div>
			
			
			<div class="socialiconssoon">
    	
				<p class="white">
				<?php 
					$description = $king->cfg['cs_description'];
					if($description){
						echo esc_html($description);
					}else{
						_e("Our website is under construction. We'll be here soon with our new awesome site. Get best experience with this one.", 'arkahost' );
					}
				?>
				</p>
				
				<div class="clearfix margin_top3"></div>
				
				<form name="myForm" action="" onSubmit="return validateForm();" method="post">
				
				<input type="text" name="email" class="newslesoon" value="Enter email..." onFocus="if (this.value == 'Enter email...') {this.value = '';}" onBlur="if (this.value == '') {this.value = 'Enter email...';}" >
				<input type="submit" value="Submit" class="newslesubmit">
				
				</form>

				<div class="clearfix"></div>
				<div class="margin_top3"></div>

				<?php king::socials( 'comming-socials', 15, false ); ?>
				
			
			</div><!-- end section -->
			
			
		</div>
	</div>
</div>

<!-- ######### JS FILES ######### -->
<script type="text/javascript" src="<?php echo THEME_URI; ?>/assets/js/comingsoon/jquery.flipTimer.js"></script>
<!-- animations -->
<script src="<?php echo THEME_URI; ?>/assets/js/comingsoon/animations.min.js" type="text/javascript"></script>

<?php
	$srcBgArray = array();
	for($i=1; $i<=5; $i++){
		$var_name = 'cs_slider'.$i;
		if(!empty($king->cfg[$var_name])){
			array_push($srcBgArray, $king->cfg[$var_name]);
		}
	}
	
	$str_arr = array();
	if($srcBgArray){
		foreach($srcBgArray as $src){
			$str_arr[] = str_replace(array('%SITE_URI%', '%HOME_URI%'), array(SITE_URI, SITE_URI), $src);
		}
	}


	if(empty($str_arr)){
		$str_arr = array(
		"http://gsrthemes.com/arkahost/demo2/js/comingsoon/img-slider-1.jpg",
		"http://gsrthemes.com/arkahost/demo2/js/comingsoon/img-slider-2.jpg",
		"http://gsrthemes.com/arkahost/demo2/js/comingsoon/img-slider-3.jpg",
		);
	} 
?>

<script type="text/javascript">
jQuery(document).ready(function() {
  //Callback works only with direction = "down"
  jQuery('.flipTimer').flipTimer({ direction: 'down', date: '<?php echo esc_js($timedown); ?>', callback: function() { alert('times up!'); } });
});

	
var srcBgArray = [
<?php foreach($str_arr as $img) echo '"'.esc_html($img).'",';?>
];

jQuery(document).ready(function() {
  jQuery('#bg-body').bcatBGSwitcher({
    urls: srcBgArray,
    alt: 'Full screen background image',
    links: true,
    prevnext: true
  });
});
</script><!--end of bg-body script-->

<script type="text/javascript">
function validateForm() {
    var x = document.forms["myForm"]["email"].value;
    var atpos = x.indexOf("@");
    var dotpos = x.lastIndexOf(".");
    if (atpos<1 || dotpos<atpos+2 || dotpos+2>=x.length) {
        alert("Not a valid e-mail address");
        return false;
    }
}
</script>
