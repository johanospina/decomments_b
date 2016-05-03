<?php
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

/**
 * Verify can edit the comment user or guest
 *
 * @param string $comment
 * @param bool   $expired_time
 *
 * @return bool
 */
function decom_is_can_edit_comment( $comment = '', $expired_time = false ) {
	if ( is_user_logged_in() && get_current_user_id() == $comment->user_id && $expired_time ) {
		return true;
	}
	$comment_id       = $comment->comment_ID;
	$cookie_author    = isset( $_COOKIE['decommentsa'] ) ? $_COOKIE['decommentsa'] : '';
	$cookie_email     = isset( $_COOKIE['decommentse'] ) ? $_COOKIE['decommentse'] : '';
	$cookie_site      = isset( $_COOKIE['decommentsp'] ) ? $_COOKIE['decommentsp'] : '';
	$comment_author   = $comment->comment_author;
	$comment_email    = $comment->comment_author_email;
	$comment_site     = isset( $_COOKIE['PHPSESSID'] ) ? $_COOKIE['PHPSESSID'] : '';
	$md5_cookie_data  = md5( $comment_id . $cookie_author . $cookie_email . $cookie_site . $comment_id );
	$md5_comment_data = md5( $comment_id . $comment_author . $comment_email . $comment_site . $comment_id );
	if ( $md5_comment_data == $md5_cookie_data && $expired_time ) {
		return true;
	}

	return false;
}

/**
 * User is the author of the comment
 *
 * @param string $comment
 *
 * @return bool
 */
function deco_is_user_author_comment( $comment = '' ) {
	return ( is_user_logged_in() && is_object( $comment ) && get_current_user_id() == $comment->user_id ) ? true : false;
}

/**
 * @param $text
 *
 * @return mixed|string
 */
