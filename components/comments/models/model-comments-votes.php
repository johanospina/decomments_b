<?php

class DECOM_Model_CommentsVotes extends DECOM_Model {
	public function __construct() {
		parent::__construct();
		$this->table_name = $this->prefix . DECOM_TABLE_VOTES;
	}

	public function prepareDB() {
		$query = 'CREATE TABLE IF NOT EXISTS ' . $this->table_name . ' (
             id INT NOT NULL AUTO_INCREMENT,
             fk_comment_id BIGINT(20) UNSIGNED NOT NULL,
             fk_user_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
             vote_user_ip VARCHAR(20) NOT NULL,
             vote_like TINYINT(1),
             vote_dislike TINYINT(1),
             vote_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
             PRIMARY KEY (id)
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;';

		return $this->createTable( $query );
	}

	public function getErrorVoid( $err ) {
		$model_notification = DECOM_Loader_MVC::getComponentModel( 'notification-messages', 'notification-messages' );
		$model_languages    = DECOM_Loader_MVC::getComponentModel( 'notification-messages', 'notification-languages' );

		$locale = $model_notification->getLocale();

		$notification = $model_notification->getNotificationLocale( $err, $locale );

		return $notification->notification_text;
	}

	public function vote( $comment_id, $user_id, $user_ip, $vote_like, $vote_dislike ) {
		if ( ! $user_id && ! $vote_like ) {
			return array( 'error' => apply_filters( 'decomments_unauth_dislike_msg', __( 'Unauthorized users can’t dislike the comment. Please enter the site', DECOM_LANG_DOMAIN ) ) );
		}

		$comment = get_comment( $comment_id );
		if ( ( $user_id && $user_id == $comment->user_id ) || ( ! $comment->user_id && ! $user_id && $comment->comment_author_IP == $user_ip ) ) {
			return array( 'error' => __( 'You can’t vote for your own comment', DECOM_LANG_DOMAIN ) );
		}

		$delete        = false;
		$insert        = false;
		$comment_voice = array();

		$voice = $this->wasThereVoiceFromUser( $comment_id, $user_id, $user_ip, $vote_like, $vote_dislike );
		if ( $voice ) {
			//*если параметр settings['enable dislike'] = true те разрешены дислайки
			if ( $settings = DECOM_Loader_MVC::getLibraryModel( 'options', 'options' )->getOption( 'enable_dislike' ) ) {
				if ( $voice['vote_like'] == $vote_like && $voice['vote_dislike'] == $vote_dislike ) {
					return array( 'error' => __( 'Your vote has been already adopted', DECOM_LANG_DOMAIN ) );
				} else {
					//*delete voice
					$delete = $this->deleteVoice( array( 'id' => $voice['id'] ) );
				}
			} else {
				/*если осталось с прошлого голосования голос -1 его удаляем и создаем новый +1*/
				if ( $voice['vote_dislike'] == 1 ) {
					$delete = $this->deleteVoice( array( 'id' => $voice['id'] ) );
					/*insert*/
					$comment_voice['fk_comment_id'] = $comment_id;
					$comment_voice['fk_user_id']    = $user_id;
					$comment_voice['vote_user_ip']  = $user_ip;
					$comment_voice['vote_like']     = $vote_like;
					$comment_voice['vote_dislike']  = $vote_dislike;
					$comment_voice_type             = array( '%d', '%d', '%s', '%s', '%s' );
					$insert                         = $this->insertVoice( $comment_voice, $comment_voice_type );
				} else {
					$vote_like = 0;
					$delete    = $this->deleteVoice( array( 'id' => $voice['id'] ) );
				}

			}

		} else {
			/*insert*/
			$comment_voice['fk_comment_id'] = $comment_id;
			$comment_voice['fk_user_id']    = $user_id;
			$comment_voice['vote_user_ip']  = $user_ip;
			$comment_voice['vote_like']     = $vote_like;
			$comment_voice['vote_dislike']  = $vote_dislike;
			$comment_voice_type             = array( '%d', '%d', '%s', '%s', '%s' );
			$insert                         = $this->insertVoice( $comment_voice, $comment_voice_type );
		}
		/*karma result*/
		if ( $insert || $delete !== false ) {
			$karma = $this->updateCommentsKarma( $comment_id, $comment->comment_karma, $vote_like );

			return array( 'success' => array( 'cId' => $comment_id, 'voice' => $karma ) );
		} else {
			return array( 'error' => 'Unexpected error' );
		}
	}

