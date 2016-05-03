<div id="deco_modal_overlay" class="deco_modal_overlayBG" onclick="decom.closeModal(); return false;"></div>
<div id="deco_modal_window">
	<div id="deco_modal_title">
		<div id="deco_modal_ajaxWindowTitle"><?php esc_html_e( 'Attention', DECOM_LANG_DOMAIN ) ?></div>
		<div id="deco_modal_closeAjaxWindow">
			<a href="#" id="deco_modal_closeWindowButton" >
				<div class="deco_modal-close-icon" onclick="decom.closeModal(); return false;"><img class="svg" src="<?php echo DECOM_TEMPLATE_URL_DEFAULT; ?>assets/images/svg/close_modal.svg"/></div>
			</a>
		</div>
	</div>
	<div id="deco_modal_ajaxContent">
		<div class="decomments-popup-style">
			<div id="decom-alert-void-text" class="decom-popup-box decom-quote-box">
				<p></p>
			</div>
		</div>
	</div>
</div>