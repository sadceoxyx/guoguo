<?php 
if((!isset($sitepress_settings['existing_content_language_verified']) || !$sitepress_settings['existing_content_language_verified']) /*|| 2 > count($sitepress->get_active_languages())*/){
    return;
}

$status_filter = isset($_GET['status']) ? intval($_GET['status']) : false;
$context_filter = isset($_GET['context']) ? $_GET['context'] : false;
$search_filter = isset($_GET['search']) ? $_GET['search'] : false;
$exact_match = isset($_GET['em']) ? $_GET['em'] == 1 : false;

$icl_string_translations = icl_get_string_translations();
if(!empty($icl_string_translations)){
    $icl_strings_in_page = icl_get_strigs_tracked_in_pages($icl_string_translations);
}
$active_languages = $sitepress->get_active_languages();            
$icl_contexts = icl_st_get_contexts($status_filter);

/*
if($status_filter != ICL_STRING_TRANSLATION_COMPLETE){
    $icl_contexts_translated = icl_st_get_contexts(ICL_STRING_TRANSLATION_COMPLETE);
}else{
    $icl_contexts_translated = $icl_contexts;
}
*/
$icl_st_translation_enabled = $sitepress->icl_account_configured() && $sitepress->get_icl_translation_enabled();

$available_contexts = array();
if(!empty($icl_contexts)){
    foreach($icl_contexts as $c){
        if($c) $available_contexts[] = $c->context;
    }                                                
}
if(is_array($sitepress_settings['st']['theme_localization_domains'])){
    foreach($sitepress_settings['st']['theme_localization_domains'] as $c){
        if($c) $available_contexts[] = 'theme ' . $c;
    }
}
$available_contexts = array_unique($available_contexts);
?>
<div class="wrap">
    <div id="icon-options-general" class="icon32 icon32_adv"><br /></div>
    <h2><?php echo __('String translation', 'sitepress') ?></h2>    
    
    <?php include ICL_PLUGIN_PATH . '/menu/basic_advanced_switch.php' ?>
    
    <?php if(isset($icl_st_po_strings) && !empty($icl_st_po_strings)): ?>
    
        <p><?php printf(__('These are the strings that we found in your .po file. Please carefully review them. Then, click on the \'add\' or \'cancel\' buttons at the <a href="%s">bottom of this screen</a>. You can exclude individual strings by clearing the check boxes next to them.', 'sitepress'), '#add_po_strings_confirm'); ?></p>        
        <form method="post" action="admin.php?page=<?php echo basename(ICL_PLUGIN_PATH) ?>/menu/string-translation.php">
        <?php if(isset($_POST['icl_st_po_translations'])): ?>
        <input type="hidden" name="icl_st_po_language" value="<?php echo $_POST['icl_st_po_language'] ?>" />
        <?php endif; ?>
        <input type="hidden" name="icl_st_domain_name" value="<?php echo $_POST['icl_st_i_context_new']?$_POST['icl_st_i_context_new']:$_POST['icl_st_i_context'] ?>" />
        
        <table id="icl_po_strings" class="widefat" cellspacing="0">
            <thead>
                <tr>
                    <th scope="col" class="manage-column column-cb check-column"><input type="checkbox" checked="checked" name="" /></th>
                    <th><?php echo __('String', 'sitepress') ?></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th scope="col" class="manage-column column-cb check-column"><input type="checkbox" checked="checked" name="" /></th>
                    <th><?php echo __('String', 'sitepress') ?></th>
                </tr>
            </tfoot>        
            <tbody>
                <?php $k = -1; foreach($icl_st_po_strings as $str): $k++; ?>
                    <tr>
                        <td><input class="icl_st_row_cb" type="checkbox" name="icl_strings_selected[]" 
                            <?php if($str['exists'] || !isset($_POST['icl_st_po_translations'])): ?>checked="checked"<?php endif;?> value="<?php echo $k ?>" /></td>
                        <td>
                            <input type="text" name="icl_strings[]" value="<?php echo htmlspecialchars($str['string']) ?>" readonly="readonly" style="width:100%;" size="100" />
                            <?php if(isset($_POST['icl_st_po_translations'])):?>
                            <input type="text" name="icl_translations[]" value="<?php echo htmlspecialchars($str['translation']) ?>" readonly="readonly" style="width:100%;<?php if($str['fuzzy']):?>;background-color:#ffecec<?php endif; ?>" size="100" />
                            <input type="hidden" name="icl_fuzzy[]" value="<?php echo $str['fuzzy'] ?>" />
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>            
        <a name="add_po_strings_confirm"></a>        
        <p><input class="button" type="button" value="<?php echo __('Cancel', 'sitepress'); ?>" onclick="location.href='admin.php?page=<?php echo $_GET['page'] ?>'" />
        &nbsp; <input class="button-primary" type="submit" name="icl_st_save_strings" value="<?php echo __('Add selected strings', 'sitepress'); ?>" />
        </p>
        </form>
        
    <?php elseif(isset($icl_st_preview_strings) && !empty($icl_st_preview_strings)): ?>
        <?php $total_langs = count(explode(",",$_POST['langs'])); ?>
        <h3><?php echo __('Preview strings','sitepress') ?></h3>
        <form name="icl_st_do_send_strings" id="icl_st_do_send_strings" method="post" action="">
        <input type="hidden" name="strings" value="<?php echo $_POST['strings'] ?>" />
        <input type="hidden" name="languages" value="<?php echo $_POST['langs'] ?>" />
        <table id="icl_preview_strings" class="widefat" cellspacing="0">
            <thead>
                <tr>                    
                    <th><?php echo __('String', 'sitepress') ?></th>
                    <th scope="col" style="text-align:right"><?php echo __('Word count', 'sitepress') ?></th>
                    <?php if(1 < $total_langs): ?>
                    <th scope="col" style="text-align:right"><?php printf(__('Cost per language (at $%s per word)', 'sitepress'),'0.7'); ?></th>
                    <?php endif; ?>
                    <th scope="col" style="text-align:right"><?php printf(__('Cost (at $%s per word)', 'sitepress'),'0.7'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $total_cost = $total_wc = $total_cost_pl = 0; 
                    $total_langs = count(explode(",",$_POST['langs']));                    
                ?>
                <?php foreach($icl_st_preview_strings as $string): ?>
                    <?php 
                        $wc = count(explode(' ',$string->value)); $total_wc += $wc;
                        $cost_pl = $wc * 0.07;
                        $cost = $wc * 0.07 * $total_langs;
                        $total_cost += $cost;
                        $total_cost_pl += $cost_pl;
                    ?>
                    <tr>                        
                        <td><?php echo htmlspecialchars($string->value) ?></td>
                        <td align="right"><?php echo $wc ?></td>
                        <?php if(1 < $total_langs): ?>
                        <td align="right"><?php echo '$'; echo money_format($cost_pl, 2); ?></td>
                        <?php endif; ?>
                        <td align="right"><?php echo '$'; echo money_format($cost, 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th><?php echo __('Total', 'sitepress'); ?></th>
                    <th style="text-align:right"><?php echo $total_wc; ?></th>
                    <?php if(1 < $total_langs): ?>
                    <th style="text-align:right"><?php echo '$'; echo money_format($total_cost_pl,2); ?></th>
                    <?php endif; ?>
                    <th style="text-align:right"><?php echo '$'; echo money_format($total_cost,2); ?></th>
                </tr>
            </tfoot>                    
        </table>    
        <p>
            <input class="button" type="button" value="<?php echo __('Cancel', 'sitepress'); ?>" onclick="history.back()" />&nbsp; 
            <input class="button-primary" type="submit" value="<?php echo __('Send to translation', 'sitepress'); ?>" />
            &nbsp;<span id="icl_st_send_progress" class="icl_ajx_response" style="display:none;"><?php echo __('Sending translation requests. Please wait!', 'sitepress') ?>&nbsp;<img src="<?php echo ICL_PLUGIN_URL ?>/res/img/ajax-loader.gif" alt="loading" /></span>
        </p>
        </form>        
    <?php else: ?>
    
        <p style="line-height:220%;">
        <?php echo __('Select which strings to display:', 'sitepress')?>
        <select name="icl_st_filter_status">
            <option value="" <?php if($status_filter === false ):?>selected="selected"<?php endif;?>><?php echo __('All strings', 'sitepress') ?></option>        
            <option value="<?php echo ICL_STRING_TRANSLATION_COMPLETE ?>" <?php if($status_filter === ICL_STRING_TRANSLATION_COMPLETE):?>selected="selected"<?php endif;?>><?php echo $icl_st_string_translation_statuses[ICL_STRING_TRANSLATION_COMPLETE] ?></option>
            <option value="<?php echo ICL_STRING_TRANSLATION_NOT_TRANSLATED ?>" <?php if($status_filter === ICL_STRING_TRANSLATION_NOT_TRANSLATED):?>selected="selected"<?php endif;?>><?php echo __('Translation needed', 'sitepress') ?></option>
        </select>
        
        <?php if(!empty($icl_contexts)): ?>
        &nbsp;&nbsp;
        <span style="white-space:nowrap">
        <?php echo __('Select strings within context:', 'sitepress')?>
        <select name="icl_st_filter_context">
            <option value="" <?php if($context_filter === false ):?>selected="selected"<?php endif;?>><?php echo __('All contexts', 'sitepress') ?></option>
            <?php foreach($icl_contexts as $v):?>
            <option value="<?php echo htmlspecialchars($v->context)?>" <?php if($context_filter == $v->context ):?>selected="selected"<?php endif;?>><?php echo $v->context . ' ('.$v->c.')'; ?></option>
            <?php endforeach; ?>
        </select>    
        </span>
        <?php endif; ?>
        
        &nbsp;&nbsp;
        <span style="white-space:nowrap">
        <label>
        <?php echo __('Search for:', 'sitepress')?>
        <input type="text" id="icl_st_filter_search" value="<?php echo $search_filter ?>" />
        </label>
        
        <label>
        <input type="checkbox" id="icl_st_filter_search_em" value="1" <?php if($exact_match):?>checked="checked"<?php endif;?> />
        <?php echo __('Exact match', 'sitepress')?>
        </label>
        
        <input class="button" type="button" value="<?php _e('Search', 'sitepress')?>" id="icl_st_filter_search_sb" />
        </span>
        
        <?php if($search_filter): ?>
        <span style="white-space:nowrap">
        <?php printf(__('Showing only strings that contain %s', 'sitepress'), '<i>' . htmlspecialchars($search_filter)) . '</i>'; ?>
        <input class="button" type="button" value="<?php _e('Exit search', 'sitepress')?>" id="icl_st_filter_search_remove" />
        </span>
        <?php endif; ?>
        
        </p>
    
        <table id="icl_string_translations" class="widefat" cellspacing="0">
            <thead>
                <tr>
                    <th scope="col" class="manage-column column-cb check-column"><input type="checkbox" /></th>
                    <th scope="col"><?php echo __('Context', 'sitepress') ?></th>
                    <th scope="col"><?php echo __('Name', 'sitepress') ?></th>
                    <th scope="col"><?php echo __('View', 'sitepress') ?></th>
                    <th scope="col"><?php echo __('String', 'sitepress') ?></th>        
                    <th scope="col"><?php echo __('Status', 'sitepress') ?></th>
                </tr>        
            </thead>        
            <tfoot>
                <tr>
                    <th scope="col" class="manage-column column-cb check-column"><input type="checkbox" /></th>
                    <th scope="col"><?php echo __('Context', 'sitepress') ?></th>
                    <th scope="col"><?php echo __('Name', 'sitepress') ?></th>
                    <th scope="col"><?php echo __('View', 'sitepress') ?></th>
                    <th scope="col"><?php echo __('String', 'sitepress') ?></th>
                    <th scope="col"><?php echo __('Status', 'sitepress') ?></th>        
                </tr>        
            </tfoot>                
            <tbody>
                <?php if(empty($icl_string_translations)):?> 
                <tr>
                    <td colspan="6" align="center"><?php echo __('No strings found', 'sitepress')?></td>
                </tr>
                <?php else: ?>
                <?php foreach($icl_string_translations as $string_id=>$icl_string): ?> 
                <tr valign="top">
                    <td><input class="icl_st_row_cb" type="checkbox" value="<?php echo $string_id ?>" /></td>
                    <td><?php echo htmlspecialchars($icl_string['context']); ?></td>
                    <td><?php echo htmlspecialchars(_icl_st_hide_random($icl_string['name'])); ?></td>
                    <td nowrap="nowrap">
                        <?php if($icl_strings_in_page[ICL_STRING_TRANSLATION_STRING_TRACKING_TYPE_SOURCE][$string_id]): ?>
                            <a class="thickbox" title="<?php _e('view in source', 'sitepress') ?>"
                                href="admin.php?page=sitepress-multilingual-cms%2Fmenu%2Fstring-translation.php&icl_action=view_string_in_source&string_id=<?php 
                                echo $string_id ?>&width=810&height=600"><img src="<?php echo ICL_PLUGIN_URL ?>/res/img/view-in-source.png" width="16" height="16"
                                alt="<?php _e('view in page', 'sitepress') ?>" /></a>
                        <?php endif; ?>
                        <?php if($icl_strings_in_page[ICL_STRING_TRANSLATION_STRING_TRACKING_TYPE_PAGE][$string_id]): ?>
                            <a class="thickbox" title="<?php _e('view in page', 'sitepress') ?>"
                            href="admin.php?page=sitepress-multilingual-cms%2Fmenu%2Fstring-translation.php&icl_action=view_string_in_page&string_id=<?php 
                            echo $string_id ?>&width=810&height=600"><img src="<?php echo ICL_PLUGIN_URL ?>/res/img/view-in-page.png" width="16" height="16" 
                            alt="<?php _e('view in page', 'sitepress') ?>" /></a>                        
                        <?php endif; ?>
                    </td> 
                    <td width="70%">                                        
                        <div class="icl-st-original" style="float:left;">                    
                        <?php echo htmlspecialchars($icl_string['value']); ?>                    
                        </div>                    
                        <div style="float:right;">
                            <a href="#icl-st-toggle-translations"><?php echo __('translations','sitepress') ?></a>
                        </div>
                        <br clear="all" />
                        <div class="icl-st-inline">                        
                            <?php foreach($active_languages as $lang): if($lang['code'] == $sitepress->get_current_language()) continue;  ?>
                            <form class="icl_st_form" name="icl_st_form_<?php echo $string_id ?>" action="">
                            <input type="hidden" name="icl_st_language" value="<?php echo $lang['code'] ?>" />                        
                            <input type="hidden" name="icl_st_string_id" value="<?php echo $string_id ?>" />                        
                            <table class="icl-st-table">
                                <?php                                
                                    if(isset($icl_string['translations'][$lang['code']]) && $icl_string['translations'][$lang['code']]['status'] == ICL_STRING_TRANSLATION_COMPLETE){
                                        $tr_complete_checked = 'checked="checked"';
                                    }else{
                                        $tr_complete_checked = '';
                                    }
                                ?>
                                <tr>
                                    <td style="border:none">
                                        <?php echo $lang['display_name'] ?>                                        
                                        <br />
                                        <img class="icl_ajx_loader" src="<?php echo ICL_PLUGIN_URL ?>/res/img/ajax-loader.gif" style="float:left;display:none;position:absolute;margin:5px" alt="" />
                                        <textarea rows="<?php echo ceil(strlen($icl_string['value'])/80) ?>" cols="40" name="icl_st_translation" style="width:100%" <?php if(isset($icl_string['translations'][$lang['code']])): ?>id="icl_st_ta_<?php echo $icl_string['translations'][$lang['code']]['id'] ?>"<?php endif;?>><?php 
                                            if(isset($icl_string['translations'][$lang['code']])) echo $icl_string['translations'][$lang['code']]['value']; else echo $icl_string['value']; 
                                            ?></textarea>                                        
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right" style="border:none">                                    
                                        <?php if(preg_match('#<([^>]*)>#im',$icl_string['value'])):?>
                                        <div style="text-align:left;display:none" class="icl_html_preview"></div>
                                        <a href="#" class="alignleft icl_htmlpreview_link">HTML preview</a>
                                        <?php endif; ?>                                    
                                        <label><input type="checkbox" name="icl_st_translation_complete" value="1" <?php echo $tr_complete_checked ?> <?php if(isset($icl_string['translations'][$lang['code']])): ?>id="icl_st_cb_<?php echo $icl_string['translations'][$lang['code']]['id'] ?>"<?php endif;?> /> <?php echo __('Translation is complete','sitepress')?></label>&nbsp;
                                        <input type="submit" class="button-secondary action" value="<?php echo __('Save', 'sitepress')?>" />
                                    </td>
                                </tr>
                                </table>
                                </form>
                                <?php endforeach;?>
                        </div>
                    </td>
                    <td nowrap="nowrap" id="icl_st_string_status_<?php echo $string_id ?>">
                    <?php
                        $icl_status = icl_translation_get_string_translation_status($string_id);
                        if ($icl_status != "") {
                            $icl_status = '<br /><span class="meta_comment">' . __('ICanLocalize ', 'sitepress').$icl_status . '</span>';
                        }
                        echo $icl_st_string_translation_statuses[$icl_string['status']].$icl_status;
                    ?>    
                    </td>
                </tr>            
                <?php endforeach;?>
                <?php endif; ?>
            </tbody>
        </table>      
                    
        <?php if($wp_query->found_posts > 10): ?>
            <div class="tablenav" style="width:70%;float:right;">            
            <?php    
            $page_links = paginate_links( array(
                'base' => add_query_arg('paged', '%#%' ),
                'format' => '',
                'prev_text' => '&laquo;',
                'next_text' => '&raquo;',
                'total' => $wp_query->max_num_pages,
                'current' => $_GET['paged'],
                'add_args' => isset($icl_translation_filter)?$icl_translation_filter:array() 
            ));         
            ?>
            <?php if(isset($_GET['show_results']) && $_GET['show_results']=='all'): ?>
            <div class="tablenav-pages">                
            <a href="admin.php?page=<?php echo $_GET['page'] ?><?php if(isset($_GET['context'])) echo '&amp;context='.$_GET['context'];?><?php if(isset($_GET['status'])) echo '&status='.$_GET['status'];?>"><?php printf(__('Display %d results per page', 'sitepress'), $sitepress_settings['st']['strings_per_page']); ?></a>
            </div>
            <?php endif; ?>            

            <div class="tablenav-pages"> 
                <?php if ( $page_links ): ?>               
                <?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s', 'sitepress' ) . '</span>%s',
                    number_format_i18n( ( $_GET['paged'] - 1 ) * $wp_query->query_vars['posts_per_page'] + 1 ),
                    number_format_i18n( min( $_GET['paged'] * $wp_query->query_vars['posts_per_page'], $wp_query->found_posts ) ),
                    number_format_i18n( $wp_query->found_posts ),
                    $page_links
                    ); echo $page_links_text; 
                ?>                                         
                <?php endif; ?>
                <?php if(!isset($_GET['show_results'])): ?>
                <?php echo __('Strings per page:', 'sitepress')?>
                <select name="icl_st_per_page" onchange="location.href='admin.php?page=<?php echo $_GET['page']?>&amp;strings_per_page='+this.value">
                    <option value="10"<?php if($sitepress_settings['st']['strings_per_page']==10) echo ' selected="selected"'; ?>>10</option>
                    <option value="20"<?php if($sitepress_settings['st']['strings_per_page']==20) echo ' selected="selected"'; ?>>20</option>
                    <option value="50"<?php if($sitepress_settings['st']['strings_per_page']==50) echo ' selected="selected"'; ?>>50</option>
                    <option value="100"<?php if($sitepress_settings['st']['strings_per_page']==100) echo ' selected="selected"'; ?>>100</option>
                </select>
                &nbsp;
                <a href="admin.php?page=<?php echo $_GET['page'] ?>&amp;show_results=all<?php if(isset($_GET['context'])) echo '&amp;context='.$_GET['context'];?><?php if(isset($_GET['status'])) echo '&amp;status='.$_GET['status'];?>"><?php echo __('Display all results', 'sitepress'); ?></a>
                <?php endif; ?>
            </div>
            
            </div>
        <?php endif; ?>    
    
        <span class="subsubsub">
            <input type="button" class="button-secondary" id="icl_st_delete_selected" value="<?php echo __('Delete selected strings', 'sitepress') ?>" disabled="disabled" />
            <span style="display:none"><?php echo __("Are you sure you want to delete these strings?\nTheir translations will be deleted too.",'sitepress') ?></span>
        </span>
        
        <br clear="all" />    
        
        <?php if($icl_st_translation_enabled): ?>
            <h4><?php _e('Translation by ICanLocalize', 'sitepress') ?></h4>
            <p><?php _e('You can send all the untranslated strings for translation by ICanLocalize.', 'sitepress') ?></p>
            <form method="post" id="icl_st_review_strings" name="icl_st_review_strings" action="">
            <input type="hidden" name="icl_st_action" value="preview" />
            <input type="hidden" name="strings" value="" />
            <input type="hidden" name="langs" value="" />            
            <input type="hidden" name="icl-tr-from" value="<?php echo $sitepress->get_current_language()?>" />
            <ul id="icl-tr-opt">
                <?php
                    $icl_lang_status = $sitepress_settings['icl_lang_status'];
                    if (isset($icl_lang_status)){
                        foreach($icl_lang_status as $lang){
                            if($lang['from'] == $sitepress->get_current_language()) {
                                $target_status[$lang['to']] = $lang['have_translators'];
                            }
                        }
                    }
                ?>
                <?php $_one_lang_enabled = false; ?>
                <?php foreach($active_languages as $lang): if($sitepress->get_current_language()==$lang['code']) continue; ?>
                    <?php if($sitepress_settings['language_pairs'] && isset($sitepress_settings['language_pairs'][$sitepress->get_current_language()][$lang['code']])): ?>
                        <?php if(isset($target_status[$lang['code']]) && $target_status[$lang['code']] == 1): $_one_lang_enabled = true;?>
                            <li><label><input type="checkbox" name="icl-tr-to-<?php echo $lang['code']?>" value="<?php echo $lang['english_name']?>" checked="checked" />&nbsp;<?php printf(__('Translate to %s %s','sitepress'), $lang['display_name'], $sitepress->get_language_status_text($sitepress->get_current_language(), $lang['code'])); ?></label></li>
                        <?php else:  ?>
                            <li><label><input type="checkbox" name="icl-tr-to-<?php echo $lang['code']?>" value="<?php echo $lang['english_name']?>" disabled="disabled" />&nbsp;<?php printf(__('Translate to %s','sitepress'), $lang['display_name'], $lang['code']); ?><span class="icl-tr-not-avail-to" id="icl-tr-not-avail-to-<?php echo $lang['code']?>"><?php echo $sitepress->get_language_status_text($sitepress->get_current_language(), $lang['code']); ?></span></label></li>
                        <?php endif; ?>
                    <?php else:  ?>
                        <li><label><input type="checkbox" name="icl-tr-to-<?php echo $lang['code']?>" value="<?php echo $lang['english_name']?>" disabled="disabled" />&nbsp;<?php printf(__('Translate to %s','sitepress'), $lang['display_name'] . __(' - This language has not been selected for translation by ICanLocalize', 'sitepress')); ?></label></li>
                    <?php endif; ?>
                <?php endforeach; ?>    
            </ul>  

            <span class="subsubsub">                
                <input type="submit" class="button-secondary" id="icl_st_send_selected" value="<?php echo __('Send selected strings to ICanLocalize', 'sitepress') ?>" disabled="disabled" />                    
                <input type="button" class="button-primary" id="icl_st_send_need_translation" value="<?php echo __('Send all strings that need update to ICanLocalize', 'sitepress') ?>" <?php if(!$_one_lang_enabled):?>disabled="disabled"<?php endif;?> />                                     
            </span><br />
            <span id="icl_st_send_progress" class="icl_ajx_response" style="display:none;float:left;"><?php echo __('Sending translation requests. Please wait!', 'sitepress') ?>&nbsp;<img src="<?php echo ICL_PLUGIN_URL ?>/res/img/ajax-loader.gif" alt="loading" /></span>
                        
            <?php if(isset($sitepress_settings['icl_balance'])): ?>
            <br clear="all" />
            <p>
                <?php echo sprintf(__('Your balance with ICanLocalize is %s. Visit your %sICanLocalize finance%s page to deposit additional funds.', 'sitepress'),
                                      '$'.$sitepress_settings['icl_balance'],
                                      $sitepress->create_icl_popup_link(ICL_API_ENDPOINT.ICL_FINANCE_LINK, 'ICanLocalize'),
                                      '</a>',
                                      'sitepress')?>
            </p>
            <br />
            <?php endif; ?>
        </form>    
        <?php else: ?>
    
            <div class="error" style="margin: 1em;">
            <p><?php _e('To translate yourself, click on the <b>Translation</b> link next to each strings.','sitepress') ?></p>
            <?php if ( current_user_can('manage_options') ): ?>
            <p><?php printf(__('To send these strings to translation by ICanLocalize you first need to set up <a href="%s">professional translation</a>.' , 'sitepress'), 'admin.php?page='.basename(ICL_PLUGIN_PATH).'/menu/content-translation.php'); ?></p>
            <?php endif; ?>
            </div>
    
        <?php endif; ?>    
    
        <br style="clear:both;" />
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="metabox-holder">
            
                <div class="postbox-container" style="width: 49%;">
                    <div id="normal-sortables-stsel" class="meta-box-sortables ui-sortable">
                        <div id="dashboard_wpml_stsel" class="postbox">
                            <div class="handlediv" title="<?php echo __('Click to toggle', 'sitepress'); ?>">
                                <br/>
                            </div>
                            <h3 class="hndle">
                                <span><?php echo __('Track where string appear on the site', 'sitepress')?></span>
                            </h3>         
                            <div class="inside">
                                <p class="sub"><?php echo __("WPML can keep track of where strings are used on the public pages. Activating this feature will enable the 'view in page' functionality and make translation easier.", 'sitepress')?></p>
                                <form id="icl_st_more_options" name="icl_st_more_options" action="">
                                    <p class="icl_form_errors" style="display:none"></p>
                                    <ul>
                                        <li>
                                            <li><input type="hidden" name="icl_st[track_strings]" value="0" />
                                            <label><input type="checkbox" id="icl_st_track_strings" name="icl_st[track_strings]" value="1" <?php 
                                            if($sitepress_settings['st']['track_strings']): ?>checked="checked"<?php endif ?> /> 
                                        <?php _e('Track where strings appear on the site', 'sitepress'); ?></label>
                                        </li>
                                        <li>
                                            <label>
                                                <?php _e('Highlight color for strings', 'sitepress'); ?>
                                                <?php $hl_color = $sitepress_settings['st']['hl_color']?$sitepress_settings['st']['hl_color']:'#FFFF00'; ?>
                                                <input type="text" size="7" id="icl_st_hl_color" name="icl_st[hl_color]" value="<?php echo $hl_color ?>" 
                                                    style="background-color:<?php echo $hl_color ?>" />
                                            </label>
                                            <img src="<?php echo ICL_PLUGIN_URL; ?>/res/img/icon_color_picker.png" id="icl_st_hl_picker" 
                                                alt="" border="0" style="vertical-align:bottom;cursor:pointer;" class="pick-show" 
                                                onclick="cp.show('icl_st_hl_color');return false;" />
                                        </li>
                                    </ul>
                                    <p>
                                    <input class="button-secondary" type="submit" name="iclt_st_save" value="<?php _e('Apply', 'sitepress')?>" />
                                    <span class="icl_ajx_response" id="icl_ajx_response2" style="display:inline"></span>
                                    </p>
                                </form>
                                                               
                            </div>           
                        </div>
                        
                        
                        <div id="dashboard_wpml_stsel" class="postbox">
                            <div class="handlediv" title="<?php echo __('Click to toggle', 'sitepress'); ?>">
                                <br/>
                            </div>
                            <h3 class="hndle">
                                <span><?php echo __('Translate general settings texts', 'sitepress')?></span>
                            </h3>         
                            <div class="inside">
                                <p class="sub"><?php echo __('WPML can translate texts entered in different admin screens. Select which texts to translate.', 'sitepress')?></p>
                                <form id="icl_st_sw_form" name="icl_st_sw_form" method="post" action="">
                                    <p class="icl_form_errors" style="display:none"></p>
                                    <ul>
                                        <li><label><input type="checkbox" name="icl_st_sw[blog_title]" value="1" <?php if($sitepress_settings['st']['sw']['blog_title']): ?>checked="checked"<?php endif ?> /> 
                                            <?php echo __('Blog Title', 'sitepress'); ?></label></li>
                                        <li><label><input type="checkbox" name="icl_st_sw[tagline]" value="1" <?php if($sitepress_settings['st']['sw']['tagline']): ?>checked="checked"<?php endif ?> /> 
                                            <?php echo __('Tagline', 'sitepress'); ?></label></li>
                                        <li><label><input type="checkbox" name="icl_st_sw[widget_titles]" value="1" <?php if($sitepress_settings['st']['sw']['widget_titles']): ?>checked="checked"<?php endif ?> /> 
                                            <?php echo __('Widget titles', 'sitepress'); ?></label></li>
                                        <li><label><input type="checkbox" name="icl_st_sw[text_widgets]" value="1" <?php if($sitepress_settings['st']['sw']['text_widgets']): ?>checked="checked"<?php endif ?> /> 
                                            <?php echo __('Content for text-widgets', 'sitepress'); ?></label></li>
                                    </ul>
                                    <p>
                                    <input class="button-secondary" type="submit" name="iclt_st_sw_save" value="<?php echo __('Save options and rescan strings', 'sitepress')?>" />
                                    <span class="icl_ajx_response" style="display:inline"><?php if(isset($_GET['updated']) && $_GET['updated']=='true') echo __('Settings saved', 'sitepress') ?></span>
                                    </p>
                                </form> 
                                                               
                            </div>           
                        </div>                        
                        
                    </div>
                </div>
                
                <div class="postbox-container" style="width: 49%;">
                    <div id="normal-sortables-poie" class="meta-box-sortables ui-sortable">
                        <div id="dashboard_wpml_st_poie" class="postbox">
                            <div class="handlediv" title="<?php echo __('Click to toggle', 'sitepress'); ?>">
                                <br/>
                            </div>
                            <h3 class="hndle">
                                <span><?php echo __('Import / export .po', 'sitepress')?></span>
                            </h3>         
                            <div class="inside">
                                <h5><?php echo __('Import', 'sitepress')?></h5>                         
                                <form id="icl_st_po_form"  name="icl_st_po_form" method="post" enctype="multipart/form-data">
                                    <p class="sub">
                                         <label for="icl_po_file"><?php echo __('.po file:', 'sitepress')?></label>
                                        <input id="icl_po_file" class="button primary" type="file" name="icl_po_file" />  
                                    </p>
                                    <p class="sub" style="line-height:2.3em">
                                        <input type="checkbox" name="icl_st_po_translations" id="icl_st_po_translations" />
                                        <label for="icl_st_po_translations"><?php echo __('Also create translations according to the .po file', 'sitepress')?></label>
                                        <select name="icl_st_po_language" id="icl_st_po_language" style="display:none">
                                        <?php foreach($active_languages as $al): if($al['code']==$sitepress->get_default_language()) continue; ?>
                                        <option value="<?php echo $al['code'] ?>"><?php echo $al['display_name'] ?></option>
                                        <?php endforeach; ?>
                                        </select>
                                    </p>           
                                    <p class="sub" style="line-height:2.3em"    >
                                        <?php echo __('Select what the strings are for:', 'sitepress');?>
                                        <?php if(!empty($available_contexts)): ?>
                                        
                                        &nbsp;&nbsp;
                                        <span>                                        
                                        <select name="icl_st_i_context">
                                            <option value="">-------</option>
                                            <?php foreach($available_contexts as $v):?>
                                            <option value="<?php echo htmlspecialchars($v)?>" <?php if($context_filter == $v ):?>selected="selected"<?php endif;?>><?php echo $v; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <a href="#" onclick="var __nxt = jQuery(this).parent().next(); jQuery(this).prev().val(''); jQuery(this).parent().fadeOut('fast',function(){__nxt.fadeIn('fast')});return false;"><?php echo __('new','sitepress')?></a>
                                        </span>
                                        <?php endif; ?>
                                        <span <?php if(!empty($available_contexts)):?>style="display:none"<?php endif ?>>                                        
                                        <input type="text" name="icl_st_i_context_new" />
                                        <?php if(!empty($available_contexts)):?>
                                        <a href="#" onclick="var __prv = jQuery(this).parent().prev(); jQuery(this).prev().val(''); jQuery(this).parent().fadeOut('fast',function(){__prv.fadeIn('fast')});return false;"><?php echo __('select from existing','sitepress')?></a>
                                        <?php endif ?>
                                        </span>                                        
                                    </p>  
                                    
                                    <p>
                                    <input class="button" name="icl_po_upload" id="icl_po_upload" type="submit" value="<?php echo __('Submit', 'sitepress')?>" />        
                                    <span id="icl_st_err_domain" class="icl_error_text" style="display:none"><?php echo __('Please enter a context!', 'sitepress')?></span>
                                    <span id="icl_st_err_po" class="icl_error_text" style="display:none"><?php echo __('Please select the .po file to upload!', 'sitepress')?></span>
                                    </p>
                                    
                                </form>       
                                <?php if(!empty($icl_contexts)):?>
                                <h5><?php echo __('Export strings into .po/.pot file', 'sitepress')?></h5>                         
                                <form method="post" action="">
                                <p>
                                    <?php echo __('Select context:', 'sitepress')?>
                                    <select name="icl_st_e_context" id="icl_st_e_context">
                                        <option value="" <?php if($context_filter === false ):?>selected="selected"<?php endif;?>><?php echo __('All contexts', 'sitepress') ?></option>
                                        <?php foreach($icl_contexts as $v):?>
                                        <option value="<?php echo htmlspecialchars($v->context)?>" <?php if($context_filter == $v->context ):?>selected="selected"<?php endif;?>><?php echo $v->context . ' ('.$v->c.')'; ?></option>
                                        <?php endforeach; ?>
                                    </select>   
                               </p>
                               <p style="line-height:2.3em">     
                                    <input type="checkbox" name="icl_st_pe_translations" id="icl_st_pe_translations" checked="checked" value="1" onchange="if(jQuery(this).attr('checked'))jQuery('#icl_st_e_language').fadeIn('fast'); else jQuery('#icl_st_e_language').fadeOut('fast')" />
                                    <label for="icl_st_pe_translations"><?php echo __('Also include translations', 'sitepress')?></label>                                
                                    <select name="icl_st_e_language" id="icl_st_e_language">
                                    <?php foreach($active_languages as $al): if($al['code']==$sitepress->get_default_language()) continue; ?>
                                    <option value="<?php echo $al['code'] ?>"><?php echo $al['display_name'] ?></option>
                                    <?php endforeach; ?>
                                    </select>                                     
                                </p>  
                                <p><input type="submit" class="button-secondary" name="icl_st_pie_e" value="<?php echo __('Submit', 'sitepress')?>" /></p>                                                                      
                                <?php endif ?>
                                </form>
                            </div>           
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        
        <br clear="all" /><br />
    <?php endif; ?>
    
    <?php do_action('icl_menu_footer'); ?>
    
</div>
