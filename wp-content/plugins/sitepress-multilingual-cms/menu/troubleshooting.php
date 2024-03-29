<?php 
$icl_tables = array(
    $wpdb->prefix . 'icl_languages',
    $wpdb->prefix . 'icl_languages_translations',
    $wpdb->prefix . 'icl_translations',
    $wpdb->prefix . 'icl_locale_map',
    $wpdb->prefix . 'icl_flags',
    $wpdb->prefix . 'icl_content_status',
    $wpdb->prefix . 'icl_core_status',
    $wpdb->prefix . 'icl_node',
    $wpdb->prefix . 'icl_plugins_texts',
    $wpdb->prefix . 'icl_strings',
    $wpdb->prefix . 'icl_string_translations',
    $wpdb->prefix . 'icl_string_status',
    $wpdb->prefix . 'icl_string_positions',
    $wpdb->prefix . 'icl_cms_nav_cache',
    $wpdb->prefix . 'icl_message_status',
    $wpdb->prefix . 'icl_reminders',    
);

if( (isset($_POST['icl_reset_allnonce']) && $_POST['icl_reset_allnonce']==wp_create_nonce('icl_reset_all'))){
    if($_POST['icl-reset-all']=='on'){
        foreach($icl_tables as $icl_table){
            mysql_query("DROP TABLE " . $icl_table);
        }
        delete_option('icl_sitepress_settings');
        delete_option('icl_sitepress_version');
        delete_option('_icl_cache');
        delete_option('WPLANG');                
        deactivate_plugins(basename(ICL_PLUGIN_PATH) . '/sitepress.php');
        $ra = get_option('recently_activated');
        $ra[basename(ICL_PLUGIN_PATH) . '/sitepress.php'] = time();
        update_option('recently_activated', $ra);        
        echo '<script type="text/javascript">location.href=\''.admin_url('plugins.php?deactivate=true').'\'</script>';
    }
}
?>
<div class="wrap">
    <div id="icon-options-general" class="icon32 icon32_adv" style="background: transparent url(<?php echo ICL_PLUGIN_URL; ?>/res/img/icon_adv.png) no-repeat;"><br /></div>
    <h2><?php echo __('Troubleshooting', 'sitepress') ?></h2>    
    
    <?php include ICL_PLUGIN_PATH . '/menu/basic_advanced_switch.php' ?>
    
    <?php
    foreach($icl_tables as $icl_table){
        echo '<a href="#'.$icl_table.'_anch">'.$icl_table.'</a> | ';
    }
    echo '<a href="#wpml-settings">'.__('WPML Settings', 'sitepress').'</a>';
    
    foreach($icl_tables as $icl_table){
        echo '<h3  id="'.$icl_table.'_anch" onclick="jQuery(\'#'.$icl_table.'\').toggle(); jQuery(\'#'.$icl_table.'_arrow_up\').toggle(); jQuery(\'#'.$icl_table.'_arrow_dn\').toggle();" style="cursor:pointer">'.$icl_table.'&nbsp;&nbsp;<span id="'.$icl_table.'_arrow_up" style="display:none">&uarr;</span><span id="'.$icl_table.'_arrow_dn">&darr;</span></h3>';        
        if(strtolower($wpdb->get_var("SHOW TABLES LIKE '{$icl_table}'")) != strtolower($icl_table)){
            echo '<p class="error">'.__('Not found!', 'sitepress').'</p>';
        }else{
            $results = $wpdb->get_results("DESCRIBE {$icl_table}", ARRAY_A);
            $keys = array_keys($results[0]);
            ?>
            <table class="widefat">
                <thead>
                    <tr>
                    <?php foreach($keys as $k): ?><th width="<?php echo floor(100/count($keys)) ?>%"><?php echo $k ?></th><?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($results as $r):?>
                    <tr>
                        <?php foreach($keys as $k): ?><td><?php echo $r[$k] ?></td><?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            <tbody>
            </table>
            <?php
            echo '<span id="'.$icl_table.'" style="display:none">';    
            $results = $wpdb->get_results("SELECT * FROM {$icl_table}", ARRAY_A);
            echo '<textarea style="font-size:10px;width:100%" wrap="off" rows="8">';
            $inc = 0;
            foreach((array)$results as $res){
                if($inc==0){
                    $columns = array_keys($res);
                    $columns = array_map('__custom_csv_escape', $columns);
                    echo implode(",", $columns) . PHP_EOL;;
                }
                $inc++;
                $res = array_map('__custom_csv_escape', $res);
                echo implode(",", $res) . PHP_EOL;
            }
            echo '</textarea>';
            echo '</span>';        
        }        
        
    }
    
    function __custom_csv_escape($s){
        $s = "&#34;". str_replace('"','&#34;',addslashes($s)) . "&#34;";
        return $s;
    }
    echo '<br /><hr /><h3 id="wpml-settings"> ' . __('WPML settings', 'sitepress') . '</h3>';
    echo '<textarea style="font-size:10px;width:100%" wrap="off" rows="16">';
    ob_start();
    print_r($sitepress->get_settings());
    $ob = ob_get_contents();
    ob_end_clean();
    echo htmlspecialchars($ob);
    echo '</textarea>';
    
    
    echo '<br /><hr /><h3 id="wpml-settings"> ' . __('Reset', 'sitepress') . '</h3>';
    echo '<form method="post" onsubmit="return confirm(\''.__('Are you sure you want to reset all languages data? This operation cannot be reversed.', 'sitepress').'\')">';
    wp_nonce_field('icl_reset_all','icl_reset_allnonce');
    echo '<label><input type="checkbox" name="icl-reset-all" onchange="if(this.checked) jQuery(\'#reset-all-but\').removeAttr(\'disabled\'); else  jQuery(\'#reset-all-but\').attr(\'disabled\',\'disabled\');" /> ' . __('I am about to reset all language data.', 'sitepress') . '</label><br /><br />';
    echo '<input id="reset-all-but" type="submit" disabled="disabled" class="button-primary" value="'.__('Reset all language data and deactivate WPML', 'sitepress').'" />';    
    echo '</form>';
    
    
    
    ?>
    <?php do_action('icl_menu_footer'); ?>
</div>

