<?php

class DECOM_Component_Comments extends DECOM_Component {

	private static $component = 'comments';

	public static function onInit() {
		if ( ! is_admin() ) {
			if ( defined( 'DECOM_COMPONENTS_URL' ) ) {
				global $wp_styles;
				wp_enqueue_script( 'jquery' );
				$model_options          = DECOM_Loader_MVC::getLibraryModel( 'options', 'options' );
				$deco_disable_css_style = $model_options->getOption( 'deco_disable_css_style' );
				$custom_folder_template = $model_options->getOption( 'custom_folder_template' );

				$template_url = DECOM_TEMPLATE_URL_DEFAULT;

				if ( is_dir( DECOM_ALTERNATIVE_TEMPLATE_PATH ) ) {
					$template_url           = DECOM_ALTERNATIVE_TEMPLATE_URL;
					$deco_disable_css_style = false;
				} elseif ( $custom_folder_template == 'theme1' ) {
					$template_url = DECOM_TEMPLATE_URL_THEME1;
				} elseif ( $custom_folder_template == 'theme2' ) {
					$template_url = DECOM_TEMPLATE_URL_THEME2;
				}
				if ( empty( $deco_disable_css_style ) ) {
					wp_enqueue_style( 'decomments', $template_url . 'assets/css/decom.css', array(), '1' );
					wp_enqueue_style( 'decomments-ie', $template_url . 'assets/css/decom-ie.css', array(), '1' );
					$wp_styles->add_data( 'decomments-ie', 'conditional', 'IE' );
				}
				if ( isset( $_GET['nomin'] ) ) {
					wp_enqueue_script( 'decomments', $template_url . 'assets/js/decom.js', array(), '1' );
				} else {
					wp_enqueue_script( 'decomments', $template_url . 'assets/js/decom.min.js', array(), '1' );
				}

				wp_localize_script( 'decomments', 'ajax_login_object', array(
					'ajaxurl'        => admin_url( 'admin-ajax.php' ),
					'redirecturl'    => $_SERVER['REQUEST_URI'],
					'loadingmessage' => __( 'Verifying data...', DECOM_LANG_DOMAIN )
				) );

				add_action( 'wp_ajax_nopriv_ajaxlogin', array( __CLASS__, 'ajax_login' ) );

				if ( ! is_user_logged_in() ) {
					add_action( 'init', 'ajax_login_init' );
				}
			}

			add_action( 'wp_insert_comment', array( __CLASS__, 'insertImageCommentIn' ), 999, 2 );
			add_action( 'pre_comment_on_post', array( __CLASS__, 'pre_comment_on_post' ), 99, 2 );
			add_action( 'pre_comment_on_post', array( __CLASS__, 'check_block_user' ), 99, 2 );
			add_filter( 'preprocess_comment', array( __CLASS__, 'set_user_cookie' ) );
			add_filter( 'preprocess_comment', array( __CLASS__, 'onPreprocessComment' ) );
			add_filter( 'authenticate', array( __CLASS__, 'allow_email_login' ), 20, 3 );
			if ( ( isset( $_GET['action'] ) && $_GET['action'] != 'logout' ) || ( isset( $_POST['login_location'] ) && ! empty( $_POST['login_location'] ) ) || ( isset( $_GET['loggedout'] ) && $_GET['loggedout'] == 'true' ) ) {
				add_filter( 'login_redirect', array( __CLASS__, 'decom_login_redirect' ), 10, 3 );
			}
		}
		add_action( 'save_post', 'decom_user_posts', 10, 2 );
		function decom_user_posts( $post_id, $post ) {
			update_post_meta( $post->post_author, 'count_user_posts', count_user_posts( $post->post_author ) );
		}
	}

	public static function allow_email_login( $user, $username, $password ) {
		if ( is_email( $username ) ) {
			$user = get_user_by( 'email', $username );
			if ( $user ) {
				$username = $user->user_login;
			}
		}

		return wp_authenticate_username_password( null, $username, $password );
	}

