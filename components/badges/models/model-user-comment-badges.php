<?php

class DECOM_Model_UserCommentBadges extends DECOM_Model {
	function __construct() {
		/*parent::__construct();
		$this->table_name = $this->prefix . DECOM_TABLE_USER_COMMENT_BADGES;*/
	}

	public function prepareDB() {
		/*$query = "
             CREATE TABLE IF NOT EXISTS " . $this->table_name . " (
                 id INT NOT NULL AUTO_INCREMENT,
                 fk_user_id INT NOT NULL,
                 fk_comment_badge_id INT NOT NULL,
             PRIMARY KEY (id)
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;";

		return $this->createTable( $query );*/
	}

	public function getBadges( $user_id ) {
		/*$sql = "
            SELECT fk_comment_badge_id
            FROM " . $this->table_name . "
            WHERE fk_user_id = " . $user_id;

		return $this->selectRows( $sql );*/
	}
}