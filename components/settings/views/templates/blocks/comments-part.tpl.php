<header>
	<i class="icon-forum"></i>

	<h2><?php esc_html_e( 'Comments', DECOM_LANG_DOMAIN ); ?></h2>
</header>

<p>
	<input name="disable_display_logo" id="_disable_display_logo" type="checkbox" <?php checked( $settings['disable_display_logo'], 1 ); ?>/>
	<label for="_disable_display_logo"><?php echo $setting_name['disable_display_logo']; ?></label>
	<br>
	<label for="decom_your_affiliate_id">
		https://decomments.com?ref=
		<input name="decom_your_affiliate_id" id="decom_your_affiliate_id" type="number" style="width:54px;" value="<?php echo $settings['decom_your_affiliate_id']; ?>" placeholder="ID" /><br>
	</label>

</p>

<p>
	<input name="allow_quote_comments" id="_allow_quote_comments" type="checkbox" <?php checked( $settings['allow_quote_comments'], 1 ); ?>/>
	<label for="_allow_quote_comments"><?php echo $setting_name['allow_quote_comments'] ?></label>
</p>

<p>
	<input name="decom_disable_replies" id="_decom_disable_replies" type="checkbox" <?php checked( $settings['decom_disable_replies'], 1 ); ?>/>
	<label for="_decom_disable_replies"><?php echo $setting_name['decom_disable_replies'] ?></label>
</p>

<p>
	<label for="_time_editing_deleting_comments"><?php echo $setting_name['time_editing_deleting_comments'] ?></label>
	<input class="easyui-numberspinner" data-options="min:1" name="time_editing_deleting_comments" id="_time_editing_deleting_comments" type="number" value="<?php echo $settings['time_editing_deleting_comments'] ?>" size="5" max="240" />
</p>

<p>
	<input name="comment_form_up" id="_comment_form_up" type="checkbox" <?php checked( $settings['comment_form_up'], 1 ); ?>/>
	<label for="_comment_form_up"><?php echo $setting_name['comment_form_up'] ?></label>
</p>

<p>
						<span class="dsp-label"><?php echo $setting_name['follow'] ?></label>
						<span class="dsp-fieldset">
							<input name="follow" id="_follow" type="radio" value="dofollow" <?php checked( $settings['follow'], 'dofollow' ); ?>/>
							<label for="_follow"><?php _e( 'dofollow', DECOM_LANG_DOMAIN ); ?></label>
						<br>
							<input name="follow" id="_nofollow" type="radio" value="nofollow" <?php checked( $settings['follow'], 'nofollow' ); ?>/>
							<label for="_nofollow"><?php _e( 'nofollow', DECOM_LANG_DOMAIN ); ?></label>
						</span>
</p>

<p>
	<input name="allocate_comments_author_post" id="_allocate_comments_author_post" type="checkbox" <?php checked( $settings['allocate_comments_author_post'], 1 ); ?>/>
	<label for="_allocate_comments_author_post"><?php echo $setting_name['allocate_comments_author_post'] ?></label>
</p>

<p>
	<label for="decomments_main_color_theme"><?php echo $setting_name['decomments_main_color_theme'] ?></label>

	<input type="text" name="decomments_main_color_theme" id="decomments-settings-color-field" value="<?php echo $settings['decomments_main_color_theme']; ?>" />
</p>

<p>
	<input name="allow_html_in_comments" id="_allow_html_in_comments" type="checkbox" <?php checked( $settings['allow_html_in_comments'], 1 ); ?>/>
	<label for="_allow_html_in_comments"><?php echo $setting_name['allow_html_in_comments'] ?></label>
</p>

<p>
	<input name="output_numbers_comments" id="_output_numbers_comments" type="checkbox" <?php checked( $settings['output_numbers_comments'], 1 ); ?>/>
	<label for="_output_numbers_comments"><?php echo $setting_name['output_numbers_comments'] ?></label>
</p>

<p>
	<input name="output_total_number_comments_top" id="_output_total_number_comments_top" type="checkbox" <?php checked( $settings['output_total_number_comments_top'], 1 ); ?>/>
	<label for="_output_total_number_comments_top"><?php echo $setting_name['output_total_number_comments_top'] ?></label>
</p>

<p>
	<input id="_enable_embed_links" name="enable_embed_links" type="checkbox" <?php echo $settings['enable_embed_links'] ? 'checked' : ''; ?>/>
	<label for="_enable_embed_links"><?php echo $setting_name['enable_embed_links']; ?></label>

						<span id="max_embed_links_count" class="show-hidden">
							<label for="_max_embed_links_count"><?php echo '=> ' . $setting_name['max_embed_links_count']; ?></label>
							<input class="easyui-numberspinner" data-options="min:1" name="max_embed_links_count" id="_max_embed_links_count" type="number" value="<?php echo $settings['max_embed_links_count'] ?>" size="5" />
						</span>
</p>

<p>
	<input name="enable_field_website" id="_enable_field_website" type="checkbox" <?php echo $settings['enable_field_website'] ? 'checked' : ''; ?>/>
	<label for="_enable_field_website"><?php echo $setting_name['enable_field_website']; ?></label>
</p>


<!--<h4><i class="icon-cloud-upload"></i> <?php /*esc_html_e( 'File Upload', DECOM_LANG_DOMAIN ); */ ?></h4>-->

<p>
	<label for="_max_size_uploaded_images"><?php echo $setting_name['max_size_uploaded_images'] ?></label>
	<input class="easyui-numberspinner" name="max_size_uploaded_images" id="_max_size_uploaded_images" type="number" value="<?php echo $settings['max_size_uploaded_images'] ?>" size="5" max="99" /> MB
</p>

<p>
						<span class="dsp-label"><?php echo $setting_name['deco_comments_paginate'] ?></label>
						<span class="dsp-fieldset">
							<input name="deco_ajax_navy" id="_deco_show_more_comments_onebutton" type="radio" value="deco_show_more_comments_onebutton" <?php checked( $settings['deco_ajax_navy'], 'deco_show_more_comments_onebutton' ); ?>/>
							<label for="_deco_show_more_comments_onebutton"><?php _e( '"Show more" button', DECOM_LANG_DOMAIN ); ?></label>
						<br>
							<input name="deco_ajax_navy" id="_deco_show_more_comments_prevnext" type="radio" value="deco_show_more_comments_prevnext" <?php checked( $settings['deco_ajax_navy'], 'deco_show_more_comments_prevnext' ); ?>/>
							<label for="_deco_show_more_comments_prevnext"><?php _e( 'Previous/next pages', DECOM_LANG_DOMAIN ); ?></label>
						<br>
							<input name="deco_ajax_navy" id="_deco_show_more_comments_lazy" type="radio" value="deco_show_more_comments_lazy" <?php checked( $settings['deco_ajax_navy'], 'deco_show_more_comments_lazy' ); ?>/>
							<label for="_deco_show_more_comments_lazy"><?php _e( 'Infinite scroll (lazy load)', DECOM_LANG_DOMAIN ); ?></label>
						</span>
</p>



