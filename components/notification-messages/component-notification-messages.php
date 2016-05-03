<?php

class DECOM_Component_NotificationMessages extends DECOM_Component {
	public static function getTypesNames() {
		return $type = array(
			'alert' => __( 'Notifications', DECOM_LANG_DOMAIN ),
			'email' => __( 'E-mail notification text', DECOM_LANG_DOMAIN )
		);
	}

	public static function onInit() {

	}

	public static function onActivation() {
		$model_notification = DECOM_Loader_MVC::getComponentModel( DECOM_COMPONENT_NOTIFICATION, DECOM_COMPONENT_NOTIFICATION );
		$model_notification->prepareDB();

		$model_notification_language = DECOM_Loader_MVC::getComponentModel( DECOM_COMPONENT_NOTIFICATION, 'notification-languages' );
		$model_notification_language->prepareDB();

		$model_notification_values = DECOM_Loader_MVC::getComponentModel( DECOM_COMPONENT_NOTIFICATION, 'notification-values' );
		$model_notification_values->prepareDB();

		$model_notification_labels = DECOM_Loader_MVC::getComponentModel( DECOM_COMPONENT_NOTIFICATION, 'notification-labels' );
		$model_notification_labels->prepareDB();

		$model_notification->setDefaultParams();
	}

	public static function onDeactivation() {

	}

	public static function includeAssets( $mode = '' ) {
//		wp_enqueue_script( 'jquery' );
//		DECOM_Application::includeLibrary( 'jquery-easy-ui' );
		wp_enqueue_style( 'notification-style', DECOM_COMPONENTS_URL . '/notification-messages/assets/css/decom-notification.css' );
		wp_enqueue_script( 'notification-script', DECOM_COMPONENTS_URL . '/notification-messages/assets/js/decom-notification.js' );
	}

	public static function registerAssets() {
		DECOM_Application::registerLibrary( 'jquery-easy-ui' );
	}

	public static function registerMenuItems() {
		$menu_items = array(
			array(
				'title'                  => __( 'Notifications', DECOM_LANG_DOMAIN ),
				'class'                  => __CLASS__,
				'method'                 => 'renderFormNotification',
				'menu_slug'              => 'form-notification',
				'register_assets_class'  => __CLASS__,
				'register_assets_method' => 'registerAssets'
			)
		);

		return $menu_items;
	}

	public static function renderFormNotification() {
		self::includeAssets();
		$view_notification = DECOM_Loader_MVC::getComponentView( 'notification-messages', 'notification-messages' );
		$view_notification->renderNotificationMessages( self::getTypesNames() );
	}

	public static function onWpAjaxDecomEditNotifications() {
		$controller = DECOM_Loader_MVC::getComponentController( 'notification-messages', 'notification-messages' );
		$get        = $_GET;
		$post       = $_POST;
		$controller->saveNotification( $get, $post );
	}

}