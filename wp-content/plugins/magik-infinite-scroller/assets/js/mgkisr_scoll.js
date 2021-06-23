var $ = jQuery.noConflict();

var mgk_infinite_scroll=mgkisr_scoll_vars.infinite_scroll;
is_shop        = mgkisr_scoll_vars.is_shop

var opts = {

  nextSelector   : mgkisr_scoll_vars.next_selector,
  navSelector    : mgkisr_scoll_vars.nav_selector,
  itemSelector   : mgkisr_scoll_vars.item_selector,
  itemclass   :  mgkisr_scoll_vars.item_class,
  contentSelector: mgkisr_scoll_vars.content_selector,
  loader         : mgkisr_scoll_vars.loader_image,
  is_shop        : mgkisr_scoll_vars.is_shop

};


if(mgk_infinite_scroll==1 && is_shop)
{
 var loading  = false;
 var finished = false;
           var desturl  = $( opts.nextSelector).attr( 'href' ); // init next url



           if( $( opts.nextSelector ).length && $( opts.navSelector ).length && $( opts.itemSelector ).length && $( opts.contentSelector ).length ) {
            $( opts.navSelector ).hide();
          }
          else {

            finished = true;
          }

          var first_elem  = $( opts.contentSelector ).find( opts.itemSelector ).first(),

          columns=  $( opts.contentSelector ).find( opts.itemSelector+" last").eq();


          var mgkisr_main_ajax = function () {


            var last_elem   = $( opts.contentSelector ).find( opts.itemSelector ).last();

            if( opts.loader )
              $( opts.contentSelector ).after( '<div class="magik-infi-loader"><img src="' + opts.loader + '"></div>' );
            loading = true;

            desturl = decodeURIComponent(desturl);
            desturl = desturl.replace(/^(?:\/\/|[^\/]+)*\//, "/");
          

            $.ajax({

              url         : desturl,
              dataType    : 'html',
              cache       : false,
              success     : function (data) {


                var obj  = $( data),
                elem = obj.find( ".products "+opts.itemSelector ),
                next = obj.find( opts.nextSelector ),
                current_url = desturl;


                if( next.length ) {
                  desturl = next.attr( 'href' );
                }
                else {

                  finished = true;

                }                     


                var wooloop    = 0;
               
                elem.each(function () {

                  var t = $(this);



                  t.removeClass(t.attr('class'));


                  if(wooloop%columns==1) 
                  {
                   pgrid='wide-first';   
                 }
                 else if(wooloop%columns==0) 
                 {
                   pgrid='last'; 
                 }
                 else
                 {
                   pgrid=''; 

                 }

                 t.addClass(opts.itemclass+" "+pgrid);
                 wooloop++;

               });


                last_elem.after( elem );




                if (typeof grid_list_load !== 'undefined' && $.isFunction(grid_list_load)) {
                  grid_list_load();
                }
                $('.magik-infi-loader').remove();


                setTimeout( function(){
                  loading = false;                    

                }, 100 );

              }
            });
          };

          
          

        // set event
        $( window ).on( 'scroll touchstart', function (){
          $(this).trigger('mgkisr_scroll_start');
        });

        $( window ).on( 'mgkisr_scroll_start', function(){
          var t       = $(this),
          elem  = $( opts.itemSelector ).last();

          if( typeof elem == 'undefined' ) {
            return;
          }

          if ( ! loading && ! finished && ( t.scrollTop() + t.height() ) >= ( elem.offset().top + elem.height() ) ) {
            mgkisr_main_ajax();
          }
        })
        

      }