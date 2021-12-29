<?php 

class ZinkobookingShortcodes {

    public $zinkoBooking;

    public $agents;

    public function register() {

        add_action('init', [$this, 'register_shortcode']);
    }

    public function register_shortcode() {

        add_shortcode('zinkobooking_filter', [$this, 'filter_shortcode']);
    }

    public function filter_shortcode($atts=array(), $content = null) {

        extract(shortcode_atts(array(
            'location' => 0,
            'type' => 0,
            'offer' => 0,
            'price' => 0,
            'agent' => 0,
        ),$atts ));

        $this->zinkoBooking = new ZinkoBooking();

        $this->agents = get_posts(array('post_type' => ['agent'], 'numberposts' => -1));
            $agents_list = '';
            foreach($this->agents as $person) {
                $agents_list .= '<option value="'. $person->ID . '">'. $person->post_title .'</option>';
            }

         $output = '';

         $output .= '<div class="wrapper filter_form">';

         $output .= '<form method="post" action="'. get_post_type_archive_link('property').'">';

         if($location == 1) {

            $output .= '<select name="zinkobooking_location">
                    <option value="">Select Location</option>
                    '. $this->zinkoBooking->get_terms_hierarchical('location', '').'
                </select>';
         }

         if($type == 1) {

            $output .= '<select name="zinkobooking_property-type">
                <option value="">Select Type</option>'
                . $this->zinkoBooking->get_terms_hierarchical('property-type', ''). '
                </select>';
         }

         if($price == 1) {

            $output .= '<input type="text" name="zinkobooking_price" placeholder="Maximum Price" value="">';
         }

         if($offer == 1) {

            $output .= '<select name="zinkobooking_type">
                <option value="">Select Offer</option>
                <option value="sale">For Sale</option>
                <option value="rent">For Rent</option>
                <option value="sold">Sold</option>
            </select>';

         }

         if($agent == 1) {

            $output .= ' <select name="zinkobooking_agent">
                <option value="">Select Agent</option>'
                . $agents_list .
                '</select>';

         }

         $output .= '<input type="submit" name="submit" value="Filter">';

         $output .= '</form></div>';


         return $output;
    }
}

$zinkobookingShortcodes = new ZinkobookingShortcodes();
$zinkobookingShortcodes->register();  