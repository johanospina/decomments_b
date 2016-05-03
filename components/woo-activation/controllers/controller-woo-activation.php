<?php

Class DECOM_Controller_WooActivation extends DECOM_Controller {
	private function redirectToPage( $param = '' ) {
		$prefix = $this->isMultisite();
		$url    = $prefix . admin_url( 'edit-comments.php?page=' . DECOM_PLUGIN_FOLDER . '-index' . $param );
		wp_safe_redirect($url);
	}

	private function redirectToActivationPage( $param = '' ) {
		$prefix = $this->isMultisite();
		$url    = $prefix . admin_url( 'edit-comments.php?page=' . DECOM_PLUGIN_FOLDER . '-index' . $param );
		wp_safe_redirect($url);
	}

	private function isMultisite() {
		$prefix = is_multisite() ? 'network_' : '';
	}


	public function activate( $get, $post ) {
		if ( $ar_errors = $this->validateFields( $post ) ) {
			$get_param = '';
			foreach ( $ar_errors as $er_field => $er ) {
				$get_param .= "&error_$er_field=$er";
			}

			$this->redirectToActivationPage( $get_param );

			return false;
		} else {

			$activation    = DECOM_Loader_MVC::getComponentClass( 'woo-activation', 'woo-activation' );
			$license_class = DECOM_Loader_MVC::getComponentClass( 'woo-activation', 'license-woo' );


			$license_result = $license_class->sendLicenseRequest( $post );


			if ( $license_result ) {
				$activation->saveActivationKey();
				$hooks_handler = DECOM_Loader::getHooksHandler();

				$networkwide = DECOM_Multisite::isNetworkWide();
				$hooks_handler->onActivation( $networkwide );
				$this->redirectToPage( '&result=success' );

				return true;
			} else {
				$this->redirectToActivationPage( '&error=invalid_key' );

				return false;
			}
		}
	}

	private function validateFields( array $post ) {

		if ( ! array_key_exists( 'user_email', $post ) || ! array_key_exists( 'activation_key', $post ) ) {
			$ar_errors['key_exists'] = 1;

			return $ar_errors;
		}

		foreach ( $post as $title => $value ) {
			if ( trim( $value ) == '' ) {
				$ar_errors[ $title ] = 1;
			}
			$post[ $title ] = trim( $value );
		}

		if ( ! is_email( $post['user_email'] ) ) {
			$ar_errors['valid_user_email'] = 1;
		}

		if ( isset( $ar_errors ) ) {
			return $ar_errors;
		}

		return false;
	}
}