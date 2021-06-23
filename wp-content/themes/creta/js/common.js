( function( window ) {
	'use strict';

	function classReg( className ) {
		return new RegExp("(^|\\s+)" + className + "(\\s+|$)");
	}
	var hasClass, addClass, removeClass;

	if ( 'classList' in document.documentElement ) {
		hasClass = function( elem, c ) {
			return elem.classList.contains( c );
		};
		addClass = function( elem, c ) {
			elem.classList.add( c );
		};
		removeClass = function( elem, c ) {
			elem.classList.remove( c );
		};
	}
	else {
		hasClass = function( elem, c ) {
			return classReg( c ).test( elem.className );
		};


	}

	function toggleClass( elem, c ) {
		var fn = hasClass( elem, c ) ? removeClass : addClass;
		fn( elem, c );
	}

	var classie = {
  // full names
  hasClass: hasClass,
  addClass: addClass,
  removeClass: removeClass,
  toggleClass: toggleClass,
  // short names
  has: hasClass,
  add: addClass,
  remove: removeClass,
  toggle: toggleClass
};

// transport
if ( typeof define === 'function' && define.amd ) {
  // AMD
  define( classie );
} else {
  // browser global
  window.classie = classie;
}

})( window );


( function( window ) {
	
	'use strict';
	
	// EventListener | @jon_neal | //github.com/jonathantneal/EventListener
	!window.addEventListener && window.Element && (function () {
		function addToPrototype(name, method) {
			Window.prototype[name] = HTMLDocument.prototype[name] = Element.prototype[name] = method;
		}

		var registry = [];

		addToPrototype("addEventListener", function (type, listener) {
			var target = this;

			registry.unshift({
				__listener: function (event) {
					event.currentTarget = target;
					event.pageX = event.clientX + document.documentElement.scrollLeft;
					event.pageY = event.clientY + document.documentElement.scrollTop;
					event.preventDefault = function () { event.returnValue = false };
					event.relatedTarget = event.fromElement || null;
					event.stopPropagation = function () { event.cancelBubble = true };
					event.relatedTarget = event.fromElement || null;
					event.target = event.srcElement || target;
					event.timeStamp = +new Date;

					listener.call(target, event);
				},
				listener: listener,
				target: target,
				type: type
			});

			this.attachEvent("on" + type, registry[0].__listener);  }); 

	})();


	function UISearch( el, options ) {	
		this.el = el;
		this.inputEl = el.querySelector( 'form > input.search-bar-input' );
		if (this.inputEl != null) {
			this._initEvents();
		}
	}

	UISearch.prototype = {
		_initEvents : function() {
			var self = this,
			initSearchFn = function( ev ) {
				ev.stopPropagation();
					// trim its value
					self.inputEl.value = self.inputEl.value.trim();
					
					if( !classie.has( self.el, 'search-bar-open' ) ) { // open it
						ev.preventDefault();
						self.open();
					}
					else if( classie.has( self.el, 'search-bar-open' ) && /^\s*$/.test( self.inputEl.value ) ) { // close it
						ev.preventDefault();
						self.close();
					}
				}

				this.el.addEventListener( 'click', initSearchFn );
				this.el.addEventListener( 'touchstart', initSearchFn );	

				this.inputEl.addEventListener( 'click', function( ev ) { ev.stopPropagation(); });
				this.inputEl.addEventListener( 'touchstart', function( ev ) { ev.stopPropagation(); } );

			},
			open : function() {
				var self = this;
				classie.add( this.el, 'search-bar-open' );
			// focus the input
			
			// close the search input if body is clicked
			var bodyFn = function( ev ) {
				self.close();
				this.removeEventListener( 'click', bodyFn );
				this.removeEventListener( 'touchstart', bodyFn );
			};
			document.addEventListener( 'click', bodyFn );
			document.addEventListener( 'touchstart', bodyFn );
		},
		close : function() {
			this.inputEl.blur();
			classie.remove( this.el, 'search-bar-open' );
		}
	}

	// add to global namespace
	window.UISearch = UISearch;

} )( window );
/*========== offer slide ===========*/
jQuery(function() {

	jQuery("#slideshow > p:gt(0)").hide();

	setInterval(function() { 
		jQuery('#slideshow > p:first')
		.fadeOut(1000)
		.next()
		.fadeIn(1000)
		.end()
		.appendTo('#slideshow');
	},  3000);

});


jQuery(document).ready(function() {
	
	"use strict";
	jQuery("#nav > li").on("hover", function() {
		var el = jQuery(this).find(".level0-wrapper");
		el.hide();
		el.css("left", "0");
		el.stop(true, true).delay(150).fadeIn(300, "easeOutCubic");
	}, function() {
		jQuery(this).find(".level0-wrapper").stop(true, true).delay(300).fadeOut(300, "easeInCubic");
	});	
	var scrolled = false;

	jQuery("#nav li.level0.drop-menu").on("mouseover", function(){
		if(jQuery(window).width() >= 740){
			jQuery(this).children('ul.level1').fadeIn(100);
		}
		return false;
	}).on("mouseleave", function(){
		if(jQuery(window).width() >= 740){
			jQuery(this).children('ul.level1').fadeOut(100);
		}
		return false;
	});
	jQuery("#nav li.level0.drop-menu li").on("mouseover", function(){
		if(jQuery(window).width() >= 740){
			jQuery(this).children('ul').css({top:0,left:"165px"});
			var offset = jQuery(this).offset();
			if(offset && (jQuery(window).width() < offset.left+325)){
				jQuery(this).children('ul').removeClass("right-sub");
				jQuery(this).children('ul').addClass("left-sub");
				jQuery(this).children('ul').css({top:0,left:"-167px"});
			} else {
				jQuery(this).children('ul').removeClass("left-sub");
				jQuery(this).children('ul').addClass("right-sub");
			}
			jQuery(this).children('ul').fadeIn(100);
		}
	}).on("mouseleave", function(){
		if(jQuery(window).width() >= 740){
			jQuery(this).children('ul').fadeOut(100);
		}
	});				

	jQuery("#bestsell-slider .slider-items").owlCarousel({
		items: 4, //10 items above 1000px browser width
		itemsDesktop: [1024, 3], //5 items between 1024px and 901px
		itemsDesktopSmall: [980, 2], // 3 items betweem 900px and 601px
		itemsTablet: [768, 2], //2 items between 600 and 0;
		itemsMobile: [360, 1],
		navigation: true,
		navigationText: ["<a class=\"flex-prev\"></a>", "<a class=\"flex-next\"></a>"],
		slideSpeed: 500,
		pagination: false
	});
	jQuery("#featured-slider .slider-items").owlCarousel({
		items: 4, //10 items above 1000px browser width
		itemsDesktop: [1024, 3], //5 items between 1024px and 901px
		itemsDesktopSmall: [980, 2], // 3 items betweem 900px and 601px
		itemsTablet: [768, 2], //2 items between 600 and 0;
		itemsMobile: [360, 1],
		navigation: true,
		navigationText: ["<a class=\"flex-prev\"></a>", "<a class=\"flex-next\"></a>"],
		slideSpeed: 500,
		pagination: false
	});
	jQuery("#new-products-slider .slider-items").owlCarousel({
	items : 4, //10 items above 1000px browser width
	itemsDesktop : [1024,3], //5 items between 1024px and 901px
	itemsDesktopSmall : [980,2], // 3 items betweem 900px and 601px
	itemsTablet: [600,2], //2 items between 600 and 0;
	itemsMobile : [320,1],
	navigation : true,
	navigationText : ["<a class=\"flex-prev\"></a>","<a class=\"flex-next\"></a>"],
	slideSpeed : 500,
	pagination : true
});


	/*** End version 6 js ***/
	jQuery("#bag-seller-slider1 .slider-items").owlCarousel({
	items : 3, //10 items above 1000px browser width
	itemsDesktop : [1024,4], //5 items between 1024px and 901px
	itemsDesktopSmall : [980,2], // 3 items betweem 900px and 601px
	itemsTablet: [600,2], //2 items between 600 and 0;
	itemsMobile : [320,1],
	navigation : true,
	navigationText : ["<a class=\"flex-prev\"></a>","<a class=\"flex-next\"></a>"],
	slideSpeed : 500,
	pagination : false
});


	jQuery("#new-arrivals-slider .slider-items").owlCarousel({
		items: 4, //10 items above 1000px browser width
		itemsDesktop: [1024, 3], //5 items between 1024px and 901px
		itemsDesktopSmall: [980, 2], // 3 items betweem 900px and 601px
		itemsTablet: [768, 2], //2 items between 600 and 0;
		itemsMobile: [360, 1],
		navigation: true,
		navigationText: ["<a class=\"flex-prev\"></a>", "<a class=\"flex-next\"></a>"],
		slideSpeed: 500,
		pagination: false
	});
	jQuery("#recommend-slider .slider-items").owlCarousel({
	items : 6, //10 items above 1000px browser width
	itemsDesktop : [1024,4], //5 items between 1024px and 901px
	itemsDesktopSmall : [980,2], // 3 items betweem 900px and 601px
	itemsTablet: [600,2], //2 items between 600 and 0;
	itemsMobile : [320,1],
	navigation : true,
	navigationText : ["<a class=\"flex-prev\"></a>","<a class=\"flex-next\"></a>"],
	slideSpeed : 500,
	pagination : false
});
	jQuery("#brand-logo-slider .slider-items").owlCarousel({
		autoPlay : true,
	items : 6, //10 items above 1000px browser width
	itemsDesktop : [1024,4], //5 items between 1024px and 901px
	itemsDesktopSmall : [900,3], // 3 items betweem 900px and 601px
	itemsTablet: [600,2], //2 items between 600 and 0;
	itemsMobile : [320,1],
	navigation : true,
	navigationText : ["<a class=\"flex-prev\"></a>","<a class=\"flex-next\"></a>"],
	slideSpeed : 500,
	pagination : false			
});


	jQuery("#related-products-slider .slider-items").owlCarousel({
	items : 4, //10 items above 1000px browser width
	itemsDesktop : [1024,3], //5 items between 1024px and 901px
	itemsDesktopSmall : [980,2], // 3 items betweem 900px and 601px
	itemsTablet: [600,2], //2 items between 600 and 0;
	itemsMobile : [360,1],
	navigation : true,
	navigationText : ["<a class=\"flex-prev\"></a>","<a class=\"flex-next\"></a>"],
	slideSpeed : 500,
	pagination : false
});

	jQuery("#upsell-products-slider .slider-items").owlCarousel({
	items : 4, //10 items above 1000px browser width
	itemsDesktop : [1024,3], //5 items between 1024px and 901px
	itemsDesktopSmall : [980,2], // 3 items betweem 900px and 601px
	itemsTablet: [600,2], //2 items between 600 and 0;
	itemsMobile : [360,1],
	navigation : true,
	navigationText : ["<a class=\"flex-prev\"></a>","<a class=\"flex-next\"></a>"],
	slideSpeed : 500,
	pagination : false
});
	jQuery("#cross-sell-products .slider-items").owlCarousel({
	items : 4, //10 items above 1000px browser width
	itemsDesktop : [1024,4], //5 items between 1024px and 901px
	itemsDesktopSmall : [900,3], // 3 items betweem 900px and 601px
	itemsTablet: [600,2], //2 items between 600 and 0;
	itemsMobile : [320,1],
	navigation : true,
	navigationText : ["<a class=\"flex-prev\"></a>","<a class=\"flex-next\"></a>"],
	slideSpeed : 500,
	pagination : false
});


	jQuery("#more-views-slider .slider-items").owlCarousel({
		autoplay : true,
	items : 3, //10 items above 1000px browser width
	itemsDesktop : [1024,4], //5 items between 1024px and 901px
	itemsDesktopSmall : [900,3], // 3 items betweem 900px and 601px
	itemsTablet: [600,2], //2 items between 600 and 0;
	itemsMobile : [320,1],
	navigation : true,
	navigationText : ["<a class=\"flex-prev\"></a>","<a class=\"flex-next\"></a>"],
	slideSpeed : 500,
	pagination : false
});

	
	jQuery("#mobile-menu").mobileMenu({
		MenuWidth: 250,
		SlideSpeed : 300,
		WindowsMaxWidth : 767,
		PagePush : true,
		FromLeft : true,
		Overlay : true,
		CollapseMenu : true,
		ClassName : "mobile-menu"
	});


	/*========== Left Nav ===========*/



        //increase/ decrease product qunatity buttons +/- in cart.html table
        if(jQuery('.subDropdown')[0]){
        	jQuery('.subDropdown').on("click", function(){
        		jQuery(this).toggleClass('plus');
        		jQuery(this).toggleClass('minus');
        		jQuery(this).parent().find('ul').slideToggle();
        	});
        }
        


        /*=============End Left Nav=============*/




    });



