<?php

class DECOM_Controller_Comments extends DECOM_Controller {

	public function __construct() {

		$this->modelComments = DECOM_Loader_MVC::getComponentModel( 'comments', 'comments' );
		$this->modelUser     = DECOM_Loader_MVC::getComponentModel( 'comments', 'user' );
		$this->modelPost     = DECOM_Loader_MVC::getComponentModel( 'comments', 'post' );
		$this->modelOptions  = DECOM_Loader_MVC::getLibraryModel( 'options', 'options' );

	}

	public function insertComment( $get, $post ) {

		include ABSPATH . '/wp-comments-post.php';

	}

	public function loadCommentsBlock( $get, $post ) {

		$jScroll_post_id = isset( $get['post_id'] ) ? $get['post_id'] : '';

		if ( $jScroll_post_id && $this->validationPostStatus( $jScroll_post_id ) ) {

			$decom_template_path = DECOM_Loader_MVC::getPathTheme();

			include $decom_template_path . 'comments.php';
		}
	}

	public function postRepley( $status, $result = array(), $error = null ) {
		$response['result'] = $status;
		if ( $status == 'error' ) {
			if ( isset( $error ) ) {
				$response['mesage'] = $this->errorMessages( $error );
				$response['id']     = $error;
			}
		} else {
			$response['data'] = $result;
		}
		wp_send_json( $response );
	}

	public function deleteComment( $get, $post ) {

		$c_id   = isset( $post['id'] ) && (int) $post['id'] ? (int) $post['id'] : '';
		$result = array( 'result' => 'error' );
		if ( $c_id ) {
			$user_id        = get_current_user_id();
			$model_comments = DECOM_Loader_MVC::getComponentModel( 'comments', 'comments' );

			$comments = $model_comments->getComment( $c_id, OBJECT );
			$is_admin = $model_comments->isSuperAdmin();

			$user_ip = $_SERVER['REMOTE_ADDR'];
			$user_ip = preg_replace( '/[^0-9a-fA-F:., ]/', '', $user_ip );

			if ( $is_admin || ( ( $user_id && $comments->user_id == $user_id ) || ( ! $user_id && $comments->comment_author_IP == $user_ip ) ) ) {
				if ( $model_comments->deleteComments( $c_id ) ) {
					$result = array( 'result' => 'success', 'data' => array( 'id' => $c_id ) );
				}
			}
		}
		wp_send_json( $result );
	}

