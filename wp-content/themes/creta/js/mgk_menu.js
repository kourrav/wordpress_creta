// creta wp Theme
window.theme = {};
"use strict";
// Configuration
(function(theme, jQuery) {
  theme = theme || {};
  var initialized = false;
  jQuery.extend(theme, {
    ajax_url: js_creta_vars.ajax_url
    , container_width: js_creta_vars.container_width
    , grid_layout_width: js_creta_vars.grid_layout_width
    , hoverIntentConfig: {
      sensitivity: 2
      , interval: 0
      , timeout: 0
    }
    , owlConfig: {
      autoPlay: 5000
      , stopOnHover: true
      , singleItem: true
      , autoHeight: true
      , lazyLoad: true
    }
    , infiniteConfig: {
      navSelector: "div.pagination"
      , nextSelector: "div.pagination a.next"
      , loading: {
        finishedMsg: ""
        , msgText: "<em class='infinite-loading'></em>"
        , img: "data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
      }
    }
    , getScrollbarWidth: function() {
      if (initialized) return this.scrollbar_width;
      initialized = true;
      this.scrollbar_width = window.innerWidth - jQuery(window).width();
      return this.scrollbar_width;
    }
    , isTablet: function() {
      var win_width = jQuery(window).width();
      if (win_width < 992 - theme.scrollbar_width) return true;
      return false;
    }
    , isMobile: function() {
      var win_width = jQuery(window).width();
      if (win_width <= 480 - theme.scrollbar_width) return true;
      return false;
    }
  });
}).apply(this, [window.theme, jQuery]);
// Mega Menu
(function(theme, jQuery) {
  theme = theme || {};
  jQuery.extend(theme, {
    MegaMenu: {
      defaults: {
        menu: jQuery('.mega-menu')
      }
      , initialize: function(jQuerymenu) {
        this.jQuerymenu = (jQuerymenu || this.defaults.menu);
        this.build().events();
        return this;
      }
      , popupWidth: function() {
        var winWidth = jQuery(window).width() + theme.getScrollbarWidth();
        if (winWidth >= theme.container_width) return theme.container_width - theme.grid_layout_width;
        if (winWidth >= 992) return 940;
        if (winWidth >= 768) return 720;
        return jQuery(window).width() - theme.grid_layout_width;
      }
      , build: function() {
        var self = this;
        self.jQuerymenu.each(function() {
          var jQuerymenu = jQuery(this);
          var jQuerymenu_container = jQuerymenu.closest('.container');
          var container_width = self.popupWidth();
          var offset = 0;
          if (jQuerymenu_container.length) {
            offset = jQuerymenu.offset().left - jQuerymenu_container.offset().left - parseInt(jQuerymenu_container.css('padding-left'));
            offset = (offset == 1) ? 0 : offset;
          }
          var jQuerymenu_items = jQuerymenu.find('> li');
          jQuerymenu_items.each(function() {
            var jQuerymenu_item = jQuery(this);
            var jQuerypopup = jQuerymenu_item.find('> .mgk-popup');
            if (jQuerypopup.length > 0) {
              jQuerypopup.css('display', 'block');
              if (jQuerymenu_item.hasClass('wide')) {
                jQuerypopup.css('left', 0);
                var padding = parseInt(jQuerypopup.css('padding-left')) + parseInt(jQuerypopup.css('padding-right')) + parseInt(jQuerypopup.find('> .inner').css('padding-left')) + parseInt(jQuerypopup.find('> .inner').css('padding-right'));
                var row_number = 4;
                if (jQuerymenu_item.hasClass('col-2')) row_number = 2;
                if (jQuerymenu_item.hasClass('col-3')) row_number = 3;
                if (jQuerymenu_item.hasClass('col-4')) row_number = 4;
                if (jQuerymenu_item.hasClass('col-5')) row_number = 5;
                if (jQuerymenu_item.hasClass('col-6')) row_number = 6;
                if (jQuery(window).width() < 992 - theme.scrollbarWidth) row_number = 1;
                var col_length = 0;
                jQuerypopup.find('> .inner > ul > li').each(function() {
                  var cols = parseInt(jQuery(this).attr('data-cols'));
                  if (cols < 1) cols = 1;
                  if (cols > row_number) cols = row_number;
                  col_length += cols;
                });
                if (col_length > row_number) col_length = row_number;
                var popup_max_width = jQuerypopup.find('.inner').css('max-width');
                var col_width = container_width / row_number;
                if ('none' !== popup_max_width && popup_max_width < container_width) {
                  col_width = parseInt(popup_max_width) / row_number;
                }
                jQuerypopup.find('> .inner > ul > li').each(function() {
                  var cols = parseFloat(jQuery(this).attr('data-cols'));
                  if (cols < 1) cols = 1;
                  if (cols > row_number) cols = row_number;
                  if (jQuerymenu_item.hasClass('pos-center') || jQuerymenu_item.hasClass('pos-left') || jQuerymenu_item.hasClass('pos-right')) jQuery(this).css('width', (100 / col_length * cols) + '%');
                  else jQuery(this).css('width', (100 / row_number * cols) + '%');
                });
                if (jQuerymenu_item.hasClass('pos-center')) { // position center
                  jQuerypopup.find('> .inner > ul').width(col_width * col_length - padding);
                  var left_position = jQuerypopup.offset().left - (jQuery(window).width() - col_width * col_length) / 2;
                  jQuerypopup.css({
                    'left': -left_position
                  });
                } else if (jQuerymenu_item.hasClass('pos-left')) { // position left
                  jQuerypopup.find('> .inner > ul').width(col_width * col_length - padding);
                  jQuerypopup.css({
                    'left': 0
                  });
                } else if (jQuerymenu_item.hasClass('pos-right')) { // position right
                  jQuerypopup.find('> .inner > ul').width(col_width * col_length - padding);
                  jQuerypopup.css({
                    'left': 'auto'
                    , 'right': 0
                  });
                } else { // position justify
                  jQuerypopup.find('> .inner > ul').width(container_width - padding);
                  jQuerypopup.css({
                    'left': 0
                    , 'right': 'auto'
                  });
                  var left_position = jQuerypopup.offset().left - jQuerymenu.offset().left + offset;
                  jQuerypopup.css({
                    'left': -left_position
                    , 'right': 'auto'
                  });
                }
              }
              if (!(jQuerymenu.hasClass('effect-down'))) jQuerypopup.css('display', 'none');
              jQuerymenu_item.hoverIntent(jQuery.extend({}, theme.hoverIntentConfig, {
                over: function() {
                  if (!(jQuerymenu.hasClass('effect-down'))) jQuerymenu_items.find('.mgk-popup').hide();
                  jQuerypopup.show();
                }
                , out: function() {
                  if (!(jQuerymenu.hasClass('effect-down'))) jQuerypopup.hide();
                }
              }));
            }
          });
        });
        return self;
      }
      , events: function() {
        var self = this;
        jQuery(window).on('resize', function() {
          self.build();
        });
        setTimeout(function() {
          self.build();
        }, 400);
        return self;
      }
    }
  });
}).apply(this, [window.theme, jQuery]);
// Accordion Menu
(function(theme, jQuery) {
  theme = theme || {};
  jQuery.extend(theme, {
    AccordionMenu: {
      defaults: {
        menu: jQuery('.accordion-menu')
      }
      , initialize: function(jQuerymenu) {
        this.jQuerymenu = (jQuerymenu || this.defaults.menu);
        this.events().build();
        return this;
      }
      , build: function() {
        var self = this;
        self.jQuerymenu.find('li.menu-item.active').each(function() {
          if (jQuery(this).find('> .arrow').length) jQuery(this).find('> .arrow').click();
        });
        return self;
      }
      , events: function() {
        var self = this;
        self.jQuerymenu.find('.arrow').click(function() {
          var jQueryparent = jQuery(this).parent();
          jQuery(this).next().stop().slideToggle();
          if (jQueryparent.hasClass('open')) {
            jQueryparent.removeClass('open');
          } else {
            jQueryparent.addClass('open');
          }
        });
        return self;
      }
    }
  });
}).apply(this, [window.theme, jQuery]);
// Mobile Panel
(function(theme, jQuery) {
  theme = theme || {};
  jQuery.extend(theme, {
    Panel: {
      initialize: function() {
        this.events();
        return this;
      }
      , events: function() {
        var self = this;
        jQuery('.mobile-toggle').click(function(e) {

          var jQueryhtml = jQuery('html');
         
          if (jQueryhtml.hasClass('menu-opened')) {
            jQueryhtml.removeClass('menu-opened');
            jQuery('.menu-overlay').removeClass('active');
          } else {
            jQueryhtml.addClass('menu-opened');
            jQuery('.menu-overlay').addClass('active');
          }
        });
        jQuery('.menu-overlay').click(function() {
          var jQueryhtml = jQuery('html');
          jQueryhtml.removeClass('menu-opened');
          jQuery(this).removeClass('active');
        });
        jQuery(window).on('resize', function() {
          var winWidth = jQuery(window).width();
          if (winWidth > 991 - theme.getScrollbarWidth()) {
            jQuery('.menu-overlay').click();
          }
        });
        return self;
      }
    }
  });
}).apply(this, [window.theme, jQuery]);

