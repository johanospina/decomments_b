<?php

class DECOM_Component_Settings extends DECOM_Component {
	public static function onInit() {

	}

	public static function onActivation() {

	}

	public static function onDeactivation() {

	}

	public static function registerMenuItems() {
		$menu_items = array(
			array(
				'title'                  => __( 'Settings', DECOM_LANG_DOMAIN ),
				'class'                  => __CLASS__,
				'method'                 => 'renderSettings',
				'menu_slug'              => 'index',
				'register_assets_class'  => __CLASS__,
				'register_assets_method' => 'registerAssets'
			)
		);

		return $menu_items;
	}

	public static function includeAssets( $mode = '' ) {
		wp_enqueue_script( 'jquery' );
		DECOM_Application::includeLibrary( 'jquery-easy-ui' );
		DECOM_Application::includeLibrary( 'assets' );
	}

	public static function registerAssets() {
		DECOM_Application::registerLibrary( 'jquery-easy-ui' );
		DECOM_Application::registerLibrary( 'assets' );

		wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_style( 'settings-style', DECOM_COMPONENTS_URL . '/settings/assets/css/decom-settings.css' );

		wp_enqueue_script( 'settings-script', DECOM_COMPONENTS_URL . '/settings/assets/js/decom-settings.js' );


	}

	public static function renderSettings() {
		self::includeAssets();

		$model_options = DECOM_Loader_MVC::getLibraryModel( 'options', 'options' );
		$settings      = $model_options->getOptions();
		$view_settings = DECOM_Loader_MVC::getComponentView( 'settings', 'settings-page' );
		$view_settings->renderSettingsPage( $settings );

	}

	public static function onAdminEnqueueScripts() {
		wp_register_script( 'decom_admin_js', DECOM_PLUGIN_URL . '/admin/assets/js/admin-settings.js' );
	}

	public static function onAdminPrintScripts() {
		wp_enqueue_script( 'decom_admin_js', DECOM_PLUGIN_URL . '/admin/assets/js/admin-settings.js' );
	}

	public static function onWpAjaxDecomEditSettings() {
		$controller = DECOM_Loader_MVC::getComponentController( 'settings', 'settings' );
		$controller->saveSettings( $_GET, $_POST );
	}
}