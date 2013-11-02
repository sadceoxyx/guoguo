<?php

abstract class Payment_Gateway_Abstract {
    
    public $id;
    public $name;        
    public $desc;
    protected $_sales_data = null;
    
    /**
     * Load payment gateway page template
     * @access public     
     * @return void
     */
    abstract public function get_payment_template();
    
    /**
     * Get filepath (fullpath!) of 'this' class
     * @access public
     * @return string
     */
    abstract public function get_path();
    
    /**
     * Display options at admin page
     * @access public
     * @return void
     */
    abstract public function admin_options();
    
    /**
     * Save data options
     * 
     * @access public
     * @param array $data
     * @return void
     */
    abstract public function save_options($data);
    
    public function update_transaction_data($sale_id,$transaction_status='completed') {
        
        //update deals-sales
        $sale_id = intval($sale_id);
        $sale_updated = array();
        $sale_updated['ID'] = $sale_id;        
        $sale_updated['post_status'] = 'publish';
        wp_update_post($sale_updated);
        
        //update transaction status        
        update_post_meta($sale_id,'_deals_sales_transaction_status',$transaction_status);
        
        //get sales data transaction
        $this->_sales_data = get_post($sale_id);
    }
    
    public function send_invoice() {
        
        //email processing
        $template = DEALS_TEMPLATE_DIR . 'form/mail-invoice.php';
        $templatePaymentSuccess = DEALS_TEMPLATE_DIR . 'form/mail-invoice.php';
        global $checkVerify,$invoice_options,$invoice_data;
        //deals_log($template);                
        
        if(!is_null($this->_sales_data)) {
            
            //setup variables
            $post = $this->_sales_data;
            $item_id = get_post_meta($post->ID,'_deals_sales_item_id',true);
            $user_id = get_post_meta($post->ID,'_deals_sales_user_id',true);
            $itemprice = get_post_meta($item_id,'_discount_price',true);
            $itempost = get_post($item_id);
            $wp_user = get_userdata($user_id);
            
            //create barcode
            $barcode_id = str_replace(array('#',' '),'',$post->post_title);
            deals_image_create_barcode($barcode_id,$barcode_id.'.png');
            $img_barcode_url = home_url('/wp-content/wpdeals/invoices/'.$barcode_id.'.png');
            
            //setup invoice settings
            $checkVerify = new stdClass();
            $checkVerify->buy_date = $post->post_date;
            $checkVerify->transaction_id = $post->post_title;
            $checkVerify->total_price = $itemprice;
            
            $invoice_options = array(
                'info'      => deals_get_option('invoice_desc'),
                'logo_url'  => deals_get_option('invoice_logo_url'),
                'store_name'=> deals_get_option('store_name'),
                'footer'    => deals_get_option('invoice_footer'),
                'barcode' => $img_barcode_url
            );
            
            $invoice_data = array(
                'title' => $itempost->post_title,   
                'link' => get_permalink(get_option('deals_page_history_id')),
                'user_name' => $wp_user->user_login
            );
            
            ob_start();
            load_template($template);
            
            //prepare for sending email
            $mail_to = $wp_user->user_email;
            $mail_subject = __('[SUCCEED] Your Deals Transaction For ', 'wpdeals') . $itempost->post_title;
            //deals_log($mail_to);
            
            //sending email
            $mail_content = ob_get_clean();
            $headers = "Content-Type: text/html" . "\r\n";
            $headers .= ' From: '.  get_bloginfo('name') . ' <'.  get_option('admin_email') .'>' . "\r\n";
            $sent_email_status = (wp_mail($mail_to, $mail_subject, $mail_content, $headers) == true )? 'sent' : 'error';
            //deals_log($sent_email_status);
            
            //sent to admin
            $admin_email = get_option('admin_email');
            $admin_mail_subject = sprintf( __('Deal Transaction - Invoice - %s', 'wpdeals'), $itempost->post_title);
            $admin_mail_status = (wp_mail($admin_email,$admin_mail_subject,$mail_content,$headers) == true )? 'send':'error';

            

            //prepare sending email confirmation
            $checkVerify->isHaveCustomMessage = get_post_meta($itempost->ID, '_is_use_custom_email',true);
            if ($checkVerify->isHaveCustomMessage){
                $checkVerify->customMessage = get_post_meta($itempost->ID, '_custom_email',true);
            }
            
            /* if coupon code active than set the coupon code for user */
            $checkVerify->isUsedCouponCode = get_post_meta($itempost->ID, '_is_send_coupon',1);
            if ($checkVerify->isUsedCouponCode){
                    /* check if sales has coupon code than skip send message*/
                    $isSalesHasCoupon = get_post_meta($post->ID,'_coupon_code_assign',true);
                    $checkVerify->couponCode = $isSalesHasCoupon;
                    if (trim($isSalesHasCoupon) ==  ''){
                        $couponCodeList = get_post_meta($itempost->ID, '_coupon_code',1);
                        $checkVerify->couponCode = array_pop($couponCodeList);
                        update_post_meta($post->ID,'_coupon_code_assign',$checkVerify->couponCode);
                       update_post_meta($itempost->ID, '_coupon_code', $couponCodeList);
            
                        /* save the used coupon code into another logs */
                        $usedCouponCode = get_post_meta($itempost->ID, '_used_coupon_code',1);
                        if (is_array($usedCouponCode) > 0){
                            $usedCouponCode = array_merge($usedCouponCode,$checkVerify->couponCode);
                        }else{
                            $usedCouponCode = $checkVerify->couponCode;
                        }
                        update_post_meta($itempost->ID, '_used_coupon_code', $usedCouponCode);
                        /* if coupon code have less than 5 than send email alert to admin */
                        if (count($couponCodeList) < 5){
                            $admin_mail_subject = 'Your coupon code deal has been sold out';
                            $mail_content = 'Hi, please check your coupon code at '.$itempost->post_title.' it will sold out. Right now you have '.count($couponCodeList).' coupon code';
                            //sent to admin
                            $admin_email = get_option('admin_email');
                            $admin_mail_subject = sprintf( __('Deal Transaction - Invoice - %s', 'wpdeals'), $itempost->post_title);
                            $admin_mail_status = (wp_mail($admin_email,$admin_mail_subject,$mail_content,$headers) == true )? 'send':'error';
                        }
                    }
                    /* end if*/
                }

            if ($checkVerify->isUsedCouponCode == 1 || $checkVerify->isHaveCustomMessage == 1){
                ob_start();
                load_template($templatePaymentSuccess);
            
                $mail_content = ob_get_clean();
                $headers = "Content-Type: text/html" . "\r\n";
                $headers .= ' From: '.  get_bloginfo('name') . ' <'.  get_option('admin_email') .'>' . "\r\n";
                $sent_email_status = (wp_mail($mail_to, $mail_subject, $mail_content, $headers) == true )? 'sent' : 'error';
                //deals_log($sent_email_status);
            } 

        }                
        
    }
    
}