	public function editComments( $get, $post ) {

		$commentId      = isset( $post['id'] ) && (int) $post['id'] ? (int) $post['id'] : '';
		$is_moderator   = isset( $post['is_m'] ) ? (int) $post['is_m'] : 0;
		$model_comments = DECOM_Loader_MVC::getComponentModel( 'comments', 'comments' );

		$comments = $model_comments->getComment( $commentId, OBJECT );
		$is_admin = $model_comments->isSuperAdmin();
		$user_id  = get_current_user_id();
		$user_ip  = $_SERVER['REMOTE_ADDR'];
		$user_ip  = preg_replace( '/[^0-9a-fA-F:., ]/', '', $user_ip );

		$model_comments->onSubscribeComments( $comments );

		if ( $is_admin || ( ( $user_id && $comments->user_id == $user_id ) || ( ! $user_id && $comments->comment_author_IP == $user_ip ) ) ) {
			$commentContent = html_entity_decode( $post['content'] );

			list( $tmp, $qute_content ) = explode( '[quote]', $commentContent );
			list( $qute_content, $tmp ) = explode( '[/quote]', $qute_content );
			$tmp_qute_content = str_replace( ' ', '', $qute_content );
			if ( empty( $tmp_qute_content ) ) {
				$commentContent = str_replace( '[quote]' . $qute_content . '[/quote]', '', $commentContent );
			} else {
				$commentContent = str_replace( '[quote]', '<blockquote><cite>', $commentContent );
				$commentContent = str_replace( '[/quote]', '</cite></blockquote>', $commentContent );
			}
			$commentContent = trim( stripslashes( $commentContent ) );
			$commentContent = strip_tags( $commentContent, '<p><div><blockquote><cite><br>' );
			$delete_picture = false;
			$delete_image   = isset( $post['delete_image'] ) && $post['delete_image'] == true ? true : false;
			$attach_id      = isset( $post['attach_id'] ) ? (int) $post['attach_id'] : '';
			if ( $delete_image && $commentContent ) {
				$delete_picture = $this->deletePicture( $commentId, $attach_id );
			}

			if ( $commentContent == '' || ( $delete_image && ! trim( $commentContent ) && ! isset( $_FILES['decom_pictures']['name'] ) ) ) {
				wp_send_json( array(
					'result'         => 'error',
					'content'        => '',
					'attach_content' => '',
					'id'             => 7,
					'message'        => '<strong>' . __( 'ERROR', DECOM_LANG_DOMAIN ) . '</strong>: ' . $this->errorMessages( 7 )
				) );

			}

			$commentArr     = array(
				'comment_ID'      => $commentId,
				'comment_content' => $commentContent
			);
			$attach_content = '';

			$gmt_offset            = get_option( 'gmt_offset', 0 ) * 60 * 60;
			$comment_modified_time = date( 'H:i d.m.Y', time() + $gmt_offset );
			update_comment_meta( $commentId, 'decom_comment_modified_date', $comment_modified_time );

			if ( $is_moderator && $user_id ) {
				$user     = wp_get_current_user();
				$dis_name = $user->display_name;
				update_comment_meta( $commentId, 'decom_comment_modified_moderator', $dis_name );
			}
			//если ползователь был авторизирован через соц.сеть
			if ( $is_moderator && $user_id ) {
				$user     = wp_get_current_user();
				$dis_name = $user->display_name;
				update_comment_meta( $commentId, 'decom_comment_modified_moderator', $dis_name );
			}


			$commentContent = decom_filter_tags_replace( $commentContent );
			$commentContent = apply_filters( 'decomments_comment_text', $commentContent );
			$result         = array(
				'result'         => 'success',
				'content'        => $commentContent,
				'attach_content' => $attach_content,
				'modified_date'  => $comment_modified_time
			);

			if ( $delete_picture ) {
				$result['deletePicture'] = true;
			}

			global $allowedtags;
			$allowedtags['div'] = array();

			wp_update_comment( $commentArr );

			echo str_replace( array( '<', '>' ), array( '\u003C', '\u003E' ), wp_send_json( $result ) );
		}


	}

	public function verifyEmail( $get, $post ) {

		if ( isset( $post['email'] ) && strlen( trim( $post['email'] ) ) > 4 && ! is_user_logged_in() ) {
			$email_search = trim( $post['email'] );
			if ( is_email( $email_search ) ) {
				$model_comments = DECOM_Loader_MVC::getComponentModel( 'comments', 'user' );
				if ( $model_comments->validateUserBy( 'email', $email_search ) ) {
					$result = array(
						'success' => __( 'This user already exists', DECOM_LANG_DOMAIN ),
						'result'  => 'error'
					);
				} else {
					$result = '';
				}
			} else {
				$result = array(
					'error'  => __( 'Email is incorrect. Please use the ', DECOM_LANG_DOMAIN ),
					'result' => 'error'
				);
			}

		} elseif ( is_user_logged_in() ) {
			$result = array(
				'logged_in' => 'is_logged_user',
				'result'    => 'success'
			);
		} else {
			$result = array(
				'error'  => __( 'Email is incorrect. Please use the ', DECOM_LANG_DOMAIN ),
				'result' => 'error'
			);
		}
		wp_send_json( $result );
	}

