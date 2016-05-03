<?php

class DECOM_Email {

	public function sendNotificationEmail( array $messages ) {

//		$headers = 'Content-type: text/html; charset=utf-8' . "\r\n";
//		$res     = @wp_mail( $messages['email'], $messages['subject'], $messages['body'], $headers );

		return $res;
	}

	public function notyfy_post_comments( array $email_array, $comment ) {
		$model_notifications = DECOM_Loader_MVC::getComponentModel( 'notification-messages', 'notification-messages' );
		$post                = get_post( $comment->comment_post_ID );
		$blog_name           = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

		$post_title       = apply_filters( 'the_title', $post->post_title );
		$comment_author   = $comment->comment_author;
		$comment_text     = apply_filters( 'decomments_comment_text', $comment->comment_content );
		$comment_post_url = get_permalink( $comment->comment_post_ID );
		$comment_link     = $comment_post_url . '#comment-' . $comment->comment_ID;

		$comment_creation_date = date( "Y-m-d H:i:s" );

		$mail_my_comment  = '';
		$emails_list_post = '';

		if ( count( $email_array['new_post_comment'] ) > 0 && $email_array['new_comment_to_comment'] ) {
			foreach ( $email_array['new_post_comment'] as $email ) {
				if ( $email_array['new_comment_to_comment'] == $email ) {
					$mail_my_comment = $email_array['new_comment_to_comment'];
				} else {
					$emails_list_post .= $email . ', ';
				}
			}
		} elseif ( count( $email_array['new_post_comment'] ) > 0 ) {
			foreach ( $email_array['new_post_comment'] as $email ) {
				$emails_list_post .= $email . ', ';
			}
		} else {
			$mail_my_comment = $email_array['new_comment_to_comment'];
		}

		$site_name = str_replace( array( 'http://', 'https://' ), '', get_bloginfo( 'url' ) );

		$headers = "Content-type: text/html, charset=utf8 \r\n";
		$headers .= "From: " . $site_name . " <noreply@" . $site_name . ">\r\n";

		add_filter( 'wp_mail_content_type', create_function( '', 'return "text/html";' ) );


		// отправка письма к комментарию
		if ( $mail_my_comment ) {
			$messageMyComment = $model_notifications->getNotificationLocale( 'new_comment_to_comment' );
			foreach ( explode( ',', $mail_my_comment ) as $email ) {
				$messagesComment = $this->substitution( $messageMyComment->notification_title, $messageMyComment->notification_text, $post_title, $comment_author, $comment_text, $comment_link, $comment_post_url, $comment_creation_date, $emil, 1 );
				@wp_mail( $mail_my_comment, $messagesComment['subject'], nl2br( $messagesComment['text'] ), $message_headers );
			}
		}

		//отправка письма к посту
		/*if ( $emails_list_post ) {

			$messagePostComment = $model_notifications->getNotificationLocale( 'new_post_comment' );

			foreach ( explode( ',', $emails_list_post ) as $email ) {
				$messagesPost = $this->substitution( $messagePostComment->notification_title, $messagePostComment->notification_text, $post_title, $comment_author, $comment_text, $comment_link, $comment_post_url, $comment_creation_date, $email, 2 );
				@wp_mail( $emails_list_post, $messagesPost['subject'], nl2br( $messagesPost['text'] ), $message_headers );
			}
		}*/

		//$sandEmailMyComment = $this->sendEmailMyComment();
		//$sendEmailPostComment  = $this->sendEmailPostComment();

	}

	public function substitution( $message_title, $message_text, $post_title, $comment_author, $comment_text, $comment_link, $comment_post_url, $comment_creation_date, $email = '', $type = 0 ) {
		$message['subject'] = preg_replace( '/%COMMENTED_POST_TITLE%/', $post_title, $message_title );
		$message['text']    = preg_replace( '/%COMMENTED_POST_TITLE%/', $post_title, $message_text );
		$message['text']    = preg_replace( '/%COMMENT_TEXT%/', $comment_text, $message['text'] );
		$message['text']    = preg_replace( '/%COMMENT_AUTHOR%/', $comment_author, $message['text'] );
		$message['text']    = preg_replace( '/%COMMENT_LINK%/', $comment_link, $message['text'] );
		$message['text']    = preg_replace( '/%COMMENTED_POST_URL%/', $comment_post_url, $message['text'] );
		$message['text']    = preg_replace( '/%COMMENT_CREATION_DATE%/', $comment_creation_date, $message['text'] );

		$message['text'] = str_replace( "\r", "<br/>", $message['text'] );

		$link = "?decomments_unsubscribe=0c043d0ea954befc328884a23da8cfe6&t=$type&email=$email";

//		$unsubscribe = '<br><br><a href="' . $comment_post_url . '">' . __( 'Unsubscribe', DECOM_LANG_DOMAIN ) . '</a>';
		//UNSUBSCRIBE

		$html_message = "<!DOCTYPE html><html><head><meta charset='utf-8' />
							<title>" . strtolower( $_SERVER['SERVER_NAME'] ) . ' - ' . $message_title . "</title></head><body>
							{$message['text']}
							<br>
							$unsubscribe
							</body></html>
		";

		$message['text'] = $html_message;

		//var_dump($message);
		return $message;
	}


	public function endOutput( $endMessage ) {
		ignore_user_abort( true );
		set_time_limit( 0 );
		header( "Content-Length: " . strlen( $endMessage ) . "\r\n" );
		header( "Connection: close\r\n" );
		echo $endMessage;
		echo str_repeat( "\r\n", 10 ); // just to be sure
		flush();
	}

	public function endOutput2( $content ) {
		ignore_user_abort( true );
		set_time_limit( 0 );
		$headers = get_headers( home_url(), 1 );
		foreach ( $headers as $header => $value ) {
			if ( $header == 0 ) {
				header( $value . "\r\n" );
			} else {
				header( $header . ': ' . $value . "\r\n" );
			}
			if ( $header == 'Vary' ) {
				header( "Content-Length: " . ( strlen( $content ) - 10 ) . "\r\n" );
			}
		}
		header( "\r\n" );
		echo $content;
		echo str_repeat( "\r\n", 10 );
		flush();
		ob_clean();
	}

	public function endOutput1( $content ) {
		$headers = get_headers( home_url(), 1 );
		header( "HTTP/1.1 200 OK" );
		header( "Cache-Control:no-cache, must-revalidate, max-age=0\r\n" );
		header( "Connection:Keep-Alive\r\n" );
		header( "Content-Encoding:gzip\r\n" );
		header( "Content-Length: " . strlen( $content ) . "\r\n" );
		header( "Content-Type:text/html\r\n" );
		header( "Date:{$headers['Date']}\r\n" );
		header( "Expires:{$headers['Expires']}\r\n" );
		header( "Keep-Alive:timeout=5, max=100\r\n" );
		header( "Pragma:no-cache\r\n" );
		header( "Server:Apache/2.2.22 (Ubuntu)\r\n" );
		header( "Vary:Accept-Encoding\r\n" );
		header( "X-Powered-By:PHP/5.4.9-4ubuntu2.2\r\n\r\n" );
		echo $content;


	}

}
