<?php

class DECOM_View_SettingsPage extends DECOM_View {

	public function renderSettingsPage( $settings ) {

		if ( isset( $_GET['result'] ) ) {
			if ( $_GET['result'] == 'error' ) {
				$message = '<div class="alert alert-error">Save bug</div>';
			} elseif ( $_GET['result'] == 'update' ) {
				$message = '<div class="alert alert-success">' . __( "Settings were saved successfully", DECOM_LANG_DOMAIN ) . '.</div>';
			}
		}

		$setting_name = $this->get_names_settings();

		$errMsg = false;
		if ( isset( $_GET['decom-error'] ) && $_GET['decom-error'] == 'file-validation' && isset( $_GET['errorCode'] ) ) {
			DECOM_Loader_MVC::includeComponentClass( 'comments', 'files-upload' );
			$code = $param1 = $param2 = '';
			if ( isset( $_GET['errorCode'] ) ) {
				$code = $_GET['errorCode'];
			}
			if ( isset( $_GET['param'] ) ) {
				$param1 = $_GET['param'];
			}
			if ( isset( $_GET['param2'] ) ) {
				$param2 = $_GET['param2'];
			}
			$errMsg = DECOM_FilesUpload::getErrorByCode( $code, $param1, $param2 );
		}

		include_once( dirname( __FILE__ ) . '/templates/settings-page.tpl.php' );
	}

	public function get_names_settings() {

		$setting_name                                   = array();
		$setting_name['avatar_size']                    = __( 'Standart avatar size', DECOM_LANG_DOMAIN );
		$setting_name['time_editing_deleting_comments'] = __( 'Time to edit / delete comments (minutes)', DECOM_LANG_DOMAIN );
		$setting_name['number_comments_per_page']       = __( 'Number of comments per page', DECOM_LANG_DOMAIN );
		$setting_name['follow']                         = __( 'Add dofollow or nofollow to commentators links?', DECOM_LANG_DOMAIN );
		$setting_name['output_subscription_comments']   = __( 'Display "subscribe to new comments" for post ', DECOM_LANG_DOMAIN );
		$setting_name['mark_subscription_comments']     = __( 'Allow "subscribe to new comments" for post by default', DECOM_LANG_DOMAIN );
		$setting_name['output_subscription_rejoin']     = __( 'Display "subscribe to new comments to this comment"', DECOM_LANG_DOMAIN );
		$setting_name['mark_subscription_rejoin']       = __( 'Allow "subscribe to new replies to this comment" by default', DECOM_LANG_DOMAIN );
		$setting_name['allocate_comments_author_post']  = __( 'Allocate comments of post author', DECOM_LANG_DOMAIN );
		$setting_name['decomments_reset_color']         = __( 'Reset Color', DECOM_LANG_DOMAIN );
		$setting_name['decomments_set_color']           = __( 'Set Color', DECOM_LANG_DOMAIN );
		$setting_name['decomments_main_color_theme']    = __( 'Main color', DECOM_LANG_DOMAIN );
		$setting_name['allow_html_in_comments']         = __( 'Allow HTML in comments', DECOM_LANG_DOMAIN );
		$setting_name['output_numbers_comments']        = __( 'Display numbers of comments', DECOM_LANG_DOMAIN );
		$setting_name['allow_quote_comments']           = __( 'Allow to quote comments', DECOM_LANG_DOMAIN );
		$setting_name['decom_disable_replies']          = __( 'Disable replies', DECOM_LANG_DOMAIN );

		$setting_name['output_total_number_comments_top'] = __( 'Display the total number of comments at the top', DECOM_LANG_DOMAIN );
		$setting_name['enable_client_validation_fields']  = __( 'Enable client validation fields', DECOM_LANG_DOMAIN );
		$setting_name['sort_comments']                    = __( 'Sort comments', DECOM_LANG_DOMAIN );

		$setting_name['show_comments_negative_rating_low_opacity']  = __( 'Show low opacity comments with negative ratings', DECOM_LANG_DOMAIN );
		$setting_name['show_two_comments_highest_ranking_top_list'] = __( 'Show two the highest ranking comments at the top of the list', DECOM_LANG_DOMAIN );
		$setting_name['show_two_comments_highest_ranking_top_list'] = __( 'Show two the highest ranking comments at the top of the list', DECOM_LANG_DOMAIN );
		$setting_name['best_comment_min_likes_count']               = __( 'Minimum number of likes to display a comment at the top of the list', DECOM_LANG_DOMAIN );
		$setting_name['max_size_uploaded_images']                   = __( 'Maximum size of uploaded images', DECOM_LANG_DOMAIN );

		$setting_name['display_avatars_right'] = __( "Display author's avatar from the right side", DECOM_LANG_DOMAIN );
		$setting_name['display_round_avatars'] = __( "Display round avatars", DECOM_LANG_DOMAIN );
		$setting_name['comment_form_up']       = __( 'Display comment box on top of list', DECOM_LANG_DOMAIN );

		$setting_name['enable_lazy_comments_loading'] = __( 'Allow endless feed', DECOM_LANG_DOMAIN );
		$setting_name['allow_lazy_load']              = __( 'Allow lazy load', DECOM_LANG_DOMAIN );
		$setting_name['enable_dislike']               = __( 'Allow dislikes', DECOM_LANG_DOMAIN );
		$setting_name['enable_embed_links']           = __( 'Enable embeding external media URLs', DECOM_LANG_DOMAIN );
		$setting_name['max_embed_links_count']        = __( 'Maximum external media URLs', DECOM_LANG_DOMAIN );

		$setting_name['disable_display_logo'] = __( "Don't display de:comments logo", DECOM_LANG_DOMAIN );

		$setting_name['deco_disable_css'] = __( 'Disable CSS plugin', DECOM_LANG_DOMAIN );

		$setting_name['deco_fb_enable_share'] = __( 'Facebook', DECOM_LANG_DOMAIN );
		$setting_name['deco_tw_enable_share'] = __( 'Twitter', DECOM_LANG_DOMAIN );
		$setting_name['deco_gp_enable_share'] = __( 'Google+', DECOM_LANG_DOMAIN );
		$setting_name['deco_ln_enable_share'] = __( 'LinkedIn', DECOM_LANG_DOMAIN );
		$setting_name['deco_vk_enable_share'] = __( 'Vkontakte', DECOM_LANG_DOMAIN );
		$setting_name['deco_ok_enable_share'] = __( 'Odnoklassniki', DECOM_LANG_DOMAIN );


		$setting_name['enable_field_website'] = __( 'Enable field  "Website" in the form of comments?', DECOM_LANG_DOMAIN );

		$setting_name['deco_comments_paginate'] = __( 'Enable comments pagination, set var:', DECOM_LANG_DOMAIN );

		return $setting_name;
	}
}

?>
