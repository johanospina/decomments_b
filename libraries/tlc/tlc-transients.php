<?php

if ( ! class_exists( 'TLC_Transient_Update_Server' ) )
	require_once dirname( __FILE__ ) . '/class-tlc-transient-update-server.php';

new TLC_Transient_Update_Server;

if ( ! class_exists( 'TLC_Transient' ) )
	require_once dirname( __FILE__ ) . '/class-tlc-transient.php';

require_once dirname( __FILE__ ) . '/functions.php';

