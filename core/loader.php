<?php
defined( 'ABSPATH' ) or die( 'Cheatin\' uh?' );

/**
 * DECOM_Loader
 *
 * This class is responsible for including of all files and getting of instances of all objects
 *
 */
class DECOM_Loader {

	/**
	 * Include file
	 *
	 * @param string file name
	 */
	public static function includeFile( $file_name ) {

		if ( ! file_exists( $file_name ) ) {
			printf( __( 'File "%s" doesn\'t exist!', DECOM_LANG_DOMAIN ), $file_name );

			return false;
		}
		require_once DECOM_COMPONENTS_PATH . '/comments/decom-comments.php';

		include_once( $file_name );
	}

	/**
	 * Include file of WP Repository Client plugin
	 */
	protected static function includePluginFile( $file_name ) {

		$file_name = DECOM_PLUGIN_PATH . '/' . $file_name;
		self::includeFile( $file_name );
	}

	public static function includeMultisite() {

		self::includeWPPlugin();
		self::includeFile( DECOM_CORE_PATH . '/multisite.php' );
	}

	/**
	 * Boot plugin framework
	 *
	 * @return HooksHandler
	 */
	public static function getHooksHandler() {

		$app_config = self::getApplicationConfiguration();

		include_once( DECOM_CORE_PATH . '/application.php' );
		$decom_application_loader = DECOM_Application::getInstance( $app_config );
		$decom_application_loader->includeLibraries();
		$decom_application_loader->includeComponents();

		include_once( DECOM_CORE_PATH . '/hooks-handler.php' );

		return new DECOM_HooksHandler( $decom_application_loader );
	}

	public static function getApplicationConfiguration() {

		self::includeFile( DECOM_CORE_PATH . '/application-configuration.php' );

		return new DECOM_ApplicationConfiguration();
	}

	public static function getApplication() {

		$app_config = self::getApplicationConfiguration();

		include_once( DECOM_CORE_PATH . '/application.php' );

		return DECOM_Application::getInstance( $app_config );
	}

	public static function includeComponent() {

		include_once( DECOM_PLUGIN_PATH . '/core/component.php' );
	}

	public static function includeLibrary() {

		include_once( DECOM_PLUGIN_PATH . '/core/library.php' );
	}

	/**
	 * Include page by name
	 *
	 * @param string page name
	 * @param string page display mode
	 */
	public static function includePage( $page_name ) {

		$page_path = DECOM_PAGES_PATH . '/' . $page_name . '.php';

		self::includeFile( $page_path );
	}

	/**
	 * Include widget by name
	 *
	 * @param string widget name
	 * @param string widget display mode
	 */
	public static function includeWidget( $widget_name ) {

//		$page_path = DECOM_WIDGET_DIR . '/decom-widget-' . $widget_name . '.php';

//		self::includeFile( $page_path );
	}

	/**
	 * Include template by name
	 *
	 * @param string template name
	 */
	public static function includeWidgetTemplate( $widget_template_name, $widget_template_dir = '' ) {

//		$widget_template_dir_path = ( $widget_template_dir <> '' ) ? $widget_template_dir . '/' : '';
//		$widget_template_path     = DECOM_WIDGET_TEMPLATES_DIR . '/' . $widget_template_dir_path . $widget_template_name . '.tpl.php';

//		self::includeFile( $widget_template_path );
	}

	/**
	 * Include admin panel header
	 *
	 * @param bool disable admin panel menus
	 */
	public static function includeAdminTop( $disable_menus = false ) {

		$disable_menus_css = '<style type="text/css">   #adminmenuwrap, #screen-meta-links {display:none;}   </style>';

		self::includeFile( ABSPATH . 'wp-admin/admin-header.php' );

		if ( $disable_menus ) {
			echo $disable_menus_css;
		}
	}

	/**
	 * Form class name from the file name
	 *
	 * e.g. 'repository-reporter' => 'RepositoryReporter'
	 *
	 * @access private
	 *
	 * @param string file name
	 *
	 * @return string class name
	 */
	protected static function getClassNameFromFileName( $file_name ) {

		if ( $file_name == '' ) {
			return false;
		}

		$name_parts = explode( '-', $file_name );

		for ( $i = 0; $i < count( $name_parts ); $i ++ ) {
			$name_parts[ $i ] = ucfirst( $name_parts[ $i ] );
		}

		return implode( '', $name_parts );
	}


	/**
	 * Include class-wp-list-table file
	 */
	public static function includeWPListTable() {

		if ( ! class_exists( 'WP_List_Table' ) ) {
			$file_name = ABSPATH . '/wp-admin/includes/class-wp-list-table.php';
			self::includeFile( $file_name );
		}
	}

	/**
	 * Include WP Upgrade file to use dbDelta()
	 */
	public static function includeWPUpgrade() {

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	}

	public static function includeWPPlugin() {

		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}

	/**
	 * Include WPRC list table by name
	 *
	 * @param string table list name
	 */
	public static function includeListTable( $table_name ) {

		self::includeFile( DECOM_TABLES_DIR . '/class-wp-' . $table_name . '-list-table.php' );
	}

	public static function getListTable( $table_name ) {

		$class_name = self::getClassNameFromFileName( $table_name );
		$class_name = 'DECOM_' . $class_name . '_List_Table';

		$table_name = 'decom-' . $table_name . '-list-table';

		self::includeWPListTable();
		self::includeListTable( $table_name );

		return new $class_name();
	}


	public static function includeSiteEnvironment() {

		self::includeFile( DECOM_CLASSES_PATH . '/decom-site-environment.php' );
	}

	public static function includeClass( $file_name ) {

		self::includeFile( DECOM_CLASSES_PATH . '/' . $file_name );
	}

	/**
	 * Include Main class
	 *
	 */
	public static function includeMainClass() {

		self::includeFile( DECOM_CLASSES_PATH . '/decom-main-class.php' );
	}

	public static function getFileParser( $short_file_name ) {

		$parser_class_name = 'DECOM_FileParser_' . self::getClassNameFromFileName( $short_file_name );

		if ( ! $parser_class_name ) {
			return false;
		}

		$parser_file_name = 'file-parser-' . $short_file_name;
		$parser_path      = DECOM_CLASSES_PATH . '/parsers/' . $parser_file_name . '.php';

		self::includePluginFile( 'classes/parsers/file-parser.php' );
		self::includeFile( $parser_path );

		if ( ! class_exists( $parser_class_name ) ) {
			return false;
		}

		return new $parser_class_name();
	}
}

?>