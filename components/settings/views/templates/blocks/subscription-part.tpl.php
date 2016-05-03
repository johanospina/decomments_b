<header>
	<i class="icon-quick-contacts-mail"></i>

	<h2><?php _e( 'Subscription', DECOM_LANG_DOMAIN ); ?></h2>
</header>

<p>
	<input name="output_subscription_comments" id="_output_subscription_comments" type="checkbox" <?php checked( $settings['output_subscription_comments'], 1 ); ?> />
	<label for="_output_subscription_comments"><?php echo $setting_name['output_subscription_comments'] ?></label>
</p>

<p>
	<input name="mark_subscription_comments" id="_mark_subscription_comments" type="checkbox" <?php disabled( $settings['output_subscription_comments'], 0 ); ?> <?php echo $settings['mark_subscription_comments'] ? 'checked' : ''; ?>/>

	<label for="_mark_subscription_comments" style="width: 420px" class="mark_subscription_comments <?php echo $settings['output_subscription_comments'] ? '' : 'disabled' ?>"><?php echo $setting_name['mark_subscription_comments'] ?></label>
</p>

<p>
	<input name="output_subscription_rejoin" id="_output_subscription_rejoin" type="checkbox" <?php checked( $settings['output_subscription_rejoin'], 1 ); ?> />
	<label for="_output_subscription_rejoin"><?php echo $setting_name['output_subscription_rejoin'] ?></label>
</p>

<p>
	<input name="mark_subscription_rejoin" id="_mark_subscription_rejoin" type="checkbox" <?php disabled( $settings['output_subscription_rejoin'], 0 ); ?> <?php echo $settings['mark_subscription_rejoin'] ? 'checked' : ''; ?>/>
	<label for="_mark_subscription_rejoin" class="mark_subscription_rejoin <?php echo $settings['output_subscription_rejoin'] ? '' : 'disabled'; ?>"><?php echo $setting_name['mark_subscription_rejoin'] ?></label>
</p>
