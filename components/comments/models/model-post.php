<?php

class DECOM_Model_Post extends DECOM_Model {

	public function getCurrentPostId() {
		return get_the_ID();
	}

	public function getPost( $id, $output = 'OBJECT', $filter = 'raw' ) {
		return get_post( $id, $output, $filter );
	}

	public function getPostStatus( $post ) {
		return get_post_status( $post );
	}

	public function getPostStatusObj( $status ) {
		return get_post_status_object( $status );
	}

	public function postPasswordRequired( $post_id ) {
		return post_password_required( $post_id );
	}

	public function getAllPublishPosts() {
		$args  = array(
			'numberposts'    => - 1,
			'offset'         => 0,
			'category'       => '',
			'orderby'        => 'post_date',
			'order'          => 'DESC',
			'include'        => '',
			'exclude'        => '',
			'meta_key'       => '',
			'meta_value'     => '',
			'post_type'      => 'post',
			'post_mime_type' => '',
			'post_parent'    => '',
			'post_status'    => 'publish'
		);
		$posts = get_posts( $args );

		return $posts;
	}

	public function recalculateCommentKarma( $settings ) {
		$model_votes    = DECOM_Loader_MVC::getComponentModel( 'comments', 'comments-votes' );
		$model_comments = DECOM_Loader_MVC::getComponentModel( 'comments', 'comments' );

		$posts = $this->getAllPublishPosts();

		if ( is_array( $posts ) ) {
			foreach ( $posts as $post ) {
				$comments = $model_comments->getComments( $post->ID );
				if ( !$settings ) {
					$new_comment_karma = $model_votes->choiceVoiceAllCommentOnlyLike( $comments );
				} else {
					$new_comment_karma = $model_votes->choiceVoiceAllComment( $comments );
				}

				if ( is_array( $new_comment_karma ) && count( $new_comment_karma ) > 0 ) {
					foreach ( $comments as $comment ) {
						$comment_ID = $comment->comment_ID;
						if ( !array_key_exists( $comment_ID, $new_comment_karma ) ) {
							$new_comment_karma[$comment_ID] = 0;
						}
						$model_comments->updateCommentsKarma( $comment_ID, $new_comment_karma[$comment_ID] );
					}

				}

			}
		}
	}

}