<?php

class DECOM_Model_NotificationLabels extends DECOM_Model {
	public function __construct() {
		parent::__construct();
		$this->table_name = $this->prefix . DECOM_TABLE_NOTIFICATION_LABELS;
	}

	public function prepareDB() {
		$query = "
             CREATE TABLE IF NOT EXISTS " . $this->table_name . " (
                 `id` INT NOT NULL AUTO_INCREMENT,
                 `notification_label` TEXT NOT NULL,
                 `fk_notification_id` INT NOT NULL,
                 `fk_language_id` INT NOT NULL,
             PRIMARY KEY (id)
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;";

		return $this->createTable( $query );
	}

	public function insertNotificationLabels( $notification_label, $notification_id, $language_id ) {
		$data['notification_label'] = $notification_label;
		$data['fk_notification_id'] = $notification_id;
		$data['fk_language_id']     = $language_id;

		$type = array( '%s', '%d', '%d' );

		return $this->insert( $this->table_name, $data, $type );
	}

	public function getNotificationLabel( $notification_id, $language_id ) {
		$data                        = array( 'notification_label' );
		$where['fk_notification_id'] = $notification_id;
		$where['fk_language_id']     = $language_id;


		$res = $this->selectRowsWhere( $this->table_name, $data, $where, array(), 'ARRAY_A' );

		return $res[0]['notification_label'];
	}
}