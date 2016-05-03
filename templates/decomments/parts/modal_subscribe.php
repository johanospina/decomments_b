<div id="deco_modal_overlay" class="deco_modal_overlayBG" onclick="decom.closeModal(); return false;"></div>
<div id="deco_modal_window">
    <div id="deco_modal_title">
        <div id="deco_modal_ajaxWindowTitle"><?php esc_html_e('Subscribe', DECOM_LANG_DOMAIN) ?></div>
        <div id="deco_modal_closeAjaxWindow">
            <a href="#" id="deco_modal_closeWindowButton">
                <div class="deco_modal-close-icon" onclick="decom.closeModal(); return false;"><img class="svg" src="<?php echo DECOM_TEMPLATE_URL_DEFAULT; ?>assets/images/svg/close_modal.svg"/></div>
            </a>
        </div>
    </div>
    <div id="deco_modal_ajaxContent">
        <div class="decomments-popup-style modal-sub-content">
            <form class="modal-sub-form">
                <a class="decomments-checkbox" href="javascript:void(0)" name="subscribe_my_comment">Replies to my
                    comments</a>
                <a class="decomments-checkbox" href="javascript:void(0)" name="subscribe_my_comment">All comments</a>
                <a class="decomments-checkbox" href="javascript:void(0)" name="subscribe_my_comment">Replies to my
                    comments</a>
                <a class="decomments-checkbox" href="javascript:void(0)" name="subscribe_my_comment">Replies to my
                    comments</a>

                <button class="decomments-button">Submit</button>
                <button class="decomments-button" onclick="decom.closeModal(); return false;">Cancel</button>

            </form>
        </div>


    </div>
</div>