jQuery().ready(function()
{
	(function(element){
		jQueryelement = jQuery(element);
		itemNav = jQuery('.item-nav',jQueryelement);
		itemContent = jQuery('.pdt-content',jQueryelement);
		btn_loadmore = jQuery('.btn-loadmore',jQueryelement);
		ajax_url="http://www.magikcommerce.com/producttabs/index/ajax";
		catids = '39';
		label_allready = 'All Ready';
		label_loading = 'Loading ...';
		function setAnimate(el){
			jQuery_items = jQuery('.item-animate',el);
			jQuery('.btn-loadmore',el).fadeOut('fast');
			jQuery_items.each(function(i){
				jQuery(this).attr("style", "-webkit-animation-delay:" + i * 300 + "ms;"
					+ "-moz-animation-delay:" + i * 300 + "ms;"
					+ "-o-animation-delay:" + i * 300 + "ms;"
					+ "animation-delay:" + i * 300 + "ms;");
				if (i == jQuery_items.size() -1) {
					jQuery(".pdt-list", el).addClass("play");
					jQuery('.btn-loadmore', el).fadeIn(i*0.3);
				}
			});
		}
		setAnimate(jQuery('.tab-content-actived',jQueryelement));

		itemNav.on("click", function(){
			var jQuerythis = jQuery(this);
			if(jQuerythis.hasClass('tab-nav-actived')) return false;
			itemNav.removeClass('tab-nav-actived');
			jQuerythis.addClass('tab-nav-actived');
			var itemActive = '.'+jQuerythis.attr('data-href');
			itemContent.removeClass('tab-content-actived');
			jQuery(".pdt-list").removeClass("play");jQuery(".pdt-list .item").removeAttr('style');
			jQuery('.item',jQuery(itemActive, jQueryelement)).addClass('item-animate').removeClass('animated');
			jQuery(itemActive, jQueryelement).addClass('tab-content-actived');

			contentLoading = jQuery('.content-loading',jQuery(itemActive, jQueryelement));
			isLoaded = jQuery(itemActive, jQueryelement).hasClass('is-loaded');
			if(!isLoaded && !jQuery(itemActive, jQueryelement).hasClass('is-loading')){
				jQuery(itemActive, jQueryelement).addClass('is-loading');
				contentLoading.show();
				pdt_type = jQuerythis.attr('data-type');
				catid = jQuerythis.attr('data-catid');
				orderby = jQuerythis.attr('data-orderby');
				jQuery.ajax({
					type: 'POST',
					url: ajax_url,
					data:{
						numberstart: 0,
						catid: catid,
						orderby: orderby,
						catids: catids,
						pdt_type: pdt_type
					},
					success: function(result){
						if(result.listProducts !=''){
							jQuery('.pdt-list',jQuery(itemActive, jQueryelement)).html(result.listProducts);
							jQuery(itemActive, jQueryelement).addClass('is-loaded').removeClass('is-loading');
							contentLoading.remove();
							setAnimate(jQuery(itemActive, jQueryelement));
							setResult(jQuery(itemActive, jQueryelement));
						}
					},
					dataType:'json'
				});
			}else{
				jQuery('.item', itemContent ).removeAttr('style');
				setAnimate(jQuery(itemActive, jQueryelement));
			}
		});
		function setResult(content){
			jQuery('.btn-loadmore', content).removeClass('loading');
			itemDisplay = jQuery('.item', content).length;
			jQuery('.btn-loadmore', content).parent('.pdt-loadmore').attr('data-start', itemDisplay);
			total = jQuery('.btn-loadmore', content).parent('.pdt-loadmore').attr('data-all');
			loadnum = jQuery('.btn-loadmore', content).parent('.pdt-loadmore').attr('data-loadnum');
			if(itemDisplay < total){
				jQuery('.load-number', content).attr('data-total', (total - itemDisplay));
				if((total - itemDisplay)< loadnum ){
					jQuery('.load-number',  content).attr('data-more', (total - itemDisplay));
				}
			}
			if(itemDisplay == total){
				jQuery('.load-number', content).css({display: 'none'});
				jQuery('.btn-loadmore', content).addClass('loaded');
				jQuery('.load-text', content).text(label_allready);
			}else{
				jQuery('.load-text', content).text(label_loadmore);
			}
		}
		btn_loadmore.on('click.loadmore', function(){
			var jQuerythis = jQuery(this);
			itemActive = '.'+jQuerythis.parent('.pdt-loadmore').attr('data-href');
			jQuery(".pdt-list").removeClass("play");jQuery(".pdt-list .item").removeAttr('style');
			jQuery('.item',jQuery(itemActive, jQueryelement)).addClass('animated').removeClass('item-animate');
			if (jQuerythis.hasClass('loaded') || jQuerythis.hasClass('loading')){
				return false;
			}else{
				jQuerythis.addClass('loading'); jQuery('.load-text', jQuerythis).text(label_loading);
				numberstart = jQuerythis.parent('.pdt-loadmore').attr('data-start');
				catid = jQuerythis.parent('.pdt-loadmore').attr('data-catid');
				pdt_type = jQuerythis.parent('.pdt-loadmore').attr('data-type');
				orderby = jQuerythis.parent('.pdt-loadmore').attr('data-orderby');
				jQuery.ajax({
					type: 'POST',
					url: ajax_url,
					data:{
						numberstart: numberstart,
						catid: catid,
						orderby: orderby,
						catids: catids,
						pdt_type: pdt_type
					},
					success: function(result){
						if(result.listProducts !=''){
							animateFrom = jQuery('.item',jQuery(itemActive, jQueryelement)).size();
							jQuery(result.listProducts).insertAfter(jQuery('.item',jQuery(itemActive, jQueryelement)).nextAll().last());
							setAnimate(jQuery(itemActive, jQueryelement));
							setResult(jQuery(itemActive, jQueryelement));
						}
					},
					dataType:'json'
				});
			}
			return false;
		});
	})('#magik_producttabs1');
});
//]]>


var isTouchDevice = ('ontouchstart' in window) || (navigator.msMaxTouchPoints > 0);
jQuery(window).on("load", function() {

	if (isTouchDevice)
	{
		jQuery('#nav a.level-top').on("click", function(e) {
			jQueryt = jQuery(this);
			jQueryparent = jQueryt.parent();
			if (jQueryparent.hasClass('parent'))
			{
				if ( !jQueryt.hasClass('menu-ready'))
				{                    
					jQuery('#nav a.level-top').removeClass('menu-ready');
					jQueryt.addClass('menu-ready');
					return false;
				}
				else
				{
					jQueryt.removeClass('menu-ready');
				}
			}
		});
	}
//on load
jQuery().UItoTop();


}); //end: on load

//]]>


