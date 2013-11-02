<?php
/*----------------------------------------------------------------
--------------
Plugin Name: Content Rotator
Plugin URI: http://www.tipsfor.us/
Description: Sample plugin for rotating chunks of custom content.
Author: Everett Griffiths
Version: 0.1
Author URI: http://www.tipsfor.us/
------------------------------------------------------------------
------------*/

//include() or require()n any necessary files here....
include_once ('includes/ContentRotatorWidget.php');
include_once ('includes/ContentRotator.php');

//Tie into wordpress hooks and any functions that could run on load.

add_action('widgets_init', 'ContentRotatorWidget::register_this_widget');

add_action('admin_menu', 'ContentRotatorWidget::add_menu_item');

//EOF.