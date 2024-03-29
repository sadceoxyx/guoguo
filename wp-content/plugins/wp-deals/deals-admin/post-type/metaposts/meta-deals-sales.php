<?php

add_action('save_post','deals_sales_save_meta', 1, 2);
function deals_sales_save_meta($post_id, $post) {
	
	if ( !$_POST ) return $post_id;
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
	if ($post->post_type == 'revision') return $post_id;	
	
	if($post->post_type == 'deals-sales' && $post->post_title != 'Sales ID #'.$post_id) {
		
		$new_post = array();
		$new_post['ID'] = $post_id;
		$new_post['post_title'] = 'Sales ID #'.$post_id;
		
		wp_update_post($new_post);
		
	}elseif($post->post_type == 'deals-sales' && $post->post_title == 'Sales ID #'.$post_id && $post->post_status != 'publish') {
            
                // publish post
                wp_publish_post($post_id);
        }
	
	if(isset($_POST['_deals_sales_payment_method'])) {
		update_post_meta($post_id,'_deals_sales_payment_method',$_POST['_deals_sales_payment_method']);
	}
	
	if(isset($_POST['_deals_sales_transaction_status'])) {
		update_post_meta($post_id,'_deals_sales_transaction_status',$_POST['_deals_sales_transaction_status']);
	}
	
	if(isset($_POST['_deals_sales_user_id'])) {
		
		update_post_meta($post_id,'_deals_sales_user_id',$_POST['_deals_sales_user_id']);
		$userdata = get_userdata($_POST['_deals_sales_user_id']);
		$username = $userdata->user_login;
		update_post_meta($post_id,'_deals_sales_user_name',$username);
		
	}
	
	if(isset($_POST['_deals_sales_item_id'])) {
		
		update_post_meta($post_id,'_deals_sales_item_id',$_POST['_deals_sales_item_id']);
		$itemdata = get_post($_POST['_deals_sales_item_id']);
		$itemprice = get_post_meta($_POST['_deals_sales_item_id'],'_discount_price',true);
		update_post_meta($post_id,'_deals_sales_item_name',$itemdata->post_title);
		update_post_meta($post_id,'_deals_sales_amount',$itemprice);
		
	}
	
	//create and send invoices
	if(isset($_POST['send_invoices'])) {
		
		$template = DEALS_TEMPLATE_DIR . 'form/mail-invoice.php';
		
		global $checkVerify, $invoice_options, $invoice_data;
		$itemdata = get_post($_POST['_deals_sales_item_id']);
		$userdata = get_userdata($_POST['_deals_sales_user_id']);
		$itemprice = get_post_meta($_POST['_deals_sales_item_id'],'_discount_price',true);
		
		//create barcode
		$barcode_id = str_replace(array('#',' '),'',$post->post_title);
                deals_image_create_barcode($barcode_id,$barcode_id.'.png');
		$img_barcode_url = home_url('/wp-content/wpdeals/invoices/'.$barcode_id.'.png');
		
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
			'title' => $itemdata->post_title,   
			'link' => home_url('/my-history'),
			'user_name' => $userdata->user_login
		);
		
		ob_start();
                load_template($template);
		
		$mail_subject = __('Your Deals Transaction For ', 'wpdeals') . $itemdata->post_title;
		$mail_content = ob_get_clean();
		$mail_to = $userdata->user_email;
		
		$headers = "Content-Type: text/html" . "\r\n";
		$headers .= ' From: '.  get_bloginfo('name') . ' <'.  get_option('admin_email') .'>' . "\r\n";
		$sent_email_status = (wp_mail($mail_to, $mail_subject, $mail_content, $headers) == true )? 'sent' : 'error';


                //prepare sending email confirmation
                $checkVerify->isHaveCustomMessage = get_post_meta($itempost->ID, '_is_use_custom_email',true);
                if ($checkVerify->isHaveCustomMessage){
                    $checkVerify->customMessage = get_post_meta($itempost->ID, '_custom_email',true);
                }

                /* if coupon code active than set the coupon code for user */
                $itempost = get_post($itemdata);
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
                    $templatePaymentSuccess = DEALS_TEMPLATE_DIR . 'form/mail-invoice.php';
                    load_template($templatePaymentSuccess);

                    $mail_content = ob_get_clean();
                    $headers = "Content-Type: text/html" . "\r\n";
                    $mail_subject = sprintf( __('Deal Transaction Activation - Invoice - %s', 'wpdeals'), $itempost->post_title);
                    $headers .= ' From: '.  get_bloginfo('name') . ' <'.  get_option('admin_email') .'>' . "\r\n";
                    $sent_email_status = (wp_mail($mail_to, $mail_subject, $mail_content, $headers) == true )? 'sent' : 'error';
                    deals_log($sent_email_status);
                }


	}
	
}

function deals_sales_detail_box() {	
	
	?>
	<style type="text/css">
		#titlediv,#edit-slug-box {display:none}
	</style>
	<?php
	
	_deals_sales_detail();
	
}