	public static function onActivation() {
		$model_votes = DECOM_Loader_MVC::getComponentModel( 'comments', 'comments-votes' );
		$model_votes->prepareDB();
		$model_options = DECOM_Loader_MVC::getLibraryModel( 'options', 'options' );
		$avatar        = $model_options->getOption( 'avatar' );
		if ( $avatar != '' ) {
			$model_options->updateWPOption( 'avatar_default', $avatar );
		}
	}

	public static function onDeactivation() {
		$model_wp_options = DECOM_Loader_MVC::getLibraryModel( 'options', 'options' );
		$model_wp_options->updateWPOption( 'avatar_default', 'mystery' );
	}

	public static function includeAssets( $mode = '' ) {
		wp_enqueue_script( 'jquery' );
		DECOM_Application::includeLibrary( 'assets' );
	}

	public static function registerAssets() {

		DECOM_Application::registerLibrary( 'assets' );
	}

	public static function onCommentsTemplate( $template ) {

		$ajaxLoad = DECOM_Loader_MVC::getLibraryModel( 'options', 'options' )
		                            ->getOption( 'allow_lazy_load' );
		if ( $ajaxLoad ) {
			return DECOM_TEMPLATE_PATH_DEFAULT . 'before-comments.php';
		} else {
			return DECOM_TEMPLATE_PATH_DEFAULT . 'comments.php';
		}
	}

	public static function set_user_cookie( $commentdata ) {
		setcookie( 'decommentsa', $commentdata['comment_author'], time() + 60 * 60 * 24 * 366 );
		setcookie( 'decommentse', $commentdata['comment_author_email'], time() + 60 * 60 * 24 * 366 );
		setcookie( 'decommentsu', $commentdata['comment_author_url'], time() + 60 * 60 * 24 * 366 );

		if ( isset( $_COOKIE['PHPSESSID'] ) ) {
			setcookie( 'decommentsp', $_COOKIE['PHPSESSID'], time() + 60 * 60 * 24 * 366 );
		}

		return $commentdata;
	}

	public static function decom_login_redirect() {
		$location = $_SERVER['HTTP_REFERER'];
		wp_safe_redirect( $location );
		die();
	}

	public static function pre_comment_on_post( $post_id, $mas ) {
		$user_email     = $_POST['email'];
		$user_id        = $_POST['user_id'];
		$decom_settings = decom_get_options();

		// Not allow comment block user
		if ( decom_is_user_block( $user_id, $user_email ) ) {
			$res = array(
				'error'   => 'moderation',
				'message' => __( 'You are not allowed to post comments.', DECOM_LANG_DOMAIN ),
				'post'    => $_POST
			);
			die( json_encode( $res ) );
		}

		// Disable reply post
		if ( isset( $decom_settings['decom_disable_replies'] ) && ! empty( $decom_settings['decom_disable_replies'] ) && isset( $_POST['comment_parent'] ) && ! empty( $_POST['comment_parent'] ) ) {
//		if ( isset( $_POST['comment_parent'] ) && ! empty( $_POST['comment_parent'] ) ) {
			$res = array(
				'error'   => 'moderation',
				'message' => __( 'Replies disabled.', DECOM_LANG_DOMAIN ),
				'post'    => $_POST
			);
			die( json_encode( $res ) );
		}

	}

