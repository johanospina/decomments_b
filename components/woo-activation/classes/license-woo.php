<?php

class DECOM_LicenseWoo {
	private function getDomain() {
		DECOM_Loader_MVC::includeComponentClass( 'woo-activation', 'woo-url-helper' );

		return DECOM_UrlHelper::getDomain();
	}

	function sendLicenseRequest( $request ) {
		$user_email  = $request['user_email'];
		$licence_key = $request['activation_key'];

		if ( is_multisite() ) {
			$option_key = md5( 'decom_licence_key' );
			update_site_option( $option_key, $licence_key );
			$option_key = md5( 'decom_user_email' );
			update_site_option( $option_key, $user_email );
		} else {
			$option_key = md5( 'decom_licence_key' );
			update_option( $option_key, $licence_key );
			$option_key = md5( 'decom_user_email' );
			update_option( $option_key, $user_email );
		}
		$plugin_data = get_plugin_data( DECOM_PLUGIN_PATH . '/decomments.php', $markup = true, $translate = true );

		/*		$url = DECOM_ACTIVATION_DOMAIN . '/?wc-api=software-api&request=deactivation&email=' . $user_email . '&licence_key=' . $licence_key . '&product_id=' . DECOM_PRODUCT_ID . '&instance=' . $licence_key . '&platform=' . site_url() . '&version=' . $plugin_data['Version'];
				$response = wp_remote_get( $url );*/


		$url = DECOM_ACTIVATION_DOMAIN . '/?wc-api=software-api&request=activation&email=' . $user_email . '&licence_key=' . $licence_key . '&product_id=' . DECOM_PRODUCT_ID . '&instance=' . $licence_key . '&platform=' . str_ireplace( array(
				'http://',
				'https://'
			), '', site_url() ) . '&version=' . $plugin_data['Version'];


//		$url = DECOM_ACTIVATION_DOMAIN . '/?wc-api=am-software-api&email=' . $user_email . '&licence_key=' . $licence_key . '&request=activation&product_id=decomments&instance=' . $licence_key . '&platform=' . site_url() . '&software_version=' . $plugin_data['Version'];
//		print_r( $url );
		$response = wp_remote_get( $url, array( 'timeout' => 60 ) );

		return $this->checkResponse( $response );
	}

	private function checkResponse( $response ) {
		$res = false;

		if ( array_key_exists( 'response', $response ) ) {
			if ( array_key_exists( 'code', $response['response'] ) && $response['response']['code'] == 200 ) {

				if ( array_key_exists( 'body', $response ) ) {
					$body = json_decode( $response['body'] );
					if ( ! isset( $body->error ) && $body->activated == true ) {
						$res = true;
					} else {
						update_option( 'decomments_last_activate_message', $body->error );
					}
				}
			}
		} else if ( isset( $response->errors ) ) {
			if ( isset( $response->errors['http_request_failed'] ) && count( $response->errors['http_request_failed'] ) > 0 ) {
				foreach ( $response->errors['http_request_failed'] as $error ) {
					update_option( 'decomments_last_activate_message', $error . '. ' );
				}
			}
		}

		return $res;
	}
}