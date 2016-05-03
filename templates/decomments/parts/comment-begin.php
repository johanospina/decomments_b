<?php

if ( $comment->comment_type == '' ) {

	global $post;
	$post                       = decom_get_comment_post( $comment->comment_post_ID );
	$is_comments_close          = decom_is_comment_close( $post );
	$comment_id                 = $comment->comment_ID;
	$link_comment_anchor        = get_permalink( $comment->comment_post_ID ) . '#comment-' . $comment_id;
	$comment_author             = $comment->comment_author;
	$decom_settings             = decom_get_options();
	$display_round_avatars      = $decom_settings['display_round_avatars'];
	$avatar_size                = $decom_settings['avatar_size_thumb'];
	$avatar                     = decom_get_comment_avatar_cached( $comment, $avatar_size );
	$follow                     = ' rel="' . $decom_settings['follow'] . '"';
	$current_user_id            = get_current_user_id();
	$decom_userdata             = decom_get_user_data( $comment->user_id );
	$decom_userdata_author_post = decom_get_user_data( $post->post_author );
	$post_author_email          = '';

	if ( isset( $decom_userdata->data ) ) {
		$comment_author = $decom_userdata->data->display_name;
	}

	if ( isset( $decom_userdata_author_post->data ) ) {
		$post_author_email = $decom_userdata_author_post->data->user_email;
	}

	// if name author email then explode name and display ziro array element
	if ( is_email( $comment_author ) ) {
		list( $comment_author, $tmp ) = explode( '@', $comment_author );
		$comment_author = str_replace( array( '.', '-', '_', ',' ), ' ', $comment_author );
	}


	$decom_author_title_bottom_url_arr = array(
		'show_link'                 => 0,
		'disable_author_title_link' => 0,
		'html'                      => '<span class="decomments-author-url ">' . $comment->comment_author_url . '</span>',
		'comment_author_url'        => $comment->comment_author_url
	);

	$decom_author_title_bottom_url_arr = apply_filters( 'decom_author_title_bottom_print_site_url', $decom_author_title_bottom_url_arr );


	if ( $comment->user_id ) {
//		$decomments_user_post_count = count_user_posts( $comment->user_id );
		$decomments_user_post_count = $decom_userdata->data->count_user_posts;
		$author_link                = '';

		if ( $decomments_user_post_count && ! $decom_author_title_bottom_url_arr['show_link'] ) {
			$author_link        = get_author_posts_url( $comment->user_id );
			$author_avatar_html = '<a class="decomments-author-img" href="' . $author_link . '" ' . $follow . '><span class="round-wrap">' . $avatar . '</span></a>';
			$author_link_html   = '<a class="decomments-autor-name" href="' . $author_link . '" ' . $follow . '>' . $comment_author . '</a>';
		} else {
			$author_avatar_html = '<span class="round-wrap">' . $avatar . '</span>';
			$author_link_html   = '<span class="decomments-autor-name">' . $comment_author . '</span>';
		}

	} elseif ( $comment->comment_author_url != '' ) {
//		$decomments_user_post_count = count_user_posts( $comment->user_id );
		if ( $decom_author_title_bottom_url_arr['show_link'] ) {
			$author_avatar_html = '<span class="round-wrap">' . $avatar . '</span>';
			$author_link_html   = '<span class="decomments-autor-name">' . $comment_author . '</span>';
		} else {
			$author_avatar_html = '<a class="decomments-author-img" href="' . $comment->comment_author_url . '" ' . $follow . '><span class="round-wrap">' . $avatar . '</span></a>';
			$author_link_html   = '<a class="decomments-autor-name" href="' . $comment->comment_author_url . '" ' . $follow . '>' . $comment_author . '</a>';
		}

	} else {
		$author_avatar_html = '<span class="round-wrap">' . $avatar . '</span>';
		$comment_author     = $comment_author ? $comment_author : __( 'Guest', DECOM_LANG_DOMAIN );
		$author_link_html   = '<span class="decomments-autor-name">' . $comment_author . '</span>';
	}

	if ( preg_match_all( '/\[decom_attached_image_[0-9]+\]/', $comment->comment_content, $matches ) ) {
		$comment_content = '';
	} else {
		$comment_content = apply_filters( 'decomments_comment_text', $comment->comment_content );
//		$comment_content = decom_filter_tags_replace( $comment_content );
	}

	$user_ip            = $_SERVER['REMOTE_ADDR'];
	$user_ip            = preg_replace( '/[^0-9a-fA-F:., ]/', '', $user_ip );
	$unix_time          = get_comment_time( 'U', false );
	$unix_time_gmt      = get_comment_time( 'U', true );
	$date_format        = isset( $decom_settings['date_format'] ) ? $decom_settings['date_format'] : 'Y.m.d';
	$time_format        = isset( $decom_settings['time_format'] ) ? $decom_settings['time_format'] : 'H:i';
	$comment_time       = date( "$time_format $date_format", $unix_time );
	$comment_time_t     = date( $time_format, $unix_time );
	$comment_time_d     = date( $date_format, $unix_time );
	$diff_expired_time  = time() - $unix_time_gmt;
	$expired_time       = false;
	$start_expired_time = $decom_settings['time_editing_deleting_comments'] * 60;
	if ( $diff_expired_time <= $start_expired_time ) {
		$expired_time = ceil( ( $start_expired_time - $diff_expired_time ) / 60 );
	}
	$comment_number ++;
	$author_html = '';
	if ( ( ! empty( $decom_settings['allocate_comments_author_post'] ) && $comment->user_id === $post->post_author ) ||
	     ( ! empty( $decom_settings['allocate_comments_author_post'] ) && $comment->comment_author_email === $post_author_email )
	) {
		$author_html  = '<span class="decomments-author active">' . __( 'Author', DECOM_LANG_DOMAIN ) . '</span>';
		$author_class = ' decomments-bypostauthor';
	}
	$expired_time_html = '';

	if ( decom_is_can_edit_comment( $comment, $expired_time ) ) {
		$expired_time_html = '
                    <span class="decomments-change-list active">
                        <ins> ' . __( 'Time remaining ', DECOM_LANG_DOMAIN ) . ' <em>' . $expired_time . ' ' . __( 'min.', DECOM_LANG_DOMAIN ) . '</em> </ins>

                        <span class="decomments-edit-block">
                    	   <a class="decomments-button decomments-button-edit" href="javascript:void(0)">' . __( 'Edit', DECOM_LANG_DOMAIN ) . '</a>
                      	   <a class="decomments-button decomments-button-delete" href="javascript:void(0)"> ' . __( 'Remove', DECOM_LANG_DOMAIN ) . '<i class="decomments-icon-remove-o"></i></a>
						</span>
                    </span>';
	}

	$badges_html = '';
	if ( $badges = deco_get_user_badges( empty( $comment->user_id ) ? $comment->comment_author_email : $comment->user_id ) ) {
		foreach ( $badges as $badge ) {
			$badges_html .= '<img width="20" title="' . $badge->badge_name . '" src="' . $badge->badge_icon_path . '" />';
		}
	}

	$comment_modified_date     = '';
	$comment_modified_time     = get_comment_meta( $comment_id, 'decom_comment_modified_date', true );
	$comment_modifed_moderator = get_comment_meta( $comment_id, 'decom_comment_modified_moderator', true );

	if ( $comment->comment_approved == 'trash' ) {
		$holder         = '<div class="decomments-text-holder decomments-trash-holder"><p>' . __( 'Comment deleted', DECOM_LANG_DOMAIN ) . '</p></div>';
		$item_block     = '';
		$pictures_block = '';
	} else {
		$holder     = '<div class="decomments-text-holder decomments-content-' . $comment_id . '">' . $comment_content . '</div>';
		$item_block = '<nav class="decomments-footer-nav">';
		if ( $current_user_id ) {
			$model_votes                       = DECOM_Loader_MVC::getComponentModel( 'comments', 'comments-votes' );
			$likes_or_dislikes                 = $model_votes->get_user_likes_or_dislikes( $comment->comment_ID, $current_user_id );
			$decomments_class_is_voted_like    = '';
			$decomments_class_is_voted_dislike = '';
			if ( intval( $likes_or_dislikes['likes'] ) ) {
				$decomments_class_is_voted_like = ' icon-clicked';
			} elseif ( intval( $likes_or_dislikes['dislike'] ) ) {
				$decomments_class_is_voted_dislike = ' icon-clicked';
			}
		}

		$item_block .= '<span class="decomments-vote"><div class="loader-ball-scale"><div></div><div></div><div></div></div>';
		if ( false === $is_comments_close ) {
			$item_block .= '<a class="decomments-like-link decomments-voted-likee' . $decomments_class_is_voted_like . '" data-res="like" href="javascript:void(0)">
		<i class="decomments-icon-like"><img class="svg" src="' . DECOM_TEMPLATE_URL_DEFAULT . 'assets/images/svg/plus.svg"/></i><b></b></a>';

			if ( $decom_settings['enable_dislike'] ) {
				$item_block .= '<a class="decomments-dislike-link decomments-voted-dislike ' . $decomments_class_is_voted_dislike . '" data-res="dislike" href="javascript:void(0)"><i class="decomments-icon-dislike"><img class="svg" src="' . DECOM_TEMPLATE_URL_DEFAULT . 'assets/images/svg/minus.svg"/></i><b></b></a>';
			}
		}

		$decomments_style_display_votes_numbers = empty( $voice ) ? 'style="display:none;"' : '';
		$item_block .= '<span class="decomments-biggest-vote" ' . $decomments_style_display_votes_numbers . ' ><i class="decomments-icon-bvote"><img class="" src="' . DECOM_TEMPLATE_URL_DEFAULT . 'assets/images/svg/rate.svg"/></i><b id="decomments-vote-id-' . $comment->comment_ID . '">' . $voice . '</b></span>';
		$item_block .= '</span>';
		if ( false === $is_comments_close ) {
			if ( ! isset( $decom_settings['decom_disable_replies'] ) || ( isset( $decom_settings['decom_disable_replies'] ) && empty( $decom_settings['decom_disable_replies'] ) ) ) {
				$item_block .= '<a href="javascript:void(0)" class="decomments-button decomments-button-reply">' . __( 'Reply', DECOM_LANG_DOMAIN ) . '</a>';
			}
		}
		$item_block .= $expired_time_html;
		$item_block .= $this->getSocialShareBlock( $settings );

		include 'moderate-menu.php';

		$item_block .= '</nav>';

		$pictures_block = '';
		$animatedClass  = '';
		$iconAnimated   = '';
		$attach_ids     = get_comment_meta( $comment_id, 'decom_attached_pictures', true );
		if ( $attach_ids ) {
			$attach_ids = unserialize( $attach_ids );
			if ( is_array( $attach_ids ) && count( $attach_ids ) > 0 ) {
				$pictures_block .= '<div class="decomments-pictures-holder">';
				foreach ( $attach_ids as $attach_id ) {
					$animated  = '';
					$meta_data = get_post_meta( $attach_id, '_wp_attachment_metadata', true );
					$mime_type = $meta_data['sizes']['thumbnail']['mime-type'];
					if ( $mime_type == 'image/gif' ) {
						$animatedClass = 'decomments-gif';
						$iconAnimated  = '<svg class="svg-icon" viewBox="0 0 20 20">
							<path fill="none" d="M19.471,8.934L18.883,8.34c-2.096-2.14-4.707-4.804-8.903-4.804c-4.171,0-6.959,2.83-8.996,4.897L0.488,8.934c-0.307,0.307-0.307,0.803,0,1.109l0.401,0.403c2.052,2.072,4.862,4.909,9.091,4.909c4.25,0,6.88-2.666,8.988-4.807l0.503-0.506C19.778,9.737,19.778,9.241,19.471,8.934z M9.98,13.787c-3.493,0-5.804-2.254-7.833-4.3C4.182,7.424,6.493,5.105,9.98,5.105c3.536,0,5.792,2.301,7.784,4.332l0.049,0.051C15.818,11.511,13.551,13.787,9.98,13.787z"></path>
							<circle fill="none" cx="9.98" cy="9.446" r="1.629"></circle>
						</svg>';
					}
					$image_attributes = wp_get_attachment_image_src( $attach_id, 'full' );
					$image_height     = $image_attributes[2] + 30;
					$pictures_block .= '<div id="decomments-picture-full-' . $attach_id . '" style="display: none;"><div class="decomments-picture-full decomments-popup-style">';
					$pictures_block .= wp_get_attachment_image( $attach_id, 'full', false, array( 'class' => 'decom-attachment-full' ) );
					$pictures_block .= '</div></div>';
					$pictures_block .= '<a onclick=\'decom.showModal(this,jQuery(".decomments-comment-section").attr("data-modal-preview")); return false;\' class="addedimg ' . $animatedClass . '" title="' . __( 'Preview image', DECOM_LANG_DOMAIN ) . '" data-img-width="' . $image_attributes[1] . '" data-img-height="' . $image_attributes[2] . '" rel="' . $image_attributes[1] . 'x' . $image_attributes[2] . '" href="/#TB_inline?width=' . $image_attributes[1] . '&height=' . $image_height . '&inlineId=decomments-picture-full-' . $attach_id . '">';
					$pictures_block .= $iconAnimated;
					$pictures_block .= wp_get_attachment_image( $attach_id, 'full', false, array(
						'class' => 'decomments-picture' . $animatedClass . '-img',
						'rel'   => $attach_id
					) );
					$pictures_block .= wp_get_attachment_image( $attach_id, 'full', false, array( "class" => "hiddenpic" ) );
					$pictures_block .= '</a>';
				}
				$pictures_block .= '</div>';
			}
		}
	}

	$comment_classes         = implode( ' ', get_comment_class( 'decomments-comment-block' ) );
	$output_numbers_comments = '';
	if ( $decom_settings['output_numbers_comments'] ) {
		$output_numbers_comments = '<a href="' . $link_comment_anchor . '" class="decomments-number"><span>' . $comment_number . '</span></a>';
	}
	$pl = 0;
	if ( $pl = intval( $decom_settings['avatar_size_thumb'] ) ) {
		$decom_author_title_bottom_url_html = '';
		if ( $decom_author_title_bottom_url_arr['show_link'] && ! empty( $comment->comment_author_url ) ) {
			$e                                  = $decom_author_title_bottom_url_arr['disable_author_title_link'];
			$decom_author_title_bottom_url_html = $decom_author_title_bottom_url_arr['html'];
		}
		$social_iсon_html = empty( $social_iсon_html ) ? '' : $social_iсon_html;
		$author_class     = empty( $author_class ) ? '' : $author_class;
		$plnew            = ( isset( $decom_settings['show_avatars'] ) && intval( $decom_settings['show_avatars'] ) == 1 ) ? $pl + 15 : 40;
		$plnew            = ( $plnew < 75 && ! isset( $decom_settings['show_avatars'] ) ) ? 75 : $plnew;

		$display_round_avatars_class = $display_round_avatars ? ' decomments-round-avatar' : '';
		$autor_block                 = '<figure style="width:' . $decom_settings['avatar_size_thumb'] . 'px; height:' . $decom_settings['avatar_size_thumb'] . 'px;" class="decomments-author-block' . $display_round_avatars_class . '">' . apply_filters( 'decomments_avatar_html', $author_avatar_html ) . '
                     	<figcaption data-cui="' . $comment->user_id . '" data-pai="' . $post->post_author . '"">' . $social_iсon_html . $author_html . $output_numbers_comments . '</figcaption>
                     </figure>';
	}

	$html .= '<div class="' . $comment_classes . '" id="comment-' . $comment_id . '" parent-id="' . $comment->comment_parent . '" >
                 <div  style="padding-left: ' . $plnew . 'px;" class="decomments-comment-body' . $max . $opacity . $author_class . '"> ' . $autor_block . '
                     <div class="decomments-description-block">';
	include 'title-block-comment.php';
	$html .= '       <div class="decomments-comment-main">' . $holder . $pictures_block . '</div>
                         ' . $item_block . '
                     </div>
                 </div>';
}