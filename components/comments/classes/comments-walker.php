<?php

class DECOM_CommentsWalker extends Walker_Comment {
	/**
	 * @see   Walker::$tree_type
	 * @since 2.7.0
	 * @var string
	 */
	var $tree_type = 'comment';

	/**
	 * @see   Walker::$db_fields
	 * @since 2.7.0
	 * @var array
	 */
	var $db_fields = array( 'parent' => 'comment_parent', 'id' => 'comment_ID' );

	/**
	 * @see   Walker::start_lvl()
	 * @since 2.7.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of comment.
	 * @param array  $args   Uses 'style' argument for type of HTML list.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {

		$GLOBALS['comment_depth'] = $depth + 1;
		$decom_options            = decom_get_options();
		$ast                      = intval( $decom_options['avatar_size_thumb'] ) + 15;
		$is_avatar_right          = isset( $decom_options['display_avatars_right'] ) ? intval( $decom_options['display_avatars_right'] ) : 0;
		$is_avatar_disable        = isset( $decom_options['show_avatars'] ) ? intval( $decom_options['show_avatars'] ) : 0;


		if ( $depth < 5 ) {
			if ( ! $is_avatar_disable ) {
				$decomments_comment_reply_margin_style = 'margin-right';

				$ast = 40;
			} else if ( $is_avatar_right ) {
				$decomments_comment_reply_margin_style = 'margin-right';
			} else {
				$decomments_comment_reply_margin_style = 'margin-left';
			}
			echo '<div class="decomments-comment-reply" style="' . $decomments_comment_reply_margin_style . ': ' . $ast . 'px;">';
		}
	}

	/**
	 * @see   Walker::end_lvl()
	 * @since 2.7.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of comment.
	 * @param array  $args   Will only append content if style argument value is 'ol' or 'ul'.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$GLOBALS['comment_depth'] = $depth + 1;
		if ( $depth < 5 ) {
			echo "</div>\n";
		}

	}

	/**
	 * This public function is designed to enhance Walker::display_element() to
	 * display children of higher nesting levels than selected inline on
	 * the highest depth level displayed. This prevents them being orphaned
	 * at the end of the comment list.
	 *
	 * Example: max_depth = 2, with 5 levels of nested content.
	 * 1
	 *  1.1
	 *    1.1.1
	 *    1.1.1.1
	 *    1.1.1.1.1
	 *    1.1.2
	 *    1.1.2.1
	 * 2
	 *  2.2
	 *
	 */
	public function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {

		if ( ! $element ) {
			return;
		}

		if ( deco_comment_get_children_comments( $element->comment_ID ) ) {
			return;
		}

		$id_field = $this->db_fields['id'];
		$id       = $element->$id_field;

		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );

