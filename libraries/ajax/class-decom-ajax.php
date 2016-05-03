<?php

class Decom_Ajax {
	public function __construct() {
		add_action( 'wp_ajax_decom_get_comment', array( $this, 'get_comment_by_id' ) );
		add_action( 'wp_ajax_nopriv_decom_get_comment', array( $this, 'get_comment_by_id' ) );

		add_action( 'wp_ajax_decom_save_edit_comment', array( $this, 'save_edit_comment' ) );
		add_action( 'wp_ajax_nopriv_decom_save_edit_comment', array( $this, 'save_edit_comment' ) );

		add_action( 'wp_ajax_deco_block_user', array( $this, 'block_user_comments' ) );

		add_action( 'wp_ajax_delete_all_user_comment', array( $this, 'delete_all_user_comment' ) );

	}

	public function save_edit_comment() {

		$comment_id = $_POST['comment_id'];
		$nonce      = $_POST['_nonce'];
		$content    = urldecode( $_POST['content'] );

		$post['id']      = $comment_id;
		$post['content'] = $content;

		if ( user_can( get_current_user_id(), 'administrator' ) ) {
			$post['is_m'] = 1;
		}

		$comments_controller = DECOM_Loader_MVC::getComponentController( 'comments', 'comments' );
		$comments_controller->editComments( $get, $post );


		$args = array(
			'comment_ID'      => $comment_id,
			'comment_content' => $content,
		);

		die();
	}

	/**
	 * Get source comment for edit
	 */
	public function get_comment_by_id() {

		$comment_id = $_POST['comment_id'];

		$comment         = get_comment( $comment_id );
		$comment_content = $comment->comment_content;
		$comment_content = str_replace( '<blockquote><div><cite>', '[quote]', $comment_content );
		$comment_content = str_replace( '</cite></div></blockquote>', '[/quote]', $comment_content );

		$comment_content = str_replace( '<blockquote><cite>', '[quote]', $comment_content );
		$comment_content = str_replace( '</cite></blockquote>', '[/quote]', $comment_content );

		$res = array(
			'content'     => $comment_content,
			'name_button' => __( 'Save', DECOM_LANG_DOMAIN )
		);
		die( json_encode( $res ) );
	}

	public function block_user_comments() {
		$res['result'] = 'error';
		if ( current_user_can( 'moderate_comments' ) ) {
			$user_id    = $_POST['user_id'];
			$user_email = $_POST['user_email'];

			if ( $user_id ) {
				update_user_meta( $user_id, 'decom_block_user_leave_comment', 1 );
			} elseif ( $user_email ) {
				$decom_blocked_guests_leave_comment = get_option( 'decom_blocked_guests_leave_comment' );

				$decom_blocked_guests_leave_comment[ $user_email ] = 1;
				update_option( 'decom_blocked_guests_leave_comment', $decom_blocked_guests_leave_comment );
			}

			$res                = $this->get_all_user_comments( $user_id, $user_email );
			$res['result']      = 'success';
			$res['comment_ids'] = $res['ids'];
			$res['id_blocks']   = $res['id_blocks'];

			$res['result'] = 'success';
			$res['post']   = $_POST;
		}

		die( json_encode( $res ) );
	}

	/**
	 * Block and Unblock user comment for all blog
	 */
	public function block_unblock_user_comments() {
		$res['result'] = 'error';
		if ( current_user_can( 'moderate_comments' ) ) {
			$user_id    = $_POST['user_id'];
			$user_email = $_POST['user_email'];
			$action     = $_POST['user_action'];

			if ( $action == 'block' ) {
				if ( $user_id ) {
					update_user_meta( $user_id, 'decom_block_user_leave_comment', 1 );
				} elseif ( $user_email ) {
					$decom_blocked_guests_leave_comment = get_option( 'decom_blocked_guests_leave_comment' );

					$decom_blocked_guests_leave_comment[ $user_email ] = 1;
					update_option( 'decom_blocked_guests_leave_comment', $decom_blocked_guests_leave_comment );
				}
			} else {
				if ( $user_id ) {
					update_user_meta( $user_id, 'decom_block_user_leave_comment', 0 );
				} elseif ( $user_email ) {
					$decom_blocked_guests_leave_comment = get_option( 'decom_blocked_guests_leave_comment' );
					if ( isset( $decom_blocked_guests_leave_comment[ $user_email ] ) ) {
						unset( $decom_blocked_guests_leave_comment[ $user_email ] );
					}
					update_option( 'decom_blocked_guests_leave_comment', $decom_blocked_guests_leave_comment );
				}
			}

			$res                = $this->get_all_user_comments( $user_id, $user_email );
			$res['result']      = 'success';
			$res['comment_ids'] = $res['ids'];
			$res['id_blocks']   = $res['id_blocks'];

			$res['result'] = 'success';
			$res['post']   = $_POST;
		}

		die( json_encode( $res ) );
	}

	public function get_all_user_comments( $user_id = 0, $user_email = '' ) {
		global $wpdb;
		if ( $user_id || $user_email ) {
			$query = "SELECT comment_ID FROM {$wpdb->comments} decom ";
			$query .= "WHERE 1=1 ";
			if ( $user_id ) {
				$query .= " AND user_id = $user_id ";

			} else {
				$query .= " AND comment_author_email = $user_email ";
			}

			$query .= " AND NOT EXISTS (SELECT * FROM {$wpdb->comments} WHERE comment_parent = decom.comment_ID AND comment_approved NOT IN ('trash') )";

			$ids = $wpdb->get_col( $query );

			$res['ids'] = $ids;

			foreach ( $ids as $id ) {
				$id_block_comment_arr[] = '#comment-' . $id;
			}
			$res['id_blocks'] = implode( ', ', $id_block_comment_arr );

			return $res;
		}

		return '';

	}

}

new Decom_Ajax();