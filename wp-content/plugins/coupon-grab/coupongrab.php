<?php

/*
Plugin Name: Coupon Grab
Plugin URI: http://clipncrazy.com/coupon-plugin/
Description: Post Coupons to your blog
Author: Nick Powers
Version: 1.2
Author URI: http://www.nickpowers.info
*/

// Include code to expire coupons
require_once('include/Expire_Coupon.php');

$the_feed = new Coupon_Feed;


if (isset($the_feed)) {

	register_activation_hook( __FILE__, array($the_feed, 'install'));
	register_deactivation_hook(__FILE__, array($the_feed, 'deactivate'));
}

$debug = 1;
// $debug = 0;

class Coupon_Feed {
	function Coupon_Feed() {
		$this->__construct();
	}

	function __construct() {

		add_action('feed_coupons', array(&$this, 'get_xml'));
		add_action('cfeed_check_cronmailer', array(&$this,'cron_check'));
		add_filter('cron_schedules', array(&$this, 'more_rec'));


		new Coupon_Feed_Options($this);
		new Expire_Coupon($this);

	}

	function deactivate() {
		wp_clear_scheduled_hook('feed_coupons');
		wp_clear_scheduled_hook('expire_coupons');
	}

	function cron_check() {
		$error = 'In cron_check()'; $this->do_debug($error);
	}

	function do_debug($data) {
		global $debug;

		if ($debug) {
			$data = date('l dS \of F Y h:i:s A'). ' '. $data;

			$myFile = 'debug.log';
			$fh = fopen($myFile, 'a');

			fwrite($fh, $data."\n");
			fclose($fh);
		}
	}
	static function install() {
		global $wpdb;

		$table_name = $wpdb->prefix . "couponfeeder";

		$sql =  "CREATE TABLE " . $table_name . " (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			post_id text NOT NULL,
			couponid text NOT NULL,
			expdate text NOT NULL,
			image text NOT NULL,
			link text NOT NULL,
			cfdesc text NOT NULL,
			majcat text NOT NULL,
			mincat text NOT NULL,
			brand text NOT NULL,
			value text NOT NULL,
			UNIQUE KEY id (id));";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	function more_rec() {

