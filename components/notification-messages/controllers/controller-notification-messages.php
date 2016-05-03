<?php

class DECOM_Controller_NotificationMessages extends DECOM_Controller {
	public function saveNotification( $get, $post ) {
		if ( array_key_exists( 'action', $post ) !== false ) {
			unset( $post['action'] );
		}

		if ( $post ) {
			$notification_message = array();
			foreach ( $post as $notification => $text ) {
				$string = '$notifications';
				$st     = explode( '-', $notification );
				foreach ( $st as $val ) {
					$string .= '["' . trim( strip_tags( stripcslashes( $val ) ) ) . '"]';
				}
				$text = trim( $text );

				if ( ! $text ) {
//					echo wp_send_json( array( 'error' => __( 'Changes are not saved! Some fields not completed.', DECOM_LANG_DOMAIN ) ) );
//					exit;
				}
				eval( "$string = \"$text\";" );
			}

			$result = DECOM_Loader_MVC::getComponentModel( DECOM_COMPONENT_NOTIFICATION, DECOM_COMPONENT_NOTIFICATION )->updateNotificationPostValues( $notifications );
			if ( $result ) {
//				echo wp_send_json( array( 'success' => __( 'Changes saved successfully.', DECOM_LANG_DOMAIN ) ) );
			} else {
//				echo wp_send_json( array( 'error' => __( 'Saving error!', DECOM_LANG_DOMAIN ) ) );
			}
//			exit;
		}
	}
}