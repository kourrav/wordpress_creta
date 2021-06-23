//Default version revslider
jQuery(document).ready(function() {
  jQuery('#rev_slider_4').show().revolution({
  dottedOverlay: 'none',
  delay: 5000,
  startwidth: 915,
  startheight: 450,
  hideThumbs: 200,
  thumbWidth: 200,
  thumbHeight: 50,
  thumbAmount: 2,
  navigationType: 'thumb',
  navigationArrows: 'solo',
  navigationStyle: 'round',
  touchenabled: 'on',
  onHoverStop: 'on',
  swipe_velocity: 0.7,
  swipe_min_touches: 1,
  swipe_max_touches: 1,
  drag_block_vertical: false,
  spinner: 'spinner0',
  keyboardNavigation: 'off',
  navigationHAlign: 'center',
  navigationVAlign: 'bottom',
  navigationHOffset: 0,
  navigationVOffset: 20,
  soloArrowLeftHalign: 'left',
  soloArrowLeftValign: 'center',
  soloArrowLeftHOffset: 20,
  soloArrowLeftVOffset: 0,
  soloArrowRightHalign: 'right',
  soloArrowRightValign: 'center',
  soloArrowRightHOffset: 20,
  soloArrowRightVOffset: 0,
  shadow: 0,
  fullWidth: 'on',
  fullScreen: 'off',
  stopLoop: 'off',
  stopAfterLoops: -1,
  stopAtSlide: -1,
  shuffle: 'off',
  autoHeight: 'off',
  forceFullWidth: 'on',
  fullScreenAlignForce: 'off',
  minFullScreenHeight: 0,
  hideNavDelayOnMobile: 1500,
  hideThumbsOnMobile: 'off',
  hideBulletsOnMobile: 'off',
  hideArrowsOnMobile: 'off',
  hideThumbsUnderResolution: 0,
  hideSliderAtLimit: 0,
  hideCaptionAtLimit: 0,
  hideAllCaptionAtLilmit: 0,
  startWithSlide: 0,
  fullScreenOffsetContainer: ''
});

});

// list grid archive page js
function grid_list_load()
  {

    jQuery(function ($) {

        "use strict";


        jQuery.display = function (view) {

            view = jQuery.trim(view);

            if (view == 'list') {
                jQuery(".button-grid").removeClass("button-active");
                jQuery(".button-list").addClass("button-active");
            
                jQuery('.pro-grid .category-products .products-grid').attr('class', 'products-list products');


                jQuery('.pro-grid ul.products-list  > li.item').each(function (index, element) {

                    var htmls = '';
                    var element = jQuery(this);


                    element.attr('class', 'item');


                    htmls += '<div class="pimg">';

                    var image = element.find('.pimg').html();

                    if (image != undefined) {
                        htmls += image;
                    }

                    htmls += '</div>';

            

                    htmls += '<div class="product-shop">';
                    if (element.find('.item-title').length > 0)
                        htmls += '<h2 class="product-name item-title"> ' + element.find('.item-title').html() + '</h2>';

                     var ratings = element.find('.ratings').html();

                    htmls += '<div class="rating"><div class="ratings">' + ratings + '</div></div>';

                    var descriptions = element.find('.desc').html();
                    htmls += '<div class="desc std">' + descriptions + '</div>';

                      var price = element.find('.price-box').html();

                    if (price != null) {
                        htmls += '<div class="price-box">' + price + '</div>';
                    }

                    htmls += '<div class="actions"><div class="action">' + element.find('.action').html() + '</div>';
                    htmls += '<ul class="add-to-links">';
                     var adtolinks = element.find('.add-to-links').html();
                    if (adtolinks != undefined) {

                        htmls += adtolinks;
                    }
                     htmls += '</ul>';
                    htmls += '</div>';
                    htmls += '</div>';


                    element.html(htmls);
                });


                jQuery.cookie('display', 'list');

            } else {
                 var wooloop=1;
                 var pgrid='';
                 jQuery(".button-list").removeClass("button-active");
                 jQuery(".button-grid").addClass("button-active");
             
                 jQuery('.pro-grid .category-products .products-list').attr('class', 'products-grid products');
                 
                 jQuery('.pro-grid ul.products-grid > li.item').each(function (index, element) {
                    var html = '';

                    element = jQuery(this);
                  
                    var item_count =js_creta_wishvar.PRODUCT_ITEM_COUNT;
                      if(wooloop%item_count==1) 
                    {
                     pgrid='wide-first';   
                     }
                     else if(wooloop%item_count==0) 
                     {
                     pgrid='last'; 
                      }
                      else
                      {
                       pgrid=''; 

                      }
                       var item_class =js_creta_wishvar.PRODUCT_ITEM_CLASS;
                     element.attr('class', item_class + ' ' +pgrid);
                    html += '<div class="item-inner"><div class="item-img"><div class="item-img-info"><div class="pimg">';
              

                    var image = element.find('.pimg').html();

                    if (image != undefined) {

                        html += image;
                    }
                    html +='</div><div class="box-hover"><ul class="add-to-links">';
                    var adtolinks = element.find('.add-to-links').html();

                    if (adtolinks != undefined) {

                        html += adtolinks;
                    }
                    html +='</ul></div></div></div>';
                    
                    html +='<div class="item-info"><div class="info-inner">';
                       if (element.find('.item-title').length > 0)
                       {
                        html += '<div class="item-title"> ' + element.find('.item-title').html() + '</div>';
                    }


                html +='<div class="item-content">';
                        var ratings = element.find('.ratings').html();

                    html += '<div class="rating"><div class="ratings">' + ratings + '</div></div>';

                        var price = element.find('.price-box').html();

                     if (price != null) {
                        html += '<div classs="item-price"><div class="price-box"> ' + price + '</div></div>';
                    }

                    var descriptions = element.find('.desc').html();
                    html += '<div class="desc std">' + descriptions + '</div>';
                    html += '<div class="action">';
                     var actions = element.find('.action').html();
                   
                     html +=actions;
                   html += '</div>';
                    html += '</div></div></div></div>';

                    element.html(html);
                      wooloop++;
                 });

                 jQuery.cookie('display', 'grid');
            }
        }

        jQuery('a.list-trigger').click(function () {
            jQuery.display('list');

        });
        jQuery('a.grid-trigger').click(function () {
            jQuery.display('grid');
        });

       var view = js_creta_wishvar.PRODUCT_CATEGORY_VIEW;
        view = jQuery.cookie('display') !== undefined ? jQuery.cookie('display') : view;

        if (view) {
            jQuery.display(view);

        } else {
            jQuery.display('grid');
        }
        return false;


    });

}

 grid_list_load();