/*--------| UItoTop jQuery Plugin 1.1-------------------*/
(function(jQuery){
	jQuery.fn.UItoTop = function(options) {

		var defaults = {
			text: '',
			min: 200,
			inDelay:600,
			outDelay:400,
			containerID: 'toTop',
			containerHoverID: 'toTopHover',
			scrollSpeed: 1200,
			easingType: 'linear'
		};

		var settings = jQuery.extend(defaults, options);
		var containerIDhash = '#' + settings.containerID;
		var containerHoverIDHash = '#'+settings.containerHoverID;

		jQuery('body').append('<a href="#" id="'+settings.containerID+'">'+settings.text+'</a>');
		jQuery(containerIDhash).hide().on("click", function(){
			jQuery('html, body').animate({scrollTop:0}, settings.scrollSpeed, settings.easingType);
			jQuery('#'+settings.containerHoverID, this).stop().animate({'opacity': 0 }, settings.inDelay, settings.easingType);
			return false;
		})
		.prepend('<span id="'+settings.containerHoverID+'"></span>')
		.on("hover", function() {
			jQuery(containerHoverIDHash, this).stop().animate({
				'opacity': 1
			}, 600, 'linear');
		}, function() { 
			jQuery(containerHoverIDHash, this).stop().animate({
				'opacity': 0
			}, 700, 'linear');
		});

		jQuery(window).on("scroll", function() {
			var sd = jQuery(window).scrollTop();
			if(typeof document.body.style.maxHeight === "undefined") {
				jQuery(containerIDhash).css({
					'position': 'absolute',
					'top': jQuery(window).scrollTop() + jQuery(window).height() - 50
				});
			}
			if ( sd > settings.min ) 
				jQuery(containerIDhash).fadeIn(settings.inDelay);
			else 
				jQuery(containerIDhash).fadeOut(settings.Outdelay);
		});

	};
})(jQuery);

/*--------| End UItoTop -------------------*/


function slideEffectAjax() {
	jQuery('.top-cart-contain').on("mouseenter", function() {
		jQuery(this).find(".top-cart-content").stop(true, true).slideDown();
	});

	jQuery('.top-cart-contain').on("mouseleave", function() {
		jQuery(this).find(".top-cart-content").stop(true, true).slideUp();
	});
}



slideEffectAjax();



/*-------- End Cart js -------------------*/


jQuery.extend( jQuery.easing,
{	
	easeInCubic: function (x, t, b, c, d) {
		return c*(t/=d)*t*t + b;
	},
	easeOutCubic: function (x, t, b, c, d) {
		return c*((t=t/d-1)*t*t + 1) + b;
	},	
});

(function(jQuery){
	jQuery.fn.extend({
		accordion: function() {       
			return this.each(function() {

				function activate(el,effect){	
					jQuery(el).siblings( panelSelector )[(effect || activationEffect)](((effect == "show")?activationEffectSpeed:false),function(){
						jQuery(el).parents().show();	
					});	
				}	
			});
		}
	}); 
})(jQuery);

jQuery(function(jQuery) {
	jQuery('.accordion').accordion();	
	jQuery('.accordion').each(function(index){
		var activeItems = jQuery(this).find('li.active');
		activeItems.each(function(i){
			jQuery(this).children('ul').css('display', 'block');
			if (i == activeItems.length - 1)
			{
				jQuery(this).addClass("current");
			}
		});
	});

});



/*-------- End Nav js -------------------*/	

(function(jQuery){
	jQuery.fn.extend({
		accordionNew: function() {       
			return this.each(function() {	
				var jQueryul			= jQuery(this),
				elementDataKey			= 'accordiated',
				activeClassName			= 'active',
				activationEffect 		= 'slideToggle',
				panelSelector			= 'ul, div',
				activationEffectSpeed 	= 'fast',
				itemSelector			= 'li';	
				if(jQueryul.data(elementDataKey))
					return false;							
				jQuery.each(jQueryul.find('ul, li>div'), function(){
					jQuery(this).data(elementDataKey, true);
					jQuery(this).hide();
				});	
				jQuery.each(jQueryul.find('em.open-close'), function(){
					jQuery(this).on("click",function(e){
						activate(this, activationEffect);
						return void(0);
					});	
					jQuery(this).bind('activate-node', function(){
						jQueryul.find( panelSelector ).not(jQuery(this).parents()).not(jQuery(this).siblings()).slideUp( activationEffectSpeed );
						activate(this,'slideDown');
					});
				});	
				var active = (location.hash)?jQueryul.find('a[href=' + location.hash + ']')[0]:jQueryul.find('li.current a')[0];	
				if(active){
					activate(active, false);
				}	
				function activate(el,effect){	
					jQuery(el).parent( itemSelector ).siblings().removeClass(activeClassName).children( panelSelector ).slideUp( activationEffectSpeed );	
					jQuery(el).siblings( panelSelector )[(effect || activationEffect)](((effect == "show")?activationEffectSpeed:false),function(){	
						if(jQuery(el).siblings( panelSelector ).is(':visible')){
							jQuery(el).parents( itemSelector ).not(jQueryul.parents()).addClass(activeClassName);
						} else {
							jQuery(el).parent( itemSelector ).removeClass(activeClassName);
						}	
						if(effect == 'show'){
							jQuery(el).parents( itemSelector ).not(jQueryul.parents()).addClass(activeClassName);
						}	
						jQuery(el).parents().show();	
					});	
				}	
			});
		}
	}); 
})(jQuery);

jQuery('.accordion').accordion();	
jQuery('.accordion').each(function(index){
	var activeItems = jQuery(this).find('li.active');
	activeItems.each(function(i){
		jQuery(this).children('ul').css('display', 'block');
		if (i == activeItems.length - 1)
		{
			jQuery(this).addClass("current");
		}
	});
});





/*-------- End Nav js -------------------*/	


