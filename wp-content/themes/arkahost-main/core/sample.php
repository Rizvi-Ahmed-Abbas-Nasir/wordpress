<?php

/**
*
* (c) king-theme.com 
*
*/

?>
<div class="style-1" id="theme-setup-section">
	<section class="wrap col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
		<div class="row" style="padding: 20px">
			<section class="content col-md-12">
				<?php 
					
					if( !empty( $_POST['importSampleData'] ) ){
				
				?>
				<img src="<?php echo THEME_URI; ?>/core/assets/images/king-gray.png" height="50" class="pull-right" />
				<div id="errorImportMsg" class="p" style="width:100%;"></div>
				<div id="importWorking">
					<h2 style="color: #30bfbf;">
						<?php _e('The importer is working', 'arkahost' ); ?>
					</h2>
					<p>
						<?php _e('Please do not navigate away while importing. Import speed depends on internet connection.', 'arkahost' ); ?>
					</p>
					<i>
						<?php _e('Status', 'arkahost' ); ?>: 
						<span id="import-status" style="font-size: 12px;color: maroon;">
							<?php _e('Downloading the demo package, it may take a few minutes...', 'arkahost' ); ?>...
						</span>
					</i>
					<div class="progress" style="height:35px;">
				    	<div class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" id="importStatus" style="width: 0%;height:35px;line-height: 35px;">
					    	0% Complete
					    </div>
				    </div>
				    <center>
					   Powered by <a href="http://king-theme.com" target=_blank>King-Theme.com</a>
				    </center>
				</div>
			    <script type="text/javascript">
			    	
			    	var docTitle = document.title;
			    	var el = document.getElementById('importStatus');
			    	
			    	function istaus( is ){
			    		
			    		var perc = parseInt( is*100 )+'%';
			    		el.style.width = perc;
			    		
			    		if( perc != '100%' ){
			    			el.innerHTML = perc+' Complete';
			    		}	
			    		else{
				    		el.innerHTML = 'Initializing...';	
			    		}
			    		document.title = el.innerHTML+'  - '+docTitle;
			    	}
			    	
			    	function tstatus( t ){
			    		document.getElementById('import-status').innerHTML = t;
			    	}
			    	
			    	function iserror( msg ){
				    	document.getElementById('errorImportMsg').innerHTML += '<div class="alert alert-danger">'+msg+'</div>';
				    	document.getElementById('errorImportMsg').style.display = 'inline-block';
			    	}
			    </script>
			    						
			<?php	
			
				get_template_part( 'core'.DS.'sample'.DS.'king.importer' );						
				
			?>		
				<script type="text/javascript">document.getElementById('importWorking').style.display = 'none';</script>
				
				<h2 style="color: #30bfbf;"><?php _e('Import has completed', 'arkahost' ); ?></h2>
				<div class="h4">
					<p><?php _e('We will redirect you to homepage after', 'arkahost' ); ?> <span id="countdown">10</span> seconds.  
						<?php _e('You can', 'arkahost' ); ?>  
						<a href="#" onclick="clearTimeout(countdownTimer)">
							<?php _e('Stop Now', 'arkahost' ); ?>
						</a>
						 <?php _e('or go to', 'arkahost' ); ?> 
						<a href="<?php echo admin_url('admin.php?page='.strtolower(THEME_NAME).'-panel'); ?>" onclick="clearTimeout(countdownTimer)">
							<?php _e('Theme Panel', 'arkahost' ); ?>
						</a>
					</p>
				</div>		
				<div class="p">
					<div class="updated settings-error below-h2">
						<p></p>
						<h3><?php _e('Import Successful', 'arkahost' ); ?></h3>
						<p><?php _e('All done. Have fun!', 'arkahost' ); ?></p>
						<p></p>
						<p></p>
					</div>
				</div>		
					
				<?php	
					
					}else{
					
				?>
				
				<form action="" method="post" onsubmit="doSubmit(this)">
					<img src="<?php echo THEME_URI; ?>/core/assets/images/king-gray.png" height="50" class="pull-right" />
					<h2 style="color: #30bfbf;"><?php _e('Welcome to', 'arkahost' ); ?> <?php echo THEME_NAME; ?> </h2>
					<div class="h4">
						<p><?php _e('Thank you for using the', 'arkahost' ); ?> <?php echo THEME_NAME; ?> Theme.</p>
					</div>	
					<div class="bs-callout bs-callout-info">
						<h4><?php _e('Sample Data', 'arkahost' ); ?></h4>			
						<div class="p">
							<p>
							<?php _e('Let our custom demo content importer do the heavy lifting. Painlessly import settings, layouts, menus, colors, fonts, content, slider and plugins. Then get customising', 'arkahost' ); ?>
							</p>
							<?php _e('Notice: Before import, Make sure your website data is empty (posts, pages, menus...etc...)', 'arkahost' ); ?> 
							<br />
							<?php _e('We suggest you use the plugin', 'arkahost' ); ?> <a href="<?php echo esc_url(SITE_URI); ?>/wp-admin/plugin-install.php?tab=plugin-information&plugin=wordpress-reset&from=<?php echo strtolower(THEME_NAME); ?>-theme&TB_iframe=true&width=800&height=550" class="thickbox" title="Install Wordpress Reset">"Wordpress Reset"</a> <?php _e('to reset your website before import', 'arkahost' ); ?>. <br />
							<i>( <?php _e('After install plugin', 'arkahost' ); ?> <a href="<?php echo esc_url(SITE_URI); ?>/wp-admin/plugin-install.php?tab=plugin-information&plugin=wordpress-reset&from=<?php echo strtolower(THEME_NAME); ?>-theme&TB_iframe=true&width=800&height=550" class="thickbox" title="Install Wordpress Reset">"Wordpress Reset"</a> go to: Tool -> reset )</i>
						</div>		
					</div>	
					
					<div class="p">
						<p>
							<label class="label-form-sel">
								<?php _e('We required using 4 plugins', 'arkahost' ); ?> ( ArkaHost Helper, Visual Composer, Revolution Slider & Contact Form 7  )
							</label>
							<br />
							<button id="submitbtn2" onclick="doSubmit2()" class="btn submit-btn button">
								<?php _e('Install Plugins Only', 'arkahost' ); ?>
							</button>
							<input type="hidden" value="" name="pluginsOnly" id="pluginsOnly" />
							<br />
							<br />
							<i class="sub-label-form-sel">
								<?php _e('Plugins will be installed automatically during Import Sample Data.<br /> You also able to find the installation files in the directory', 'arkahost' ); ?>: wp-content/themes/<?php echo strtolower(THEME_NAME); ?>/core/sample/plugins
							</i>
						</p>
					</div>
										
					<div class="p">
						<p>
							<input type="submit" id="submitbtn" value="Import All Demos & Plugins" class="btn submit-btn" />
							<h3 id="imp-notice">
								<img src="<?php echo THEME_URI; ?>/core/assets/images/loading.gif" /> 
								<?php _e('Please do not navigate away while importing', 'arkahost' ); ?>
								<br />
								<span style="font-size: 10px;float: right;margin: 5px 7px 0 0;">
									<?php _e('It may take up to 10 minutes', 'arkahost' ); ?>
								</span>
							</h3>
							
							<input type="hidden" value="1" name="importSampleData" />
						</p>
					</div>
				</form>		
				<?php } ?>
			</section><!-- /content -->
		</div><!-- /row -->

		<div class="row">
			<section class="col-md-12">
				<div class="footer">
					<?php echo THEME_NAME; ?> <?php _e('version', 'arkahost' ); ?> 
					<?php global $king_options; echo KING_VERSION; ?> &copy; by King-Theme
					|  <?php _e('Question?', 'arkahost' ); ?> 
					<a href="<?php echo esc_url( 'http://help.king-theme.com' ); ?>">help.king-theme.com</a>
					
					<a onclick="if(!confirm('<?php _e('Notice: If you do not install plugins and sample data, your site will not work fully functional. Click Ok if you want to dismiss.', 'arkahost' ); ?>')){return false;}else{clearTimeout(countdownTimer);return true;}" class="pull-right link btn btn-default" class="btn btn-default" href="<?php echo admin_url('admin.php?page='.strtolower(THEME_NAME).'-panel'); ?>">
						<?php _e('Dismiss', 'arkahost' ); ?> &nbsp; <i class="fa fa-sign-out"></i>
					</a>
				</div>
			</section><!-- /subscribe -->
		</div><!-- /row -->
	</section>
</div>		
<script type="text/javascript">


	function doSubmit( form ){
		var btn = document.getElementById('submitbtn');
		btn.className+=' disable';
		btn.disabled=true;
		btn.value='Importing.....';
		document.getElementById('imp-notice').style.display = 'block';
	}
	function doSubmit2(){
		jQuery('#pluginsOnly').val('ON');
		jQuery('#submitbtn').trigger('click');
	}
	var countdown = document.getElementById('countdown');
	var countdownTimer = null;
	if( countdown ){
		
		function count_down( second ){
			
			second--;
			countdown.innerHTML = second;
			if(second>0){
				countdownTimer = setTimeout('count_down('+second+')', 1000);
			}else{
				window.location = '<?php echo SITE_URI; ?>';
			}	
		}

		count_down( 10 );
		
	}
	
	
	
</script>  