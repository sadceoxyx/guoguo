<?php 
/*
Plugin Name: Absolute Links Plugin
Plugin URI: http://wpml.org/wordpress-cms-plugins/absolute-links-plugin/
Description: Doesn't just fix links. Makes sure they can never break. <a href="options-general.php?page=alp">Configure &raquo;</a>.
Author: ICanLocalize
Author URI: http://wpml.org
Version: 1.1.1
*/

/*
    This file is part of Absolute Links.

    Absolute Links is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Absolute Links is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Absolute Links.  If not, see <http://www.gnu.org/licenses/>.
*/

class AbsoluteLinksPlugin{
    var $settings;
    var $cur_ver;
    var $broken_links;
    var $plugin_url = '';
    
    
    function __construct(){  
        $this->settings = get_option('alp_settings');
        //add_action('admin_menu',array($this,'management_page'));
        if(isset($_POST['save_alp']) && $_POST['save_alp']){            
            add_action('init', array($this,'save_settings'));           
        }
        add_action('save_post', array($this,'save_default_urls'));
        add_action('init', array($this,'ajax_responses'));
        add_action('admin_head',array($this,'js_scripts'));  
        
        add_filter('the_content', array($this,'show_permalinks'));
        
        $path = dirname(substr(__FILE__, strpos(__FILE__,'wp-content')));
        $path = str_replace('\\','/',$path);
        $this->plugin_url = rtrim(get_option('siteurl'),'/') .'/' . $path;

        if(0 === strpos(ICL_PLUGIN_URL,'https://')){
            $this->plugin_url = str_replace('http://', 'https://', $this->plugin_url);
        }        
        
        
    }
    
    function __destruct(){
        return;
    }
    
    /* MAKE IT PHP 4 COMPATIBLE */
    function AbsoluteLinksPlugin(){
     //destructor
     register_shutdown_function(array(&$this, '__destruct'));

     //constructor
     $argcv = func_get_args();
     call_user_func_array(array(&$this, '__construct'), $argcv);
    }    
    
    
    function save_settings(){
        $nonce = wp_create_nonce('absolute-links-plugin');
        if($nonce != $_POST['_wpnonce']) return;        
    }
    
