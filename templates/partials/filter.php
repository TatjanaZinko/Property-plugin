<div class="wrapper filter_form">

<?php $options = get_option('zinkobooking_settings_options');

if(isset($options['filter_title'])) { echo $options['filter_title']; }
    ?>

    <form method="post" action="<?php get_post_type_archive_link('property'); ?>">

        <?php $zinkoBooking = new ZinkoBooking();?>
    
        <select name="zinkobooking_location">
            <option value="">Select Location</option>
            <?php echo $zinkoBooking->get_terms_hierarchical('location', $_POST['zinkobooking_location']); ?>
        </select>

        <select name="zinkobooking_property-type">
            <option value="">Select Type</option>
            <?php echo $zinkoBooking->get_terms_hierarchical('property-type', $_POST['zinkobooking_property-type']); ?>
        </select>

        <input type="text" name="zinkobooking_price" placeholder="Maximum Price" value="<?php if(isset($_POST['zinkobooking_price'])) { echo esc_attr($_POST['zinkobooking_price']); } ?>">

        <select name="zinkobooking_type">
            <option value="">Select Offer</option>
            <option value="sale" <?php if(isset($_POST['zinkobooking_type']) and $_POST['zinkobooking_type'] == 'sale') { echo 'selected'; } ?>>For Sale</option>
            <option value="rent" <?php if(isset($_POST['zinkobooking_type']) and $_POST['zinkobooking_type'] == 'rent') { echo 'selected'; } ?>>For Rent</option>
            <option value="sold" <?php if(isset($_POST['zinkobooking_type']) and $_POST['zinkobooking_type'] == 'sold') { echo 'selected'; } ?>>Sold</option>
        </select>

        <select name="zinkobooking_agent">
            <option value="">Select Agent</option>
            <?php $agents = get_posts(array('post_type' => ['agent'], 'numberposts' => -1));
                $selected = '';
                 if(isset($_POST['zinkobooking_agent'])) {
                     $agent_id = $_POST['zinkobooking_agent'];
                 }
                foreach($agents as $agent) {
                    echo '<option value="'. $agent->ID . '"' . selected($agent->ID, $agent_id, false) . '>'. $agent->post_title .'</option>';
                }
            ?>
        </select>

        <input type="submit" name="submit" value="Filter">

    </form>
</div>