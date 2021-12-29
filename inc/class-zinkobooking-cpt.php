<?php

if(!class_exists('ZinkoBookingCPT')) {
    class ZinkoBookingCPT {        
    
        function __construct() {
            add_action('init', [$this, 'custom_post_type']);

            add_action('add_meta_boxes', [$this, 'add_meta_box_property']);          

            add_action('save_post', [$this, 'save_metabox'], 10, 2);

            add_action('manage_property_posts_columns', [$this, 'custom_property_columns']);
            add_action('manage_property_posts_custom_column', [$this, 'custom_property_columns_data'], 10, 2);
            add_filter('manage_edit-property_sortable_columns', [$this, 'custom_property_columns_sort']);
            add_action('pre_get_posts', [$this, 'custom_property_order']);
        } 

        public function add_meta_box_property() {
            add_meta_box(
                'zinkobooking_settings',
                'Property Settings',
                [$this, 'metabox_property_html'],
                'property',
                'normal',
                'default',
            );
        }

        public function save_metabox($post_id, $post) {            

            if(!isset($_POST['_zinkobooking']) || !wp_verify_nonce($_POST['_zinkobooking'], 'zinkobookingfields')) {
                return $post_id;
            }

            if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return $post_id;
            }

            if($post->post_type != 'property') {
                return $post_id;
            }

            // $post_type = get_post_type_object($post->post_type);
            // if(!current_user_can($post_type->can->edit_post, $post_id)) {
            //     return $post_id;
            // }

            if(is_null($_POST['zinkobooking_price'])) {
                delete_post_meta($post_id, 'zinkobooking_price');
            } else {
                update_post_meta($post_id, 'zinkobooking_price', sanitize_text_field(intval($_POST['zinkobooking_price'])));
            }

            if(is_null($_POST['zinkobooking_period'])) {
                delete_post_meta($post_id, 'zinkobooking_period');
            } else {
                update_post_meta($post_id, 'zinkobooking_period', sanitize_text_field($_POST['zinkobooking_period']));
            }

            if(is_null($_POST['zinkobooking_type'])) {
                delete_post_meta($post_id, 'zinkobooking_type');
            } else {
                update_post_meta($post_id, 'zinkobooking_type', sanitize_text_field($_POST['zinkobooking_type']));
            }

            if(is_null($_POST['zinkobooking_agent'])) {
                delete_post_meta($post_id, 'zinkobooking_agent');
            } else {
                update_post_meta($post_id, 'zinkobooking_agent', sanitize_text_field($_POST['zinkobooking_agent']));
            }

        }

        public function metabox_property_html($post) {
            $price = get_post_meta($post->ID, 'zinkobooking_price', true);
            $period = get_post_meta($post->ID, 'zinkobooking_period', true);
            $type = get_post_meta($post->ID, 'zinkobooking_type', true);
            $agent_meta = get_post_meta($post->ID, 'zinkobooking_agent', true);

            wp_nonce_field('zinkobookingfields', '_zinkobooking');

            echo '
                <p>
                    <label for="zinkobooking_price">'.esc_html__('Price', 'zinkobooking').'</label>
                    <input type="number" id="zinkobooking_price" name="zinkobooking_price" value="' . esc_html($price) . '">
                </p>

                <p>
                    <label for="zinkobooking_period">'.esc_html__('Period', 'zinkobooking').'</label>
                    <input type="text" id="zinkobooking_period" name="zinkobooking_period" value="' . esc_html($period) . '">
                </p>

                <p>
                    <label for="zinkobooking_type">'.esc_html__('Type', 'zinkobooking').'</label>
                    <select id="zinkobooking_type" name="zinkobooking_type">
                        <option value="">'.esc_html__('Select Type', 'zinkobooking').'</option>
                        <option value="sale" '. selected('sale', $type, false).'>'.esc_html__('For Sale', 'zinkobooking').'</option>
                        <option value="rent" '. selected('rent', $type, false).'>'.esc_html__('For Rent', 'zinkobooking').'</option>
                        <option value="sold" '. selected('sold', $type, false).'>'.esc_html__('Sold', 'zinkobooking').'</option>
                    </select>
                </p>';

                $agents = get_posts(array('post_type'=>'agent', 'numberposts'=>-1));

               if($agents){
                   echo '<p>
                   <label for="zinkobooking_agent">'.esc_html__('Agents', 'zinkobooking').'</label>
                   <select id="zinkobooking_agent" name="zinkobooking_agent">
                   <option value="">'.esc_html__('Select Agent', 'zinkobooking').'</option>';
                    foreach($agents as $agent) { ?>
                        <option value="<?php echo esc_html($agent->ID); ?>" <?php if($agent->ID == $agent_meta){echo 'selected';}; ?>> <?php echo esc_html($agent->post_title) ?> </option>
                    <?php }
                    echo '</select></p>';
               }               
        }
            
        public function custom_post_type() {
    
            register_post_type('property',
                array(
                    'public' => true,
                    'has_archive' => true,
                    'rewrite' => array('slug' => 'properties'),
                    'label' => esc_html__('Property', 'zinkobooking'),
                    'supports' => array('title', 'editor', 'thumbnail'),
            ));
            register_post_type('agent',
                array(
                    'public' => true,
                    'has_archive' => true,
                    'rewrite' => array('slug' => 'agents'),
                    'label' => esc_html__('Agents', 'zinkobooking'),
                    'supports' => array('title', 'editor', 'thumbnail'),
                    'show_in_rest' => true,
            ));
    
            $labels = array(            
                'name'              => esc_html_x( 'Locations', 'taxonomy general name', 'zinkobooking' ),
                'singular_name'     => esc_html_x( 'Location', 'taxonomy singular name', 'zinkobooking' ),
                'search_items'      => esc_html__( 'Search Locations', 'zinkobooking' ),
                'all_items'         => esc_html__( 'All Locations', 'zinkobooking' ),
                'view_item'         => esc_html__( 'View Location', 'zinkobooking' ),
                'parent_item'       => esc_html__( 'Parent Genre', 'zinkobooking' ),
                'parent_item_colon' => esc_html__( 'Parent Genre:', 'zinkobooking' ),
                'edit_item'         => esc_html__( 'Edit Location', 'zinkobooking' ),
                'update_item'       => esc_html__( 'Update Location', 'zinkobooking' ),
                'add_new_item'      => esc_html__( 'Add New Location', 'zinkobooking' ),
                'new_item_name'     => esc_html__( 'New Location Name', 'zinkobooking' ),
                'not_found'         => esc_html__( 'No Locations Found', 'zinkobooking' ),
                'back_to_items'     => esc_html__( 'Back to Locations', 'zinkobooking' ),
                'menu_name'         => esc_html__( 'Location', 'zinkobooking' ),
            );
    
            $args = array(
                'hierarchical' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'query-var' => true,
                'rewrite' => array('slag' => 'properties/location'),
                'labels' => $labels,
            );
    
            register_taxonomy('location', 'property', $args);
    
            unset($args);
            unset($labels);
    
            $labels = array(            
                'name'              => esc_html_x( 'Type', 'taxonomy general name', 'zinkobooking' ),
                'singular_name'     => esc_html_x( 'Type', 'taxonomy singular name', 'zinkobooking' ),
                'search_items'      => esc_html__( 'Search Types', 'zinkobooking' ),
                'all_items'         => esc_html__( 'All Types', 'zinkobooking' ),
                'view_item'         => esc_html__( 'View Type', 'zinkobooking' ),
                'parent_item'       => esc_html__( 'Parent Type', 'zinkobooking' ),
                'parent_item_colon' => esc_html__( 'Parent Type:', 'zinkobooking' ),
                'edit_item'         => esc_html__( 'Edit Type', 'zinkobooking' ),
                'update_item'       => esc_html__( 'Update Type', 'zinkobooking' ),
                'add_new_item'      => esc_html__( 'Add New Type', 'zinkobooking' ),
                'new_item_name'     => esc_html__( 'New Type Name', 'zinkobooking' ),
                'not_found'         => esc_html__( 'No Types Found', 'zinkobooking' ),
                'back_to_items'     => esc_html__( 'Back to Types', 'zinkobooking' ),
                'menu_name'         => esc_html__( 'Type', 'zinkobooking' ),
            );
    
            $args = array(
                'hierarchical' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'query-var' => true,
                'rewrite' => array('slag' => 'properties/type'),
                'labels' => $labels,
            );
    
            register_taxonomy('property-type', 'property', $args);
        }

        public function custom_property_columns($columns) {

            $title = $columns['title'];
            $date = $columns['date'];
            $location = $columns['taxonomy-location'];
            $type = $columns['taxonomy-property-type'];

            $columns['title'] = $title;
            $columns['date'] = $date;
            $columns['taxonomy-location'] = $location;
            $columns['taxonomy-property-type'] = $type;
            $columns['price'] = esc_html__('Price', 'zinkobooking');
            $columns['offer'] = esc_html__('Offer', 'zinkobooking');
            $columns['agent'] = esc_html__('Agent', 'zinkobooking');

            return $columns;

        }

        public function custom_property_columns_data($column, $post_id) {

            $price = get_post_meta($post_id, 'zinkobooking_price', true);
            $offer = get_post_meta($post_id, 'zinkobooking_type', true);
            $agent_id = get_post_meta($post_id, 'zinkobooking_agent', true);
            if($agent_id) {
                $agent = get_the_title($agent_id);
            } else {
                $agent = 'No agent';
            }
            

            switch($column) {
                case 'price':
                    echo $price;
                    break;
                case 'offer':
                    echo $offer;
                    break;
                case 'agent':
                    echo $agent;
                    break;
            }

        }

        public function custom_property_columns_sort($columns){

            $columns['price'] = 'price';
            $columns['offer'] = 'offer';
            //$columns['agent'] = 'agent';

            return $columns;

        }

        public function custom_property_order($query) {

            if(!is_admin()) {
                return;
            }
            $orderby = $query->get('orderby');

            if('price' == $orderby) {
                $query->set('meta_key', 'zinkobooking_price');
                $query->set('orderby', 'meta_value-num');
            }
            if('offer' == $orderby) {
                $query->set('meta_key', 'zinkobooking_type');
                $query->set('orderby', 'meta_value');
            }

        }
    }
}


if(class_exists('ZinkoBookingCPT')){
    $zinkoBookingCPT = new ZinkoBookingCPT();
    //$zinkoBookingCPT -> register();
}