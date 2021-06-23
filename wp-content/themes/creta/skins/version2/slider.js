//version2 revslider 
  var $=jQuery.noConflict();
          var revapi;
  jQuery(document).ready(function() {   
    revapi = jQuery("#rev_slider").revolution({
      sliderType:"standard",
      sliderLayout:"fullscreen",
      fullScreenOffset:"0px",
      delay:9000,
      navigation: {
        arrows:{enable:true}        
      },      
      gridwidth:1200,
      gridheight:720    
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

                    htmls += '<div class="actions"><div class="action actions-no">' + element.find('.actions-no').html() + '</div>';
                  
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

                    html += '<div class="item-inner"><div class="images-container"><div class="product-hover"><div class="pimg">';
              

                    var image = element.find('.pimg').html();

                    if (image != undefined) {

                        html += image;
                    }
                    html +='</div></div><div class="actions-no hover-box">';
                     var actions = element.find('.actions-no').html();
                   
                     html +=actions;

                     html +='</div></div>';

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
                 
                    html += '</div></div></div></div></div>';

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

 jQuery(document).ready(function($){
     
       
       new UISearch(document.getElementById('form-search'));
   });