	public function errorMessages( $num ) {

		switch ( $num ) {
			case 1:
				$message = __( 'Comment can not be edited.', DECOM_LANG_DOMAIN );
				break;
			case 2:
				$message = __( 'User not found.', DECOM_LANG_DOMAIN );
				break;
			case 3:
				$message = __( 'Post status not public and not private.', DECOM_LANG_DOMAIN );
				break;
			case 4:
				$message = __( 'Draft comment', DECOM_LANG_DOMAIN );
				break;
			case 5:
				$message = __( 'Password protected comment', DECOM_LANG_DOMAIN );
				break;
			case 6:
				$message = __( 'Sorry, you must be logged in to post a comment.', DECOM_LANG_DOMAIN );
				break;
			case 7:
				$message = __( 'please type a comment.', DECOM_LANG_DOMAIN );
				break;
			case 8:
				$message = __( 'Enter a valid name or email address, please.', DECOM_LANG_DOMAIN );
				break;
			case 9:
				$message = __( 'Comment dublicate!', DECOM_LANG_DOMAIN );
				break;
		}

		return $message;
	}

	public function deletePicture( $get, $post ) {
		$comment_id         = $post['comment_id'];
		$comment            = get_comment( $comment_id );
		$comment_date       = $gmt ? $comment->comment_date_gmt : $comment->comment_date;
		$unix_time          = mysql2date( 'U', $comment_date, false );
		$unix_time_gmt      = mysql2date( 'U', $comment_date, true );
		$comment_time       = date( 'H:i d.m.Y', $unix_time );
		$diff_expired_time  = time() - $unix_time_gmt;
		$expired_time       = false;
		$start_expired_time = $settings['time_editing_deleting_comments'] * 60;
		if ( $diff_expired_time <= $start_expired_time ) {
			$expired_time = ceil( ( $start_expired_time - $diff_expired_time ) / 60 );
		}

		$decom_nonce = $post['decom_rmimgnonce'];
		$res['post'] = $decom_nonce;

		if ( decom_is_can_edit_comment( $comment, $expired_time ) || current_user_can( 'moderate_comments' ) ) {
			$attach_id  = $post['attach_id'];
			$attach_ids = get_comment_meta( $comment_id, 'decom_attached_pictures', true );
			if ( ! $attach_ids ) {
				wp_send_json( array(
					'result' => 'fail',
				) );
			}
			$attach_ids = unserialize( $attach_ids );
			if ( is_array( $attach_ids ) && count( $attach_ids ) > 0 ) {
				foreach ( $attach_ids as $k => $id ) {
					if ( $id == $attach_id ) {
						wp_delete_attachment( $id, true );
						unset( $attach_ids[ $k ] );
						break;
					}
				}
				if ( count( $attach_ids ) > 0 ) {
					$attach_ids = serialize( $attach_ids );
					update_comment_meta( $comment_id, 'decom_attached_pictures', $attach_ids );
				} else {
					delete_comment_meta( $comment_id, 'decom_attached_pictures' );
				}
			}
			$res['result'] = 'success';
			wp_send_json( $res );
		}
		$res['result'] = 'fail';
		$res['mes']    = 'can not remove';
		wp_send_json( $res );
	}

	public function getCommentsByPaginate( $get, $post ) {

		$ajax          = 1;
		$ajax_post_id  = $post['post_id'];
		$ajax_page_num = $post['page_num'];
		if ( $this->validationPostStatus( $ajax_post_id ) ) {

			$decom_template_path = DECOM_Loader_MVC::getPathTheme();
			include $decom_template_path . 'comments.php';
		} else {
			wp_send_json( array( 'result' => 'error' ) );
		}
		die();
	}

	public function validationPostStatus( $post_id ) {

		if ( get_post_status( $post_id ) == 'publish' ) {
			return true;
		} else {
			return false;
		}
	}

