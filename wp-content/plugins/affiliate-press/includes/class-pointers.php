<?php

if( !class_exists( 'LDB_Affiliate_Press_Pointers' ) ) {

	class LDB_Affiliate_Press_Pointers {

		private $pointers = array();
		private $options = array();

		function __construct() {
			add_action( 'admin_enqueue_scripts', array( &$this, 'AP_enqueue' ) );
			add_action( 'admin_print_footer_scripts', array( &$this, 'AP_loadOptions' ), 100 );
			add_action( 'admin_print_footer_scripts', array( &$this, 'AP_addPointers' ), 101 );
			add_action( 'admin_print_footer_scripts', array( &$this, 'AP_printPointers' ), 102 );
		}

		function AP_enqueue() {
			wp_enqueue_style( 'wp-pointer' );
			wp_enqueue_script( 'wp-pointer' );
		}

		function AP_loadOptions() {
			$options = get_option( 'LDB_Affiliate_Press_Pointers', array() );
			$default = array(
				'ap_rp_add' => true
			);
			foreach( $default as $key => $value ) {
				if( isset( $_GET[ $key ] ) )
					$options[ $key ] = false; 
			}
			$options = array_merge( $default, $options );
			update_option( 'LDB_Affiliate_Press_Pointers', $options );
			$this->options = $options;
		}

		function AP_addPointers() {
			$page = false;
			$pointers = array();
			if( isset( $_GET['page'] ) )
				$page = $_GET['page'];
			
			switch( $page ) {
				case 'affiliate_press_add':
					if( isset( $this->options['ap_rp_add'] ) && $this->options['ap_rp_add'] ) {
						$this->pointers[] = array(
							'el' => '.wrap h2',
							'title' => esc_js( __( 'Adding a feed', 'LDB_AP' ) ),
							'content' => '<p>' . esc_js( __( "This is the screen where you add a feed. The name, image, price, link and identifier XPaths you need to enter are relative to the item XPath and should therefor be prefixed with a \'.\'", 'LDB_AP' ) ) . '</p>',
							'position' => 'top',
							'link' => '?page=affiliate_press_add&ap_rp_add',
							'link_text' => esc_js( __( 'I know, stop bugging me.', 'LDB_AP' ) )
						);
					}
				break;
			}
		}

		function AP_printPointers() {
			if( count( $this->pointers ) > 0 ) {
?>
<script type="text/javascript">
// <![CDATA[
	jQuery(document).ready( function($) {
<?php
				foreach( $this->pointers as $pointer ){
?>
		$( '<?php echo $pointer['el']; ?>' ).pointer({
			content: '<h3><?php echo $pointer['title']; ?></h3><?php echo $pointer['content']; ?>',
			position: '<?php echo $pointer['position']; ?>',
			close: function() { }
		}).pointer('open');
<?php
					if( isset( $pointer['link'] ) ) {
?>
		$( '.wp-pointer-buttons a.close' ).after('<a class="close ap_close" href="<?php echo $pointer['link']; ?>"><?php echo $pointer['link_text']; ?></a>');
<?php
					}
				}
?>
	});
// ]]>
</script>
<?php
			}
		}

	}

	$pointers = new LDB_Affiliate_Press_Pointers;

}