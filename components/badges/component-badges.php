<?php

class DECOM_Component_Badges extends DECOM_Component {
	private static $component = 'comments';


	public static function onInit() {

	}

	public static function onActivation() {
		$model_badges = DECOM_Loader_MVC::getComponentModel( 'badges', 'badges' );
		$model_badges->prepareDB();
		$model_ucb = DECOM_Loader_MVC::getComponentModel( 'badges', 'user-comment-badges' );
		$model_ucb->prepareDB();
	}

	public static function onDeactivation() {

	}

	public static function registerMenuItems() {
		$menu_items = array(
			array(
				'title'                  => __( 'Badges', DECOM_LANG_DOMAIN ),
				'class'                  => __CLASS__,
				'method'                 => 'renderBadges',
				'menu_slug'              => 'badges-index',
				'register_assets_class'  => __CLASS__,
				'register_assets_method' => 'registerAssets'
			),
			array(
				'title'     => __( 'Add badge', DECOM_LANG_DOMAIN ),
				'class'     => __CLASS__,
				'method'    => 'renderAddBadgePage',
				'menu_slug' => 'badge-add',
				'type'      => 'hidden'
			),
			array(
				'title'     => __( 'Edit badge', DECOM_LANG_DOMAIN ),
				'class'     => __CLASS__,
				'method'    => 'renderEditBadgePage',
				'menu_slug' => 'badge-edit',
				'type'      => 'hidden'
			)
		);

		return $menu_items;
	}

	public static function includeAssets( $mode = '' ) {
		//wp_enqueue_script('jquery');
//		DECOM_Application::includeLibrary( 'jquery-easy-ui' );
		//DECOM_Application::includeLibrary('assets');
	}

	public static function registerAssets() {
		//wp_enqueue_script('jquery');
		wp_enqueue_media();

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );


		wp_enqueue_style( 'badges-style', DECOM_COMPONENTS_URL . '/badges/assets/css/decom-badges.css' );


		wp_enqueue_script( 'badges-angular', DECOM_PLUGIN_URL . '/admin/assets/js/angularjs/angular.min.js', array(), false, true );
		wp_enqueue_script( 'badges-table-script', DECOM_PLUGIN_URL . '/admin/assets/js/angularjs/modules/ng-table/ng-table.js', array(), false, true );

		wp_enqueue_script( 'badges-app-script', DECOM_COMPONENTS_URL . '/badges/assets/js/app.js', array(), false, true );

		wp_enqueue_script( 'badges-app-directive', DECOM_COMPONENTS_URL . '/badges/assets/js/directive.js', array(), false, true );

		wp_enqueue_script( 'badges-script', DECOM_COMPONENTS_URL . '/badges/assets/js/decom-badges.js', array(), false, true );

		$translation_array = array(
			'name'           => __( 'Name of the Badge', DECOM_LANG_DOMAIN ),
			'count_like'     => __( 'Number of likes', DECOM_LANG_DOMAIN ),
			'count_dizlike'  => __( 'Number of dislikes', DECOM_LANG_DOMAIN ),
			'count_comments' => __( 'Number of comments', DECOM_LANG_DOMAIN ),
			'activity'       => __( 'Activity', DECOM_LANG_DOMAIN )
		);
		wp_localize_script( 'badges-datagrid-script', 'obj_translate', $translation_array );
		//DECOM_Application::registerLibrary('assets');
	}

	public static function renderBadges() {
		self::includeAssets();

		$model_options = DECOM_Loader_MVC::getComponentModel( 'badges', 'badges' );
		$badges        = $model_options->getBadges();

		$view_badges = DECOM_Loader_MVC::getComponentView( 'badges', 'badges' );
		$view_badges->renderBadgesPage( $badges );

	}

	public static function renderAddBadgePage() {
		/*$view_badges = DECOM_Loader_MVC::getComponentView( 'badges', 'badges' );
		$view_badges->renderAddBadgePage();*/
	}

	public static function renderEditBadgePage() {
		$view_badges = DECOM_Loader_MVC::getComponentView( 'badges', 'badges' );
		$view_badges->renderEditBadgePage();
	}

	public static function onWpAjaxDecomBadges() {
		$controller = DECOM_Loader_MVC::getComponentController( 'badges', 'badges' );
		$action     = $_POST['f'];
		$post       = $_POST;
		switch ( $action ) {
			case 'get_badges':
				$controller->getBadges( $post );
				break;
			case 'add_badges':
				$controller->addBadge( $post );
				break;
			case 'delete_badges':
				$controller->deleteBadges( $post );
				break;
			default:
				exit;
		}
		exit;
	}
}