<?php

class DECOM_Router {
	public static function execute() {
		$component_name  = self::getParam( DECOM_PREFIX . 'com' );
		$controller_name = self::getParam( DECOM_PREFIX . 'c' );
		$action          = self::getParam( DECOM_PREFIX . 'a' );

		if ( $controller_name == '' || $action == '' || $component_name == '' ) {
			return false;
		}

		$controllerObject = DECOM_Loader_MVC::getComponentController( $component_name, $controller_name );

		if ( $controllerObject ) {
			if ( method_exists( $controllerObject, $action ) ) {
				call_user_func_array( array( $controllerObject, $action ), array( $_GET, $_POST ) );
			}
		}
		exit;
	}

	private static function getParam( $param_name ) {
		if ( !array_key_exists( $param_name, $_GET ) ) {
			return false;
		}

		$param = strip_tags( $_GET[$param_name] );

		if ( !preg_match( '/^[-a-zA-Z_]*/', $param ) ) {
			return false;
		}

		return $param;
	}
}

?>