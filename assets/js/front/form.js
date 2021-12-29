jQuery(document).ready(function($) {
    
    $('#zinkobooking_submit').on('click', function(e){

        e.preventDefault();

        $.ajax({
            url: zinkobooking_form_var.ajaxurl,
            type: 'post',
            data: {
                action: 'booking_form',
                nonce: zinkobooking_form_var.nonce,
                name: $('#zinkobooking_name').val(),
                email: $('#zinkobooking_email').val(),
                phone: $('#zinkobooking_phone').val(),
                price: $('#zinkobooking_price').val(),
                location: $('#zinkobooking_location').val(),
                agent: $('#zinkobooking_agent').val(),
            },
            success: function(data){
                $('#zinkobooking_result').html(data);
            },
            error: function(errorThrown){
                console.log(errorThrown);
            },
        })
    })
})