		return array('weekly' => array('interval' => 604800, 'display' => 'Once Weekly'));
	}

	function get_xml() {
		global $wpdb;
		$options['coupon_maxpost'] = get_option('coupon_maxpost', '1');

		//$url = 'http://clipncrazy.com/cfeed.xml?site='.urlencode(get_site_url());
		$url = 'http://deals.ebay.com/feeds/xmls';
                $xml = simplexml_load_file($url);

		$size = sizeof($xml->Item);

		$coupon_count = 0;

		for ($loop = 0; $loop < $size; $loop++) {

			$couponid = $xml->Item[$loop]->ItemId;
			$link = $xml->Item[$loop]->DealURL;
			$description = htmlspecialchars($xml->Item[$loop]->Title, ENT_QUOTES);
			$image = $xml->Item[$loop]->PictureURL;
			//$activedate = $xml->Item[$loop]->activedate;
			//$shutoff = $xml->Item[$loop]->shutoff;
			//$expiration = $xml->Item[$loop]->expiration;
			$majorCategory = $xml->Item[$loop]->PrimaryCategoryName;
			//$minorCategory = $xml->Item[$loop]->minorCategory;
			//$brand = htmlspecialchars($xml->Item[$loop]->brand, ENT_QUOTES);
			$value = $xml->Item[$loop]->MSRP;
			//$geotarget = $xml->Item[$loop]->geotarget;

			$sql = 'SELECT COUNT(*) FROM '.$wpdb->prefix.'couponfeeder WHERE couponid = '.$couponid;

			$coupon_posted = $wpdb->get_var($wpdb->prepare($sql));

//			$exp_notime = reset(explode(' ', $expiration));
//			$exp_year = end(explode('/', $exp_notime));
//			$exp_month = reset(explode('/', $exp_notime));
//			$exp_day = array_search(1,array_flip(explode('/', $exp_notime)));
//			$exp_date = $exp_year.'-'.$exp_month.'-'.$exp_day;
//			$exp_timestamp = strtotime($exp_date);
//
//			$todays_date = date("Y-m-d");
//			$today = strtotime($todays_date);

			// Dont post if already posted or coupon expired
			if (!$coupon_posted && /* $exp_timestamp > $today && */ !empty($couponid)) {

				$upload_dir = wp_upload_dir();
				$newfile = $upload_dir['path'].'/'.end(explode('/', $image));

				if (!file_exists($newfile)) {
					copy($image, $newfile);
				}

				$img_link = $upload_dir['url']. '/' . end(explode('/', $image));

				$data_array = array(	'couponid' => $couponid,
							//'expdate' => $exp_timestamp,
							'image' => htmlspecialchars($img_link),
							'link' => htmlspecialchars($link),
							'cfdesc' => $description,
							'majcat' => $majorCategory,
							//'brand' => $brand,
							'value' => $value);

				$wpdb->insert( $wpdb->prefix . 'couponfeeder', $data_array );

				$this->post($description, $couponid, $majorCategory, $newfile);
				$coupon_count ++;

				if($options["coupon_maxpost"] != 0 && $coupon_count >= $options["coupon_maxpost"]) {
					break; 
				}
			}
		}
	}

	function post($description, $couponid, $majcat, $filename){
		global $wpdb;

		$options['coupon_author'] = get_option('coupon_author', '0');
		$options['coupon_status'] = get_option('coupon_status', '0');
		$options['coupon_tag_list'] = get_option('coupon_tag_list', 'Coupons, %majcat%');
		$options['coupon_cat'] = get_option('coupon_cat', '0');
		$options['cgrab_thumbnail'] = get_option('cgrab_thumbnail', 'checked');

		$min_id = get_cat_ID($mincat);
		if(!$min_id) {
			require_once (ABSPATH.'/wp-admin/includes/taxonomy.php');
			$maj_id = get_cat_ID($majcat);
			if(!$maj_id) {
				if($options["coupon_cat"]) {
					$maj_id = wp_create_category($majcat, $options["coupon_cat"]);
				}
				else {
					$maj_id = wp_create_category($majcat);
				}
			}

			$min_id = wp_create_category($mincat, $maj_id);
		}

		$coupon_token = array ("%majcat%", "%mincat%", "%brand%");
		$coupon_replace = array($majcat, $mincat, $brand);
		$coupon_tag_parse = str_replace($coupon_token, $coupon_replace, $options["coupon_tag_list"]);
		$coupon_tags = explode(",", $coupon_tag_parse);

		for($loop = 0; $loop < sizeof($coupon_tags); $loop++) {
			$coupon_tags[$loop] = trim($coupon_tags[$loop]);
		}

		$status_type = array('draft', 'publish', 'pending', 'private');
		$wm_mypost = new wm_mypost();

		$wm_mypost->post_title = $description;
		$wm_mypost->post_content = $description;
		$wm_mypost->post_status = $status_type[$options['coupon_status']];
		$wm_mypost->post_author = $options['coupon_author'];
		$wm_mypost->post_category = array($min_id, $maj_id, $options["coupon_cat"]);
		$wm_mypost->tags_input = $coupon_tags;
		$wp_rewrite->feeds = 'yes';

		$coupon_post_id = wp_insert_post($wm_mypost);

		if ($coupon_post_id) {
			$sql = 'UPDATE '.$wpdb->prefix.'couponfeeder SET post_id='.$coupon_post_id.' WHERE couponid='.$couponid;
			$wpdb->query($sql);

			$wp_filetype = wp_check_filetype(basename($filename), null );

			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
				'post_content' => '',
				'post_status' => 'inherit');

			$attach_id = wp_insert_attachment( $attachment, $filename, $coupon_post_id );

			require_once(ABSPATH . 'wp-admin/includes/image.php');

			$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
			wp_update_attachment_metadata( $attach_id, $attach_data );

			if ($options['cgrab_thumbnail'] == 'checked') {
				add_post_meta($coupon_post_id, '_thumbnail_id', $attach_id, true);
			}
		}
	}

	function parse($id) {

		global $wpdb;


		$options['coupon_format'] = get_option('coupon_format', '<h4><b><font color="blue">%description%</font></b></h4>'
									.'<a href="%link%"><img src="%image%" border="0" align="left" style="padding:10px;"></a>'
									.'<a href="%link%">%brand% coupon</a><br><b>%majcat% %mincat%</b>'
									.'<br /><br /><br /><b>Exp: <font color="green">%exp%</font></b><br /><br />');

		if(empty($id)) {
			$output = '';
		}
		else {
			$couponid = $id;
			$sql = "SELECT expdate FROM ". $wpdb->prefix. "couponfeeder WHERE couponid = ". $couponid;
			$expiration_date = $wpdb->get_var($wpdb->prepare($sql));

			$todays_date = date("Y-m-d");
			$today = strtotime($todays_date);


			$sql = "SELECT * FROM ". $wpdb->prefix. "couponfeeder WHERE couponid = ". $couponid;
			$mycoupon = $wpdb->get_row($sql);

			if($expiration_date > $today) {
			}


			$coupon_token = array ('%image%', '%link%', '%description%', '%majcat%', '%mincat%', '%exp%', '%brand%');
			$coupon_replace = array(htmlspecialchars_decode(stripslashes($mycoupon->image)), htmlspecialchars_decode(stripslashes($mycoupon->link)), $mycoupon->cfdesc, $mycoupon->majcat, $mycoupon->mincat, date("Y-m-d",$mycoupon->expdate), $mycoupon->brand);

			$coupon_parse = str_replace($coupon_token, $coupon_replace, $options["coupon_format"]);
			$output = htmlspecialchars_decode(stripslashes($coupon_parse));

		}
		return $output;
	}
}


