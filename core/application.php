<?php

class DECOM_Application {
	protected static $components = array();
	protected static $libraries = array();

	protected static $_instance;

	/**
	 * Associative array of plugin configuration
	 *
	 * @var array|null
	 */
	private $configuration = array();

	private function __clone() {

	}

	public static function getInstance( DECOM_ApplicationConfiguration $app_config ) {
		if ( null === self::$_instance ) {
			self::$_instance = new self( $app_config );
		}

		return self::$_instance;
	}

	/**
	 * Class constructor
	 *
	 * @param ApplicationConfiguration $app_config
	 */
	private function __construct( DECOM_ApplicationConfiguration $app_config ) {
		$this->configuration = $app_config->getConfiguration();

		self::$components = $this->configuration['components'];
		self::$libraries  = $this->configuration['libraries'];
	}

	/**
	 * Get option from application array of configuration array
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	public function getOption( $key ) {
		if ( ! array_key_exists( 'application', $this->configuration ) ) {
			return false;
		}

		if ( ! array_key_exists( $key, $this->configuration['application'] ) ) {
//            DECOM_Debug::errorLog('Warning: There is no "'.$key.'" application option found', true, true);
			return false;
		}

		return $this->configuration['application'][ $key ];
	}

	/**
	 * Check required components
	 *
	 * @return bool
	 */
	public function checkRequiredComponents() {
		if ( count( self::$components ) == 0 ) {
			return false;
		}

		$components               = array_keys( self::$components );
		$not_installed_components = array();

		foreach ( self::$components AS $component_name => $component ) {
			if ( array_key_exists( 'required_components', $component ) ) {
				for ( $i = 0; $i < count( $component['required_components'] ); $i ++ ) {
					if ( ! in_array( $component['required_components'][ $i ], $components ) ) {
						$not_installed_components[] = $component['required_components'][ $i ];
					}
				}
			}

			if ( count( $not_installed_components ) > 0 ) {
				$warning = 'Fatal error: Component "' . $component_name . '" requires following components: ' . implode( ', ', $not_installed_components );
//                DECOM_Debug::errorLog($warning, true, true);
			}
		}


	}

	/**
	 * Include application libraries
	 *
	 * @return bool
	 */
	public function includeLibraries() {
		DECOM_Loader::includeLibrary();

		if ( count( self::$libraries ) == 0 ) {
			return false;
		}

		foreach ( self::$libraries AS $library_name => $library ) {
			$library_file       = $library['path'] . '/library-' . $library_name . '.php';
			$configuration_file = $library['path'] . '/configuration-' . $library_name . '.php';
			$loader_file        = $library['path'] . '/loader-' . $library_name . '.php';

			if ( ! file_exists( $library_file ) ) {
				$warning = 'Fatal error: File "' . $library_file . '" is not exists';
//                DECOM_Debug::errorLog($warning);
//
//                DECOM_Debug::outputErrorLog();
				exit;
			}

			// include configuration of library
			if ( file_exists( $configuration_file ) ) {
				include_once( $configuration_file );
			}

			// include loader of library
			if ( file_exists( $loader_file ) ) {
				include_once( $loader_file );
			}

			include_once( $library_file );
		}
	}

	/**
	 * Include application components
	 *
	 * @return bool
	 */
	public function includeComponents() {
		DECOM_Loader::includeComponent();

		if ( count( self::$components ) == 0 ) {
			return false;
		}

		$block_other_components = false;

		foreach ( self::$components AS $component_name => $component ) {
			$component_file     = $component['path'] . '/component-' . $component_name . '.php';
			$configuration_file = $component['path'] . '/configuration-' . $component_name . '.php';
			$loader_file        = $component['path'] . '/loader-' . $component_name . '.php';
			$filter_file        = $component['path'] . '/filter-' . $component_name . '.php';

			$component_class        = $component['class_name'];
			$component_filter_class = $component_class . '_Filter';

			if ( ! file_exists( $component_file ) ) {
				$warning = 'Fatal error: File "' . $component_file . '" is not exists';
//                DECOM_Debug::errorLog($warning);
//
//                DECOM_Debug::outputErrorLog();
				exit;
			}

			if ( file_exists( $filter_file ) ) {
				include_once( $filter_file );
				//$block_other_components = $component_filter_class::blockOtherComponents();
				$block_other_components = call_user_func( array( $component_filter_class, "blockOtherComponents" ) );
			}

			// include configuration of component
			if ( file_exists( $configuration_file ) ) {
				include_once( $configuration_file );
			}

			// include loader of component
			if ( file_exists( $loader_file ) ) {
				include_once( $loader_file );
			}

			// include component class
			include_once( $component_file );

			if ( $block_other_components === true ) {
				break;
			}
		}
	}

	/**
	 * Include application component
	 *
	 * @param $component_name
	 *
	 * @return bool
	 */
	public function includeComponent( $component_name ) {
		DECOM_Loader::includeComponent();

		if ( ! array_key_exists( $component_name, self::$components ) ) {
			return false;
		}

		$component = self::$components[ $component_name ];

		$component_file     = $component['path'] . '/component-' . $component_name . '.php';
		$configuration_file = $component['path'] . '/configuration-' . $component_name . '.php';
		$loader_file        = $component['path'] . '/loader-' . $component_name . '.php';
		$filter_file        = $component['path'] . '/filter-' . $component_name . '.php';

		$component_class        = $component['class_name'];
		$component_filter_class = $component_class . '_Filter';

		if ( ! file_exists( $component_file ) ) {
			$warning = 'Fatal error: File "' . $component_file . '" is not exists';
//            DECOM_Debug::errorLog($warning);
//
//            DECOM_Debug::outputErrorLog();
			exit;
		}

		if ( file_exists( $filter_file ) ) {
			include_once( $filter_file );
			//$block_other_components = call_user_func(array($component_filter_class, "blockOtherComponents"));
		}

		// include configuration of component
		if ( file_exists( $configuration_file ) ) {
			include_once( $configuration_file );
		}

		// include loader of component
		if ( file_exists( $loader_file ) ) {
			include_once( $loader_file );
		}

		// include component class
		include_once( $component_file );
	}

