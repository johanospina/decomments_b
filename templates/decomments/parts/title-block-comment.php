<?php ob_start(); ?>
	<div class="decomments-title-block" style="min-height:37px;">
		<?php if ( $comment->comment_approved != 'trash' ) { ?>
			<a href="<?php echo $link_comment_anchor; ?>" class="decomments-date-link">
				<?php echo apply_filters( 'decom_comment_date', '
			<time datetime="' . $comment_time . '">' . $comment_time_t . '<i>' . $comment_time_d . '</i></time>' ); ?>
			</a>
			<?php echo $comment_modified_date . $author_link_html . $badges_html . ' ' . $decom_author_title_bottom_url_html; ?>
		<?php } ?>
	</div>
<?php
$html .= ob_get_contents();
ob_end_clean();
?>