class Coupon_Feed_Options {
	function Coupon_Feed_Options($feed) {
		$this->feed = $feed;
		$this->__construct();
	}

	function __construct() {
		add_action('admin_menu', array(&$this, 'admin_menu'));
	}

	function admin_menu() {
		// Main Admin Menu
		add_menu_page('Coupon Grab', 'Coupon Grab', 'manage_options', 'cfeed-general','',plugins_url('coupon-grab/images/admin/red_feed.png'));

		// Admin Submenus
		add_submenu_page('cfeed-general', 'General', 'General', 'manage_options', 'cfeed-general', array( $this, 'general' ));
		add_submenu_page('cfeed-general', 'HTML', 'HTML', 'manage_options', 'cfeed-html', array( $this, 'html' ));

	}

	function general() {
		global $the_feed;

		$message = '';
		if ($_POST['action'] == 'update') {
			$message .= '<div id="message" class="updated fade"><p><strong>Options saved</div>';
			if($_POST['coupon_interval'] != get_option('coupon_interval')) {

				if (wp_next_scheduled('feed_coupons')) {
					wp_clear_scheduled_hook('feed_coupons');
				}

				switch ($_POST['coupon_interval']) {
					case 1:
						wp_schedule_event( time() + 3600, 'hourly', 'feed_coupons' );
						break;
					case 2: 
						wp_schedule_event( time() + 86400, 'daily', 'feed_coupons' );
						break;
					case 3:
						wp_schedule_event( time() + 172800, 'twicedaily', 'feed_coupons' );
						break;
					case 4:
						wp_schedule_event( time() + 604800, 'weekly', 'feed_coupons' );
						break;
				}
			}

			$_POST['cgrab_thumbnail'] == 'on' ? update_option('cgrab_thumbnail', 'checked') : update_option('cgrab_thumbnail', '');

			update_option('coupon_interval', $_POST['coupon_interval']);
			update_option('coupon_maxpost', $_POST['coupon_maxpost']);
			update_option('coupon_status', $_POST['coupon_status']);
			update_option('coupon_author', $_POST['coupon_author']);
			update_option('coupon_cat', $_POST['coupon_cat']);
			update_option('coupon_tag_list', $_POST['coupon_tag_list']);
		}

		if($_POST['coupon_grab_now'] == 'on') {
			$the_feed->get_xml();
		}

		$options['coupon_interval'] = get_option('coupon_interval', '0'); // Defaults to NONE
		$options['coupon_maxpost'] = get_option('coupon_maxpost','1');
		$options['coupon_status'] = get_option('coupon_status', '0'); // Defaults to draft
		$options['coupon_author'] = get_option('coupon_author', '0'); // Defaults to Admin
		$options['coupon_cat'] = get_option('coupon_cat', '0'); // Defaults to NONE
		$options['coupon_tag_list'] = get_option('coupon_tag_list', 'Coupons, %majcat%, %mincat%, %brand%');
		$options['cgrab_thumbnail'] = get_option('cgrab_thumbnail', 'checked');

		$authors = get_users('blog_id=1&orderby=display_name&role=subscriber');

		if (wp_next_scheduled('feed_coupons')) {
			$timestamp = wp_next_scheduled( 'feed_coupons' );
			$time_now = time();
			$next_coupon_grab = wp_next_scheduled('feed_coupons');
			$time_period = ($timestamp - $time_now);
			$days = (int) ($time_period / 86400); $time_period = ($time_period % 86400);
			$hours = (int) ($time_period / 3600); $time_period = ($time_period % 3600);
			$minutes = (int) ($time_period / 60); $time_period = ($time_period % 60);
			$seconds = $time_period;

			$message .= '<div id="message" class="updated fade"><p><strong>Next Coupon Grab: '.$days.' days '.$hours. ' hours '. $minutes. ' minutes '. $seconds. ' seconds</strong></p></div>';
		}

		// General Options Header
		$output = '<div class="wrap">'. $message
		. '<div id="icon-options-general" class="icon32"><br /></div>'
		. '<h2>Coupon Grab General Options</h2>'

		. '<form method="post" action=""><input type="hidden" name="action" value="update" />&nbsp;'
		. '<table class="widefat"><thead><tr><th>Timing</th>'
		. '<th><div width="100%" align="right">Run NOW <input name="coupon_grab_now" type="checkbox" id="coupon_grab_now"/></div></th>'
		. '</tr></thead>'
		. '<tbody><tr><td>Time Interval</td><td>Maximum Posts Per Interval</td></tr><tr>'

		. '<tbody><tr>';


		// Interval for pulling coupons
		$output .= '<td><select name="coupon_interval">';

		$interval_type = array('none', 'hourly', 'daily', 'twicedaily', 'weekly');
		for ($loop = 0; $loop < 5; $loop++) {
			if($options["coupon_interval"] == $loop) {
				$output .= '<option value="'.$loop.'" SELECTED>'.$interval_type[$loop].'</option>';
			}
			else {
				$output .= '<option value="'.$loop.'">'.$interval_type[$loop].'</option>';
			}
		}
		$output .= '</select></td>';

		// Maximum number of coupons to post per interval
		$output .= '<td><input name="coupon_maxpost" type="text" id="coupon_maxpost" value="'.$options["coupon_maxpost"].'" size="2"/ maxlength="2"></td></tr>'

		// Second Header
		. '<thead><tr><th colspan="2">Coupon Meta</th>'
		. '</tr></thead>'
		. '<tbody><tr><td>Status for new coupons</td><td>Author for coupon posts</td></tr><tr>';

		// Coupon status defaults to draft
		$output .= '</td><td><select name="coupon_status">';

		$status_type = array('draft', 'publish', 'pending', 'private');
		for ($loop = 0; $loop < 4; $loop++) {
			if($options["coupon_status"] == $loop) {
				$output .= '<option value="'.$loop.'" SELECTED>'.$status_type[$loop].'</option>';
			}
			else {
				$output .= '<option value="'.$loop.'">'.$status_type[$loop].'</option>';
			}
		}
		$output .= '</select></td>';

		// Author of coupon posts
		$blogusers = get_users('blog_id=1&orderby=display_name');

		$output .= '<td><select name="coupon_author">';
		foreach ($blogusers as $user) {
			if(user_can($user->ID, "edit_posts")) {
				if($options["coupon_author"] == $user->ID) {
					$output .= '<option value="'.$user->ID.'" SELECTED>'.$user->display_name .'</option>';
				}
				else {
					$output .= '<option value="'.$user->ID.'">'.$user->display_name .'</option>';
				}
			}
		}
		$output .= '</select></td></tr><tr>'

		// Third Header
		. '<thead><tr><th colspan="2">Category and Tags</th>'
		. '</tr></thead>'
		. '<tbody><tr><td>Parent category</td><td>Tags to be added to coupons</td></tr><tr>';

		// Parent Category
		$output .= '<td><select name="coupon_cat">';
		if($options["coupon_cat"] == 0) {
			$output .= '<option value="0" SELECTED>NONE</option>';
		}
		else {
			$output .= '<option value="0">NONE</option>';
		}

		$category_ids = get_all_category_ids();
		foreach ($category_ids as $cat_id) {
			if($options["coupon_cat"] == $cat_id) {
				$output .= '<option value="'.$cat_id.'" SELECTED>'.get_cat_name($cat_id).'</option>';
			}
			else {
				$output .= '<option value="'.$cat_id.'">'.get_cat_name($cat_id).'</option>';
			}
		}
		$output .= '</select></td>';

		// Coupon Tags
		$output .= '<td><input name="coupon_tag_list" type="text" id="coupon_maxpost" value="'.$options["coupon_tag_list"].'"></td>';


		$output .= '</tr><tr>'
		. '<td colspan="2"><input name="cgrab_thumbnail" type="checkbox" id="cgrab_thumbnail" '.$options["cgrab_thumbnail"].' /> Enable featured image tagging</td>';
		
		$output .= '</tr></tbody></table><br />';

		// Directions
		$output .= '<b>For Tags</b>: <i>Enter comma delimeted list of tags (i.e. tag1,tag2,tag3,etc) variables: %brand, %majcat%, and %mincat%</i><br /><br />'
		. '<input type="submit" class="button-primary" value="Save Changes" /></form>';

		// Output
		echo $output;
	}

