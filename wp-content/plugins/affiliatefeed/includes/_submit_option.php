<?php
    //This file will submit the option from users' input.
    
    print_r($_POST);
    include ('affiliatefeed.php');
    //update_option($option, $newvalue);
    $option = array(
        'af_name' => strip_tags($_POST['name']),
        'af_url'  => strip_tags($_POST['url'])
    );
    
    update_option('af_option', $option);
?>
