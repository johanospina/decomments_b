<header>
	<i class="icon-portrait"></i>

	<h2><?php _e( 'Avatar', DECOM_LANG_DOMAIN ); ?></h2>
</header>

<p>
	<label for="_avatar"><?php _e( 'Default avatar', DECOM_LANG_DOMAIN ); ?></label>
	<?php
	?>
	<input class="decom-settings-avatar" name="avatar"
	       id="decom-avatar-input"
	       type="hidden"
	       value="<?php echo $settings['avatar']; ?>" />
	<br>

	<img id="decom-settings-avatar" width="80px" src="<?php echo $settings['avatar']; ?>" alt="avatar" <?php echo $settings['avatar'] ? '' : 'style="display:none;"' ?>>
	<br>
	<a class="button insert-media add_media" id="deco-upload-default-avatar" href="#" <?php echo $settings['avatar'] ? 'style="display:none;width:82px;"' : '' ?>>
		<?php _e( 'Upload', DECOM_LANG_DOMAIN ); ?>
	</a>
	<a class="button insert-media add_media" id="deco-remove-uploaded-avatar" href="#" <?php echo $settings['avatar'] ? '' : 'style="display:none; width:82px;"' ?>>
		<?php _e( 'Remove', DECOM_LANG_DOMAIN ); ?>
	</a>
</p>

<p>
	<label for="_avatar_size_thumb"><?php echo $setting_name['avatar_size'] ?></label>
	<input class="easyui-numberspinner decom-settings-numberspinner" name="avatar_size_thumb" id="_avatar_size_thumb" type="number" value="<?php echo $settings['avatar_size_thumb'] ?>" data-options="min:1" size="5" /> px
</p>


<p>
	<input name="display_avatars_right" id="_display_avatars_right" type="checkbox" <?php checked( $settings['display_avatars_right'], 1 ); ?>/>
	<label for="_display_avatars_right"><?php echo $setting_name['display_avatars_right'] ?></label>
</p>

<p>
	<input name="display_round_avatars" id="display_round_avatars" type="checkbox" <?php checked( $settings['display_round_avatars'], 1 ); ?>/>
	<label for="display_round_avatars"><?php echo $setting_name['display_round_avatars'] ?></label>
</p>