	function html() {
		$message = '';

		if ($_POST['action'] == 'update') {
			update_option('coupon_format', htmlspecialchars($_POST['coupon_format']), ENT_QUOTES);

			$message .= '<div id="message" class="updated fade"><p><strong>Options saved</div>';
		}

		$options['coupon_format'] = get_option('coupon_format', '<h4><b><font color="blue">%description%</font></b></h4>'
									.'<a href="%link%"><img src="%image%" border="0" align="left" style="padding:10px;"></a>'
									.'<a href="%link%">%brand% coupon</a><br><b>%majcat% %mincat%</b>'
									.'<br /><br /><br /><b>Exp: <font color="green">%exp%</font></b><br /><br />');

		// Static HTML Header
		$output = '<div class="wrap">'. $message
		. '<div id="icon-options-general" class="icon32"><br /></div>'
		. '<h2>Coupon Grab HTML Options</h2>'

		. '<form method="post" action=""><input type="hidden" name="action" value="update" />&nbsp;'
		. '<table class="widefat"><thead><tr><th colspan="2">HTML to use when displaying coupons</th></tr>'
		. '<tbody><tr>'

		// Coupon format
		. '<td><textarea name="coupon_format" cols="40" rows="10">'.htmlspecialchars_decode(stripslashes($options["coupon_format"]), ENT_QUOTES).'</textarea></td>'
		. '<td>You can insert variables into your post template by surrounding them with percent signs (%).<br /><br />'
		. '<b>%image%</b> - The URL to the coupon image, this needs to be embedded in a img tag.<br />'
		. '<b>%link%</b> - The URL to print the coupon, this needs to be embedded in an href tag.<br />'
		. '<b>%description%</b> - description<br />'
		. '<b>%majcat%</b> - The major category, i.e. Food<br />'
		. '<b>%mincat%</b> - The minor category, i.e. Butter/Margarine<br />'
		. '<b>%exp%</b> - The expiration date in YYYY-MM-DD format.<br />'
		. '<b>%brand%</b> - The brand, i.e. I Can.t Believe It.s Not Butter!<br />'
		. '</tr></tbody></table>'
		. '<br /><input type="submit" class="button-primary" value="Save Changes" /></form>';
		


		// Output
		echo $output;
	}
}

class wm_mypost {
	var $post_title;
	var $post_content;
	var $post_status;
	var $post_author;
	var $post_name;
	var $post_type;
	var $comment_status;
}
?>
