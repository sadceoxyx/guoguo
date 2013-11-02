<div class="wrap">
	<div class="icon32" id="icon-feeds"><br /></div><h2><?php echo __( 'Link item to an existing product', 'LDB_AP' ); ?></h2>
	<div id="ap_body">
		<div id="ap_main">
			<form action="?page=affiliate_press" method="post">
				<?php wp_nonce_field( 'linkto' . $_GET['identifier'], 'wp_nonce_linkto' ); ?>
				<table class="form-table">
					<tr>
						<th scope="row"><label for="identifier"><?php echo __( 'Item name', 'LDB_AP' ); ?></label></th>
						<td><?php echo $_GET['name']; ?><input type="hidden" name="view_referer" id="view_referer" value="<?php echo esc_attr( $view_referer ); ?>" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="identifier"><?php echo __( 'Item identifier', 'LDB_AP' ); ?></label></th>
						<td><?php echo $_GET['identifier']; ?><input type="hidden" name="identifier" id="identifier" value="<?php echo esc_attr( $_GET['identifier'] ); ?>" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="matches"><?php echo __( 'Item matches', 'LDB_AP' ); ?></label></th>
						<td><?php echo $_GET['matches']; ?><input type="hidden" name="matches" id="matches" value="<?php echo esc_attr( $_GET['matches'] ); ?>" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="product"><?php echo __( 'Product', 'LDB_AP' ); ?></label></th>
						<td><?php echo $linkto; ?></td>
					</tr>
				</table>
				<p class="submit"><input type="submit" value="<?php echo __( 'Link to product' ); ?>" class="button-primary" id="submit" name="submit"></p>
			</form>
		</div>
		<?php $this->AP_sidebar(); ?>
	</div>
</div>