(function(jQuery){
	jQuery.fn.extend({
		accordionNew: function() {       
			return this.each(function() {	
				var jQueryul			= jQuery(this),
				elementDataKey			= 'accordiated',
				activeClassName			= 'active',
				activationEffect 		= 'slideToggle',
				panelSelector			= 'ul, div',
				activationEffectSpeed 	= 'fast',
				itemSelector			= 'li';	
				if(jQueryul.data(elementDataKey))
					return false;							
				jQuery.each(jQueryul.find('ul, li>div'), function(){
					jQuery(this).data(elementDataKey, true);
					jQuery(this).hide();
				});	
				jQuery.each(jQueryul.find('em.open-close'), function(){
					jQuery(this).on("click",function(e){
						activate(this, activationEffect);
						return void(0);
					});	
					jQuery(this).bind('activate-node', function(){
						jQueryul.find( panelSelector ).not(jQuery(this).parents()).not(jQuery(this).siblings()).slideUp( activationEffectSpeed );
						activate(this,'slideDown');
					});
				});	
				var active = (location.hash)?jQueryul.find('a[href=' + location.hash + ']')[0]:jQueryul.find('li.current a')[0];	
				if(active){
					activate(active, false);
				}	
				function activate(el,effect){	
					jQuery(el).parent( itemSelector ).siblings().removeClass(activeClassName).children( panelSelector ).slideUp( activationEffectSpeed );	
					jQuery(el).siblings( panelSelector )[(effect || activationEffect)](((effect == "show")?activationEffectSpeed:false),function(){	
						if(jQuery(el).siblings( panelSelector ).is(':visible')){
							jQuery(el).parents( itemSelector ).not(jQueryul.parents()).addClass(activeClassName);
						} else {
							jQuery(el).parent( itemSelector ).removeClass(activeClassName);
						}	
						if(effect == 'show'){
							jQuery(el).parents( itemSelector ).not(jQueryul.parents()).addClass(activeClassName);
						}	
						jQuery(el).parents().show();	
					});	
				}	
			});
		}
	}); 
	jQuery(function () {
	// Remove Search if user Resets Form or hits Escape!
	jQuery('.navbar-collapse form[role="search"] button[type="reset"]').on('click keyup', function(event) {
		console.log(event.currentTarget);
		if (event.which == 27 && jQuery('.navbar-collapse form[role="search"]').hasClass('active') ||
			jQuery(event.currentTarget).attr('type') == 'reset') {
			closeSearch();
	}
});
	
	function closeSearch() {
		var jQueryform = jQuery('.navbar-collapse form[role="search"].active')
		jQueryform.find('input').val('');
		jQueryform.removeClass('active');
	}
	
	// Show Search if form is not active // event.preventDefault() is important, this prevents the form from submitting
	jQuery(document).on('click', '.navbar-collapse form[role="search"]:not(.active) button[type="submit"]', function(event) {
		event.preventDefault();
		var jQueryform = jQuery(this).closest('form'),
		jQueryinput = jQueryform.find('input');
		jQueryform.addClass('active');
		jQueryinput.focus();

	});
	// ONLY FOR DEMO // Please use jQuery('form').submit(function(event)) to track from submission
	// if your form is ajax remember to call `closeSearch()` to close the search container
	jQuery(document).on('click', '.navbar-collapse form[role="search"].active button[type="submit"]', function(event) {
		event.preventDefault();
		var jQueryform = jQuery(this).closest('form'),
		jQueryinput = jQueryform.find('input');
		jQuery('#showSearchTerm').text(jQueryinput.val());
		closeSearch()
	});
});

/*
 * jQuery Cycle Plugin (with Transition Definitions)
 * Examples and documentation at: http://jquery.malsup.com/cycle/
 * Requires: jQuery v1.2.6 or later
 */
 (function(jQuery){var ver="2.86";if(jQuery.support==undefined){jQuery.support={opacity:!(jQuery.browser.msie)};}function debug(s){if(jQuery.fn.cycle.debug){log(s);}}function log(){if(window.console&&window.console.log){window.console.log("[cycle] "+Array.prototype.join.call(arguments," "));}}jQuery.fn.cycle=function(options,arg2){var o={s:this.selector,c:this.context};if(this.length===0&&options!="stop"){if(!jQuery.isReady&&o.s){log("DOM not ready, queuing slideshow");jQuery(function(){jQuery(o.s,o.c).cycle(options,arg2);});return this;}log("terminating; zero elements found by selector"+(jQuery.isReady?"":" (DOM not ready)"));return this;}return this.each(function(){var opts=handleArguments(this,options,arg2);if(opts===false){return;}opts.updateActivePagerLink=opts.updateActivePagerLink||jQuery.fn.cycle.updateActivePagerLink;if(this.cycleTimeout){clearTimeout(this.cycleTimeout);}this.cycleTimeout=this.cyclePause=0;var jQuerycont=jQuery(this);var jQueryslides=opts.slideExpr?jQuery(opts.slideExpr,this):jQuerycont.children();var els=jQueryslides.get();if(els.length<2){log("terminating; too few slides: "+els.length);return;}var opts2=buildOptions(jQuerycont,jQueryslides,els,opts,o);if(opts2===false){return;}var startTime=opts2.continuous?10:getTimeout(opts2.currSlide,opts2.nextSlide,opts2,!opts2.rev);if(startTime){startTime+=(opts2.delay||0);if(startTime<10){startTime=10;}debug("first timeout: "+startTime);this.cycleTimeout=setTimeout(function(){go(els,opts2,0,!opts2.rev);},startTime);}});};function handleArguments(cont,options,arg2){if(cont.cycleStop==undefined){cont.cycleStop=0;}if(options===undefined||options===null){options={};}if(options.constructor==String){switch(options){case"destroy":case"stop":var opts=jQuery(cont).data("cycle.opts");if(!opts){return false;}cont.cycleStop++;if(cont.cycleTimeout){clearTimeout(cont.cycleTimeout);}cont.cycleTimeout=0;jQuery(cont).removeData("cycle.opts");if(options=="destroy"){destroy(opts);}return false;case"toggle":cont.cyclePause=(cont.cyclePause===1)?0:1;checkInstantResume(cont.cyclePause,arg2,cont);return false;case"pause":cont.cyclePause=1;return false;case"resume":cont.cyclePause=0;checkInstantResume(false,arg2,cont);return false;case"prev":case"next":var opts=jQuery(cont).data("cycle.opts");if(!opts){log('options not found, "prev/next" ignored');return false;}jQuery.fn.cycle[options](opts);return false;default:options={fx:options};}return options;}else{if(options.constructor==Number){var num=options;options=jQuery(cont).data("cycle.opts");if(!options){log("options not found, can not advance slide");return false;}if(num<0||num>=options.elements.length){log("invalid slide index: "+num);return false;}options.nextSlide=num;if(cont.cycleTimeout){clearTimeout(cont.cycleTimeout);cont.cycleTimeout=0;}if(typeof arg2=="string"){options.oneTimeFx=arg2;}go(options.elements,options,1,num>=options.currSlide);return false;}}return options;function checkInstantResume(isPaused,arg2,cont){if(!isPaused&&arg2===true){var options=jQuery(cont).data("cycle.opts");if(!options){log("options not found, can not resume");return false;}if(cont.cycleTimeout){clearTimeout(cont.cycleTimeout);cont.cycleTimeout=0;}go(options.elements,options,1,1);}}}function removeFilter(el,opts){if(!jQuery.support.opacity&&opts.cleartype&&el.style.filter){try{el.style.removeAttribute("filter");}catch(smother){}}}function destroy(opts){if(opts.next){jQuery(opts.next).unbind(opts.prevNextEvent);}if(opts.prev){jQuery(opts.prev).unbind(opts.prevNextEvent);}if(opts.pager||opts.pagerAnchorBuilder){jQuery.each(opts.pagerAnchors||[],function(){this.unbind().remove();});}opts.pagerAnchors=null;if(opts.destroy){opts.destroy(opts);}}function buildOptions(jQuerycont,jQueryslides,els,options,o){var opts=jQuery.extend({},jQuery.fn.cycle.defaults,options||{},jQuery.metadata?jQuerycont.metadata():jQuery.meta?jQuerycont.data():{});if(opts.autostop){opts.countdown=opts.autostopCount||els.length;}var cont=jQuerycont[0];jQuerycont.data("cycle.opts",opts);opts.jQuerycont=jQuerycont;opts.stopCount=cont.cycleStop;opts.elements=els;opts.before=opts.before?[opts.before]:[];opts.after=opts.after?[opts.after]:[];opts.after.unshift(function(){opts.busy=0;});if(!jQuery.support.opacity&&opts.cleartype){opts.after.push(function(){removeFilter(this,opts);});}if(opts.continuous){opts.after.push(function(){go(els,opts,0,!opts.rev);});}saveOriginalOpts(opts);if(!jQuery.support.opacity&&opts.cleartype&&!opts.cleartypeNoBg){clearTypeFix(jQueryslides);}if(jQuerycont.css("position")=="static"){jQuerycont.css("position","relative");}if(opts.width){jQuerycont.width(opts.width);}if(opts.height&&opts.height!="auto"){jQuerycont.height(opts.height);}if(opts.startingSlide){opts.startingSlide=parseInt(opts.startingSlide);}if(opts.random){opts.randomMap=[];for(var i=0;i<els.length;i++){opts.randomMap.push(i);}opts.randomMap.sort(function(a,b){return Math.random()-0.5;});opts.randomIndex=1;opts.startingSlide=opts.randomMap[1];}else{if(opts.startingSlide>=els.length){opts.startingSlide=0;}}opts.currSlide=opts.startingSlide||0;var first=opts.startingSlide;jQueryslides.css({position:"absolute",top:0,left:0}).hide().each(function(i){var z=first?i>=first?els.length-(i-first):first-i:els.length-i;jQuery(this).css("z-index",z);});jQuery(els[first]).css("opacity",1).show();removeFilter(els[first],opts);if(opts.fit&&opts.width){jQueryslides.width(opts.width);}if(opts.fit&&opts.height&&opts.height!="auto"){jQueryslides.height(opts.height);}var reshape=opts.containerResize&&!jQuerycont.innerHeight();if(reshape){var maxw=0,maxh=0;for(var j=0;j<els.length;j++){var jQuerye=jQuery(els[j]),e=jQuerye[0],w=jQuerye.outerWidth(),h=jQuerye.outerHeight();if(!w){w=e.offsetWidth||e.width||jQuerye.attr("width");}if(!h){h=e.offsetHeight||e.height||jQuerye.attr("height");}maxw=w>maxw?w:maxw;maxh=h>maxh?h:maxh;}if(maxw>0&&maxh>0){jQuerycont.css({width:maxw+"px",height:maxh+"px"});}}if(opts.pause){jQuerycont.hover(function(){this.cyclePause++;},function(){this.cyclePause--;});}if(supportMultiTransitions(opts)===false){return false;}var requeue=false;options.requeueAttempts=options.requeueAttempts||0;jQueryslides.each(function(){var jQueryel=jQuery(this);this.cycleH=(opts.fit&&opts.height)?opts.height:(jQueryel.height()||this.offsetHeight||this.height||jQueryel.attr("height")||0);this.cycleW=(opts.fit&&opts.width)?opts.width:(jQueryel.width()||this.offsetWidth||this.width||jQueryel.attr("width")||0);if(jQueryel.is("img")){var loadingIE=(jQuery.browser.msie&&this.cycleW==28&&this.cycleH==30&&!this.complete);var loadingFF=(jQuery.browser.mozilla&&this.cycleW==34&&this.cycleH==19&&!this.complete);var loadingOp=(jQuery.browser.opera&&((this.cycleW==42&&this.cycleH==19)||(this.cycleW==37&&this.cycleH==17))&&!this.complete);var loadingOther=(this.cycleH==0&&this.cycleW==0&&!this.complete);if(loadingIE||loadingFF||loadingOp||loadingOther){if(o.s&&opts.requeueOnImageNotLoaded&&++options.requeueAttempts<100){log(options.requeueAttempts," - img slide not loaded, requeuing slideshow: ",this.src,this.cycleW,this.cycleH);setTimeout(function(){jQuery(o.s,o.c).cycle(options);},opts.requeueTimeout);requeue=true;return false;}else{log("could not determine size of image: "+this.src,this.cycleW,this.cycleH);}}}return true;});if(requeue){return false;}opts.cssBefore=opts.cssBefore||{};opts.animIn=opts.animIn||{};opts.animOut=opts.animOut||{};jQueryslides.not(":eq("+first+")").css(opts.cssBefore);if(opts.cssFirst){jQuery(jQueryslides[first]).css(opts.cssFirst);}if(opts.timeout){opts.timeout=parseInt(opts.timeout);if(opts.speed.constructor==String){opts.speed=jQuery.fx.speeds[opts.speed]||parseInt(opts.speed);}if(!opts.sync){opts.speed=opts.speed/2;}var buffer=opts.fx=="shuffle"?500:250;while((opts.timeout-opts.speed)<buffer){opts.timeout+=opts.speed;}}if(opts.easing){opts.easeIn=opts.easeOut=opts.easing;}if(!opts.speedIn){opts.speedIn=opts.speed;}if(!opts.speedOut){opts.speedOut=opts.speed;}opts.slideCount=els.length;opts.currSlide=opts.lastSlide=first;if(opts.random){if(++opts.randomIndex==els.length){opts.randomIndex=0;}opts.nextSlide=opts.randomMap[opts.randomIndex];}else{opts.nextSlide=opts.startingSlide>=(els.length-1)?0:opts.startingSlide+1;}if(!opts.multiFx){var init=jQuery.fn.cycle.transitions[opts.fx];if(jQuery.isFunction(init)){init(jQuerycont,jQueryslides,opts);}else{if(opts.fx!="custom"&&!opts.multiFx){log("unknown transition: "+opts.fx,"; slideshow terminating");return false;}}}var e0=jQueryslides[first];if(opts.before.length){opts.before[0].apply(e0,[e0,e0,opts,true]);}if(opts.after.length>1){opts.after[1].apply(e0,[e0,e0,opts,true]);}if(opts.next){jQuery(opts.next).bind(opts.prevNextEvent,function(){return advance(opts,opts.rev?-1:1);});}if(opts.prev){jQuery(opts.prev).bind(opts.prevNextEvent,function(){return advance(opts,opts.rev?1:-1);});}if(opts.pager||opts.pagerAnchorBuilder){buildPager(els,opts);}exposeAddSlide(opts,els);return opts;}function saveOriginalOpts(opts){opts.original={before:[],after:[]};opts.original.cssBefore=jQuery.extend({},opts.cssBefore);opts.original.cssAfter=jQuery.extend({},opts.cssAfter);opts.original.animIn=jQuery.extend({},opts.animIn);opts.original.animOut=jQuery.extend({},opts.animOut);jQuery.each(opts.before,function(){opts.original.before.push(this);});jQuery.each(opts.after,function(){opts.original.after.push(this);});}function supportMultiTransitions(opts){var i,tx,txs=jQuery.fn.cycle.transitions;if(opts.fx.indexOf(",")>0){opts.multiFx=true;opts.fxs=opts.fx.replace(/\s*/g,"").split(",");for(i=0;i<opts.fxs.length;i++){var fx=opts.fxs[i];tx=txs[fx];if(!tx||!txs.hasOwnProperty(fx)||!jQuery.isFunction(tx)){log("discarding unknown transition: ",fx);opts.fxs.splice(i,1);i--;}}if(!opts.fxs.length){log("No valid transitions named; slideshow terminating.");return false;}}else{if(opts.fx=="all"){opts.multiFx=true;opts.fxs=[];for(p in txs){tx=txs[p];if(txs.hasOwnProperty(p)&&jQuery.isFunction(tx)){opts.fxs.push(p);}}}}if(opts.multiFx&&opts.randomizeEffects){var r1=Math.floor(Math.random()*20)+30;for(i=0;i<r1;i++){var r2=Math.floor(Math.random()*opts.fxs.length);opts.fxs.push(opts.fxs.splice(r2,1)[0]);}debug("randomized fx sequence: ",opts.fxs);}return true;}function exposeAddSlide(opts,els){opts.addSlide=function(newSlide,prepend){var jQuerys=jQuery(newSlide),s=jQuerys[0];if(!opts.autostopCount){opts.countdown++;}els[prepend?"unshift":"push"](s);if(opts.els){opts.els[prepend?"unshift":"push"](s);}opts.slideCount=els.length;jQuerys.css("position","absolute");jQuerys[prepend?"prependTo":"appendTo"](opts.jQuerycont);if(prepend){opts.currSlide++;opts.nextSlide++;}if(!jQuery.support.opacity&&opts.cleartype&&!opts.cleartypeNoBg){clearTypeFix(jQuerys);}if(opts.fit&&opts.width){jQuerys.width(opts.width);}if(opts.fit&&opts.height&&opts.height!="auto"){jQueryslides.height(opts.height);}s.cycleH=(opts.fit&&opts.height)?opts.height:jQuerys.height();s.cycleW=(opts.fit&&opts.width)?opts.width:jQuerys.width();jQuerys.css(opts.cssBefore);if(opts.pager||opts.pagerAnchorBuilder){jQuery.fn.cycle.createPagerAnchor(els.length-1,s,jQuery(opts.pager),els,opts);}if(jQuery.isFunction(opts.onAddSlide)){opts.onAddSlide(jQuerys);}else{jQuerys.hide();}};}jQuery.fn.cycle.resetState=function(opts,fx){fx=fx||opts.fx;opts.before=[];opts.after=[];opts.cssBefore=jQuery.extend({},opts.original.cssBefore);opts.cssAfter=jQuery.extend({},opts.original.cssAfter);opts.animIn=jQuery.extend({},opts.original.animIn);opts.animOut=jQuery.extend({},opts.original.animOut);opts.fxFn=null;jQuery.each(opts.original.before,function(){opts.before.push(this);});jQuery.each(opts.original.after,function(){opts.after.push(this);});var init=jQuery.fn.cycle.transitions[fx];if(jQuery.isFunction(init)){init(opts.jQuerycont,jQuery(opts.elements),opts);}};function go(els,opts,manual,fwd){if(manual&&opts.busy&&opts.manualTrump){debug("manualTrump in go(), stopping active transition");jQuery(els).stop(true,true);opts.busy=false;}if(opts.busy){debug("transition active, ignoring new tx request");return;}var p=opts.jQuerycont[0],curr=els[opts.currSlide],next=els[opts.nextSlide];if(p.cycleStop!=opts.stopCount||p.cycleTimeout===0&&!manual){return;}if(!manual&&!p.cyclePause&&((opts.autostop&&(--opts.countdown<=0))||(opts.nowrap&&!opts.random&&opts.nextSlide<opts.currSlide))){if(opts.end){opts.end(opts);}return;}var changed=false;if((manual||!p.cyclePause)&&(opts.nextSlide!=opts.currSlide)){changed=true;var fx=opts.fx;curr.cycleH=curr.cycleH||jQuery(curr).height();curr.cycleW=curr.cycleW||jQuery(curr).width();next.cycleH=next.cycleH||jQuery(next).height();next.cycleW=next.cycleW||jQuery(next).width();if(opts.multiFx){if(opts.lastFx==undefined||++opts.lastFx>=opts.fxs.length){opts.lastFx=0;}fx=opts.fxs[opts.lastFx];opts.currFx=fx;}if(opts.oneTimeFx){fx=opts.oneTimeFx;opts.oneTimeFx=null;}jQuery.fn.cycle.resetState(opts,fx);if(opts.before.length){jQuery.each(opts.before,function(i,o){if(p.cycleStop!=opts.stopCount){return;}o.apply(next,[curr,next,opts,fwd]);});}var after=function(){jQuery.each(opts.after,function(i,o){if(p.cycleStop!=opts.stopCount){return;}o.apply(next,[curr,next,opts,fwd]);});};debug("tx firing; currSlide: "+opts.currSlide+"; nextSlide: "+opts.nextSlide);opts.busy=1;if(opts.fxFn){opts.fxFn(curr,next,opts,after,fwd,manual&&opts.fastOnEvent);}else{if(jQuery.isFunction(jQuery.fn.cycle[opts.fx])){jQuery.fn.cycle[opts.fx](curr,next,opts,after,fwd,manual&&opts.fastOnEvent);}else{jQuery.fn.cycle.custom(curr,next,opts,after,fwd,manual&&opts.fastOnEvent);}}}if(changed||opts.nextSlide==opts.currSlide){opts.lastSlide=opts.currSlide;if(opts.random){opts.currSlide=opts.nextSlide;if(++opts.randomIndex==els.length){opts.randomIndex=0;}opts.nextSlide=opts.randomMap[opts.randomIndex];if(opts.nextSlide==opts.currSlide){opts.nextSlide=(opts.currSlide==opts.slideCount-1)?0:opts.currSlide+1;}}else{var roll=(opts.nextSlide+1)==els.length;opts.nextSlide=roll?0:opts.nextSlide+1;opts.currSlide=roll?els.length-1:opts.nextSlide-1;}}if(changed&&opts.pager){opts.updateActivePagerLink(opts.pager,opts.currSlide,opts.activePagerClass);}var ms=0;if(opts.timeout&&!opts.continuous){ms=getTimeout(curr,next,opts,fwd);}else{if(opts.continuous&&p.cyclePause){ms=10;}}if(ms>0){p.cycleTimeout=setTimeout(function(){go(els,opts,0,!opts.rev);},ms);}}jQuery.fn.cycle.updateActivePagerLink=function(pager,currSlide,clsName){jQuery(pager).each(function(){jQuery(this).children().removeClass(clsName).eq(currSlide).addClass(clsName);});};function getTimeout(curr,next,opts,fwd){if(opts.timeoutFn){var t=opts.timeoutFn(curr,next,opts,fwd);while((t-opts.speed)<250){t+=opts.speed;}debug("calculated timeout: "+t+"; speed: "+opts.speed);if(t!==false){return t;}}return opts.timeout;}jQuery.fn.cycle.next=function(opts){advance(opts,opts.rev?-1:1);};jQuery.fn.cycle.prev=function(opts){advance(opts,opts.rev?1:-1);};function advance(opts,val){var els=opts.elements;var p=opts.jQuerycont[0],timeout=p.cycleTimeout;if(timeout){clearTimeout(timeout);p.cycleTimeout=0;}if(opts.random&&val<0){opts.randomIndex--;if(--opts.randomIndex==-2){opts.randomIndex=els.length-2;}else{if(opts.randomIndex==-1){opts.randomIndex=els.length-1;}}opts.nextSlide=opts.randomMap[opts.randomIndex];}else{if(opts.random){opts.nextSlide=opts.randomMap[opts.randomIndex];}else{opts.nextSlide=opts.currSlide+val;if(opts.nextSlide<0){if(opts.nowrap){return false;}opts.nextSlide=els.length-1;}else{if(opts.nextSlide>=els.length){if(opts.nowrap){return false;}opts.nextSlide=0;}}}}var cb=opts.onPrevNextEvent||opts.prevNextClick;if(jQuery.isFunction(cb)){cb(val>0,opts.nextSlide,els[opts.nextSlide]);}go(els,opts,1,val>=0);return false;}function buildPager(els,opts){var jQueryp=jQuery(opts.pager);jQuery.each(els,function(i,o){jQuery.fn.cycle.createPagerAnchor(i,o,jQueryp,els,opts);});opts.updateActivePagerLink(opts.pager,opts.startingSlide,opts.activePagerClass);}jQuery.fn.cycle.createPagerAnchor=function(i,el,jQueryp,els,opts){var a;if(jQuery.isFunction(opts.pagerAnchorBuilder)){a=opts.pagerAnchorBuilder(i,el);debug("pagerAnchorBuilder("+i+", el) returned: "+a);}else{a='<a href="#">'+(i+1)+"</a>";}if(!a){return;}var jQuerya=jQuery(a);if(jQuerya.parents("body").length===0){var arr=[];if(jQueryp.length>1){jQueryp.each(function(){var jQueryclone=jQuerya.clone(true);jQuery(this).append(jQueryclone);arr.push(jQueryclone[0]);});jQuerya=jQuery(arr);}else{jQuerya.appendTo(jQueryp);}}opts.pagerAnchors=opts.pagerAnchors||[];opts.pagerAnchors.push(jQuerya);jQuerya.bind(opts.pagerEvent,function(e){e.preventDefault();opts.nextSlide=i;var p=opts.jQuerycont[0],timeout=p.cycleTimeout;if(timeout){clearTimeout(timeout);p.cycleTimeout=0;}var cb=opts.onPagerEvent||opts.pagerClick;if(jQuery.isFunction(cb)){cb(opts.nextSlide,els[opts.nextSlide]);}go(els,opts,1,opts.currSlide<i);});if(!/^click/.test(opts.pagerEvent)&&!opts.allowPagerClickBubble){jQuerya.bind("click.cycle",function(){return false;});}if(opts.pauseOnPagerHover){jQuerya.hover(function(){opts.jQuerycont[0].cyclePause++;},function(){opts.jQuerycont[0].cyclePause--;});}};jQuery.fn.cycle.hopsFromLast=function(opts,fwd){var hops,l=opts.lastSlide,c=opts.currSlide;if(fwd){hops=c>l?c-l:opts.slideCount-l;}else{hops=c<l?l-c:l+opts.slideCount-c;}return hops;};function clearTypeFix(jQueryslides){debug("applying clearType background-color hack");function hex(s){s=parseInt(s).toString(16);return s.length<2?"0"+s:s;}function getBg(e){for(;e&&e.nodeName.toLowerCase()!="html";e=e.parentNode){var v=jQuery.css(e,"background-color");if(v.indexOf("rgb")>=0){var rgb=v.match(/\d+/g);return"#"+hex(rgb[0])+hex(rgb[1])+hex(rgb[2]);}if(v&&v!="transparent"){return v;}}return"#ffffff";}jQueryslides.each(function(){jQuery(this).css("background-color",getBg(this));});}jQuery.fn.cycle.commonReset=function(curr,next,opts,w,h,rev){jQuery(opts.elements).not(curr).hide();opts.cssBefore.opacity=1;opts.cssBefore.display="block";if(w!==false&&next.cycleW>0){opts.cssBefore.width=next.cycleW;}if(h!==false&&next.cycleH>0){opts.cssBefore.height=next.cycleH;}opts.cssAfter=opts.cssAfter||{};opts.cssAfter.display="none";jQuery(curr).css("zIndex",opts.slideCount+(rev===true?1:0));jQuery(next).css("zIndex",opts.slideCount+(rev===true?0:1));};jQuery.fn.cycle.custom=function(curr,next,opts,cb,fwd,speedOverride){var jQueryl=jQuery(curr),jQueryn=jQuery(next);var speedIn=opts.speedIn,speedOut=opts.speedOut,easeIn=opts.easeIn,easeOut=opts.easeOut;jQueryn.css(opts.cssBefore);if(speedOverride){if(typeof speedOverride=="number"){speedIn=speedOut=speedOverride;}else{speedIn=speedOut=1;}easeIn=easeOut=null;}var fn=function(){jQueryn.animate(opts.animIn,speedIn,easeIn,cb);};jQueryl.animate(opts.animOut,speedOut,easeOut,function(){if(opts.cssAfter){jQueryl.css(opts.cssAfter);}if(!opts.sync){fn();}});if(opts.sync){fn();}};jQuery.fn.cycle.transitions={fade:function(jQuerycont,jQueryslides,opts){jQueryslides.not(":eq("+opts.currSlide+")").css("opacity",0);opts.before.push(function(curr,next,opts){jQuery.fn.cycle.commonReset(curr,next,opts);opts.cssBefore.opacity=0;});opts.animIn={opacity:1};opts.animOut={opacity:0};opts.cssBefore={top:0,left:0};}};jQuery.fn.cycle.ver=function(){return ver;};jQuery.fn.cycle.defaults={fx:"fade",timeout:4000,timeoutFn:null,continuous:0,speed:1000,speedIn:null,speedOut:null,next:null,prev:null,onPrevNextEvent:null,prevNextEvent:"click.cycle",pager:null,onPagerEvent:null,pagerEvent:"click.cycle",allowPagerClickBubble:false,pagerAnchorBuilder:null,before:null,after:null,end:null,easing:null,easeIn:null,easeOut:null,shuffle:null,animIn:null,animOut:null,cssBefore:null,cssAfter:null,fxFn:null,height:"auto",startingSlide:0,sync:1,random:0,fit:0,containerResize:1,pause:0,pauseOnPagerHover:0,autostop:0,autostopCount:0,delay:0,slideExpr:null,cleartype:!jQuery.support.opacity,cleartypeNoBg:false,nowrap:0,fastOnEvent:0,randomizeEffects:1,rev:0,manualTrump:true,requeueOnImageNotLoaded:true,requeueTimeout:250,activePagerClass:"activeSlide",updateActivePagerLink:null};})(jQuery);
/*
 * jQuery Cycle Plugin Transition Definitions
 */
 (function(jQuery){jQuery.fn.cycle.transitions.none=function(jQuerycont,jQueryslides,opts){opts.fxFn=function(curr,next,opts,after){jQuery(next).show();jQuery(curr).hide();after();};};jQuery.fn.cycle.transitions.scrollUp=function(jQuerycont,jQueryslides,opts){jQuerycont.css("overflow","hidden");opts.before.push(jQuery.fn.cycle.commonReset);var h=jQuerycont.height();opts.cssBefore={top:h,left:0};opts.cssFirst={top:0};opts.animIn={top:0};opts.animOut={top:-h};};jQuery.fn.cycle.transitions.scrollDown=function(jQuerycont,jQueryslides,opts){jQuerycont.css("overflow","hidden");opts.before.push(jQuery.fn.cycle.commonReset);var h=jQuerycont.height();opts.cssFirst={top:0};opts.cssBefore={top:-h,left:0};opts.animIn={top:0};opts.animOut={top:h};};jQuery.fn.cycle.transitions.scrollLeft=function(jQuerycont,jQueryslides,opts){jQuerycont.css("overflow","hidden");opts.before.push(jQuery.fn.cycle.commonReset);var w=jQuerycont.width();opts.cssFirst={left:0};opts.cssBefore={left:w,top:0};opts.animIn={left:0};opts.animOut={left:0-w};};jQuery.fn.cycle.transitions.scrollRight=function(jQuerycont,jQueryslides,opts){jQuerycont.css("overflow","hidden");opts.before.push(jQuery.fn.cycle.commonReset);var w=jQuerycont.width();opts.cssFirst={left:0};opts.cssBefore={left:-w,top:0};opts.animIn={left:0};opts.animOut={left:w};};jQuery.fn.cycle.transitions.scrollHorz=function(jQuerycont,jQueryslides,opts){jQuerycont.css("overflow","hidden").width();opts.before.push(function(curr,next,opts,fwd){jQuery.fn.cycle.commonReset(curr,next,opts);opts.cssBefore.left=fwd?(next.cycleW-1):(1-next.cycleW);opts.animOut.left=fwd?-curr.cycleW:curr.cycleW;});opts.cssFirst={left:0};opts.cssBefore={top:0};opts.animIn={left:0};opts.animOut={top:0};};jQuery.fn.cycle.transitions.scrollVert=function(jQuerycont,jQueryslides,opts){jQuerycont.css("overflow","hidden");opts.before.push(function(curr,next,opts,fwd){jQuery.fn.cycle.commonReset(curr,next,opts);opts.cssBefore.top=fwd?(1-next.cycleH):(next.cycleH-1);opts.animOut.top=fwd?curr.cycleH:-curr.cycleH;});opts.cssFirst={top:0};opts.cssBefore={left:0};opts.animIn={top:0};opts.animOut={left:0};};jQuery.fn.cycle.transitions.slideX=function(jQuerycont,jQueryslides,opts){opts.before.push(function(curr,next,opts){jQuery(opts.elements).not(curr).hide();jQuery.fn.cycle.commonReset(curr,next,opts,false,true);opts.animIn.width=next.cycleW;});opts.cssBefore={left:0,top:0,width:0};opts.animIn={width:"show"};opts.animOut={width:0};};jQuery.fn.cycle.transitions.slideY=function(jQuerycont,jQueryslides,opts){opts.before.push(function(curr,next,opts){jQuery(opts.elements).not(curr).hide();jQuery.fn.cycle.commonReset(curr,next,opts,true,false);opts.animIn.height=next.cycleH;});opts.cssBefore={left:0,top:0,height:0};opts.animIn={height:"show"};opts.animOut={height:0};};jQuery.fn.cycle.transitions.shuffle=function(jQuerycont,jQueryslides,opts){var i,w=jQuerycont.css("overflow","visible").width();jQueryslides.css({left:0,top:0});opts.before.push(function(curr,next,opts){jQuery.fn.cycle.commonReset(curr,next,opts,true,true,true);});if(!opts.speedAdjusted){opts.speed=opts.speed/2;opts.speedAdjusted=true;}opts.random=0;opts.shuffle=opts.shuffle||{left:-w,top:15};opts.els=[];for(i=0;i<jQueryslides.length;i++){opts.els.push(jQueryslides[i]);}for(i=0;i<opts.currSlide;i++){opts.els.push(opts.els.shift());}opts.fxFn=function(curr,next,opts,cb,fwd){var jQueryel=fwd?jQuery(curr):jQuery(next);jQuery(next).css(opts.cssBefore);var count=opts.slideCount;jQueryel.animate(opts.shuffle,opts.speedIn,opts.easeIn,function(){var hops=jQuery.fn.cycle.hopsFromLast(opts,fwd);for(var k=0;k<hops;k++){fwd?opts.els.push(opts.els.shift()):opts.els.unshift(opts.els.pop());}if(fwd){for(var i=0,len=opts.els.length;i<len;i++){jQuery(opts.els[i]).css("z-index",len-i+count);}}else{var z=jQuery(curr).css("z-index");jQueryel.css("z-index",parseInt(z)+1+count);}jQueryel.animate({left:0,top:0},opts.speedOut,opts.easeOut,function(){jQuery(fwd?this:curr).hide();if(cb){cb();}});});};opts.cssBefore={display:"block",opacity:1,top:0,left:0};};jQuery.fn.cycle.transitions.turnUp=function(jQuerycont,jQueryslides,opts){opts.before.push(function(curr,next,opts){jQuery.fn.cycle.commonReset(curr,next,opts,true,false);opts.cssBefore.top=next.cycleH;opts.animIn.height=next.cycleH;});opts.cssFirst={top:0};opts.cssBefore={left:0,height:0};opts.animIn={top:0};opts.animOut={height:0};};jQuery.fn.cycle.transitions.turnDown=function(jQuerycont,jQueryslides,opts){opts.before.push(function(curr,next,opts){jQuery.fn.cycle.commonReset(curr,next,opts,true,false);opts.animIn.height=next.cycleH;opts.animOut.top=curr.cycleH;});opts.cssFirst={top:0};opts.cssBefore={left:0,top:0,height:0};opts.animOut={height:0};};jQuery.fn.cycle.transitions.turnLeft=function(jQuerycont,jQueryslides,opts){opts.before.push(function(curr,next,opts){jQuery.fn.cycle.commonReset(curr,next,opts,false,true);opts.cssBefore.left=next.cycleW;opts.animIn.width=next.cycleW;});opts.cssBefore={top:0,width:0};opts.animIn={left:0};opts.animOut={width:0};};jQuery.fn.cycle.transitions.turnRight=function(jQuerycont,jQueryslides,opts){opts.before.push(function(curr,next,opts){jQuery.fn.cycle.commonReset(curr,next,opts,false,true);opts.animIn.width=next.cycleW;opts.animOut.left=curr.cycleW;});opts.cssBefore={top:0,left:0,width:0};opts.animIn={left:0};opts.animOut={width:0};};jQuery.fn.cycle.transitions.zoom=function(jQuerycont,jQueryslides,opts){opts.before.push(function(curr,next,opts){jQuery.fn.cycle.commonReset(curr,next,opts,false,false,true);opts.cssBefore.top=next.cycleH/2;opts.cssBefore.left=next.cycleW/2;opts.animIn={top:0,left:0,width:next.cycleW,height:next.cycleH};opts.animOut={width:0,height:0,top:curr.cycleH/2,left:curr.cycleW/2};});opts.cssFirst={top:0,left:0};opts.cssBefore={width:0,height:0};};jQuery.fn.cycle.transitions.fadeZoom=function(jQuerycont,jQueryslides,opts){opts.before.push(function(curr,next,opts){jQuery.fn.cycle.commonReset(curr,next,opts,false,false);opts.cssBefore.left=next.cycleW/2;opts.cssBefore.top=next.cycleH/2;opts.animIn={top:0,left:0,width:next.cycleW,height:next.cycleH};});opts.cssBefore={width:0,height:0};opts.animOut={opacity:0};};jQuery.fn.cycle.transitions.blindX=function(jQuerycont,jQueryslides,opts){var w=jQuerycont.css("overflow","hidden").width();opts.before.push(function(curr,next,opts){jQuery.fn.cycle.commonReset(curr,next,opts);opts.animIn.width=next.cycleW;opts.animOut.left=curr.cycleW;});opts.cssBefore={left:w,top:0};opts.animIn={left:0};opts.animOut={left:w};};jQuery.fn.cycle.transitions.blindY=function(jQuerycont,jQueryslides,opts){var h=jQuerycont.css("overflow","hidden").height();opts.before.push(function(curr,next,opts){jQuery.fn.cycle.commonReset(curr,next,opts);opts.animIn.height=next.cycleH;opts.animOut.top=curr.cycleH;});opts.cssBefore={top:h,left:0};opts.animIn={top:0};opts.animOut={top:h};};jQuery.fn.cycle.transitions.blindZ=function(jQuerycont,jQueryslides,opts){var h=jQuerycont.css("overflow","hidden").height();var w=jQuerycont.width();opts.before.push(function(curr,next,opts){jQuery.fn.cycle.commonReset(curr,next,opts);opts.animIn.height=next.cycleH;opts.animOut.top=curr.cycleH;});opts.cssBefore={top:h,left:w};opts.animIn={top:0,left:0};opts.animOut={top:h,left:w};};jQuery.fn.cycle.transitions.growX=function(jQuerycont,jQueryslides,opts){opts.before.push(function(curr,next,opts){jQuery.fn.cycle.commonReset(curr,next,opts,false,true);opts.cssBefore.left=this.cycleW/2;opts.animIn={left:0,width:this.cycleW};opts.animOut={left:0};});opts.cssBefore={width:0,top:0};};jQuery.fn.cycle.transitions.growY=function(jQuerycont,jQueryslides,opts){opts.before.push(function(curr,next,opts){jQuery.fn.cycle.commonReset(curr,next,opts,true,false);opts.cssBefore.top=this.cycleH/2;opts.animIn={top:0,height:this.cycleH};opts.animOut={top:0};});opts.cssBefore={height:0,left:0};};jQuery.fn.cycle.transitions.curtainX=function(jQuerycont,jQueryslides,opts){opts.before.push(function(curr,next,opts){jQuery.fn.cycle.commonReset(curr,next,opts,false,true,true);opts.cssBefore.left=next.cycleW/2;opts.animIn={left:0,width:this.cycleW};opts.animOut={left:curr.cycleW/2,width:0};});opts.cssBefore={top:0,width:0};};jQuery.fn.cycle.transitions.curtainY=function(jQuerycont,jQueryslides,opts){opts.before.push(function(curr,next,opts){jQuery.fn.cycle.commonReset(curr,next,opts,true,false,true);opts.cssBefore.top=next.cycleH/2;opts.animIn={top:0,height:next.cycleH};opts.animOut={top:curr.cycleH/2,height:0};});opts.cssBefore={left:0,height:0};};jQuery.fn.cycle.transitions.cover=function(jQuerycont,jQueryslides,opts){var d=opts.direction||"left";var w=jQuerycont.css("overflow","hidden").width();var h=jQuerycont.height();opts.before.push(function(curr,next,opts){jQuery.fn.cycle.commonReset(curr,next,opts);if(d=="right"){opts.cssBefore.left=-w;}else{if(d=="up"){opts.cssBefore.top=h;}else{if(d=="down"){opts.cssBefore.top=-h;}else{opts.cssBefore.left=w;}}}});opts.animIn={left:0,top:0};opts.animOut={opacity:1};opts.cssBefore={top:0,left:0};};jQuery.fn.cycle.transitions.uncover=function(jQuerycont,jQueryslides,opts){var d=opts.direction||"left";var w=jQuerycont.css("overflow","hidden").width();var h=jQuerycont.height();opts.before.push(function(curr,next,opts){jQuery.fn.cycle.commonReset(curr,next,opts,true,true,true);if(d=="right"){opts.animOut.left=w;}else{if(d=="up"){opts.animOut.top=-h;}else{if(d=="down"){opts.animOut.top=h;}else{opts.animOut.left=-w;}}}});opts.animIn={left:0,top:0};opts.animOut={opacity:1};opts.cssBefore={top:0,left:0};};jQuery.fn.cycle.transitions.toss=function(jQuerycont,jQueryslides,opts){var w=jQuerycont.css("overflow","visible").width();var h=jQuerycont.height();opts.before.push(function(curr,next,opts){jQuery.fn.cycle.commonReset(curr,next,opts,true,true,true);if(!opts.animOut.left&&!opts.animOut.top){opts.animOut={left:w*2,top:-h/2,opacity:0};}else{opts.animOut.opacity=0;}});opts.cssBefore={left:0,top:0};opts.animIn={left:0};};jQuery.fn.cycle.transitions.wipe=function(jQuerycont,jQueryslides,opts){var w=jQuerycont.css("overflow","hidden").width();var h=jQuerycont.height();opts.cssBefore=opts.cssBefore||{};var clip;if(opts.clip){if(/l2r/.test(opts.clip)){clip="rect(0px 0px "+h+"px 0px)";}else{if(/r2l/.test(opts.clip)){clip="rect(0px "+w+"px "+h+"px "+w+"px)";}else{if(/t2b/.test(opts.clip)){clip="rect(0px "+w+"px 0px 0px)";}else{if(/b2t/.test(opts.clip)){clip="rect("+h+"px "+w+"px "+h+"px 0px)";}else{if(/zoom/.test(opts.clip)){var top=parseInt(h/2);var left=parseInt(w/2);clip="rect("+top+"px "+left+"px "+top+"px "+left+"px)";}}}}}}opts.cssBefore.clip=opts.cssBefore.clip||clip||"rect(0px 0px 0px 0px)";var d=opts.cssBefore.clip.match(/(\d+)/g);var t=parseInt(d[0]),r=parseInt(d[1]),b=parseInt(d[2]),l=parseInt(d[3]);opts.before.push(function(curr,next,opts){if(curr==next){return;}var jQuerycurr=jQuery(curr),jQuerynext=jQuery(next);jQuery.fn.cycle.commonReset(curr,next,opts,true,true,false);opts.cssAfter.display="block";var step=1,count=parseInt((opts.speedIn/13))-1;(function f(){var tt=t?t-parseInt(step*(t/count)):0;var ll=l?l-parseInt(step*(l/count)):0;var bb=b<h?b+parseInt(step*((h-b)/count||1)):h;var rr=r<w?r+parseInt(step*((w-r)/count||1)):w;jQuerynext.css({clip:"rect("+tt+"px "+rr+"px "+bb+"px "+ll+"px)"});(step++<=count)?setTimeout(f,13):jQuerycurr.css("display","none");})();});opts.cssBefore={display:"block",opacity:1,top:0,left:0};opts.animIn={left:0};opts.animOut={left:0};};})(jQuery);
})(jQuery);
/*============= End Left Nav =============*/
jQuery(document).off("click", ".link-wishlist");

