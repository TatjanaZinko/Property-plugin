<?php
get_header(); ?>

<?php $zinkoBooking_Template->get_template_part('partials/filter'); ?>

<div class="wrapper archive-property">

<?php

if(!empty($_POST['submit'])) {

    $args = array(
        'post_type' => 'property',
        'posts_per_page' => -1,
        'meta_query' => array('relation'=> 'AND'),
        'tax_query' => array('relation'=> 'AND'),
    );

    if(isset($_POST['zinkobooking_type']) and $_POST['zinkobooking_type'] != '') {
        array_push($args['meta_query'], array(
            'key' => 'zinkobooking_type',
            'value' => esc_attr($_POST['zinkobooking_type']),
        ));
    }

    if(isset($_POST['zinkobooking_price']) and $_POST['zinkobooking_price'] != '') {
        array_push($args['meta_query'], array(
            'key' => 'zinkobooking_price',
            'value' => esc_attr($_POST['zinkobooking_price']),
            'type' => 'numeric',
            'compare' => '<=',
        ));
    }

    if(isset($_POST['zinkobooking_agent']) and $_POST['zinkobooking_agent'] != '') {
        array_push($args['meta_query'], array(
            'key' => 'zinkobooking_agent',
            'value' => esc_attr($_POST['zinkobooking_agent']),
        ));
    }

    if(isset($_POST['zinkobooking_location']) and $_POST['zinkobooking_location'] != '') {
        array_push($args['tax_query'], array(
            'taxonomy' => 'location',
            'terms' => $_POST['zinkobooking_location'],
        ));
    }
    
    if(isset($_POST['zinkobooking_property-type']) and $_POST['zinkobooking_property-type'] != '') {
        array_push($args['tax_query'], array(
            'taxonomy' => 'property-type',
            'terms' => $_POST['zinkobooking_property-type'],
        ));
    }  

    $properties = new WP_Query($args);

    if ( $properties->have_posts() ) {

        // Load posts loop.
        while ( $properties->have_posts() ) {
            $properties->the_post(); 
            $zinkoBooking_Template->get_template_part('partials/content');
        } 
    }
    

} else {

    if ( have_posts() ) {

        // Load posts loop.
        while ( have_posts() ) {
            the_post(); 
            $zinkoBooking_Template->get_template_part('partials/content');
        } 

    //Pagination
    posts_nav_link();
        
    } else {

        echo '<p>' . esc_html__('No Properties', 'zinkobooking') . '</p>';	

    }
    } ?>

</div>

<?php
get_footer();