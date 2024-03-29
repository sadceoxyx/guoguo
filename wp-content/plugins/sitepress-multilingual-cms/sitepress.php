<?php
/*
Plugin Name: WPML Multilingual CMS
Plugin URI: http://wpml.org/
Description: WPML Multilingual CMS. <a href="http://wpml.org">Documentation</a>.
Author: OnTheGoSystems
Author URI: http://www.onthegosystems.com
Version: 1.7.0
*/

/*
    This file is part of ICanLocalize Translator.

    ICanLocalize Translator is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    ICanLocalize Translator is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with ICanLocalize Translator.  If not, see <http://www.gnu.org/licenses/>.
*/

if(defined('ICL_SITEPRESS_VERSION')) return;
define('ICL_SITEPRESS_VERSION', '1.7.0');
define('ICL_PLUGIN_PATH', dirname(__FILE__));
define('ICL_PLUGIN_FOLDER', basename(ICL_PLUGIN_PATH));

if(defined('WP_ADMIN') && defined('FORCE_SSL_ADMIN') && FORCE_SSL_ADMIN){
    define('ICL_PLUGIN_URL', rtrim(str_replace('http://','https://',get_option('siteurl')),'/') . '/'. PLUGINDIR . '/' . basename(dirname(__FILE__)) );
}else{
    define('ICL_PLUGIN_URL', rtrim(get_option('siteurl'),'/') . '/'. PLUGINDIR . '/' . basename(dirname(__FILE__)) );
}
if(defined('WP_ADMIN')){
    require ICL_PLUGIN_PATH . '/inc/php-version-check.php';
    if(defined('PHP_VERSION_INCOMPATIBLE')) return;
}
require ICL_PLUGIN_PATH . '/inc/not-compatible-plugins.php';
if(!empty($icl_ncp_plugins)){
    return;
}  

require ICL_PLUGIN_PATH . '/inc/constants.inc';
if(defined('ICL_DEBUG_DEVELOPMENT') && ICL_DEBUG_DEVELOPMENT){
    include ICL_PLUGIN_PATH . '/inc/hacks/debug-actions.php';
}     


require ICL_PLUGIN_PATH . '/inc/sitepress-schema.php';
require ICL_PLUGIN_PATH . '/inc/template-functions.php';
require ICL_PLUGIN_PATH . '/inc/icl-recent-comments-widget.php';
require ICL_PLUGIN_PATH . '/sitepress.class.php';
require ICL_PLUGIN_PATH . '/inc/functions.php';
require ICL_PLUGIN_PATH . '/inc/hacks.php';
require ICL_PLUGIN_PATH . '/inc/upgrade.php';
require ICL_PLUGIN_PATH . '/inc/functions-string-translation.php';
require ICL_PLUGIN_PATH . '/inc/compatibility-packages/functions-packages.php';
require ICL_PLUGIN_PATH . '/inc/compatibility-packages/wpml-package.class.php';
require ICL_PLUGIN_PATH . '/inc/affiliate-info.php';
require ICL_PLUGIN_PATH . '/inc/language-switcher.php';


if( !isset($_REQUEST['action'])     || ($_REQUEST['action']!='activate' && $_REQUEST['action']!='activate-selected') 
    || (($_REQUEST['plugin'] != basename(ICL_PLUGIN_PATH).'/'.basename(__FILE__)) 
        && !in_array(basename(ICL_PLUGIN_PATH).'/'.basename(__FILE__), (array)$_REQUEST['checked']))){
        
    $sitepress = new SitePress();
    $sitepress_settings = $sitepress->get_settings();    
    
    // modules load
    // CMS Navigation
    if(isset($_GET['enable-cms-navigation'])){
        $sitepress_settings['modules']['cms-navigation']['enabled'] = intval($_GET['enable-cms-navigation']);
        $sitepress->save_settings($sitepress_settings);
    }    
    if($sitepress_settings['modules']['cms-navigation']['enabled']){
        require ICL_PLUGIN_PATH . '/modules/cms-navigation/cms-navigation.php';
        $iclCMSNavigation = new CMSNavigation();
    }
    
    // Sticky Links
    if(isset($_REQUEST['icl_enable_alp'])){
        $sitepress_settings['modules']['absolute-links']['enabled'] = intval($_REQUEST['icl_enable_alp']);
        $sitepress->save_settings($sitepress_settings);
    }
    if($sitepress_settings['modules']['absolute-links']['enabled']){
        require ICL_PLUGIN_PATH . '/modules/absolute-links/absolute-links-plugin.php';
        $iclAbsoluteLinks = new AbsoluteLinksPlugin();
    }

    // Professional Translation    
    require ICL_PLUGIN_PATH . '/modules/icl-translation/icl-translation.php';
    
    // Comments translation
    if($sitepress_settings['existing_content_language_verified']){
        require ICL_PLUGIN_PATH . '/inc/comments-translation/functions.php';    
    }
    
    require ICL_PLUGIN_PATH . '/inc/compatibility-packages/init-packages.php';
    
    //icl_st_scan_plugin_files(WP_PLUGIN_DIR . '/sitepress-multilingual-cms');
    
}

 
// activation hook
register_activation_hook( __FILE__, 'icl_sitepress_activate' );
register_deactivation_hook(__FILE__, 'icl_sitepress_deactivate');

add_filter('plugin_action_links', 'icl_plugin_action_links', 10, 2); 


?>