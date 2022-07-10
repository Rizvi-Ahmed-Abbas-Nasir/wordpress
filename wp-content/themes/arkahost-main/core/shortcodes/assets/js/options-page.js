// Wait DOM
jQuery(document).ready(function ($) {

	// ########## About screen ##########

	$('.king-demo-video').magnificPopup({
		type: 'iframe',
		callbacks: {
			open: function () {
				// Change z-index
				$('body').addClass('king-mfp-shown');
			},
			close: function () {
				// Change z-index
				$('body').removeClass('king-mfp-shown');
			}
		}
	});

	// ########## Custom CSS screen ##########

	$('.king-custom-css-originals a').magnificPopup({
		type: 'iframe',
		callbacks: {
			open: function () {
				// Change z-index
				$('body').addClass('king-mfp-shown');
			},
			close: function () {
				// Change z-index
				$('body').removeClass('king-mfp-shown');
			}
		}
	});

	// Enable ACE editor
	if ($('#sunrise-field-custom-css-editor').length > 0) {
		var editor = ace.edit('sunrise-field-custom-css-editor'),
			$textarea = $('#sunrise-field-custom-css').hide();
		editor.getSession().setValue($textarea.val());
		editor.getSession().on('change', function () {
			$textarea.val(editor.getSession().getValue());
		});
		editor.getSession().setMode('ace/mode/css');
		editor.setTheme('ace/theme/monokai');
		editor.getSession().setUseWrapMode(true);
		editor.getSession().setWrapLimitRange(null, null);
		editor.renderer.setShowPrintMargin(null);
		editor.session.setUseSoftTabs(null);
	}

	// ########## Add-ons screen ##########

	var addons_timer = 0;
	$('.king-addons-item').each(function () {
		var $item = $(this),
			delay = 300;
		$item.on( 'click', function (e) {
			window.open($(this).data('url'));
			e.preventDefault();
		});
		addons_timer = addons_timer + delay;
		window.setTimeout(function () {
			$item.addClass('animated bounceIn').css('visibility', 'visible');
		}, addons_timer);
	});

	// ########## Examples screen ##########

	// Disable all buttons
	$('#king-examples-preview').on('click', '.king-button', function (e) {
		if ($(this).hasClass('king-example-button-clicked')) alert(su_options_page.not_clickable);
		else $(this).addClass('king-example-button-clicked');
		e.preventDefault();
	});

	var examples_timer = 0,
		open = $('#su_open_example').val(),
		$example_window = $('#king-examples-window'),
		$example_preview = $('#king-examples-preview');
	$('.king-examples-group-title, .king-examples-item').each(function () {
		var $item = $(this),
			delay = 200;
		if ($item.hasClass('king-examples-item')) {
			$item.on('click', function (e) {
				var code = $(this).data('code'),
					id = $(this).data('id');
				$item.magnificPopup({
					type: 'inline',
					alignTop: true,
					callbacks: {
						open: function () {
							// Change z-index
							$('body').addClass('king-mfp-shown');
						},
						close: function () {
							// Change z-index
							$('body').removeClass('king-mfp-shown');
							$example_preview.html('');
						}
					}
				});
				var su_example_preview = $.ajax({
					url: ajaxurl,
					type: 'get',
					dataType: 'html',
					data: {
						action: 'su_example_preview',
						code: code,
						id: id
					},
					beforeSend: function () {
						if (typeof su_example_preview === 'object') su_example_preview.abort();
						$example_window.addClass('king-ajax');
						$item.magnificPopup('open');
					},
					success: function (data) {
						$example_preview.html(data);
						$example_window.removeClass('king-ajax');
					}
				});
				e.preventDefault();
			});
			// Open preselected example
			if ($item.data('id') === open) $item.trigger('click');
		}
		examples_timer = examples_timer + delay;
		window.setTimeout(function () {
			$item.addClass('animated fadeInDown').css('visibility', 'visible');
		}, examples_timer);
	});
	$('#king-examples-window').on('click', '.king-examples-get-code', function (e) {
		$(this).hide();
		$(this).parent('.king-examples-code').children('textarea').slideDown(300);
		e.preventDefault();
	});

	// ########## Cheatsheet screen ##########
	$('.king-cheatsheet-switch').on('click', function (e) {
		$('body').toggleClass('king-print-cheatsheet');
		e.preventDefault();
	});
});