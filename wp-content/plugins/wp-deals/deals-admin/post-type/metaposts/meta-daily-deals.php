<?php
// metabox for detail deals
function deals_product_data_box(){
    global $post;
    
        wp_nonce_field( basename( __FILE__ ), 'deals_product_data_box_nonce' );

        echo '<div class="detail-deals">';
        
                deals_wp_text_input( array( 'id' => '_base_price', 'label' => __('Regular Price (value)', 'wpdeals') ) );
                deals_wp_text_input( array( 'id' => '_discount_price', 'label' => __('Discount Price (value)', 'wpdeals') ) );
                deals_wp_text_input( array( 'id' => '_stock', 'label' => __('Stock (value)', 'wpdeals') ) );
                deals_wp_date( array( 'id' => '_end_time', 'label' => __('End Time', 'wpdeals') ) );
                
                // Set expired
                deals_wp_select( array( 'id' => '_is_expired', 'label' => __('Set Expired', 'wpdeals'), 
                    'options' => array(
                        'no' => __('No', 'wpdeals'),
                        'yes' => __('Yes', 'wpdeals')
                ) ) );
                                
                // Do action for product data box
                do_action( 'deals_product_data_box_after', $post );
                
        echo '</div>';
}

/* Save the meta box's post metadata. */
function deals_product_data_save( $post_id, $post ) {

	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['deals_product_data_box_nonce'] ) || !wp_verify_nonce( $_POST['deals_product_data_box_nonce'], basename( __FILE__ ) ) ) return $post_id;
        if ( !current_user_can( 'edit_post', $post_id )) return $post_id;

        
	update_post_meta( $post_id, '_base_price', stripslashes( $_POST['_base_price'] ) );
	update_post_meta( $post_id, '_discount_price', stripslashes( $_POST['_discount_price'] ) );
	update_post_meta( $post_id, '_stock', stripslashes( $_POST['_stock'] ) );
	update_post_meta( $post_id, '_end_time', stripslashes( $_POST['_end_time'] ) );
	update_post_meta( $post_id, '_is_expired', stripslashes( $_POST['_is_expired'] ) );
                
	// Do action for product save box
	do_action( 'deals_product_data_save_after', $post_id );

}
add_action( 'save_post', 'deals_product_data_save', 10, 2 );



/**
 * metabox for detail deals
 *
 * @global type $post 
 */
function deals_product_file_box(){
    global $post;
    
        wp_nonce_field( basename( __FILE__ ), 'deals_product_file_box_nonce' );

        echo '<div class="file-deals">';
                        
                deals_wp_upload( array( 'id' => '_product_link', 'label' => __('File path', 'wpdeals') ) );
                
                // Do action for product data box
                do_action( 'deals_product_file_box_after', $post );
                
        echo '</div>';
}


/* Save the meta box's post metadata. */
function deals_product_file_save( $post_id, $post ) {

	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['deals_product_file_box_nonce'] ) || !wp_verify_nonce( $_POST['deals_product_file_box_nonce'], basename( __FILE__ ) ) ) return $post_id;      
        if ( !current_user_can( 'edit_post', $post_id )) return $post_id;
        
	update_post_meta( $post_id, '_product_link', stripslashes( $_POST['_product_link'] ) );
                
	// Do action for product save box
	do_action( 'deals_product_file_save_after', $post_id );

}

/* open the csv file for coupon */
function deals_coupon_file($post){
    global $post;
    wp_nonce_field( basename( __FILE__ ), 'deals_coupon_file_box_nonce' );
    
    $couponCode = get_post_meta($post->ID, '_coupon_code');
    $_is_send_coupon = get_post_meta($post->ID, '_is_send_coupon',true);

    if(count($couponCode) > 0 ){
        $couponCode = implode('; ', $couponCode[0]);
    }else{
        $couponCode = '';
    }

    /* create form for select the coupon code */?>
        <table class="form-table">
                <tbody>
                    <tr style="border-top:1px solid #eeeeee;">
                        <td colspan="2">
                        <input type="checkbox" name="_is_send_coupon" id="_is_send_coupon" <?php if ($_is_send_coupon == '1'):?>checked="checked"<?php endif;?> /> Send Coupon</span>
                        </td>
                    </tr>
                    <tr style="border-top:1px solid #eeeeee;">
                        <th style="width:25%">
                        <label for="_coupon_area_deals">
                        <span>Coupon <br/> [Separated by <strong>;</strong>]</span></label>
                        <span style=" display:block; color:#999; line-height: 20px; margin:5px 0 0 0;"></span>
                        </th>
                        <td>
                        <textarea name="_coupon_area_deals" id="_coupon_area_deals" style="width:75%; margin-right: 20px; float:left; height:80px;"><?php echo @$couponCode;?></textarea>
                        </td>
                    </tr>
                    <tr style="border-top:1px solid #eeeeee;">
                        <th style="width:25%">
                        <label for="_coupon_file">
                        <span>Coupon File</span></label>
                        <span style=" display:block; color:#999; line-height: 20px; margin:5px 0 0 0;"></span>
                        </th>
                        <td>
                        <input type="file" name="_coupon_file" id="_coupon_file" />
                        </td>
                    </tr>
                </tbody>
        </table>
            <?php
            /*deals_wp_upload( array( 'id' => '_coupon_link', 'label' => __('File path', 'wpdeals') ) );*/

            // Do action for product data box
            do_action( 'deals_coupon_file_box_after', $post_id );

    /* end create form */
    
}

