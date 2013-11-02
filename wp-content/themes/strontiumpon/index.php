<?php
/*
Template Name: Index Page
*/
?>
<?php get_header(); ?>

<div id="content" class="content-group content-index">
	<div id="content-pad">
		<div class="box box-ads">
			<div class="title">
				<h2>Sponsors</h2>		
			</div>
			<div class="interior">
				<?php padd_theme_widget_sponsors_text(); ?>
			</div>
		</div>   
		<div id="featured" class="box box-featured">
			<div class="title">
				<h2><?php _e('Featured Deals'); ?></h2>		
			</div>
			<div class="interior">
				<?php padd_theme_post_featured_posts(); ?>
			</div>
		</div>
		<div class="post-group append-clear">
			<div class="content-title">
				<h2><?php _e('Latest Deals');?></h2>
			</div>
			<div class="box-ads">
				<?php padd_theme_widget_sponsors_space(); ?>
			</div>
			<?php get_template_part('loop','index'); ?>
		</div>
	</div>
</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>