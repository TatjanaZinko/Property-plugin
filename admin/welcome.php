<h1><?php echo esc_html__('Welcome to ZinkoBooking plugin', 'zinkobooking'); ?></h1>
<div class="content">
    <?php settings_errors(); ?>
    <form method="post" action="options.php">
        <?php settings_fields('zinkobooking_settings');
        do_settings_sections('zinkobooking_settings');
        submit_button(); ?>
    </form>
</div>
