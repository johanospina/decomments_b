<?php

class DECOM_Model_NotificationMessages extends DECOM_Model {
	public static $defaultLocale = array( 'en_US' );

	private function getDefaultParams() {
		$default_params = array(
			'alert' => array(
				'vote_for_my_comment'     => array(
					'ru_RU' => 'Вы не можете голосовать за свой комментарий',
					'en_US' => 'You can’t vote for your own comment'
				),
				'against_only_authorized' => array(
					'ru_RU' => 'Голосовать против могут только авторизованные пользователи',
					'en_US' => 'Unauthorized users can’t dislike the comment. Please enter the site'
				),
				'vote_passed'             => array(
					'ru_RU' => 'Ваш голос уже принят',
					'en_US' => 'Your vote has been already adopted'
				),
			),
			'email' => array(
				'new_post_comment'       => array(
					'ru_RU' => array(
						'label' => 'Новый комментарий к посту',
						'title' => 'Новый комментарий к посту "%COMMENTED_POST_TITLE%"',
						'text'  => 'Новый комментарий к посту "%COMMENTED_POST_TITLE%"
Автор: %COMMENT_AUTHOR%
Комментарий:
%COMMENT_TEXT%

Все комментарии к посту Вы можете увидеть здесь:
%COMMENT_LINK%',
					),
					'en_US' => array(
						'label' => 'New comment on post',
						'title' => 'New comment on post "%COMMENTED_POST_TITLE%"',
						'text'  => 'New comment on post "%COMMENTED_POST_TITLE%"
Author: %COMMENT_AUTHOR%
Comment:
%COMMENT_TEXT%

You can see all comments on this post here:
Permalink: %COMMENT_LINK%',
					)
				),
				'new_comment_to_comment' => array(
					'ru_RU' => array(
						'label' => 'Новый комментарий к комментарию',
						'title' => 'Новый комментарий к комментарию. Пост "%COMMENTED_POST_TITLE%"',
						'text'  => 'Новый комментарий к комментарию. Пост "%COMMENTED_POST_TITLE%"
Автор: %COMMENT_AUTHOR%
Комментарий:
%COMMENT_TEXT%

Все комментарии к посту Вы можете увидеть здесь:
%COMMENT_LINK%',
					),
					'en_US' => array(
						'label' => 'New comment on comment',
						'title' => 'New comment on comment. "%COMMENTED_POST_TITLE%" post',
						'text'  => 'New comment on comment. "%COMMENTED_POST_TITLE%" post
Author: %COMMENT_AUTHOR%
Comment:
%COMMENT_TEXT%

You can see all comments on this post here:
Permalink: %COMMENT_LINK%',
					)
				)
			)
		);

		return $default_params;
	}

	public function __construct() {
		parent::__construct();
		$this->table_notification          = $this->prefix . DECOM_TABLE_NOTIFICATION;
		$this->table_notification_values   = $this->prefix . DECOM_TABLE_NOTIFICATIONS_VALUES;
		$this->table_notification_language = $this->prefix . DECOM_TABLE_NOTIFICATIONS_LANGUAGES;
		$this->table_notification_labels   = $this->prefix . DECOM_TABLE_NOTIFICATION_LABELS;
	}

