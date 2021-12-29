<?php
class WPBakery_zinkobooking_Shortcodes {

    protected $zinkoBooking_Template;

    function __construct() {

        add_action('init', [$this, 'create_shortcode']);
        add_shortcode('zinkobookink_list', [$this, 'render_shortcode']);
    }

    public function create_shortcode() {
        if(function_exists('vc_map')){
            vc_map(array(
                'name' => 'List Properties',
                'base' => 'zinkobookink_list',
                'description' => 'First Shortcode',
                'category' => 'zinkobooking',
                'params' => array(
                    array(
                        'type' => 'textfield',
                        'heading' => 'Title',
                        'param_name' => 'title',
                        'value' => '',
                        'description' => 'Insert the title',
                    ),
                    array(
                        'type' => 'textfield',
                        'heading' => 'Count',
                        'param_name' => 'count',
                        'value' => '',
                        'description' => 'Insert the count',
                    ),
                ),
            ));

            vc_map(array(
                'name' => 'Filter',
                'base' => 'zinkobooking_filter',
                'description' => 'Filter Shortcode',
                'category' => 'zinkobooking',
                'params' => array(
                    array(
                        'type' => 'textfield',
                        'heading' => 'Location',
                        'param_name' => 'location',
                        'value' => '',
                        'description' => 'paste 1 to show location or 0 to hide',
                    ),
                    array(
                        'type' => 'textfield',
                        'heading' => 'Type',
                        'param_name' => 'type',
                        'value' => '',
                        'description' => 'paste 1 to show type or 0 to hide',
                    ),
                ),
            ));
        }
        
    }

    public function render_shortcode($atts, $content, $tag) {
        $atts = (shortcode_atts(array(
            'title' => '',
            'count' => '3',
        ),
        $atts));

        $this->zinkoBooking_Template = new zinkobooking_Template_loader();

        $args = array(
            'post_type' => 'property',
            'posts_per_page' => $atts['count'],
        );

        $proprties = new WP_Query($args);

        echo '<div class="wrapper archive-property">';

        if ( $proprties->have_posts() ) {

            while ( $proprties->have_posts() ) {
                $proprties->the_post(); 
                $this->zinkoBooking_Template->get_template_part('partials/content');
            }             
        }

        echo '</div>';
    }
}

new WPBakery_zinkobooking_Shortcodes();