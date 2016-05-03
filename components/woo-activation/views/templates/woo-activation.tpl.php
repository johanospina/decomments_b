<style>
	.error_field {
		color: red;
	}
</style>
<h1 id="title"><?php esc_html_e( 'Activate plugin', DECOM_LANG_DOMAIN ); ?></h1>
<?php
if ( ! function_exists( 'curl_init' ) ) {
	?>
	<div class="error"><p>
			<?php _e( "Plugin de:comments couldn't activate : CURL extension not found. CURL is required to be installed on your server. Please ask your hosting provider to install PHP CURL for you.", DECOM_LANG_DOMAIN ); ?>
		</p>
	</div>
	<?php
}
?>
<form method="post" action="<?php echo admin_url( 'edit-comments.php?decom_com=woo-activation&decom_c=woo-activation&decom_a=activate' ); ?>">
	<table cellpadding="4">
		<tr>
			<td>
				<?php esc_html_e( 'Email', DECOM_LANG_DOMAIN ); ?>
			</td>
			<td>
				<input type="text" name="user_email" style="width:200px">
				<?php if ( isset( $_GET['error_user_email'] ) ) { ?>
					<span class="error_field"><?php esc_html_e( 'Please enter Email', DECOM_LANG_DOMAIN ) ?></span>
				<?php } ?>
				<?php if ( isset( $_GET['error_valid_user_email'] ) ) { ?>
					<span class="error_field"><?php esc_html_e( 'Please enter valid Email', DECOM_LANG_DOMAIN ) ?></span>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php esc_html_e( 'Activation key', DECOM_LANG_DOMAIN ); ?>

			</td>
			<td>
				<input type="text" name="activation_key" style="width:200px">
				<?php if ( isset( $_GET['error_activation_key'] ) ) { ?>
					<span class="error_field"><?php esc_html_e( 'Please enter Activation key', DECOM_LANG_DOMAIN ) ?></span>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td>
				<input type="submit" value="<?php esc_html_e( 'Activate', DECOM_LANG_DOMAIN ); ?>" class="button-primary" />
			</td>
		</tr>
	</table>
</form>