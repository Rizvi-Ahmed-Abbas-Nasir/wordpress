/***** Ajax login script ******/
var $ = jQuery;
$(document).ready(function($){
	
	$('#king-form .btn-login').off('click').on('click', function(e){
		console.log('call');
		e.preventDefault();
		$('#king-form p.status').show().text(ajax_user_object.loadingmessage);
		$.ajax({
			url: ajax_user_object.ajaxurl,
			data: $('#king-form').serialize(),
			type: 'POST',
			dataType: 'json',
			success: function(response){
				$('#king-form p.status').show().text(response.message);
				if (response.loggedin == true){
	                document.location.href = ajax_user_object.redirecturl;
	            }
			}
		});
	});
	
	$('#king-form>.btn-resetpwd').on( 'click', function(e){
		e.preventDefault();
		$('#king-form p.status').show().text(ajax_user_object.loadingmessage);
		$.ajax({
			url: ajax_user_object.ajaxurl,
			data: $('#king-form').serialize(),
			type: 'POST',
			dataType: 'json',
			success: function(response){
				$('#king-form p.status').show().text(response.message);
				if(response.status){
					$('input[name="email"]').val('');
				}
			}
		});
	});
	
	$('#king_form>.btn-register').on( 'click', function(){
		$('#king_form p.status').show().text(ajax_user_object.loadingmessage);
		$.ajax({
			url: ajax_user_object.ajaxurl,
			data: $('#king_form').serialize(),
			type: 'POST',
			dataType: 'json',
			success: function(response){
				$('#king_form p.status').show().text(response.message);
				if (response.loggedin == true){
	                document.location.href = ajax_user_object.redirecturl;
	            }
			}
		});
	});
});