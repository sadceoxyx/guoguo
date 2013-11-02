<?php

add_action('save_post','deals_ipn_save_meta', 1, 2);
function deals_ipn_save_meta($post_id) {
	
	if ( !$_POST ) return $post_id;
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
	if ($post->post_type == 'revision') return $post_id;
	
	if(isset($_POST['_deals_ipn_sales'])) {
		update_post_meta($post_id,'_deals_ipn_sales',$_POST['_deals_ipn_sales']);
	}
	
	if(isset($_POST['_deals_ipn_status'])) {
		update_post_meta($post_id,'_deals_ipn_status',$_POST['_deals_ipn_status']);
	}
	
}

function deals_ipn_detail_box() {	
	
	global $pagenow;
	
	?>
	<style type="text/css">
		#titlediv,#edit-slug-box {display:none}
	</style>
	<?php
	
	if( $pagenow == 'post-new.php' ) {
		_deals_ipn_new();
	}else{
		_deals_ipn_edit();
	}
	
}

function _deals_ipn_new() {
	global $post;
	
	$sales = new WP_Query(array(
		'post_type' => 'deals-sales',
		'post_status' => 'publish',
		'posts_per_page' => -1
	));
	
	if($sales->have_posts()) {
		
		$data = array();
		while($sales->have_posts()) : $sales->the_post();
			$data[get_the_ID()] = get_the_title();
		endwhile;wp_reset_postdata();
		
		deals_wp_select( array(
						'id' => '_deals_ipn_sales',
						'label' => __('Choose Sales', 'wpdeals'), 'class' => 'wide' ,
            'options' => $data) );
		
		deals_wp_select( array(
						'id' => '_deals_ipn_payment_status',
						'label' => __('Choose Status', 'wpdeals'), 'class' => 'wide' ,
            'options' => array(
							'completed' => 'Completed',
							'pending' => 'Pending'
						)) );
		
		echo '<strong>'.__('*Note: All ipn data that created manually,did not have any parameters.').'</strong>';
		
	}else{
		_e('<label>No sales data retrieved.</label><br />', 'wpdeals');
	}
	
}

function _deals_ipn_edit() {
	global $post;
	
	$sales_id = get_post_meta($post->ID,'_deals_ipn_sales',true);
	$payment_status = get_post_meta($post->ID,'_deals_ipn_payment_status',true);
	
	echo '<strong>'.__('Sales:','wpdeals').'</strong> '.$sales_id.'<br />';
	echo '<strong>'.__('Payment Status:','wpdeals').'</strong> '.$payment_status;
}

function deals_ipn_params_box() {
	
	global $post;
	
	$ipn_params = get_post_meta($post->ID,'_deals_ipn_params',true);
	if($ipn_params) {
		
		foreach($ipn_params as $key => $val) {
			echo '<strong>'.$key.'</strong> : '.$val.'<br />';
		}
		
	}else{
		echo __('No params available. Parameters only received from Paypal.','wpdeals');
	}
	
}