jQuery(document).on("click", ".link-wishlist", function() {

	var b = yith_wcwl_l10n.ajax_url;
	var opts = {
		add_to_wishlist: jQuery(this).data("product-id"),
		product_type: jQuery(this).data("product-type"),
		action: "add_to_wishlist"
	};
	mgk_yith_ajax_wish_list(jQuery(this), b, opts);
	return false;
});

mgk_yith_ajax_wish_list = function(obj, ajaxurl, opts) {
	jQuery.ajax({
		type: "POST",
		url: ajaxurl,
		data: "product_id=" + opts.add_to_wishlist + "&" + jQuery.param(opts),
		dataType: 'json',
		success: function(resp) {
			response_result = resp.result,
			response_message = resp.message;
           // alert(response_result+"----"+response_message);
           jQuery('body .page div#notification').remove();
           var ntop = jQuery('#wpadminbar') !== undefined ? jQuery('#wpadminbar').height() : 10;
           if (response_result == 'true') {
           
            if (js_creta_wishvar.MGK_ADD_TO_WISHLIST_SUCCESS_TEXT !== undefined)
            	jQuery('<div id="notification" class="row"><div class="success">' + js_creta_wishvar.MGK_ADD_TO_WISHLIST_SUCCESS_TEXT + '<img class="close" alt="" src="' + js_creta_wishvar.IMAGEURL + '/close.png"></div></div>').prependTo('body .page');
            jQuery('body .page div#notification').css('top', ntop + 'px');
            jQuery('body .page div#notification > div').fadeIn('show');
            jQuery('html,body').animate({
            	scrollTop: 0
            }, 300);
        } else if (response_result == 'exists') {
        	if (js_creta_wishvar.MGK_ADD_TO_WISHLIST_EXISTS_TEXT !== undefined)
        		jQuery('<div id="notification" class="row"><div class="success">' + js_creta_wishvar.MGK_ADD_TO_WISHLIST_EXISTS_TEXT + '<img class="close" alt="" src="' + js_creta_wishvar.IMAGEURL + '/close.png"></div></div>').prependTo('body .page');
        	jQuery('body .page div#notification').css('top', ntop + 'px');
        	jQuery('body .page div#notification > div').fadeIn('show');
        	jQuery('html,body').animate({
        		scrollTop: 0
        	}, 300);

        }
        setTimeout(function() {
        	removeNft();
        }, 10000);

    }
});
};

