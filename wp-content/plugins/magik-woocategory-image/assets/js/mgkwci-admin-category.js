jQuery(document).ready(function() {
  
  "use strict";
  
  // add category code
        // Only show the "remove image" button when needed
        if ( ! jQuery( '#product_cat_magik_thumbnail_id' ).val() ) {
          jQuery( '.magik_img_remove_button' ).hide();
        }

        // Uploading files
        var file_frame1;

        jQuery( document ).on( 'click', '.magik_img_add_button', function( event ) {

          event.preventDefault();

          // If the media frame already exists, reopen it.
          if ( file_frame1 ) {
            file_frame1.open();
            return;
          }

          // Create the media frame.
          file_frame1 = wp.media.frames.downloadable_file = wp.media({
            title: mgkwci_params.MGKWCI_CHOOSE_IMAGE_TEXT,
            button: {
              text: mgkwci_params.MGKWCI_USE_IMAGE_TEXT
            },
            multiple: false
          });

          // When an image is selected, run a callback.
          file_frame1.on( 'select', function() {
            var attachment           = file_frame1.state().get( 'selection' ).first().toJSON();
            var attachment_thumbnail = attachment.sizes.thumbnail || attachment.sizes.full;

            jQuery( '#product_cat_magik_thumbnail_id' ).val( attachment.id );
            jQuery( '#product_cat_thumbnail_magik' ).find( 'img' ).attr( 'src', attachment_thumbnail.url );
            jQuery( '.magik_img_remove_button' ).show();
          });

          // Finally, open the modal.
          file_frame1.open();
        });

        jQuery( document ).on( 'click', '.magik_img_remove_button', function() {
          jQuery( '#product_cat_thumbnail_magik' ).find( 'img' ).attr( 'src', mgkwci_params.MGKWCI_PLACE_HOLDER_IMAGE);
          jQuery( '#product_cat_magik_thumbnail_id' ).val( '' );
          jQuery( '.magik_img_remove_button' ).hide();
          return false;
        });

        jQuery( document ).ajaxComplete( function( event, request, options ) {
          if ( request && 4 === request.readyState && 200 === request.status
            && options.data && 0 <= options.data.indexOf( 'action=add-tag' ) ) {

            var res = wpAjax.parseAjaxResponse( request.responseXML, 'ajax-response' );
          if ( ! res || res.errors ) {
            return;
          }
            // Clear Thumbnail fields on submit
            jQuery( '#product_cat_thumbnail_magik' ).find( 'img' ).attr( 'src', mgkwci_params.MGKWCI_PLACE_HOLDER_IMAGE );
            jQuery( '#product_cat_magik_thumbnail_id' ).val( '' );
            jQuery( '.magik_img_remove_button' ).hide();
            // Clear Display type field on submit
            jQuery( '#display_type' ).val( '' );
            return;
          }
        } );




// edit category code


 // Only show the "remove image" button when needed
          if ( '0' === jQuery( '#product_cat_magik_thumbnail_id' ).val() ) {
            jQuery( '.magik_img_remove_button' ).hide();
          }

          // Uploading files
          var file_frame1;

          jQuery( document ).on( 'click', '.magik_img_add_button', function( event ) {

            event.preventDefault();

            // If the media frame already exists, reopen it.
            if ( file_frame1 ) {
              file_frame1.open();
              return;
            }

            // Create the media frame.
            file_frame1 = wp.media.frames.downloadable_file = wp.media({
             title: mgkwci_params.MGKWCI_CHOOSE_IMAGE_TEXT,
            button: {
              text: mgkwci_params.MGKWCI_USE_IMAGE_TEXT
              },
              multiple: false
            });

            // When an image is selected, run a callback.
            file_frame1.on( 'select', function() {
              var attachment           = file_frame1.state().get( 'selection' ).first().toJSON();
              var attachment_thumbnail = attachment.sizes.thumbnail || attachment.sizes.full;

              jQuery( '#product_cat_magik_thumbnail_id' ).val( attachment.id );
              jQuery( '#product_cat_thumbnail_magik' ).find( 'img' ).attr( 'src', attachment_thumbnail.url );
              jQuery( '.magik_img_remove_button' ).show();
            });

            // Finally, open the modal.
            file_frame1.open();
          });

          jQuery( document ).on( 'click', '.magik_img_remove_button', function() {
            jQuery( '#product_cat_thumbnail_magik' ).find( 'img' ).attr( 'src', mgkwci_params.MGKWCI_PLACE_HOLDER_IMAGE );
            jQuery( '#product_cat_magik_thumbnail_id' ).val( '' );
            jQuery( '.magik_img_remove_button' ).hide();
            return false;
          });


  });