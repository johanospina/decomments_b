<?php

class DECOM_Controller_Badges extends DECOM_Controller {

	public function getBadges( $post ) {

		global $wpdb;

		$query =
			"SELECT *
             FROM " . $wpdb->prefix . DECOM_TABLE_BADGES . "
                ORDER BY id DESC";

		$badges = $wpdb->get_results( $query );


		foreach ( $badges as $key => $val ) {
			if ( $val->badge_like_number > 0 ) {
				$badges[ $key ]->assign_badge_achive = 'badge_like_number';
				$badges[ $key ]->number              = $val->badge_like_number;
			} elseif ( $val->badge_dislike_number > 0 ) {
				$badges[ $key ]->assign_badge_achive = 'badge_dislike_number';
				$badges[ $key ]->number              = $val->badge_dislike_number;

			} elseif ( $val->badge_comments_number > 0 ) {
				$badges[ $key ]->assign_badge_achive = 'badge_comments_number';
				$badges[ $key ]->number              = $val->badge_comments_number;
			}
		}

		$res = array(
			'total'  => count( $badges ),
			'result' => $badges
		);

		echo json_encode( $res );
		exit;
	}

	/**
	 * @param $post
	 */
	public function addBadge( $post ) {

		global $wpdb;


		$id                  = intval( $post['id'] );
		$badge_icon_path     = $post['badge_icon_path'];
		$badge_name          = $post['badge_name'];
		$assign_badge_achive = $post['assign_badge_achive'];
		$number              = $post['number'];

		if ( $assign_badge_achive == 'badge_like_number' ) {

			$badge_like_number     = $number;
			$badge_comments_number = 0;
			$badge_dislike_number  = 0;

		} elseif ( $assign_badge_achive == 'badge_dislike_number' ) {

			$badge_like_number     = 0;
			$badge_comments_number = 0;
			$badge_dislike_number  = $number;

		} elseif ( $assign_badge_achive == 'badge_comments_number' ) {

			$badge_like_number     = 0;
			$badge_comments_number = $number;
			$badge_dislike_number  = 0;

		} else {
			$badge_like_number     = 0;
			$badge_comments_number = 0;
			$badge_dislike_number  = 0;
		}


		if ( $id ) {
			$res = $wpdb->update(
				$wpdb->prefix . DECOM_TABLE_BADGES,
				array(
					'badge_name'            => $badge_name,
					'badge_like_number'     => $badge_like_number,
					'badge_dislike_number'  => $badge_dislike_number,
					'badge_comments_number' => $badge_comments_number,
					'badge_icon_path'       => $badge_icon_path,
				),
				array( 'id' => $id ),
				array(
					'%s',
					'%d',
					'%d',
					'%d',
					'%s',
					'%s',
				),
				array( '%d' )
			);
		} else {
			$wpdb->insert(
				$wpdb->prefix . DECOM_TABLE_BADGES,
				array(
					'badge_name'            => $badge_name,
					'badge_like_number'     => $badge_like_number,
					'badge_dislike_number'  => $badge_dislike_number,
					'badge_comments_number' => $badge_comments_number,
					'badge_icon_path'       => $badge_icon_path,
					'badge_creation_date'   => current_time( 'mysql' ),
				),
				array(
					'%s',
					'%d',
					'%d',
					'%d',
					'%s',
					'%s',
				)
			);
		}

		echo json_encode( $post );
		die();
	}

	/**
	 * @param $post
	 */
	public function deleteBadges( $post ) {

		$id = intval( $post['id'] );

		if ( $id ) {

			global $wpdb;

			$res = $wpdb->delete(
				$wpdb->prefix . DECOM_TABLE_BADGES,
				array( 'id' => intval( $post['id'] ) ),
				array( '%d' )
			);

			echo json_encode( array( 'success' => __( 'Badge was successfully removed', DECOM_LANG_DOMAIN ) ) );
			exit;
		}

		echo 'failed';

		exit;
	}
}