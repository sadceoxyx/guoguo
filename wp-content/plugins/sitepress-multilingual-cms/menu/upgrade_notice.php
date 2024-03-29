<?php 
$upgrade_lines =  array(
    '1.3.1' => __('translation controls on posts and pages lists', 'sitepress'),
    '1.3.3' => __('huge speed improvements and the ability to prevent loading WPML\'s CSS and JS files', 'sitepress'),
    '1.3.4' => __('you can configure the position and contents of the posts page in the top navigation', 'sitepress'),
    '1.3.5' => __('many bugs fixed and an easy way to show your love for WPML', 'sitepress'),
    '1.4.0' => __('simplified operation for basic usage and for getting professional translation', 'sitepress'),
    '1.5.0' => __('theme compatibility packages, design for language switcher and language fall-back for posts', 'sitepress'),
    '1.5.1' => __('bugs fixed and new support for Headspace2 SEO plugin', 'sitepress'),
    '1.6.0' => __('WPML can now translate other plugins', 'sitepress'),
    '1.7.0' => __('WPML adapts to any WordPress theme', 'sitepress')
    
);

$short_v = implode('.', array_slice(explode('.', ICL_SITEPRESS_VERSION), 0, 3));
if(!isset($upgrade_lines[$short_v])) return;

?>
<br clear="all" />
<div id="icl_update_message" class="updated message fade" style="clear:both;margin-top:5px;">
    <p><?php printf(__('New in WPML %s: <b>%s</b>', 'sitepress'), $short_v, $upgrade_lines[$short_v]); ?></p>
    <p>
        <a href="http://wpml.org/?cat=48"><?php _e('Learn more', 'sitepress')?></a>&nbsp;|&nbsp;
        <a title="<?php _e('Stop showing this message', 'sitepress') ?>" id="icl_dismiss_upgrade_notice" href="#"><?php _e('Dismiss', 'sitepress') ?></a>
    </p>
</div>
