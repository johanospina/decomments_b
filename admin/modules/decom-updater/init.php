<?php

require_once DECOM_PLUGIN_PATH . '/admin/modules/decom-updater/decom-updater.php';

/* hook updater to init */
add_action( 'admin_init', 'decomments_updater_init' );
function decomments_updater_init() {
	new Decomments_Plugin_Update();
	if ( isset( $_GET['decomments_reset_update_plugin'] ) ) {
		decomments_updater_custom_time();
	}
}

function decomments_updater_custom_time() {
	$current = get_site_transient( 'update_plugins' );
	if ( isset( $_GET['decomments_reset_update_plugin'] ) ) {
		print_r( $current );
	}
	set_site_transient( 'update_plugins', '' );
	wp_update_plugins();
}