	/**
	 * Invokes method of components that implemented this methods
	 *
	 * @param $method_name
	 *
	 * @return array|bool
	 */
	public function invokeComponents( $method_name ) {
		if ( count( self::$components ) == 0 ) {
			return false;
		}

		$results                = array();
		$block_other_components = false;

		foreach ( self::$components AS $component_name => $component ) {
			$class_name = $component['class_name'];

			$filter_file            = $component['path'] . '/filter-' . $component_name . '.php';
			$component_filter_class = $class_name . '_Filter';

			if ( file_exists( $filter_file ) ) {
				include_once( $filter_file );
				$block_other_components = call_user_func( array( $component_filter_class, "blockOtherComponents" ) );
			}

			if ( ! class_exists( $class_name ) ) {
				continue;
			}

			if ( ! method_exists( $class_name, $method_name ) ) {
				continue;
			}

				$args                       = func_get_args();
				$args                       = array_slice( $args, 1 );
				$results[ $component_name ] = call_user_func_array( array( $class_name, $method_name ), $args );
			if ( $block_other_components === true ) {
				break;
			}
		}

		return $results;
	}

	public function invokeNetworkComponents( $method_name, $networkwide ) {
		DECOM_Loader::includeMultisite();

		if ( DECOM_Multisite::isMultisite() ) {
			// check if it is a network activation - if so, run the activation function for each blog id
			if ( $networkwide ) {
				$old_blog = DECOM_Multisite::getOldBlog();
				// Get all blog ids
				$blogids = DECOM_Multisite::getMultisiteBlogs();

				foreach ( $blogids as $blog_id ) {
					DECOM_Multisite::switchToBlog( $blog_id );
					$this->invokeComponents( $method_name );
				}


				DECOM_Multisite::switchToBlog( $old_blog );

				return;
			} else {
				$this->invokeComponents( $method_name );
			}
		} else {
			$this->invokeComponents( $method_name );
		}
	}

	public function invokeNetworkLibraries( $method_name, $networkwide ) {
		DECOM_Loader::includeMultisite();

		if ( DECOM_Multisite::isMultisite() ) {
			// check if it is a network activation - if so, run the activation function for each blog id
			if ( $networkwide ) {
				// Get all blog ids
				$old_blog = DECOM_Multisite::getOldBlog();
				$blogids  = DECOM_Multisite::getMultisiteBlogs();

				foreach ( $blogids as $blog_id ) {
					DECOM_Multisite::switchToBlog( $blog_id );
					$this->invokeLibraries( $method_name );
				}


				DECOM_Multisite::switchToBlog( $old_blog );

				return;
			} else {
				$this->invokeLibraries( $method_name );
			}
		} else {
			$this->invokeLibraries( $method_name );
		}
	}

	public function invokeBlogComponents( $method_name, $blog_id ) {
		DECOM_Loader::includeMultisite();

		if ( DECOM_Multisite::isActiveForNetwork() ) {
			$old_blog = DECOM_Multisite::getOldBlog();
			DECOM_Multisite::switchToBlog( $blog_id );

			$this->invokeComponents( $method_name );

			DECOM_Multisite::switchToBlog( $old_blog );
		}

	}

	public function invokeBlogLibraries( $method_name, $blog_id ) {
		DECOM_Loader::includeMultisite();

		if ( DECOM_Multisite::isActiveForNetwork() ) {
			$old_blog = DECOM_Multisite::getOldBlog();
			DECOM_Multisite::switchToBlog( $blog_id );

			$this->invokeLibraries( $method_name );

			DECOM_Multisite::switchToBlog( $old_blog );
		}

	}

	/**
	 * Invokes method of components that implemented this methods
	 *
	 * @param $method_name
	 *
	 * @return array|bool
	 */
	public function invokeLibraries( $method_name, $params = '' ) {

		if ( count( self::$libraries ) == 0 ) {
			return false;
		}

		$results = array();

		foreach ( self::$libraries AS $library_name => $library ) {
			$class_name = $library['class_name'];
			if ( ! class_exists( $class_name ) ) {
//                DECOM_Debug::errorLog('Fatal error: Class "'.$class_name.'" is not defined', true);
				continue;
			}

			if ( ! method_exists( $class_name, $method_name ) ) {
				continue;
			}

			$results[ $library_name ] = call_user_func( array( $class_name, $method_name ), $params );
		}

		return $results;
	}

	public static function includeLibrary( $library_name, $mode = '' ) {
		if ( ! array_key_exists( $library_name, self::$libraries ) ) {
			return false;
		}

		if ( ! array_key_exists( 'class_name', self::$libraries[ $library_name ] ) ) {
			return false;
		}

		$library_class_name = self::$libraries[ $library_name ]['class_name'];

		call_user_func( array( $library_class_name, "onManualInclude" ), $mode );

	}

	public static function registerLibrary( $library_name, $mode = '' ) {
		if ( ! array_key_exists( $library_name, self::$libraries ) ) {
			return false;
		}

		if ( ! array_key_exists( 'class_name', self::$libraries[ $library_name ] ) ) {
			return false;
		}

		$library_class_name = self::$libraries[ $library_name ]['class_name'];

		call_user_func( array( $library_class_name, "registerAssets" ), $mode );

	}
}