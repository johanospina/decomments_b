<?php

class DECOM_Loader_MVC extends DECOM_Loader {

	/**
	 * Include plugin router
	 */
	public static function includeRouter() {

		self::includeFile( dirname( __FILE__ ) . '/router.php' );
	}

	/**
	 * Return instance of controller by name
	 *
	 * @param string short controller name (without 'controller-' prefix)
	 *
	 * @return object Controller instance
	 */
	public static function getController( $short_file_name ) {

		$controller_class_name = 'DECOM_Controller_' . self::getClassNameFromFileName( $short_file_name );

		if ( !$controller_class_name ) {
			return false;
		}

		$controller_file_name = 'decom-controller-' . $short_file_name;
		$controller_path      = 'controllers/' . $controller_file_name . '.php';

		self::includePluginFile( 'core/controller.php' );
		self::includePluginFile( $controller_path );

		if ( !class_exists( $controller_class_name ) ) {
			return false;
		}

		return new $controller_class_name();
	}

	/**
	 * Return instance of component controller by name
	 *
	 * @param string component name
	 * @param string short controller name (without 'controller-' prefix)
	 *
	 * @return object Controller instance
	 */
	public static function getComponentController( $component_name, $short_file_name ) {

		$controller_class_name = 'DECOM_Controller_' . self::getClassNameFromFileName( $short_file_name );

		if ( !$controller_class_name ) {
			return false;
		}

		$controller_file_name = 'controller-' . $short_file_name;
		$controller_path      = DECOM_COMPONENTS_PATH . '/' . $component_name . '/controllers/' . $controller_file_name . '.php';

		self::includeController();
		self::includeFile( $controller_path );

		if ( !class_exists( $controller_class_name ) ) {
			return false;
		}

		return new $controller_class_name();
	}

	/**
	 * Return instance of component views by name
	 *
	 * @param string component name
	 * @param string short views name (without 'views-' prefix)
	 *
	 * @return object View instance
	 */
	public static function getComponentView( $component_name, $short_file_name ) {

		$view_class_name = 'DECOM_View_' . self::getClassNameFromFileName( $short_file_name );


		if ( !$view_class_name ) {
			return false;
		}

		$view_file_name = 'view-' . $short_file_name;

		$view_path = DECOM_COMPONENTS_PATH . '/' . $component_name . '/views/' . $view_file_name . '.php';


		self::includeView();
		self::includeFile( $view_path );

		if ( !class_exists( $view_class_name ) ) {
			return false;
		}

		return new $view_class_name();
	}

	/**
	 * Include WPRC model by name
	 *
	 * @param string model name
	 */
	public static function getModel( $short_file_name ) {

		$model_class_name = 'DECOM_Model_' . self::getClassNameFromFileName( $short_file_name );

		if ( !$model_class_name ) {
			return false;
		}

		$model_file_name = 'model-' . $short_file_name;
		$model_path      = DECOM_MODELS_PATH . '/' . $model_file_name . '.php';

		self::includeModel();
		self::includeFile( $model_path );

		if ( !class_exists( $model_class_name ) ) {
			return false;
		}

		return new $model_class_name();
	}

	/**
	 * Get instance of component model by name
	 *
	 * @param string component name
	 * @param string model name
	 */
	public static function getComponentModel( $component_name, $short_file_name ) {

		$model_class_name = 'DECOM_Model_' . self::getClassNameFromFileName( $short_file_name );

		if ( !$model_class_name ) {
			return false;
		}

		$model_file_name = 'model-' . $short_file_name;
		$model_path      = DECOM_COMPONENTS_PATH . '/' . $component_name . '/models/' . $model_file_name . '.php';

		self::includeModel();
		self::includeFile( $model_path );

		if ( !class_exists( $model_class_name ) ) {
			return false;
		}

		return new $model_class_name();
	}

	/**
	 * Get instance of component class by name
	 *
	 * @param string component name
	 * @param string class name
	 */
	public static function getComponentClass( $component_name, $short_file_name ) {

		$class_name = 'DECOM_' . self::getClassNameFromFileName( $short_file_name );


		if ( !$class_name ) {
			return false;
		}

		$class_path = DECOM_COMPONENTS_PATH . '/' . $component_name . '/classes/' . $short_file_name . '.php';


		self::includeFile( $class_path );


		if ( !class_exists( $class_name ) ) {
			return false;
		}

		return new $class_name();
	}

