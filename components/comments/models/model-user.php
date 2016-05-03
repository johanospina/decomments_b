<?php

class DECOM_Model_User extends DECOM_Model {
	public function __construct() {
		parent::__construct();
	}

	public function getCurrentUserId() {
		return get_current_user_id();
	}

	public function getCurrentUser() {
		return wp_get_current_user();
	}

	public function userIsLogged() {

	}

	public function getUserIp() {
		return preg_replace( '/[^0-9a-fA-F:., ]/', '', $_SERVER['REMOTE_ADDR'] );
	}

	public function validateUserBy( $field, $value ) {
		if ( is_object( get_user_by( $field, $value ) ) ) {
			return true;
		} else {
			return false;
		}
	}

}