	public static function insertImageCommentIn( $comment_id, $comment_object ) {
		$post['decom_pictures']['name'] = $_POST['image_name'];
		$post['decom_pictures']['src']  = $_POST['image_base64'];
		if ( isset( $post['decom_pictures']['name'] ) ) {
			$file_base64     = $post['decom_pictures']['src'];
			$file_base64_arr = explode( ',', $file_base64 );
			$file_content    = base64_decode( end( $file_base64_arr ) );
			if ( ! empty( $file_content ) ) {
				$tmpfname = tempnam( sys_get_temp_dir(), "decom" );
				$handle   = fopen( $tmpfname, "w" );
				fwrite( $handle, $file_content );
				fclose( $handle );
				$finfo                                    = getimagesize( $tmpfname );
				$file_path_info                           = pathinfo( $post['decom_pictures']['name'] );
				$_FILES['decom_pictures'][0]['tmp_name']  = $tmpfname;
				$_FILES['decom_pictures'][0]['name']      = $file_path_info['basename'];
				$_FILES['decom_pictures'][0]['size']      = filesize( $tmpfname );
				$_FILES['decom_pictures'][0]['type']      = $finfo['mime'];
				$_FILES['decom_pictures'][0]['width']     = $finfo[0];
				$_FILES['decom_pictures'][0]['height']    = $finfo[1];
				$_FILES['decom_pictures'][0]['extension'] = $file_path_info['extension'];
				$_FILES['decom_pictures'][0]['filename']  = $file_path_info['filename'];
				DECOM_Loader_MVC::includeComponentClass( 'comments', 'files-upload' );
				$decom_upload = new DECOM_FilesUpload( $_FILES['decom_pictures'] );
				$validate     = $decom_upload->validateFiles();
				if ( ! $validate ) {
					$errors   = $decom_upload->getErrors();
					$errFiles = $errors['files'][ $_FILES['decom_pictures'][0]['name'] ];
					$code     = $param1 = $param2 = '';
					if ( isset( $errFiles['code'] ) ) {
						$code = $errFiles['code'];
					}
					if ( isset( $errFiles['param'] ) ) {
						$param1 = $errFiles['param'];
					}
					if ( isset( $errFiles['param2'] ) ) {
						$param2 = $errFiles['param2'];
					}
					$errMsg = DECOM_FilesUpload::getErrorByCode( $code, $param1, $param2 );
				}
				$attachIds = $decom_upload->uploadFiles( $comment_id );
				unlink( $tmpfname );
			}
		}
	}

	public static function onPreprocessComment( $data ) {
		$decom_settings = decom_get_options();

		if ( isset( $decom_settings['allow_html_in_comments'] ) && empty( $decom_settings['allow_html_in_comments'] ) ) {
			$data['comment_content'] = decom_xss_clean( $data['comment_content'] );
		}

		list( $tmp, $qute_content ) = explode( '<blockquote><div><cite>', $data['comment_content'] );
		list( $qute_content, $tmp ) = explode( '</cite></div></blockquote>', $qute_content );
		$tmp_qute_content = str_replace( ' ', '', $qute_content );
		if ( empty( $tmp_qute_content ) ) {
			$data['comment_content'] = str_replace( '<blockquote><div><cite>' . $qute_content . '</cite></div></blockquote>', '', $data['comment_content'] );
		}

		$data['comment_content'] = html_entity_decode( $data['comment_content'] );
		$data['comment_content'] = trim( stripslashes( $data['comment_content'] ) );


		if ( isset( $decom_settings['allow_html_in_comments'] ) && empty( $decom_settings['allow_html_in_comments'] ) ) {
			$data['comment_content'] = strip_tags( $data['comment_content'], '<p><div><blockquote><cite><br>' );
			global $allowedtags;
			$allowedtags['div'] = array();
		} else {
			global $allowedtags;
			$allowedtags['div']    = array(
				'class' => array(),
			);
			$allowedtags['span']   = array(
				'class' => array(),
			);
			$allowedtags['strong'] = array();
			$allowedtags['em']     = array();
			$allowedtags['p']      = array(
				'class' => array(),
			);
			$allowedtags['a']      = array(
				'href'  => array(),
				'title' => array()
			);
		}


		return $data;
	}

