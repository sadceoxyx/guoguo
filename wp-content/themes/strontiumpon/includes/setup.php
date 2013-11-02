<?php

if (!function_exists('padd_theme_setup')) {
	function padd_theme_setup() {
		remove_action('wp_head','wp_generator');
		
		add_theme_support('post-thumbnails');
		add_theme_support('automatic-feed-links');
		
		register_nav_menus(array(
			'main' => 'Main Menu',
		));
		
		set_post_thumbnail_size(PADD_LIST_THUMB_W,PADD_LIST_THUMB_H,true);
		add_image_size(PADD_THEME_SLUG . '-thumbnail',PADD_LIST_THUMB_W,PADD_LIST_THUMB_H,true);
		add_image_size(PADD_THEME_SLUG . '-gallery',PADD_GALL_THUMB_W,PADD_GALL_THUMB_H,true);
		add_image_size(PADD_THEME_SLUG . '-related-posts',136,70,true);
		
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('theme', get_stylesheet_directory_uri() . '/js/frontend.js.php', array('jquery'), PADD_THEME_VERS, true);
	}
}
add_action('after_setup_theme', 'padd_theme_setup');

function padd_theme_widgets_init() {
	register_sidebar(array(
		'name' => 'Sidebar',
		'before_widget' => '<div id="%1$s" class="box %2$s">',
		'after_widget' => '</div></div>',
		'before_title' => '<div class="title"><h3>',
		'after_title' => '</h3></div><div class="interior">',
	));
	register_sidebar(array(
		'name' => 'Footer 1',
		'before_widget' => '<div id="%1$s" class="box %2$s">',
		'after_widget' => '</div></div>',
		'before_title' => '<div class="title"><h3>',
		'after_title' => '</h3></div><div class="interior">',
	));
	register_sidebar(array(
		'name' => 'Footer 2',
		'before_widget' => '<div id="%1$s" class="box %2$s">',
		'after_widget' => '</div></div>',
		'before_title' => '<div class="title"><h3>',
		'after_title' => '</h3></div><div class="interior">',
	));
	register_sidebar(array(
		'name' => 'Footer 3',
		'before_widget' => '<div id="%1$s" class="box %2$s">',
		'after_widget' => '</div></div>',
		'before_title' => '<div class="title"><h3>',
		'after_title' => '</h3></div><div class="interior">',
	));
	register_sidebar(array(
		'name' => 'Footer 4',
		'before_widget' => '<div id="%1$s" class="box %2$s">',
		'after_widget' => '</div></div>',
		'before_title' => '<div class="title"><h3>',
		'after_title' => '</h3></div><div class="interior">',
	));
}
add_action('widgets_init', 'padd_theme_widgets_init');

add_filter('excerpt_more', 'padd_theme_hook_excerpt_index_more');
add_filter('get_comments_number', 'padd_theme_hook_count_comments',0);
add_filter('wp_page_menu_args', 'padd_theme_hook_menu_args');