	/**
	 * Include component class by name
	 *
	 * @param string component name
	 * @param string class name
	 */
	public static function includeComponentClass( $component_name, $short_file_name ) {

		$class_name = 'DECOM_' . self::getClassNameFromFileName( $short_file_name );

		if ( !$class_name ) {
			return false;
		}

		$class_path = DECOM_COMPONENTS_PATH . '/' . $component_name . '/classes/' . $short_file_name . '.php';

		self::includeFile( $class_path );

		if ( !class_exists( $class_name ) ) {
			return false;
		}

		return $class_name;
	}

	/**
	 * Get instance of library model by name
	 *
	 * @param string library name
	 * @param string model name
	 */
	public static function getLibraryModel( $library_name, $short_file_name ) {

		$model_class_name = 'DECOM_Model_' . self::getClassNameFromFileName( $short_file_name );

		if ( !$model_class_name ) {
			return false;
		}

		$model_file_name = 'model-' . $short_file_name;
		$model_path      = DECOM_LIBRARIES_PATH . '/' . $library_name . '/models/' . $model_file_name . '.php';

		self::includeModel();
		self::includeFile( $model_path );

		if ( !class_exists( $model_class_name ) ) {
			return false;
		}

		return new $model_class_name();
	}

	public static function getLibraryModelInstance( $library_name, $short_file_name ) {

		$model_class_name = 'DECOM_Model_' . self::getClassNameFromFileName( $short_file_name );

		if ( !$model_class_name ) {
			return false;
		}

		if ( !class_exists( $model_class_name ) ) {
			return false;
		}

		return new $model_class_name();
	}

	/**
	 * Return instance of library controller by name
	 *
	 * @param string library name
	 * @param string short controller name (without 'controller-' prefix)
	 *
	 * @return object Controller instance
	 */
	public static function getLibraryController( $library_name, $short_file_name ) {

		$controller_class_name = 'DECOM_Controller_' . self::getClassNameFromFileName( $short_file_name );

		if ( !$controller_class_name ) {
			return false;
		}

		$controller_file_name = 'controller-' . $short_file_name;
		$controller_path      = DECOM_LIBRARIES_PATH . '/' . $library_name . '/controllers/' . $controller_file_name . '.php';

		self::includeController();
		self::includeFile( $controller_path );

		if ( !class_exists( $controller_class_name ) ) {
			return false;
		}

		return new $controller_class_name();
	}

	/**
	 * Return instance of library views by name
	 *
	 * @param string library name
	 * @param string short views name (without 'views-' prefix)
	 *
	 * @return object View instance
	 */
	public static function getLibraryView( $library_name, $short_file_name ) {

		$view_class_name = 'DECOM_View_' . self::getClassNameFromFileName( $short_file_name );

		if ( !$view_class_name ) {
			return false;
		}

		$view_file_name = 'views-' . $short_file_name;
		$view_path      = DECOM_LIBRARIES_PATH . '/' . $library_name . '/views/' . $view_file_name . '.php';
		self::includeView();
		self::includeFile( $view_path );

		if ( !class_exists( $view_class_name ) ) {
			return false;
		}

		return new $view_class_name();
	}

	/**
	 * Return instance of library class by name
	 *
	 * @param string library name
	 * @param string short class name (without 'views-' prefix)
	 *
	 * @return object View instance
	 */
	public static function getLibraryClass( $library_name, $short_file_name ) {

		$class_name = 'DECOM_' . self::getClassNameFromFileName( $short_file_name );

		if ( !$class_name ) {
			return false;
		}

		$class_path = DECOM_LIBRARIES_PATH . '/' . $library_name . '/classes/' . $short_file_name . '.php';

		self::includeFile( $class_path );

		if ( !class_exists( $class_name ) ) {
			return false;
		}

		return new $class_name();
	}

	public static function includeView() {

		self::includeFile( dirname( __FILE__ ) . '/view.php' );
	}

	public static function includeController() {

		self::includeFile( dirname( __FILE__ ) . '/controller.php' );
	}

	public static function includeModel() {

		self::includeFile( dirname( __FILE__ ) . '/model.php' );
	}

	public static function getPathTheme() {
		$decom_template_path    = DECOM_TEMPLATE_PATH_DEFAULT;
		$model_options          = DECOM_Loader_MVC::getLibraryModel( 'options', 'options' );
		$custom_folder_template = $model_options->getOption( 'custom_folder_template' );

		if ( is_dir( DECOM_ALTERNATIVE_TEMPLATE_PATH ) ) {
			$decom_template_path = DECOM_ALTERNATIVE_TEMPLATE_PATH;
		} elseif ( $custom_folder_template == 'theme1' ) {
			$decom_template_path = DECOM_TEMPLATE_PATH_THEME1;
		} elseif ( $custom_folder_template == 'theme2' ) {
			$decom_template_path = DECOM_TEMPLATE_PATH_THEME2;
		}

		return $decom_template_path;
	}

}