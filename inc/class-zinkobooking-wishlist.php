<?php

class Zinkobooking_Wishlist {
    function register() {
        add_action('wp_ajax_zinkobooking_add_wishlist', [$this, 'zinkobooking_add_wishlist']);
        add_action('wp_ajax_zinkobooking_remove_wishlist', [$this, 'zinkobooking_remove_wishlist']);
    }

    public function zinkobooking_add_wishlist() {
        if(isset($_POST['zinkobooking_property_id']) && $_POST['zinkobooking_user_id']) {

            $property_id = intval($_POST['zinkobooking_property_id']);
            $user_id = intval($_POST['zinkobooking_user_id']);

            if($property_id > 0 && $user_id > 0) {
                if(add_user_meta($user_id, 'zinkobooking_wishlist_properties', $property_id)){
                   esc_html_e('Successful added to wishlist', 'zinkobooking');
                } else {
                    esc_html_e('Failed', 'zinkobooking');
                }
            }

        }
        wp_die();
    }

    public function zinkobooking_remove_wishlist() {
        if(isset($_POST['zinkobooking_property_id']) && $_POST['zinkobooking_user_id']) {

            $property_id = intval($_POST['zinkobooking_property_id']);
            $user_id = intval($_POST['zinkobooking_user_id']);

            if($property_id > 0 && $user_id > 0) {
                if(delete_user_meta($user_id, 'zinkobooking_wishlist_properties', $property_id)){
                   echo 3; //Success
                } else {
                    echo 2; //Failed
                }
            }else {
                echo 1; //Bad
            }

        } else {
            echo 1; //Bad
        }
        wp_die();
    }

    public function zinkobooking_in_wishlist($user_id, $property_id) {
        global $wpdb;
        $result = $wpdb->get_results("SELECT * FROM $wpdb->usermeta WHERE meta_key='zinkobooking_wishlist_properties' AND meta_value=". $property_id." AND user_id=" .$user_id);
        //var_export($result[0]->mata_value);
        if(isset($result[0]->meta_value) && $result[0]->meta_value == $property_id) {
            return true;
        } else {
            return false;
        }
    }
}

$zinkobooking_Wishlist = new Zinkobooking_Wishlist();
$zinkobooking_Wishlist->register();