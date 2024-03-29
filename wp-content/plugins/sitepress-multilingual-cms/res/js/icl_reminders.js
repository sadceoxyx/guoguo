jQuery(document).ready(function(){
    // put the reminders after the "advanced switch" if it exists.
    if (jQuery('.icl_advanced_switch').length > 0) {
        jQuery('#icl_reminder_message').insertAfter('.icl_advanced_switch');
        if (jQuery('#icl_update_message').length > 0) {
            jQuery('#icl_update_message').insertAfter('.icl_advanced_switch');
        }
    }
    
    jQuery('#icl_reminder_show').click(icl_show_hide_reminders);

    jQuery('#icl_reminder_message').css({'margin-bottom' : '5px'});
    jQuery('#icl_reminder_message').css({'padding-bottom' : '2px'});
    jQuery('#icl_reminder_message h4').css({'margin-bottom' : '0px'});
    jQuery('#icl_reminder_message h4').css({'margin-top' : '0px'});
    
    if (location.href.indexOf('&icl_refresh_langs') != -1) {
        do_message_refresh = true;
    }
    show_messages();    
    do_message_refresh = false;
});

var do_message_refresh = false;
function show_messages() {
    var command = "icl_ajx_action=icl_messages";
    if (do_message_refresh) {
        command += "&refresh=1";
        do_message_refresh = false;
    }
    jQuery.ajax({
        type: "POST",
        url: icl_ajx_url,
        data: command,
        cache: false,
        success: function(msg){
            spl = msg.split('|');
            if(spl[0]!='0'){
                jQuery('#icl_show_text').html(spl[0]);
                
                jQuery('#icl_reminder_list').html(spl[1]);
                jQuery('#icl_reminder_message').fadeIn();
                icl_tb_init('a.icl_thickbox');
                icl_tb_set_size('a.icl_thickbox');
            } else {
                jQuery('#icl_reminder_message').fadeOut();
            }  
        }
    }); 

}

function icl_tb_init(domChunk) {
    // copied from thickbox.js
    // add code so we can detect closure of popup

    jQuery(domChunk).unbind('click');
    
    jQuery(domChunk).click(function(){
    var t = this.title || this.name || "ICanLocalize Reminder";
    var a = this.href || this.alt;
    var g = this.rel || false;
    tb_show(t,a,g);
    
    do_message_refresh = true;
    jQuery('#TB_window').bind('unload', function(){
        url = location.href;
        if (url.indexOf('content-translation.php') != -1) {
        
            url = url.replace(/&icl_refresh_langs=1/g, '');
            url = url.replace(/&show_config=1/g, '');
            url = url.replace(/#.*/,'');
            if(jQuery('#icl_account_setup').is(':visible')) {
                location.href = url + "&icl_refresh_langs=1&show_config=1"
            } else {
                location.href = url + "&icl_refresh_langs=1"
            }
        } else {           
            if (t == "ICanLocalize Reminder" && do_message_refresh) {
                
                // do_message_refresh will only be true if we close the popup.
                // if the dismiss link is clicked then do_message_refresh is set to false before closing the popup.
                
                jQuery('#icl_reminder_list').html('Refreshing messages  ' + icl_ajxloaderimg);
                show_messages();
                }
            
            if(a.indexOf('after=refresh_langs') != -1) {
            
                icl_refresh_translator_not_available_links();
            }
        }        
        });
    
    this.blur();
    return false;
    });
}


function icl_tb_set_size(domChunk) {
    if (typeof(tb_getPageSize) != 'undefined') {

        var pagesize = tb_getPageSize();
        jQuery(domChunk).each(function() {
            var url = jQuery(this).attr('href');
            url += '&width=' + (pagesize[0] - 150);
            url += '&height=' + (pagesize[1] - 150);
            url += '&tb_avail=1'; // indicate that thickbox is available.
            jQuery(this).attr('href', url);
        });
    }
}

function dismiss_message(message_id) {
    do_message_refresh = false;
    jQuery('#icl_reminder_list').html('Refreshing messages  ' + icl_ajxloaderimg);
    tb_remove();
    
    jQuery.ajax({
        type: "POST",
        url: icl_ajx_url,
        data: "icl_ajx_action=icl_delete_message&message_id=" + message_id,
        async: false,
        success: function(msg){
        }
    }); 
    
    show_messages();
}

function icl_show_hide_reminders() {
    jqthis = jQuery(this);
    if(jQuery('#icl_reminder_list').css('display')=='none'){
        jQuery('#icl_reminder_list').fadeIn();
        jQuery.ajax({
            type: "POST",
            url: icl_ajx_url,
            data: "icl_ajx_action=icl_show_reminders&state=show",
            async: true,
            success: function(msg){
            }
        }); 
    } else {
        jQuery('#icl_reminder_list').fadeOut();
        jQuery.ajax({
            type: "POST",
            url: icl_ajx_url,
            data: "icl_ajx_action=icl_show_reminders&state=hide",
            async: true,
            success: function(msg){
            }
        }); 
        
    }
    jqthis.children().toggle();    
}