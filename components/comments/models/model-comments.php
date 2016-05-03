<?php

class DECOM_Model_Comments extends DECOM_Model {

	private $children = array();

	public function __construct() {

		parent::__construct();
		$this->table_badges = $this->prefix . DECOM_TABLE_BADGES;
		$this->table_ucb    = $this->prefix . DECOM_TABLE_USER_COMMENT_BADGES;
		$this->table_social = $this->prefix . DECOM_TABLE_SOCIAL;

	}

	public function edit() {

	}

	public function addCommentReplay( $text_replay, $author_id, $post_id, $id_replay ) {

		$type = array( '%s', '%d', '%d' );
		$data = array(
			'comment_content' => $text_replay,
			'fk_author_id'    => $author_id,
			'fk_post_id'      => $post_id
		);


		if ( $author_id && $post_id && $text_replay ) {
			return $this->insert( $this->table_name, $data, $type );
		}

		return false;
	}

	public function addComments(
		$post_id,
		$author_name = '',
		$author_email = '',
		$author_url = '',
		$comment_content,
		$user_id = 0,
		$comment_parent = 0
	) {

		$modelUser         = DECOM_Loader_MVC::getComponentModel( 'comments', 'user' );
		$comment_author_ip = $modelUser->getUserIp();
		$comment_agent     = $this->getCommentsAgent();
		$comment_date      = current_time( 'mysql' );
		$comment_date_gmt  = current_time( 'mysql', 1 );
		$comment_type      = '';

		# Задаем массив входных параметров:
		$commentData = array(
			'comment_post_ID'      => $post_id,
			'comment_author'       => $author_name,
			'comment_author_email' => $author_email,
			'comment_author_url'   => $author_url,  // - поле домашней веб-страницы отправителя.
			'comment_content'      => $comment_content,
			'user_id'              => $user_id,      // - идентификатор пользователя, 0 = гость.
			'comment_author_IP'    => $comment_author_ip,
			'comment_agent'        => $comment_agent,
			'comment_date'         => $comment_date,
			'comment_date_gmt'     => $comment_date_gmt,
			'comment_type'         => $comment_type,
			'comment_parent'       => $comment_parent,
		);

		$commentData                     = wp_filter_comment( $commentData );
		$commentData['comment_approved'] = $this->allowComment( $commentData );
		if ( ! $commentData['comment_approved'] ) {
			return false;
		}

		$comment_ID = wp_insert_comment( $commentData );

		if ( 'spam' !== $commentData['comment_approved'] ) { // If it's spam save it silently for later crunching
			if ( '0' == $commentData['comment_approved'] ) {
				wp_notify_moderator( $comment_ID );
			}

			$post = get_post( $commentData['comment_post_ID'] ); // Don't notify if it's your own comment

			$model_options   = DECOM_Loader_MVC::getLibraryModel( 'options', 'options' );
			$comments_notify = $model_options->getOption( 'comments_notify' );
			if ( $comments_notify && $commentData['comment_approved'] && ( ! isset( $commentData['user_id'] ) || $post->post_author != $commentData['user_id'] ) ) {
				wp_notify_postauthor( $comment_ID, isset( $commentData['comment_type'] ) ? $commentData['comment_type'] : '' );
			}
		}

		return $comment_ID;
	}

	public function getPostComments( array $args = array() ) {

		if ( count( $args ) <= 0 ) {
			return false;
		}

		$args = array(
			'order' => 'ASC'
		);

		return get_comments( $args );
	}

	public function getComment( $id, $output = ARRAY_A ) {

		return get_comment( $id, $output );
	}