	public static function onSetCommentCookies( $newComment ) {

		if ( isset( $_FILES['decom_pictures'] ) ) {
			$decom_upload_class = DECOM_Loader_MVC::includeComponentClass( 'comments', 'files-upload' );
			$decom_upload       = new $decom_upload_class( $_FILES['decom_pictures'] );
			$decom_upload->uploadFiles( $newComment->comment_ID );
		}
		$model_badges = DECOM_Loader_MVC::getComponentModel( 'badges', 'badges' );
		$model_badges->addUserBadgesForComments( $newComment->user_id );
		$model_comments = DECOM_Loader_MVC::getComponentModel( 'comments', 'comments' );
		$email_list     = $model_comments->subscribeComments( $newComment );
		$model_options  = DECOM_Loader_MVC::getLibraryModel( 'options', 'options' );
		$options        = $model_options->getOptions();
		if ( array_key_exists( 'social_icon', $_POST ) ) {
			if ( ! empty( $_POST['social_icon'] ) ) {
				$model_comments->saveSocial( $newComment->comment_ID, $_POST['social_icon'] );
			}
		}
		header( "Content-type: text/html; charset=UTF-8" );
		$view_comments = DECOM_Loader_MVC::getComponentView( 'comments', 'comments' );
		$result        = $view_comments->renderCommentBegin( $newComment, array(
			'settings'   => $options,
			'user_voice' => array()
		), null );
		if ( $email_list['new_post_comment'] || count( $email_list['new_comment_to_comment'] ) > 0 ) {
			require_once DECOM_LIBRARIES_PATH . '/email/email.php';
			$email_class = new DECOM_Email();
			$email_class->notyfy_post_comments( $email_list, $newComment );
		}

		if ( $newComment->comment_approved ) {
			echo $result;
		} else {
			header( 'Content-Type: text/html' );
			echo json_encode( array(
				'error'   => 'moderation',
				'message' => __( 'Your comment is awaiting moderation.', DECOM_LANG_DOMAIN )
			) );
		}
		exit;
	}

	public static function printJsLanguage() {
		$model_options    = DECOM_Loader_MVC::getLibraryModel( 'options', 'options' );
		$main_color_theme = $model_options->getOption( 'decomments_main_color_theme' );
		$js               = '';
		if ( 'transparent' != $main_color_theme ) {
			$js .= "
			<style> .decomments-button, .decomments-head i:before { background: $main_color_theme !important; } .selectrics, .selectricsItems, .decomments-comment-section .decomments-footer-nav .decomments-buttons-moderate.active,
			.decomments-comment-section .decomments-footer-nav .moderate-action, .de-select.de-select-filter dt,.de-select.de-select-filter dd, .modal-wrap .flipper .login-form .btn{ border-color:$main_color_theme !important; } .decomments-comments-number, .modal-wrap .flipper .login-form .close-modal:hover{ color:$main_color_theme !important; } .decomments-gif:after{ border-left-color:$main_color_theme !important; } .loader-ball-scale > div, .modal-wrap .flipper .login-form .btn{background:$main_color_theme !important;} .decomments-file-uploaded .decomments-icon-insert-photo .svg *{stroke:$main_color_theme !important;}
#decomments-comment-section.decomments-comment-section.decomments-comment-section .decomments-pictures-holder .decomments-gif:hover .svg-icon path{fill:$main_color_theme !important; }
#decomments-comment-section.decomments-comment-section.decomments-comment-section .decomments-pictures-holder .decomments-gif:hover .svg-icon circle{stroke:$main_color_theme !important; }
			 </style>
			";
		}
		echo $js;
	}

	public static function renderCommentTemplate() {
		$model_post     = DECOM_Loader_MVC::getComponentModel( 'comments', 'post' );
		$post_id        = $model_post->getCurrentPostId();
		$model_user     = DECOM_Loader_MVC::getComponentModel( 'comments', 'user' );
		$user_id        = $model_user->getCurrentUserId();
		$model_comments = DECOM_Loader_MVC::getComponentModel( 'comments', 'comments' );
		$comments       = $model_comments->getPostComments( array( 'post_id' => $post_id ) );
		$view_comments  = DECOM_Loader_MVC::getComponentView( 'comments', 'comments' );
		$view_comments->renderComments( $user_id, $post_id, $comments );
	}

	public static function renderFormComments() {
		$view_settings = DECOM_Loader_MVC::getComponentView( self::$component, 'comments' );
		$view_settings->renderComments();
	}

	public static function onDeletedComment( $comment_id ) {
		$model_votes = DECOM_Loader_MVC::getComponentModel( self::$component, 'comments-votes' );
		$model_votes->cleanVotes( $comment_id );
	}

	public static function onWPMakeClickable( $text ) {
		$text          = make_clickable( $text );
		$model_options = DECOM_Loader_MVC::getLibraryModel( 'options', 'options' );
		$follow        = $model_options->getOption( 'follow' );
		$text          = preg_replace( '|<a href="(.*)" rel="nofollow">.*</a>|', '<a href="\1" rel="' . $follow . '" target="_blank">\1</a>', $text );

		return $text;
	}

