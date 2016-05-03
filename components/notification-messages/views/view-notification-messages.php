<?php

class DECOM_View_NotificationMessages extends DECOM_View {
	public function renderNotificationMessages( $tabs ) {
		$notifications = array();
		foreach ( $tabs as $type => $tabs_name ) {
			$notifications[ $type ] = $this->getNotifications( $type );

		}
		include_once( dirname( __FILE__ ) . '/templates/page-notification-messages.tpl.php' );
	}

	public function getNotifications( $type ) {

		$model_notifications = DECOM_Loader_MVC::getComponentModel( DECOM_COMPONENT_NOTIFICATION, DECOM_COMPONENT_NOTIFICATION );
		$model_labels        = DECOM_Loader_MVC::getComponentModel( DECOM_COMPONENT_NOTIFICATION, 'notification-labels' );
		$model_values        = DECOM_Loader_MVC::getComponentModel( DECOM_COMPONENT_NOTIFICATION, 'notification-values' );
		$model_languages     = DECOM_Loader_MVC::getComponentModel( DECOM_COMPONENT_NOTIFICATION, 'notification-languages' );

		$defaultLanguage = $model_languages->getLanguages( $model_notifications->getLocale() );

		if ( $type ) {

			$notifications = $model_notifications->getNotification( $type );
			$languages     = $model_languages->getLanguages();
			$result        = array();
			if ( $notifications ) {
				foreach ( $notifications as $notification ) {
					$notification_label = $model_labels->getNotificationLabel( $notification['id'], $defaultLanguage );
					foreach ( $languages as $language ) {
						$notification_tt                                                                                        = $model_values->getNotificationValues( $notification['id'], $language['id'] );
						$result[ $notification['notification_key'] ]['notification_label']                                      = $notification_label;
						$result[ $notification['notification_key'] ]['language'][ $language['language'] ]['notification_title'] = $notification_tt['notification_title'];
						$result[ $notification['notification_key'] ]['language'][ $language['language'] ]['notification_text']  = $notification_tt['notification_text'];
					}
				}

				return $result;
			}
		}
	}
}