function _deals_sales_detail() {	
	global $post,$pagenow;
	
        if($post->post_title == '')
            $title = 'Sales ID #' . $post->ID;
        else
            $title = $post->post_title;
        
	echo '<label><strong>' . $title . '</strong></label>';


        $isSalesHasCoupon = get_post_meta($post->ID,'_coupon_code_assign',true);

	$users = new WP_User_Query(array(
		'fields' => 'all_with_meta'
	));
	
	$deals = new WP_Query(array(
		'post_type' => 'daily-deals',
		'post_status' => 'publish',
		'posts_per_page' => -1,
//		'meta_key' => '_is_expired',
//		'meta_value' => 'no'
	));
	
	$user_results = $users->get_results();
	if(!empty($user_results)) {
		
		$user_options = array();
		foreach($user_results as $user) {
			$user_options[$user->ID] = $user->user_login;
		}
		
		deals_wp_select( array( 'id' => '_deals_sales_user_id', 'label' => __('Choose User', 'wpdeals'), 'class' => 'wide' ,
            'options' => $user_options) );
	}else{
		_e('<label>No customers data retrieved.</label><br />', 'wpdeals');
	}
	
	if($deals->have_posts()) {
		
		$deal_options = array();
		while($deals->have_posts()) : $deals->the_post();						
			$deal_options[get_the_ID()] = get_the_title();			
		endwhile;wp_reset_postdata();
		
		deals_wp_select( array( 'id' => '_deals_sales_item_id', 'label' => __('Choose Items', 'wpdeals'), 'class' => 'wide' ,
            'options' => $deal_options) );
		
	}else{
		echo '<label>No deals data retrieved.</label><br />';
	}
        ?>
        <p class="form-field _deals_sales_item_id_field">
            <label for="_deals_coupon_item_id">Coupon Code</label>
            <label><?php echo $isSalesHasCoupon;?></label>
        </p><br/><br/>
        <?php
	
}

function deals_sales_payments_box() {
    global $post;
    
    $payments = get_option('deals_payments_used');
    $payment_now = get_post_meta($post->ID,'_deals_sales_payment_method',true);
    $payment_now = empty($payment_now) ? __('None', 'wpdeals'): $payment_now;
    
    if(!empty($payments)) {
        
        $payments_data = get_option('deals_payments');
        $payment_options = array();
        
        foreach($payments as $payment) {
            
            $payment_name = $payments_data[$payment]['name'];
            $payment_options[$payment] = $payment_name;
                    
        }
        
        deals_wp_select( array( 'id' => '_deals_sales_payment_method', 'label' => __('Payments', 'wpdeals'), 'class' => 'wide' ,
            'options' => $payment_options) );
        
        echo '<label>Current Payment Used :</label> <strong>'.$payment_now.'</strong>';
        
    }else{
        _e('No payment available', 'wpdeals');
    }
    
}

function deals_sales_status_box() {
    global $post,$pagenow;
    
    switch($pagenow) {
        
        case 'post-new.php':
            $current_status = 'None';
            break;
        
        case 'post.php':
            $current_status = get_post_meta($post->ID,'_deals_sales_transaction_status',true);
            break;
    }
    
    wp_nonce_field( basename( __FILE__ ), 'deals_sales_status_box' );
    echo '<div class="detail-deals">';                    
                        
            deals_wp_select( array( 'id' => '_deals_sales_transaction_status', 'label' => __('Status', 'wpdeals'),'class' => 'wide' ,
                'options' => array(
                    'completed' => __('Completed', 'wpdeals'),
                    'pending' => __('Pending', 'wpdeals'),
                    'onhold' => __('On Hold','wpdeals'),
                    'refund' => __('Refund','wpdeals')
            ) ) );
            
            echo '<label>Current Status :</label> <strong>'.$current_status.'</strong>';            
            
            do_action('deals_sales_status_box_after_select',$post);
            
    echo '</div>';
}

function deals_sales_save_box() {
    global $post,$pagenow;    
    
    switch($pagenow) {
        
        case 'post-new.php':
            $text_inv = __('Save & Send Invoice', 'wpdeals');
            break;
        
        case 'post.php':
            $text_inv = __('Save & Send Invoice', 'wpdeals');
            break;
    }
    ?>
    <input type="submit" class="button button-primary" name="save" value="<?php _e('Save', 'wpdeals'); ?>"  />
    <input type="submit" class="button" name="send_invoices" value="<?php _e($text_inv, 'wpdeals'); ?>"  />
    
    <?php if($pagenow == 'post.php') : ?>
    <ul>        
        <li class="wide">
            <a class="submitdelete deletion" href="<?php echo esc_url( get_delete_post_link($post->ID) ); ?>"><?php _e('Delete', 'nesia'); ?></a>
        </li>
    </ul>    
    <?php
    endif;
    
}