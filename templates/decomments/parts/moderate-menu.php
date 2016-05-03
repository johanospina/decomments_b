<?php if ( current_user_can( 'moderate_comments' ) ) {
	ob_start();
	?>
	<a href="javascript:void(0)" class="decomments-buttons-moderate"><i class="decomments-icon-moderate"></i></a>
	<div class="moderate-action">
		<a class="decomments-link-edit" data-id="<?php echo $comment->comment_ID; ?>" href="#"><?php _e( 'Edit', DECOM_LANG_DOMAIN ); ?></a>
		<a class="decomments-link-unapprove" href="#"><?php _e( 'Unapprove', DECOM_LANG_DOMAIN ); ?></a>
		<a class="decomments-link-spam" href="#"><?php _e( 'Spam', DECOM_LANG_DOMAIN ); ?></a>
		<a class="decomments-link-trash" href="#"><?php _e( 'Trash', DECOM_LANG_DOMAIN ); ?></a>
		<?php if ( decom_is_user_block( $comment->user_id, $comment->comment_author_email ) ) { ?>
			<a class="decomments-link-unblock-user" data-user-id="<?php echo $comment->user_id; ?>" data-user-email="<?php echo $comment->comment_author_email; ?>" data-user-action="unblock" href="#"><?php _e( 'Allow commenting', DECOM_LANG_DOMAIN ); ?></a>
		<?php } else { ?>
			<a class="decomments-link-block-user" data-user-id="<?php echo $comment->user_id; ?>" data-user-email="<?php echo $comment->comment_author_email; ?>" data-user-action="block" href="#"><?php _e( 'Disallow commenting', DECOM_LANG_DOMAIN ); ?></a>
		<?php } ?>
		<a class="decomments-link-remove-all-comments-user" data-user-id="<?php echo $comment->user_id; ?>" data-user-email="<?php echo $comment->comment_author_email; ?>" href="#"><?php _e( 'Delete all user\'s comments', DECOM_LANG_DOMAIN ); ?></a>
	</div>
	<?php
	$item_block .= ob_get_contents();
	ob_end_clean();
} ?>
