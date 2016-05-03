<?php

class DECOM_Model_Badges extends DECOM_Model {
	function __construct() {
		parent::__construct();
		$this->table_name     = $this->prefix . DECOM_TABLE_BADGES;
		/*$this->table_name_ucb = $this->prefix . DECOM_TABLE_USER_COMMENT_BADGES;*/
	}

	public function prepareDB() {
		$query = "
             CREATE TABLE IF NOT EXISTS " . $this->table_name . " (
                 id INT NOT NULL AUTO_INCREMENT,
                 badge_name VARCHAR(255) NOT NULL,
                 badge_like_number INT NOT NULL,
                 badge_dislike_number INT NOT NULL,
                 badge_comments_number INT NOT NULL,
                 badge_icon_path VARCHAR(255) NOT NULL,
                 badge_creation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
             PRIMARY KEY (id)
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;";

		return $this->createTable( $query );
	}

	public function getBadges() {
		/*$query =
			"SELECT *
             FROM " . $this->table_name . "
                ORDER BY badge_name";

		return $this->selectRows( $query );*/
	}

	public function getBadge( $badgeId ) {
		/*$query =
			"SELECT *
             FROM " . $this->table_name . "
             WHERE id = $badgeId
             ORDER BY badge_name";

		return $this->selectRow( $query );*/
	}

	public function addBadge( $data = array(), $type = array() ) {
		/*return $this->insert( $this->table_name, $data, $type );*/
	}

	public function editBadge( $id, $data, $type ) {
		/*return $this->update( $this->table_name, $data, $type, array( 'id' => $id ) );*/
	}

	public function deleteBadges( $id ) {
		/*$query = "DELETE FROM " . $this->table_name . " WHERE id = $id";
		return $res = $this->preparedQuery( $query, $data_array );*/
	}

	public function assignBadges( $user_id, $user_votes ) {
		/*$this->deleteWhere( $this->table_name_ucb, array( 'fk_user_id' => $user_id ), array( '%d' ) );

		$badges = $this->getBadges();
		if ( ! $badges || count( $badges ) == 0 || ! $user_votes || count( $user_votes ) == 0 ) {
			return false;
		}
		foreach ( $badges as $badge ) {
			if
			(
				( $badge->badge_like_number != 0 && $badge->badge_like_number <= $user_votes['likes'] ) ||
				( $badge->badge_dislike_number != 0 && $badge->badge_dislike_number <= $user_votes['dislikes'] ) ||
				( $badge->badge_comments_number != 0 && $badge->badge_comments_number <= $user_votes['comments'] )
			) {
				$type = array( '%d', '%d' );
				$data = array( 'fk_user_id' => $user_id, 'fk_comment_badge_id' => $badge->id );
				$this->insert( $this->table_name_ucb, $data, $type );
			}
		}*/
	}

	/**
	 * @param $user_id
	 * @param $type - 'like' || 'dislike'
	 *
	 * @return int
	 */
	public function addUserBadgesFromVotes( $user_id, $type ) {
/*		$table_votes = $this->prefix . DECOM_TABLE_VOTES;
		$data        = array( $user_id );

		// like
		$old_user_badges_like = $this->getOldUserBadges( 'badge_like_number', $data );
		$badges_like          = $this->getBadgesForVotes( 'like', $data );

		$old_user_badges_like = $this->insertBadgeIfNotExists( $user_id, $badges_like, $old_user_badges_like );
		$this->cleanOldBadges( $old_user_badges_like );

		// dislike
		if ( $type == 'dislike' ) {
			$old_user_badges_like = $this->getOldUserBadges( 'badge_dislike_number', $data );
			$badges_dislike       = $this->getBadgesForVotes( 'dislike', $data );

			$old_user_badges_dislike = $this->insertBadgeIfNotExists( $user_id, $badges_dislike, $old_user_badges_like );
			$this->cleanOldBadges( $old_user_badges_dislike );
		}*/

		return 1;
	}

	public function addUserBadgesForComments( $user_id ) {
		/*		$table_comments  = $this->wpdb->comments;
				$data            = array( $user_id );
				$old_user_badges = $this->getOldUserBadges( 'badge_comments_number', $data );
				$sql             = "SELECT COUNT(comment_ID) AS counts
											FROM $table_comments
											WHERE user_id =:user_id AND comment_approved = 1";

				$sql = "SELECT b.id FROM {$this->table_name} b
								INNER JOIN (SELECT COUNT(comment_ID) AS counts
											FROM $table_comments
											WHERE user_id = %d AND comment_approved = 1) c
						 WHERE c.counts >= b.badge_comments_number AND b.badge_comments_number != 0";

				$badges = $this->selectRows( $sql, $data );

				$old_user_badges = $this->insertBadgeIfNotExists( $user_id, $badges, $old_user_badges );

				$this->cleanOldBadges( $old_user_badges );*/
		return 1;
	}

	public function getOldUserBadges( $type, array $data ) {
		/*$sql = "SELECT ucb.id, ucb.fk_comment_badge_id FROM {$this->table_name_ucb} AS ucb
                INNER JOIN (SELECT id FROM {$this->table_name} WHERE $type > 0) AS b ON (b.id = ucb.fk_comment_badge_id)
                WHERE ucb.fk_user_id = %d";

		return $this->selectRows( $sql, $data );*/
		return false;
	}

	public function cleanOldBadges( $old_user_badges ) {
		/*if ( count( $old_user_badges ) > 0 ) {
			foreach ( $old_user_badges AS $b ) {
				$this->deleteRowById( $this->table_name_ucb, $b->id );
			}
		}*/

		return 1;
	}

	public function insertBadgeIfNotExists( $user_id, $badges, $old_user_badges ) {
		/*		if ( count( $badges ) > 0 ) {
					foreach ( $badges AS $badge ) {
						$addBadge = true;
						if ( count( $old_user_badges ) > 0 ) {
							foreach ( $old_user_badges AS $key => $old_badge ) {
								if ( $old_badge->fk_comment_badge_id == $badge->id ) {
									unset ( $old_user_badges[ $key ] );
									$addBadge = false;
									break;
								}
							}
						}

						if ( $addBadge ) {
							$type = array( '%d', '%d' );
							$data = array( 'fk_user_id' => $user_id, 'fk_comment_badge_id' => $badge->id );
							$this->insert( $this->table_name_ucb, $data, $type );
						}

					}
				}

				return $old_user_badges;*/
		return false;
	}

	public function getBadgesForVotes( $type, $data ) {
/*		$table_votes = $this->prefix . DECOM_TABLE_VOTES;
		$sql = "SELECT b.id FROM {$this->table_name} b
                    INNER JOIN (SELECT COUNT(id) as counts FROM $table_votes as v
                                    INNER JOIN {$this->wpdb->comments} as c
                                      ON v.fk_comment_id = c.comment_ID
                                    WHERE c.comment_approved = 1
                                    AND c.user_id = %d
                                    AND v.vote_$type = 1
                                ) AS vote
                 WHERE vote.counts >= b.badge_{$type}_number AND b.badge_{$type}_number != 0";

		return $this->selectRows( $sql, $data );*/
		return false;
	}

}