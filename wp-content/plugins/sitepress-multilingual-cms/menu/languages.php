<?php
    if(!is_plugin_active(basename(dirname(dirname(__FILE__))) . "/sitepress.php")){
        ?>
        <h2><?php echo __('Setup WPML', 'sitepress') ?></h2>
        <div class="updated fade">
        <p style="line-height:1.5"><?php echo __('The WPML Multilingual CMS plugin is not currently enabled.', 'sitepress');?></p>
        <p style="line-height:1.5"><?php printf(__('Please go to the <a href="%s">Plugins</a> page and enable the WPML Multilingual CMS plugin before trying to configure the plugin.', 'sitepress'), 'plugins.php');?></p>
        </div>
        <?php
        return;
    }
    if(!$sitepress_settings['existing_content_language_verified']){
        // try to determine the blog language
        $blog_current_lang = 0;            
        if($blog_lang = get_option('WPLANG')){
            $exp = explode('_',$blog_lang);
            $blog_current_lang = $wpdb->get_var("SELECT code FROM {$wpdb->prefix}icl_languages WHERE code='{$exp[0]}'");
        }
        if(!$blog_current_lang && defined('WPLANG') && WPLANG != ''){
            $blog_lang = WPLANG;
            $exp = explode('_',$blog_lang);
            $blog_current_lang = $wpdb->get_var("SELECT code FROM {$wpdb->prefix}icl_languages WHERE code='{$exp[0]}'");        
        }
        if(!$blog_current_lang){
            $blog_current_lang = 'en';
        }        
        $languages = $sitepress->get_languages($blog_current_lang);            
    }else{
        $active_languages = $sitepress->get_active_languages();            
        $languages = $sitepress->get_languages($sitepress->get_admin_language());            
        $sitepress_settings = $sitepress->get_settings();
        foreach($active_languages as $lang){
            if($lang['code'] != $sitepress->get_default_language()){
                $sample_lang = $lang;
                break;
            }
        }
        $default_language = $sitepress->get_language_details($sitepress->get_default_language());        
        $inactive_content = $sitepress->get_inactive_content();        
    }  
    
