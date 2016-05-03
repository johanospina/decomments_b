<?php

class Decom_Comment_Posting {
	public function __construct() {
		add_action( 'wp_ajax_', array( $this, 'process' ) );
		add_action( 'wp_ajax_nopriv', array( $this, 'process' ) );
	}

	public function process() {
		$comment = wp_handle_comment_submission( wp_unslash( $_POST ) );
		if ( is_wp_error( $comment ) ) {
			$data = $comment->get_error_data();
			if ( ! empty( $data ) ) {
				wp_die( $comment->get_error_message(), $data );
			} else {
				exit;
			}
		}

		$user = wp_get_current_user();


		$location = empty( $_POST['redirect_to'] ) ? get_comment_link( $comment ) : $_POST['redirect_to'] . '#comment-' . $comment->comment_ID;

	}
}