	public function updateCommentsKarma( $comment_id, $karma, $vote_like ) {
		if ( $vote_like ) {
			$karma ++;
		} else {
			$karma --;
		}

		$this->setCommentsCarma( $comment_id, $karma );
		$comment_res = get_comment( $comment_id );

		$this->onVoteChanging( $comment_res->user_id, $vote_like );

		return $comment_res->comment_karma;
	}

	public function wasThereVoiceFromUser( $comment_id, $user_id, $user_ip ) {
		$w_rows['fk_comment_id'] = $comment_id;

		if ( $user_id ) {
			$w_rows['fk_user_id'] = $user_id;
		} else {
			$w_rows['vote_user_ip'] = $user_ip;
			$w_rows['fk_user_id']   = $user_id;
		}

		$votes = $this->getVoice( array(), $w_rows );

		if ( $votes ) {
			return $votes[0];
		}

		return false;
	}

	public function setCommentsCarma( $comment_id, $comment_karma ) {
		$model_comments          = DECOM_Loader_MVC::getComponentModel( 'comments', 'comments' );
		$params['comment_ID']    = $comment_id;
		$params['comment_karma'] = $comment_karma;
		$model_comments->updateComments( $params );
	}

	public function insertVoice( array $data, array $type ) {
		return $this->insert( $this->table_name, $data, $type );
	}

	public function getVoice( array $sel_rows = array(), array $w_rows = array() ) {
		return $this->selectRowsWhere( $this->table_name, $sel_rows, $w_rows, array(), 'ARRAY_A' );
	}

	public function updateVoice( $data, $type, $where ) {
		return $this->update( $this->table_name, $data, $type, $where );
	}

	public function deleteVoice( $id_votes ) {
		return $this->deleteWhere( $this->table_name, $id_votes, array( '%d' ) );
	}

	public function choiceVoiceComment( $comment_id ) {
		$voices = $this->getVoice( array( 'vote_like', 'vote_dislike' ), array( 'fk_comment_id' => $comment_id ) );

		return $this->choice( $voices );
	}

	public function choiceVoiceAllComment( $comments ) {
		if ( count( $comments ) > 0 ) {
			$in  = $this->filter_array_comments( $comments );
			$sql = 'SELECT fk_comment_id, vote_like, vote_dislike FROM ' . $this->table_name . ' WHERE fk_comment_id IN (' . $in . ')';

			$votes = $this->selectRows( $sql, array(), 'ARRAY_A' );
			if ( count( $votes ) > 0 ) {
				$new_arr = array();
				foreach ( $votes as $vote ) {
					$new_arr[ $vote['fk_comment_id'] ][] = $vote;
				}

				foreach ( $new_arr as $id_comments => $array_vote ) {
					$choice_votes[ $id_comments ] = $this->choice( $array_vote );
				}

				return $choice_votes;
			}

			return 0;
		}

	}

	public function choiceVoiceAllCommentOnlyLike( $comments ) {
		$vote_like = array();
		if ( count( $comments ) > 0 ) {
			foreach ( $comments as $comment ) {
				$c_id               = $comment->comment_ID;
				$sql                = 'SELECT COUNT(*) FROM ' . $this->table_name . ' WHERE fk_comment_id = ' . $c_id . ' AND vote_like = 1';
				$votes              = $this->selectRows( $sql, array(), 'ARRAY_A' );
				$vote_like[ $c_id ] = $votes[0]['COUNT(*)'];
			}

		}

		return $vote_like;
	}

	public function getMyVoice( $user_id, $comment_id, $user_ip ) {
		if ( $user_id ) {
			$sql   = 'SELECT * FROM ' . $this->table_name . ' WHERE fk_user_id=' . $user_id . ' AND fk_comment_id=' . $comment_id;
			$votes = $this->selectRows( $sql, array(), 'ARRAY_A' );
		} else {
			$sql   = 'SELECT * FROM ' . $this->table_name . ' WHERE vote_user_ip="' . $user_ip . '" AND fk_comment_id=' . $comment_id;
			$votes = $this->selectRows( $sql, array(), 'ARRAY_A' );
		}

		if ( count( $votes ) > 0 ) {
			if ( $votes[0]['vote_like'] ) {
				return '1';
			} else {
				return '-1';
			}
		} else {
			return '0';
		}

	}

