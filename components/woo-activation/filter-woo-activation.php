<?php

class DECOM_Component_WooActivation_Filter {

	public static function blockOtherComponents() {

		if ( class_exists( 'DECOM_Loader_MVC' ) ) {
			$activation = DECOM_Loader_MVC::getComponentClass( 'woo-activation', 'woo-activation' );

			return !$activation->checkAccess();
		}

		return false;
	}
}