// Init Theme
(function(theme, jQuery) {
  "use strict";

  function creta_init() {

    // Mega Menu
    if (typeof theme.MegaMenu !== 'undefined') {
      theme.MegaMenu.initialize();
    }
    // Accordion Menu
    if (typeof theme.AccordionMenu !== 'undefined') {
      theme.AccordionMenu.initialize();
    }
    // Panel open
    if (typeof theme.Panel !== 'undefined') {
      theme.Panel.initialize();
    }
   
    // bootstrap dropdown hover
    //   jQuery('[data-toggle="dropdown"]').dropdownHover();
    // bootstrap popover
    // jQuery("[data-toggle='popover']").popover();
  }
  jQuery(document).ready(function() {
    creta_init();
  });
}.apply(this, [window.theme, jQuery]));
/*
 * hoverIntent r7 // 2013.03.11 // jQuery 1.9.1+
 * http://cherne.net/brian/resources/jquery.hoverIntent.html
 *
 * You may use hoverIntent under the terms of the MIT license. Basically that
 * means you are free to use hoverIntent as long as this header is left intact.
 * Copyright 2007, 2013 Brian Cherne
 */
(function(jQuery) {
  jQuery.fn.hoverIntent = function(handlerIn, handlerOut, selector) {
    // default configuration values
    var cfg = {
      interval: 100
      , sensitivity: 7
      , timeout: 0
    };
    if (typeof handlerIn === "object") {
      cfg = jQuery.extend(cfg, handlerIn);
    } else if (jQuery.isFunction(handlerOut)) {
      cfg = jQuery.extend(cfg, {
        over: handlerIn
        , out: handlerOut
        , selector: selector
      });
    } else {
      cfg = jQuery.extend(cfg, {
        over: handlerIn
        , out: handlerIn
        , selector: handlerOut
      });
    }
    // instantiate variables
    // cX, cY = current X and Y position of mouse, updated by mousemove event
    // pX, pY = previous X and Y position of mouse, set by mouseover and polling interval
    var cX, cY, pX, pY;
    // A private function for getting mouse position
    var track = function(ev) {
      cX = ev.pageX;
      cY = ev.pageY;
    };
    // A private function for comparing current and previous mouse position
    var compare = function(ev, ob) {
      ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t);
      // compare mouse positions to see if they've crossed the threshold
      if ((Math.abs(pX - cX) + Math.abs(pY - cY)) < cfg.sensitivity) {
        jQuery(ob).off("mousemove.hoverIntent", track);
        // set hoverIntent state to true (so mouseOut can be called)
        ob.hoverIntent_s = 1;
        return cfg.over.apply(ob, [ev]);
      } else {
        // set previous coordinates for next time
        pX = cX;
        pY = cY;
        // use self-calling timeout, guarantees intervals are spaced out properly (avoids JavaScript timer bugs)
        ob.hoverIntent_t = setTimeout(function() {
          compare(ev, ob);
        }, cfg.interval);
      }
    };
    // A private function for delaying the mouseOut function
    var delay = function(ev, ob) {
      ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t);
      ob.hoverIntent_s = 0;
      return cfg.out.apply(ob, [ev]);
    };
    // A private function for handling mouse 'hovering'
    var handleHover = function(e) {
      // copy objects to be passed into t (required for event object to be passed in IE)
      var ev = jQuery.extend({}, e);
      var ob = this;
      // cancel hoverIntent timer if it exists
      if (ob.hoverIntent_t) {
        ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t);
      }
      // if e.type == "mouseenter"
      if (e.type == "mouseenter") {
        // set "previous" X and Y position based on initial entry point
        pX = ev.pageX;
        pY = ev.pageY;
        // update "current" X and Y position based on mousemove
        jQuery(ob).on("mousemove.hoverIntent", track);
        // start polling interval (self-calling timeout) to compare mouse coordinates over time
        if (ob.hoverIntent_s != 1) {
          ob.hoverIntent_t = setTimeout(function() {
            compare(ev, ob);
          }, cfg.interval);
        }
        // else e.type == "mouseleave"
      } else {
        // unbind expensive mousemove event
        jQuery(ob).off("mousemove.hoverIntent", track);
        // if hoverIntent state is true, then call the mouseOut function after the specified delay
        if (ob.hoverIntent_s == 1) {
          ob.hoverIntent_t = setTimeout(function() {
            delay(ev, ob);
          }, cfg.timeout);
        }
      }
    };
    // listen for mouseenter and mouseleave
    return this.on({
      'mouseenter.hoverIntent': handleHover
      , 'mouseleave.hoverIntent': handleHover
    }, cfg.selector);
  };
})(jQuery);