jQuery(document).ready(function ($) {
    "use strict";

      var el = $('.mgkwooas-searchstr'),      
        
        search_button = $('.mgkwooas-searchbt'),
        min_chars = el.data('min-chars'),
        ajaxurl = mgkwooas_params.ajax_url,
       
        product_image = mgkwooas_params.product_image,
        product_price= mgkwooas_params.product_price,
        product_desc = mgkwooas_params.product_desc,
       // loader_icon = mgkwooas_params.loading,        
        product_sku= mgkwooas_params.product_sku,
        product_salebadge = mgkwooas_params.product_salebadge,
        product_featuredbadge = mgkwooas_params.product_featuredbadge,
        product_saletext = mgkwooas_params.product_saletext,
        product_featuredtext = mgkwooas_params.product_featuredtext;




    if (ajaxurl.indexOf('?') == -1) {
        ajaxurl += '?';
    }

    search_button.on('click', function () {
        var form = $(this).closest('form');
        if (form.find('.mgkwooas-searchstr').val() == '') {
            return false;
        }
        return true;
    });

    if (el.length == 0) el = $('.mgkwooas-searchstr');

    el.each(function () {
        var $t = $(this),
            append_to = (typeof  $t.data('append-to') == 'undefined') ? $t.closest('.mgkwooas-ajaxsearchform-container') : $t.closest($t.data('append-to'));

        $t.mgkwooasautocomplete({
            minChars: min_chars,
            appendTo: append_to,
            product_image:product_image,
            product_price:product_price,
            product_desc:product_desc,
            product_sku:product_sku,
            product_salebadge:product_salebadge,
            product_featuredbadge:product_featuredbadge,
            product_saletext:product_saletext,
            product_featuredtext:product_featuredtext,
            triggerSelectOnValidInput: false,
            serviceUrl: ajaxurl + 'action=mgkwoo_ajax_search_products',
            onSearchStart: function () {
              
                // $t.css({
                //     'background-image': 'url(' + loader_icon + ')',
                //     'background-repeat': 'no-repeat',
                //     'background-position': 'center 50px'
                // });
            },
            onSelect: function (suggestion) {
                if (suggestion.id != -1) {
                    window.location.href = suggestion.url;
                }
            },
            onSearchComplete: function () {
                $t.css('background-image', 'none');
            }
        });
    });
});


