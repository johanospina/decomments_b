<?php
defined( 'ABSPATH' ) or die( 'Cheatin\' uh?' );

/**
 * Link to the configuration page of the plugin
 *
 * @since 1.0
 */
add_filter( 'plugin_action_links_' . plugin_basename( DECOM_FILE ), '__decomments_settings_action_links' );
/**
 * @param $actions
 *
 * @return mixed
 */
function __decomments_settings_action_links( $actions ) {
	array_unshift( $actions, sprintf( '<a href="%s">%s</a>', admin_url( 'edit-comments.php?page=decomments-index' ), __( 'Settings', DECOM_LANG_DOMAIN ) ) );

	return $actions;
}

/**
 * print js variable decomSettings
 *
 * @since 1.0
 */
add_action( 'admin_enqueue_scripts', '__decomments_decom_settings_variable', - 99 );
function __decomments_decom_settings_variable() {
	$site_url = site_url();
	?>
	<script type='text/javascript'>
		/* <![CDATA[ */
		var decomSettings = {
			"site_url"  : "<?php echo $site_url; ?>",
			"admin_ajax": "<?php echo $site_url; ?>/wp-admin/admin-ajax.php"
		};
		/* ]]> */
	</script>
	<?php
}


/**
 * @param $actions
 * @param $user_object
 *
 * @return mixed
 */
function __decomments_block_unblock_user_comment_leave_action_links( $actions, $user_object ) {
	$nonce = wp_create_nonce( '__decomments_block_unblock_user_comment_leave_' );
	if ( get_user_meta( $user_object->ID, 'decom_block_user_leave_comment', true ) ) {
		$actions['unblock_user_comment_leave'] = "<a href='" . admin_url( "users.php?action=allow_commenting&amp;user=$user_object->ID&amp;nonce=$nonce" ) . "'>" . __( 'Allow commenting', DECOM_LANG_DOMAIN ) . "</a>";
	} else {
		$actions['block_user_comment_leave'] = "<a href='" . admin_url( "users.php?action=disallow_commenting&amp;user=$user_object->ID&amp;nonce=$nonce" ) . "'>" . __( 'Disallow commenting', DECOM_LANG_DOMAIN ) . "</a>";
	}

	return $actions;
}

add_filter( 'user_row_actions', '__decomments_block_unblock_user_comment_leave_action_links', 10, 2 );


function __decomments_block_unblock_user_comment_leave_action() {
	if ( isset( $_GET['action'] ) && in_array( $_GET['action'], array(
			'allow_commenting',
			'disallow_commenting'
		) ) && wp_verify_nonce( $_GET['nonce'], '__decomments_block_unblock_user_comment_leave_' )
	) {
		$user_id = (int) $_GET['user'];
		switch ( $_GET['action'] ) {
			case 'allow_commenting' :
				update_user_meta( $user_id, 'decom_block_user_leave_comment', 0 );
				break;
			case 'disallow_commenting' :
				update_user_meta( $user_id, 'decom_block_user_leave_comment', 1 );
				break;
		}
		wp_safe_redirect( $_SERVER['HTTP_REFERER'] );
		die();
	}
}

add_action( 'admin_init', '__decomments_block_unblock_user_comment_leave_action' );