<header>
	<i class="icon-favorite-outline"></i>

	<h2><?php _e( 'Other', DECOM_LANG_DOMAIN ); ?></h2>
</header>


<h4><i class="icon-cloud-upload"></i> <?php esc_html_e( 'File Upload', DECOM_LANG_DOMAIN ); ?></h4>

<p>
	<label for="_max_size_uploaded_images"><?php echo $setting_name['max_size_uploaded_images'] ?></label>
	<input class="easyui-numberspinner" name="max_size_uploaded_images" id="_max_size_uploaded_images" type="number" value="<?php echo $settings['max_size_uploaded_images'] ?>" size="5" max="99" /> MB
</p>

<h4><i class="icon-dvr"></i> <?php _e( 'Templates', DECOM_LANG_DOMAIN ); ?></h4>

<p>
	<input name="deco_disable_css_style" id="_deco_disable_css_style" type="checkbox" <?php checked( $settings['deco_disable_css_style'], 1 ); ?>/>
	<label for="_deco_disable_css_style"><?php echo $setting_name['deco_disable_css']; ?></label>
</p>

<!--			<p>
						<span class="dsp-label">Select comment template style:</span>
						<span class="dsp-fieldset">
							<input name="custom_folder_template" id="_custom_folder_template_1" type="radio" value="default" <?php /*checked( $settings['custom_folder_template'], 'default' ); */ ?>/>
							<label for="_custom_folder_template">default</label><br>
							<input name="custom_folder_template" id="_custom_folder_template_2" type="radio" value="theme1" <?php /*checked( $settings['custom_folder_template'], 'theme1' ); */ ?>/>
							<label for="_custom_folder_template_2">theme1</label><br>
							<input name="custom_folder_template" id="_custom_folder_template_3" type="radio" value="theme2" <?php /*checked( $settings['custom_folder_template'], 'theme2' ); */ ?>/>
							<label for="_custom_folder_template_3">theme2</label>
						</span>
					</p>
		-->

