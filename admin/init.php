<?php
defined( 'ABSPATH' ) or die( 'Cheatin\' uh?' );

define( 'DECOM_ADMIN_PATH', dirname( __FILE__ ) . '/' );


if ( is_admin() ) {
	require_once DECOM_ADMIN_PATH . 'modules/decom-updater/init.php';

	require_once DECOM_ADMIN_PATH . 'admin.php';
}