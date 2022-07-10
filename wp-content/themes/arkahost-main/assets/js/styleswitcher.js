
function setActiveStyleSheet( id ) {
	
	var $ = jQuery;
	if( !$( '#style-selector .stylesheet-'+id ).get(0) ){
		var convert = {'red':'f62459','lightblue':'37c6f5','blue':'3183d7','green':'3fc35f','cyan':'35d3b7','orange':'ff6e41','pink':'fa3aab','purple':'c762cb','bridge':'a5d549','slate':'6b798f','yellow':'f2d438','darkred':'970001'};
		
		if( id != 'bridge' ){
			$( '#style-selector' ).append('<link rel="stylesheet" type="text/css" onload="this.disabled=false;" class="stylesheet stylesheet-'+id+'" href="'+site_uri+'/?mode=css-color-style&color=%23'+convert[id]+'" title="'+id+'" disabled />');
		}
		
	}
	$( '#style-selector .stylesheet' ).each(function(){
		if( $(this).hasClass('stylesheet-'+id) ){
			this.disabled = false;
		}else{
			this.disabled = true;
		}
	});
	
  
}


function createCookie(name,value,days) {
  if (days) {
    var date = new Date();
    date.setTime(date.getTime()+(days*24*60*60*1000));
    var expires = "; expires="+date.toGMTString();
  }
  else expires = "";
  document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
  var nameEQ = name + "=";
  var ca = document.cookie.split(';');
  for(var i=0;i < ca.length;i++) {
    var c = ca[i];
    while (c.charAt(0)==' ') c = c.substring(1,c.length);
    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
  }
  return null;
}


jQuery(document).ready(function($){  

	$('#style-selector').addClass('hide-panel').animate({left: -240});
	$('#switcher-style-button').click(function(e){
	  if( $('#style-selector').css('left') == '0px' ){
		  $('#style-selector').addClass('hide-panel').animate({left: -240});
	  }else{
		  $('#style-selector').removeClass('hide-panel').animate({left: 0});
	  }
	  e.preventDefault();
	});
	
	$('#list-style-colors a').click(function(e){
		var title = $(this).attr('title').toLowerCase().replace(/ /g,'');
		setActiveStyleSheet( title );
		createCookie("__color", title, 365);
		e.preventDefault();
		$(this).find('span').append('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
		$('header .logo img,.navbar-header .logo img,.top_header .logo img').attr({'src':'http://arkahost.com/attachment/logos/logo-'+title+'.png'});
		var elm = $(this).find('span');
		setTimeout(function(){
			elm.html('');
		}, 1000);
	});
	
	$('#layouts-style-colors input').change(function(){
		$('#main').removeClass('layout-wide').removeClass('layout-boxed').addClass('layout-'+this.value);
		createCookie("__layout", this.value, 365);
	});
	
	$('#style-switcher-bg li span').click(function(e){
		if( $('#navRadio01').get(0).checked == true ){
			alert('Please set layout as Boxed first');
			return;
		}
		$('body').css({'background-image' : $(this).css('background-image').replace('-small','') });
		createCookie("__bg", $(this).css('background-image').replace('-small',''), 365);
		e.preventDefault();
		$(this).append('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
		setTimeout(function(el){
			el.innerHTML = '';
		}, 1000, this );
	});
	
  	var color = readCookie("__color");
  	if( color ){
	  	$('#list-style-colors a').each(function(){
		  	if( $(this).attr('title').toLowerCase().replace(/ /g,'') == color ){
			  	$(this).click();
		  	}
		  	$('header .logo img,.navbar-header .logo img,.top_header .logo img').attr({'src':'http://arkahost.com/attachment/logos/logo-'+color+'.png'});
	  	});
  	}
  	var layout = readCookie("__layout");
  	if( layout ){
	 	$('#layouts-style-colors input').each(function(){
		 	if( this.value == layout ){
			 	$(this).attr({'checked':true}).change();
		 	}
	 	}); 	
	}  	
	var bg = readCookie("__bg");
  	if( bg ){
	 	$('body').css({ 'background-image' : bg }); 	
	}  	

});



