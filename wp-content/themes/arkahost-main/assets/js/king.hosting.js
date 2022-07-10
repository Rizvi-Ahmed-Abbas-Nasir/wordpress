(function($){
	"use strict";

	var adv_checker = {

		whois : [],

		init : function (){

			$('.arkahost-advance-search').each(function (index){

				var $this = $(this),
					$submit = $this.find('.arkahost-submit'),
					$input = $this.find('.arkahost-domain'),
					$form = $this.find('form'),
					$suggestions = $form.data('suggestions').replace(/\s+/g, '').split(','),
					data;
				$this.attr('data-index', index);
				data = {
					suggestions : $suggestions,
					form : $form,
					input : $input,
					submit : $submit,
					wrp : $this,
					index : index
				};
				$submit.on('click', data, adv_checker.submit);
			});
		},

		submit : function (e){
			e.preventDefault();
			var obj = e.data,
				el = obj.wrp.find('.arkahost-search-results'),
				domain_default = obj.form.data('default'),
				basename = adv_checker.basename((obj.input.val()!=='')? obj.input.val() : domain_default),
				suggestions = obj.wrp.find('input[name=suggestion]:checked').serializeArray(),

				tlds = [];

			suggestions.map(function (tld){tlds.push(tld.value)});

			if(tlds.length == 0)
				tlds.push(obj.suggestions[0]);

			el.html('');
			obj.security = obj.form.find('input[name=security]').val();
			obj.el = el;
			obj.last = false;
			adv_checker.whois[obj.index] = {};
			for(var i in tlds){
				var domain_data = {},
					placeholder = $('<div class="arkahost-placeholder loading" data-domain="">\
					<div class="arkahost-spinner">\
					  <div class="rect1"></div>\
					  <div class="rect2"></div>\
					  <div class="rect3"></div>\
					  <div class="rect4"></div>\
					  <div class="rect5"></div>\
					</div>\
					</div>');
				$.extend( domain_data, obj );
				domain_data.domain  = basename + tlds[i];
				if(i == tlds.length -1 )
					domain_data.last = true;
				domain_data.el= placeholder;
				placeholder.data('domain', domain_data.domain);
				placeholder.append(domain_data.domain);
				obj.el.append(placeholder);
				adv_checker.check(domain_data);
			}

		},

		basename : function (domain){
			return domain.replace(/^.*\/|\.[^.]*$/g, '');
		},

		funcs : function (elm, form_index) {
			var wrp = $('.arkahost-advance-search[data-index=' + form_index+ ']'),
				fields = ['domain', 'domainsregperiod[###]'];
				
			fields.push(king_hosting_params.adv_custom_field);

			elm.find('.arkahost-adv-search-btn').on('click', function (e){
				e.preventDefault();
				var $this = $(this),
					domain = this.getAttribute('data-domain');

				switch (this.getAttribute('data-action')) {
					case 'whois':
						var popup = $('body').find('.arkahost-lightbox');

						if(!popup.get(0)){
							popup = $('<div class="arkahost-lightbox">\
								<div class="arkahost-lightbox-body">\
									<div class="arkahost-lightbox-content">\
									</div>\
									<a class="kalb-close" href="#close" title="Close">\
										<i class="tsdx-android-close icon-close"></i>\
									</a>\
								</div>\
								<div class="kalb-overlay"></div>\
							</div>').appendTo($('body'));
							popup.find('.kalb-close,div.kalb-overlay').on('click', function (e){
								e.preventDefault();
								popup.remove();
							});

						}

						popup.find('.arkahost-lightbox-content').html(window.atob(adv_checker.whois[form_index][domain]));
						break;
					case 'transfer':
						var form = $('<form method="POST"></form>');
						form.attr('action', king_hosting_params.arkahost_adv_url);
						for(var f in fields ){
							var name = fields[f],
								val = domain;
							if(name.indexOf('###') > -1){
								name = name.replace('###', domain);
								val = 1;
							}
							form.append('<input name="'+name+'" value="'+val+'" type="hidden"/>');
						}

						form.append('<input name="token" value="'+window.btoa('random')+'" type="hidden"/>');
						form.append('<input name="transfer" value="Transfer" type="hidden"/>');

						$('body').append(form);
						form.submit();
						break;

					case 'buynow':
						var form = $('<form method="POST"></form>');
						form.attr('action', king_hosting_params.arkahost_adv_url);
						for(var f in fields ){
							var name = fields[f],
								val = domain;
							if(name.indexOf('###') > -1){
								name = name.replace('###', domain);
								val = 1;
							}
							form.append('<input name="'+name+'" value="'+val+'" type="hidden"/>');
						}

						form.append('<input name="token" value="'+window.btoa('random')+'" type="hidden"/>');

						$('body').append(form);
						form.submit();
						break;

					default:

				}
			})
		},

		check : function (obj){

			var data = {
				'domain': obj.domain,
				'action': 'king_adv_search_domain',
				'security': obj.security
			};

			$.ajax({
				url: king_hosting_params.ajax_url + '?'+Math.random(),
				type: 'POST',
				dataType: 'json',
				data: data,
				success: function(data){
					obj.el.removeClass('loading').html(data.results_html);
					adv_checker.whois[obj.index][data.domain] = data.whois;
					adv_checker.funcs(obj.el,obj.index);
				}
			});

		}

	};

	$(document).ready(function($){

		adv_checker.init();

		$('.serch_area .input_submit').on( 'click', function(e){
			e.preventDefault();
			var domain = $(this).closest('.serch_area').find('.domain_input').val();
			var search_domain_form = $(this).closest(".search_domain_form");

			//is diretly method
			var data = search_domain_form.data("form");

			if( data == 'direct')
			{

				search_domain_form.find('.domain').val( domain );
				search_domain_form.find('.domains').val( domain );
				search_domain_form.find('.domainsregperiod').attr( 'name', 'domainsregperiod[' + domain + ']' );
				search_domain_form.submit();
			}
			else
			{
				e.preventDefault();
				if($.active < 1){
					$('#domain_input').addClass('loading');
					$.ajax({
						url: king_hosting_params.ajax_url + '?'+Math.random(),
						type: 'POST',
						dataType: 'json',
						data: {
							'domain': domain,
							'action': 'king_search_domain',
							'security': $('#security').val()
						},
						success: function(data){
							$('#domain_search_results').html(data.results_html);
							$('#domain_input').removeClass('loading');
							$.getScript(king_hosting_params.hosting_js);
						}
					});
				}
			}

		});


		$('.domain_input').keypress(function (e) {
			if (e.which == 13) {
				$(this).closest('form').find( ".input_submit" ).trigger( "click" );
				e.preventDefault();
				return false;
			}
		});



		$('.select_this_domain').on( 'click', function(){
			var o_this = $(this);
			var domain = o_this.attr('data-domain');
			$('#select_this_domain input.domains_val').val(domain);
			$('#select_this_domain .domainsregperiod_val').attr("name", "domainsregperiod[" + domain + "]");
			$('#select_this_domain').submit();
		});

		$('.whois_view').on( 'click', function(){
			var o_this = $(this);
			var domain = o_this.attr('data-domain');

			if($.active < 1){
				if($('#whois_view_result').length > 0) $('#whois_view_result').remove();

				$('#domain_search_results .content-result').append('<div id="whois_view_result"><a class="close" href="javascript:;"><i class="fas fa-times"></i></a><div class="content"></div></div>');
				$('#whois_view_result').hide();
				o_this.text('Loading...');

				$.ajax({
					url: king_hosting_params.ajax_url,
					type: 'POST',
					dataType: 'json',
					data: {
						'domain': domain,
						'action': 'king_get_whois'
					},
					success: function(data){
						$('#domain_search_results .suggest_domain').slideUp('fast');
						$('#whois_view_result .content').html(data.results_html);
						$('#whois_view_result').slideDown('fast');
						o_this.text('Whois');

						$.getScript(king_hosting_params.hosting_js);
					}
				});
			}
		});

		$('#whois_view_result .close').on( 'click', function(){
			$('#whois_view_result').slideUp('fast');
			$('#domain_search_results .suggest_domain').slideDown('fast');
		});

	});

})(jQuery);
