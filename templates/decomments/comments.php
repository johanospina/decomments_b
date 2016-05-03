<?php

global $wp_query, $post;
$ajax_load_comments    = false;
$is_ajax_load_comments = apply_filters( 'decomments_load_comments_ajax', false );
if ( isset( $ajax ) ) {
	$post_id = $ajax_post_id;
} elseif ( isset( $jScroll_post_id ) ) {
	if ( $is_ajax_load_comments ) {
		$ajax_load_comments = true;
	}

	$post_id = $jScroll_post_id;
//	$post    = get_post( $post_id );
	query_posts( array( 'p' => $post_id ) );
//	setup_postdata( get_post( $post_id ) );
	echo '<div class="decom_dop_bloc">';
	$_SERVER['REQUEST_URI'] = get_permalink( $post_id );
} else {
	$post    = get_post();
	$post_id = $post->ID;
	echo '<div class="decom_dop_bloc">';
}
$decom_settings = decom_get_options();
$settings       = $decom_settings;
if ( isset( $decom_settings['show_avatars'] ) && intval( $decom_settings['show_avatars'] ) ) {
	$da_position = $decom_settings['display_avatars_right'] ? ' decomments-avatar-right' : '';
} else {
	$da_position = ' no-avatar';
}
$current_user_id = DECOM_Loader_MVC::getComponentModel( 'comments', 'user' )->getCurrentUserId();
$model_comments  = DECOM_Loader_MVC::getComponentModel( 'comments', 'comments' );
$model_votes     = DECOM_Loader_MVC::getComponentModel( 'comments', 'comments-votes' );
//print_r( $decom_settings );
$display_round_avatars = isset( $decom_settings['display_round_avatars'] ) ? intval( $decom_settings['display_round_avatars'] ) : 0;
$comments_per_page     = - 1;
$is_comments_close     = decom_is_comment_close( $post );
$comments_per_page     = ( isset( $decom_settings['page_comments'] ) && intval( $decom_settings['page_comments'] ) && isset( $decom_settings['comments_per_page'] ) ) ? intval( $decom_settings['comments_per_page'] ) : 0;
$all_comments          = $model_comments->getComments( $post_id );
if ( $all_comments ) {
	$all_comments_compact = $model_votes->filter_array_comments( $all_comments );
	$user_voice           = $model_votes->user_voice_like( $all_comments_compact );
} else {
	$user_voice = array();
}
$max_comments_votes_1  = array();
$max_comments_votes_2  = array();
$comments_branch_1     = array();
$comments_branch_2     = array();
$comments_branch_merge = array();
$user_sort             = getUserSort( $current_user_id );
if ( $decom_settings['show_two_comments_highest_ranking_top_list'] ) {
	$max_comments_votes = $model_comments->getPostMaxCommentKarma( $post_id, $decom_settings['best_comment_min_likes_count'], $user_sort );
	if ( array_key_exists( 0, $max_comments_votes ) ) {
		$comments_branch_1    = $model_comments->getCommentsBranchByParentId( $post_id, $max_comments_votes[0]->comment_ID );
		$max_comments_votes_1 = $model_comments->getCommentsByIds( $comments_branch_1 );
	}
	if ( array_key_exists( 1, $max_comments_votes ) ) {
		$comments_branch_2    = $model_comments->getCommentsBranchByParentId( $post_id, $max_comments_votes[1]->comment_ID );
		$max_comments_votes_2 = $model_comments->getCommentsByIds( $comments_branch_2 );
	}
	if ( is_array( $comments_branch_1 ) && is_array( $comments_branch_2 ) ) {

		$comments_branch_merge = array_merge( $comments_branch_1, $comments_branch_2 );
	} else {
		if ( is_array( $comments_branch_1 ) ) {
			$comments_branch_merge = $comments_branch_1;
		}
	}
}
$comments = $model_comments->getComments( $post_id, $comments_branch_merge );
if ( $comments ) {
	$pages_count = get_comment_pages_count( $comments, $comments_per_page );
} else {
	$pages_count = 0;
}
if ( isset( $ajaxCurrentPage ) && $ajaxCurrentPage ) {
	if ( $comment_paginate_action == 'next' && $pages_count > $ajaxCurrentPage ) {
		$current_comments_page = $ajaxCurrentPage + 1;
	} elseif ( $comment_paginate_action == 'previous' && $pages_count > 1 ) {
		$current_comments_page = $ajaxCurrentPage - 1;
	} elseif ( $comment_paginate_action == 'end' ) {
		$current_comments_page = $pages_count;
	} elseif ( $comment_paginate_action == 'beginning' ) {
		$current_comments_page = 1;
	}
} elseif ( isset( $wp_query->query_vars['cpage'] ) ) {
	$page                  = $wp_query->query_vars['cpage'];
	$current_comments_page = $wp_query->query_vars['cpage'];
} else {
	$current_comments_page = 1;
}
$votes            = $model_votes->choiceVoiceAllComment( $comments );
$height_auth_form = 200;
if ( isset( $ajax ) ) {
	wp_list_comments( array(
		'callback'          => 'decom_render_comment',
		'end-callback'      => 'decom_end_comment',
		'style'             => 'div',
		'walker'            => DECOM_Loader_MVC::getComponentClass( 'comments', 'comments-walker' ),
		'settings'          => $settings,
		'votes'             => $votes,
		'user_voice'        => $user_voice,
		'reverse_top_level' => $user_sort,
		'ajax_post_id'      => $ajax_post_id,
		'per_page'          => $comments_per_page,
		'page'              => $ajax_page_num
	), $comments );
	die();
}
if ( post_password_required() ) {
	return;
}
?>
<a name="comments"></a>
<div id="decomments-comment-section" class="decomments-comment-section"
     data-modal-alert='<?php include 'parts/modal_alerts.php'; ?>'
     data-modal-addimage='<?php include 'parts/modal_addimage.php'; ?>'
     data-modal-quote='<?php include 'parts/modal_quote.php'; ?>'
     data-modal-preview='<?php include 'parts/modal_preview.php'; ?>'
     data-modal-sub='<?php include 'parts/modal_subscribe.php'; ?>'
     data-post_id="<?php echo $post_id; ?>"
     data-user_id="<?php echo $current_user_id; ?>"
     data-is_need_logged="<?php echo get_option( 'comment_registration' ) ? 1 : 0 ?>"
     data-lang="<?php echo ( function_exists( 'qtrans_getLanguage' ) ) ? qtrans_getLanguage() : 'en'; ?>"
     data-decom_comment_single_translate="<?php echo ' ' . _n( 'comment', 'comments', 1, DECOM_LANG_DOMAIN ); ?>"
     data-decom_comment_twice_translate="<?php echo ' ' . _n( 'comment', 'comments', 2, DECOM_LANG_DOMAIN ); ?>"
     data-decom_comment_plural_translate="<?php echo ' ' . _n( 'comment', 'comments', 5, DECOM_LANG_DOMAIN ); ?>"
     data-multiple_vote="<?php echo ( ! empty( $decom_settings['enable_dislike'] ) ) ? 1 : 0; ?>"
     data-text_lang_comment_deleted='<?php _e( 'Comment deleted', DECOM_LANG_DOMAIN ); ?>'
     data-text_lang_edited="<?php _e( 'Edited at', DECOM_LANG_DOMAIN ); ?>"
     data-text_lang_delete="<?php _e( 'Delete', DECOM_LANG_DOMAIN ); ?>"
     data-text_lang_not_zero="<?php _e( 'Field is not null', DECOM_LANG_DOMAIN ); ?>"
     data-text_lang_required="<?php _e( 'This field is required.', DECOM_LANG_DOMAIN ); ?>"
     data-text_lang_checked="<?php _e( 'Mark one of the points', DECOM_LANG_DOMAIN ); ?>"
     data-text_lang_completed="<?php _e( 'Operation completed', DECOM_LANG_DOMAIN ); ?>"
     data-text_lang_items_deleted="<?php _e( 'The items have been deleted', DECOM_LANG_DOMAIN ); ?>"
     data-text_lang_close="<?php esc_html_e( 'Close', DECOM_LANG_DOMAIN ); ?>"
     data-text_lang_loading="<?php esc_html_e( 'Loading...', DECOM_LANG_DOMAIN ); ?>">
	<?php
	wp_comment_form_unfiltered_html_nonce();
	add_thickbox();
	$social_iсon   = '';
	$login_success = '';
	if ( is_user_logged_in() ) {
		$current_user   = wp_get_current_user();
		$active_plugins = $decom_settings['active_plugins'];
		if ( is_array( $active_plugins ) && in_array( 'wordpress-social-login/wp-social-login.php', $active_plugins ) ) {
			$social_iсon = $model_comments->selectSocial( $current_user->ID ) ? $model_comments->selectSocial( $current_user->ID ) : '';
		}
		$display_round_avatars_class = $display_round_avatars ? ' decomments-round-avatar' : '';
		$login_success .= '<figure class="decomments-user-thumb' . $display_round_avatars_class . '">';
		$login_success .= get_avatar( $current_user->ID, 80, '', $current_user->display_name );
		$login_success .= '</figure>';
	} else {
		$login_form = '';
		ob_start();
		?>
		<div class="decomments-enter-row">

			<form id="decomments-enterform" class="decomments-enterform" action="#" method="post">

				<fieldset>

					<div class="de-form-field">
						<label for="decom-name-author"><?php _e( 'Name', DECOM_LANG_DOMAIN ); ?><em>*</em></label>
						<input id="decom-name-author" type="text" value="<?php echo $_COOKIE['decommentsa']; ?>" />
					</div>

					<div class="de-form-field">
						<label for="decom-mail-author"><?php _e( 'E-mail', DECOM_LANG_DOMAIN ); ?><em>*</em></label>

						<input id="decom-mail-author" type="text" value="<?php echo $_COOKIE['decommentse']; ?>" />
					</div>

					<?php if ( $settings['enable_field_website'] == 1 ) { ?>
						<div class="de-form-field">
							<label for="decom-site-author"><?php _e( 'Website', DECOM_LANG_DOMAIN ); ?></label>

							<input id="decom-site-author" type="text" value="<?php echo $_COOKIE['decommentsu']; ?>" />
						</div>
					<?php } ?>

				</fieldset>

				<p class="decomments-enterform-message decomments-error-message">
					<i class="decomments-icon-warning"></i>
					<span id="decomments-login-form-message"><?php esc_html_e( 'E-mail is already registered on the site. Please use the ', DECOM_LANG_DOMAIN ) ?></span>
					<a href="#" id="decomments-show-loginform"><?php echo ucfirst( __( 'login form', DECOM_LANG_DOMAIN ) ); ?></a>
					<?php esc_html_e( ' or ', DECOM_LANG_DOMAIN ) ?>
					<a href="#" id="decomments-show-enterform"><?php esc_html_e( 'enter another', DECOM_LANG_DOMAIN ) ?></a>.
				</p>

			</form>

			<form id="decomments-loginform" class="decomments-loginform" action="<?php echo get_option( 'home' ) . '/wp-login.php' ?>" method="post">
				<p class="decomments-loginform-message decomments-error-message">
					<i class="decomments-icon-warning"></i> <?php esc_html_e( 'You entered an incorrect username or password', DECOM_LANG_DOMAIN ) ?>
				</p>
				<fieldset>
					<div class="de-form-field">
						<label for="log"><?php esc_html_e( 'Name', DECOM_LANG_DOMAIN ) ?>:</label>
						<input type="text" name="log" id="log" value="" size="20" />
					</div>
					<div class="de-form-field">
						<label for="pwd"><?php echo esc_html_e( 'Password', DECOM_LANG_DOMAIN ) ?>:</label>
						<input type="password" name="pwd" id="pwd" size="20" />
					</div>
				</fieldset>
				<button type="submit" class="decomments-button decomments-button-submit"><?php esc_html_e( 'Log in', DECOM_LANG_DOMAIN ); ?></button>
				<button type="button" id="decomments-login-form-another" class="decomments-button decomments-button-submit"><?php echo ucfirst( __( 'enter another', DECOM_LANG_DOMAIN ) ); ?></button>
				<input id="submit-form" type="hidden" name="submit" />
				<input type="hidden" name="redirect_to" value="" />
				<?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
			</form>
		</div>
		<?php

		$login_form = ob_get_contents();
		ob_end_clean();
	}

	if ( $settings['comment_form_up'] ) {
		if ( $is_comments_close ) {
			if ( ! is_page() ) {
				echo apply_filters( 'decomments_comments_close_text', '<br /><br /> <p class="nocomments">' . esc_html__( 'Comments are closed.', DECOM_LANG_DOMAIN ) . '</p>' );
			}
		} else {
			include_once( 'comment-form.php' );
		}
	}

	$number        = get_comments_number();
	$have_comments = $number > 0 ? true : false;

	if ( $have_comments ) { ?>

		<div class="decomments-head">

			<?php
			if ( $settings['output_total_number_comments_top'] ) {

				$display = ( $number == 0 ) ? ' decomments-hide' : ' decomments-block';

				$comment_str = '<h3><i><span class="decomments-comments-number' . $display . '">' . $number . '</span><span class="decomments-comment-title">' . ' ' . _n( 'comment', 'comments', $number, DECOM_LANG_DOMAIN ) . '</span></i></h3>';
				echo $comment_str;
			}
			?>
			<form class=" decom_comments_sort decomments-comment-sort" action="#comments" method="post" <?php echo $have_comments ? '' : 'style="display:none;"'; ?>>
				<label><?php esc_html_e( 'Sort', DECOM_LANG_DOMAIN ); ?>:</label>
				<dl class="de-select de-select-filter">
					<dt><?php esc_html_e( 'by Oldest', DECOM_LANG_DOMAIN ); ?></dt>
					<dd name="decom_comments_sort" class="decomments-select">
						<a onclick="decom.sortComments(this)" href="#" data-sort="rate"<?php selected( $user_sort, 'rate' ); ?>>
							<i class="decomments-icon-thumb-down"></i> <?php esc_html_e( 'by Best', DECOM_LANG_DOMAIN ); ?>
						</a>
						<a onclick="decom.sortComments(this)" href="#" data-sort="newer"<?php selected( $user_sort, 'newer' ); ?>>
							<i class="decomments-icon-thumb-down"></i> <?php esc_html_e( 'by Newest', DECOM_LANG_DOMAIN ); ?>
						</a>
						<a onclick="decom.sortComments(this)" class="current" href="#" data-sort="older"<?php selected( $user_sort, 'older' ); ?>>
							<i class="decomments-icon-thumb-down"></i> <?php esc_html_e( 'by Oldest', DECOM_LANG_DOMAIN ); ?>
						</a>
					</dd>
				</dl>
			</form>
		</div>
		<div class="decomments-comment-list<?php echo $da_position; ?>">
			<div class="loader-ball-scale lbs-remove">
				<div></div>
				<div></div>
				<div></div>
			</div>
			<?php
			if ( ( $is_ajax_load_comments && $ajax_load_comments ) || ! $is_ajax_load_comments ) {
				if ( count( $max_comments_votes_1 ) > 0 ) {
					wp_list_comments( array(
						'callback'     => 'decom_render_comment',
						'end-callback' => 'decom_end_comment',
						'style'        => 'div',
						'walker'       => DECOM_Loader_MVC::getComponentClass( 'comments', 'comments-walker' ),
						'settings'     => $settings,
						'votes'        => $votes,
						'user_voice'   => $user_voice,
						'max'          => true
					), $max_comments_votes_1 );
				}


				if ( count( $max_comments_votes_2 ) > 0 ) {
					wp_list_comments( array(
						'callback'     => 'decom_render_comment',
						'end-callback' => 'decom_end_comment',
						'style'        => 'div',
						'walker'       => DECOM_Loader_MVC::getComponentClass( 'comments', 'comments-walker' ),
						'settings'     => $settings,
						'votes'        => $votes,
						'user_voice'   => $user_voice,
						'max'          => true
					), $max_comments_votes_2 );
				}

				wp_list_comments( array(
					'callback'          => 'decom_render_comment',
					'end-callback'      => 'decom_end_comment',
					'style'             => 'div',
					'walker'            => DECOM_Loader_MVC::getComponentClass( 'comments', 'comments-walker' ),
					'settings'          => $settings,
					'votes'             => $votes,
					'user_voice'        => $user_voice,
					'reverse_top_level' => $user_sort,
					'per_page'          => $comments_per_page,
					'page'              => $current_comments_page ? $current_comments_page : '1'
				), $comments );
			}
			?>
		</div><!-- .commentlist -->
		<?php
		$deco_ajax_navy = $settings['deco_ajax_navy'];
		if ( $deco_ajax_navy ) {
			if ( $deco_ajax_navy == 'deco_show_more_comments_prevnext' ) { ?>
				<nav id="comment-nav-below" class="navigation decomments-navigation" role="navigation" page="<?php echo $current_comments_page; ?>" pages-count="<?php echo $pages_count; ?>">
					<?php otherPagination( $pages_count, $post_id, $current_comments_page ); ?>
				</nav>
				<?php
			} else if ( $deco_ajax_navy == 'deco_show_more_comments_lazy' && $pages_count > 1 ) { ?>
				<div class="decomments-ajax-paginate-lazy" data-cur-page="1" data-comments-perpage="<?php echo $comments_per_page; ?>" data-page-count="<?php echo $pages_count; ?>" page="2" pages-count="<?php echo $pages_count; ?>">
					<div class="decomments-paginate-loader" style="display: none;">
						<div class="loader-ball-scale">
							<div></div>
							<div></div>
							<div></div>
						</div>
					</div>
					<a href="#" class="decomments-button decomments-loader-btn"><?php esc_html_e( 'Load more', DECOM_LANG_DOMAIN ); ?>
						<i class="decomments-icon-angle-double-right"></i></a>
				</div>
				<?php
			} else if ( $deco_ajax_navy == 'deco_show_more_comments_onebutton' && $pages_count > 1 ) { ?>
				<div class="decomments-ajax-paginate" data-cur-page="1" data-comments-perpage="<?php echo $comments_per_page; ?>" data-page-count="<?php echo $pages_count; ?>" page="2" pages-count="<?php echo $pages_count; ?>">
					<div class="decomments-paginate-loader" style="display: none;">
						<div class="loader-ball-scale">
							<div></div>
							<div></div>
							<div></div>
						</div>
						<span style="position: relative;top: -11px;"><?php esc_html_e( 'Loading comments..', DECOM_LANG_DOMAIN ); ?></span>
					</div>
					<a href="#" class="decomments-button decomments-loader-btn"><?php esc_html_e( 'Load more', DECOM_LANG_DOMAIN ); ?>
						<i class="decomments-icon-angle-double-right"></i></a>
				</div>
				<?php
			}
		}
	} // have_comments()
	if ( ! $settings['comment_form_up'] ) {
		if ( $is_comments_close ) {
			if ( ! is_page() ) {
				?>
				<br /><br />
				<p class="nocomments"><?php esc_html_e( 'Comments are closed.', DECOM_LANG_DOMAIN ); ?></p>
				<?php
			}
		} else {
			include_once( 'comment-form.php' );
		}
	} ?>
</div>
</div>
