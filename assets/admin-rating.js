jQuery(function($){
    /*
     * Select/Upload image(s) event
     */
    $('body').on('click', '.misha_upload_image_button', function(e){
        e.preventDefault();
 
            var button = $(this),
                custom_uploader = wp.media({
            title: 'Insert image',
            library : {
                // uncomment the next line if you want to attach image to the current post
                // uploadedTo : wp.media.view.settings.post.id, 
                type : 'image'
            },
            button: {
                text: 'Use this image' // button label text
            },
            multiple: false // for multiple image selection set to true
        }).on('select', function() { // it also has "open" and "close" events 
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            $(button).removeClass('button').html('<img class="true_pre_image" src="' + attachment.url + '" style="max-width:150px;display:block;" />').next().val(attachment.id).next().show();
            
            console.log(attachment.url);


             $.ajax({
                    url: lp_object.ajax_url,
                    type: 'POST',
                    dataType: 'json',
                    data: { action: 'lp_submit_rating', _wpnonce: lp_object.nonce, rating: val, post_id: rate_id },
                    success: function( resp ) {
                        if( resp.success ) {
                    
                            $text.html( lp_object.text.thank_you );
                            
                            $("#entryRating").removeClass( 'active' );
                            
                        } else {
                            
                            $("#ratingErrors").html( resp.message );
                            
                            $this.html( lp_object.text.submit );
                        
                        }   
                    }
                });

            /* if you sen multiple to true, here is some code for getting the image IDs*/
            // var attachments = frame.state().get('selection'),
            //     attachment_ids = new Array(),
            //     i = 0;
            // attachments.each(function(attachment) {
            //     attachment_ids[i] = attachment['id'];
            //     console.log( attachment );
            //     i++;
            // });
            
        })
        .open();
    });
 
    /*
     * Remove image event
     */
    $('body').on('click', '.misha_remove_image_button', function(){
        $(this).hide().prev().val('').prev().addClass('button').html('Upload image');
        return false;
    });

     $(".form-table #clearRatings").on( 'click', function(){
                
                var $this = $( this );
                var post_id = $this.attr('data-id');
                    console.log(post_id );
                $.ajax({
                    url: lp_object.ajax_url,
                    type: 'POST',
                    dataType: 'json',
                    data: { action: 'lp_clear_rating', _wpnonce: lp_object.nonce, post_id: post_id },
                    success: function( resp ) {

                        console.log(resp);



                        if( resp.success ) {
                                alert('Data Cleared');

                                $this.closest("tr").remove();
                        }
                        // if( resp.success ) {
                    
                        //     $text.html( lp_object.text.thank_you );
                            
                        //     $("#entryRating").removeClass( 'active' );
                            
                        // } else {
                            
                        //     $("#ratingErrors").html( resp.message );
                            
                        //     $this.html( lp_object.text.submit );
                        
                        // }   
                    }
                });
            
        });
 
});