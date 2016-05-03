<?php

class DECOM_Controller_CommentsVotes extends DECOM_Controller {

	public function voting( $get, $post ) {

		if ( isset( $post['voice'] ) ) {
			switch ( $post['voice'] ) {
				case 'like':
					$vote_like    = 1;
					$vote_dislike = 0;
					break;
				case 'dislike':
					$vote_like    = 0;
					$vote_dislike = 1;
					break;
				default:
					return false;
			}
		} else {
			return false;
		}

		$comment_id = ( isset( $post['fk_comment_id'] ) ) ? (int) ( $post['fk_comment_id'] ) : null;
		$user_id    = ( isset( $post['fk_user_id'] ) ) ? (int) ( $post['fk_user_id'] ) : null;

		$user_ip = $_SERVER['REMOTE_ADDR'];
		$user_ip = preg_replace( '/[^0-9a-fA-F:., ]/', '', $user_ip );

		if ( isset( $comment_id ) && isset( $user_id ) && isset( $user_ip ) ) {
			$modelVotes        = DECOM_Loader_MVC::getComponentModel( 'comments', 'comments-votes' );
			$result            = $modelVotes->vote( $comment_id, $user_id, $user_ip, $vote_like, $vote_dislike );
			$result['myVoice'] = $modelVotes->getMyVoice( $user_id, $comment_id, $user_ip );
		} else {
			$result = array( 'error' => __( 'Vote error', DECOM_LANG_DOMAIN ) );
		}

		wp_send_json( $result );
	}

}