var removeNft = function() {
	if (jQuery("#notification") !== undefined)
		jQuery("#notification").remove();
};

jQuery(document).on("click", "#notification img.close", function() {
	removeNft();
});




/*** Version 4 detail page js ***/
jQuery(function ($) {
	var carCount = jQuery('.product-img-box .verticl-carousel').find('a').length;
	if (carCount <= 3) {
		jQuery('.product-img-box .more-views-nav').hide();
	}
	jQuery(".product-img-box #carousel-up").on("click", function () {
		if (!jQuery(".product-img-box .verticl-carousel").is(':animated')) {
			var bottom = jQuery(".product-img-box .verticl-carousel > a:last-child");
			var clone = jQuery(".product-img-box .verticl-carousel > a:last-child").clone();
			clone.prependTo(".product-img-box .verticl-carousel");
			jQuery(".product-img-box .verticl-carousel").animate({
				"top": "-=85"
			}, 0).stop().animate({
				"top": '+=85'
			}, 250, function () {
				bottom.remove();
			});
			ProductMediaManager.init();
		}
	});
	jQuery(".product-img-box #carousel-down").on("click", function () {
		if (!jQuery(".product-img-box .verticl-carousel").is(':animated')) {
			var top = jQuery(".product-img-box .verticl-carousel > a:first-child");
			var clone = jQuery(".product-img-box .verticl-carousel > a:first-child").clone();
			clone.appendTo(".product-img-box .verticl-carousel");
			jQuery(".product-img-box .verticl-carousel").animate({
				"top": '-=85'
			}, 250, function () {
				top.remove();
				jQuery(".product-img-box .verticl-carousel").animate({
					"top": "+=85"
				}, 0);
			});

		}
	});
	jQuery(".product-img-box #carousel-up1").on("click", function () {
		if (!jQuery(".product-img-box .verticl-carousel").is(':animated')) {
			var bottom = jQuery(".product-img-box .verticl-carousel > a:last-child");
			var clone = jQuery(".product-img-box .verticl-carousel > a:last-child").clone();
			clone.prependTo(".product-img-box .verticl-carousel");
			jQuery(".product-img-box .verticl-carousel").animate({
				"top": "-=85"
			}, 0).stop().animate({
				"top": '+=85'
			}, 250, function () {
				bottom.remove();
			});
			ProductMediaManager.init();
		}
	});
	jQuery(".product-img-box #carousel-down1").on("click", function () {
		if (!jQuery(".product-img-box .verticl-carousel").is(':animated')) {
			var top = jQuery(".product-img-box .verticl-carousel > a:first-child");
			var clone = jQuery(".product-img-box .verticl-carousel > a:first-child").clone();
			clone.appendTo(".product-img-box .verticl-carousel");
			jQuery(".product-img-box .verticl-carousel").animate({
				"top": '-=85'
			}, 250, function () {
				top.remove();
				jQuery(".product-img-box .verticl-carousel").animate({
					"top": "+=85"
				}, 0);
			});

		}
	});
});


