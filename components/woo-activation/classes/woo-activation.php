<?php

class DECOM_WooActivation {
	private $networkwide = true;
	private $auth_key = '&Wy+XB>omu*zT[Y~OtBj.d;8&;LeVgFCJ^iIe}Zg-b1%~Ml-TW7|>%a0R#0c*L{=';
	private $site_url;

	public function __construct() {
		DECOM_Loader::includeMultisite();
		$this->networkwide = DECOM_Multisite::isNetworkWide();
		if ( defined( 'AUTH_KEY' ) ) {
			$this->auth_key = AUTH_KEY;
		}
		if ( is_multisite() ) {
			$this->site_url = get_site_option( 'siteurl' );
		} else {
			$this->site_url = site_url();
		}
	}

	function saveActivationKey() {
		if ( is_multisite() ) {
			$option_key = md5( 'decom_activation_key' );
			update_site_option( $option_key, $this->cacheKey( $this->auth_key, $this->site_url ) );
		} else {
			$option_key = md5( 'decom_activation_key' );
			update_option( $option_key, $this->cacheKey( $this->auth_key, $this->site_url ) );
		}
	}

	public function checkActivationKey() {
		$option_key = md5( 'decom_activation_key' );
		if ( is_multisite() ) {
			$option_data = get_site_option( $option_key );
		} else {
			$option_data = get_option( $option_key );
		}
		if ( $this->cacheKey( $this->auth_key, $this->site_url ) == $option_data ) {
			return true;
		} else {
			return false;
		}
	}

	function dropActivationKey() {
		if ( is_multisite() ) {
			$user_email  = get_site_option( md5( 'decom_user_email' ) );
			$licence_key = get_site_option( md5( 'decom_licence_key' ) );
		} else {
			$user_email  = get_option( md5( 'decom_user_email' ) );
			$licence_key = get_option( md5( 'decom_licence_key' ) );
		}

		$plugin_data = get_plugin_data( DECOM_PLUGIN_PATH . '/decomments.php', $markup = true, $translate = true );
		$url         = DECOM_ACTIVATION_DOMAIN . '/?wc-api=software-api&request=deactivation&email=' . $user_email . '&licence_key=' . $licence_key . '&product_id=' . DECOM_PRODUCT_ID . '&instance=' . $licence_key . '&platform=' . str_ireplace( array(
				'http://',
				'https://'
			), '', site_url() ) . '&version=' . $plugin_data['Version'];

		$response = wp_remote_get( $url );

		$key = md5( 'decom_activation_key' );
		if ( is_multisite() ) {
			return delete_site_option( $key );
		} else {
			return delete_option( $key );
		}
	}

	private function cacheKey( $data, $key ) {
		$cache = $key . $data;

		return md5( $cache );
	}

	public function checkAccess() {
		$token = $this->checkActivationKey();
		if ( ! $token ) {
			return false;
		}

		return true;
	}
}