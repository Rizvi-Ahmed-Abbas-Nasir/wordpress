jQuery(document).ready(function ($) {
	// Enable sliders
	$('.king-slider').each(function () {
		// Prepare data
		var $slider = $(this);
		// Apply Swiper
		var $swiper = $slider.swiper({
			wrapperClass: 'king-slider-slides',
			slideClass: 'king-slider-slide',
			slideActiveClass: 'king-slider-slide-active',
			slideVisibleClass: 'king-slider-slide-visible',
			pagination: '#' + $slider.attr('id') + ' .king-slider-pagination',
			autoplay: $slider.data('autoplay'),
			paginationClickable: true,
			grabCursor: true,
			mode: 'horizontal',
			mousewheelControl: $slider.data('mousewheel'),
			speed: $slider.data('speed'),
			calculateHeight: $slider.hasClass('king-slider-responsive-yes'),
			loop: true
		});
		// Prev button
		$slider.find('.king-slider-prev').on( 'click', function (e) {
			$swiper.swipeNext();
		});
		// Next button
		$slider.find('.king-slider-next').on( 'click', function (e) {
			$swiper.swipePrev();
		});
	});
	// Enable carousels
	$('.king-carousel').each(function () {
		// Prepare data
		var $carousel = $(this),
			$slides = $carousel.find('.king-carousel-slide');
		// Apply Swiper
		var $swiper = $carousel.swiper({
			wrapperClass: 'king-carousel-slides',
			slideClass: 'king-carousel-slide',
			slideActiveClass: 'king-carousel-slide-active',
			slideVisibleClass: 'king-carousel-slide-visible',
			pagination: '#' + $carousel.attr('id') + ' .king-carousel-pagination',
			autoplay: $carousel.data('autoplay'),
			paginationClickable: true,
			grabCursor: true,
			mode: 'horizontal',
			autoHight: true,
			mousewheelControl: $carousel.data('mousewheel'),
			speed: $carousel.data('speed'),
			slidesPerView: ($carousel.data('items') > $slides.length) ? $slides.length : $carousel.data('items'),
			slidesPerGroup: $carousel.data('scroll'),
			calculateHeight: true,
			loop: true,
			onSlideChangeStart: function(){
				var newHeight = 0;
				$carousel.find('.king-carousel-slide-visible img').each(function(){
					if( $(this).height() > newHeight ){
						newHeight = $(this).height();
					}
				});
				$carousel.find('.king-carousel-slides').animate({height: newHeight });
			},
			onImagesReady: function(){
				var newHeight = $carousel.find('.king-carousel-slide-visible img').eq(0).height();
				$carousel.find('.king-carousel-slides').animate({height: newHeight });
			}
		});
		// Prev button
		$carousel.find('.king-carousel-prev').on( 'click', function (e) {
			$swiper.swipeNext(function(){});
		});
		// Next button
		$carousel.find('.king-carousel-next').on( 'click', function (e) {
			$swiper.swipePrev();
		});

		
	});
	// Lightbox for galleries (slider, carousel, custom_gallery)
	$('.king-lightbox-gallery').each(function () {
		$(this).magnificPopup({
			delegate: 'a',
			type: 'image',
			mainClass: 'mfp-img-mobile',
			gallery: {
				enabled: true,
				navigateByImgClick: true,
				preload: [0, 1], // Will preload 0 - before current, and 1 after the current image
				tPrev: su_magnific_popup.prev,
				tNext: su_magnific_popup.next,
				tCounter: su_magnific_popup.counter
			},
			image: {
				tError: su_magnific_popup.error,
				titleSrc: function (item) {
					return item.el.children('img').attr('alt');
				}
			},
			tClose: su_magnific_popup.close,
			tLoading: su_magnific_popup.loading
		});
	});
});