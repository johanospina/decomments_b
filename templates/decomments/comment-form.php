<?php
echo '<span id="decom_default_position_form_add" style="display: none"></span>';

if ( get_option( 'show_avatars' ) ) {
	$da_position = $settings['display_avatars_right'] ? ' decomments-avatar-right' : '';
} else {
	$da_position = ' no-avatar';
}

?>

<div id="decomments-form-add-comment" class="decomments-addform<?php echo $da_position; ?>" data-site-url="<?php echo site_url(); ?>">

	<div class="decomments-social-login-widget">
		<?php
		do_action( 'comment_form_top' );
		?>
	</div>

	<div class="decomments-addform-title">

		<h3><?php echo apply_filters( 'decomments_add_comment_title', esc_html__( 'Add comment', DECOM_LANG_DOMAIN ) ); ?></h3>

	</div>

	<?php if ( ! is_user_logged_in() ) {
		echo $login_form;
	} ?>


	<div class="decomments-addform-head"<?php if ( is_user_logged_in() ) : echo ' data-full="short"'; endif; ?>>

		<?php if ( ! $decom_settings['disable_display_logo'] ) {
			if ( empty( $decom_settings['decom_your_affiliate_id'] ) ) {
				$link = "https://decomments.com";
			} else {
				$link = "https://decomments.com?ref=" . $decom_settings['decom_your_affiliate_id'];
			}
			?>
			<a href="<?php echo $link; ?>" class="decomments-affilate-link">
				<img class="svg" src="<?php echo DECOM_TEMPLATE_URL_DEFAULT; ?>assets/images/svg/logo.svg" width="78" height="21" />
			</a>
		<?php } ?>

		<?php if ( $decom_settings['output_subscription_rejoin'] || $decom_settings['output_subscription_comments'] ) { ?>
			<?php if ( is_user_logged_in() ) { ?>
				<a class="decomments-logout-link" href="<?php echo wp_logout_url( home_url() . $_SERVER['REQUEST_URI'] ); ?>"><?php _e( 'Log out', DECOM_LANG_DOMAIN ); ?></a>
			<?php } ?>

			<nav class="decomments-subscribe-block">

				<span class="decomments-subscribe-show"><i class="decomments-icon-quick-contacts-mail"></i><?php _e( 'Subscribe', DECOM_LANG_DOMAIN ); ?></span>

				<span class="decomments-subscribe-links">
					<?php $subcribe_block = '';

					if ( $decom_settings['output_subscription_rejoin'] ) {
						$active = $settings['mark_subscription_rejoin'] ? ' active' : '';
						$subcribe_block .= '<a class="decomments-checkbox' . $active . '" href="javascript:void(0)" name="subscribe_my_comment">' . __( 'Replies to my comments', DECOM_LANG_DOMAIN ) . '</a>';
					}
					if ( $decom_settings['output_subscription_comments'] ) {
						$active = $settings['mark_subscription_comments'] ? ' active' : '';
						$subcribe_block .= '<a class="decomments-checkbox' . $active . '" href="javascript:void(0)" name="subscribe_all_comments">' . __( 'All comments', DECOM_LANG_DOMAIN ) . '</a>';
					}
					echo $subcribe_block; ?>
				</span>
			</nav>


		<?php } ?>


		<nav class="descomments-form-nav">
			<a title="<?php _e( 'Add a quote', DECOM_LANG_DOMAIN ) ?>" class="decomments-add-blockquote " onclick="decom.showModal(this,jQuery('.decomments-comment-section').attr('data-modal-quote')); return false;"><i class="decomments-icon-format-quote"></i></a>
			<a title="<?php _e( 'Add a picture', DECOM_LANG_DOMAIN ) ?>" class="decomments-add-image " onclick="decom.showModal(this,jQuery('.decomments-comment-section').attr('data-modal-addimage')); return false;" data-width="500" data-height="120"><i class="decomments-icon-insert-photo"><img class="svg" src="<?php echo DECOM_TEMPLATE_URL_DEFAULT; ?>assets/images/svg/photo.svg" width="28" height="23" /></i></a>
		</nav>
	</div>


	<div class="decomments-addform-body"<?php if ( is_user_logged_in() ) : echo ' data-full="short"'; endif; ?>>

		<?php if ( is_user_logged_in() ) : echo $login_success; endif; ?>

		<input type="hidden" name="social_icon" id="decomments-social-icon" value="<?php echo $social_iсon ?>">

		<textarea rows="5" cols="30" class="decomments-editor"></textarea>

		<div class="decomments-commentform-message">
			<i class="decomments-icon-warning"></i>
			<span><?php _e( 'Sorry, you must be logged in to post a comment.', DECOM_LANG_DOMAIN ); ?></span>
		</div>

		<span class="decomments-loading"><div class="loader-ball-scale">
				<div></div>
				<div></div>
				<div></div>
			</div></span>

		<button class="decomments-button decomments-button-send"><?php esc_html_e( 'Submit', DECOM_LANG_DOMAIN ) ?></button>
		<button class="decomments-button decomments-button-cancel"><?php esc_html_e( 'Cancel', DECOM_LANG_DOMAIN ) ?></button>

	</div>

</div>

<div class="decomments-form-popup">


	<div id="decom_alert_void-block" class="decomments-popup-style" style="display:none;">
		<div class="decomments-popup-style">
			<div id="decom-alert-void-text" class="decom-popup-box decom-quote-box">
				<p></p>
			</div>
		</div>
	</div>
</div>