	public function getComments( $id, $max_votes = array() ) {

		if ( count( $max_votes ) > 0 ) {
			//            /var_dump($max_votes);

			/*foreach ($max_votes as $max)
			{
				$max_id[] = $max->comment_ID;
			}*/
			$notIn = ' AND comment_ID NOT IN (' . implode( ',', $max_votes ) . ')';
		} else {
			$notIn = '';
		}

		$sql = 'SELECT *
        FROM ' . $this->wpdb->comments . '
        WHERE `comment_post_ID`="' . $id . '" AND comment_type = "" AND (`comment_approved` = 1 OR `comment_approved` = "trash")' . $notIn . ' ORDER BY comment_date DESC';

		$comments = $this->wpdb->get_results( $sql, OBJECT );

		/*$sql = '
            SELECT b.badge_icon_path, b.badge_name, ucb.fk_user_id
            FROM ' . $this->table_badges . ' as b
            INNER JOIN ' . $this->table_ucb . ' as ucb
            ON b.id = ucb.fk_comment_badge_id';*/

		//$users = $this->selectIndexedRows( $sql, array(), OBJECT, array( 'fk_user_id' ), true );

		/*for ( $i = 0; $i < count( $comments ); $i ++ ) {
			if ( array_key_exists( $comments[ $i ]->user_id, $users ) ) {
				$comments[ $i ]->badges = $users[ $comments[ $i ]->user_id ];
			} else {
				$comments[ $i ]->badges = array();
			}
		}*/

		return $comments;
	}

	public function updateComments( array $params ) {

		return wp_update_comment( $params );
	}

	public function updateCommentsKarma( $comment_ID, $new_comments_karma ) {

		$args                  = array();
		$args['comment_ID']    = $comment_ID;
		$args['comment_karma'] = $new_comments_karma;

		return $this->updateComments( $args );
	}

	public function deleteComments( $comment_id ) {
		if ( deco_comment_get_children_comments( $comment_id ) ) {
			return wp_delete_comment( $comment_id );
		}

		return wp_delete_comment( $comment_id );
	}

	public function addMetaCommentWp( $comment_id, $meta_key, $meta_value, $unique ) {

		return add_comment_meta( $comment_id, $meta_key, $meta_value, $unique );
	}

	public function getMetaCommentWp( $comment_id, $key, $single ) {

		return get_comment_meta( $comment_id, $key, $single );
	}

	public function updateCommentMeta( $comment_id, $meta_key, $meta_value, $prev_value ) {

		return update_comment_meta( $comment_id, $meta_key, $meta_value, $prev_value );
	}

	public function getCommentMeta( $comment_id, $key, $single ) {

		return get_comment_meta( $comment_id, $key, $single );
	}

	public function updatePostMeta( $post_id, $meta_key, $meta_value, $prev_value = true ) {

		return update_post_meta( $post_id, $meta_key, $meta_value, $prev_value );
	}

	public function getPostMeta( $post_id, $key, $single = true ) {

		return get_post_meta( $post_id, $key, $single );
	}

	public function decom_escape( $data ) {

		return $this->wpdb->escape( $data );
	}

	public function getCommentsAgent() {

		return substr( $_SERVER['HTTP_USER_AGENT'], 0, 254 );
	}

	public function validationComments() {

	}

	public function allowComment( array $commentData ) {

		foreach ( $commentData as $key => $value ) {
			$$key = $value;
		}

		// expected_slashed ($comment_post_ID, $comment_author, $comment_author_email, $comment_content)
		$sql = 'SELECT comment_ID FROM ' . $this->wpdb->comments . ' WHERE comment_post_ID = "' . $comment_post_ID . '" AND comment_parent = "' . $comment_parent . '" AND comment_approved != "trash" AND ( comment_author = "' . $comment_author . '" ';
		if ( $comment_author_email ) {
			$sql .= 'OR comment_author_email = "' . $comment_author_email . '" ';
		}
		$sql .= ') AND comment_content = "' . $comment_content . '" LIMIT 1';

		if ( count( $this->wpdb->get_results( $sql ) ) > 0 ) {
			/*TODO dublicate*/
			return false;
		}

		do_action( 'check_comment_flood', $comment_author_IP, $comment_author_email, $comment_date_gmt );

		if ( ! empty( $user_id ) ) {
			$user        = get_userdata( $user_id );
			$post_author = $this->wpdb->get_var( $this->wpdb->prepare( "SELECT post_author FROM {$this->wpdb->posts} WHERE ID = %d LIMIT 1", $comment_post_ID ) );
		}

		if ( isset( $user ) && ( $user_id == $post_author || $user->has_cap( 'moderate_comments' ) ) ) {
			// The author and the admins get respect.
			$approved = 1;
		} else {
			// Everyone else's comments will be checked.
			if ( check_comment( $comment_author, $comment_author_email, $comment_author_url, $comment_content, $comment_author_IP, $comment_agent, $comment_type ) ) {
				$approved = 1;
			} else {
				$approved = 0;
			}
			if ( wp_blacklist_check( $comment_author, $comment_author_email, $comment_author_url, $comment_content, $comment_author_IP, $comment_agent ) ) {
				$approved = 'spam';
			}
		}

		return $approved;
	}

