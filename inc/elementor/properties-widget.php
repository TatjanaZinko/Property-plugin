<?php

class Elementor_Properties_Widget extends \Elementor\Widget_Base {

    protected $zinkoBooking_Template;

    protected $zinko_locations = array(''=>'Select Smth');
	
	public function get_name() {
		return 'zinkobooking';
	}

	public function get_title() {
		return esc_html__( 'Properties List', 'zinkobooking' );
	}

	public function get_icon() {
		return 'eicon-welcome';
	}

	public function get_categories() {
		return [ 'zinkobooking' ];
	}

	protected function _register_controls() {

        $tempt_locations = get_terms('location');

        foreach($tempt_locations as $location) {
            $this->zinko_locations[$location->term_id] = $location->name;
        }

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'zinkobooking' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'count',
			[
				'label' => __( 'Posts Count', 'zinkobooking' ),
				'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 3,
			]
		);

        $this->add_control(
			'offer',
			[
				'label' => __( 'Offer', 'zinkobooking' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
                    '' => 'Select Smth',
					'sale'  => __( 'For Sale', 'zinkobooking' ),
					'rent' => __( 'For Rent', 'zinkobooking' ),
					'sold' => __( 'Sold', 'zinkobooking' ),
				],
			]
		);

        $this->add_control(
			'location',
			[
				'label' => __( 'Location', 'zinkobooking' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => $this->zinko_locations,
			]
		);

		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings_for_display();

        $args = array(
            'post_type' => 'property',
            'posts_per_page' => $settings['count'],
            'meta_query' => array('relation'=> 'AND'),
            'tax_query' => array('relation'=> 'AND'),
        );

        if(isset($settings['offer']) && $settings['offer'] != '') {
            array_push($args['meta_query'], array(
                'key' => 'zinkobooking_type',
                'value' => esc_attr($settings['offer']),
            ));
        }

        if(isset($settings['location']) && $settings['location'] != '') {
            array_push($args['tax_query'], array(
                'taxonomy' => 'location',
                'terms' => $settings['location'],
            ));
        }

        $proprties = new WP_Query($args);

        $this->zinkoBooking_Template = new zinkobooking_Template_loader();

        if ( $proprties->have_posts() ) {

            echo '<div class="wrapper archive-property">';

            while ( $proprties->have_posts() ) {
                $proprties->the_post(); 
                $this->zinkoBooking_Template->get_template_part('partials/content');
            } 

            echo '</div>';
            
        }

        wp_reset_postdata();

	}

}