		// If we're at the max depth, and the current element still has children, loop over those and display them at this level
		// This is to prevent them being orphaned to the end of the list.
		if ( $max_depth <= $depth + 1 && isset( $children_elements[ $id ] ) ) {
			foreach ( $children_elements[ $id ] as $child ) {
				$this->display_element( $child, $children_elements, $max_depth, $depth, $args, $output );
			}

			unset( $children_elements[ $id ] );
		}

	}

	/**
	 * @see   Walker::start_el()
	 * @since 2.7.0
	 *
	 * @param string $output  Passed by reference. Used to append additional content.
	 * @param object $comment Comment data object.
	 * @param int    $depth   Depth of comment in reference to parents.
	 * @param array  $args
	 */
	public function start_el( &$output, $comment, $depth = 0, $args = array(), $id = 0 ) {
		$depth ++;
		$GLOBALS['comment_depth'] = $depth;
		$GLOBALS['comment']       = $comment;

		if ( ! empty( $args['callback'] ) ) {
			call_user_func( $args['callback'], $comment, $args, $depth );

			return;
		}

		extract( $args, EXTR_SKIP );

		if ( 'div' == $args['style'] ) {
			$tag       = 'div';
			$add_below = 'comment';
		} else {
			$tag       = 'li';
			$add_below = 'div-comment';
		}
		?>
		<<?php echo $tag ?><?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID() ?>">
		<?php if ( 'div' != $args['style'] ) : ?>
			<div id="div-comment-<?php comment_ID() ?>" class="comment-body">
		<?php endif; ?>
		<div class="comment-author vcard">
			<?php if ( $args['avatar_size'] != 0 ) {
				echo get_avatar( $comment, $args['avatar_size'] );
			} ?>
			<?php printf( '<cite class="fn">%s</cite> <span class="says">' . __( 'says', DECOM_LANG_DOMAIN ) . ':</span>', get_comment_author_link() ) ?>
		</div>
		<?php if ( $comment->comment_approved == '0' ) : ?>
			<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' ) ?></em>
			<br />
		<?php endif; ?>

		<div class="comment-meta commentmetadata">
			<a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>">
				<?php
				/* translators: 1: date, 2: time */
				printf( __( '%1$s at %2$s' ), get_comment_date(), get_comment_time() ) ?></a><?php edit_comment_link( __( '(Edit)' ), '&nbsp;&nbsp;', '' );
			?>
		</div>

		<?php
		echo apply_filters( 'decomments_comment_text', $comment->comment_content );
		?>

		<div class="reply">
			<?php comment_reply_link( array_merge( $args, array(
				'add_below' => $add_below,
				'depth'     => $depth,
				'max_depth' => $args['max_depth']
			) ) ) ?>
		</div>
		<?php if ( 'div' != $args['style'] ) : ?>
			</div>
		<?php endif; ?>
		<?php
	}

	/**
	 * @see   Walker::end_el()
	 * @since 2.7.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $comment
	 * @param int    $depth  Depth of comment.
	 * @param array  $args
	 */
	public function end_el( &$output, $comment, $depth = 0, $args = array() ) {
		if ( ! empty( $args['end-callback'] ) ) {
			call_user_func( $args['end-callback'], $comment, $args, $depth );

			return;
		}
		echo "</div>\n";
	}

	/**
	 * paged_walk() - produce a page of nested elements
	 *
	 * Given an array of hierarchical elements, the maximum depth, a specific page number,
	 * and number of elements per page, this public function first determines all top level root elements
	 * belonging to that page, then lists them and all of their children in hierarchical order.
	 *
	 * @package WordPress
	 * @since   2.7
	 *
	 * @param int $max_depth = 0 means display all levels; $max_depth > 0 specifies the number of display levels.
	 * @param int $page_num  the specific page number, beginning with 1.
	 *
	 * @return XHTML of the specified page of elements
	 */
	public function paged_walk( $elements, $max_depth, $page_num, $per_page ) {

		/* sanity check */
		if ( empty( $elements ) || $max_depth < - 1 ) {
			return '';
		}

		$args   = array_slice( func_get_args(), 4 );
		$output = '';

		$id_field     = $this->db_fields['id'];
		$parent_field = $this->db_fields['parent'];

		$count = - 1;
		if ( - 1 == $max_depth ) {
			$total_top = count( $elements );
		}
		if ( $page_num < 1 || $per_page < 0 ) {
			// No paging
			$paging = false;
			$start  = 0;
			if ( - 1 == $max_depth ) {
				$end = $total_top;
			}
			$this->max_pages = 1;
		} else {
			$paging = true;
			$start  = ( (int) $page_num - 1 ) * (int) $per_page;
			$end    = $start + $per_page;
			if ( - 1 == $max_depth ) {
				$this->max_pages = ceil( $total_top / $per_page );
			}
		}

		// flat display
		if ( - 1 == $max_depth ) {
			if ( $args[0]['reverse_top_level'] == 'older' ) {
				$elements = array_reverse( $elements );
				$oldstart = $start;
				$start    = $total_top - $end;
				$end      = $total_top - $oldstart;
			} elseif ( $args[0]['reverse_top_level'] == 'rate' ) {
				usort( $top_level_elements, array( 'DECOM_CommentsWalker', 'cmp' ) );
				$oldstart = $start;
				$start    = $total_top - $end;
				$end      = $total_top - $oldstart;
			}

			$empty_array = array();
			foreach ( $elements as $e ) {
				$count ++;
				if ( $count < $start ) {
					continue;
				}
				if ( $count >= $end ) {
					break;
				}
				$this->display_element( $e, $empty_array, 1, 0, $args, $output );
			}

			return $output;
		}

		/*
		 * separate elements into two buckets: top level and children elements
		 * children_elements is two dimensional array, eg.
		 * children_elements[10][] contains all sub-elements whose parent is 10.
		 */
		$top_level_elements = array();
		$children_elements  = array();
		foreach ( $elements as $e ) {
			if ( 0 == $e->$parent_field ) {
				$top_level_elements[] = $e;
			} else {
				$children_elements[ $e->$parent_field ][] = $e;
			}
		}

		$total_top = count( $top_level_elements );
		if ( $paging ) {
			$this->max_pages = ceil( $total_top / $per_page );
		} else {
			$end = $total_top;
		}

		if ( $args[0]['reverse_top_level'] == 'older' ) {
			$top_level_elements = array_reverse( $top_level_elements );
			/*$oldstart = $start;
			$start = $total_top - $end;
			$end = $total_top - $oldstart;*/
		} elseif ( $args[0]['reverse_top_level'] == 'rate' ) {
			usort( $top_level_elements, array( 'DECOM_CommentsWalker', 'cmp' ) );
			/*$oldstart = $start;
			$start = $total_top - $end;
			$end = $total_top - $oldstart;*/
		}
		if ( ! empty( $args[0]['reverse_children'] ) ) {
			foreach ( $children_elements as $parent => $children ) {
				$children_elements[ $parent ] = array_reverse( $children );
			}
		}

		foreach ( $top_level_elements as $e ) {
			$count ++;

			//for the last page, need to unset earlier children in order to keep track of orphans
			if ( $end >= $total_top && $count < $start ) {
				$this->unset_children( $e, $children_elements );
			}

			if ( $count < $start ) {
				continue;
			}

			if ( $count >= $end ) {
				break;
			}

			$this->display_element( $e, $children_elements, $max_depth, 0, $args, $output );
		}

		if ( $end >= $total_top && count( $children_elements ) > 0 ) {
			$empty_array = array();
			foreach ( $children_elements as $orphans ) {
				foreach ( $orphans as $op ) {
					$this->display_element( $op, $empty_array, 1, 0, $args, $output );
				}
			}
		}

		return $output;
	}

	public function cmp( $a, $b ) {
		if ( $a->comment_karma == $b->comment_karma ) {
			return 0;
		}

		return ( $a->comment_karma > $b->comment_karma ) ? - 1 : 1;
	}

}