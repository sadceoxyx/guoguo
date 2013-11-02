<?php

define('PAYPAL_ENABLE_LOG',true);

/**
 *
 * Confirm paypal transaction
 * 
 * @param array $post
 * @param string $base_url
 * @return bool 
 */
function paypal_check_transaction($post, $base_url) {
    
    if(empty($post) || is_null($post)) {
        return false;
    }

    $req = 'cmd=_notify-validate';
    foreach ($post as $key => $value) {
        $value = urlencode(stripslashes($value));
        $req .= "&$key=$value";
    }

    $header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

    $fp = fsockopen($base_url, 443, $errno, $errstr, 30);    
    
    if (!$fp) {
        return false;        
    } else {

        fputs($fp, $header . $req);
        $output = 'Result confirm';
        while (!feof($fp)) {
            
            $res = fgets($fp, 1024);
            $output .= $req."\n";
            
            if (strcmp($res, "VERIFIED") == 0) {
                return true;                
                break;
            } else if (strcmp($res, "INVALID") == 0) {
                return false;                
                break;
            }
            
        }
                
        fclose($fp);
        
    }
}

/**
 *
 * Create paypal file log
 * 
 * @global string $paypal_wp_url
 * @param string $messages 
 */
function paypal_log($messages) {
    
    global $paypal_wp_url;
    $log_path = WP_CONTENT_DIR . '/wpdeals/tmps/';    
    $log_file_path = $log_path.'paypal-log';    
    
    if( PAYPAL_ENABLE_LOG ) {
        
        $fp = fopen($log_file_path,'a+');
        $messages = '[ '.date('d-m-Y H:i:s').' ] > '.$messages."\n"; 
        
        if($fp) {
            flock($fp,LOCK_EX);
            fwrite($fp,$messages);        
            flock($fp,LOCK_UN);
        }
        
        fclose($fp);
        
    }
    
}

function paypal_track_ipn($data) {
        
    $post_params = $data['post_vars'];
    $post_id = wp_insert_post(array(
                                    'post_author' => 1,
                                    'post_type' => 'deals-ipn'));
    
    if($post_id) {
        
        update_post_meta($post_id,'_deals_ipn_user_id',$data['user_id']);
        update_post_meta($post_id,'_deals_ipn_item_id',$data['item_id']);
        update_post_meta($post_id,'_deals_ipn_confirm_paypal',$data['confirm_paypal']);
        update_post_meta($post_id,'_deals_ipn_txn_id',$post_params['txn_id']);
        update_post_meta($post_id,'_deals_ipn_txn_type',$post_params['txn_type']);
        update_post_meta($post_id,'_deals_ipn_sales',$data['sales_id']);
        update_post_meta($post_id,'_deals_ipn_track_id',$post_params['ipn_track_id']);
        update_post_meta($post_id,'_deals_ipn_payer_id',$post_params['payer_id']);
        update_post_meta($post_id,'_deals_ipn_payer_email',$post_params['payer_email']);
        update_post_meta($post_id,'_deals_ipn_payer_status',$post_params['payer_status']);
        update_post_meta($post_id,'_deals_ipn_receiver_email',$post_params['receiver_email']);
        update_post_meta($post_id,'_deals_ipn_receiver_id',$post_params['receiver_id']);
        update_post_meta($post_id,'_deals_ipn_payment_status',$post_params['payment_status']);
        update_post_meta($post_id,'_deals_ipn_params',$post_params);
        
    }
    
}