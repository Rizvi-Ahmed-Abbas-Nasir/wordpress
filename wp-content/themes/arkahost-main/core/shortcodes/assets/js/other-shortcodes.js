jQuery(document).ready(function ($) {

	// Check for jQuery version
	// if (parseInt(jQuery.fn.jquery.replace('.', '')) < 170) alert('Shortcodes Ultimate: Your theme uses an outdated version of jQuery. Some shortcodes may not work properly. Required version - 1.7.0');

	// Spoiler
	$('body:not(.king-other-shortcodes-loaded)').on('click', '.king-spoiler-title', function (e) {
		var $title = $(this),
			$spoiler = $title.parent(),
			bar = ($('#wpadminbar').length > 0) ? 28 : 0;
		// Open/close spoiler
		$spoiler.toggleClass('king-spoiler-closed');
		// Close other spoilers in accordion
		$spoiler.parent('.king-accordion').children('.king-spoiler').not($spoiler).addClass('king-spoiler-closed');
		// Scroll in spoiler in accordion
		if ($(window).scrollTop() > $title.offset().top) $(window).scrollTop($title.offset().top - $title.height() - bar);
		e.preventDefault();
	});
	$('.king-spoiler-content').removeAttr('style');
	// Tabs
	$('body:not(.king-other-shortcodes-loaded)').on('click', '.king-tabs-nav span', function (e) {
		var $tab = $(this),
			index = $tab.index(),
			is_disabled = $tab.hasClass('king-tabs-disabled'),
			$tabs = $tab.parent('.king-tabs-nav').children('span'),
			$panes = $tab.parents('.king-tabs').find('.king-tabs-pane'),
			$gmaps = $panes.eq(index).find('.king-gmap:not(.king-gmap-reloaded)');
		// Check tab is not disabled
		if (is_disabled) return false;
		// Hide all panes, show selected pane
		$panes.hide().eq(index).show();
		// Disable all tabs, enable selected tab
		$tabs.removeClass('king-tabs-current').eq(index).addClass('king-tabs-current');
		// Reload gmaps
		if ($gmaps.length > 0) $gmaps.each(function () {
			var $iframe = $(this).find('iframe:first');
			$(this).addClass('king-gmap-reloaded');
			$iframe.attr('src', $iframe.attr('src'));
		});
		// Set height for vertical tabs
		tabs_height();
		e.preventDefault();
	});

	// Activate tabs
	$('.king-tabs').each(function () {
		var active = parseInt($(this).data('active')) - 1;
		$(this).children('.king-tabs-nav').children('span').eq(active).trigger('click');
		tabs_height();
	});

	// Activate anchor nav for tabs and spoilers
	anchor_nav();

	// Lightbox
	$('.king-lightbox').each(function () {
		$(this).on('click', function (e) {
			e.preventDefault();
			e.stopPropagation();
			if ($(this).parent().attr('id') === 'king-generator-preview') $(this).html(su_other_shortcodes.no_preview);
			else {
				var type = $(this).data('mfp-type');
				$(this).magnificPopup({
					type: type,
					tClose: su_magnific_popup.close,
					tLoading: su_magnific_popup.loading,
					gallery: {
						tPrev: su_magnific_popup.prev,
						tNext: su_magnific_popup.next,
						tCounter: su_magnific_popup.counter
					},
					image: {
						tError: su_magnific_popup.error
					},
					ajax: {
						tError: su_magnific_popup.error
					}
				}).magnificPopup('open');
			}
		});
	});
	// Tables
	$('.king-table tr:even').addClass('king-even');
	// Frame
	$('.king-frame-align-center, .king-frame-align-none').each(function () {
		var frame_width = $(this).find('img').width();
		$(this).css('width', frame_width + 12);
	});
	// Tooltip
	$('.king-tooltip').each(function () {
		var $tt = $(this),
			$content = $tt.find('.king-tooltip-content'),
			is_advanced = $content.length > 0,
			data = $tt.data(),
			config = {
				style: {
					classes: data.classes
				},
				position: {
					my: data.my,
					at: data.at,
					viewport: $(window)
				},
				content: {
					title: '',
					text: ''
				}
			};
		if (data.title !== '') config.content.title = data.title;
		if (is_advanced) config.content.text = $content;
		else config.content.text = $tt.attr('title');
		if (data.close === 'yes') config.content.button = true;
		if (data.behavior === 'click') {
			config.show = 'click';
			config.hide = 'click';
			$tt.on('click', function (e) {
				e.preventDefault();
				e.stopPropagation();
			});
			$(window).on('scroll resize', function () {
				$tt.qtip('reposition');
			});
		} else if (data.behavior === 'always') {
			config.show = true;
			config.hide = false;
			$(window).on('scroll resize', function () {
				$tt.qtip('reposition');
			});
		} else if (data.behavior === 'hover' && is_advanced) {
			config.hide = {
				fixed: true,
				delay: 600
			}
		}
		$tt.qtip(config);
	});

	// Animate
	$('.king-animate').each(function () {
		$(this).one('inview', function (e) {
			$(this).addClass('animated').css('visibility', 'visible');
		});
	});

	function tabs_height() {
		$('.king-tabs-vertical').each(function () {
			var $tabs = $(this),
				$nav = $tabs.children('.king-tabs-nav'),
				$panes = $tabs.find('.king-tabs-pane'),
				height = 0;
			$panes.css('min-height', $nav.outerHeight(true));
		});
	}

	function anchor_nav() {
		// Check hash
		if (document.location.hash === '') return;
		// Go through tabs
		$('.king-tabs-nav span[data-anchor]').each(function () {
			if ('#' + $(this).data('anchor') === document.location.hash) {
				var $tabs = $(this).parents('.king-tabs'),
					bar = ($('#wpadminbar').length > 0) ? 28 : 0;
				// Activate tab
				$(this).trigger('click');
				// Scroll-in tabs container
				window.setTimeout(function () {
					$(window).scrollTop($tabs.offset().top - bar - 10);
				}, 100);
			}
		});
		// Go through spoilers
		$('.king-spoiler[data-anchor]').each(function () {
			if ('#' + $(this).data('anchor') === document.location.hash) {
				var $spoiler = $(this),
					bar = ($('#wpadminbar').length > 0) ? 28 : 0;
				// Activate tab
				if ($spoiler.hasClass('king-spoiler-closed')) $spoiler.find('.king-spoiler-title:first').trigger('click');
				// Scroll-in tabs container
				window.setTimeout(function () {
					$(window).scrollTop($spoiler.offset().top - bar - 10);
				}, 100);
			}
		});
	}

	if ('onhashchange' in window) $(window).on('hashchange', anchor_nav);

	$('body').addClass('king-other-shortcodes-loaded');
});