	public function onSubscribeComments( $comments ) {

		$email_list = $this->subscribeComments( $comments );

		$model_options = DECOM_Loader_MVC::getLibraryModel( 'options', 'options' );
		$options       = $model_options->getOptions();

		if ( $email_list['new_post_comment'] || count( $email_list['new_comment_to_comment'] ) > 0 ) {
			require_once DECOM_LIBRARIES_PATH . '/email/email.php';
			$email_class = new DECOM_Email();
//			$email_class = DECOM_Loader_MVC::getLibraryClass( 'email', 'email' );
			$email_class->notyfy_post_comments( $email_list, $comments );
		}
	}

	public function subscribeComments( $data ) {

		$mails            = array( 'new_post_comment' => array(), 'new_comment_to_comment' => '' );
		$new_post_comment = $subscribe_post = $this->getPostMeta( $data->comment_post_ID, '_decom_subscribers', true );
		if ( $new_post_comment ) {
			$mails['new_post_comment'] = $new_post_comment;
		}

		if ( $data->comment_parent ) {
			$subscribe_mail_comments = $this->getCommentMeta( $data->comment_parent, '_decom_subscriber', true );
			if ( $subscribe_mail_comments ) {
				$mails['new_comment_to_comment'] = $subscribe_mail_comments;
			}
		}

		$subscribe_all_post_comments = isset( $_POST['subscribe_all_comments'] ) && $_POST['subscribe_all_comments'] == 'true' ? true : false;
		$subscribe_my_comment        = isset( $_POST['subscribe_my_comment'] ) && $_POST['subscribe_my_comment'] == 'true' ? true : false;

		if ( $subscribe_my_comment ) {
			$this->updateCommentMeta( $data->comment_ID, '_decom_subscriber', $data->comment_author_email, true );
		}

		if ( $subscribe_all_post_comments ) {
			if ( is_array( $subscribe_post ) ) {
				if ( in_array( $data->comment_author_email, $subscribe_post ) ) {
					return $mails;
				}
			}

			$subscribe_post[] = $data->comment_author_email;
			$this->updatePostMeta( $data->comment_post_ID, '_decom_subscribers', $subscribe_post, false );
		}

		return $mails;
	}

	public function getPostMaxCommentKarmaId() {

		$sql = 'SELECT * FROM ' . $this->wpdb->comments . ' WHERE comment_post_ID =:id ORDER BY comment_karma DESC LIMIT 0,2';
	}

	public function getPostMaxCommentKarma( $post_id, $rate, $user_sort ) {

		$sort = ' DESC';
		if ( $user_sort == 'older' ) {
			$sort = ' ASC';
		}
		$sql = 'SELECT * FROM ' . $this->wpdb->comments . '
                WHERE comment_post_ID = %d
                AND comment_approved = 1
                AND comment_parent = 0
                AND comment_karma >= ' . $rate . '
                ORDER BY comment_karma DESC, comment_date' . $sort . '
                LIMIT 0,2';

		$param = array( $post_id );
		$maxim = $this->selectRows( $sql, $param );

		if ( $maxim ) {
			return $maxim;
		}

		return array();
	}

