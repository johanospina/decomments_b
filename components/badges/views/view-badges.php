<?php

class DECOM_View_Badges extends DECOM_View {
	public function __construct() {
		parent::__construct();
		DECOM_Component_Badges::registerAssets();
	}

	public function renderBadgesPage( $badges ) {
		include_once( dirname( __FILE__ ) . '/templates/badges-page.tpl.php' );
	}
}