<div class="wrap">
	<div class="icon32" id="icon-feeds"><br /></div><h2><?php echo sprintf( __( 'View feed %s', 'LDB_AP' ), $feed['title'] ); ?></h2>
	<?php $this->AP_getMessage(); ?>
	<div id="ap_body">
		<div id="ap_main">
			<form action="?page=affiliate_press_view&amp;feed=<?php echo $feed['ID']; ?>&amp;action=view&amp;_viewnonce=<?php echo $_GET['_viewnonce']; ?>" method="get">
				<input type="hidden" name="page" value="<?php echo $_GET['page']; ?>" />
				<input type="hidden" name="_viewnonce" value="<?php echo $_GET['_viewnonce']; ?>" />
				<input type="hidden" name="feed" value="<?php echo $feed['ID']; ?>" />
<?php
	$itemsTable = new itemsTable();
	$itemsTable->prepare_items( $items );
	$itemsTable->display();
?>
			</form>
		</div>
		<?php $this->AP_sidebar(); ?>
	</div>
</div>