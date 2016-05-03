<?php

class DECOM_View_WooActivation extends DECOM_View {
	public function renderWooActivationPage() {
		if ( isset( $_GET['error'] ) ) {
			switch ( $_GET['error'] ) {
				case( 'invalid_key' ):
					echo '<div class="error"><p><strong>' . get_option( 'decomments_last_activate_message' ) . '</strong></p></div>';
					break;
				//case('fields'): echo '<div class="error"><p><strong>'.__("Activation fields can't be empty!",DECOM_LANG_DOMAIN ).'</strong></p></div>';
				// break;
			}
		}

		include_once( dirname( __FILE__ ) . '/templates/woo-activation.tpl.php' );
	}
}
