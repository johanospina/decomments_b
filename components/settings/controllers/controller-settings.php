<?php

class DECOM_Controller_Settings extends DECOM_Controller {

	public function saveSettings( $get, $post ) {

		$param = array();

		$model_options = DECOM_Loader_MVC::getLibraryModel( 'options', 'options' );

		$file_error    = '';
		$error_message = '';

		$param['avatar'] = $post['avatar'];

		$param['avatar_size_thumb'] = isset( $post['avatar_size_thumb'] ) ? (int) $post['avatar_size_thumb'] : 0;

		$param['number_comments_per_page'] = isset( $post['number_comments_per_page'] ) ? (int) $post['number_comments_per_page'] : 10;

		$param['follow'] = ( isset( $post['follow'] ) ) ? $this->valStr( $post['follow'] ) : 'dofollow';

		$param['output_subscription_comments'] = ( isset( $post['output_subscription_comments'] ) && $post['output_subscription_comments'] == 'on' ) ? 1 : 0;

		$param['mark_subscription_comments']    = ( isset( $post['mark_subscription_comments'] ) && $post['mark_subscription_comments'] == 'on' ) ? 1 : 0;
		$param['output_subscription_rejoin']    = ( isset( $post['output_subscription_rejoin'] ) && $post['output_subscription_rejoin'] == 'on' ) ? 1 : 0;
		$param['mark_subscription_rejoin']      = ( isset( $post['mark_subscription_rejoin'] ) && $post['mark_subscription_rejoin'] == 'on' ) ? 1 : 0;
		$param['allocate_comments_author_post'] = ( isset( $post['allocate_comments_author_post'] ) && $post['allocate_comments_author_post'] == 'on' ) ? 1 : 0;
		$param['decomments_reset_color']        = ( isset( $post['decomments_reset_color'] ) && $post['decomments_reset_color'] == 'on' ) ? 1 : 0;
		$param['decomments_set_color']          = ( isset( $post['decomments_set_color'] ) && $post['decomments_set_color'] == 'on' ) ? 1 : 0;
		$param['decomments_main_color_theme']   = ( isset( $post['decomments_main_color_theme'] ) ) ? $post['decomments_main_color_theme'] : '';
		$param['output_numbers_comments']       = ( isset( $post['output_numbers_comments'] ) && $post['output_numbers_comments'] == 'on' ) ? 1 : 0;
		$param['allow_html_in_comments']       = ( isset( $post['allow_html_in_comments'] ) && $post['allow_html_in_comments'] == 'on' ) ? 1 : 0;

		$param['allow_quote_comments'] = ( isset( $post['allow_quote_comments'] ) && $post['allow_quote_comments'] == 'on' ) ? 1 : false;

		$param['decom_disable_replies'] = ( isset( $post['decom_disable_replies'] ) && $post['decom_disable_replies'] == 'on' ) ? 1 : 0;

		$param['output_total_number_comments_top']           = ( isset( $post['output_total_number_comments_top'] ) && $post['output_total_number_comments_top'] == 'on' ) ? 1 : 0;
		$param['enable_client_validation_fields']            = ( isset( $post['enable_client_validation_fields'] ) && $post['enable_client_validation_fields'] == 'on' ) ? 1 : 0;
		$param['sort_comments']                              = ( isset( $post['sort_comments'] ) ) ? $this->valStr( $post['sort_comments'] ) : 'best';
		$param['comments_negative_rating_below']             = ( isset( $post['comments_negative_rating_below'] ) && $post['comments_negative_rating_below'] == 'on' ) ? 1 : 0;
		$param['show_comments_negative_rating_low_opacity']  = ( isset( $post['show_comments_negative_rating_low_opacity'] ) && $post['show_comments_negative_rating_low_opacity'] == 'on' ) ? 1 : 0;
		$param['show_two_comments_highest_ranking_top_list'] = ( isset( $post['show_two_comments_highest_ranking_top_list'] ) && $post['show_two_comments_highest_ranking_top_list'] == 'on' ) ? 1 : 0;
		$param['allow_lazy_load']                            = ( isset( $post['allow_lazy_load'] ) && $post['allow_lazy_load'] == 'on' ) ? 1 : 0;
		$param['enable_embed_links']                         = ( isset( $post['enable_embed_links'] ) && $post['enable_embed_links'] == 'on' ) ? 1 : 0;
		$param['enable_social_share']                        = ( isset( $post['enable_social_share'] ) && $post['enable_social_share'] == 'on' ) ? 1 : 0;


		if ( isset( $post['deco_ajax_navy'] ) ) {

			if ( $this->valStr( $post['deco_ajax_navy'] ) == 'deco_show_more_comments_onebutton' ) {
				$param['deco_ajax_navy'] = 'deco_show_more_comments_onebutton';
			} else if ( $this->valStr( $post['deco_ajax_navy'] ) == 'deco_show_more_comments_prevnext' ) {
				$param['deco_ajax_navy'] = 'deco_show_more_comments_prevnext';
			} else if ( $this->valStr( $post['deco_ajax_navy'] ) == 'deco_show_more_comments_lazy' ) {
				$param['deco_ajax_navy'] = 'deco_show_more_comments_lazy';
			}

		} else {

			$param['deco_ajax_navy'] = 'deco_show_more_comments_onebutton';

		}

		/* Social Networks Shared Enable/Disable */
		$param['deco_fb_enable_share'] = ( isset( $post['deco_fb_enable_share'] ) && $post['deco_fb_enable_share'] == 'on' ) ? 1 : 0;
		$param['deco_tw_enable_share'] = ( isset( $post['deco_tw_enable_share'] ) && $post['deco_tw_enable_share'] == 'on' ) ? 1 : 0;
		$param['deco_gp_enable_share'] = ( isset( $post['deco_gp_enable_share'] ) && $post['deco_gp_enable_share'] == 'on' ) ? 1 : 0;
		$param['deco_ln_enable_share'] = ( isset( $post['deco_ln_enable_share'] ) && $post['deco_ln_enable_share'] == 'on' ) ? 1 : 0;
		$param['deco_vk_enable_share'] = ( isset( $post['deco_vk_enable_share'] ) && $post['deco_vk_enable_share'] == 'on' ) ? 1 : 0;
		$param['deco_ok_enable_share'] = ( isset( $post['deco_ok_enable_share'] ) && $post['deco_ok_enable_share'] == 'on' ) ? 1 : 0;

		$param['disable_display_logo'] = ( isset( $post['disable_display_logo'] ) && $post['disable_display_logo'] == 'on' ) ? 1 : 0;

		$param['deco_disable_css_style'] = ( isset( $post['deco_disable_css_style'] ) && $post['deco_disable_css_style'] == 'on' ) ? 1 : 0;

		$param['custom_folder_template'] = ( isset( $post['custom_folder_template'] ) ) ? $this->valStr( $post['custom_folder_template'] ) : 'default';

		if ( isset( $post['enable_social_share'] ) && $post['enable_social_share'] == 'on' ) {
			$param['tweet_share']     = ( isset( $post['tweet_share'] ) && $post['tweet_share'] == 'on' ) ? 1 : 0;
			$param['facebook_share']  = ( isset( $post['facebook_share'] ) && $post['facebook_share'] == 'on' ) ? 1 : 0;
			$param['vkontakte_share'] = ( isset( $post['vkontakte_share'] ) && $post['vkontakte_share'] == 'on' ) ? 1 : 0;
			$param['google_share']    = ( isset( $post['google_share'] ) && $post['google_share'] == 'on' ) ? 1 : 0;
			$param['linkedin_share']  = ( isset( $post['linkedin_share'] ) && $post['linkedin_share'] == 'on' ) ? 1 : 0;

			if ( $param['tweet_share'] == 0 && $param['facebook_share'] == 0 && $param['vkontakte_share'] == 0 && $param['google_share'] == 0 && $param['linkedin_share'] == 0 ) {
				$error_message = __( 'At least one social network should be selected', DECOM_LANG_DOMAIN );
				echo wp_send_json( array( 'error' => $error_message ) );
				exit();
			}
		} else {
			$param['tweet_share']     = 0;
			$param['facebook_share']  = 0;
			$param['vkontakte_share'] = 0;
			$param['google_share']    = 0;
			$param['linkedin_share']  = 0;
		}

		if ( isset( $post['enable_embed_links'] ) && $post['enable_embed_links'] == 'on' ) {
			$param['max_embed_links_count'] = $post['max_embed_links_count'];
		} else {
			$param['max_embed_links_count'] = 3;
		}

		$param['decom_your_affiliate_id'] = intval( $post['decom_your_affiliate_id'] );


		if ( isset( $post['best_comment_min_likes_count'] ) ) {
			$best_comment_min_likes_count = (int) $post['best_comment_min_likes_count'];
			if ( $best_comment_min_likes_count <= 0 ) {
				$best_comment_min_likes_count = 5;
			}
			$param['best_comment_min_likes_count'] = $best_comment_min_likes_count;
		}

		$param['display_avatars_right'] = ( isset( $post['display_avatars_right'] ) && $post['display_avatars_right'] == 'on' ) ? 1 : 0;

		$param['display_round_avatars'] = ( isset( $post['display_round_avatars'] ) && $post['display_round_avatars'] == 'on' ) ? 1 : 0;


		if ( isset( $post['max_size_uploaded_images'] ) ) {
			$max_size_uploaded_images = (int) $post['max_size_uploaded_images'];
			if ( $max_size_uploaded_images <= 0 ) {
				$max_size_uploaded_images = 5;
			}
			$model_options->updateOption( 'max_size_uploaded_images', $max_size_uploaded_images );
			$param['max_size_uploaded_images'] = $max_size_uploaded_images;
		}

		if ( isset( $post['time_editing_deleting_comments'] ) ) {
			$time_editing_deleting_comments = (int) $post['time_editing_deleting_comments'];
			if ( $time_editing_deleting_comments <= 0 ) {
				$time_editing_deleting_comments = 1;
			}
			$param['time_editing_deleting_comments'] = $time_editing_deleting_comments;
		} else {
			$param['time_editing_deleting_comments'] = 30;
		}

		$param['comment_form_up']              = ( isset( $post['comment_form_up'] ) ) ? 1 : 0;
		$param['enable_lazy_comments_loading'] = ( isset( $post['enable_lazy_comments_loading'] ) ) ? 1 : 0;
		$param['enable_dislike']               = ( isset( $post['enable_dislike'] ) ) ? 1 : 0;

		$param['enable_field_website'] = ( isset( $post['enable_field_website'] ) && $post['enable_field_website'] == 'on' ) ? 1 : 0;


		if ( array_key_exists( 'avatar', $param ) ) {
			if ( $param['avatar'] != '' ) {
				$model_options->updateWPOption( 'avatar_default', $param['avatar'] );
			}
		}

		if ( $param['enable_lazy_comments_loading'] ) {
			$enable_lazy_comments_loading = $model_options->getOption( 'enable_lazy_comments_loading' );
			if ( $enable_lazy_comments_loading == '' ) {
				$model_options->updateWPOption( 'page_comments', 1 );
			}
		}

		// Save Notifications
// ##############
		foreach ( $post as $notification => $text ) {
			if ( ! preg_match( '/email-/', $notification, $match ) ) {
				continue;
			}
			$string = '$notifications';
			$st     = explode( '-', $notification );
			foreach ( $st as $val ) {
				$string .= '["' . trim( strip_tags( stripcslashes( $val ) ) ) . '"]';
			}
			$text = trim( $text );
			if ( ! $text ) {
				echo wp_send_json( array( 'error' => __( 'The changes are not saved! Some fields are not completed.', DECOM_LANG_DOMAIN ) ) );
				exit;
			}
			eval( "$string = \"$text\";" );
		}

		DECOM_Loader_MVC::getComponentModel( DECOM_COMPONENT_NOTIFICATION, DECOM_COMPONENT_NOTIFICATION )->updateNotificationPostValues( $notifications );

		$result = $model_options->updateOptions( $param );

		if ( $result && ! $error_message ) {
			echo wp_send_json( array(
				'success' => __( 'Changes saved successfully.', DECOM_LANG_DOMAIN ),
				'post'    => $post
			) );
		} else {
			echo wp_send_json( array( 'error' => __( $error_message, DECOM_LANG_DOMAIN ) ) );
		}
		exit;
	}

	public function valStr( $value ) {

		return trim( strip_tags( stripslashes( $value ) ) );
	}
}

