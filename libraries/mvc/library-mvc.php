<?php

class DECOM_Library_Mvc extends DECOM_Library {
	public static function onActivation() {
		//update_option(DECOM_DB_VERSION_OPTION_NAME, DECOM_DB_VERSION);
	}

	public static function onDeactivation() {

	}

	public static function onAutoInclude( $mode = '' ) {
		if ( is_admin() ) {
			// include router
			DECOM_Loader_MVC::includeRouter();
			DECOM_Router::execute();
		}
	}

	public static function onManualInclude( $mode = '' ) {

	}
}