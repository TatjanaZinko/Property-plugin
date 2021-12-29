<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php if(get_the_post_thumbnail(get_the_ID(), 'large')) {
        echo get_the_post_thumbnail(get_the_ID(), 'large');
    }; ?>
    <h2><?php the_title(); ?></h2>
    <div class="description"><?php the_excerpt(); ?></div>
    <div class="property_info">       
        <?php $locations = get_the_terms(get_the_ID(), 'location');
            if(!empty($locations)) { ?>
                <span class="location"><?php esc_html_e('Location:', 'zinkobooking');
                foreach($locations as $location) {
                    echo ' ' . esc_html($location->name); 
                } ?>
                </span>
        <?php }          
            $types = get_the_terms(get_the_ID(), 'property-type'); ?>
            <span class="type"><?php esc_html_e('Type:', 'zinkobooking');
            if(!empty($types)) {
                foreach($types as $type) {
                    echo ' ' . esc_html($type->name); 
                } ?>
                </span>
            <?php } ?>
        <span class="price"><?php esc_html_e('Price:', 'zinkobooking'); echo ' ' . get_post_meta(get_the_ID(), 'zinkobooking_price', true); ?></span>
        <span class="offer"><?php esc_html_e('Offer:', 'zinkobooking'); echo ' ' . get_post_meta(get_the_ID(), 'zinkobooking_type', true); ?></span>
        <span class="agent"><?php esc_html_e('Agent:', 'zinkobooking');
            $agent_id =  get_post_meta(get_the_ID(), 'zinkobooking_agent', true);
            if($agent_id){
                $agent = get_post($agent_id);
                echo ' ' . esc_html($agent->post_title);
            }
             ?></span>
    </div>
    <a href="<?php the_permalink(); ?>">Open Property</a><br>
    <?php if(is_user_logged_in()) {
        $property_id = get_the_ID(); 
        $user_id = get_current_user_id();
        $wishlist = new Zinkobooking_Wishlist();

        //$wishlist->zinkobooking_in_wishlist($user_id, $property_id);
        if($wishlist->zinkobooking_in_wishlist($user_id, $property_id)){
            if(is_page_template('tpl/template-wishlist.php')) { ?>
                <a href="<?php echo admin_url('admin-ajax.php')?>" class="zinkobooking_remove_property" data-property_id="<?php echo $property_id; ?>" data-user_id="<?php echo $user_id; ?>">Remove from Wishlist</a>
           <?php } else {
                esc_html_e('Already Added');
            }
            
        } else { ?>
            <form action="<?php echo admin_url('admin-ajax.php')?>" method="post" id="zinkobooking_add_to_wishlist_form_<?php echo $property_id; ?>">
                <input type="hidden" name="zinkobooking_user_id" value="<?php esc_html_e($user_id)?>">
                <input type="hidden" name="zinkobooking_property_id" value="<?php esc_html_e($property_id)?>">
                <input type="hidden" name="action" value="zinkobooking_add_wishlist">
            </form>
            <a href="#" class="zinkobooking_add_to_wishlist" data-property_id="<?php echo $property_id; ?>">Add to Wishlist</a>
            <span class="successfull_added" style="display:none">Added to Wishlist</span>
        <?php }  ?>
        
    <?php } ?>
    
</article>		
	