global $language_switcher_defaults, $language_switcher_defaults_alt;      
?>
<?php $sitepress->noscript_notice() ?>
<div class="wrap">
    <div id="icon-options-general" class="icon32<?php if(!$sitepress_settings['basic_menu']) echo ' icon32_adv'?>" ><br /></div>
    <h2><?php echo __('Setup WPML', 'sitepress') ?></h2>    
    
    <?php 
        if($sitepress_settings['setup_complete']){
            include ICL_PLUGIN_PATH . '/menu/basic_advanced_switch.php'; 
        }        
    ?>
    
    <?php if(!$sitepress_settings['setup_complete']): /* setup wizard */ ?>
    <?php 
        if(!$sitepress_settings['existing_content_language_verified']){
            $sw_width = 16;
        }elseif(count($sitepress->get_active_languages()) < 2 || $sitepress_settings['setup_wizard_step'] == 2){
            $sw_width = 50;
        }else{
            $sw_width = 84;
        }
    ?>
    <div id="icl_setup_wizard_wrap">
        <h3><?php _e('Before you can start using WPML, it needs to be set up', 'sitepress') ?></h3>
        <div id="icl_setup_wizard">
            <div class="icl_setup_wizard_step"><strong><?php _e('1. Language for existing contents', 'sitepress')?></strong></div>
            <div class="icl_setup_wizard_step"><strong><?php _e('2. Select languages', 'sitepress')?></strong></div>
            <div class="icl_setup_wizard_step"><strong><?php _e('3. Add a language switcher', 'sitepress')?></strong></div>            
        </div>        
        <br clear="all" />
        <div id="icl_setup_wizard_progress"><div id="icl_setup_wizard_progress_bar" style="width:<?php echo $sw_width ?>%">&nbsp;</div></div>
    </div>
    <br />
    <?php endif; /* setup wizard */ ?>
    
    <?php if(!$sitepress_settings['existing_content_language_verified']): ?>        
        <form id="icl_initial_language" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
        <table class="widefat">
            <thead>
                <tr>
                    <th><?php echo __('Current content language', 'sitepress') ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>                    
                        <?php wp_nonce_field('icl_initial_language','icl_initial_languagenonce') ?>            
                        <p>
                            <?php echo __('Before adding other languages, please select the language existing contents are written in:', 'sitepress') ?><br /><br />
                            <select name="icl_initial_language_code">
                            <?php 
                                // for the wizard
                                if($sitepress->get_default_language()){
                                    $blog_current_lang = $sitepress->get_default_language();
                                }
                            ?>
                            <?php foreach($languages as $lang):?>
                            <option <?php if($blog_current_lang==$lang['code']):?>selected="selected"<?php endif;?> value="<?php echo $lang['code']?>"><?php echo $lang['display_name']?></option>
                            <?php endforeach; ?>
                            </select>            
                            &nbsp;
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>         
        <br />
        <div style="text-align:right">
            <input class="button-primary" name="save" value="<?php echo __('Next', 'sitepress') ?>" type="submit" />
            <?php /*
            <input class="button-primary" name="save" value="<?php echo __('Add more languages', 'sitepress') ?> &raquo;" type="submit" />
            <input class="button" name="save_one_language" value="<?php echo __('Done (just one language)', 'sitepress') ?>" type="submit" />
            */ ?>
        </div>
        </form>                
    <?php else: ?>
        <?php if($sitepress_settings['setup_complete'] || $sitepress_settings['setup_wizard_step'] == 2): ?>
        <table id="icl_languages_selection_table" class="widefat">
            <thead>
                <tr>
                    <th><?php echo __('Site Languages', 'sitepress') ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <table id="icl_setup_table" class="form-table">
                            <tr valign="top">            
                                <td>
                                    <?php if($sitepress_settings['setup_complete']): ?>
                                        <?php _e('These languages are enabled for this site.','sitepress'); ?><br />
                                        <ul id="icl_enabled_languages">
                                                <?php foreach($active_languages as $lang): $is_default = ($sitepress->get_default_language()==$lang['code']); ?>
                                            <li <?php if($is_default):?>class="default_language"<?php endif;?>><label><input name="default_language" type="radio" value="<?php echo $lang['code'] ?>" <?php if($is_default):?>checked="checked"<?php endif;?> /> <?php echo $lang['display_name'] ?> <?php if($is_default):?>(<?php echo __('default', 'sitepress') ?>)<?php endif?></label></li>
                                            <?php endforeach ?>
                                        </ul>
                                        <br clear="all" />
                                    <?php else: ?>
                                        <?php _e('Select the languages to enable for your site (you can also add and remove languages later).','sitepress'); ?><br />
                                    <?php endif; ?>
                                    <input id="icl_save_default_button" type="button" class="button-secondary action" value="<?php echo __('Apply', 'sitepress') ?>" />
                                    <input id="icl_cancel_default_button" type="button" class="button-secondary action" value="<?php echo __('Cancel', 'sitepress') ?>" />                                    
                                    <?php if($sitepress_settings['setup_complete']): ?>
                                    <input id="icl_change_default_button" type="button" class="button-secondary action" value="<?php echo __('Change default language', 'sitepress') ?>" <?php if(count($active_languages) < 2): ?>style="display:none"<?php endif ?> />
                                    <?php endif; ?>
                                    
                                    <?php if($sitepress_settings['setup_complete']): ?>
                                    <input id="icl_add_remove_button" type="button" class="button-secondary action" value="<?php echo __('Add / Remove languages', 'sitepress') ?>" />
                                    <span class="icl_ajx_response" id="icl_ajx_response"></span>
                                    <br clear="all" />
                                    <?php endif; ?>
                                    <div id="icl_avail_languages_picker" <?php if(!$sitepress_settings['setup_complete']) echo 'style="display:block"' ?>>                
                                        <ul>
                                        <?php foreach($languages as $lang): ?>
                                            <li><label><input type="checkbox" value="<?php echo $lang['code'] ?>" <?php if($lang['active']):?>checked="checked"<?php endif;?> 
                                            <?php if($sitepress->get_default_language()==$lang['code']):?>disabled="disabled"<?php endif;?>/>
                                                <?php if($lang['major']):?><strong><?php endif;?><?php echo $lang['display_name'] ?><?php if($lang['major']):?></strong><?php endif;?></label></li>
                                        <?php endforeach ?>
                                        </ul>
                                        <?php if($sitepress_settings['setup_complete']): ?>
                                        <br clear="all" />
                                        <div>
                                            <input id="icl_save_language_selection" type="button" class="button-secondary action" value="<?php echo __('Apply', 'sitepress') ?>" />
                                            <input id="icl_cancel_language_selection" type="button" class="button-secondary action" value="<?php echo __('Cancel', 'sitepress') ?>" />                                
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        </table>                
                        
                        <?php if(!empty($inactive_content)): ?>
                            <?php 
                                foreach($inactive_content as $language=>$ic){
                                    $t_posts += $ic['post'];
                                    $t_pages += $ic['page'];
                                    $t_cats += $ic['category'];
                                    $t_tags += $ic['post_tag'];
                                } 
                            ?>        
                            <br />
                            <h3><?php echo __('Inactive content', 'sitepress') ?></h3>
                            <small><?php echo __('In order to edit or delete these you need to activate the corresponding language first', 'sitepress') ?></small>
                            <table id="icl_inactive_content_table" class="widefat" cellspacing="0">
                            <thead>
                            <tr>
                            <th scope="col"><?php echo __('Language', 'sitepress') ?></th>
                            <th scope="col"><?php echo __('Posts', 'sitepress') ?></th>
                            <th scope="col"><?php echo __('Pages', 'sitepress') ?></th>        
                            <th scope="col"><?php echo __('Categories', 'sitepress') ?></th>
                            <th scope="col"><?php echo __('Tags', 'sitepress') ?></th>                
                            </tr>        
                            </thead>
                            <tfoot>
                            <tr>
                            <th scope="col"><?php echo __('Total', 'sitepress') ?></th>
                            <td scope="col"><?php echo intval($t_posts) ?></td>
                            <td scope="col"><?php echo intval($t_pages) ?></td>
                            <td scope="col"><?php echo intval($t_cats) ?></td>
                            <td scope="col"><?php echo intval($t_tags) ?></td>
                            </tr>        
                            </tfoot>                                
                            <tbody>        
                            <?php foreach($inactive_content as $language=>$ic): ?>
                            <tr>
                            <th scope="col"><?php echo $language ?></th>
                            <td scope="col"><?php echo intval($ic['post']); ?></td>
                            <td scope="col"><?php echo intval($ic['page']); ?></td>
                            <td scope="col"><?php echo intval($ic['category']); ?></td>
                            <td scope="col"><?php echo intval($ic['post_tag']); ?></td>
                            </tr>
                            <?php endforeach; ?>                                                                  
                            </tbody>        
                            </table>    
                        <?php endif; ?>                      
                    </td>
                </tr>
            </tbody>
        </table> 
        <br />
        <?php if($sitepress_settings['setup_wizard_step']==2): ?>             
        <div style="text-align:right">
            <input id="icl_setup_back_1" class="button-primary" name="save" value="<?php echo __('Back', 'sitepress') ?>" type="button" />
            <input id="icl_setup_next_1" class="button-primary" name="save" value="<?php echo __('Next', 'sitepress') ?>" type="button" <?php if(count($active_languages) < 2):?>disabled="disabled"<?php endif;?> />
        </div>
        <?php endif; ?>                      
        <?php endif; ?>                      

        
        <div id="icl_more_languages_wrap">
            <div id="icl_lnt" class="icl_advanced_feature">
            <?php if(count($active_languages) > 1): ?>            
            
                <table class="widefat">
                    <thead>
                        <tr>
                            <th><?php echo __('Choose how to determine which language visitors see contents in', 'sitepress') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <br />
                                <form id="icl_save_language_negotiation_type" name="icl_save_language_negotiation_type" action="">
                                <ul>
                                    <?php
                                    if(!class_exists('WP_Http')){
                                       include_once ICL_PLUGIN_PATH . '/lib/http.php';
                                    }
                                    $client = new WP_Http();
                                    if(false === strpos($_POST['url'],'?')){$url_glue='?';}else{$url_glue='&';}                    
                                    //set_error_handler('trigger_error');
                                    $response = $client->request(get_option('home') . '/' . $sample_lang['code'] .'/' . $url_glue . '____icl_validate_domain=1', array('timeout'=>15, 'decompress'=>false));
                                    //restore_error_handler();
                                    if(!is_wp_error($response) && ($response['response']['code']=='200') && ($response['body'] == '<!--'.get_option('home').'-->')){
                                        $icl_folder_url_disabled = false;
                                    }else{
                                        $icl_folder_url_disabled = true;
                                    }                    
                                    ?>
                                    <li>
                                        <label>
                                            <input<?php //if($icl_folder_url_disabled) echo ' disabled="disabled'; ?> type="radio" name="icl_language_negotiation_type" value="1" <?php if($sitepress_settings['language_negotiation_type']==1):?>checked="checked"<?php endif?> />                                                    
                                            <?php echo sprintf(__('Different languages in directories (%s - %s, %s/%s/ - %s, etc.)', 'sitepress'), trailingslashit(get_option('home')), $default_language['display_name'] , get_option('home'), $sample_lang['code'], $sample_lang['display_name'] ) ?>
                                        </label>
                                        <?php if($icl_folder_url_disabled):?>                                        
                                        <div class="icl_error_text" style="margin:10px;">
                                            <p>
                                                <?php echo __('It looks like languages per directories will not function.', 'sitepress'); ?>
                                                <a href="#" onClick="jQuery(this).parent().parent().next().toggle();return false">Details</a>
                                            </p>
                                        </div>                                                 
                                        <div class="icl_error_text" style="display:none;margin:10px;"><?php echo __('
                                            <p>This can be a result of either:</p>
                                            <ul style="list-style: circle;margin-left:18px">
                                            <li>WordPress is installed in a directory (not root) and you\'re using default links.</li>
                                            <li>URL rewriting is not enabled in your web server.</li>
                                            <li>The web server cannot write to the .htaccess file</li>
                                            </ul>
                                            <a href="http://wpml.org/?page_id=1010">How to fix</a>
                                            ', 'sitepress')?>                            
                                            <p>
                                                <?php printf(__('When WPML accesses <a target="_blank" href="%s">%s</a> it gets:', 'sitepress'), $__url = get_option('home').'/' . $sample_lang['code'] .'/?____icl_validate_domain=1', $__url); ?>
                                                <br />
                                                <?php 
                                                    if(is_wp_error($response)){
                                                        echo '<strong>';                                            
                                                        echo $response->get_error_message();
                                                        echo '</strong>';
                                                    }elseif($response['response']['code']!='200'){
                                                        echo '<strong>';
                                                        printf(__('HTTP code: %s (%s)', 'sitepress'), $response['response']['code'], $response['response']['message']);
                                                        echo '</strong>';
                                                    }else{
                                                        echo '<div style="width:100%;height:150px;overflow:auto;background-color:#fff;color:#000;font-family:Courier;font-style:normal;border:1px solid #aaa;">'.htmlentities($response['body']).'</div>';
                                                    }
                                                ?>
                                            </p>
                                            <p>
                                                <?php printf(__('The expected value is: %s', 'sitepress'), '<br /><strong>&lt;!--'.get_option('home').'--&gt;</strong>'); ?>
                                            </p>
                                        </div>                            
                                        <?php endif; ?>
                                    </li>
                                    <?php 
                                    global $wpmu_version;
                                    if(isset($wpmu_version)){
                                        $icl_lnt_disabled = 'disabled="disabled" ';
                                    }else{
                                        $icl_lnt_disabled = '';
                                    } 
                                    ?>
                                    <li>
                                        <label>
                                            <input <?php echo $icl_lnt_disabled ?>id="icl_lnt_domains" type="radio" name="icl_language_negotiation_type" value="2" <?php if($sitepress_settings['language_negotiation_type']==2):?>checked="checked"<?php endif?> />
                                            <?php echo __('A different domain per language', 'sitepress') ?>
                                            <?php if($icl_lnt_disabled): ?>
                                            <span class="icl_error_text"><?php echo __('This option is not yet available for WPMU', 'sitepress')?></span>
                                            <?php endif; ?>
                                        </label>
                                        <?php if($sitepress_settings['language_negotiation_type']==2):?>                    
                                        <div id="icl_lnt_domains_box">
                                        <table class="language_domains">
                                        <?php foreach($active_languages as $lang) :?>
                                        <tr>
                                            <td><?php echo $lang['display_name'] ?></td>
                                            <?php if($lang['code']==$sitepress->get_default_language()): ?>                        
                                            <td id="icl_ln_home"><?php echo get_option('home') ?></td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <?php else: ?>
                                            <td><input type="text" id="language_domain_<?php echo $lang['code'] ?>" name="language_domains[<?php echo $lang['code'] ?>]" value="<?php echo $sitepress_settings['language_domains'][$lang['code']] ?>" size="40" /></td>
                                            <td><label><input class="validate_language_domain" type="checkbox" name="validate_language_domains[]" value="<?php echo $lang['code'] ?>" checked="checked" /> <?php echo  __('Validate on save', 'sitepress') ?></td>
                                            <td><span id="ajx_ld_<?php echo $lang['code'] ?>"></span></td>
                                            <?php endif; ?>
                                        </tr>
                                        <?php endforeach; ?>
                                        </table>
                                        </div>
                                        <?php endif; ?>
                                    </li>                
                                    <li>
                                        <label>
                                            <input type="radio" name="icl_language_negotiation_type" value="3" <?php if($sitepress_settings['language_negotiation_type']==3):?>checked="checked"<?php endif?> />
                                            <?php echo sprintf(__('Language name added as a parameter (%s?lang=%s - %s)', 'sitepress'),get_option('home'),$sample_lang['code'],$sample_lang['display_name']) ?>
                                        </label>
                                    </li>
                                </ul>
                                <p>
                                    <input class="button" name="save" value="<?php echo __('Apply','sitepress') ?>" type="submit" />
                                    <span class="icl_ajx_response" id="icl_ajx_response2"></span>
                                </p>
                                </form>                           
                            </td>
                        </tr>
                    </tbody>
                </table> 
                <br />  
            <?php endif; ?>
            </div>
            
            <div id="icl_lso">
            <?php if($sitepress_settings['setup_complete'] && count($active_languages) > 1 || $sitepress_settings['setup_wizard_step']==3): ?>

                <form id="icl_save_language_switcher_options" name="icl_save_language_switcher_options" action="">            
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th><?php echo __('Language switcher options', 'sitepress') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <p class="icl_form_errors" style="display:none"></p>
                                    <ul>
                                        <li>
                                            <h4 class="icl_advanced_feature"><?php echo __('Language switcher widget', 'sitepress')?></h4>                                
                                            <?php _e('Choose where to display the language switcher widget:', 'sitepress') ?>
                                            <?php 
                                            global $wp_registered_sidebars; 
                                            $swidgets = wp_get_sidebars_widgets();
                                            $sb = '';
                                            foreach($swidgets as $k=>$v){
                                                if(in_array('language-selector', $v)){
                                                    $sb = $k;
                                                }
                                            }
                                            ?>                
                                            <select name="icl_language_switcher_sidebar">                
                                            <?php foreach($wp_registered_sidebars as $rs): ?>                
                                            <option value="<?php echo $rs['id']?>" <?php if($sb == $rs['id']) echo 'selected="SELECTED"'?>><?php echo $rs['name']?>&nbsp;</option>
                                            <?php endforeach;?>
                                            <option value="" <?php if(!$sb && $sitepress_settings['setup_complete']) echo 'selected="SELECTED"' ?> ><?php _e('--none--', 'sitepress'); ?></option>
                                            </select>
                                                                                        
                                            <p class="icl_advanced_feature"><?php printf(__('The drop-down language switcher can be added to your theme by inserting this PHP code: %s or as a widget','sitepress'),
                                            '<code class="php">&lt;?php do_action(\'icl_language_selector\'); ?&gt;</code>'); ?>.</p>
                                                                                        
                                            <p class="icl_advanced_feature"><?php echo __('You can also create custom language switchers, such as a list of languages or country flags.','sitepress'); ?>
                                            <a href="http://wpml.org/?page_id=989"><?php echo __('Custom language switcher creation guide','sitepress')?></a>.
                                            </p>
                                        </li>                                   
                                        <li class="icl_advanced_feature">
                                            <h4><?php echo __('How to handle languages without translation', 'sitepress')?></h4>
                                            <p><?php echo __('Some pages or posts may not be translated to all languages. Select how the language selector should behave in case translation is missing.', 'sitepress') ?></p>
                                            <ul>
                                                <li>
                                                    <label>
                                                        <input type="radio" name="icl_lso_link_empty" value="0" <?php if(!$sitepress_settings['icl_lso_link_empty']):?>checked="checked"<?php endif?> />
                                                        <?php echo __('Skip language', 'sitepress') ?>
                                                    </label>
                                                </li>
                                                <li>
                                                <label>
                                                    <input type="radio" name="icl_lso_link_empty" value="1" <?php if($sitepress_settings['icl_lso_link_empty']==1):?>checked="checked"<?php endif?> />
                                                    <?php echo __('Link to home of language for missing translations', 'sitepress') ?>
                                                </label>                    
                                                </li>
                                            </ul>
                                        </li>                    
                                        <li>
                                        
                                            <h4><?php echo __('What to include in the language switcher', 'sitepress')?></h4>
                                    
                                                                             
                                            <?php if(!defined('ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS') || !ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS): ?>                                            
                                            <div id="icl_lang_sel_preview_wrap" style="height:<?php echo 50 + 20 * count($sitepress->get_active_languages())?>px">                                            
                                            <span id="icl_lang_sel_preview">                                            
                                            <h4><?php _e('Language switcher widget preview', 'sitepress')?></h4>
                                            <?php 
                                                global $icl_language_switcher_preview;
                                                $icl_language_switcher_preview = true;                                                
                                                $sitepress->language_selector(); 
                                            ?>                                                                          
                                            </span>                                                                     
                                            </div>
                                            <?php endif; ?>
                                            
                                            <ul>
                                                <li>
                                                    <label>
                                                        <input type="checkbox" name="icl_lso_flags" value="1" <?php if($sitepress_settings['icl_lso_flags']):?>checked="checked"<?php endif?> />
                                                        <?php echo __('Flag', 'sitepress') ?>
                                                    </label>
                                                </li>
                                                <li>
                                                <label>
                                                    <input type="checkbox" name="icl_lso_native_lang" value="1" <?php if($sitepress_settings['icl_lso_native_lang']):?>checked="checked"<?php endif?> />
                                                    <?php echo __('Native language name (the language name as it\'s written in that language)', 'sitepress') ?>
                                                </label>                    
                                                </li>
                                                <li>
                                                <label>
                                                    <input type="checkbox" name="icl_lso_display_lang" value="1" <?php if($sitepress_settings['icl_lso_display_lang']):?>checked="checked"<?php endif?> />
                                                    <?php echo __('Language name in display language (the language name as it\'s written in the currently displayed language)', 'sitepress') ?>
                                                </label>                    
                                                </li>                            
                                            </ul>

                                            <?php foreach($language_switcher_defaults_alt as $key=>$val): ?>
                                                <?php foreach($language_switcher_defaults as $k=>$v): ?>                                                
                                                <input type="hidden" id="icl_lang_sel_config_alt_<?php echo $key ?>_<?php echo $k ?>" value="<?php echo $language_switcher_defaults_alt[$key][$k] ?>" />
                                                <?php endforeach; ?>
                                            <?php endforeach; ?>                                            
                                            
                            
                                            <?php if(!defined('ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS') || !ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS): ?>
                                            <br />
                                            <a href="#" onclick="jQuery(this).next().slideToggle();return false;"><?php _e('Edit the language switcher widget colors', 'sitepress')?></a>                                            
                                            <div style="display:none">                                          
                                                <table id="icl_lang_preview_config" style="width:auto;">
                                                    <thead>
                                                    <tr>
                                                        <th>&nbsp;</th>
                                                        <th><?php _e('Normal', 'sitepress')?></th>
                                                        <th><?php _e('Hover', 'sitepress')?></th>
                                                    </tr>
                                                    </thead>
								                    <tbody>                                                
                                                    <tr>
                                                        <td><?php _e('Current language font color', 'sitepress')?></td>
                                                        <td><input type="text" size="7" id="icl-lang-sel-config-font-current-normal" name="icl_lang_sel_config[font-current-normal]" value="<?php 
                                                            if(isset($sitepress_settings['icl_lang_sel_config']['font-current-normal'])) 
                                                                echo $sitepress_settings['icl_lang_sel_config']['font-current-normal']; 
                                                            else 
                                                                echo $language_switcher_defaults['font-current-normal'] ;
                                                            ?>" /><img src="<?php echo ICL_PLUGIN_URL; ?>/res/img/icon_color_picker.png" id="icl-font-current-normal-picker" alt="" border="0" style="vertical-align:bottom;cursor:pointer;" class="pick-show" onclick="icl_cp_target='icl-lang-sel-config-font-current-normal';cp.show('icl-font-current-normal-picker');return false;" /></td>
                                                        <td><input type="text" size="7" id="icl-lang-sel-config-font-current-hover" name="icl_lang_sel_config[font-current-hover]" value="<?php 
                                                            if(isset($sitepress_settings['icl_lang_sel_config']['font-current-hover'])) 
                                                                echo $sitepress_settings['icl_lang_sel_config']['font-current-hover']; 
                                                            else 
                                                                echo $language_switcher_defaults['font-current-hover'] ;
                                                            ?>" /><img src="<?php echo ICL_PLUGIN_URL; ?>/res/img/icon_color_picker.png" id="icl-font-current-hover-picker" alt="" border="0" style="vertical-align:bottom;cursor:pointer;" class="pick-show" onclick="icl_cp_target='icl-lang-sel-config-font-current-hover';cp.show('icl-font-current-hover-picker');return false;" /></td>
                                                    </tr>                                                
                                                    <tr>
                                                        <td><?php _e('Current language background color', 'sitepress')?></td>
                                                        <td><input type="text" size="7" id="icl-lang-sel-config-background-current-normal" name="icl_lang_sel_config[background-current-normal]" value="<?php 
                                                            if(isset($sitepress_settings['icl_lang_sel_config']['background-current-normal'])) 
                                                                echo $sitepress_settings['icl_lang_sel_config']['background-current-normal']; 
                                                            else 
                                                                echo $language_switcher_defaults['background-current-normal'] ;
                                                            ?>" /><img src="<?php echo ICL_PLUGIN_URL; ?>/res/img/icon_color_picker.png" id="icl-background-current-normal-picker" alt="" border="0" style="vertical-align:bottom;cursor:pointer;" class="pick-show" onclick="icl_cp_target='icl-lang-sel-config-background-current-normal';cp.show('icl-background-current-normal-picker');return false;" /></td>
                                                        <td><input type="text" size="7" id="icl-lang-sel-config-background-current-hover" name="icl_lang_sel_config[background-current-hover]" value="<?php 
                                                            if(isset($sitepress_settings['icl_lang_sel_config']['background-current-hover'])) 
                                                                echo $sitepress_settings['icl_lang_sel_config']['background-current-hover']; 
                                                            else 
                                                                echo $language_switcher_defaults['background-current-hover'] ;
                                                            ?>" /><img src="<?php echo ICL_PLUGIN_URL; ?>/res/img/icon_color_picker.png" id="icl-background-current-hover-picker" alt="" border="0" style="vertical-align:bottom;cursor:pointer;" class="pick-show" onclick="icl_cp_target='icl-lang-sel-config-background-current-hover';cp.show('icl-background-current-hover-picker');return false;" /></td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php _e('Other languages font color', 'sitepress')?></td>
                                                        <td><input type="text" size="7" id="icl-lang-sel-config-font-other-normal" name="icl_lang_sel_config[font-other-normal]" value="<?php 
                                                            if(isset($sitepress_settings['icl_lang_sel_config']['font-other-normal'])) 
                                                                echo $sitepress_settings['icl_lang_sel_config']['font-other-normal']; 
                                                            else 
                                                                echo $language_switcher_defaults['font-other-normal'] ;
                                                            ?>" /><img src="<?php echo ICL_PLUGIN_URL; ?>/res/img/icon_color_picker.png" id="icl-font-other-normal-picker" alt="" border="0" style="vertical-align:bottom;cursor:pointer;" class="pick-show" onclick="icl_cp_target='icl-lang-sel-config-font-other-normal';cp.show('icl-font-other-normal-picker');return false;" /></td>
                                                        <td><input type="text" size="7" id="icl-lang-sel-config-font-other-hover" name="icl_lang_sel_config[font-other-hover]" value="<?php 
                                                            if(isset($sitepress_settings['icl_lang_sel_config']['font-other-hover'])) 
                                                                echo $sitepress_settings['icl_lang_sel_config']['font-other-hover']; 
                                                            else 
                                                                echo $language_switcher_defaults['font-other-hover'] ;
                                                            ?>" /><img src="<?php echo ICL_PLUGIN_URL; ?>/res/img/icon_color_picker.png" id="icl-font-other-hover-picker" alt="" border="0" style="vertical-align:bottom;cursor:pointer;" class="pick-show" onclick="icl_cp_target='icl-lang-sel-config-font-other-hover';cp.show('icl-font-other-hover-picker');return false;" /></td>
                                                    </tr>                                                
                                                    <tr>
                                                        <td><?php _e('Other languages background color', 'sitepress')?></td>
                                                        <td><input type="text" size="7" id="icl-lang-sel-config-background-other-normal" name="icl_lang_sel_config[background-other-normal]" value="<?php 
                                                            if(isset($sitepress_settings['icl_lang_sel_config']['background-other-normal'])) 
                                                                echo $sitepress_settings['icl_lang_sel_config']['background-other-normal']; 
                                                            else 
                                                                echo $language_switcher_defaults['background-other-normal'] ;
                                                            ?>" /><img src="<?php echo ICL_PLUGIN_URL; ?>/res/img/icon_color_picker.png" id="icl-background-other-normal-picker" alt="" border="0" style="vertical-align:bottom;cursor:pointer;" class="pick-show" onclick="icl_cp_target='icl-lang-sel-config-background-other-normal';cp.show('icl-background-other-normal-picker');return false;" /></td>
                                                        <td><input type="text" size="7" id="icl-lang-sel-config-background-other-hover" name="icl_lang_sel_config[background-other-hover]" value="<?php 
                                                            if(isset($sitepress_settings['icl_lang_sel_config']['background-other-hover'])) 
                                                                echo $sitepress_settings['icl_lang_sel_config']['background-other-hover']; 
                                                            else 
                                                                echo $language_switcher_defaults['background-other-hover'] ;
                                                            ?>" /><img src="<?php echo ICL_PLUGIN_URL; ?>/res/img/icon_color_picker.png" id="icl-background-other-hover-picker" alt="" border="0" style="vertical-align:bottom;cursor:pointer;" class="pick-show" onclick="icl_cp_target='icl-lang-sel-config-background-other-hover';cp.show('icl-background-other-hover-picker');return false;" /></td>
                                                    </tr>                                                
                                                    <tr>
                                                        <td><?php _e('Border', 'sitepress')?></td>
                                                        <td><input type="text" size="7" id="icl-lang-sel-config-border" name="icl_lang_sel_config[border]" value="<?php 
                                                            if(isset($sitepress_settings['icl_lang_sel_config']['border'])) 
                                                                echo $sitepress_settings['icl_lang_sel_config']['border']; 
                                                            else 
                                                                echo $language_switcher_defaults['border'] ;
                                                            ?>" /><img src="<?php echo ICL_PLUGIN_URL; ?>/res/img/icon_color_picker.png" id="icl-border-picker" alt="" border="0" style="vertical-align:bottom;cursor:pointer;" class="pick-show" onclick="icl_cp_target='icl-lang-sel-config-border';cp.show('icl-border-picker');return false;" /></td>
                                                        <td>&nbsp;</td>
                                                    </tr>
                                                    </tbody>                                                
                                                    
                                                </table>

                                                <?php _e('Presets:', 'sitepress')?>
                                                <select id="icl_lang_sel_color_scheme" name="icl_lang_sel_color_scheme">
                                                    <option value=""><?php _e('--select--', 'sitepress') ?>&nbsp;</option>
                                                    <option value="Gray"><?php _e('Gray', 'sitepress') ?>&nbsp;</option>
                                                    <option value="White"><?php _e('White', 'sitepress') ?>&nbsp;</option>                                                    
                                                    <option value="Blue"><?php _e('Blue', 'sitepress') ?>&nbsp;</option>
                                                </select>
                                                <span style="display:none"><?php _e("Are you sure? The customization you may have made will be overriden once you click 'Apply'", 'sitepress')?></span>
                                            </div>   
                                            <?php else: ?>
                                            <em><?php printf(__("%s is defined in your theme. The language switcher can only be customized using the theme's CSS.", 'sitepress'), 'ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS') ?></em>
                                            <?php endif; ?>
                                        </li>
										
                                        <?php do_action('icl_language_switcher_options'); ?>

                                    </ul>
                                    <?php if($sitepress_settings['setup_complete']): ?>             
                                        <p>
                                            <input class="button" name="save" value="<?php echo __('Apply','sitepress') ?>" type="submit" />
                                            <span class="icl_ajx_response" id="icl_ajx_response3"></span>
                                        </p>                                    
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table> 
                    <br />
                    <?php if(!$sitepress_settings['setup_complete']): ?>             
                    <div id="icl_setup_nav_3" style="text-align:right">
                        <input id="icl_setup_back_2" class="button-primary" name="save" value="<?php echo __('Back', 'sitepress') ?>" type="button" />
                        <input class="button-primary" name="save" value="<?php echo __('Finish', 'sitepress') ?>" type="submit" />
                    </div>
                    <script type="text/javascript">
                    addLoadEvent(function(){     
                        jQuery('#icl_save_language_switcher_options').submit(function(){
                            iclSaveForm_success_cb.push(function(){
                                location.href = location.href.replace(/#.*/,'')
                            });
                        });
                    });
                    </script>
                    <?php endif; ?>
                </form>                                          
            <?php endif; ?>
            </div>
            
            <div id="icl_mo" class="icl_advanced_feature">
            <?php if(count($active_languages) > 1): ?>   
                <table class="widefat">
                    <thead>
                        <tr>
                            <th><?php echo __('Admin language', 'sitepress') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <form id="icl_admin_language_options" name="icl_admin_language_options" action="">        
                                <?php if(is_admin()): ?>
                                <p>
                                    <label>
                                        <?php _e('Default admin language: ', 'sitepress'); ?>
                                        <?php $default_language = $sitepress->get_language_details($sitepress->get_default_language()); ?>                                        
                                        <select name="icl_admin_default_language">
                                        <option value="_default_"><?php printf(__('Default language (currently %s)', 'sitepress'),  $default_language['display_name']); ?></option>
                                        <?php foreach($active_languages as $al):?>
                                        <option value="<?php echo $al['code'] ?>"<?php if($sitepress_settings['admin_default_language']==$al['code']) echo ' selected="selected"'?>><?php echo $al['display_name']; if($sitepress->get_admin_language() != $al['code']) echo ' ('. $al['native_name'] .')' ?>&nbsp;</option>
                                        <?php endforeach; ?>
                                        </select>
                                    </label>
                                </p>
                                <?php endif; ?>
                                <p><?php printf(__('Each user can choose the admin language. You can edit your language preferences by visiting your <a href="%s">profile page</a>.','sitepress'),'profile.php#wpml')?></p>
                                <p>
                                    <input class="button" name="save" value="<?php echo __('Apply','sitepress') ?>" type="submit" />
                                    <span class="icl_ajx_response" id="icl_ajx_response_al"></span>
                                </p>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <br />
            <?php endif; ?>                            
            </div>
            
            <div id="icl_mo" class="icl_advanced_feature">
            <?php if(count($active_languages) > 1): ?>   
                <table class="widefat">
                    <thead>
                        <tr>
                            <th><?php echo __('More options', 'sitepress') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <h4><?php _e('Translation controls', 'sitepress')?></h4>
                                <form id="icl_lang_more_options" name="icl_lang_more_options" action="">        
                                <p>
                                    <label>
                                        <input type="checkbox" name="icl_translation_controls_on_posts_lists" <?php if(!$sitepress_settings['hide_translation_controls_on_posts_lists']) echo 'checked="checked"' ?> /> <?php _e('Display translation controls on posts and pages lists.','sitepress'); ?>
                                    </label>
                                </p>
                                <p>
                                    <input class="button" name="save" value="<?php echo __('Apply','sitepress') ?>" type="submit" />
                                    <span class="icl_ajx_response" id="icl_ajx_response_mo"></span>
                                </p>
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h4><?php _e('Blog posts to display', 'sitepress')?></h4>
                                <form id="icl_blog_posts" name="icl_blog_posts" action="">                                        
                                <p>
                                    <label>
                                        <input type="radio" name="icl_untranslated_blog_posts" <?php if(!$sitepress_settings['show_untranslated_blog_posts']) echo 'checked="checked"' ?> value="0" /> <?php _e('Only translated posts.','sitepress'); ?>
                                    </label>
                                </p>
                                <p>
                                    <label>
                                        <input type="radio" name="icl_untranslated_blog_posts" <?php if($sitepress_settings['show_untranslated_blog_posts']) echo 'checked="checked"' ?> value="1" /> <?php _e('All posts (display translation if it exists or posts in default language otherwise).','sitepress'); ?>
                                    </label>
                                </p>                                
                                <p>
                                    <input class="button" name="save" value="<?php echo __('Apply','sitepress') ?>" type="submit" />
                                    <span class="icl_ajx_response" id="icl_ajx_response_bp"></span>
                                </p>
                                </form>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <h4><?php _e('Hide languages', 'sitepress')?></h4>
                                <form id="icl_hide_languages" name="icl_hide_languages" action="">                                        
                                <p>
                                    <?php foreach($active_languages as $l): ?>
                                    <?php if($l['code'] == $default_language['code']) continue; ?>
                                    <label style="white-space:nowrap">
                                        <input type="checkbox" name="icl_hidden_languages[]" <?php if(in_array($l['code'],(array)$sitepress_settings['hidden_languages'])) echo 'checked="checked"' ?> value="<?php echo $l['code']?>" /> <?php echo $l['display_name'] ?>
                                    </label>&nbsp;&nbsp;&nbsp;
                                    <?php endforeach; ?>
                                </p>
                                <p>
                                    <input class="button" name="save" value="<?php echo __('Apply','sitepress') ?>" type="submit" />
                                    <span class="icl_ajx_response" id="icl_ajx_response_hl"></span>
                                </p>
                                <p id="icl_hidden_languages_status">                     
                                <?php 
                                    if(!empty($sitepress_settings['hidden_languages'])){
                                        
                                         if(1 == count($sitepress_settings['hidden_languages'])){
                                             printf(__('%s is currently hidden to visitors.', 'sitepress'), 
                                                $active_languages[$sitepress_settings['hidden_languages'][0]]['display_name']);
                                         }else{
                                             foreach($sitepress_settings['hidden_languages'] as $l){
                                                 $_hlngs[] = $active_languages[$l]['display_name'];
                                             }                                 
                                             $hlangs = join(', ', $_hlngs);
                                             printf(__('%s are currently hidden to visitors.', 'sitepress'), $hlangs);
                                         }
                                         echo ' ';
                                         printf(__('You can enable its/their display for yourself, in your <a href="%s">profile page</a>.', 'sitepress'),
                                            'profile.php#wpml');
                                     }else{
                                          _e('All languages are currently displayed.', 'sitepress');
                                     }
                                ?>
                                </p>                                
                                </form>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>
                                <h4><?php _e('Make themes work multilingual', 'sitepress')?></h4>
                                <form id="icl_adjust_ids" name="icl_adjust_ids" action="">        
                                <p>
                                    <label>
                                        <input type="checkbox" value="1" name="icl_adjust_ids" <?php if($sitepress_settings['auto_adjust_ids']) echo 'checked="checked"' ?> /> 
                                        <?php _e('Adjust IDs for multilingual functionality', 'sitepress')?>
                                    </label>
                                </p>
                                <p><?php _e('This feature turns themes into multilingual, without having to edit their PHP files.', 'sitepress')?></p>
                                <p>
                                    <input class="button" name="save" value="<?php echo __('Apply','sitepress') ?>" type="submit" />
                                    <span class="icl_ajx_response" id="icl_ajx_response_ai"></span>
                                </p>
                                </form>
                            </td>
                        </tr>
                        
                    </tbody>
                </table>
                <br />
            <?php endif; ?>                            
            </div>            

        </div>
    <?php endif; ?>
    
    <?php if($sitepress_settings['setup_complete']): ?>  
    
    <?php do_action('icl_extra_options_' . $_GET['page']); ?>        
        
    <form name="icl_promote_form">
    <p>
        <label><input type="checkbox" name="icl_promote" <?php if($sitepress_settings['promote_wpml']) echo 'checked="checked"' ?> value="1" /> <?php _e("Tell the world your site is running multilingual with WPML (places a message in your site's footer)", 'sitepress'); ?></label>
    </p>
    </form>
    <?php endif; ?>
    
    <div id="icl_translate_help_collapsed" <?php if(!$sitepress_settings['dont_show_translate_help']) echo 'style="display:none"' ?>>
    <img src="<?php echo ICL_PLUGIN_URL ?>/res/img/question-green.png" width="16" height="16" />&nbsp;<a href="#"><?php _e('Instructions for translating contents', 'sitepress'); ?> &raquo;</a>
    </div>    
    <div id="icl_translate_help" class="icl_yellow_box" <?php if(!$sitepress_settings['setup_complete'] || $sitepress_settings['dont_show_translate_help']) echo 'style="display:none"' ?>>
    <h3><?php _e('How to translate ', 'sitepress')?></h3>
    <h4><?php _e('Translating yourself?', 'sitepress')?></h4>
    <p><?php _e('You can add translations to posts, pages, tags and categories. Each edit page now includes a new languages box. From that box you can add translations to other languages.', 'sitepress'); ?></p>                                                                                                         
    <h4><?php _e('Need help translating?', 'sitepress')?></h4>    
    
    <img align="left" src="<?php echo ICL_PLUGIN_URL ?>/res/img/icon16.png" width="16" height="16" style="margin-right:7px" />
    
    <p><?php printf(__('Try WPML&#8217;s <a href="%s">professional translation</a> and get excellent translations at an affordable rate. <a href="%s">Learn more</a>.','sitepress'), 'admin.php?page='.basename(ICL_PLUGIN_PATH).'/menu/content-translation.php', 'admin.php?page='.basename(ICL_PLUGIN_PATH).'/menu/content-translation.php'); ?></p>
    <br />
    <input id="icl_dismiss_translate_help" type="button" class="button secondary" value="<?php _e('Hide this message', 'sitepress');?>" />
    </div> 
    <br />   
       
    <?php do_action('icl_menu_footer'); ?>
    
</div>