	public function user_voice_like( $comments_in ) {
		$user_votes = array();
		if ( count( $comments_in ) > 0 ) {
			$user_ID = get_current_user_id();
			if ( $user_ID ) {
				$and = 'fk_user_id = "' . $user_ID . '"';
			} else {
				$user_ip = $_SERVER['REMOTE_ADDR'];
				$and     = 'vote_user_ip = "' . $user_ip . '" AND fk_user_id = 0';
			}

			$in    = $comments_in;
			$sql   = 'SELECT * FROM ' . $this->table_name . ' WHERE fk_comment_id IN (' . $in . ') AND ' . $and;
			$votes = $this->selectRows( $sql, array(), 'ARRAY_A' );
			if ( count( $votes ) > 0 ) {
				foreach ( $votes as $vote ) {
					$user_votes[ $vote['fk_comment_id'] ] = $vote['vote_like'];
				}
			}
		}

		return $user_votes;
	}

	public function get_user_likes_or_dislikes( $comment_id, $user_id = 0 ) {
		$user_votes = array();
		if ( ! empty( $comment_id ) ) {
			global $wpdb;
			if ( $user_id ) {
				$where = " AND fk_user_id = $user_id";
			}

			$sql = 'SELECT * FROM ' . $this->table_name . ' WHERE fk_comment_id = ' . $comment_id . $where;
			if ( $votes = $wpdb->get_row( $sql ) ) {
				return array( 'likes' => $votes->vote_like, 'dislike' => $votes->vote_dislike );
			}
		}

		return array( 'likes' => 0, 'dislike' => 0 );
	}

	public function filter_array_comments( $comments ) {
		foreach ( $comments as $comment ) {
			$comment_id[] = $comment->comment_ID;
		}

		return implode( ',', $comment_id );
	}

	public function choice( $voices ) {
		$result = 0;
		if ( count( $voices ) > 0 ) {
			foreach ( $voices as $voice ) {
				$result += (int) $voice['vote_like'];
				$result -= (int) $voice['vote_dislike'];
			}
		}

		return $result;
	}

	function max_value( $array ) {
		$max = 0;
		foreach ( $array as $maximum ) {
			if ( $max < $maximum && $maximum > 0 ) {
				$max = $maximum;
			}
		}

		if ( $max ) {
			foreach ( $array as $key => $value ) {
				if ( $value == $max ) {
					$result[] = $key;
				}
			}

			if ( count( $result ) > 1 ) {
				return array_slice( $result, 0, 2 );
			} elseif ( count( $result ) == 1 && count( $array ) == 1 ) {
				return $result;
			} elseif ( count( $result ) == 1 ) {
				$max_second = 0;
				foreach ( $array as $second_pass ) {
					if ( $second_pass > 0 && $second_pass < $max ) {
						if ( $second_pass > $max_second ) {
							$max_second = $second_pass;
						}
					}
				}

				if ( $max_second != 0 ) {
					foreach ( $array as $key => $value ) {
						if ( $value == $max_second ) {
							$result[] = $key;
						}
					}

					if ( count( $result ) > 1 ) {
						return array_slice( $result, 0, 2 );
					}
				}
			}

			return $result;
		}

		return array();
	}

	public function filterCommentsAsMax( $comments, $max_comments_votes ) {

	}

	public function onVoteChanging( $user_id, $vote_like ) {
		$model_badges = DECOM_Loader_MVC::getComponentModel( 'badges', 'badges' );

		if ( $vote_like ) {
			$model_badges->addUserBadgesFromVotes( $user_id, 'like' );
		} else {
			$model_badges->addUserBadgesFromVotes( $user_id, 'dislike' );
		}

	}

	public function cleanVotes( $comment_id ) {

		$this->deleteWhere( $this->table_name, array( 'fk_comment_id' => $comment_id ), array( '%d' ) );
	}

}