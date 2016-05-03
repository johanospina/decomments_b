<div id="deco_modal_overlay" class="deco_modal_overlayBG" onclick="decom.closeModal(); return false;"></div>
<div id="deco_modal_window">
	<div id="deco_modal_title">
		<div id="deco_modal_ajaxWindowTitle"><?php _e( 'Add a picture', DECOM_LANG_DOMAIN ) ?></div>
		<div id="deco_modal_closeAjaxWindow">
			<a href="#" id="deco_modal_closeWindowButton" onclick="decom.closeModal(); return false;">
				<div class="deco_modal-close-icon"><img class="svg" src="<?php echo DECOM_TEMPLATE_URL_DEFAULT; ?>assets/images/svg/close_modal.svg"/></div>
			</a>
		</div>
	</div>
	<div id="deco_modal_ajaxContent">
		<div class="decomments-popup-style">
			<form enctype="multipart/form-data" method="post" action="" id="decomments-add-picture-form" class="decomments-add-picture-form">

				<div class="decomments-load-img">
					<img src="" alt="" />
				</div>

				<div class="decomments-addfile-field" >
					<input type="file" name="decom_pictures[]" />
					<span class="decomments-addfile-cover"><?php _e( 'Choose file', DECOM_LANG_DOMAIN ); ?></span>
				</div>
				<button class="decomments-button decomments-button-addfile-send"><?php _e( 'Submit', DECOM_LANG_DOMAIN ) ?></button>
				<button onclick="decom.closeModal(); return false;" class="decomments-button decomments-button-addfile-cancel"><?php _e( 'Cancel', DECOM_LANG_DOMAIN ) ?></button>
                <button onclick="decom.removeAttachment(this); return false;" class="decomments-button decomments-button-del-image"><i class="icon-bin"></i></button>

			</form>
		</div>
	</div>
</div>