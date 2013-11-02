<div class="wrap">
	<div class="icon32" id="icon-feeds"><br /></div><h2><?php echo __( 'Affiliate Press', 'LDB_AP' ); ?></h2>
	<?php $this->AP_getMessage(); ?>
	<div id="ap_body">
		<div id="ap_main">
			<div class="metabox-holder">
				<div id="ap_dashboard" class="postbox">
					<div class="handlediv" title="Click to toggle"><br /></div>
					<h3 class="hndle"><span>Dashboard</span></h3>
					<div class="inside">
						<div class="table">
							<p><?php echo __( 'General', 'LDB_AP' ); ?></p>
							<table>
								<tr>
									<td class="first"><a href="edit.php"><?php echo $dashboard['products']; ?></a></td>
									<td><a href="edit.php?post_type=product"><?php echo _n( 'Product', 'Products', $dashboard['products'], 'LDB_AP' ); ?></a></td>
								</tr>
								<tr>
									<td class="first"><a href="edit.php?post_type=page"><?php echo $dashboard['feeds']; ?></a></td>
									<td><a href="admin.php?page=affiliate_press_feeds"><?php echo _n( 'Feed', 'Feeds', $dashboard['feeds'], 'LDB_AP' ); ?></a></td>
								</tr>
							</table>
						</div>
						<div class="table last">
							<p><?php echo __( 'Prices', 'LDB_AP' ); ?></p>
							<table>
								<tr>
									<td class="first"><a href="edit.php"><?php echo $dashboard['multipleprices']; ?></a></td>
									<td><a href="edit.php?post_type=product" class="green"><?php echo _n( 'Product with multiple prices', 'Products with multiple prices', $dashboard['multipleprices'], 'LDB_AP' ); ?></a></td>
								</tr>
								<tr>
									<td class="first"><a href="edit.php?post_type=page"><?php echo $dashboard['oneprice']; ?></a></td>
									<td><a href="edit.php?post_type=product" class="yellow"><?php echo _n( 'Product with one price', 'Products with one price', $dashboard['oneprice'], 'LDB_AP' ); ?></a></td>
								</tr>
								<tr>
									<td class="first"><a href="edit.php?post_type=page"><?php echo $dashboard['noprices']; ?></a></td>
									<td><a href="edit.php?post_type=product" class="red"><?php echo _n( 'Product with zero prices', 'Products with zero prices', $dashboard['noprices'], 'LDB_AP' ); ?></a></td>
								</tr>
							</table>
						</div>
						<span class="clr">&nbsp;</span>
					</div>
				</div>
			</div>
			<p><?php echo __( 'Still thinking about the dashboard layout. :)' ); ?></p>
			<p><?php echo __( 'A help and support section is also in the works.' ); ?></p>
			<p><a href="http://ma.tt/2010/11/one-point-oh/" target="_blank"><?php echo wptexturize( __( '"if you’re not embarrassed when you ship your first version you waited too long" -- Matt Mullenweg' ) ); ?></a></p>
		</div>
		<?php $this->AP_sidebar(); ?>
	</div>
</div>