function deals_coupon_file_save($post_id){
    /* verify first */
    if ( !isset( $_POST['deals_coupon_file_box_nonce'] ) || !wp_verify_nonce( $_POST['deals_coupon_file_box_nonce'], basename( __FILE__ ) ) ) return $post_id;
    if ( !current_user_can( 'edit_post', $post_id )) return $post_id;
    
    
    $isSendCoupon = '0';
    if (isset($_POST['_is_send_coupon'])){
        $isSendCoupon = '1';
    }
    update_post_meta( $post_id, '_is_send_coupon', $isSendCoupon );
    /* get the file from post */
    $arrayCoupon = array();
    if(isset($_FILES['_coupon_file']['tmp_name'])) {
        $tmpName = $_FILES['_coupon_file']['tmp_name'];
        if (($handle = fopen($tmpName, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
                /* save the content into database */
                if ($data[0] != ''){
                    $arrayCoupon[trim($data[0])] = trim($data[0]) ;
                }
            }

        }

    }

    if (trim($_POST['_coupon_area_deals'])!= ''){

        $dealsCoupon = str_replace(' ', '', $_POST['_coupon_area_deals']);
        $dealsCoupon = explode(';', $dealsCoupon);
        //$dealsCoupon = array_flip($dealsCoupon);
        //print_r($dealsCoupon);
        $arrayCoupon = array_merge($dealsCoupon,$arrayCoupon);
        
    }

    foreach ($arrayCoupon as $key => $value){
        if (trim($key) == ''){
            unset($arrayCoupon[$key]);
        }
    }
    //print_r($_FILES);
    //print_r($arrayCoupon);
    //exit;
    if (count($arrayCoupon) > 0){
        update_post_meta( $post_id, '_coupon_code', $arrayCoupon );
    }
    /* Save all file into post_meta */
    /* end save */
}

function deals_custom_email($post){
    wp_nonce_field( basename( __FILE__ ), 'deals_custom_email_box_nonce' );
    $_is_use_custom_email = get_post_meta($post->ID, '_is_use_custom_email',true);
    $_custom_email = get_post_meta($post->ID, '_custom_email',true);
    /* create form for select the coupon code */?>
    <table class="form-table">
        <tbody>
            <tr style="border-top:1px solid #eeeeee;">
                <td colspan="2">
                    <label for="_is_use_custom_email" class="selectit">
                        <input type="checkbox" name="_is_use_custom_email" id="_is_use_custom_email" value="1" <?php if ($_is_use_custom_email == '1'):?>checked="checked"<?php endif;?> /> Custom Email
                    </label>
                </td>
            </tr>
            <tr style="border-top:1px solid #eeeeee;">
                <th style="width:25%">
                <label for="_custom_email">
                    Custom Email <br/>[HTML Tags Allowed]
                    <span style=" display:block; color:#999; line-height: 20px; margin:5px 0 0 0;"></span>
                </label>
                </th>
                <td>
                    <textarea name="_custom_email" id="_custom_email" style="width:75%; margin-right: 20px; float:left; height:80px;"><?php echo $_custom_email;?></textarea>
                </td>
            </tr>
        </tbody>
    </table>
<?php
}

function deals_custom_email_save($post_id){
    /* verify first */
    if ( !isset( $_POST['deals_custom_email_box_nonce'] ) || !wp_verify_nonce( $_POST['deals_custom_email_box_nonce'], basename( __FILE__ ) ) ) return $post_id;
    if ( !current_user_can( 'edit_post', $post_id )) return $post_id;

    /* save the custom email */
    $isCustomEmail = '0';
    if (isset($_POST['_is_use_custom_email'])){
        $isCustomEmail = '1';
    }
    update_post_meta( $post_id, '_is_use_custom_email', $isCustomEmail );
    update_post_meta( $post_id, '_custom_email', $_POST['_custom_email'] );
}


/* end open */
add_action( 'save_post', 'deals_product_file_save', 10, 2 );
add_action( 'save_post', 'deals_coupon_file_save', 10, 1 );
add_action( 'save_post', 'deals_custom_email_save', 10, 1 );