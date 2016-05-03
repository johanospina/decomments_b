<?php

class DECOM_Component_WooActivation extends DECOM_Component {
	private static $component_name = 'woo-activation';


	public static function onInit() {
		return '';
	}

	public static function onActivation() {
		return '';
	}

	public static function onDeactivation() {
		DECOM_Loader_MVC::getComponentClass( self::$component_name, self::$component_name )->dropActivationKey();
	}

	public static function registerMenuItems() {
		$activation = DECOM_Loader_MVC::getComponentClass( self::$component_name, self::$component_name );

		$menu_items = array();

		if ( ! $activation->checkAccess() ) {
			$menu_items = array(
				array(
					'title'     => 'Activation',
					'class'     => __CLASS__,
					'method'    => 'renderWooActivationIndex',
					'menu_slug' => 'index'
				)
			);
		}

		return $menu_items;
	}

	public static function renderWooActivationIndex() {
		$activation_view = DECOM_Loader_MVC::getComponentView( self::$component_name, 'woo-activation' );
		$activation_view->renderWooActivationPage();
	}
}