<?php

class DECOM_HooksHandler {

	/**
	 * Contain Application object
	 *
	 * @var Application|null
	 */
	private $application = null;

	/**
	 * Class constructor
	 *
	 * @param Application $application
	 */
	public function __construct( DECOM_Application $application ) {

		$this->application = $application;
	}

	/**
	 * Invokes on 'init' wordpress hook
	 */
	public function onInit() {

		$this->application->invokeLibraries( 'onAutoInclude' );
		$this->application->invokeComponents( 'onInit' );
	}

	public function onAdminInit() {
		$this->application->invokeLibraries( 'onAutoInclude' );
		$this->application->invokeComponents( 'onAdminInit' );

	}

	/**
	 * Invokes on plugin activation
	 */
	public function onActivation( $networkwide ) {

		$this->application->invokeNetworkLibraries( 'onActivation', $networkwide );
		$this->application->invokeNetworkComponents( 'onActivation', $networkwide );

	}

	/**
	 * Invokes on plugin deactivation
	 */
	public function onDeactivation( $networkwide ) {

		$this->application->invokeNetworkLibraries( 'onDeactivation', $networkwide );
		$this->application->invokeNetworkComponents( 'onDeactivation', $networkwide );

	}

	public function onWpmuNewBlog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
		$this->application->invokeComponents( 'onWpmuNewBlog', $blog_id, $user_id, $domain, $path, $site_id, $meta );
	}

	public function onWpmuDeleteBlog( $blog_id, $drop = false ) {
		$this->application->invokeComponents( 'onWpmuDeleteBlog', $blog_id, $drop );
	}

	/**
	 * Add menu items to admin panel menu
	 *
	 * @return bool
	 */
	public function registerMenuItems() {
		$subpages = $this->application->invokeComponents( 'registerMenuItems' );
		if ( function_exists( 'ini_set' ) ) {
			ini_set( 'display_errors', 'Off' );
			ini_set( 'error_reporting', E_ALL );
		} else if ( function_exists( 'error_reporting' ) ) {
			error_reporting( 0 );
		}
		if ( ! $subpages ) {
			return false;
		}
		if ( count( $subpages ) == 0 ) {
			return false;
		}
		$menu_slug = DECOM_PLUGIN_FOLDER . '-index';

		if ( ! isset( $subpages['settings'] ) ) {
			if ( is_multisite() && is_network_admin() ) {
				add_menu_page( 'de:comments', 'de:comments', 'manage_options', $menu_slug, array(
					'DECOM_Component_WooActivation',
					'renderWooActivationIndex'
				) );
			} else {
				$page_hook_suffix = add_submenu_page( 'edit-comments.php', 'de:comments', 'de:comments', 'manage_options', DECOM_PLUGIN_FOLDER . '-index', array(
					'DECOM_Component_WooActivation',
					'renderWooActivationIndex'
				) );
			}
		} else {

			$page_hook_suffix = add_submenu_page( 'edit-comments.php', 'de:comments', 'de:comments', 'manage_options', $menu_slug, array(
				'DECOM_Component_Settings',
				'renderSettings'
			) );

			add_action( 'admin_print_styles-' . $page_hook_suffix, array(
				'DECOM_Component_Settings',
				'registerAssets'
			) );

			add_action( 'admin_print_scripts-' . $page_hook_suffix, array(
				'DECOM_Component_Settings',
				'registerAssets'
			) );

			$page_hook_suffix = add_submenu_page( 'edit-comments.php', apply_filters( 'decomments_sttings_menu_name', __( 'Discussion settings', DECOM_LANG_DOMAIN ) ), __( 'Discussion settings' ), 'manage_options', 'options-discussion.php', array() );
		}
	}

	/**
	 * Add meta boxes to admin panel
	 *
	 * @return bool
	 */
	public function registerMetaBoxes() {

		$this->application->invokeComponents( 'registerMetaBoxes' );
	}

	public function onPostSave( $post_id ) {

		$this->application->invokeLibraries( 'onPostSave', $post_id );
		$this->application->invokeComponents( 'onPostSave', $post_id );
	}

	public function onDraftSave( $post_id ) {

		$this->application->invokeLibraries( 'onDraftSave', $post_id );
		$this->application->invokeComponents( 'onDraftSave', $post_id );
	}

	public function onPluginsActivation( $plugin ) {

		$this->application->invokeLibraries( 'onPluginsActivation', $plugin );
		$this->application->invokeComponents( 'onPluginsActivation', $plugin );
	}

	public function onUpdateCategory( $category ) {

		$this->application->invokeComponents( 'onUpdateCategory', $category );
	}

	public function onRewriteRules( $rules ) {

		$this->application->invokeComponents( 'onRewriteRules', $rules );
	}

	public function onPostCommentsForm( $fields ) {

		return $this->application->invokeComponents( 'onPostCommentsForm', $fields );
	}

	public function onCommentsTemplate( $template ) {

		$templates = $this->application->invokeComponents( 'onCommentsTemplate', $template );

		return array_pop( $templates );
	}

	public function onPreprocessComment( $commentdata ) {

		$rCommentData = $this->application->invokeComponents( 'onPreprocessComment', $commentdata );

		return array_pop( $rCommentData );
	}

	public function onSetCommentCookies( $commentdata ) {

		$rCommentData = $this->application->invokeComponents( 'onSetCommentCookies', $commentdata );

		return array_pop( $rCommentData );
	}


	public function replaceRecentCommentsWidget() {

		$class_name = $this->application->invokeComponents( 'replaceRecentCommentsWidget' );
		register_widget( $class_name );
	}

	public function PrintJsLanguage() {

		$class_name = $this->application->invokeComponents( 'PrintJsLanguage' );
	}

	public function onDeletedComment( $comment_id ) {

		$this->application->invokeComponents( 'onDeletedComment', $comment_id );
	}

	public function onWPMakeClickable( $text ) {

		$array = $this->application->invokeComponents( 'onWPMakeClickable', $text );
		if ( is_array( $array ) && count( $array ) > 0 ) {
			$key = array_keys( $array );

			return $array[ $key[0] ];
		}

		return $text;
	}

	public function onInsertMedia( $text ) {

		$array = $this->application->invokeComponents( 'onInsertMedia', $text );
		if ( is_array( $array ) && count( $array ) > 0 ) {
			$key = array_keys( $array );

			return $array[ $key[0] ];
		}

		return $text;
	}

	public function onWPDefaultAvatars( $default_avatars ) {

		$array = $this->application->invokeComponents( 'onWPDefaultAvatars', $default_avatars );
		if ( is_array( $array ) && count( $array ) > 0 ) {
			$key = array_keys( $array );

			return $array[ $key[0] ];
		}

		return $default_avatars;
	}

	public function onPluginsLoaded() {

		$this->application->invokeLibraries( 'onPluginsLoaded' );
		$this->application->invokeComponents( 'onPluginsLoaded' );
	}

	public function onWPCommentsNumber( $count, $post_id ) {

		$array = $this->application->invokeComponents( 'onWPCommentsNumber', $count, $post_id );
		if ( is_array( $array ) && count( $array ) > 0 ) {
			$key = array_keys( $array );

			return $array[ $key[0] ];
		}

		return $count;
	}

	public function onAdminPrintScripts() {

		$this->application->invokeComponents( 'onAdminPrintScripts' );
	}

	public function onAdminEnqueueScripts() {

		$this->application->invokeComponents( 'onAdminEnqueueScripts' );
	}

	public function onWpAjaxDecomEditNotifications() {

		$this->application->invokeComponents( 'onWpAjaxDecomEditNotifications' );
	}

	public function onWpAjaxDecomEditSettings() {

		$this->application->invokeComponents( 'onWpAjaxDecomEditSettings' );
	}

	public function onWpAjaxDecomBadges() {

		$this->application->invokeComponents( 'onWpAjaxDecomBadges' );
	}

	public function onWpAjaxDecomComments() {

		$this->application->invokeComponents( 'onWpAjaxDecomComments' );
	}

	public function onHttpRequestArgs( $r ) {

		$r['timeout'] = 120;

		return $r;
	}
}