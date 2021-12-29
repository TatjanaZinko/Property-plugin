<?php
get_header(); ?>

<div class="wrapper single-property">

<?php
if ( have_posts() ) {

	// Load posts loop.
	while ( have_posts() ) {
		the_post(); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <?php if(get_the_post_thumbnail(get_the_ID(), 'large')) {
                echo get_the_post_thumbnail(get_the_ID(), 'large');
            }; ?>

            <?php 

            $price = get_post_meta(get_the_ID(), 'zinkobooking_price', true);

            $location_arr = '';
            $locations = get_the_terms(get_the_ID(), 'location');
                foreach($locations as $location) {
                    $location_arr .= ' ' .esc_html($location->name); 
                }

            $agent_id =  get_post_meta(get_the_ID(), 'zinkobooking_agent', true);
            $agent = get_post($agent_id);
            
            echo do_shortcode('[zinkobooking price="'.$price.'" location="'.$location_arr.'" agent="'.$agent->post_title.'"]'); ?>

            <h2><?php the_title(); ?></h2>
            <div class="description"><?php the_content(); ?></div>
            <div class="property_info">
                <span class="location"><?php esc_html_e('Location:', 'zinkobooking');
                echo $location_arr; ?></span>
                <span class="type"><?php esc_html_e('Type:', 'zinkobooking'); 
                $types = get_the_terms(get_the_ID(), 'property-type');
                foreach($types as $type) {
                   echo ' ' .esc_html($type->name); 
                } ?></span>
                <span class="price"><?php esc_html_e('Price:', 'zinkobooking'); echo ' ' . get_post_meta(get_the_ID(), 'zinkobooking_type', true); ?></span>
                <span class="offer"><?php esc_html_e('Offer:', 'zinkobooking'); echo ' ' . $price; ?></span>
                <span class="agent"><?php esc_html_e('Agent:', 'zinkobooking');
              
                echo ' ' . esc_html($agent->post_title) ?></span>
            </div>
        </article>
	<?php } 
	
} ?>

</div>

<?php
get_footer();