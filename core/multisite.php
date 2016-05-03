<?php

class DECOM_Multisite {
	public static function isChildSite() {
		$result   = false;
		$is_multi = self::isMultisite();
		if ( $is_multi ) {
			if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
				require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
			}
			if ( is_plugin_active_for_network( DECOM_PLUGIN_NAME ) ) {
				$result = true;
			}
		}

		return $result;

	}

	public static function isMultisite() {
		return is_multisite() ? true : false;
	}

	private static function isMainSite() {
		$current_blog_id = get_current_blog_id();

		return is_main_site( $current_blog_id ) ? true : false;
	}

	public static function getMultisiteBlogs() {
		global $wpdb;
		$blogids                = array();
		$blogids['old_blog']    = self::getOldBlog();
		$blogids['other_blogs'] = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

		if ( array_key_exists( 'other_blogs', $blogids ) && count( $blogids['other_blogs'] ) > 0 ) {
			return $blogids['other_blogs'];
		}

		return array();


	}

	public static function isActiveForNetwork() {
		return is_plugin_active_for_network( DECOM_PLUGIN_NAME );
	}

	public static function switchToBlog( $blog_id ) {
		switch_to_blog( $blog_id );
	}

	public static function getOldBlog() {
		global $wpdb;

		return $wpdb->blogid;
	}

	public static function isNetworkWide() {
		if ( self::isMultisite() && is_network_only_plugin( DECOM_PLUGIN_NAME ) ) {
			return true;
		}

		return false;
	}
}
