<header>
	<i class="icon-share"></i>

	<h2><?php _e( 'Social', DECOM_LANG_DOMAIN ); ?></h2>
</header>
<h4><?php _e( 'To allow social login buttons we recommend you to install', DECOM_LANG_DOMAIN ); ?>
	<a href="https://wordpress.org/plugins/wordpress-social-login/" target="_blank"><?php _e( 'WP social login (WSL)', DECOM_LANG_DOMAIN ); ?></a>
</h4>
<h4><?php _e( 'Sharing icons', DECOM_LANG_DOMAIN ); ?></h4>

<p>
	<input name="deco_fb_enable_share" id="_deco_fb_enable_share" type="checkbox" <?php checked( $settings['deco_fb_enable_share'], 1 ); ?> />
	<label for="_deco_fb_enable_share"><?php echo $setting_name['deco_fb_enable_share']; ?></label>
</p>

<p>
	<input name="deco_tw_enable_share" id="_deco_tw_enable_share" type="checkbox" <?php checked( $settings['deco_tw_enable_share'], 1 ); ?> />
	<label for="_deco_tw_enable_share"><?php echo $setting_name['deco_tw_enable_share']; ?></label>
</p>

<p>
	<input name="deco_gp_enable_share" id="_deco_gp_enable_share" type="checkbox" <?php checked( $settings['deco_gp_enable_share'], 1 ); ?> />
	<label for="_deco_gp_enable_share"><?php echo $setting_name['deco_gp_enable_share'] ?></label>
</p>

<p>
	<input name="deco_ln_enable_share" id="_deco_ln_enable_share" type="checkbox" <?php checked( $settings['deco_ln_enable_share'], 1 ); ?> />
	<label for="_deco_ln_enable_share"><?php echo $setting_name['deco_ln_enable_share'] ?></label>
</p>

<p>
	<input name="deco_vk_enable_share" id="_deco_vk_enable_share" type="checkbox" <?php checked( $settings['deco_vk_enable_share'], 1 ); ?> />
	<label for="_deco_vk_enable_share"><?php echo $setting_name['deco_vk_enable_share'] ?></label>
</p>

<p>
	<input name="deco_ok_enable_share" id="_deco_ok_enable_share" type="checkbox" <?php checked( $settings['deco_ok_enable_share'], 1 ); ?> />
	<label for="_deco_ok_enable_share"><?php echo $setting_name['deco_ok_enable_share'] ?></label>
</p>