jQuery(".cross-img").on("click", function () {
	jQuery(".header-banner").hide();
});
/*** End Version 4 detail page js ***/



//countdown js filter
jQuery('.timer-grid').each(function(){
	var countTime=jQuery(this).attr('data-time');
	jQuery(this).countdown(countTime,function(event){jQuery(this).html('<div class="day box-time-date"><span class="number">'+event.strftime('%D')+' </span>days</div> <div class="hour box-time-date"><span class="number">'+event.strftime('%H')+'</span>hrs</div><div class="min box-time-date"><span class="number">'+event.strftime('%M')+'</span>mins</div> <div class="sec box-time-date"><span class="number">'+event.strftime('%S')+' </span>sec</div>')});

});

// jQuery(function($){            
// 	$(document.body).on('click','.of-radio-img-img',function(){
// 		$(this).parents('.tile_img_wrap').find('.of-radio-img-img').removeClass('of-radio-img-selected');
// 		$(this).parent().find('.of-radio-img-radio').attr('checked','checked');
// 		$(this).addClass('of-radio-img-selected');
// 	});            
// });


/* category style js */
jQuery(function() {
	jQuery(".widget_product_categories ul > li.cat-item.cat-parent > ul").hide();
	jQuery(".widget_product_categories ul > li.cat-item.cat-parent.current-cat-parent > ul").show();
	jQuery(".widget_product_categories ul > li.cat-item.cat-parent.current-cat.cat-parent > ul").show();
	jQuery(".widget_product_categories ul > li.cat-item.cat-parent").on("click", function() {
		if (jQuery(this).hasClass('current-cat-parent')) {
			var li = jQuery(this).closest('li');
			li.find(' > ul').slideToggle('fast');
			jQuery(this).toggleClass("close-cat");
		} else {
			var li = jQuery(this).closest('li');
			li.find(' > ul').slideToggle('fast');
			jQuery(this).toggleClass("cat-item.cat-parent open-cat");
		}
	});
	jQuery(".widget_product_categories ul.children li.cat-item,ul.children li.cat-item > a").on("click", function(e) {
		e.stopPropagation();
	});
});


