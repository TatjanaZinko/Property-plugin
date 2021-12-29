<?php 
class zinkobooking_Form {
    public function __construct(){
        add_action('wp_enqueue_scripts', [$this, 'enqueue']);
        add_action('init', [$this, 'zinkobooking_shortcode']);

        add_action('wp_ajax_booking_form', [$this, 'booking_form']);
        add_action('wp_ajax_nopriv_booking_form', [$this, 'booking_form']);
    }

    public function enqueue(){
        wp_enqueue_script('zinkobooking_form', plugins_url('zinkobooking/assets/js/front/form.js'), array('jquery'), '1.0', true);

        wp_localize_script('zinkobooking_form', 'zinkobooking_form_var', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('_wpnonce'),
            'title' => esc_html__('Booking form', 'zinkobooking'),
        ));
    }

    public function zinkobooking_shortcode() {
        add_shortcode('zinkobooking', [$this, 'booking_form_html']);
    }

    public function booking_form_html($atts, $content) {

        extract(shortcode_atts(array(
            'location' => '',
            'type' => '',
            'offer' => '',
            'price' => '',
            'agent' => '',
        ),$atts ));

        echo '
            <div id="zinkobooking_result"></div>
            <form method="post">
                <p>
                    <input type="text" name="name" id="zinkobooking_name">
                </p>
                <p>
                    <input type="text" name="email" id="zinkobooking_email">
                </p>
                <p>
                    <input type="text" name="phone" id="zinkobooking_phone">
                </p>';

        if($price != ''){
             echo ' <p>
                <input type="hidden" name="price" id="zinkobooking_price" value="'. $price . '">
            </p>';
         }
        if($location != ''){
            echo ' <p>
               <input type="hidden" name="location" id="zinkobooking_location" value="'. $location . '">
           </p>';
        }

        if($agent != ''){
            echo ' <p>
               <input type="hidden" name="agent" id="zinkobooking_agent" value="'. $agent . '">
           </p>';
        }


        echo  '<p>
                    <input type="submit" name="submit" id="zinkobooking_submit">
                </p>
            </form>';
    }

    public function booking_form(){

        check_ajax_referer('_wpnonce', 'nonce');

        if(!empty($_POST)){
            if(isset($_POST['name'])){
                $name = sanitize_text_field($_POST['name']);
            }
            if(isset($_POST['email'])){
                $email = sanitize_text_field($_POST['email']);
            }
            if(isset($_POST['phone'])){
                $phone = sanitize_text_field($_POST['phone']);
            }
            if(isset($_POST['price'])){
                $price = sanitize_text_field($_POST['price']);
            }
            if(isset($_POST['location'])){
                $location = sanitize_text_field($_POST['location']);
            }
            if(isset($_POST['agent'])){
                $agent = sanitize_text_field($_POST['agent']);
            }

            //email Admin
            $data_mesaage = '';

            $data_mesaage .= 'Name: '. $name .'<br>';
            $data_mesaage .= 'Email: '. $email .'<br>';
            $data_mesaage .= 'Phone: '. $phone .'<br>';
            $data_mesaage .= 'Price: '. $price .'<br>';
            $data_mesaage .= 'Location: '. $location .'<br>';
            $data_mesaage .= 'Agent: '. $agent .'<br>';

            echo $data_mesaage;
            $result_admin = wp_mail(get_option('admin_email'), 'New Reservation', $data_mesaage);
            if($result_admin) {
                echo 'All Right';
            }

            //email client
            $message = esc_html('Thank you for you reservation', 'zinkobooking');
            wp_mail($email, esc_html('Booking', 'zinkobooking'), $message);

        } else {
            echo 'smth wrong';
        }
       
        wp_die();

    }
}

$booking_form = new zinkobooking_Form();