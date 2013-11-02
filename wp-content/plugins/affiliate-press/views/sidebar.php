<div class="postbox-container" id="ap_sidebar">
	<div class="metabox-holder">	
		<div class="meta-box-sortables">
<?php
	$donate = '<p>' . __( "Thank you for installing my plugin. Since you've obviously installed it to try and make some money a donation would be highly appreciated.", 'LDB_AP' ) . '</p><p class="ap_center"><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=PMTHMTFJKCP26" target="_blank"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" alt="PayPal - The safer, easier way to pay online!" /></a></p>';
	$this->AP_postbox( 'ap_donate', __( 'Donate!', 'LDB_AP' ), $donate );
	$support = '<p>' . sprintf( __( 'If you are having problems with this plugin, or have any questions, please read and post about them in the <a href="%s">Support forums</a>.', 'LDB_AP' ), 'http://wordpress.org/tags/affiliate-press' ) . '</p>';
	$this->AP_postbox( 'ap_support', __( 'Support?', 'LDB_AP' ), $support );
	$contact = '<ul><li class="lifacebook"><a href="https://www.facebook.com/lucdebrouwernl" target="_blank">' . sprintf( __( 'Like %s on Facebook', 'LDB_AP' ), 'Luc' ) . '</a></li><li class="litwitter"><a href="https://twitter.com/ldebrouwer" target="_blank">' . sprintf( __( 'Follow %s on Twitter', 'LDB_AP' ), 'Luc' ) . '</a></li><li class="limail"><a href="mailto:affiliatepress@lucdebrouwer.nl">' . sprintf( __( 'Contact %s for paid support', 'LDB_AP' ), 'Luc' ) . '</a></li></ul>';
	$this->AP_postbox( 'ap_contact', sprintf( __( 'Contact or follow %s', 'LDB_AP' ), 'Luc De Brouwer' ), $contact );
?>
		</div>
	</div>
</div>
<br class="clr" />