<?php

class DECOM_Component_MultisiteSupport extends DECOM_Component {
	private static $component = 'multisite-support';

	public static function onActivation() {

	}

	public static function onDeactivation() {

	}

	public static function onWpmuNewBlog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
		$application = DECOM_Loader::getApplication();

		$application->invokeBlogLibraries( 'onActivation', $blog_id );
		$application->invokeBlogComponents( 'onActivation', $blog_id );
	}

	public static function onWpmuDeleteBlog( $blog_id, $drop ) {
		$application = DECOM_Loader::getApplication();

		$application->invokeBlogLibraries( 'onDeactivation', $blog_id );
		$application->invokeBlogComponents( 'onDeactivation', $blog_id );
	}
}