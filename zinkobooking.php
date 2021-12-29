<?php
/*
Plugin Name: Zinko Booking
Plugin URI: http://plugin.prfi.ru/
Description: Plugin for booking property
Version: 1.0
Author: Zinko
Author URI: http://plugin.prfi.ru/
Licence: GPLv2 or later
Text Domain: zinkobooking
Domain Path: /lang
*/

if(!defined('ABSPATH')) {
    die;
}

define('ZINKOBOOKING_PATH', plugin_dir_path(__FILE__));

if(!class_exists('ZinkoBookingCPT')) {
    require ZINKOBOOKING_PATH . 'inc/class-zinkobooking-cpt.php';
}

if(!class_exists('Gamajo_Template_Loader')) {
    require ZINKOBOOKING_PATH . 'inc/class-gamajo-template-loader.php';
}


require ZINKOBOOKING_PATH . 'inc/class-zinkobooking-template-loader.php';
require ZINKOBOOKING_PATH . 'inc/class-zinkobooking-shortcodes.php';
require ZINKOBOOKING_PATH . 'inc/class-zinkobooking-filter-widget.php';
require ZINKOBOOKING_PATH . 'inc/class-zinkobooking-elementor.php';
require ZINKOBOOKING_PATH . 'inc/class-zinkobooking-wpbakery.php';
require ZINKOBOOKING_PATH . 'inc/class-zinkobooking-form.php';
require ZINKOBOOKING_PATH . 'inc/class-zinkobooking-wishlist.php';


class ZinkoBooking {
   
    function register() {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_front']);

        add_action('plugins_loaded', [$this, 'load_text_domain']);
        add_action('widgets_init', [$this, 'register_widget']);

        add_action('admin_menu', [$this, 'add_menu_item']);

        add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'add_plugin_setting_link']);
        add_action('admin_init', [$this, 'settings_init']);
    }

    public function settings_init() {

        register_setting('zinkobooking_settings', 'zinkobooking_settings_options');

        add_settings_section('zinkobooking_settings_section', 'Settings', [$this , 'zinkobooking_settings_section_html'], 'zinkobooking_settings' );

        add_settings_field('filter_title', esc_html('Title for Filter', 'zinkobooking'), [$this, 'filter_title_html'], 'zinkobooking_settings', 'zinkobooking_settings_section');
        add_settings_field('archive_title', esc_html('Title for Archive', 'zinkobooking'), [$this, 'archive_title_html'], 'zinkobooking_settings', 'zinkobooking_settings_section');

    }

    public function zinkobooking_settings_section_html () {
        esc_html_e('Settings for Zinkobooking plugin');
    }

    public function filter_title_html() {

        $options = get_option('zinkobooking_settings_options'); ?>

        <input type="text" name="zinkobooking_settings_options[filter_title]" value="<?php echo isset($options['filter_title']) ? $options['filter_title'] : ""; ?>">

    <?php }

    public function archive_title_html() {

        $options = get_option('zinkobooking_settings_options'); ?>

        <input type="text" name="zinkobooking_settings_options[archive_title]" value="<?php echo isset($options['archive_title']) ? $options['archive_title'] : ""; ?>">

    <?php }

    public function add_plugin_setting_link ($link) {

        $zinkobooking_link = '<a href="admin.php?page=zinkobooking_settings">Settings</a>';

        array_push($link, $zinkobooking_link);

        return $link;
    }

    public function add_menu_item() {
        add_menu_page(
            esc_html__('Zinkobooking Settings Page', 'zinkobooking'),
            'Zinkobooking',
            'manage_options',
            'zinkobooking_settings',
            [$this, 'main_admin_page'],
            'dashicons-admin-plugins',
            100
        );
    }

    public function main_admin_page() {
        require_once ZINKOBOOKING_PATH . 'admin/welcome.php';
    }

    public function register_widget() {
        register_widget('zinkobooking_filter_widget');
    }

    public function get_terms_hierarchical ($tax_name, $current_term) {

        $taxonomy_terms = get_terms($tax_name, ['hide_empty'=>false, 'parent'=>0]);

        $html = '';

        if(!empty($taxonomy_terms)) {
            foreach($taxonomy_terms as $term) {
                if($current_term ==  $term->term_id) {
                    $html .=  '<option value="'. $term->term_id . '" selected>'.$term->name.'</option>';
                } else {
                    $html .= '<option value="'. $term->term_id . '">'.$term->name.'</option>';
                }

                $child_terms =  get_terms($tax_name, ['hide_empty'=>false, 'parent'=> $term->term_id]);

                if(!empty($child_terms)) {
                    foreach($child_terms as $child) {
                        if($current_term ==  $child->term_id) {
                            $html .= '<option value="'. $child->term_id . '" selected> - '.$child->name.'</option>';
                        } else {
                            $html .= '<option value="'. $child->term_id . '"> - '.$child->name.'</option>';
                        }
                    }
                }
            }

        }
        return $html;
    }
    

    function load_text_domain() {
        load_plugin_textdomain('zinkobooking', false, dirname(plugin_basename(__FILE__)).'/lang');
    }
    
    public function enqueue_admin(){
        wp_enqueue_style('zinkoBooking_style_admin', plugins_url('/assets/css/admin/style.css', __FILE__));
        wp_enqueue_script('zinkoBooking_script_admin', plugins_url('/assets/js/admin/scripts.css', __FILE__), array('jquery'), '1.0', true);
    }

    public function enqueue_front(){
        wp_enqueue_style('zinkoBooking_style', plugins_url('/assets/css/front/style.css', __FILE__));
        wp_enqueue_script('zinkoBooking_script', plugins_url('/assets/js/front/scripts.js', __FILE__), array('jquery'), '1.0', true);
        wp_enqueue_script('jquery-form');
    }

    static function activation () {
        flush_rewrite_rules();
    }

    static function deactivation () {
        flush_rewrite_rules();
    }    
}

if(class_exists('ZinkoBooking')){
    $zinkoBooking = new ZinkoBooking();
    $zinkoBooking ->register();
}

register_activation_hook(__FILE__, array($zinkoBooking, 'activation'));
register_deactivation_hook(__FILE__, array($zinkoBooking, 'deactivation'));

