<?php
class AffiliateFeed{
    public $name = 'Affiliate Feed';
    public $options = array();
    
    function __construct() {
      
    }
    
    function generate_admin_page(){ 
        $src = plugins_url('js/af_admin_option.js', dirname(__FILE__));
        wp_register_script('af_admin_option', $src);
        wp_enqueue_script('af_admin_option');
        $msg = '';
        if(!empty($_POST)){
            self::process_data($_POST);
            $msg = '<div class="updated"><p>Your settings have been <strong>updated</strong></p></div>';
        }
        include('_main_option_page.php');
    }
    
    function add_menu_item(){
        add_menu_page( 'Affiliate Feed', 'Affiliate feed', 'manage_options', 'af', 'AffiliateFeed::generate_admin_page' );
    }
    
    function process_data($post){
        //update_option('af_network_name', strip_tags($post['name']));
        //update_option('af_network_url', strip_tags($post['url']));
        //print_r($post);
        
//        $arr = array(
//            'name' => 'haha',
//            'age' => 'what',
//            'sex' => 'bird'
//        );
//        
//        print_r($arr);
//        foreach($post as $name){
//            print_r($name . "<br>");
//        }
        //print_r($post);
    }
}
?>