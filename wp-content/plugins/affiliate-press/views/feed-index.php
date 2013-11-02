<div class="wrap">
	<div class="icon32" id="icon-feeds"><br /></div><h2><?php echo __( 'Feeds', 'LDB_AP' ); ?><a class="add-new-h2" href="?page=affiliate_press_add"><?php echo __( 'Add New', 'LDB_AP' ); ?></a></h2>
	<?php $this->AP_getMessage(); ?>
	<div id="ap_body">
		<div id="ap_main">
			<form id="feeds" method="get">
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
<?php
	$feedsTable = new feedsTable();
	$feedsTable->prepare_items();
	$feedsTable->display();
?>
			</form>
		</div>
		<?php $this->AP_sidebar(); ?>
	</div>
</div>