function decom_comments_formated_content( $content ) {
	$decom_settings     = decom_get_options();
	$enable_embed_links = $follow = empty( $decom_settings['enable_embed_links'] ) ? 0 : $decom_settings['enable_embed_links'];
	if ( $enable_embed_links ) {
		$max_embed_links_count = empty( $decom_settings['max_embed_links_count'] ) ? 0 : $decom_settings['max_embed_links_count'];
	}

	$search = "/((https?)\:\/\/)?([a-z0-9]{1})((\.[a-z0-9-])|([a-z0-9-]))*\.([a-z]{2,6})+(\/)?[^\s^<]*\b(\/)?/";
	if ( preg_match_all( $search, $content, $matches ) ) {
		if ( is_array( $matches ) ) {
			if ( isset( $decom_settings['allow_html_in_comments'] ) && $decom_settings['allow_html_in_comments'] == 1 ) {
				preg_match_all( '/(href|src)="(.*?)"/', $content, $links_not_convert );
				foreach ( $links_not_convert[2] as $item ) {
					$links_not_convert_arr[] = md5( $item );
				}
			}
			$i = 0;
			foreach ( $matches[0] as $url ) {
				if ( is_array( $links_not_convert_arr ) && in_array( md5( $url ), $links_not_convert_arr ) ) {
					continue;
				}

				$info      = pathinfo( $url );
				$mime_type = isset( $info['extension'] ) ? $info['extension'] : '';
				$replace   = '';
				if ( $enable_embed_links ) {
					$i ++;
					if ( $i <= $max_embed_links_count ) {
						if ( in_array( $mime_type, array(
							'png',
							'jpeg',
							'jpg',
							'gif'
						) )
						) {
							$replace = "<img src=\"$url\" width=\"100%\" height=\"100%\"/>";
							$content = decom_url_replace( $url, $replace, $content );
						} elseif ( preg_match( "/(http|https):\/\/(www.instagram|instagram|tw|twitter|www.twitter|vimeo|pinterest|www.pinterest)\.(be|com)\/([^<\s]*)/", $url, $match ) ) {
							$replace = wp_oembed_get( $url );
							$content = str_replace( $url, $replace, $content );
						} elseif ( preg_match( "/(http|https):\/\/(www.youtube|youtube|youtu)\.(be|com)\/([^<\s]*)/", $url, $match ) ) {

							if ( preg_match( '/youtube\.com\/watch\?v=([^\&\?\/]+)/', $url, $id ) ) {
								$values = $id[1];
							} else if ( preg_match( '/youtube\.com\/embed\/([^\&\?\/]+)/', $url, $id ) ) {
								$values = $id[1];
							} else if ( preg_match( '/youtube\.com\/v\/([^\&\?\/]+)/', $url, $id ) ) {
								$values = $id[1];
							} else if ( preg_match( '/youtu\.be\/([^\&\?\/]+)/', $url, $id ) ) {
								$values = $id[1];
							} else if ( preg_match( '/youtube\.com\/verify_age\?next_url=\/watch%3Fv%3D([^\&\?\/]+)/', $url, $id ) ) {
								$values = $id[1];
							}
							$replace = '<iframe width="500" height="281" src="https://www.youtube.com/embed/' . $values . '" frameborder="0" allowfullscreen></iframe>';
							$content = str_replace( $url, $replace, $content );
						} elseif ( preg_match( "/(http|https):\/\/(fb|www.facebook|facebook)\.com\/([^<\s]*)/", $url, $match ) ) {
							if ( preg_match( '/\/posts\//', $url, $match ) || preg_match( '/\/photos\//', $url, $match ) ) {
								$replace = '<div id="fb-root"></div>';
								$replace .= '<script>(function(d, s, id) {  var js, fjs = d.getElementsByTagName(s)[0];  if (d.getElementById(id)) return;  js = d.createElement(s); js.id = id;  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";  fjs.parentNode.insertBefore(js, fjs);}(document, \'script\', \'facebook-jssdk\'));</script>';
								$replace .= '<div class="fb-post" data-href="' . $url . '"></div>';
								$content = str_replace( $url, $replace, $content );
							} else {
								$replace = decom_make_clicable_url( $url );
								$content = str_replace( $url, $replace, $content );
							}
						} elseif ( preg_match( "/(http|https):\/\/plus.google\.com\/([^<\s]*)/", $url, $match ) ) {
							$replace = '<!-- Place this tag in your head or just before your close body tag. -->';
							$replace .= '<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>';
							$replace .= '<!-- Place this tag where you want the widget to render. -->';
							$replace .= '<div class="g-post" data-href="' . $url . '"></div>';
							$content = str_replace( $url, $replace, $content );
						} else {
							$replace = decom_make_clicable_url( $url );
							$content = str_replace( $url, $replace, $content );
						}
					} else {
						$replace = decom_make_clicable_url( $url );
						$content = str_replace( $url, $replace, $content );
					}
				} else {
					$replace = decom_make_clicable_url( $url );
					$content = str_replace( $url, $replace, $content );
				}
			}
		}
	}
	if ( preg_match( '/<blockquote><div><cite>/', $content, $match ) ) {
		list( $tmp, $qute_content ) = explode( '<blockquote><div><cite>', $content );
		list( $qute_content, $tmp ) = explode( '</cite></div></blockquote>', $qute_content );
		$tmp_qute_content = str_replace( ' ', '', $qute_content );
		if ( empty( $tmp_qute_content ) ) {
			$content = str_replace( '<blockquote><div><cite>' . $qute_content . '</cite></div></blockquote>', '', $content );
		}
	}
	$content = convert_smilies( $content );
	$content = str_replace( array( "\r", "\n", "\r\n", "\n\r" ), '<br>', $content );

	$content = wpautop( $content );

	return $content;
}

add_filter( 'decomments_comment_text', 'decom_comments_formated_content' );

/**
 * @param $text
 *
 * @return mixed|string
 */
function decom_make_clicable_url( $text ) {
	$text   = make_clickable( $text );
	$follow = get_option( 'decom_follow' );
	$text   = preg_replace( '|<a href="(.*)" rel="nofollow">.*</a>|', '<a href="\1" rel="' . $follow . '" target="_blank">\1</a>', $text );

	return $text;
}

/**
 * @param $url
 * @param $replace
 * @param $content
 *
 * @return mixed
 */
function decom_url_replace( $url, $replace, $content ) {

	$start  = strpos( $content, $url );
	$length = strlen( $url );

	$element_check = substr( $content, $start - 6, 6 );
	while ( $element_check == 'href="' || $element_check == 'cite="' ) {
		$new_start     = $start + strlen( $url );
		$start         = strpos( $content, $url, $new_start );
		$element_check = substr( $content, $start - 6, 6 );
	}

	return $content = $start !== false ? substr_replace( $content, $replace, $start, $length ) : $content;
}

