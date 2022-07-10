jQuery(document).ready(function(){


	if(jQuery('#last_tab').val() == ''){

		jQuery('.nhp-opts-group-tab:first').slideDown('fast');
		jQuery('#nhp-opts-group-menu li:first').addClass('active');

	}else{

		tabid = jQuery('#last_tab').val();
		jQuery('#'+tabid+'_section_group').slideDown('fast');
		jQuery('#'+tabid+'_section_group_li').addClass('active');

	}


	jQuery('input[name="'+king_opts.opt_name+'[defaults]"]').on( 'click', function(){
		if(!confirm(king_opts.reset_confirm)){
			return false;
		}
	});

	jQuery('.nhp-opts-group-tab-link-a').on( 'click', function(){
		relid = jQuery(this).attr('data-rel');

		jQuery('#last_tab').val(relid);

		jQuery('.nhp-opts-group-tab').each(function(){
			if(jQuery(this).attr('id') == relid+'_section_group'){
				jQuery(this).delay(150).fadeIn(300);
			}else{
				jQuery(this).fadeOut(150);
			}

		});

		jQuery('.nhp-opts-group-tab-link-li').each(function(){
				if(jQuery(this).attr('id') != relid+'_section_group_li' && jQuery(this).hasClass('active')){
					jQuery(this).removeClass('active');
				}
				if(jQuery(this).attr('id') == relid+'_section_group_li'){
					jQuery(this).addClass('active');
				}
		});
	});




	if(jQuery('#nhp-opts-save').is(':visible')){
		jQuery('#nhp-opts-save').delay(4000).slideUp('slow');
	}

	if(jQuery('#nhp-opts-imported').is(':visible')){
		jQuery('#nhp-opts-imported').delay(4000).slideUp('slow');
	}

	jQuery('input, textarea, select').change(function(){
		jQuery('#nhp-opts-save-warn').slideDown('slow');
	});


	jQuery('#nhp-opts-import-code-button').on( 'click', function(){
		if(jQuery('#nhp-opts-import-link-wrapper').is(':visible')){
			jQuery('#nhp-opts-import-link-wrapper').fadeOut('fast');
			jQuery('#import-link-value').val('');
		}
		jQuery('#nhp-opts-import-code-wrapper').fadeIn('slow');
	});

	jQuery('#nhp-opts-import-link-button').on( 'click', function(){
		if(jQuery('#nhp-opts-import-code-wrapper').is(':visible')){
			jQuery('#nhp-opts-import-code-wrapper').fadeOut('fast');
			jQuery('#import-code-value').val('');
		}
		jQuery('#nhp-opts-import-link-wrapper').fadeIn('slow');
	});


	jQuery('#nhp-opts-export-code-copy').on( 'click', function(){
		if(jQuery('#nhp-opts-export-link-value').is(':visible')){jQuery('#nhp-opts-export-link-value').fadeOut('slow');}
		jQuery('#nhp-opts-export-code').toggle('fade');
	});

	jQuery('#nhp-opts-export-link').on( 'click', function(){
		if(jQuery('#nhp-opts-export-code').is(':visible')){jQuery('#nhp-opts-export-code').fadeOut('slow');}
		jQuery('#nhp-opts-export-link-value').toggle('fade');
	});


	jQuery('#verify-purchase-key').on( 'click', function( e ){

		if( jQuery(this).closest('td').hasClass('verifying') )
			return;

		jQuery('#nhp-opts-form-wrapper').data({ 'go' : 'no' });

		var key = jQuery('#input-purchase-key').val();

		if( key == '' ){
			jQuery('#verify-purchase-status').css({color:'red'}).html('Error! Empty Key.');
			return false;
		}

		jQuery(this).closest('td').addClass('verifying');

		jQuery.post(

			ajaxurl,
			{
				action : 'verifyPurchase',
				code : key
			},
			function( result ){

				jQuery('#verify-purchase-wrp').removeClass('verifying');

				if( result == null )
				{
					jQuery('#verify-purchase-status').css({color:'red'}).html( 'Could not contact with server at this time. Please check your connection and try again.' );
				}
				else if( result.status == 0 )
				{
					jQuery('#verify-purchase-status').css({color:'red'}).html( result.message );
					jQuery('#verify-purchase-msg-wrp .msg-notice').addClass('active');
					jQuery('#verify-purchase-msg-wrp .msg-success').removeClass('active');
				}
				else
				{
					jQuery('#verify-purchase-status').css({color:'green'}).html( result.message );
					jQuery('#verify-purchase-msg-wrp .msg-notice').removeClass('active');
					jQuery('#verify-purchase-msg-wrp .msg-success').addClass('active');
				}
			}
		);

		e.preventDefault();
		return false;
	});

	jQuery("#input-purchase-key").on( 'keydown', function(e){
	    if( e.keyCode == 13 ){
		    jQuery('#verify-purchase-key').trigger('click');
	    	e.preventDefault();
	    	return false;
	    }
	});

	jQuery('#nhp-opts-form-wrapper').on('submit', function(){
		if( jQuery(this).data('go') != 'no' )
			return true;
		else{
			jQuery(this).data({ 'go' : '' });
			return false;
		}
	});

	jQuery('#theme-export-button').on( 'click', function(e){

		var form = jQuery('<form action="'+window.location.href+'" method="POST"><input name="doAction" type="hidden" value="export" /></form>');
		jQuery('body').append( form );
		form.trigger('submit');

		e.preventDefault();
		return false;

	});

	jQuery('#theme-import-button').on( 'click', function(e){

		var wrp = jQuery(this).closest('.king-file-upload');
		if( jQuery('#file-upload-to-import').val() == '' )
		{
			jQuery('#import-warning-msg')
				.html('Error! Please choose a file to import.')
				.animate({marginLeft:-10,marginRight:10}, 100)
				.animate({marginLeft:10,marginRight:-10}, 100)
				.animate({marginLeft:-5,marginRight:5}, 100)
				.animate({marginLeft:3,marginRight:-3}, 100)
				.animate({marginLeft:0,marginRight:0}, 100);
		}
		else
		{
			var form = jQuery('<form enctype="multipart/form-data" action="'+window.location.href+'" method="POST" style="display:none;"><input name="doAction" type="hidden" value="import" /><input type="text" name="option" value="'+wrp.find('input[name="import_type"]:checked').val()+'" /></form>');
			jQuery('body').append( form );
			form.append( jQuery('#file-upload-to-import') );
			form.trigger('submit');
		}

		e.preventDefault();
		return false;

	});

	/*remove label*/
	jQuery("#19_section_group.nhp-opts-group-tab table th").not("#19_section_group.nhp-opts-group-tab table table th").css("display", "none");
});

jQuery( window ).load(function(){

	var url = window.location.href;
	if( url.indexOf( '#' ) > -1 ){
		url = url.split('#')[1];
		if( url.indexOf('tab-') > -1 ){
			jQuery('#nhp-opts-group-menu li').eq( url.split('tab-')[1] ).find('a').trigger('click');
		}
	}

});
