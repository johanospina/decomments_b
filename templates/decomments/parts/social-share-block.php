<?php
$social_share_block = '';


$share_enable = false;

if ( $settings['deco_fb_enable_share'] || $settings['deco_tw_enable_share'] || $settings['deco_gp_enable_share'] || $settings['deco_ln_enable_share'] || $settings['deco_vk_enable_share'] || $settings['deco_ok_enable_share'] ) {
	$share_enable = true;
}

if ( $share_enable ) {
	$social_share_block .= '

                                <span class="decomments-share-block decom-share">
                                    <ins><i class="decomments-icon-share"></i>' . __( 'Share', DECOM_LANG_DOMAIN ) . '</ins>
                                    <span>';
	if ( $settings['deco_tw_enable_share'] == 1 ) {
		$social_share_block .= '<a href="#" class="decomments-tw-link"><i class="decomments-icon-twitter"></i></a>';
	}
	if ( $settings['deco_fb_enable_share'] == 1 ) {
		$social_share_block .= '<a href="#" class="decomments-fb-link"><i class="decomments-icon-facebook"></i></a>';
	}
	if ( $settings['deco_gp_enable_share'] == 1 ) {
		$social_share_block .= '<a href="#" class="decomments-gp-link"><i class="decomments-icon-googleplus"></i></a>';
	}
	if ( $settings['deco_ln_enable_share'] == 1 ) {
		$social_share_block .= '<a href="#" class="decomments-ln-link"><i class="decomments-icon-linkedin"></i></a>';
	}
	if ( $settings['deco_vk_enable_share'] == 1 ) {
		$social_share_block .= '<a href="#" class="decomments-vk-link"><i class="decomments-icon-vk"></i></a>';
	}
	if ( $settings['deco_ok_enable_share'] == 1 ) {
		$social_share_block .= '<a href="#" class="decomments-oc-link"><i class="decomments-icon-oc"></i></a>';
	}

	$social_share_block .= '</span>
                                </span>
                    ';
}