	public static function onWPDefaultAvatars( $default_avatars ) {
		$model_options = DECOM_Loader_MVC::getLibraryModel( 'options', 'options' );
		$avatar        = $model_options->getOption( 'avatar' );
		if ( $avatar <> '' ) {
			$url                     = html_entity_decode( $avatar );
			$default_avatars[ $url ] = 'de:comments';
		}

		return $default_avatars;
	}

	public static function onWPCommentsNumber( $count, $post_id ) {
		$args  = array(
			'status'  => 'approve',
			'type'    => 'comment',
			'post_id' => $post_id,
			'count'   => true,
		);
		$count = get_comments( $args );

		return $count;
	}

	public static function ajax_login() {

		check_ajax_referer( 'ajax-login-nonce', 'security' );

		$info                  = array();
		$info['user_login']    = $_POST['username'];
		$info['user_password'] = $_POST['password'];
		$info['remember']      = true;

		$user_signon = wp_signon( $info, false );
		if ( is_wp_error( $user_signon ) ) {
			echo wp_send_json( array( 'loggedin' => false, 'message' => __( 'Wrong login or password!' ) ) );
		} else {
			echo wp_send_json( array( 'loggedin' => true, 'message' => __( 'Redirection...' ) ) );
		}

		die();
	}

	public static function convertEmbed( $content ) {

		return $content;
	}

	public static function onInsertMedia( $content ) {

		return $content;
	}

	public static function numericEntities( $string ) {

		$mapping_hex = array();
		$mapping_dec = array();

		foreach ( get_html_translation_table( HTML_ENTITIES, ENT_QUOTES ) as $char => $entity ) {
			$mapping_hex[ html_entity_decode( $entity, ENT_QUOTES, "UTF-8" ) ] = '&#x' . strtoupper( dechex( ord( html_entity_decode( $entity, ENT_QUOTES ) ) ) ) . ';';
			$mapping_dec[ html_entity_decode( $entity, ENT_QUOTES, "UTF-8" ) ] = '&#' . ord( html_entity_decode( $entity, ENT_QUOTES ) ) . ';';
		}
		$string = str_replace( array_values( $mapping_hex ), array_keys( $mapping_hex ), $string );
		$string = str_replace( array_values( $mapping_dec ), array_keys( $mapping_dec ), $string );

		return $string;
	}

	public static function onWpAjaxDecomComments() {

		$post   = $get = $_REQUEST;
		$action = $_REQUEST['f'];

		$comments_controller       = DECOM_Loader_MVC::getComponentController( 'comments', 'comments' );
		$comments_votes_controller = DECOM_Loader_MVC::getComponentController( 'comments', 'comments-votes' );

		switch ( $action ) {
			case 'voting':
				$comments_votes_controller->voting( $get, $post );
				break;
			case 'get_comments_by_paginate':
				$comments_controller->getCommentsByPaginate( $get, $post );
				break;
			case 'sort_comments':
				$comments_controller->sortComments( $get, $post );
				break;
			case 'delete_comment':
				$comments_controller->deleteComment( $get, $post );
				break;
			case 'moderate_comment_status':
				$comments_controller->moderateCommentStatus( $get, $post );
				break;
			case 'verify_email':
				$comments_controller->verifyEmail( $get, $post );
				break;
			case 'delete_picture':
				$comments_controller->deletePicture( $get, $post );
				break;
			case 'edit_comments':
				$comments_controller->editComments( $get, $post );
				break;
			case 'edit_comment_popup':
				$comments_controller->editCommentPopup( $get, $post );
				break;
			case 'share_popup':
				$comments_controller->sharePopup( $get, $post );
				break;
			case 'ajax_paginate':
				$comments_controller->ajaxPaginate( $get, $post );
				break;
			default:
				exit;
		}
		exit;
	}

	public static function filterTagsReplace( $text ) {

		$decom_settings = decom_get_options();
		if ( isset( $decom_settings['allow_html_in_comments'] ) && empty( $decom_settings['allow_html_in_comments'] ) ) {
			$text = decom_filter_tags_replace( $text );
		}

		return $text;
	}

}
