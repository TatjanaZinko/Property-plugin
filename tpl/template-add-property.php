<?php
/*
*
* Template Name: Add Property
*
*
*/

function property_image_validation ($file_name){
    $valid_extensions = array('jpg', 'jpeg', 'gif', 'png');
    $exploded_array = explode('.', $file_name);
    if(!empty($exploded_array) && is_array($exploded_array)) {
        $ext = array_pop($exploded_array);
        return in_array($ext, $valid_extensions);
    } else {
        return false;
    }
}

function zinkobooking_insert_attachment($file_handler, $post_id, $setthumb=false) {

    if($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();

    require_once(ABSPATH . "wp-admin" . "/includes/image.php");
    require_once(ABSPATH . "wp-admin" . "/includes/file.php");
    require_once(ABSPATH . "wp-admin" . "/includes/media.php");

    $attach_id = media_handle_upload($file_handler, $post_id);

    if($setthumb) {
        update_post_meta($post_id, '_thumbnail_id', $attach_id);
    }

    return $attach_id;
}

if(isset($_POST['action']) && is_user_logged_in()) {
    if(wp_verify_nonce($_POST['property_nonce'], 'submit_property')) {
        $zinkobooking_item = array();
        $zinkobooking_item['post_title'] = sanitize_text_field($_POST['property_title']);
        $zinkobooking_item['post_type'] = 'property';
        $zinkobooking_item['post_content'] = sanitize_textarea_field($_POST['property_description']);

        global $current_user; wp_get_current_user();
        $zinkobooking_item['post_author'] = $current_user->ID;

        $zinkobooking_action = $_POST['action'];

        if($zinkobooking_action == 'zinkobooking_add_property') {
            $zinkobooking_item['post_status'] = 'pending';            
            $zinkobooking_item_id = wp_insert_post($zinkobooking_item);
            if($zinkobooking_item_id > 0) {
                do_action('wp_insert_post', 'wp_insert_post');
                $success = 'Property Successfull Published';
            }
        } elseif($zinkobooking_action == 'zinkobooking_edit_property') {
            $zinkobooking_item['post_status'] = 'pending';
            $zinkobooking_item['ID'] = intval($_POST['property_id']);
            $zinkobooking_item_id = wp_update_post($zinkobooking_item);
            $success = 'Property Successfull Updated';
        }
        if($zinkobooking_item_id > 0) {

            //Metabox
            if(isset($_POST['property_offer']) && $_POST['property_offer'] == "") {
                update_post_meta($zinkobooking_item_id, 'zinkobooking_type', trim($_POST['property_offer']) );
            }
            if(isset($_POST['property_price'])) {
                update_post_meta($zinkobooking_item_id, 'zinkobooking_price', trim($_POST['property_price']) );
            }
            if(isset($_POST['property_period'])) {
                update_post_meta($zinkobooking_item_id, 'zinkobooking_period', trim($_POST['property_period']) );
            }
            if(isset($_POST['property_agent']) && $_POST['property_agent'] != "disable") {
                update_post_meta($zinkobooking_item_id, 'zinkobooking_agent', trim($_POST['property_agent']) );
            }

            //Taxonomy
            if(isset($_POST['property_location'])) {
                wp_set_object_terms($zinkobooking_item_id, intval($_POST['property_location']), 'location');
            }
            if(isset($_POST['property_type'])) {
                wp_set_object_terms($zinkobooking_item_id, intval($_POST['property_type']), 'property_type');
            }

            //featured image
            if($_FILES['property_image']) {
                foreach($_FILES['property_image'] as $submitted_file => $file_array) {
                   
                    if(property_image_validation($_FILES[$submitted_file]['name'])) {
                        $size = intval($_FILES[$submitted_file]['size']);

                        if($size > 0){
                            zinkobooking_insert_attachment($submitted_file, $zinkobooking_item_id, true);
                        } 
                    }
                }
            }
        }

    }
}

get_header(); ?>

<div class="wrapper">
<?php
if ( have_posts() ) {

	while ( have_posts() ) {
		the_post(); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="description"><?php the_content(); ?></div>        
        </article>

        <div class="add_form">
            <?php if(is_user_logged_in()) { 
                if(!empty($success)) {
                    echo esc_html__($success);
                } else {    
                
                    if(isset($_GET['edit']) && !empty($_GET['edit'])) {

                        $property_id_edit = intval(trim($_GET['edit']));

                        $zinkobooking_edit_property = get_post($property_id_edit);

                        if(!empty($zinkobooking_edit_property) && $zinkobooking_edit_property->post_type == 'property') {

                            global $current_user; wp_get_current_user();

                            if($zinkobooking_edit_property->post_author == $current_user->ID) {
                                $zinkobooking_metadata = get_post_custom($zinkobooking_edit_property->ID);
                                ?>
                                
                                    <h2>Edit property</h2>
                                    <form method="post" id="add_property" enctype="multipart/form-data">
                                        <p>
                                            <label for="property_title">Title</label>
                                            <input type="text" name="property_title" id="property_title" placeholder="Add the title" value="<?php echo $zinkobooking_edit_property->post_title; ?>" required tabindex="1">
                                        </p>
                                        <p>
                                            <label for="property_description">Description</label>
                                            <textarea name="property_description" id="property_description" placeholder="Add the description" required tabindex="2"><?php echo $zinkobooking_edit_property->post_content; ?></textarea>
                                        </p>
                                        <p>
                                            <label for="property_image">Featured image</label>
                                            <input type="file" name="property_image" id="property_image" tabindex="3">
                                        </p>  
                                        <p>
                                            <label for="property_location">Select location</label>
                                            <select name="property_location" id="property_location" required tabindex="4">
                                                <?php 
                                                $current_term_id = 0;
                                                $tax_terems = get_the_terms($zinkobooking_edit_property->ID, 'location');

                                                if(!empty($tax_terems)) {
                                                    foreach($tax_terems as $tax_terem) {
                                                        $current_term_id = $tax_terem->term_id;
                                                        break;
                                                    }
                                                }

                                                $current_term_id = intval($current_term_id);
                                                
                                                $locations = get_terms(array('location'), array('hide_empty'=>false));
                                                    if(!empty($locations)) {
                                                        foreach($locations as $location) {
                                                            $selected = '';
                                                            if($current_term_id == $location->term_id) {
                                                                $selected = 'selected';
                                                            }
                                                            echo '<option value="'. $location->term_id . '"' . $selected . '>'.$location->name . '<option>';
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </p>
                                        <p>
                                            <label for="property_type">Select property type</label>
                                            <select name="property_type" id="property_type" required tabindex="5">
                                                <?php 
                                                 $current_term_id = 0;
                                                 $tax_terems = get_the_terms($zinkobooking_edit_property->ID, 'property-type');
 
                                                 if(!empty($tax_terems)) {
                                                     foreach($tax_terems as $tax_terem) {
                                                         $current_term_id = $tax_terem->term_id;
                                                         break;
                                                     }
                                                 }
 
                                                 $current_term_id = intval($current_term_id);
                                                $types = get_terms(array('property-type'), array('hide_empty'=>false));
                                                    if(!empty($types)) {
                                                        
                                                        foreach($types as $type) {
                                                            $selected = '';
                                                            if($current_term_id == $type->term_id) {
                                                                $selected = 'selected';
                                                            }
                                                            echo '<option value="'. $type->term_id . '"'. $selected .'>'.$type->name . '</option>';
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </p>
                                        <p>
                                            <label for="property_offer">Select offer</label>
                                            <select name="property_offer" id="property_offer" tabindex="6">
                                                <option selected value="">Not selected</option>
                                                <option value="sale" <?php if(get_post_meta($zinkobooking_edit_property->ID, 'zinkobooking_type', true) == 'sale'){echo 'selected';} ?>>For sale</option>
                                                <option value="sold" <?php if(get_post_meta($zinkobooking_edit_property->ID, 'zinkobooking_type', true) == 'sold'){echo 'selected';} ?>>Sold</option>
                                                <option value="rent" <?php if(get_post_meta($zinkobooking_edit_property->ID, 'zinkobooking_type', true) == 'rent'){echo 'selected';} ?>>For rent</option>
                                            </select>
                                        </p>
                                        <p>
                                            <label for="property_price">Price</label>
                                            <input type="text" name="property_price" id="property_price" value="<?php echo get_post_meta($zinkobooking_edit_property->ID, 'zinkobooking_price', true); ?>" tabindex="7">
                                        </p>
                                        <p>
                                            <label for="property_period">Period</label>
                                            <input type="text" name="property_period" id="property_period" value="<?php echo get_post_meta($zinkobooking_edit_property->ID, 'zinkobooking_period', true); ?>" tabindex="8">
                                        </p>
                                        <p>
                                            <?php global $current_user; wp_get_current_user(); ?>
                                            <label for="property_agent">Agent</label>
                                            <select name="property_agent" id="property_agent" tabindex="9">
                                                <option selected value="disable">Disavble Agent</option>
                                                <?php $agents = get_posts(array('post_type'=>'agent', 'numberposts'=>-1));
                                                if(!empty($agents)) {
                                                    $selected = '';
                                                    $current_agent = get_post_meta($zinkobooking_edit_property->ID, 'zinkobooking_agent', true);
                                                    foreach($agents as $agent){
                                                        if($current_agent == $agent->ID) {
                                                            $selected = 'selected';
                                                        }
                                                        echo '<option value="'. $agent->post_id . '"'.$selected.'>'.$agent->post_title . '</option>'; 
                                                    }
                                                } ?>
                                                
                                            </select>
                                        </p>
                                        <p>
                                            <?php wp_nonce_field('submit_property', 'property_nonce'); ?>
                                            <input type="submit" name="submit" tabindex="9" value="Edit property">
                                            <input type="hidden" name="action" value="zinkobooking_edit_property">
                                            <input type="hidden" name="property_id" value="<?php echo esc_attr($zinkobooking_edit_property->ID) ?>">
                                        </p>

                                    </form>

                            <?php }

                        }

                    } else {                        
            ?>
            
                        <h2><?php the_title(); ?></h2>
                        <form method="post" id="add_property" enctype="multipart/form-data">
                            <p>
                                <label for="property_title">Title</label>
                                <input type="text" name="property_title" id="property_title" placeholder="Add the title" value="" required tabindex="1">
                            </p>
                            <p>
                                <label for="property_description">Description</label>
                                <textarea name="property_description" id="property_description" placeholder="Add the description" required tabindex="2"></textarea>
                            </p>
                            <p>
                                <label for="property_image">Featured image</label>
                                <input type="file" name="property_image" id="property_image" tabindex="3">
                            </p>  
                            <p>
                                <label for="property_location">Select location</label>
                                <select name="property_location" id="property_location" required tabindex="4">
                                    <?php $locations = get_terms(array('location'), array('hide_empty'=>false));
                                        if(!empty($locations)) {
                                            foreach($locations as $location) {
                                                echo '<option value="'. $location->term_id . '">'.$location->name . '<option>';
                                            }
                                        }
                                    ?>
                                </select>
                            </p>
                            <p>
                                <label for="property_type">Select property type</label>
                                <select name="property_type" id="property_type" required tabindex="5">
                                    <?php $types = get_terms(array('property-type'), array('hide_empty'=>false));
                                        if(!empty($types)) {
                                            foreach($types as $type) {
                                                echo '<option value="'. $type->term_id . '">'.$type->name . '</option>';
                                            }
                                        }
                                    ?>
                                </select>
                            </p>
                            <p>
                                <label for="property_offer">Select offer</label>
                                <select name="property_offer" id="property_offer" tabindex="6">
                                    <option selected value="">Not selected</option>
                                    <option value="sale">For sale</option>
                                    <option value="sold">Sold</option>
                                    <option value="rent">For rent</option>
                                </select>
                            </p>
                            <p>
                                <label for="property_price">Price</label>
                                <input type="text" name="property_price" id="property_price" value="" tabindex="7">
                            </p>
                            <p>
                                <label for="property_period">Period</label>
                                <input type="text" name="property_period" id="property_period" value="" tabindex="8">
                            </p>
                            <p>
                                <?php global $current_user; wp_get_current_user(); ?>
                                <label for="property_agent">Agent</label>
                                <select name="property_agent" id="property_agent" tabindex="9">
                                    <option selected value="disable">Disavble Agent</option>
                                    <?php $agents = get_posts(array('post_type'=>'agent', 'numberposts'=>-1));
                                    if(!empty($agents)) {
                                        foreach($agents as $agent){
                                            echo '<option value="'. $agent->post_id . '">'.$agent->post_title . '</option>'; 
                                        }
                                    } ?>
                                    
                                </select>
                            </p>
                            <p>
                                <?php wp_nonce_field('submit_property', 'property_nonce'); ?>
                                <input type="submit" name="submit" tabindex="9" value="Add new property">
                                <input type="hidden" name="action" value="zinkobooking_add_property">
                            </p>

                        </form>
            <?php }
                }
            } ?>
        </div>

	<?php } 
	
} ?>
</div>

<?php get_footer();