<?php

class DECOM_Model_NotificationLanguages extends DECOM_Model {
	public function __construct() {
		parent::__construct();
		$this->table_name = $this->prefix . DECOM_TABLE_NOTIFICATIONS_LANGUAGES;
	}

	public function prepareDB() {
		$query = "
             CREATE TABLE IF NOT EXISTS " . $this->table_name . " (
                 `id` INT NOT NULL AUTO_INCREMENT,
                 `language` VARCHAR(10) NOT NULL,
             PRIMARY KEY (id)
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;";

		return $this->createTable( $query );
	}

	public function insertLanguage( $lang ) {
		return $this->insert( $this->table_name, array( 'language' => $lang ), array( '%s' ) );
	}

	public function getLanguages( $lang = '' ) {
		if ( $lang ) {
			$data  = array( 'id' );
			$where = array( 'language' => $lang );
		} else {
			$data  = array();
			$where = array();
		}

		$res = $this->selectRowsWhere( $this->table_name, $data, $where, array(), 'ARRAY_A' );

		if ( $lang ) {
			if ( $res ) {
				$result = $res[0]['id'];
			} else {
				$result = false;
			}

		} else {
			$result = $res;
		}

		if ( $lang && !$result ) {
			$result = $this->getLanguages( 'en_US' );
		}

		return $result;
	}
}