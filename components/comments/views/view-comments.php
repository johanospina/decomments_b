<?php

class DECOM_View_Comments extends DECOM_View {

	/**
	 * Render comment begin
	 * This method is called by standard wp_list_comments function
	 *
	 * @param $comment comment object
	 * @param $args    additional arguments
	 * @param $depth   depth of current comment
	 *
	 * @return string
	 */
	public function renderCommentBegin( $comment, $args, $depth ) {

		global $comment_number;

		if ( isset( $args['ajax_post_id'] ) ) {
			$post = get_post( $args['ajax_post_id'] );
		} else {
			$post = get_post();
		}

		$settings = $args['settings'];

		$opacity    = '';
		$user_votes = $args['user_voice'];

		$voice = $comment->comment_karma;

		if ( $voice > 0 ) {
			$voice        = '+' . $voice;
			$decom_status = ' decomments-like';
		} elseif ( $voice < 0 ) {
			$decom_status = ' decomments-dislike';
			if ( $settings['show_comments_negative_rating_low_opacity'] ) {
				$opacity = ' decomments-opacity';
			}
		} else {
			$decom_status = ' decomments-like';
			$voice        = '';
		}

		if ( isset( $args['max'] ) && $comment->comment_parent == 0 ) {
			$max = ' decomments-top-rated';
		} else {
			$max = '';
		}

		$html = '';

		$decom_template_path = DECOM_Loader_MVC::getPathTheme();
		include $decom_template_path . 'parts/comment-begin.php';

		return $html;
	}

	/**
	 * Render comment end
	 * This method is called by standard wp_list_comments function
	 *
	 * @param $comment comment object
	 * @param $args    additional arguments
	 * @param $depth   depth of current comment
	 *
	 * @return string
	 */
	public function renderCommentEnd( $comment, $args, $depth ) {

		$html = '';
		if ( $comment->comment_type == '' ) {
			$html = '</div>';
		}

		return $html;
	}

	/**
	 * Render comment container
	 *
	 * @param $comment comment object
	 * @param $args    additional arguments
	 * @param $depth   depth of current comment
	 *
	 * @return string
	 */
	public function renderComment( $comment, $args, $depth ) {

		$html = $this->renderCommentBegin( $comment, $args, $depth ) . '</div>';

		return $html;
	}

	public function getSocialIсonHtml( $social_icon ) {

		switch ( $social_icon ) {
			case 'Facebook':
				$social_icon_img = 'facebook.png';
				break;
			case 'Google':
				$social_icon_img = 'google.png';
				break;
			case 'Linkedin':
				$social_icon_img = 'linkedin.png';
				break;
			case 'Mailru':
				$social_icon_img = 'moi-mir.png';
				break;
			case 'Twitter':
				$social_icon_img = 'twitter.png';
				break;
			case 'Vkontakte':
				$social_icon_img = 'vkontakte.png';
				break;
			default:
				$social_icon_img = '';
		}
		$social_icon_html = '';
		if ( $social_icon_img != '' ) {
			$social_icon_html = '<img src="' . DECOM_COMPONENTS_IMG . '/icons/' . $social_icon_img . '" class="decomments-social-icon">';
		}

		return $social_icon_html;
	}

	public function getSocialShareBlock( $settings ) {

		$decom_template_path = DECOM_Loader_MVC::getPathTheme();

		include $decom_template_path . 'parts/social-share-block.php';

		return $social_share_block;

	}
}