	public function sortComments( $get, $post ) {

		$jScroll_post_id = isset( $post['post_id'] ) ? $post['post_id'] : '';
		if ( $jScroll_post_id && $this->validationPostStatus( $jScroll_post_id ) ) {
			$decom_template_path = DECOM_Loader_MVC::getPathTheme();
			include $decom_template_path . 'comments.php';
		}
	}

	public function editCommentPopup( $get, $post ) {

		$current_user_id = get_current_user_id();
		$comment_id      = isset( $get['comment_id'] ) && (int) $get['comment_id'] ? (int) $get['comment_id'] : '';
		if ( $comment_id && is_super_admin( $current_user_id ) ) {
			$model_comments     = DECOM_Loader_MVC::getComponentModel( 'comments', 'comments' );
			$comment            = $model_comments->getComment( $comment_id, OBJECT );
			$the_comment_status = wp_get_comment_status( $comment->comment_ID );

			$row_actions = '<div class="row-actions" c_id="' . $comment_id . '">';

			if ( $the_comment_status == 'approved' ) {
				$row_actions .= '<span class="unapprove"><a href="javascript:void(0)" class="vim-u">' . __( 'Unapprove', DECOM_LANG_DOMAIN ) . '</a></span>';
			} else {
				$row_actions .= '<span class="approve"><a href="javascript:void(0)" class="vim-a">' . __( 'Approve', DECOM_LANG_DOMAIN ) . '</a></span>';
			}

			if ( 'spam' != $the_comment_status && 'trash' != $the_comment_status ) {
				$row_actions .= '<span class="trash"> | <a href="javascript:void(0)">' . __( 'Delete', DECOM_LANG_DOMAIN ) . '</a></span>';
				$row_actions .= '<span class="spam"> | <a href="javascript:void(0)">' . __( 'Spam', DECOM_LANG_DOMAIN ) . '</a></span>';
				$row_actions .= '<span class="edit"> | <a href="javascript:void(0)">' . __( 'Edit', DECOM_LANG_DOMAIN ) . '</a></span>';
			}

			$row_actions .= '</div>';
			echo $row_actions;
		}
	}

	public function moderateCommentStatus( $get, $post ) {

		$model_comments = DECOM_Loader_MVC::getComponentModel( 'comments', 'comments' );
		$user_can       = $model_comments->isSuperAdmin();
		$result         = false;
		$all_status     = array( 'hold', 'approve', 'spam' );
		$comment_id     = isset( $post['c_id'] ) && (int) $post['c_id'] ? (int) $post['c_id'] : '';
		$action         = isset( $post['actions'] ) && trim( $post['actions'] ) ? trim( $post['actions'] ) : '';

		if ( $comment_id && $user_can && in_array( $action, $all_status ) ) {
			$result = $model_comments->moderateCommentStatus( $comment_id, $action );
		}

		if ( $result ) {
			wp_send_json( array( 'result' => 'success', 'data' => array( 'id' => $comment_id ) ) );
		} else {
			wp_send_json( array( 'result' => 'error' ) );
		}
	}

	public function convertMedia( $get, $post ) {
		$content = stripslashes( $post['content'] );
		echo decom_comments_formated_content( $content );
//		echo DECOM_Component_Comments::onInsertMedia( $content );
	}

	public function ajaxPaginate( $get, $post ) {

		$fastAjaxCommentId       = isset( $post['comment_id'] ) && (int) $post['comment_id'] ? (int) $post['comment_id'] : false;
		$ajaxCurrentPage         = isset( $post['cur_page'] ) ? (int) $post['cur_page'] : 1;
		$jScroll_post_id         = isset( $post['post_id'] ) ? $post['post_id'] : '';
		$comment_paginate_action = isset( $post['actions'] ) ? $post['actions'] : '';

		if ( $jScroll_post_id && $this->validationPostStatus( $jScroll_post_id ) ) {

			$decom_template_path = DECOM_Loader_MVC::getPathTheme();

			include $decom_template_path . 'comments.php';
		}
	}

}
