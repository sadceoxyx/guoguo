<?php
/**
 * WP Deals Install
 * 
 * Plugin install script which adds default pages, taxonomies, and database tables
 *
 * @package	WP Deals
 * @category	Admin
 * @author	Onnay Okheng
 */

/**
 * Activate WP Deals
 */
function activate_deals() {
	
	install_deals();
	
	// Update installed variable
	update_option( "deals_installed", 1 );
}

/**
 * Install deals
 */
function install_deals() {
	global $deals_settings, $deals;
	
	// Do install
        if(get_option('deals_installed') != 1)
            deals_default_options();
	
	// Update version
	update_option( "deals_db_version", DEALS_VERSION );
}

/**
 * Install deals redirect
 */
add_action('admin_init', 'install_deals_redirect');
function install_deals_redirect() {
	global $pagenow;

	if ( is_admin() && isset( $_GET['activate'] ) && ($_GET['activate'] == true) && $pagenow == 'plugins.php' && get_option( "deals_installed" ) == 1 ) :
				
		// Unset installed flag
		update_option( "deals_installed", 0 );
		
		// Flush rewrites
		flush_rewrite_rules( false );
		
		// Redirect to settings
		wp_redirect(admin_url('admin.php?page=wpdeals&installed=true'));
		exit;
		
	endif;
	
	/*
	 Check for tmps & invoices directories
	 */
	if(!deals_check_dir(WP_CONTENT_DIR.'/wpdeals')) {
		
		add_action('admin_notices','deals_notice_dir_error');
		deals_create_dir(WP_CONTENT_DIR.'/wpdeals/tmps',0777);
		deals_create_dir(WP_CONTENT_DIR.'/wpdeals/invoices',0777);						
		
	}	
	
}

function deals_notice_dir_error() {
	
	$wpdeals_dir_tmps = WP_CONTENT_DIR.'/wpdeals/tmps';
	$wpdeals_dir_invs = WP_CONTENT_DIR.'/wpdeals/invoices';
	
	echo '<div class="error">
       <p class="error">Please make sure '.$wpdeals_dir_tmps.' and '.$wpdeals_dir_invs.' is exists and writable.</p>
    </div>';
	
}

/**
 * Default options
 * 
 * Sets up the default options used on the settings page
 */
function deals_default_options() {
	global $deals_settings;
	
	// Include settings so that we can run through defaults
	include_once( 'admin-settings.php' );
	
	foreach ($deals_settings as $section) :
	
		foreach ($section as $value) :
	
	        if (isset($value['std'])) :
	        
	        	if ($value['type']=='image_width') :
	        		
	        		add_option($value['id'].'_width', $value['std']);
	        		add_option($value['id'].'_height', $value['std']);
	        		
	        	else :
	        		
	        		add_option($value['id'], $value['std']);
	        	
	        	endif;
	        	
	        endif;
        
        endforeach;
        
    endforeach;
    
}
