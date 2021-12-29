<?php

class zinkobooking_Template_loader extends Gamajo_Template_Loader {

    protected $filter_prefix = 'zinkobooking';

    protected $theme_template_directory = 'zinkobooking';
    
    protected $plugin_directory = ZINKOBOOKING_PATH;

    protected $plugin_template_directory = 'templates';

    public $templates;

    public function register() {

        add_filter('template_include', [$this,'zinkobooking_templates']);

        $this->templates = array(
            'tpl/template-add-property.php' => 'Add Property',
            'tpl/template-listproperty.php' => 'List Personal Properties',
            'tpl/template-wishlist.php' => 'Wishlist',
        );
        add_filter('theme_page_templates', [$this,'custom_template']);
        add_filter('template_include', [$this,'load_template']);
    }

    public function load_template($template) {

        global $post;

        $template_name = get_post_meta($post->ID, '_wp_page_template', true);      

        if($template_name && $this->templates[$template_name]) {
             $file = ZINKOBOOKING_PATH . $template_name;
    
            if(file_exists($file)) {
                return $file;
            }           
        }
             

        return $template;
    }

    public function custom_template($templates) {

        $templates = array_merge($templates, $this->templates);

        return $templates;

    }

    public function zinkobooking_templates($template) {

        if(is_post_type_archive('property')) {
            $theme_files = ['archive-property.php', 'zinkobooking/archive-property.php'];
            $exist = locate_template($theme_files, false);
            if($exist != '') {
                return $exist;
            } else {
                return plugin_dir_path(__DIR__) . 'templates/archive-property.php';
            }
        } elseif(is_post_type_archive('agent')) {
            $theme_files = ['archive-agent.php', 'zinkobooking/archive-agent.php'];
            $exist = locate_template($theme_files, false);
            if($exist != '') {
                return $exist;
            } else {
                return plugin_dir_path(__DIR__) . 'templates/archive-agent.php';
            }
        } elseif(is_singular('property')) {
            $theme_files = ['single-property.php', 'zinkobooking/single-property.php'];
            $exist = locate_template($theme_files, false);
            if($exist != '') {
                return $exist;
            } else {
                return plugin_dir_path(__DIR__) . 'templates/single-property.php';
            }
        } elseif(is_singular('agent')) {
            $theme_files = ['single-agent.php', 'zinkobooking/single-agent.php'];
            $exist = locate_template($theme_files, false);
            if($exist != '') {
                return $exist;
            } else {
                return plugin_dir_path(__DIR__) . 'templates/single-agent.php';
            }
        }

        return $template;

    }
}

$zinkoBooking_Template = new zinkobooking_Template_loader();
$zinkoBooking_Template->register();