/**
 * @param $text
 *
 * @return mixed|string
 */
function decom_filter_tags_replace( $text ) {

	$text = preg_replace( '#<p>\s*</p>#siU', '', $text );
	$text = preg_replace( '#(<div>.*)(<p>)(.*<cite>)#siU', '$1$3', $text );
	$text = preg_replace( '#(</div>.*)(<p>)(.*</cite>)#siU', '$1$3', $text );
	$text = preg_replace( '#(</blockquote>.*)(<p>)(.*<script.*)#siU', '$1$3', $text );

	$arr = preg_split( '#(<p.*>.*</p>)#siU', $text, - 1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );

	if ( is_array( $arr ) && count( $arr ) > 0 ) {
		foreach ( $arr as &$a ) {
			if ( strpos( $a, '<p>' ) !== false && strpos( $a, '</p>' ) === false ) {
				$a = str_replace( '<p>', '', $a );
			} elseif ( strpos( $a, '<p>' ) === false && strpos( $a, '</p>' ) !== false ) {
				$a = str_replace( '</p>', '', $a );
			} else {
				$a = preg_replace( '#(<p.*>.*)(<p>)+?(.*</p>)#siU', '$1$3', $a );
				$a = preg_replace( '#(<p.*>.*)(</p>)+?(.*</p>)#siU', '$1$3', $a );
			}
		}
		$text = implode( '', $arr );
	}

	return $text;
}

/**
 * Clean XSS code in comments
 *
 * @param $content
 *
 * @return mixed|string
 */
