<div class="scroll">
	<a name="comments"></a>
	<a class="loadComments" href="<?php echo admin_url() ?>edit-comments.php?decom_com=comments&decom_c=comments&decom_a=loadCommentsBlock&post_id=<?php echo get_the_ID() ?>"></a>
</div>

<script>
	jQuery('.scroll').jscroll({
		/*autoTrigger: false*/
		autoTriggerUntil: 1,
		nextSelector    : 'a.loadComments',
		loadingHtml     : '<span class="deco-preloader"></span>'
	});
</script>
