<?php

class ContentRotatorWidget extends WP_Widget
{
    public $name = 'Content Rotator';
    public $description = 'Rotates chunks of content on a peridic basis';
    public $control_options = array(
        'title' => 'Content Rotator',
        'seconds_shelf_life' => 86400  // 86400 seconds in a day
    );
    
    function __construct() {
        $widget_options = array(
            'classname' =>__CLASS__,
            'description' => $this->description,
        );
        
        parent::__construct(__CLASS__, $this->name, $widget_options, $this->control_options);
    }
    
    function widget($args, $instance) {
        print __CLASS__;
    }
    
    static function register_this_widget(){
        register_widget(__CLASS__);
    }
    
    function form(){
        print 'form goes here';
    }

    static function generate_admin_page()
    {
        $msg = 'hello';
        if( !empty($_POST) && check_admin_referer('content_rotation_admin_options_update', 'content_rotation_admin_once')){
            update_option('content_rotation_content_block', stripslashes($_POST['separator']));
            update_option('content_rotation_content_block', stripslashes($_POST['content_block']));
            
            $msg = '<div class="updated"><p>Your settings have been<strong>updated</strong></p></div>';
        }
        include('admin_page.php');
    }
    
    static function add_menu_item()
    {
        add_menu_page( 'Affiliate Feed', 'Affiliate feed', 'manage_options', 'af', 'ContentRotatorWidget::generate_admin_page' );
    }
}