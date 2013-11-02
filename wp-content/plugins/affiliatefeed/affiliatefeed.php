<?php
/*
Plugin Name: affiliate feed 
Plugin URI: www.guoguodaigou.com
Description: Plugin that work with certain affiliate network.
Version: 1.0
Author: Yuxuan Xue
Author URI: www.guoguodaigou.com
*/

/*  Copyright 2012  Yuxuan Xue   (email : yuxuan.xue.uw@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
//include() or require() any necessary files here...
include_once('includes/AffiliateFeed.php');

//Tie into wordpress hooks and any functions that could run on load.

add_action('admin_menu', 'AffiliateFeed::add_menu_item');

?>