	public function prepareDB() {
		$query = "
             CREATE TABLE IF NOT EXISTS " . $this->table_notification . " (
                 `id` INT NOT NULL AUTO_INCREMENT,
                 `notification_key` VARCHAR(255) NOT NULL,
                 `notification_type` VARCHAR(20) NOT NULL,
             PRIMARY KEY (id)
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;";

		return $this->createTable( $query );
	}

	public function setDefaultParams() {
		if ( ! $this->checkTableOnTheRecord( $this->table_notification )
		     && ! $this->checkTableOnTheRecord( $this->table_notification_values )
		) {
			$this->insertDefaultParamsTables( $this->getDefaultParams() );
		}
	}

	public function checkTableOnTheRecord( $table_name ) {
		$query = 'SELECT * FROM ' . $table_name;

		return $this->selectRows( $query );
	}

	public function selectNotification( array $columns, array $where ) {
		return $this->selectRowsWhere( $this->table_notification, $columns, $where, array(), 'ARRAY_A' );
	}

	public function insertNotification( $notification_key, $notification_type ) {
		$data                      = array();
		$data['notification_key']  = $notification_key;
		$data['notification_type'] = $notification_type;
		$type                      = array( '%s', '%s' );

		return $this->insert( $this->table_notification, $data, $type );
	}

	public function updateNotifications( $notification_key, $notification_type, $where ) {
		$type                      = array();
		$type['notification_key']  = '%s';
		$type['notification_type'] = '%s';

		$data = array( $notification_key, $notification_type );

		return $this->update( $this->table_notification, $data, $type, $where );
	}

	public function getNotification( $type ) {
		$select = array();
		$where  = array( 'notification_type' => $type );

		return $this->selectNotification( $select, $where );
	}

	public function getNotificationLocale( $notification_key, $notification_locale = 'en_US' ) {
		if ( ! $notification_locale ) {
			$notification_locale = $this->getLocale();
		}

		$sql = 'SELECT notification_text, notification_title FROM ' . $this->table_notification_values . ' v
                JOIN ' . $this->table_notification . ' n ON v.fk_notification_id = n.id
                JOIN ' . $this->table_notification_language . ' l ON  v.fk_language_id = l.id
                WHERE n.notification_key = "' . $notification_key . '" AND l.language = "' . $notification_locale . '"';

		$result = $this->selectRow( $sql );

		return $result;
	}

	public function insertDefaultParamsTables( $notification_param ) {
		$model_labels    = DECOM_Loader_MVC::getComponentModel( DECOM_COMPONENT_NOTIFICATION, 'notification-labels' );
		$model_values    = DECOM_Loader_MVC::getComponentModel( DECOM_COMPONENT_NOTIFICATION, 'notification-values' );
		$model_languages = DECOM_Loader_MVC::getComponentModel( DECOM_COMPONENT_NOTIFICATION, 'notification-languages' );
		$insert_language = array();

		foreach ( $notification_param as $notification_type => $arr ) {
			foreach ( $arr as $notification_key => $textLanguage ) {
				$notification_id = $this->insertNotification( $notification_key, $notification_type );
				if ( $notification_id ) {
					foreach ( $textLanguage as $language => $text ) {
						if ( ! array_key_exists( $language, $insert_language ) ) {
							$language_id                  = $model_languages->insertLanguage( $language );
							$insert_language[ $language ] = $language_id;
						} else {
							$language_id = $insert_language[ $language ];
						}

						if ( is_string( $text ) ) {
							$notification_label = $text;
							$notification_text  = $text;
							$notification_title = '';
						} else {
							$notification_label = $text['label']; //str_replace('/\".*\"/', '', $text['title']);
							$notification_title = $text['title'];
							$notification_text  = $text['text'];
						}

						$model_labels->insertNotificationLabels( $notification_label, $notification_id, $language_id );
						$model_values->insertNotificationValues( $notification_title, $notification_text, $notification_id, $language_id );
					}
				}
			}
		}
	}

	public function clearShortCodeInLabel() {

	}

	public function updateNotificationPostValues( $notification_param ) {
		foreach ( $notification_param as $notification => $arr ) {
			foreach ( $arr as $notification_key => $textLanguage ) {
				foreach ( $textLanguage as $language => $tt ) {
					if ( is_string( $tt ) ) {
						if ( $this->updateNotificationsTextTitle( $notification_key, $language, '', $tt ) === false ) {
							return false;
						}
					} else {
						if ( $this->updateNotificationsTextTitle( $notification_key, $language, $tt['title'], $tt['text'] ) === false ) {
							return false;
						}
					}
				}
			}
		}

		return true;
	}

	public function updateNotificationsTextTitle( $notification_key, $language, $title = '', $text ) {
		$param = array();

		$sql     = "UPDATE " . $this->table_notification_values . " v
                JOIN " . $this->table_notification . ' n ON v.fk_notification_id = n.id
                JOIN ' . $this->table_notification_language . " l ON  v.fk_language_id = l.id
                SET v.notification_text = '%s', v.notification_title = '%s'
                WHERE n.notification_key = '%s' AND l.language = '%s'";
		$param[] = $text;
		$param[] = $title;
		$param[] = $notification_key;
		$param[] = $language;
		$result  = $this->preparedQuery( $sql, $param );

		return $result;
	}

	public function getNotificationID( $where ) {
		$res = $this->selectRowsWhere( $this->table_notification, array( 'id' ), $where );

		return $res[0]['id'];
	}

	public function getLocale() {
		$locale = get_locale();
		if ( in_array( $locale, self::$defaultLocale ) ) {
			return $locale;
		} else {
			return self::$defaultLocale[0];
		}
	}
}

