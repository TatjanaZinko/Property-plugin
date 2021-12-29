jQuery(document).ready(function($) {
    $('.zinkobooking_add_to_wishlist').on('click', function(e){
        e.preventDefault();  
        
        var id  = $(this).data('property_id');

        var zinkobooking_add_to_wishlist = {
            success: function() {    
                $('#zinkobooking_add_to_wishlist_form_' + id).next('.zinkobooking_add_to_wishlist').hide(0, function() {
                    $('#zinkobooking_add_to_wishlist_form_' + id).next('.zinkobooking_add_to_wishlist').next('.successfull_added').delay(700).show();
                });
            }
        }

        $('#zinkobooking_add_to_wishlist_form_' + id).ajaxSubmit(zinkobooking_add_to_wishlist);
    });

    $('.zinkobooking_remove_property').on('click', function(e){
        e.preventDefault();  

        var id  = $(this).data('property_id');

        $.ajax({
            url: $(this).attr('href'),
            type: "POST",
            data: {
                zinkobooking_property_id: $(this).data('property_id'),
                zinkobooking_user_id: $(this).data('user_id'),
                action: "zinkobooking_remove_wishlist",
            },
            dataType: "html",
            success: function(result){
                $('.post-' + id).hide();
            }
        })
    });
})