    function ajax_responses(){  
        if(!isset($_POST['alp_ajx_action'])){
            return;
        }
        global $wpdb;
        
        $limit  = 5;
        
        switch($_POST['alp_ajx_action']){
            case 'rescan':
                $posts_pages = $wpdb->get_col("
                    SELECT SQL_CALC_FOUND_ROWS p1.ID FROM {$wpdb->posts} p1 WHERE post_type IN ('page','post') AND ID NOT IN 
                    (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_alp_processed')
                    ORDER BY p1.ID ASC LIMIT $limit
                ");
                if($posts_pages){
                    $found = $wpdb->get_var("SELECT FOUND_ROWS()");                
                    foreach($posts_pages as $ppid){
                        $this->process_post($ppid);
                    }
                    echo $found >= $limit ? $found - $limit : 0;
                }else{
                    echo -1;
                }                
                break;
            case 'rescan_reset':
                $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key='_alp_processed'");
                echo mysql_affected_rows();
                break;
            case 'use_suggestion':
                $broken_links = get_post_meta($_POST['post_id'],'_alp_broken_links', true);
                foreach($broken_links as $k=>$bl){
                    if($k==$_POST['orig_url']){
                        $broken = $k;
                        $repl = $bl['suggestions'][$_POST['sug_id']]['absolute'];
                        unset($broken_links[$k]);
                        $c = count($broken_links);
                        if($c){
                            update_post_meta($_POST['post_id'],'_alp_broken_links', $broken_links);
                        }else{
                            delete_post_meta($_POST['post_id'],'_alp_broken_links');
                        }
                        echo $c.'|'.$bl['suggestions'][$_POST['sug_id']]['perma'];
                        break;
                    }
                }
                $post_content = $wpdb->get_var("SELECT post_content FROM {$wpdb->posts} WHERE ID={$_POST['post_id']}");
                $post_content = preg_replace('@href="('.$broken.')"@i', 'href="'.$repl.'"', $post_content);
                $wpdb->update($wpdb->posts, array('post_content'=>$post_content), array('ID'=>$_POST['post_id']));
                break;
            case 'alp_revert_urls':
                $posts_pages = $wpdb->get_results("
                    SELECT SQL_CALC_FOUND_ROWS p1.ID, p1.post_content FROM {$wpdb->posts} p1
                    JOIN {$wpdb->postmeta} p2 ON p1.ID = p2.post_id
                    WHERE p1.post_type IN ('page','post') AND p2.meta_key = '_alp_processed'
                    ORDER BY p1.ID ASC LIMIT $limit
                ");   
                if($posts_pages){
                    $found = $wpdb->get_var("SELECT FOUND_ROWS()");                
                    foreach($posts_pages as $p){
                        $cont = $this->show_permalinks($p->post_content);
                        $wpdb->update($wpdb->posts, array('post_content'=>$cont), array('ID'=>$p->ID));                        
                        delete_post_meta($p->ID,'_alp_processed');
                        delete_post_meta($p->ID,'_alp_broken_links');
                    }
                    echo $found >= $limit ? $found - $limit : 0;
                }else{
                    echo -1;
                }                                    
                break;
        }
        exit;
    }    
    
    function js_scripts(){
        ?>
        <script type="text/javascript">
            addLoadEvent(function(){                     
                jQuery('#alp_re_scan_but').click(alp_toogle_scan);                
                jQuery('#alp_re_scan_but_all').click(alp_reset_scan_flags);
                jQuery('.alp_use_sug').click(alp_use_suggestion);
                jQuery('#alp_revert_urls').click(alp_do_revert_urls);
                
            });
            var alp_scan_started = false;
            var req_timer = 0;
            function alp_toogle_scan(){                       
                if(!alp_scan_started){  
                    alp_send_request(0); 
                    jQuery('#alp_ajx_ldr_1').fadeIn();
                    jQuery('#alp_re_scan_but').attr('value','<?php echo __('Running', 'sitepress') ?>');    
                }else{
                    jQuery('#alp_re_scan_but').attr('value','<?php echo __('Scan', 'sitepress') ?>');    
                    window.clearTimeout(req_timer);
                    jQuery('#alp_ajx_ldr_1').fadeOut();
                    location.reload();
                }
                alp_scan_started = !alp_scan_started;
                return false;
            }
            
            function alp_send_request(offset){
                jQuery.ajax({
                    type: "POST",
                    url: "<?php echo htmlentities($_SERVER['REQUEST_URI']) ?>",
                    data: "alp_ajx_action=rescan&amp;offset="+offset,
                    success: function(msg){                        
                        if(-1==msg || msg==0){
                            left = '0';
                            alp_toogle_scan();
                        }else{
                            left=msg;
                        }
                        
                        if(left=='0'){
                            jQuery('#alp_re_scan_but').attr('disabled','disabled');    
                        }
                        
                        jQuery('#alp_re_scan_toscan').html(left);
                        if(alp_scan_started){
                            req_timer = window.setTimeout(alp_send_request,3000,offset);
                        }
                    }                                                            
                });
            }
            
            function alp_reset_scan_flags(){
                if(alp_scan_started) return;
                alp_scan_started = false;
                jQuery('#alp_re_scan_but').removeAttr('disabled');    
                jQuery.ajax({
                    type: "POST",
                    url: "<?php echo htmlentities($_SERVER['REQUEST_URI']) ?>",
                    data: "alp_ajx_action=rescan_reset",
                    success: function(msg){    
                        if(msg){
                            alp_toogle_scan()
                        }
                    }                                                            
                });
            }
            function alp_use_suggestion(){
                jqthis = jQuery(this);
                jqthis.parent().parent().css('background-color','#eee');                
                spl = jqthis.attr('id').split('_');
                sug_id = spl[3];
                post_id = spl[4];
                orig_url = jQuery('#alp_bl_'+spl[5]).html();
                jQuery.ajax({
                    type: "POST",
                    url: "<?php echo htmlentities($_SERVER['REQUEST_URI']) ?>",
                    data: "alp_ajx_action=use_suggestion&amp;sug_id="+sug_id+"&amp;post_id="+post_id+"&amp;orig_url="+orig_url,
                    success: function(msg){                                                    
                        spl = msg.split('|');
                        jqthis.parent().html('<?php echo __('fixed', 'sitepress')?> - ' + spl[1]);
                    },
                    error: function (msg){
                        alert('Something went wrong');
                        jqthis.parent().parent().css('background-color','#fff');
                    }                                                            
                });
                                
            }
            
            var req_rev_timer = '';
            function alp_do_revert_urls(){
                jQuery('#alp_revert_urls').attr('disabled','disabled');
                jQuery('#alp_revert_urls').attr('value','<?php echo __('Running', 'sitepress') ?>');
                jQuery.ajax({
                    type: "POST",
                    url: "<?php echo htmlentities($_SERVER['REQUEST_URI']) ?>",
                    data: "alp_ajx_action=alp_revert_urls",
                    success: function(msg){                                                    
                        if(-1==msg || msg==0){
                            jQuery('#alp_ajx_ldr_2').fadeOut();
                            jQuery('#alp_rev_items_left').html('');
                            window.clearTimeout(req_rev_timer);
                            jQuery('#alp_revert_urls').removeAttr('disabled');                            
                            jQuery('#alp_revert_urls').attr('value','<?php echo __('Start', 'sitepress')?>');                            
                            location.reload();
                        }else{
                            jQuery('#alp_rev_items_left').html(msg + ' <?php echo __('items left', 'sitepress')?>');
                            req_rev_timer = window.setTimeout(alp_do_revert_urls,3000);
                            jQuery('#alp_ajx_ldr_2').fadeIn();
                        }                            
                    },
                    error: function (msg){
                        //alert('Something went wrong');
                    }                                                            
                });
            }
            
        </script>
        <?php
    }
    
    function management_page(){
        add_options_page(__('Absolute Links','sitepress'),__('Absolute Links', 'sitepress'),'10', 'alp' , array($this,'management_page_content'));
    }
    
    function management_page_content(){
        global $wpdb;
        $this->get_broken_links();
        $total_posts_pages = $wpdb->get_var("
            SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type IN ('page','post') AND ID NOT IN 
            (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_alp_processed')
        ");
        
        $total_posts_pages_processed = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key = '_alp_processed'");   
        
        include dirname(__FILE__).'/management-page.php';
    }
    
    function save_default_urls($post_id){
       $this->process_post($post_id);
    }
    
    function process_post($post_id){
        global $wpdb;
        global $wp_rewrite, $sitepress;
        if(!isset($wp_rewrite)){
            require_once ABSPATH . WPINC . '/rewrite.php'; 
            $wp_rewrite = new WP_Rewrite();
        }
        
        $rewrite = $wp_rewrite->wp_rewrite_rules();
        
        delete_post_meta($post_id,'_alp_broken_links');
         
        $post = $wpdb->get_row("SELECT * FROM {$wpdb->posts} WHERE ID={$post_id}"); 
        $home_url = $sitepress->language_url($_POST['icl_post_language']);
        $int = preg_match_all('#<a([^>]*)href="('.rtrim($home_url,'/').'/([^"^>]+))"([^>]*)>#i',$post->post_content,$alp_matches);        
        $sitepress_settings = $sitepress->get_settings();
        
        if($int){   
            $url_parts = parse_url(rtrim(get_option('home'),'/').'/');                                                    
            foreach($alp_matches[3] as $k=>$m){
                if(0===strpos($m,'wp-content')) continue;
                
                if($sitepress_settings['language_negotiation_type']==1){
                        $m_orig = $m;
                        $exp = explode('/', $m, 2);                
                        $lang = $exp[0];
                        if($wpdb->get_var("SELECT code FROM {$wpdb->prefix}icl_languages WHERE code='{$lang}'")){
                            $m = $exp[1];    
                        }else{
                            $m = $m_orig;
                            unset($m_orig);
                            $lang = false;
                        }                        
                }elseif($sitepress_settings['language_negotiation_type']==2){
                    //
                }

                
                $pathinfo = '';
                $req_uri = '/' . $m;                                
                $req_uri_array = explode('?', $req_uri);
                $req_uri = $req_uri_array[0];
                // separate anchor
                $req_uri_array = explode('#', $req_uri);
                $req_uri = $req_uri_array[0];
                $anchor = $req_uri_array[1];
                $self = '/index.php';
                $home_path = parse_url(get_option('home'));
                if ( isset($home_path['path']) )
                    $home_path = $home_path['path'];
                else
                    $home_path = '';
                $home_path = trim($home_path, '/');
                
                $req_uri = str_replace($pathinfo, '', rawurldecode($req_uri));
                $req_uri = trim($req_uri, '/');
                $req_uri = preg_replace("|^$home_path|", '', $req_uri);
                $req_uri = trim($req_uri, '/');
                $pathinfo = trim($pathinfo, '/');
                $pathinfo = preg_replace("|^$home_path|", '', $pathinfo);
                $pathinfo = trim($pathinfo, '/');
                $self = trim($self, '/');
                $self = preg_replace("|^$home_path|", '', $self);
                $self = trim($self, '/');
                
                if ( ! empty($pathinfo) && !preg_match('|^.*' . $wp_rewrite->index . '$|', $pathinfo) ) {
                    $request = $pathinfo;
                } else {
                    // If the request uri is the index, blank it out so that we don't try to match it against a rule.
                    if ( $req_uri == $wp_rewrite->index )
                        $req_uri = '';
                    $request = $req_uri;
                }

                $this_request = $request;
                
                $request_match = $request;
                
                foreach ( (array) $rewrite as $match => $query) {

                    // If the requesting file is the anchor of the match, prepend it
                    // to the path info.
                    if ((! empty($req_uri)) && (strpos($match, $req_uri) === 0) && ($req_uri != $request)) {
                        $request_match = $req_uri . '/' . $request;
                    }
                    
                    if (preg_match("!^$match!", $request_match, $matches) ||
                        preg_match("!^$match!", urldecode($request_match), $matches)) {
                        // Got a match.
                        $matched_rule = $match;

                        // Trim the query of everything up to the '?'.
                        $query = preg_replace("!^.+\?!", '', $query);
                        
                        // Substitute the substring matches into the query.
                        eval("@\$query = \"" . addslashes($query) . "\";");

                        $matched_query = $query;

                        // Parse the query.
                        parse_str($query, $perma_query_vars);
                        
                        break;
                    }
                }                
                $post_name = $category_name = false;
                if(isset($perma_query_vars['pagename'])){
                    $post_name = basename($perma_query_vars['pagename']); 
                }elseif(isset($perma_query_vars['name'])){
                    $post_name = $perma_query_vars['name']; 
                }elseif(isset($perma_query_vars['category_name'])){
                    $category_name = $perma_query_vars['category_name']; 
                }
                if($post_name){                    
                    $name = $wpdb->escape($post_name);
                    $p = $wpdb->get_row("SELECT ID, post_type FROM {$wpdb->posts} WHERE post_name='{$name}' AND post_type IN('post','page')");
                    if($p){
                        if($p->post_type=='post'){
                            $qvid = 'p';
                        }else{
                            $qvid = 'page_id';
                        }
                        
                        if($sitepress_settings['language_negotiation_type']==1 && $lang){
                            $langprefix = '/' . $lang;
                        }else{
                            $langprefix = '';
                        }
                        $perm_url = rtrim($home_url,'/'). $langprefix .'/'.$m;
                        $regk = '@href="('.$perm_url.')"@i'; 
                        if ($anchor){
                            $anchor = "#".$anchor;
                        } else {
                            $anchor = "";
                        }
                        // check if this is an offsite url
                        if($p->post_type=='page' && $offsite_url = get_post_meta($p->ID, '_cms_nav_offsite_url', true)){
                            $regv = 'href="'.$offsite_url.$anchor.'"';
                        }else{
                            $regv = 'href="' . '/' . ltrim($url_parts['path'],'/') . '?' . $qvid . '=' . $p->ID.$anchor.'"';
                        }
                        $def_url[$regk] = $regv;
                    }else{ 
                        $alp_broken_links[$alp_matches[2][$k]] = array();                            
                        $p = $wpdb->get_results("SELECT ID, post_type FROM {$wpdb->posts} WHERE post_name LIKE '{$name}%' AND post_type IN('post','page')");
                        if($p){
                            foreach($p as $post_suggestion){
                                if($post_suggestion->post_type=='post'){
                                    $qvid = 'p';
                                }else{
                                    $qvid = 'page_id';
                                }
                                $alp_broken_links[$alp_matches[2][$k]]['suggestions'][] = array(
                                        'absolute'=> '/' . ltrim($url_parts['path'],'/') . '?' . $qvid . '=' . $post_suggestion->ID,
                                        'perma'=> '/'. ltrim(str_replace(get_option('home'),'',get_permalink($post_suggestion->ID)),'/'),
                                        );
                            }
                        }                        
                    }
                }elseif($category_name){
                    $name = $wpdb->escape($category_name);                    
                    $c = $wpdb->get_row("SELECT term_id FROM {$wpdb->terms} WHERE slug='{$name}'");                    
                    if($c){
                        $perm_url = rtrim(get_option('home'),'/').'/'.$m;
                        $regk = '@href="('.$perm_url.')"@i';
                        $url_parts = parse_url(rtrim(get_option('home'),'/').'/');
                        $regv = 'href="' . '/' . ltrim($url_parts['path'],'/') . '?cat_ID=' . $c->term_id.'"';
                        $def_url[$regk] = $regv;                        
                    }else{
                        $alp_broken_links[$alp_matches[2][$k]] = array();                             
                        $c = $wpdb->get_results("SELECT term_id FROM {$wpdb->terms} WHERE slug LIKE '{$name}%'");                        
                        if($c){
                            foreach($c as $cat_suggestion){
                                $alp_broken_links[$alp_matches[2][$k]]['suggestions'][] = array(
                                        'absolute'=>'?cat_ID=' . $cat_suggestion->term_id,
                                        'perma'=> '/'. ltrim(str_replace(get_option('home'),'',get_category_link($cat_suggestion->term_id)),'/')
                                        );
                            }
                        }                        
                    }                        
                }
            }
            $post_content = $post->post_content;
            
            if($def_url){
                $post_content = preg_replace(array_keys($def_url),array_values($def_url),$post_content);
                
            }
            
            $int = preg_match_all('@href="('.rtrim(get_option('home'),'/').'/?\?(p|page_id)=([0-9]+))"@i',$post_content,$matches2);            
            if($int){
                $url_parts = parse_url(rtrim(get_option('home'),'/').'/');
                $post_content = preg_replace('@href="('. rtrim(get_option('home'),'/') .'/?\?(p|page_id)=([0-9]+))"@i', 'href="'.'/' . ltrim($url_parts['path'],'/').'?$2=$3"', $post_content);
            }
            
            if($post_content){
                $wpdb->update($wpdb->posts, array('post_content'=>$post_content), array('ID'=>$post_id));
            }
            
        }                
        update_post_meta($post_id,'_alp_processed',time());        
        if($alp_broken_links){
            update_post_meta($post_id,'_alp_broken_links',$alp_broken_links);                    
        }
    }
    
    function show_permalinks($cont){
        $home = rtrim(get_option('home'),'/');        
        $parts = parse_url($home);        
        $abshome = $parts['scheme'] .'://' . $parts['host'];
        $path = ltrim($parts['path'],'/');    
        $cont = preg_replace_callback('@<a([^>]+)?href="(('.$abshome.')?/'.$path.'/?\?(p|page_id|cat_ID)=([0-9]+))(#?[^"]*)"([^>]+)?>@i',
            array($this,'show_permalinks_cb'),$cont);            
        return $cont;
    }
       
    function show_permalinks_cb($matches){
        if($matches[4]=='cat_ID'){
            $_func = 'get_category_link';
        }else{
            $_func = 'get_permalink';
        }  
        $fragment = $matches[6];
        return '<a'.$matches[1]. 'href="'.$_func($matches[5]) . $fragment . '"' . $matches[3] . '>';
    }
    
    function get_broken_links(){
        global $wpdb;
        $this->broken_links = $wpdb->get_results("SELECT p2.ID, p2.post_title, p1.meta_value AS links
            FROM {$wpdb->postmeta} p1 JOIN {$wpdb->posts} p2 ON p1.post_id=p2.ID WHERE p1.meta_key='_alp_broken_links'");
    }
}
?>