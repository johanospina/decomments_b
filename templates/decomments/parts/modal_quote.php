<div id="deco_modal_overlay" class="deco_modal_overlayBG" onclick="decom.closeModal(); return false;"></div>
<div id="deco_modal_window">
	<div id="deco_modal_title">
		<div id="deco_modal_ajaxWindowTitle"><?php _e( 'Add a quote', DECOM_LANG_DOMAIN ) ?></div>
		<div id="deco_modal_closeAjaxWindow" onclick="decom.closeModal(); return false;">
			<a href="#" id="deco_modal_closeWindowButton">
				<div class="deco_modal-close-icon"><img class="svg" src="<?php echo DECOM_TEMPLATE_URL_DEFAULT; ?>assets/images/svg/close_modal.svg"/></div>
			</a>
		</div>
	</div>
	<div id="deco_modal_ajaxContent">
		<div class="decomments-popup-style">

			<div id="decomments-add-blockquote-form">

				<textarea></textarea>

				<button class="decomments-button decomments-button-quote-send" ><?php esc_html_e( 'Submit', DECOM_LANG_DOMAIN ) ?></button>
				<button class="decomments-button decomments-button-quote-cancel" onclick="decom.closeModal(); return false;"><?php esc_html_e( 'Cancel', DECOM_LANG_DOMAIN ) ?></button>

			</div>

		</div>
	</div>
</div>