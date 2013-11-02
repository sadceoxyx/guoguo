<?php
/*
Template Name: Category
*/
?>
<?php get_header(); ?>

<div id="content" class="content-group content-category">
	<div id="content-pad">
		<div class="post-group">
			<div class="content-title">
				<h1 class="title"><?php _e('Posts Under'); single_cat_title(); _e('Category'); ?></h1>
			</div>
			<?php rewind_posts(); ?>		
			<?php get_template_part('loop','category'); ?>
		</div>
	</div>
</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>