<?php

class DECOM_Model_Options extends DECOM_Model {
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

	public function __construct() {
		parent::__construct();

		self::$decom_prefix = DECOM_PREFIX;
	}

	private function getPluginOptionName( $option_name ) {
		return self::$decom_prefix . $option_name;
	}

	public function updateOptions( array $settings ) {
		if ( is_array( $settings ) && count( $settings ) > 0 ) {
			foreach ( $settings as $key => $value ) {
				$this->updateOption( $key, $value );
			}
		}

		return true;
	}

	public function updateOption( $option, $option_value ) {
		$option = $this->getPluginOptionName( $option );

		return update_option( $option, $option_value );
	}

	public function insertOption( $option, $option_value ) {
		$option = $this->getPluginOptionName( $option );

		return update_option( $option, $option_value );
	}

	public function getOptions() {
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

		return false;
	}

	public function getWPOption( $option ) {
		return get_option( $option );
	}

	public function updateWPOption( $option, $option_value ) {
		return update_option( $option, $option_value );
	}

	public function getOption( $option ) {
		if ( trim( $option ) == '' ) {
			return false;
		}

		$settings = $this->getOptions();

		if ( array_key_exists( $option, $settings ) ) {
			$option_value = $settings[ $option ];
		} else {
			return false;
		}

		return $option_value;
	}

	public function deleteWPOption( $option ) {
		return delete_option( $option );
	}

	public function deleteOption( $option ) {
		$option = $this->getPluginOptionName( $option );

		return delete_option( $option );
	}
}