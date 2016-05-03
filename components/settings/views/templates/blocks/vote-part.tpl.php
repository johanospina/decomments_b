<header>
	<i class="icon-thumbs-up-down"></i>

	<h2><?php _e( 'Voting', DECOM_LANG_DOMAIN ); ?></h2>
</header>


<p>
	<label for="_best_comment_min_likes_count"><?php echo $setting_name['best_comment_min_likes_count'] ?></label>
	<input class="easyui-numberspinner" name="best_comment_min_likes_count" id="_best_comment_min_likes_count" type="number" value="<?php echo $settings['best_comment_min_likes_count']; ?>" size="5" max="99" />
</p>

<p>
	<input name="show_two_comments_highest_ranking_top_list" id="_show_two_comments_highest_ranking_top_list" type="checkbox" <?php checked( $settings['show_two_comments_highest_ranking_top_list'], 1 ); ?>/>
	<label for="_show_two_comments_highest_ranking_top_list"><?php echo $setting_name['show_two_comments_highest_ranking_top_list'] ?></label>
</p>

<p>
	<input name="show_comments_negative_rating_low_opacity" id="_show_comments_negative_rating_low_opacity" type="checkbox" <?php checked( $settings['show_comments_negative_rating_low_opacity'], 1 ); ?>/>
	<label for="_show_comments_negative_rating_low_opacity"><?php echo $setting_name['show_comments_negative_rating_low_opacity'] ?></label>
</p>

<p>
	<input name="enable_dislike" id="_enable_dislike" type="checkbox" <?php checked( $settings['enable_dislike'], 1 ); ?>/>
	<label for="_enable_dislike"><?php echo $setting_name['enable_dislike'] ?></label>
</p>