jQuery(document).off('click', 'a.compare');
jQuery(document).on('click', 'a.compare', function(e) {

	e.preventDefault();
	var button = jQuery(this),
	data = {
		action: yith_woocompare.actionadd,
		id: button.data('product_id'),
		context: 'frontend'
	},
	widget_list = jQuery('.yith-woocompare-widget ul.products-list');

        // add ajax loader
        if( typeof jQuery.fn.block != 'undefined' ) {
        	button.block({message: null, overlayCSS: { background: '#fff url(' + yith_woocompare.loader + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6}});
        	widget_list.block({message: null, overlayCSS: { background: '#fff url(' + yith_woocompare.loader + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6}});
        }

        jQuery.ajax({
        	type: 'post',
        	url: yith_woocompare.ajaxurl.toString().replace( '%%endpoint%%', yith_woocompare.actionadd ),
        	data: data,
        	dataType: 'json',
        	success: function(response){

        		if( typeof jQuery.fn.block != 'undefined' ) {
        			button.unblock();
        			widget_list.unblock()
        		}

               // button.addClass('added').attr( 'href', response.table_url ).text( yith_woocompare.added_label );

                // add the product in the widget
                widget_list.html( response.widget_table );

                if ( yith_woocompare.auto_open == 'yes')
                	jQuery('body').trigger( 'yith_woocompare_open_popup', { response: response.table_url, button: button } );
            }
        });
    });



jQuery(document).on('click', 'a.compare.added', function (ev) {
	ev.preventDefault();

	var table_url = this.href;

	if (typeof table_url == 'undefined')
		return;

	jQuery('body').trigger('yith_woocompare_open_popup', {response: table_url, button: jQuery(this)});
});

/* variation image change js default*/

	
	jQuery(".product-shop .variations_form .variations select").on("click", function () {        
		var varimg= jQuery(".product-full a img").attr('src');        	

		jQuery(".zoomWindowContainer div").css("background-image","url("+varimg+")");  

	});


/* variation image change js version2 */


	jQuery(".version2 .variations_form .variations select" ).on("click", function () {
		var varimg= jQuery( ".images a.woocommerce-main-image").attr('href');          

		jQuery(".product-img-box .images .cloud-zoom-big").css('background-image', varimg );

		jQuery('.cloud-zoom, .cloud-zoom-gallery').CloudZoom();


	});

