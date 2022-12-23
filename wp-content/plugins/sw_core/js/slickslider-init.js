(function ($) {  
	"use strict";
	function RespSlider( $id, $append, $target ){
		var $col_lg = $id.data('lg');
		var $col_md = $id.data('md');
		var $col_sm = $id.data('sm');
		var $col_xs = $id.data('xs');
		var $col_mobile = $id.data('mobile');
		var $speed = $id.data('speed');
		var $interval = $id.data('interval');
		var $scroll = $id.data('scroll');
		var $autoplay = $id.data('autoplay');
		var $rtl = ( $('body').hasClass('rtl') ) ? true : false;
		var $vertical = ( typeof( $id.data('vertical') != "undefined" ) ) ? $id.data('vertical') : false;
		var $fade 		= ( typeof( $id.data('fade') != "undefined" ) ) ? $id.data('fade') : false;
	    var $dots 		= ( typeof( $id.data('dots') != "undefined" ) ) ? $id.data('dots') : false;
		$target.slick({
		  appendArrows: $append,
		  prevArrow: '<span data-role="none" class="res-button slick-prev" aria-label="previous"></span>',
		  nextArrow: '<span data-role="none" class="res-button slick-next" aria-label="next"></span>',
		  dots: $dots,
		  infinite: true,
		  speed: $speed,
		  slidesToShow: $col_lg,
		  slidesToScroll: $scroll,
		  autoplay: $autoplay,
		  autoplaySpeed: $interval,
		  rtl: $rtl,
		  vertical: $vertical,		  
		  responsive: [
			{
			  breakpoint: 1199,
			  settings: {
				slidesToShow: $col_md
			  }
			},
			{
			  breakpoint: 991,
			  settings: {
				slidesToShow: $col_sm
			  }
			},
			{
			  breakpoint: 767,
			  settings: {
				slidesToShow: $col_xs
			  }
			},
			{
			  breakpoint: 480,
			  settings: {
				slidesToShow: $col_mobile    
			  }
			}
			// You can unslick at a given breakpoint now by adding:
			// settings: "unslick"
			// instead of a settings object
		  ]
		});
		$id.fadeIn(1000, function() {		
			setTimeout(function(){
				$(this).removeClass("loading");
			}, 500);
		});
	}
	$(document).ready(function(){
		$('.responsive-slider').each( function(){
			var $this 	= this.id;
			var $id 	= $('#' + $this);
			var $app 	= $id.data('append');
			var $append	= ( typeof( $app ) == 'undefined' ) ? $id : $(this).find($app);
			var $target = $(this).find( '.responsive' );
			RespSlider( $id, $append, $target );
		});
	});
	
	$( '.testimonial-post-slider' ).each(function(){
			var $rtl 					= $('body').hasClass( 'rtl' );
			var $img_slider 	= $(this).find('.responsive-content');
			var $thumb_slider = $(this).find('.responsive-thumbnail');
			
			$img_slider.slick({
				slidesToShow: 1,
				slidesToScroll: 1,
				fade: true,
				arrows: false,
				rtl: $rtl,
				asNavFor: $thumb_slider
			});
			$thumb_slider.slick({
				centerMode: true,
				centerPadding: '0px',
				slidesToShow: 5,
				slidesToScroll: 1,
				asNavFor: $img_slider,
				arrows: true,
				rtl: $rtl,
				focusOnSelect: true,
				responsive: [
					{
					  breakpoint: 1024,
					  settings: {
						slidesToShow: 3,
						slidesToScroll: 1						
					  }
					},
					{
					  breakpoint: 480,
					  settings: {
						slidesToShow: 1,
						slidesToScroll: 1						
					  }
					},
				  ]
			});

			var el = $(this);
			setTimeout(function(){
				el.removeClass("loading");
			}, 1000);
		});
})(jQuery);