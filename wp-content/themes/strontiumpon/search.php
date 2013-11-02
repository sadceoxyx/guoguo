<?php
/*
Template Name: Search Result
*/
?>
<?php get_header(); ?>

<div id="content" class="content-group content-search">
	<div id="content-pad">
		<div class="post-group">
			<div class="content-title">
				<h1><?php _e('Search Results for:'); echo get_search_query(); ?></h1>	
			</div>
			<?php get_template_part('loop','search'); ?>
		</div>
	</div>
</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>