function decom_xss_clean( $data ) {
	$data = str_replace( array( '&amp;', '&lt;', '&gt;' ), array( '&amp;amp;', '&amp;lt;', '&amp;gt;' ), $data );
	$data = preg_replace( '/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data );
	$data = preg_replace( '/(&#x*[0-9A-F]+);*/iu', '$1;', $data );
	$data = html_entity_decode( $data, ENT_COMPAT, 'UTF-8' );
	// Remove any attribute starting with "on" or xmlns
	$data = preg_replace( '#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data );
	// Remove javascript: and vbscript: protocols
	$data = preg_replace( '#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data );
	$data = preg_replace( '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data );
	$data = preg_replace( '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data );
	// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
	$data = preg_replace( '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data );
	$data = preg_replace( '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data );
	$data = preg_replace( '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data );
	// Remove namespaced elements (we do not need them)
	$data = preg_replace( '#</*\w+:\w[^>]*+>#i', '', $data );
	do {
		// Remove really unwanted tags
		$old_data = $data;
		$data     = preg_replace( '#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data );
	} while ( $old_data !== $data );
	$data = strip_tags( $data );
	$data = filter_var( $data, FILTER_SANITIZE_STRING );

	// we are done...
	return $data;
}

/**
 * @param $max_page
 * @param $post_id
 * @param $page
 */
function otherPagination( $max_page, $post_id, $page ) {

	if ( $page > $max_page ) {
		$page = $max_page;
	}

	$args = array(
		'base'         => add_query_arg( 'cpage', '%#%' ),
//		'base'         => '',
		'format'       => null,
		'total'        => $max_page,
		'current'      => $page,
		'echo'         => false,
		'add_fragment' => '#comments',
		'prev_text'    => '<span class="decomments-button nav-previous decomments-nav-previous"><i class="decomments-icon-angle-double-right"></i>' . __( 'Previous comments', DECOM_LANG_DOMAIN ) . '</span>',
		'next_text'    => '<span style="float:right" class="decomments-button nav-next decomments-nav-next">' . __( 'Next comments', DECOM_LANG_DOMAIN ) . '<i class="decomments-icon-angle-double-right"></i></span>',
		'type'         => 'array'
	);

	$arr = paginate_comments_links( $args );
	if ( count( $arr ) > 1 ) {
		if ( $page != 1 ) {
			$prev_link_comment = $arr[0];
//			echo $prev_link_comment;
			$yes_slash         = get_end_slash_in_url( $prev_link_comment );
			$prev_link_comment = str_replace( '?cpage=' . ( $page - 1 ), '', $prev_link_comment );
			$prev_link_comment = str_replace( '&cpage=' . ( $page - 1 ), '', $prev_link_comment );
			$prev_link_comment = str_replace( 'comment-page-' . $page, 'comment-page-' . ( $page - 1 ), $prev_link_comment );
			if ( ! preg_match( '/comment-page/', $prev_link_comment, $match ) ) {
				if ( $yes_slash ) {
					$prev_link_comment = str_replace( '#comments', 'comment-page-' . ( $page - 1 ) . '/#comments', $prev_link_comment );
				} else {
					$prev_link_comment = str_replace( '#comments', '/comment-page-' . ( $page - 1 ) . '/#comments', $prev_link_comment );
				}

			}

			echo $prev_link_comment;
		} else {
			$href = get_permalink( $post_id );
			$href = str_replace( site_url(), '', $href );
			if ( $href ) {
				echo '<a id="decom_cur_page" style="display: none" href="' . $href . '"></a>';
			}
		}

		if ( $page < $max_page ) {
			$next_link_comment = $arr[ count( $arr ) - 1 ];
			$yes_slash         = get_end_slash_in_url( $next_link_comment );

			$next_link_comment = str_replace( '?cpage=' . ( $page + 1 ), '', $next_link_comment );
			$next_link_comment = str_replace( 'comment-page-' . $page, 'comment-page-' . ( $page + 1 ), $next_link_comment );
			if ( ! preg_match( '/comment-page/', $next_link_comment, $match ) ) {
				if ( $yes_slash ) {
					$next_link_comment = str_replace( '#comments', 'comment-page-' . ( $page + 1 ) . '/#comments', $next_link_comment );
				} else {
					$next_link_comment = str_replace( '#comments', '/comment-page-' . ( $page + 1 ) . '/#comments', $next_link_comment );
				}
			}
			if ( ( $page + 1 ) == $max_page ) {
				$next_link_comment = str_replace( 'comment-page-' . ( $page + 1 ) . '/#comments', '', $next_link_comment );
				$next_link_comment = str_replace( 'comment-page-' . ( $page + 1 ) . '#comments', '', $next_link_comment );
			}

			echo $next_link_comment;
		}
	}
}

/**
 * @param $content
 *
 * @return bool
 */
function get_end_slash_in_url( $content ) {
	$search = "/href=\"(.*?)\"/";

	if ( preg_match( $search, $content, $match ) ) {
		list( $main_url, $comment_pagination_params ) = explode( '?', $match[1] );
		$main_url = str_split( $main_url );
		if ( count( $main_url ) > 0 && $main_url[ count( $main_url ) - 1 ] == '/' ) {
			return true;
		}
	}

	return false;
}

/**
 * @param $current_user_id
 *
 * @return mixed|string
 */
function getUserSort( $current_user_id ) {

	$allVariableCommentsSort = array(
		'rate',
		'newer',
		'older'
	);

	if ( array_key_exists( 'decom_comments_sort', $_POST ) && in_array( $_POST['decom_comments_sort'], $allVariableCommentsSort ) ) {
		update_user_meta( $current_user_id, 'decom_comments_sort', $_POST['decom_comments_sort'] );
		$user_sort = $_POST['decom_comments_sort'];
	} else {
		$user_sort = get_user_meta( $current_user_id, 'decom_comments_sort', true );
		if ( ! $user_sort ) {
			$user_sort = 'older';
		}
	}

	return $user_sort;
}

/**
 * @param $comment
 * @param $args
 * @param $depth
 */
function decom_render_comment( $comment, $args, $depth ) {

	$view_comments = DECOM_Loader_MVC::getComponentView( 'comments', 'comments' );
	echo $view_comments->renderCommentBegin( $comment, $args, $depth );
}

/**
 * @param $comment
 * @param $args
 * @param $depth
 */
function decom_end_comment( $comment, $args, $depth ) {

	$view_comments = DECOM_Loader_MVC::getComponentView( 'comments', 'comments' );
	echo $view_comments->renderCommentEnd( $comment, $args, $depth );
}


/**
 * @return mixed
 */
function decom_get_options() {
	$decom_options_instance = Decom_Settings::instance();

	return $decom_options_instance->get_settings();
}

/**
 * Class Decom_Settings
 */
class Decom_Settings {

	private static $settings = array();

	private static $decom_prefix = '';

	private static $default_settings = array(
		'avatar'                                     => '',
		'avatar_size_thumb'                          => 60,
		'avatar_height'                              => 44,
		'avatar_width'                               => 44,
		'number_comments_per_page'                   => 10,
		'follow'                                     => 'dofollow',
		// dofollow или nofollow
		'output_subscription_comments'               => true,
		'mark_subscription_comments'                 => 0,
		'output_subscription_rejoin'                 => true,
		'mark_subscription_rejoin'                   => true,
		'allocate_comments_author_post'              => true,
		'allocate_comments_author_post'              => 'Reset color',
		'background_comment_author'                  => '#ffffff',
		'allow_html_in_comments'                     => false,
		'output_numbers_comments'                    => true,
		'allow_quote_comments'                       => true,
		'output_total_number_comments_top'           => true,
		'enable_client_validation_fields'            => true,
		'sort_comments'                              => 'best',
		//array( best ‘Лучший’,  newest ‘Самые новые’, earlier ‘Ранее’)
		'comments_negative_rating_below'             => true,
		'show_comments_negative_rating_low_opacity'  => true,
		'show_two_comments_highest_ranking_top_list' => true,
		'max_size_uploaded_images'                   => 5,
		'time_editing_deleting_comments'             => 30,
		'display_avatars_right'                      => false,
		'comment_form_up'                            => true,
		'enable_lazy_comments_loading'               => false,
		'best_comment_min_likes_count'               => 5,
		'enable_dislike'                             => true,
		'allow_lazy_load'                            => false,
		'enable_embed_links'                         => false,
		'max_embed_links_count'                      => 3,
		'enable_social_share'                        => false,
		'tweet_share'                                => 0,
		'facebook_share'                             => 0,
		'vkontakte_share'                            => 0,
		'google_share'                               => 0,
		'linkedin_share'                             => 0,
		'enable_field_website'                       => 0,
	);

	/**
	 * Class instance.
	 */
	protected static $_instance = null;

	/**
	 * Get class instance
	 */
	final public static function instance() {
		$class = get_called_class();

		if ( is_null( self::$_instance ) ) {
			self::$_instance    = new $class();
			self::$decom_prefix = DECOM_PREFIX;
		}

		return self::$_instance;
	}

	function get_settings() {
		if ( count( self::$settings ) > 0 ) {
			return self::$settings;
		}

		$options = wp_load_alloptions();

		$options_are_exist = false;
		$tmp_options       = array();

		if ( count( $options ) > 0 ) {

			foreach ( $options as $op_key => $option ) {
				if ( preg_match( '/^' . self::$decom_prefix . '(.*)/', $op_key, $matches ) ) {
					$tmp_options[ $matches[1] ] = $option;
					$options_are_exist          = true;
				} else if ( $op_key == 'page_comments' ) {
					$tmp_options['page_comments'] = $option;
				} else if ( $op_key == 'active_plugins' ) {
					$tmp_options['active_plugins'] = unserialize( $option );
				} else if ( $op_key == 'show_avatars' ) {
					$tmp_options['show_avatars'] = $option;
				} else if ( $op_key == 'comments_per_page' ) {
					$tmp_options['comments_per_page'] = $option;
				} else if ( $op_key == 'date_format' ) {
					$tmp_options['date_format'] = $option;
				} else if ( $op_key == 'time_format' ) {
					$tmp_options['time_format'] = $option;
				}
			}
		}

		if ( ! $options_are_exist ) {
			$tmp_options = self::$default_settings;
		}
		$options = $tmp_options;

		if ( is_array( $options ) && count( $options ) ) {
			self::$settings = $options;

			return $options;
		}
	}

}

function deco_get_user_badges( $user_id_or_email ) {
	$decom_options_instance = Decom_Badges_By_User::instance();

	return $decom_options_instance->get_badges_by_user( $user_id_or_email );

}

class Decom_Badges_By_User {

	private static $users = array();

	/**
	 * Class instance.
	 */
	protected static $_instance = null;

	/**
	 * Get class instance
	 */
	final public static function instance() {
		$class = get_called_class();

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new $class();

			return self::$_instance;
		}

		return self::$_instance;
	}

	function get_badges_by_user( $user_id_or_email ) {
		if ( empty( $user_id_or_email ) ) {
			return false;
		}

		if ( isset( self::$users[ $user_id_or_email ] ) ) {
			return self::$users[ $user_id_or_email ];
		}

		global $wpdb;

		if ( $badges = $wpdb->get_results( "select * from {$wpdb->prefix}decom_badges" ) ) {

			if ( is_email( $user_id_or_email ) ) {
				$where          = "WHERE comment_approved = 1 AND comment_author_email = '$user_id_or_email' ";
				$comment_count  = $wpdb->get_var( "SELECT comment_ID FROM {$wpdb->comments} {$where} " );
				$likes_dislikes = $wpdb->get_var( "select (SUM(vote_like) - SUM(vote_dislike)) as votes from {$wpdb->prefix}decom_comments_votes where fk_comment_id IN ($coment_IDS) group by fk_user_id" );
			} else {
				$where          = 'WHERE comment_approved = 1 AND user_id = ' . $user_id_or_email;
				$likes_dislikes = $wpdb->get_var( "select (SUM(vote_like) - SUM(vote_dislike)) as votes from {$wpdb->prefix}decom_comments_votes where fk_user_id = $user_id group by fk_user_id" );
			}

			$comment_count = $wpdb->get_var( "SELECT COUNT(*) AS total FROM {$wpdb->comments} {$where}" );

			if ( $likes_dislikes > 0 ) {
				$likes_count = $likes_dislikes;
			} elseif ( $likes_dislikes < 0 ) {
				$dislike_count = - 1 * $likes_dislikes;
			}
			$badges_user = '';
			foreach ( $badges as $item ) {
				if ( $item->badge_like_number && $likes_count && $likes_count >= $item->badge_like_number ) {
					$badges_user[] = $item;
				} elseif ( $item->badge_dislike_number && $dislike_count && $dislike_count >= $item->badge_dislike_number ) {
					$badges_user[] = $item;
				} elseif ( $item->badge_comments_number && $comment_count && $comment_count >= $item->badge_comments_number ) {
					$badges_user[] = $item;
				}
			}
			self::$users[ $user_id_or_email ] = $badges_user;

			return self::$users[ $user_id_or_email ];
		}

		return false;
	}

}

function decom_is_user_block( $user_id = 0, $user_email = '' ) {
	$is_blocked = false;
	if ( $user_id ) {
		$is_blocked = get_user_meta( $user_id, 'decom_block_user_leave_comment', true ) ? true : false;
	} elseif ( $user_email ) {
		$decom_blocked_guests_leave_comment = get_option( 'decom_blocked_guests_leave_comment' );
		if ( isset( $decom_blocked_guests_leave_comment[ $user_email ] ) ) {
			$is_blocked = intval( $decom_blocked_guests_leave_comment[ $user_email ] ) ? true : false;
		}
	}

	return $is_blocked;

}

/**
 * @param string $comment
 * @param int    $avatar_size
 *
 * @return mixed
 */
function decom_get_comment_avatar_cached( $comment = '', $avatar_size = 90 ) {
	$comment_avatar_hash    = md5( "{$comment->comment_author_email}{$comment->user_id}" );
	$decom_options_instance = Decom_Comment_Avatar_Cached::instance();

	return $decom_options_instance->get_comment_avatar_by_comment_hash( $comment_avatar_hash, $comment, $avatar_size );

}

/**
 * Class Decom_Comment_Avatar_Cached
 */
class Decom_Comment_Avatar_Cached {

	private static $users_avatar = array();

	/**
	 * Class instance.
	 */
	protected static $_instance = null;

	/**
	 * Get class instance
	 */
	final public static function instance() {
		$class = get_called_class();

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new $class();

			return self::$_instance;
		}

		return self::$_instance;
	}

	function get_comment_avatar_by_comment_hash( $comment_avatar_hash, $comment, $avatar_size ) {
		if ( empty( $comment_avatar_hash ) ) {
			return false;
		}

		if ( isset( self::$users_avatar[ $comment_avatar_hash ] ) ) {
			return self::$users_avatar[ $comment_avatar_hash ];
		}

		$avatar = get_avatar( $comment, $avatar_size );

		/*$search = "/((https?)\:\/\/)?([a-z0-9]{1})((\.[a-z0-9-])|([a-z0-9-]))*\.([a-z]{2,6})+(\/)?[^\s^<]*\b(\/)?/";
		preg_match_all( $search, $avatar, $matches );
		$image_mime = pathinfo( $matches[0][0] );
		if ( isset( $_GET['decotest'] ) ) {
			print_r( $image_mime );
		}
		if ( isset( $image_mime['extension'] ) && in_array( $image_mime['extension'], array(
				'jpg',
				'jpeg',
				'gif',
				'png'
			) )
		) {
			$img_content_check = wp_remote_get( $matches[0][0], array( 'timeout' => 13 ) );
			if ( ! is_wp_error( $img_content_check ) ) {
				if ( empty( $img_content_check['body'] ) ) {
					$avatar = get_avatar( 0, $avatar_size );
				}
			}
		}*/

		self::$users_avatar[ $comment_avatar_hash ] = $avatar;

		return $avatar;
	}

}

/**
 * @param $user_id
 *
 * @return mixed
 */
function decom_get_user_data( $user_id ) {
	$decom_options_instance = Decom_Comment_User_Data::instance();

	return $decom_options_instance->get_user_data( $user_id );

}

/**
 * Class Decom_Comment_User_Data
 */
class Decom_Comment_User_Data {

	private static $users = array();

	/**
	 * Class instance.
	 */
	protected static $_instance = null;

	/**
	 * Get class instance
	 */
	final public static function instance() {
		$class = get_called_class();

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new $class();

			return self::$_instance;
		}

		return self::$_instance;
	}

	function get_user_data( $user_id ) {
		if ( empty( $user_id ) ) {
			return false;
		}

		if ( isset( self::$users[ $user_id ] ) ) {
			return self::$users[ $user_id ];
		}

		self::$users[ $user_id ] = get_userdata( $user_id );

		self::$users[ $user_id ]->data->count_user_posts = get_post_meta( $user_id, 'count_user_posts', true );

		return self::$users[ $user_id ];
	}

}

/**
 * If comment have children comments or not
 *
 * @param $comment_ID
 *
 * @return bool
 */
function deco_comment_get_children_comments( $comment_ID ) {
	global $wpdb;
	static $comments_not_in;

	if ( empty( $comments_not_in ) ) {
		$comments_not_in = $wpdb->get_col( "SELECT comment_ID FROM {$wpdb->comments} decom WHERE comment_approved IN ('trash') and NOT EXISTS (SELECT * FROM {$wpdb->comments} WHERE comment_parent = decom.comment_ID  AND comment_approved NOT IN ('trash') )" );
	}

	if ( in_array( $comment_ID, $comments_not_in ) ) {
		return true;
	}

	return false;
}


function decom_pre_get_comments( $query ) {
	global $wpdb, $wp_query;

	static $comments_not_in;
	if ( empty( $comments_not_in ) ) {
		$comments_not_in = $wpdb->get_col( "SELECT comment_ID FROM {$wpdb->comments} decom WHERE comment_approved IN ('trash') and NOT EXISTS (SELECT * FROM {$wpdb->comments} WHERE comment_parent = decom.comment_ID  AND comment_approved NOT IN ('trash') )" );
	}
//	unset( $query );
//	$query->set( 'comment__not_in', array( 614 ) );
//	print_r( $wp_query );

}

//add_action( 'pre_get_comments', 'action_pre_get_comments' );


function decom_is_comment_close( $post ) {
	$obj = $post;
	if ( is_object( $obj ) ) {
		return ( 'open' == $post->comment_status ) ? false : true;
	}

	return false;
}

function decom_get_comment_post( $post_id ) {
	global $post;
	if ( empty( $post ) ) {
		static $decom_get_comment_post;
		if ( $decom_get_comment_post ) {
			return $decom_get_comment_post;
		}

		return get_post( $post_id );
	}

	return $post;
}