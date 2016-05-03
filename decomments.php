<?php
/**
 * Plugin Name: de:comments
 * Plugin URI: http://decomments.com
 * Description:  The most powerful plugin for WordPress comments
 * Version: 2.1
 * Author: deco.agency
 * Author URI: http://deco.agency
 * Licence: https://decomments.com/license/
 * Text Domain: decomments
 * Domain Path: languages
 * Copyright 2014-2016 de:comments
 */

defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

define( 'DECOM_FILE', __FILE__ );
define( 'DECOM_PREFIX', 'decom_' );
define( 'DECOM_SITE_URL', 'https://decomments.com' );
define( 'DECOM_PLUGIN_PATH', dirname( __FILE__ ) );
define( 'DECOM_PLUGIN_FOLDER', basename( DECOM_PLUGIN_PATH ) );
define( 'DECOM_LANG_DOMAIN', 'decomments' );
define( 'DECOM_PLUGIN_NAME', DECOM_PLUGIN_FOLDER . '/' . basename( __FILE__ ) );
define( 'DECOM_PLUGIN_URL', plugins_url( '', __FILE__ ) );
define( 'DECOM_ASSETS_URL', DECOM_PLUGIN_URL . '/assets' );
define( 'DECOM_LIBRARIES_URL', DECOM_PLUGIN_URL . '/libraries' );
define( 'DECOM_COMPONENTS_URL', DECOM_PLUGIN_URL . '/components' );
define( 'DECOM_COMPONENTS_IMG', DECOM_COMPONENTS_URL . '/comments/assets/images' );
define( 'DECOM_CORE_PATH', DECOM_PLUGIN_PATH . '/core' );
define( 'DECOM_COMPONENTS_PATH', DECOM_PLUGIN_PATH . '/components' );
define( 'DECOM_LIBRARIES_PATH', DECOM_PLUGIN_PATH . '/libraries' );
define( 'DECOM_ASSETS_PATH', DECOM_PLUGIN_PATH . '/assets' );
define( 'DECOM_IMAGES_URL', DECOM_PLUGIN_URL . '/assets/images' );
define( 'DECOM_VIEWS_PATH', DECOM_PLUGIN_PATH . '/views' );
define( 'DECOM_TEMPLATES_PATH', DECOM_PLUGIN_PATH . '/views/templates' );
define( 'DECOM_CLASSES_PATH', DECOM_PLUGIN_PATH . '/classes' );
define( 'DECOM_TABLES_PATH', DECOM_PLUGIN_PATH . '/tables' );
define( 'DECOM_MODELS_PATH', DECOM_PLUGIN_PATH . '/models' );
define( 'DECOM_TEMPLATE_PATH', DECOM_PLUGIN_PATH . '/templates' );
define( 'DECOM_TEMPLATE_PATH_DEFAULT', DECOM_PLUGIN_PATH . '/templates/decomments/' );
define( 'DECOM_TEMPLATE_URL_DEFAULT', DECOM_PLUGIN_URL . '/templates/decomments/' );
define( 'DECOM_ALTERNATIVE_TEMPLATE_PATH', get_template_directory() . '/decomments/' );
define( 'DECOM_ALTERNATIVE_TEMPLATE_URL', get_template_directory_uri() . '/decomments/' );
require_once DECOM_PLUGIN_PATH . '/core/loader.php';
require_once DECOM_LIBRARIES_PATH . '/ajax/class-decom-ajax.php';
require_once DECOM_PLUGIN_PATH . '/admin/modules/decom-updater/init.php';
require_once DECOM_PLUGIN_PATH . '/admin/init.php';
$decom_hooks_handler   = DECOM_Loader::getHooksHandler();
$decom_error_reporting = error_reporting( 0 );
/**
 * Initialize plugin environment
 */

add_action( 'init', array( $decom_hooks_handler, 'onInit' ) );

/**
 * Include scripts on admin panel initialization
 */
add_action( 'admin_init', array( $decom_hooks_handler, 'onAdminInit' ) );

/**
 * Add plugin menu items
 */

add_action( 'admin_menu', array( $decom_hooks_handler, 'registerMenuItems' ) );
if ( is_multisite() ) {
	add_action( 'network_admin_menu', array( $decom_hooks_handler, 'registerMenuItems' ) );
}

/**
 * On add/delete blog
 */
add_action( 'wpmu_new_blog', array( $decom_hooks_handler, 'onWpmuNewBlog' ), 10, 6 );

add_action( 'delete_blog', array( $decom_hooks_handler, 'onWpmuDeleteBlog' ), 5, 2 );

/**
 * Initialize plugin localization
 */
add_action( 'plugins_loaded', 'decom_load_textdomain' );
function decom_load_textdomain() {
	load_plugin_textdomain( 'decomments', false, DECOM_PLUGIN_FOLDER . '/languages' );
}

/**
 *  Disable function comment preprocess in other plugins and theme
 */
add_action( 'init', 'decom_init_site', 999 );
function decom_init_site() {
//	remove_all_filters( 'comments_template', 10);
	remove_all_filters( 'comment_post_redirect', 10 );
	remove_all_filters( 'preprocess_comment', 10 );
	remove_all_actions( 'comment_post', 10 );
	remove_all_actions( 'wp_insert_comment', 10 );
}

/**
 * On Plugin activation
 */
register_activation_hook( __FILE__, array( $decom_hooks_handler, 'onActivation' ) );

/**
 * On Plugin deactivation
 */
register_deactivation_hook( __FILE__, array( $decom_hooks_handler, 'onDeactivation' ) );
add_filter( 'comments_template', array( $decom_hooks_handler, 'onCommentsTemplate' ), 999 );
add_filter( 'set_comment_cookies', array( $decom_hooks_handler, 'onSetCommentCookies' ) );
add_action( 'wp_print_scripts', array( $decom_hooks_handler, 'PrintJsLanguage' ) );
add_filter( 'decomments_comment_text', array( $decom_hooks_handler, 'onInsertMedia' ), 99 );
add_filter( 'avatar_defaults', array( $decom_hooks_handler, 'onWPDefaultAvatars' ) );
add_filter( 'get_comments_number', array( $decom_hooks_handler, 'onWPCommentsNumber' ), 99, 2 );
add_action( 'admin_enqueue_scripts', array( $decom_hooks_handler, 'onAdminEnqueueScripts' ) );
add_action( 'admin_print_scripts', array( $decom_hooks_handler, 'onAdminPrintScripts' ) );
add_action( 'wp_ajax_decom_edit_notifications', array( $decom_hooks_handler, 'onWpAjaxDecomEditNotifications' ) );
add_action( 'wp_ajax_decom_edit_settings', array( $decom_hooks_handler, 'onWpAjaxDecomEditSettings' ) );
add_action( 'wp_ajax_decom_badges', array( $decom_hooks_handler, 'onWpAjaxDecomBadges' ) );
add_action( 'wp_ajax_decom_comments', array( $decom_hooks_handler, 'onWpAjaxDecomComments' ) );
add_action( 'wp_ajax_nopriv_decom_comments', array( $decom_hooks_handler, 'onWpAjaxDecomComments' ) );
