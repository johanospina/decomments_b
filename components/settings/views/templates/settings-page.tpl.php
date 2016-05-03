

<div id="decom-settings-page" class="decom-settings-page">


	<form id="decom-settings-form">


		<header class="dsc-header">

			<div class="ds-wrap">
				<h1><?php esc_html_e( 'de:comments settings', DECOM_LANG_DOMAIN ); ?></h1>

				<div class="decom-message-box">
					<span class="spinner"></span>

					<div id="message-success" class="updated below-h3"><p></p></div>
					<div id="message-error" class="error below-h2"><p></p></div>
				</div>

				<div id="decom-tools-save" class="decom-tools-save">
					<input id="decom-btn-submit-settings" type="submit" class="button button-primary"
					       value="<?php esc_html_e( 'Save', DECOM_LANG_DOMAIN ); ?>" />
				</div>

			</div>
		</header>


		<ul class="dsp-nav">
			<li data-tab-num="1">
				<span><i class="icon-forum"></i> <?php _e( 'Comments', DECOM_LANG_DOMAIN ); ?></span>
			</li>
			<li data-tab-num="2">
				<span><i class="icon-share"></i>  <?php _e( 'Social', DECOM_LANG_DOMAIN ); ?></span>
			</li>
			<li data-tab-num="3">
				<span><i class="icon-thumbs-up-down"></i>  <?php _e( 'Voting', DECOM_LANG_DOMAIN ); ?></span>
			</li>
			<li data-tab-num="4">
				<span><i class="icon-portrait"></i>  <?php _e( 'Avatar', DECOM_LANG_DOMAIN ); ?></span>
			</li>
			<li data-tab-num="5">
				<span><i class="icon-email"></i>  <?php _e( 'Subscription', DECOM_LANG_DOMAIN ); ?></span>
			</li>
			<li data-tab-num="6">
				<span><i class="icon-contacts"></i>  <?php _e( 'Badges', DECOM_LANG_DOMAIN ); ?></span>
			</li>
			<!--			<li data-tab-num="7">
				<span><i class="icon-play-install"></i>  <?php /*_e( 'Bonus', DECOM_LANG_DOMAIN ); */ ?></span>
			</li>
-->
			<li data-tab-num="8">
				<span><i class="icon-quick-contacts-mail"></i>  <?php _e( 'Notifications', DECOM_LANG_DOMAIN ); ?></span>
			</li>
			<!--			<li data-tab-num="9">
				<span><i class="icon-favorite-outline"></i>  <?php /*_e( 'Other', DECOM_LANG_DOMAIN ); */ ?></span>
			</li>
-->        </ul>


		<div class="decom-settings-list">

			<div class="dsl-item" data-tab="1">
				<?php include 'blocks/comments-part.tpl.php'; ?>
			</div>


			<div class="dsl-item" data-tab="2">
				<?php include 'blocks/social-share-part.tpl.php'; ?>
			</div>


			<div class="dsl-item" data-tab="3">
				<?php include 'blocks/vote-part.tpl.php'; ?>
			</div>


			<div class="dsl-item" data-tab="4">
				<?php include 'blocks/avatar-part.tpl.php'; ?>
			</div>


			<div class="dsl-item" data-tab="5">
				<?php include 'blocks/subscription-part.tpl.php'; ?>
			</div>


			<div class="dsl-item" data-tab="6">
				<?php include 'blocks/badges-part.tpl.php'; ?>
			</div>


			<!--			<div class="dsl-item" data-tab="7">
				<?php /*include 'blocks/bonus-part.tpl.php'; */ ?>
			</div>
-->

			<div class="dsl-item" data-tab="8">
				<?php include 'blocks/notification-part.tpl.php'; ?>
			</div>


			<!--			<div class="dsl-item" data-tab="9">
				<?php /*include 'blocks/other-part.tpl.php'; */ ?>
			</div>
-->
		</div>


	</form>

</div>