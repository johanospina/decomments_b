<?php

class DECOM_ApplicationConfiguration {
	/**
	 * Get application configuration
	 *
	 * @return array
	 */
	public function getConfiguration() {
		$configuration = array(
			'libraries'  => array(
				'mvc'                => array(
					'path'       => DECOM_LIBRARIES_PATH . '/mvc',
					'class_name' => 'DECOM_Library_Mvc'
				),
				'options'            => array(
					'path'       => DECOM_LIBRARIES_PATH . '/options',
					'class_name' => 'DECOM_Library_Options'
				)
			),
			'components' => array(
				'woo-activation'        => array(
					'path'       => DECOM_COMPONENTS_PATH . '/woo-activation',
					'class_name' => 'DECOM_Component_WooActivation'
				),
				'multisite-support'     => array(
					'path'       => DECOM_COMPONENTS_PATH . '/multisite-support',
					'class_name' => 'DECOM_Component_MultisiteSupport'
				),
				'comments'              => array(
					'path'       => DECOM_COMPONENTS_PATH . '/comments',
					'class_name' => 'DECOM_Component_Comments'
				),
				'settings'              => array(
					'path'       => DECOM_COMPONENTS_PATH . '/settings',
					'class_name' => 'DECOM_Component_Settings'
				),
				'badges'                => array(
					'path'       => DECOM_COMPONENTS_PATH . '/badges',
					'class_name' => 'DECOM_Component_Badges'
				),
				'notification-messages' => array(
					'path'       => DECOM_COMPONENTS_PATH . '/notification-messages',
					'class_name' => 'DECOM_Component_NotificationMessages'
				),
			)
		);

		return $configuration;
	}
}