<?php

add_filter('manage_edit-deals-ipn_columns','deals_ipn_columns');
function deals_ipn_columns($columns) {
    
    $columns = array();
    
    $columns['cb'] = '<input type="checkbox" />';
		$columns['ID'] = __('ID','wpdealss');		
    $columns['sales_id'] = __('Sales ID','wpdealss');
    $columns['txn_id'] = __('Txn ID','wpdealss');
    $columns['txn_type'] = __('Txn Type','wpdealss');
    $columns['ipn_track_id'] = __('IPN Track ID','wpdealss');
    $columns['payer_email'] = __('Payer Email','wpdealss');
    $columns['payer_status'] = __('Payer Status','wpdealss');
    $columns['payment_status'] = __('Payment Status','wpdealss');
    $columns['date'] = __('Date','wpdealss');
    
    return $columns;
}

add_filter('manage_deals-ipn_posts_custom_column','deals_ipn_posts_custom_column');
function deals_ipn_posts_custom_column($column) {
    global $post;
        
    $sales_id = get_post_meta($post->ID,'_deals_ipn_sales',true);
		$payment_status = get_post_meta($post->ID,'_deals_ipn_payment_status',true);
		$user_id = get_post_meta($post->ID,'_deals_ipn_user_id',true);
		$item_id = get_post_meta($post->ID,'_deals_ipn_item_id',true);
		$confirm_paypal = get_post_meta($post->ID,'_deals_ipn_confirm_paypal',true);
		$txn_id = get_post_meta($post->ID,'_deals_ipn_txn_id',true);		
		$txn_type = get_post_meta($post->ID,'_deals_ipn_txn_type',true);		
		$track_id = get_post_meta($post->ID,'_deals_ipn_track_id',true);
		$payer_id = get_post_meta($post->ID,'_deals_ipn_payer_id',true);
		$payer_email = get_post_meta($post->ID,'_deals_ipn_payer_email',true);
		$payer_status = get_post_meta($post->ID,'_deals_ipn_payer_status',true);
		$rec_email = get_post_meta($post->ID,'_deals_ipn_receiver_email',true);
		$rec_id = get_post_meta($post->ID,'_deals_ipn_receiver_id',true);		
    
    switch($column) {
        
        case 'ID':
					echo '<a href="'.get_edit_post_link($post->ID).'">'.$post->ID.'</a>';
					break;
				
				case 'sales_id':
					echo '<a href="'.get_edit_post_link($sales_id).'">'.$sales_id.'</a>';
					break;
				
				case 'txn_id':
						echo $txn_id;
						break;
				
				case 'txn_type':
						echo $txn_type;
						break;
				
				case 'ipn_track_id':
						echo $track_id;
						break;
				
				case 'payer_email':
						echo $payer_email;
						break;
				
				case 'payer_status':
						echo $payer_status;
						break;	
				
				case 'payment_status':
					echo $payment_status;
					break;
        
    }
}