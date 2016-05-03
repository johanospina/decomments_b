<?php

class DECOM_Model_NotificationValues extends DECOM_Model {
	public function __construct() {
		parent::__construct();
		$this->table_name = $this->prefix . DECOM_TABLE_NOTIFICATIONS_VALUES;
	}

	public function prepareDB() {
		$query = "
             CREATE TABLE IF NOT EXISTS " . $this->table_name . " (
                 `id` INT NOT NULL AUTO_INCREMENT,
                 `notification_title` VARCHAR(255) DEFAULT NULL,
                 `notification_text` TEXT NOT NULL,
                 `fk_notification_id` INT NOT NULL,
                 `fk_language_id` INT NOT NULL,
             PRIMARY KEY (id)
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;";

		return $this->createTable( $query );
	}

	public function insertNotificationValues( $notification_title, $notification_text, $notification_id, $language_id ) {
		$data                       = array();
		$data['notification_title'] = $notification_title;
		$data['notification_text']  = $notification_text;
		$data['fk_notification_id'] = $notification_id;
		$data['fk_language_id']     = $language_id;

		$type = array( '%s', '%s', '%d', '%d' );

		return $this->insert( $this->table_name, $data, $type );
	}

	public function updateNotificationsValues( $notification_title, $notification_text, $where ) {
		$data                       = array();
		$data['notification_title'] = $notification_title;
		$data['notification_text']  = $notification_text;

		return $this->update( $this->table_name, $data, $where );
	}

	public function selectNotificationValues( array $select, array $where ) {
		return $this->selectRowsWhere( $this->table_name, $select, $where, array(), 'ARRAY_A' );
	}

	public function getNotificationValues( $notification_id, $language_id ) {
		$select                      = array( 'notification_title', 'notification_text' );
		$where['fk_notification_id'] = $notification_id;
		$where['fk_language_id']     = $language_id;

		$res = $this->selectNotificationValues( $select, $where );

		return $res[0];
	}


}