	public function getCommentsByIds( $comment_ids_array ) {

		if ( ! is_array( $comment_ids_array ) ) {
			return false;
		}

		$ids = implode( ',', $comment_ids_array );

		$sql = "SELECT *
        FROM {$this->wpdb->comments}
        WHERE comment_ID IN ({$ids})
        AND comment_approved = 1
        ORDER BY comment_date DESC";

		$comments = $this->wpdb->get_results( $sql, OBJECT );

		$sql = '
            SELECT b.badge_icon_path, b.badge_name, ucb.fk_user_id
            FROM ' . $this->table_badges . ' as b
            INNER JOIN ' . $this->table_ucb . ' as ucb
            ON b.id = ucb.fk_comment_badge_id';

		$users = $this->selectIndexedRows( $sql, array(), null, array( 'fk_user_id' ), true );

		for ( $i = 0; $i < count( $comments ); $i ++ ) {
			if ( array_key_exists( $comments[ $i ]->user_id, $users ) ) {
				$comments[ $i ]->badges = $users[ $comments[ $i ]->user_id ];
			} else {
				$comments[ $i ]->badges = array();
			}
		}

		return $comments;
	}

	public function getChildrenCommentsByParentId( $post_id, $parent_id ) {

		$comments_branch = $this->getCommentsBranchByParentId( $post_id, $parent_id );

		return $this->getCommentsByIds( $comments_branch );
	}

	public function getCommentsBranchByParentId( $post_id, $parent_id ) {

		$comments_branch   = $this->getChildrenHierarchy( $post_id, $parent_id );
		$comments_branch[] = $parent_id;

		return $comments_branch;
	}

	public function getChildrenHierarchy( $post_id, $parent_id ) {

		$this->children = array();

		$comments = $this->getIndexedCommentsForTree( $post_id );

		$this->traverseChildren( $parent_id, $comments );

		return $this->children;
	}

	private function traverseChildren( $parent_id, $comments ) {

		if ( ! array_key_exists( $parent_id, $comments ) ) {
			return false;
		}

		$children = $comments[ $parent_id ];

		for ( $i = 0; $i < count( $children ); $i ++ ) {
			$this->children[] = $children[ $i ]['id'];

			$parent_id = $children[ $i ]['id'];
			$this->traverseChildren( $parent_id, $comments );
		}
	}

	public function getIndexedCommentsForTree( $post_id ) {

		$sql = "SELECT comment_ID as id, comment_parent as parent_id
        FROM {$this->wpdb->comments}
        WHERE comment_post_ID = %d
        AND comment_approved = 1
        ORDER BY comment_date DESC
        ";

		$pdo_params = array(
			$post_id
		);

		$rows = $this->selectRows( $sql, $pdo_params, 'ARRAY_A' );

		$comments = array();

		for ( $i = 0; $i < count( $rows ); $i ++ ) {
			$comments[ $rows[ $i ]['parent_id'] ][] = $rows[ $i ];
		}

		return $comments;
	}

	public function getChildren( $parent_id ) {

		$sql   = 'SELECT * FROM ' . $this->wpdb->comments . ' WHERE comment_parent = %d ORDER BY comment_date DESC';
		$param = array( $parent_id );

		return $this->selectRows( $sql, $param );
	}

	public function isSuperAdmin() {

		$user_id = get_current_user_id();
		if ( is_super_admin( $user_id ) ) {
			return true;
		} else {
			return false;
		}
	}

	public function moderateCommentStatus( $comment_id, $comment_status ) {

		return wp_set_comment_status( $comment_id, $comment_status );
	}

	public function selectSocial( $user_id ) {

		$sql    = 'SELECT provider FROM ' . $this->table_social . ' WHERE user_id = %d';
		$param  = array( $user_id );
		$social = $this->selectRow( $sql, $param, 'ARRAY_A' );
		if ( empty( $social ) ) {
			return false;
		} else {
			return $social['provider'];
		}
	}

	public function saveSocial( $comment_id, $social ) {

		$this->updateCommentMeta( $comment_id, 'social_icon', $social, true );
	}
}