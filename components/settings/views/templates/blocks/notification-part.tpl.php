<header>
	<i class="icon-info-outline"></i>

	<h2><?php esc_html_e( 'Notifications', DECOM_LANG_DOMAIN ); ?></h2>
</header>

<div class="dsl-popup">

	<?php DECOM_Component_NotificationMessages::renderFormNotification(); ?>

</div>
