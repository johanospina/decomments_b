<div id="message-success" class="updated below-h3" style="display: none"><p></p></div>
<div id="message-error" class="error below-h2" style="display: none"><p></p></div>

<div class="easyui-tabs" data-options="tools:'#decom-tools-save'" style="margin-right:14px;">
	<div title="<?php echo $tabs['email'] ?>" style="padding:20px;">
		<div id="decom-supported-shortcodes" class="blogdescription">
			<div id="decom_shortcodes" class="decom-description">
				<table>
					<tr>
						<th colspan="2" style=""><?php esc_html_e( 'Supported shortcodes', DECOM_LANG_DOMAIN ); ?></php></th>
					</tr>
					<tr class="odd">
						<td>%COMMENT_AUTHOR%</td>
						<td><?php esc_html_e( "Comment's author", DECOM_LANG_DOMAIN ) ?></td>
					</tr>
					<tr>
						<td>%COMMENT_CREATION_DATE%</td>
						<td><?php esc_html_e( 'Comment creation date', DECOM_LANG_DOMAIN ) ?></td>
					</tr>
					<tr class="odd">
						<td>%COMMENT_TEXT%</td>
						<td><?php esc_html_e( 'Comment text', DECOM_LANG_DOMAIN ) ?></td>
					</tr>
					<tr>
						<td>%COMMENT_LINK%</td>
						<td><?php esc_html_e( 'Comment link', DECOM_LANG_DOMAIN ) ?></td>
					</tr>
					<tr class="odd">
						<td>%COMMENTED_POST_TITLE%</td>
						<td><?php esc_html_e( 'Post title to which was added a comment', DECOM_LANG_DOMAIN ) ?></td>
					</tr>
					<tr>
						<td>%COMMENTED_POST_URL%</td>
						<td><?php esc_html_e( 'Post link', DECOM_LANG_DOMAIN ) ?></td>
					</tr>
				</table>
			</div>
		</div>
		<?php foreach ( $notifications['email'] as $notification_name => $param ): ?>
			<div class="decom-notifikation"><?php echo $param['notification_label'] ?></div>
			<table class="decom-block-lang">
				<?php foreach ( $param['language'] as $leng => $text ):
					if ( $leng == 'ru_RU' ) {
						continue;
					}

					?>
					<tr>
<!--						<td>
							<div class="decom-language">
								<label for="<?php /*echo $notification_name */?>"><?php /*echo $leng */?></label></div>
						</td>
-->						<td>
							<div class="decom-input-lang">
								<input class="easyui-validatebox" required="true" type="text" name="<?php echo 'email-' . $notification_name . '-' . $leng . '-title' ?>" id="<?php echo $notification_name ?>" value='<?php echo $text['notification_title'] ?>' size="84" />
							</div>
						</td>
					</tr>
					<tr>
<!--						<td></td>-->
						<td>
							<textarea class="easyui-validatebox" required="true" cols="87" rows="7" name="<?php echo 'email-' . $notification_name . '-' . $leng . '-text' ?>"><?php echo $text['notification_text'] ?></textarea>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
		<?php endforeach; ?>
	</div>
</div>