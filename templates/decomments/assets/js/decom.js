$ = jQuery.noConflict();
var globalAddPicture = false;
jQuery(function () {
	decom.initAllTimeCounterExpired();
	decom.showLoadPicture();
	decom.bestComments();
	decom.resizeImage();
	decom.paginate();
	decom.liveFunctions();
	decom.focuss();
	decom.labels();
	var comment_id = decom.getURLParameter('comment');
	if (comment_id) {
		decom.scrollToId('comment-' + comment_id, 500);
	}
	jQuery('[name^=decom_pictures]').on('change', function () {
		jQuery('.decom-popup-holder .decom-addfile-send').removeAttr('disabled');
	});
});


function decom_manual_authorization() {
	jQuery('.login_box #authorization_form').submit(function (e) {
		jQuery('#error_auth').hide(150);
		jQuery.ajax({
			type    : 'POST',
			dataType: 'json',
			url     : ajax_login_object.ajaxurl,
			data    : {
				'action'  : 'ajaxlogin', //calls wp_ajax_nopriv_ajaxlogin
				'username': jQuery('.login_box #log').val(),
				'password': jQuery('.login_box #pwd').val(),
				'security': jQuery('.login_box #security').val()
			},
			success : function (data) {
				jQuery('.login_box .status_login').text(data.message);
				if (data.loggedin == true) {
					document.location.href = ajax_login_object.redirecturl;
				} else {
					jQuery('#error_auth').show(100);
				}
			}
		});
		e.preventDefault();
	});
}

jQuery(window).on('load', function () {
	jQuery('.decomments-pictures-holder').find('img[src$=".gif"]').addClass('gifimg');
	jQuery('.gifimg').parents('.thickbox').addClass('decomments-gif');

	if (jQuery('.decomments-comment-body').length <= 0 && jQuery('#decomments-form-add-comment').length > 0) {
		decom.getSortComments('older');
		decom.console_log('comments load ...');
	}

	jQuery(document).on('click', '.decomments-date-link', function (e) {
		e.preventDefault();
		var url = jQuery(this).attr('href');
		var destination = jQuery(this).offset().top - 100;
		window.history.pushState("object or string", "Title", url);
		jQuery('body,html').animate({scrollTop: destination}, 400);
	});


	jQuery(document).on('click', '.de-select dt', function () {
		var parent = jQuery(this).parent();
		jQuery(document).find('.de-select').find('dt').removeClass('active');
		jQuery(this).toggleClass('active');
	});

	jQuery(document).on('click', '#decomments-login-form-another', function () {
		decom.showEnterForm();
	});

	jQuery(document).on('click', '.de-select dd a', function () {
		var parent = jQuery(this).parent().parent(),
			trigger = parent.find('dt');
		if (!jQuery(this).hasClass('current')) {
			parent.find('a').removeClass('current');
			jQuery(this).addClass('current');
			trigger.removeClass('active').html(jQuery(this).text());

		}

		return false;
	});


	jQuery(document).on('click', function (e) {
		if (!jQuery(e.target).parents().hasClass('de-select') && !jQuery(e.target).hasClass('de-select') && !jQuery(e.target).parents().hasClass('de-select-tab') && !jQuery(e.target).hasClass('de-select-tab')) {
			jQuery('.de-select dt, .de-select-tab dt').removeClass('active');
		}
	});

	if (jQuery('img.svg').length) {
		jQuery('img.svg').each(function () {
			var $img = jQuery(this);
			var imgID = $img.attr('id');
			var imgClass = $img.attr('class');
			var imgURL = $img.attr('src');
			var imgWidth = $img.attr('width');
			var imgHeight = $img.attr('height');

			jQuery.get(imgURL, function (data) {
				// Get the SVG tag, ignore the rest
				var $svg = jQuery(data).find('svg');

				// Add replaced image's ID to the new SVG
				if (typeof imgID !== 'undefined') {
					$svg = $svg.attr('id', imgID);
				}
				// Add replaced image's classes to the new SVG
				if (typeof imgClass !== 'undefined') {
					$svg = $svg.attr('class', imgClass + ' replaced-svg');
				}
				if (typeof imgWidth !== 'undefined') {
					$svg = $svg.attr('width', imgWidth);
				}
				if (typeof imgHeight !== 'undefined') {
					$svg = $svg.attr('height', imgHeight);
				}

				// Remove any invalid XML tags as per http://validator.w3.org
				$svg = $svg.removeAttr('xmlns:a');

				// Replace image with new SVG
				$img.replaceWith($svg);
			}, 'xml');
		});
	}
});

var decom = {
	cfocus                   : false,
	debug_enable             : false,
	site_url                 : '',
	admin_ajax               : '',
	post_id                  : 0,
	user_id                  : 0,
	is_need_logged           : 0,
	lang                     : false,
	text_lang_comment_deleted: '',
	text_lang_edited         : '',
	text_lang_delete         : '',
	text_lang_not_zero       : '',
	text_lang_required       : '',
	text_lang_checked        : '',
	text_lang_completed      : '',
	text_lang_items_deleted  : '',
	text_lang_close          : '',
	text_lang_loading        : '',
	multiple_vote            : 0,


	console_log                 : function (a) {
		if (decom.debug_enable) {
			console.log(a);
		}
	},
	getURLParameter             : function (name) {
		return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search) || [, ""])[1].replace(/\+/g, '%20')) || null
	},
	focuss                      : function () {
		jQuery('.decomments-addform-body').find('textarea').focus(function () {
			jQuery(this).parents('.decomments-addform-body').addClass('tfocus');
		}).blur(function () {
			jQuery(this).parents('.decomments-addform-body').removeClass('tfocus');
		});
	},
	labels                      : function () {
		decom.get_data();
		if (jQuery('.de-form-field').length) {
			jQuery('.de-form-field').each(function () {
				var el = jQuery(this);

				if (el.find('input').val() !== '') {
					el.addClass('de-field-complete');
				}
			});
			jQuery('.de-form-field').find('input').focus(function () {
				var el = jQuery(this);

				if (el.val() === '') {
					el.parent().addClass('de-field-focus');
				}
			}).blur(function () {
				var el = jQuery(this);
				if (el.val() === '') {
					el.parent().removeClass('de-field-focus').removeClass('de-field-complete');
				}
			});
		}
	},
	liveFunctions               : function () {
		jQuery(document).on('click', '.decomments-dislike-link', function () {
			decom.votes(this);
		});
		jQuery(document).on('click', '.decomments-like-link', function () {
			decom.votes(this)
		});
		jQuery(document).on('click', '.decomments-tw-link', function () {
			decom.tweet(this);
			return false;
		});
		jQuery(document).on('click', '.decomments-fb-link', function () {
			decom.facebook(this);
			return false;
		});
		jQuery(document).on('click', '.decomments-vk-link', function () {
			decom.vkontakte(this);
			return false;
		});
		jQuery(document).on('click', '.decomments-gp-link', function () {
			decom.google(this);
			return false;
		});
		jQuery(document).on('click', '.decomments-ln-link', function () {
			decom.linkedin(this);
			return false;
		});

		jQuery(document).on('click', '.decomments-oc-link', function () {
			decom.od(this);
			return false;
		});
		jQuery(document).on('click', '.decomments-checkbox', function () {
			decom.checkSubscribe(this);
		});
		jQuery(document).on('click', '.decomments-button-quote-send', function () {
			decom.insertQuote();
		});
		jQuery(document).on('click', '.decomments-button-quote-cancel', function () {
			decom.closeQuote();
		});
		jQuery(document).on('click', '.decomments-button-addfile-send', function () {
			decom.insertPicture(this);
		});
		jQuery(document).on('click', '.decomments-button-addfile-cancel', function () {
			decom.closePicture();
		});
		jQuery(document).on('click', '.decomments-button-send', function () {
			var obj = jQuery(this);
			var is_edit_comment = obj.data('edit');
			if (!is_edit_comment) {
				if (document.getElementById('decom-mail-author')) {
					decom.verifyEmail(obj);
				} else {
					decom.sendComment(obj);
				}
			}
		});
		jQuery(document).on('click', '.decomments-button-reply', function () {
			decom.replyComment(this);
		});
		jQuery(document).on('click', '.decomments-button-cancel', function () {
			decom.cancelComment(this);
		});
		jQuery(document).on('click', '.decomments-button-delete', function () {
			decom.deleteComment(this);
		});
		jQuery(document).on('click', '.decomments-button-edit', function () {
			decom.editComment(this);
		});

		jQuery(document).on('click', '#decomments-show-loginform', function () {
			decom.showLoginForm(this);
			return false;
		});
		jQuery(document).on('click', '#decomments-show-enterform', function () {
			decom.showEnterForm(this);
			return false;
		});
		jQuery(document).on('submit', '#decomments-loginform', function () {
			decom.sendAuthorizationForm(this);
			return false;
		});
		jQuery(document).on('click', function (e) {
			if (jQuery(e.target).hasClass('decomments-buttons-moderate')) {
				var th = jQuery(e.target),
					a = th;
				jQuery('.decomments-buttons-moderate').removeClass('active');
				a.toggleClass('active');
			}
			else if (jQuery(e.target).parents('a').hasClass('decomments-buttons-moderate')) {
				var th = jQuery(e.target).parents('a'),
					a = th;
				jQuery('.decomments-buttons-moderate').removeClass('active');
				a.toggleClass('active');
			}

			else if (!jQuery(e.target).parents().hasClass('moderate-action')) {
				jQuery('.decomments-buttons-moderate').removeClass('active');
			}
		});
		jQuery(document).on('click', '.moderate-action a', function (e) {
			e.preventDefault();
			if (jQuery(this).hasClass('decomments-link-edit')) {
				decom.editComment(this);
				jQuery(this).parent().toggleClass('active');
			} else if (jQuery(this).hasClass('decomments-link-remove-all-comments-user')) {
				decom.deleteAllUserComments(this);
				jQuery(this).parent().toggleClass('active');
			} else if (jQuery(this).hasClass('decomments-link-block-user')) {
				decom.blockUserComments(this);
				jQuery(this).parent().toggleClass('active');
			} else if (jQuery(this).hasClass('decomments-link-unblock-user')) {
				decom.blockUserComments(this);
				jQuery(this).parent().toggleClass('active');
			} else {
				decom.deleteComment(this);
				jQuery(this).parent().toggleClass('active');
			}
		});
		jQuery(document).on('click', '#comment-nav-below .decomments-nav-next', function (e) {
			decom.ajaxPaginate(this);
			return false;
		});
		jQuery(document).on('click', '#comment-nav-below .decomments-nav-previous', function (e) {
			decom.ajaxPaginate(this);
			return false;
		});
		jQuery(document).on('click', '.decom-cancel', function () {
			decom.cancel()
		});
		jQuery(document).on('click', '.decom-manual-authorization', function () {
			decom_manual_authorization()
		});
	},
	cancel                      : function () {
		decom.get_data();
		jQuery('.decom-add-holder textarea').val('');
		jQuery('.decom-message-error').html('');
		jQuery('.decom-add-picture').removeClass('decom-file-uploaded');
		jQuery('.decom-pictures-holder').removeClass('decom_action_delete_image');
		jQuery('.decom-pictures-holder').show();
	},
	getParentObj                : function (item) {
		return jQuery(item).parents('[id^=comment-]');
	},
	getId                       : function (val) {
		if (typeof val != 'undefined') {
			var arr = val.split('-');
			return arr[arr.length - 1];
		}
	},
	writeAlert                  : function (response) {
		var body = response.replace(/^[\S\s]*<body[^>]*?>/i, "").replace(/<\/body[\S\s]*$/i, "");
		body = jQuery(body);
		if (decom.is_need_logged) {
			decom.showLoginForm();
		}
		jQuery('.decomments-commentform-message span').html(body.html());
		jQuery('.decomments-commentform-message').slideDown();
	},
	writeResponse               : function (response) {
		var result = jQuery(response),
			response_id = result.attr('id'),
			id = decom.getId(response_id),
			pId = result.attr('parent-id'),
			form = jQuery('#decomments-form-add-comment'),
			edition = form.attr('data-edit'),
			adit_id = form.attr('data-edit-id');
		if (edition === 'true') {

		} else {
			if (pId != '0') {
				var postParent = jQuery('#comment-' + pId),
					reply = postParent.children('.decomments-comment-reply');

				if (!reply.attr('class')) {
					postParent.append('<div class="decomments-comment-reply"></div>');
					reply = postParent.children('.decomments-comment-reply');
				}
				reply.append(result);
			}
			else {
				if (!jQuery('.decomments-comment-section').children('.decomments-comment-list').attr('class')) {
					jQuery('.decomments-comment-section').append('<div class="decomments-comment-list"></div>');
				}

				var sort = jQuery('.decom_comments_sort select').val();
				switch (sort) {
					case 'newer':
						var prev = jQuery('.decom-nav-previous').html();
						if (prev == '' || typeof(prev) == 'undefined') {
							var rated = jQuery('.decomments-comment-list').find('.decom-top-rated').length;
							if (rated) {
								jQuery('.decomments-top-rated').eq(rated - 1).parents('.decom-comment-block').after(result);
							}
							else {
								jQuery('.decomments-comment-list').prepend(result);
							}
							decom.scrollToId(response_id, 500);
						}
						else {
							decom.ajaxPaginate(id, 'beginning');
						}
						break;
					case 'older':
						var next = jQuery('.decom-nav-next').html();
						if (next == '' || typeof(next) == 'undefined') {
							jQuery('.decomments-comment-list').append(result);
							decom.scrollToId(response_id, 500);
						}
						else {
							decom.ajaxPaginate(id, 'end');
						}
						break;
					case 'rate':
					default:
						jQuery('.decomments-comment-list').append(result);
						decom.scrollToId(response_id, 800);
						break;
				}
			}
			jQuery('.decomments-addform-body textarea').val('');
			jQuery('#decom_default_position_form_add').after(form);
		}
		decom.onChangeTimeCounterExpired(id);
		decom.setCommentsNumber(1);
		decom.recalculationCommentsNumbers(id, 1);
	},
	scrollToId                  : function (id, speed) {
		// speed = 500
		jQuery('body').animate({scrollTop: jQuery('#' + id).offset().top - 250}, speed);
	},
	editComment                 : function (self) {
		decom.get_data();
		var obj = jQuery(self).parents('.decomments-comment-block'),
			id = decom.getId(obj.attr('id')),
			comid = jQuery(self).parents('.comment').attr('id'),
			form = jQuery('#decomments-form-add-comment'),
			where_adds = obj.find('.decomments-description-block').eq(0),
			comment_block = jQuery('#comment-' + id),
			comment_block_body = where_adds.find('.decomments-comment-main'),
			text_height = comment_block_body.find('.decomments-text-holder').height(),
			picture_holder = comment_block_body.find('.decomments-pictures-holder'),
			clone_save_comment_block_body = comment_block_body.clone(),
			wa = jQuery('#' + comid).find('.decomments-description-block').eq(0).find('.decomments-comment-main'),
			block_content = wa.find('.decomments-text-holder'),
			txt = block_content.html(),
			button_name = form.find('.decomments-button-send').html();
		jQuery('.decomments-button-cancel').trigger('click');
		jQuery('.decomments-commentform-message span').html('');
		jQuery('.decomments-commentform-message').slideUp();
		wa.parent().attr('data-html-f', wa.html()).addClass('decomments-comment-edit-codition').append(form);
		form.find('textarea').addClass('active');
		jQuery('.decomments-addform-body').find('.decomments-loading').addClass('active');
		form.addClass('decomments-form-edit').find('textarea').val('');
		form.find('.decomments-button-send').hide();
		jQuery.ajax({
			type    : "POST",
			url     : decom.admin_ajax + '?lang=' + decom.lang,
			dataType: "json",
			data    : "action=decom_get_comment&comment_id=" + id,
			success : function (result) {
				decom.console_log(result);
				if (result.content) {
					decom.console_log(comid);
					jQuery('.decomments-addform-body').find('textarea').removeAttr('disabled', 'disabled');
					jQuery('.decomments-addform-body').find('.decomments-loading').removeClass('active');

					//form.addClass('decomments-form-edit').find('textarea').val(txt);
					form.addClass('decomments-form-edit').find('textarea').val(result.content);

					form.find('.decomments-button-send').addClass('decomments-button-save').removeClass('decomments-button-send').html(result.name_button).attr('data-edit', 'true').attr('onclick', 'decom.saveEditComment(this)').attr('data-m-n', button_name);
					form.find('.decomments-button-save').show();
					form.attr('data-edit', 'true');
					form.attr('data-edit-id', comid);
					form.find('textarea').focus();
				}
			}
		});

		if (picture_holder.length) {
			form.find('textarea').after(picture_holder);
			picture_holder.append('<a href="javascript:void(0);" onclick="decom.deletePicture(this);" class="decomments-delete-image"><i class="decomments-icon-trash-o"></i> ' + decom.text_lang_delete + '</a>')
		}

		jQuery('body,html').stop().animate({scrollTop: jQuery('#' + comid).offset().top - 200}, 400);
		jQuery(document).on('click', '#decomments-form-add-comment .decomments-button-cancel', function () {
			var comment_block_body_holder = where_adds.find('.decomments-title-block');
			wa.parent().removeClass('decomments-comment-edit-codition');
			var content_prev = wa.parent().attr('data-html-f');
			if (content_prev) {
				wa.html(wa.parent().attr('data-html-f'));
			}
			form.find('.decomments-button-save').addClass('decomments-button-send').removeClass('decomments-button-save').html(button_name).attr('data-edit', 'false').removeAttr('onclick');

			form.removeAttr('data-edit');
			form.removeAttr('data-edit-id');

			if (form.find('.decomments-pictures-holder').length) {
				form.find('.decomments-pictures-holder').remove();
			}
			form.find('textarea').removeAttr('style');
			jQuery('body,html').stop().animate({scrollTop: jQuery('#' + comid).offset().top - 200}, 400);
			clone_save_comment_block_body = undefined;
			return false;
		});

		return false;
	},
	saveEditComment             : function (item) {
		decom.get_data();
		var form = jQuery(item).parent();
		var button = jQuery(item);
		var id = form.parent().data('edit-id');
		var obj = jQuery(item).parents('.decomments-comment-block'),
			id = decom.getId(obj.attr('id')),
			wa = jQuery('#comment-' + id).find('.decomments-description-block').eq(0).find('.decomments-comment-main');

		form.find('textarea').attr('disabled', 'disabled');
		var edited_content = encodeURIComponent(form.find('textarea').val());

		jQuery('.decomments-addform-body').find('.decomments-loading').addClass('active');
		jQuery.ajax({
			type    : "POST",
			url     : decom.admin_ajax + '?lang=' + decom.lang,
			dataType: "json",
			data    : "action=decom_save_edit_comment&comment_id=" + id + "&content=" + edited_content,
			success : function (result) {
				jQuery('.decomments-addform-body').find('.decomments-loading').removeClass('active');
				if (result.result == 'success') {
					decom.cancelComment(item);
					wa.parent().removeClass('decomments-comment-edit-codition');
					wa.parent().attr('data-html-f', '');
					form.find('.decomments-button-save').addClass('decomments-button-send').removeClass('decomments-button-save').html(button.data('m-n'));
					button.removeAttr('data-m-n');
					form.removeAttr('data-edit');
					form.removeAttr('data-edit-id');
					if (form.find('.decomments-pictures-holder').length) {
						form.find('.decomments-pictures-holder').remove();
					}
					form.find('textarea').removeAttr('style');
					form.find('textarea').removeAttr('disabled');
					jQuery('.decomments-button-save').off();
					jQuery('body,html').stop().animate({scrollTop: jQuery('#comment-' + id).offset().top - 200}, 400);
					jQuery('.decomments-content-' + id).html(result.content);
				}
			}
		});
	},
	deleteAllUserComments       : function (item) {
		decom.get_data();
		var user_id = jQuery(item).data('user-id');
		var user_email = jQuery(item).data('user-email');
		var del_parent = jQuery(item).parents('.decomments-description-block');
		var id = decom.getId(decom.getParentObj(item).attr('id'));
		del_parent.css({'opacity': '.3'});
		jQuery.ajax({
			type    : "POST",
			url     : decom.admin_ajax + '?lang=' + decom.lang,
			dataType: "json",
			data    : "action=delete_all_user_comment&user_id=" + user_id + "&user_email=" + user_email,
			success : function (result) {
				console.log(result);
				if (result.result == 'success') {
					jQuery(result.id_blocks).each(function () {
						var obj = jQuery(this);
						var del_comment_user = obj.find('.decomments-description-block');
						obj.addClass('trash');
						del_comment_user.find('.decomments-text-holder').addClass('decomments-trash-holder').html('<p>Comments User removed</p>');
						del_comment_user.find('.decomments-comment-body-nav').remove();
						del_comment_user.find('.decomments-footer-nav').remove();
						del_comment_user.find('.decomments-pictures-holder').remove();
						del_comment_user.find('.decomments-comment-social-part').remove();
						decom.setCommentsNumber(-1);
						decom.recalculationCommentsNumbers(id, -1);
					});
				}
				del_parent.css({'opacity': '1'});
			}
		});
	},
	blockUserComments           : function (item) {
		decom.get_data();
		var user_id = jQuery(item).data('user-id');
		var user_email = jQuery(item).data('user-email');
		var user_action = jQuery(item).data('user-action');
		var del_parent = jQuery(item).parents('.decomments-description-block');
		var id = decom.getId(decom.getParentObj(item).attr('id'));
		del_parent.css({'opacity': '.3'});
		jQuery.ajax({
			type    : "POST",
			url     : decom.admin_ajax + '?lang=' + decom.lang,
			dataType: "json",
			data    : "action=deco_block_user&user_id=" + user_id + "&user_email=" + user_email + "&user_action=" + user_action,
			success : function (result) {
				console.log(result);
				if (result.result == 'success') {
					jQuery(result.id_blocks).each(function () {
						var obj = jQuery(this);
						var del_comment_user = obj.find('.decomments-description-block');
						obj.addClass('trash');
						if (user_action == 'block') {
							del_parent.find('.decomments-text-holder').addClass('decomments-trash-holder').html('<p>User blocked</p>');
						} else {
							del_parent.find('.decomments-text-holder').addClass('decomments-trash-holder').html('<p>User unblocked</p>');
						}
						del_comment_user.find('.decomments-comment-body-nav').remove();
						del_comment_user.find('.decomments-vote').remove();
						del_comment_user.find('.decomments-button-reply').remove();
						del_comment_user.find('.decomments-share-block').remove();
						//del_comment_user.find('.decomments-footer-nav').remove();
						del_comment_user.find('.decomments-pictures-holder').remove();
						del_comment_user.find('.decomments-comment-social-part').remove();
						decom.setCommentsNumber(-1);
						decom.recalculationCommentsNumbers(id, -1);
					});
				}
				del_parent.css({'opacity': '1'});
			}
		});
	},
	removeDuplicateComments     : function (comments) {
		var currentCommentList = new Array();
		var newComments = new Array();

		jQuery('.decomments-comment-list .comment').each(function () {
			currentCommentList.push(jQuery(this).attr('id'));
		});

		jQuery(comments + ' .comments').each(function (i, val) {
			var newComment = jQuery(this).attr('id');
			if (jQuery.inArray(newComment, currentCommentList) == -1) {
				newComments.push(this);
			}
		});

		return newComments;
	},
	paginate                    : function () {
		jQuery(document).on('click', '.decomments-ajax-paginate a', function () {
			var el = jQuery(this),
				p = el.parents('.decomments-comment-section'),
				holder = p.find('.decomments-comment-list'),
				paginate = el.parents('.decomments-ajax-paginate'),
				maxpage = parseInt(paginate.attr('data-page-count')),
				curpage = parseInt(paginate.attr('data-cur-page')),
				nextpage = maxpage == curpage ? 0 : curpage + 1,
				perpage = parseInt(paginate.attr('data-comments-perpage')),
				loader = p.find('.decomments-paginate-loader'),
				pages_count = parseInt(paginate.attr('pages-count')),
				page = parseInt(paginate.attr('page'));
			el.hide();
			loader.show();
			if (nextpage !== 0) {
				jQuery.post(decom.admin_ajax + '?lang=' + decom.lang + '&action=decom_comments&f=get_comments_by_paginate', {
						decom_ajax: 1,
						post_id   : decom.post_id,
						per_page  : perpage,
						page_num  : nextpage
					},
					function (data) {
						var d = data;
						var fid = jQuery(d).eq(0).attr('id');
						holder.append(d);
						var com = jQuery(data).eq(0);
						var id = decom.getId(com.attr('id'));
						decom.recalculationCommentsNumbers(id, 1);
						if (nextpage != maxpage) {
							paginate.attr('data-cur-page', nextpage);
							loader.hide();
							el.show();
						}
						if (nextpage == maxpage) {
							loader.hide();
							el.hide();
						}
					});
			}
			return false;
		});
		jQuery(document).on('click', '.decomments-ajax-paginate-lazy a', function () {
			var el = jQuery(this),
				p = el.parents('.decomments-comment-section'),
				holder = p.find('.decomments-comment-list'),
				paginate = el.parents('.decomments-ajax-paginate-lazy'),
				maxpage = parseInt(paginate.attr('data-page-count')),
				curpage = parseInt(paginate.attr('data-cur-page')),
				nextpage = maxpage == curpage ? 0 : curpage + 1,
				perpage = parseInt(paginate.attr('data-comments-perpage')),
				loader = p.find('.decomments-paginate-loader'),
				pages_count = parseInt(paginate.attr('pages-count')),
				page = parseInt(paginate.attr('page'));

			el.hide();
			loader.show();
			paginate.addClass('loading');

			if (nextpage !== 0) {

				jQuery.post(decom.admin_ajax + '?lang=' + decom.lang + '&action=decom_comments&f=get_comments_by_paginate', {
						decom_ajax: 1,
						post_id   : decom.post_id,
						per_page  : perpage,
						page_num  : nextpage
					},
					function (data) {

						var d = data;
						var fid = jQuery(d).eq(0).attr('id');

						holder.append(d);

						var com = jQuery(data).eq(0);
						var id = decom.getId(com.attr('id'));

						decom.recalculationCommentsNumbers(id, 1);

						if (nextpage != maxpage) {
							paginate.attr('data-cur-page', nextpage);
							loader.hide();
							el.show();
						}

						if (nextpage == maxpage) {
							loader.hide();
							el.hide();
						}
						paginate.removeClass('loading');

					});
			}
			return false;
		});

		jQuery(document).on('scroll', jQuery(window), function () {

			if (jQuery('.decomments-ajax-paginate-lazy').length) {

				var dapl = jQuery('.decomments-ajax-paginate-lazy'),
					wst = jQuery(window).scrollTop(),
					wh = jQuery(window).height(),
					listtop = jQuery('.decomments-comment-list').offset().top,
					listheight = jQuery('.decomments-comment-list').height(),
					listend = listtop + listheight - wh;

				if (wst > listend) {

					if (!dapl.find('a').is(':hidden')) {
						dapl.find('a').trigger('click');
					}
				}
			}
		});
	},
	ajaxPaginate                : function ($this, fast, page) {
		var link, action;
		if (typeof fast == 'undefined') {
			action = jQuery($this).attr('class');
			action = action.split('-');
			action = action[action.length - 1];

			var comBlock = jQuery('#comment-nav-below');

			var old_num_id_lb = jQuery('.decom_dop_bloc').find('.comment:last'),
				old_num_id_fb = jQuery('.decom_dop_bloc').find('.comment:first'),
				old_num_up = decom.getId(old_num_id_fb.attr('id')),
				old_num_down = decom.getId(old_num_id_lb.attr('id'));

			decom.console_log(old_num_up);
			decom.console_log(old_num_down);


			jQuery('.decom_dop_bloc').empty().prepend('<span class="decomments-loader"><div class="loader-ball-scale"><div></div><div></div><div></div></div></span>');
			link = jQuery($this).parent('a').attr('href');
			var cur_page = comBlock.attr('page');
		}
		else {
			action = fast;
		}

		var url = decom.admin_ajax + '?action=decom_comments&f=ajax_paginate';
		var data = {cur_page: cur_page, post_id: decom.post_id, actions: action};
		if (typeof fast != 'undefined') {
			jQuery.extend(data, {comment_id: $this});
		}

		jQuery.post(url, data, function (result) {

			jQuery('.decom_dop_bloc').empty();
			jQuery('.decom_dop_bloc').html(jQuery(result).html());

			var comBlock_new = jQuery('#comment-nav-below'),
				new_cur_page = comBlock_new.attr('page');


			if (new_cur_page > cur_page) {
				decom.recalculationCommentsNumbers(old_num_down, 1);
			}

			if (new_cur_page < cur_page) {
				decom.recalculationCommentsNumbers(old_num_up, 1);
			}
			decom.console_log(action);
			if (typeof link == 'undefined' && action == 'end') {
				link = jQuery('#fast-comment-link').attr('href');
			}
			else if (typeof link == 'undefined') {
				link = jQuery('#decom_cur_page').attr('href');
			}

			if (link != window.location) {
				window.history.pushState(null, null, link);
			}

			if (action == 'end' || action == 'beginning') {
				jQuery('body,html').stop().animate({scrollTop: jQuery('.decomments-head').offset().top - 100}, 400);
			}
			else {
				jQuery('body,html').stop().animate({scrollTop: jQuery('.decomments-head').offset().top - 100}, 400);
			}
		});
		return false;

	},
	sendComment                 : function (obj) {
		decom.get_data();
		jQuery('.decomments-addform-body .decomments-loading').addClass('active');
		var decom_comment = jQuery('#decomments-form-add-comment textarea').val(),
			decom_comment = decom.quoteShortcodeDecode(decom_comment),
			subscribe_all_comments = decom.validateSubscribe('subscribe_all_comments'),
			subscribe_my_comment = decom.validateSubscribe('subscribe_my_comment'),
			parentId = 0,
			parentObject = obj.parents('[id^=comment-]'),
			parentLength = parentObject.length,
			social_icon = jQuery('#decomments-social-icon').val(),
			errorbox = jQuery('.decomments-commentform-message'),
			addFileForm = jQuery('#decomments-add-picture-form'),
			superform = jQuery('#decomments-form-add-comment');

		if (!globalAddPicture) {
			jQuery('#decomments-add-picture-form input').val('');
		}
		globalAddPicture = false;

		if (parentLength) {
			parentId = decom.getId(parentObject.attr('id'));
			if (parentLength > 4) {
				jQuery(parentObject[0]).addClass('depth-disabled');
			}
		}

		decom_comment2 = decom_comment.replace(/\n/g, "");
		if (decom_comment2.length == 0 && jQuery('.decomments-add-image').eq(0).hasClass('decomments-file-uploaded')) {
			var unique = new Date().getTime();
			decom_comment = '[decom_attached_image_' + unique + ']';
		}

		var url = jQuery('#decom-site-author').length ? jQuery('#decom-site-author').val() : '';
		var author = jQuery('#decom-name-author').val();
		var email = jQuery('#decom-mail-author').val();
		var data = '';

		data = 'user_id=' + decom.user_id;
		data += '&comment_post_ID=' + decom.post_id;
		data += '&author=' + author;
		data += '&email=' + email;
		data += '&comment=' + decom_comment;
		data += '&url=' + url;
		data += '&_wp_unfiltered_html_comment=' + jQuery("#_wp_unfiltered_html_comment_disabled").val();
		data += '&comment_parent=' + parentId;
		data += '&subscribe_all_comments=' + subscribe_all_comments;
		data += '&subscribe_my_comment=' + subscribe_my_comment;
		data += '&social_icon=' + social_icon;

		decom.console_log(decom.site_url);

		jQuery.ajax({
			url     : decom.site_url + "/wp-comments-post.php",
			global  : false,
			type    : "POST",
			dataType: "html",
			data    : ({
				user_id                    : decom.user_id,
				comment_post_ID            : decom.post_id,
				author                     : jQuery('#decom-name-author').val(),
				email                      : jQuery('#decom-mail-author').val(),
				comment                    : decom_comment,
				url                        : jQuery('#decom-site-author').length ? jQuery('#decom-site-author').val() : '',
				_wp_unfiltered_html_comment: jQuery('#_wp_unfiltered_html_comment_disabled').val(),
				comment_parent             : parentId,
				subscribe_all_comments     : subscribe_all_comments,
				subscribe_my_comment       : subscribe_my_comment,
				social_icon                : social_icon,
				image_name                 : jQuery('#pic-name').val(),
				image_base64               : jQuery('#pic-src').val()
			}),
			success : function (response) {
				decom.console_log(response);
				jQuery('#pic-name').remove();
				jQuery('#pic-src').remove();
				jQuery('.decomments-add-image').removeAttr('data-img');
				jQuery('.decomments-add-image').removeClass('decomments-file-uploaded');
				jQuery('.decomments-addform-body .decomments-loading').removeClass('active');
				var is_error_comment_check_fields = false;
				try {
					var response = JSON.parse(response);
				}
				catch (e) {
					// is message error parse wp-comments-post
					is_error_comment_check_fields = true;
// if not message error, find system message comment error
					if (jQuery(response).find('.decomments-comment-body').length == 0) {
						var body = response.replace(/^[\S\s]*<body[^>]*?>/i, "").replace(/<\/body[\S\s]*$/i, "");
						body = jQuery(body);
						var error_message_str = body.html();
						errorbox.find('span').html(error_message_str);
						errorbox.slideDown();
						if (decom.is_need_logged) {
							decom.showLoginForm();
						}
					} else {
						errorbox.slideUp(500, function () {
							errorbox.find('span').html('Error! Please try again later');
						});

						if (superform.attr('data-edit') === 'true') {

						} else {
							decom.writeResponse(response);
						}
					}
				}


				if (is_error_comment_check_fields) { // is error fill fields
					jQuery('.decomments-add-image').removeClass('decomments-file-uploaded');
				} else { // comment ok check and post
					jQuery('.decomments-addform-body .decomments-loading').removeClass('active');
					var error = response.message;
					if (response.error == 'moderation') {
						error = '<em>' + error + '</em>';
						jQuery('.decomments-addform-body textarea').val('');
					}
					errorbox.find('span').html(error);
					errorbox.slideDown();
					jQuery('.decomments-add-image').removeClass('decomments-file-uploaded');
					setTimeout(function () {
						decom.cancel();
					}, 500);
				}
			},
			error   : function (XMLHttpRequest, textStatus, errorThrown) {
				jQuery('.decomments-addform-body').find('.decomments-loading').removeClass('active');
				decom.writeAlert(XMLHttpRequest.responseText);
			}
		});

	},
	cancelComment               : function (item) {
		decom.get_data();
		jQuery('.decomments-commentform-message span').html('');
		jQuery('.decomments-commentform-message').slideUp();
		jQuery('.decomments-add-image').removeClass('decomments-file-uploaded');

		var form = jQuery('#decomments-form-add-comment'),
			where_add = jQuery('#decom_default_position_form_add');

		where_add.after(form);

		form.find('textarea').val('');

		if (form.hasClass('decomments-form-edit')) {

			form.removeClass('decomments-form-edit');

		} else {

			form.removeClass('decomments-form-reply');
		}

	},
	replyComment                : function (item) {
		decom.get_data();
		if (jQuery('.decomments-comment-edit-codition').length) {
			jQuery(document).find('#decomments-form-add-comment .decomments-button-cancel').trigger('click');
		}

		jQuery('.decomments-commentform-message span').html('');
		jQuery('.decomments-commentform-message').slideUp();
		jQuery('.decomments-add-image').removeClass('decomments-file-uploaded');

		var form = jQuery('#decomments-form-add-comment'),
			scrolldest = jQuery(item).parents('.decomments-comment-block'),
			where_add = jQuery(item).parents('.decomments-description-block');

		where_add.append(form);
		form.addClass('decomments-form-reply').find('textarea').focus();

		//jQuery('html,body').stop().animate({scrollTop: scrolldest.offset().top - 100}, 600);
	},
	deleteComment               : function (item) {
		decom.get_data();
		var del_parent = jQuery(item).parents('.decomments-description-block'),
			id = decom.getId(decom.getParentObj(item).attr('id')),
			url = decom.admin_ajax + '?lang=' + decom.lang + '&action=decom_comments&f=delete_comment';
		del_parent.css({'opacity': '.3'});
		jQuery.post(url, {id: id}, function (result) {
			if (result.result == 'success') {
				if (result.result == 'success') {
					jQuery('#comment-' + id).addClass('trash');
					del_parent.find('.decomments-text-holder').addClass('decomments-trash-holder').html('<p>' + jQuery("#decomments-comment-section").data("text_lang_comment_deleted") + '</p>');
					del_parent.find('.decomments-comment-body-nav').remove();
					del_parent.find('.decomments-footer-nav').remove();
					del_parent.find('.decomments-pictures-holder').remove();
					del_parent.find('.decomments-comment-social-part').remove();
					del_parent.css({'opacity': '1'});
					decom.setCommentsNumber(-1);
					decom.recalculationCommentsNumbers(id, -1);
				}
			}
		});
	},
	sortComments                : function (self) {
		decom.get_data();
		decom.getSortComments(jQuery(self).attr('data-sort'));
		return false;
	},
	getSortComments             : function (sort) {
		decom.get_data();
		var decom_list_height = jQuery('.decomments-comment-list').height();

		decom.console_log(sort);

		jQuery('.decomments-comment-list').attr('style', 'min-height:' + decom_list_height + 'px;').empty().addClass('decomments-loading').html('<div class="loader-ball-scale lbs-remove"><div></div><div></div><div></div></div>');

		var url = decom.admin_ajax + '?lang=' + decom.lang + '&action=decom_comments&f=sort_comments';
		var data = {decom_comments_sort: sort, post_id: decom.post_id};
		jQuery.post(url, data, function (result) {
			if (result) {
				var content_html = jQuery('<div>' + result + '</div>').find('.decomments-comment-list').html();
				jQuery('.decomments-comment-list').html(content_html);
				jQuery('.decomments-comment-list').removeClass('decomments-loading').removeAttr('style')
				;
				var pathname = window.location.pathname;
				var prefUrl = jQuery('#decom_cur_page').attr('href');
				var url = decom.site_url + prefUrl;
				if (prefUrl) {
					if (url != window.location) {
						window.history.pushState(null, null, url);
					}
				}
				if (sort == 'rate') {
					decom.bestComments();
				}
			}
		});
	},
	bestComments                : function () {
		decom.get_data();
		if (typeof(comment_id_max) != 'undefined') {
			var best_comment = jQuery('#comment-' + comment_id_max);
			jQuery('.decomments-comment-list').prepend(best_comment);
			best_comment.find('.decomments-comment-body').addClass('decomments-best-comments').removeAttr('style');

			if (typeof(comment_id_max_second) != 'undefined') {
				var best_second_comment = jQuery('#comment-' + comment_id_max_second);
				jQuery(best_comment).after(best_second_comment);
				best_second_comment.find('.decomments-comment-body').addClass('decomments-best-comments').removeAttr('style');
			}
		}
	},
	verifyEmail                 : function (obj) {
		jQuery.post(decom.admin_ajax + '?lang=' + decom.lang + '&action=decom_comments&f=verify_email',
			{
				email: jQuery("#decom-mail-author").val()
			}, function (data) {
				console.log(data);
				if (data.result == 'error') {
					jQuery('#decomments-login-form-message').text(data.error);
					jQuery('.decomments-enterform-message').slideDown();
					jQuery('#decomments-enterform').find('fieldset').slideUp();
				} else {
					decom.sendComment(obj);
				}
			});
	},
	showLoginForm               : function (item) {
		jQuery('.decomments-enterform-message').slideUp();
		jQuery('#decomments-enterform').find('fieldset').slideUp();
		jQuery('#decomments-loginform').slideDown();
	},
	showEnterForm               : function (item) {
		jQuery('.decomments-enterform-message').slideUp();
		jQuery('#decomments-loginform').slideUp();
		jQuery('#decomments-enterform').find('fieldset').slideDown();
	},
	authAndRedirect             : function () {
		var parentId = '',
			pId = jQuery('#decomments-form-add-comment').parents('[id^=comment-]').attr('id'),
			redirect_to = jQuery(location).attr('href'),
			redirect_to = redirect_to.split('#')[0];
		if (pId) {
			parentId = '#' + pId;
		} else {
			parentId = '#decomments-form-add-comment';
		}

		//	@Todo url encode
		var uri = encodeURIComponent(redirect_to + parentId);
		return redirect_to + parentId;
	},
	sendAuthorizationForm       : function () {
		var log = jQuery('[name=log]').val(),
			pwd = jQuery('[name=pwd]').val(),
			errorbox = jQuery('.decomments-loginform-message'),
			title = jQuery('title').html(),
			redirect_to = decom.authAndRedirect();

		jQuery.post(decom.site_url + "/wp-login.php", {
			log        : log,
			pwd        : pwd,
			redirect_to: redirect_to
		}, function (result) {
			var reTitle = result.replace(/^[\S\s]*<title[^>]*?>/i, "")
				.replace(/<\/title[\S\s]*$/i, "");
			reTitle = jQuery.parseHTML(reTitle);
			reTitle = reTitle[0].data;

			if (title == reTitle) {
				location.reload();
			}
			else {
				errorbox.slideDown();
			}
		});
	},
	deletePicture               : function (self) {
		decom.get_data();
		var obj = decom.getParentObj(self),
			attach_id = jQuery(self).prev().find('img').attr('rel'),
			id = decom.getId(decom.getParentObj(self).attr('id')),
			url = decom.admin_ajax + '?lang=' + decom.lang + '&action=decom_comments&f=delete_picture';

		jQuery(self).parent().slideUp();

		jQuery.post(url, {
			comment_id: id,
			attach_id : attach_id,
			post_id   : decom.post_id,
		}, function (data) {
			if (data) {
				//decom.console_log(data);
			}
		});

	},
	insertPicture               : function (self) {
		decom.get_data();
		var fileform = jQuery('#decomments-add-picture-form');
		fileform.submit(function () {
			return false;
		});

		if (fileform.find('input[type="file"]').val() != '') {
			globalAddPicture = true;
			jQuery('.decomments-add-image').addClass('decomments-file-uploaded');
			jQuery('.decomments-add-image').attr('data-img', jQuery('#decomments-add-picture-form img').attr('src'));


			jQuery('#decomments-add-picture-form').find('.decomments-load-img').addClass('decomments-complete');


			jQuery('.decomments-button-addfile-send').attr('data-disabled', 'disabled');


		}
		setTimeout(function () {
			tb_remove();
			decom.closeModal();
		}, 500);

		return false;

	},
	showLoadPicture             : function () {
		jQuery(document).on('change', '#decomments-add-picture-form input', function (e) {
			var file = jQuery('#decomments-add-picture-form input').get(0).files[0];
			var imageType = /image.*/;
			var thisel = jQuery(this);

			var formPicture = jQuery('#decomments-add-picture-form'),
				cover = formPicture.find('.decomments-addfile-cover'),
				cancel_btn = formPicture.find('.decomments-button-addfile-cancel'),
				submit_btn = formPicture.find('.decomments-button-addfile-send');

			if (file.type.match(imageType)) {
				var reader = new FileReader();
				var name = file.name;
				reader.onload = function (e) {
					var img = new Image();
					img.src = reader.result;
					formPicture.find('.decomments-load-img').html(img);
					formPicture.find('.decomments-load-img').addClass('decomments-complete');

					var lmhead = formPicture.find('.decomments-load-img').height(),
						parent = formPicture.parents('#deco_modal_window'),
						top_m = (125 + lmhead) / 2;

					if (!$('#decomments-comment-section').data('text_lang_choose_photo')) {
						$('#decomments-comment-section').data('text_lang_choose_photo', cover.text());
					}
					cover.html(name);
					cancel_btn.css('display', 'none');
					submit_btn.css('display', 'inline-block');

					jQuery('#decomments-add-picture-form').addClass('added-image');


					jQuery('.decomments-addform-body').append('<input type="hidden" name="decom_pictures[\'name\']" id="pic-name" value="' + name + '">');
					jQuery('.decomments-addform-body').append('<input type="hidden" name="decom_pictures[\'src\']" id="pic-src" value="' + img.src + '">');

				}


				reader.readAsDataURL(file);

			} else {
				jQuery('#decomments-add-picture-form .decomments-add-message').show();
				jQuery('.decomments-addform-body #pic-name, .decomments-addform-body #pic-src').remove();
			}
		});
	},
	removeAttachment            : function (e) {
		//e.preventDefault();
		var formPicture = jQuery('#decomments-add-picture-form'),

			cancel_btn = formPicture.find('.decomments-button-addfile-cancel'),
			submit_btn = formPicture.find('.decomments-button-addfile-send');
		jQuery('#decomments-add-picture-form').removeClass('added-image');
		jQuery('.decomments-add-image').removeClass('decomments-file-uploaded');
		jQuery('.decomments-add-image').removeAttr('data-img');

		cancel_btn.css('display', 'inline-block');
		submit_btn.css('display', 'none');
		formPicture.find('.decomments-load-img').html('');
		formPicture.find('.decomments-load-img').removeClass('decomments-complete');
		jQuery('.decomments-addfile-cover').text($('#decomments-comment-section').data('text_lang_choose_photo') || 'Choose file');
	},
	closePicture                : function () {
		jQuery('#decomments-add-picture-form').find('input').val('');
		jQuery('.decomments-add-img').removeClass('decomments-file-uploaded');
		jQuery('#decomments-add-picture-form').submit(function () {
			return false;
		});
		tb_remove();
	},
	resizeImage                 : function () {
		jQuery(document).on('mouseenter', '.decomments-pictures-holder .thickbox', function () {
			var $this = jQuery(this),
				fid = $this.prev().attr('id'),
				href = $this.attr('rel'),
				size = href.split('x'),
				iW = size[0],
				iH = size[1],
				wW = jQuery(window).width(),
				wH = jQuery(window).height();

			if (iW > wW || iH > wH) {
				var iSize = decom.resizerImages(iW, iH, wW, wH);
				$this.prev().find('img').attr({'height': iSize.h, 'width': iSize.w});
				$this.attr('href', '/#TB_inline?width=' + iSize.w + '&height=' + iSize.h + '&inlineId=' + fid);
			}
		});

	},
	resizerImages               : function (iW, iH, wW, wH) {
		if (iW > iH) {
			rW = wW;
			rH = Math.round((rW / iW) * (iH - 200));
			if (rH > wH) {
				rHn = wH;
				rW = Math.round((rHn / rH) * rW);
				rH = rHn;
			}
		}
		else {
			rH = wH - 200;
			rW = Math.round((rH / iH) * iW);
			if (rW > wW) {
				rWn = wW;
				rH = Math.round((rWn / rW) * rH);
				rW = rWn;
			}
		}

		return {w: rW, h: rH};
	},
	insertQuote                 : function () {

		var q_txtarea = jQuery('#decomments-add-blockquote-form textarea'),
			txtarea = jQuery('.decomments-addform-body textarea');
		if (q_txtarea.length > 1) {
			q_txtarea = q_txtarea[1];
		}

		var txt = q_txtarea.val();

		if (txt && txt.length > 0) {
			txt = '[quote]' + txt + '[/quote] ';
			decom.insertAtCaret(txtarea, txt);
		}
		decom.closeQuote();
	},
	insertAtCaret               : function (txtarea, text) {
		if (txtarea[0])txtarea = txtarea[0];
		var scrollPos = txtarea.scrollTop;
		if (decom.cfocus) {
			var strPos = 0;
			if (document.selection) {
				txtarea.focus();
				strPos = ieStrPos;
			}
			else {
				strPos = txtarea.selectionStart;
			}
		}
		else {
			var strPos = 0;
		}
		var front = (txtarea.value).substring(0, strPos);
		var back = (txtarea.value).substring(strPos, txtarea.value.length);
		txtarea.value = front + text + back;

		strPos = strPos + text.length;
		if (document.selection) {
			txtarea.focus();
			var range = document.selection.createRange();
			range.moveStart('character', -txtarea.value.length);
			range.moveStart('character', strPos);
			range.moveEnd('character', 0);
			//range.select();
		}
		else {
			txtarea.selectionStart = strPos;
			txtarea.selectionEnd = strPos;
			txtarea.focus();
		}
		txtarea.scrollTop = scrollPos;
	},
	quoteShortcodeEncode        : function (txt) {
		txt = txt.replace(/<blockquote>[\n\r\t]*[<div>|<p>]+[\n\r\t]*<cite>[\n\r\t]*/mgi, '[quote]');
		txt = txt.replace(/[\n\r\t]*<\/cite>[\n\r\t]*[<\/div>|<\/p>]+[\n\r\t]*<\/blockquote>/mgi, '[/quote]');

		txt = txt.replace(new RegExp('\\s*</p>\\s*<p>\\s*', 'mgi'), "\n\n");
		txt = txt.replace(new RegExp('<p>(\\s| )*</p>', 'mg'), '\n\n');
		txt = txt.replace(new RegExp('<p>', 'mgi'), '');
		txt = txt.replace(new RegExp('\\s*</p>\\s*', 'mgi'), '\n');
		txt = txt.replace(new RegExp('\\n\\s*\\n', 'mgi'), '\n\n');
		txt = txt.replace(new RegExp('\\s*<br ?/?>\\s*', 'gi'), '\n');

		return txt;
	},
	quoteShortcodeDecode        : function (txt) {
		txt = txt.replace(/\[quote\]/mgi, '<blockquote><div><cite>');
		txt = txt.replace(/\[\/quote\]/mgi, '</cite></div></blockquote>');

		return txt;
	},
	closeQuote                  : function () {
		decom.cfocus = false;
		jQuery('#decomments-add-blockquote-form textarea').val('');
		decom.closeModal();
		tb_remove();
	},
	onChangeTimeCounterExpired  : function (id) {
		var self = jQuery('#comment-' + id + ' .decomments-button-edit em');
		var remaining = parseInt(jQuery(self).text());
		var myInterval = setInterval(function () {
			jQuery(self).text(remaining);
			if (remaining == 0) {
				clearInterval(myInterval);
				var obj = jQuery(self).parents('.comment').attr('id');
				var id = decom.getId(obj);
				jQuery(self).parents('.decomments-change-list').remove();
			}
			remaining--;
		}, 60000);
	},
	initAllTimeCounterExpired   : function () {
		//intervals = new Array();
		jQuery('.decomments-button-edit em').each(function () {
			var self = this;
			var remaining = parseInt(jQuery(this).text());
			var myInterval = setInterval(function () {
				jQuery(self).text(remaining);
				if (remaining == 0) {
					clearInterval(myInterval);
					var obj = jQuery(self).parents('.comment').attr('id');
					var id = decom.getId(obj);
					jQuery(self).parents('.decomments-change-list').remove();
				}
				remaining--;
			}, 60000);
		});
	},
	checkSubscribe              : function (item) {
		jQuery(item).toggleClass('active');
	},
	validateSubscribe           : function (name) {
		if (jQuery('[name=' + name + ']').hasClass('active')) {
			return true;
		}
		else return false;
	},
	votes                       : function ($this) {
		decom.get_data();
		var voice = jQuery($this).attr('data-res'),
			thisel = jQuery($this),
			p = jQuery($this).parents('.decomments-vote'),
			comObj = decom.getParentObj($this),
			cID = decom.getId(comObj.attr('id')),
			uID = decom.user_id,
			url = decom.admin_ajax + '?lang=' + decom.lang + '&action=decom_comments&f=voting';

		p.addClass('loading');

		jQuery.post(url, {fk_comment_id: cID, fk_user_id: uID, voice: voice}, function (data) {

			if (data.success) {

				var obj = jQuery('#comment-' + data.success.cId),
					status = obj.find('#decomments-vote-id-' + data.success.cId),
					aLike = status.parent().find('.decomments-like-link'),
					aDislike = status.parent().find('.decomments-dislike-link'),
					voice = parseInt(data.success.voice);

				if (parseInt(data.success.voice) == 0) {
					p.find('.decomments-biggest-vote').fadeOut();
				}

				if (voice > 0 && status.attr('class', 'decomments-dislike')) {
					status.removeClass('decomments-dislike');
					status.addClass('decomments-like-status decomments-like');
					voice = '+' + voice;
					p.find('.decomments-biggest-vote').fadeIn();
				}
				else if (voice < 0 && status.attr('class', 'decomments-like')) {
					status.removeClass('decomments-like');
					status.addClass('decomments-like-status decomments-dislike');
					p.find('.decomments-biggest-vote').fadeIn();
				}

				decom.votesFon(data, aLike, aDislike);
				status.html(voice);
				thisel.parents('.decomments-vote').find('.icon-clicked').removeClass('icon-clicked');
				thisel.addClass('icon-clicked');
				p.removeClass('loading');
			}
			else {
				p.removeClass('loading');
				var modal = jQuery('.decomments-comment-section').attr('data-modal-alert');
				decom.showModal(this, modal);
				jQuery('#decom-alert-void-text p').html(data.error);
			}
		});
	},
	votesFon                    : function (data, aLike, aDislike) {
		decom.get_data();
		if (decom.multiple_vote && data.myVoice > 0) {
			aLike.addClass('decomments-voted-like');
			aDislike.removeClass('decomments-voted-dislike');
		}
		else if (decom.multiple_vote && data.myVoice < 0) {
			aLike.removeClass('decomments-voted-like');
			aDislike.addClass('decomments-voted-dislike');
		}
		else if (!decom.multiple_vote) {
			jQuery(aLike).toggleClass('decomments-voted-like');
		}
		else {
			aDislike.removeClass('decomments-voted-dislike');
			aLike.removeClass('decomments-voted-like');
		}
	},
	setCommentsNumber           : function (add) {
		decom.get_data();
		var total = parseInt(jQuery('.decomments-comments-number').html());
		if (total == 0) {
			//jQuery('.decomments-comment-title').html(decom_comment_single_translate);
			jQuery('.decomments-comment-title').html(jQuery('#decomments-comment-section').data('decom_comment_single_translate'));
		}
		else if (add == 1 && total >= 1 && total < 4) {
			//jQuery('.decomments-comment-title').html(decom_comment_twice_translate);
			jQuery('.decomments-comment-title').html(jQuery('#decomments-comment-section').data('decom_comment_twice_translate'));
		}
		else if (add == 1 && total == 4) {
			//jQuery('.decomments-comment-title').html(c);
			jQuery('.decomments-comment-title').html(jQuery('#decomments-comment-section').data('decom_comment_single_translate'));
		}
		else if (add == -1 && total == 2) {
			//jQuery('.decomments-comment-title').html(decom_comment_single_translate);
			jQuery('.decomments-comment-title').html(jQuery('#decomments-comment-section').data('decom_comment_single_translate'));
		}
		else if (add == -1 && total == 5) {
			//jQuery('.decomments-comment-title').html(decom_comment_twice_translate);
			jQuery('.decomments-comment-title').html(jQuery('#decomments-comment-section').data('decom_comment_twice_translate'));
		}
		else if (add == -1 && total == 1) {
			jQuery('.decom_comments_sort').hide();
		}
		var num = total + add;
		jQuery('.decomments-comments-number').html(num);
	},
	recalculationCommentsNumbers: function (id, add) {
		var prev_number = 0,
			prev_holder = jQuery('#comment-' + id).prev('.comment').find('.decomments-number span');

		if (prev_holder.length) {
			prev_number = prev_holder.eq(prev_holder.length - 1).html();
		}
		if (prev_number !== 0) {
			var pid = jQuery('#comment-' + id).attr('parent-id');
			if (pid !== '0') {
				prev_number = jQuery('#comment-' + pid).find('.decomments-number span').eq(0).html();
			}
		}
		var pnum = 0;
		if (prev_number !== 0) {
			pnum = parseInt(prev_number);
		}
		var numbers = jQuery('.decomments-number span');
		jQuery(numbers).each(function (i) {
			if (i > pnum) {
				jQuery(this).html(i + add);
			}
			else if (i == pnum) {
				jQuery(this).html(pnum + add);
			}
		});
	},
	tweet                       : function (self) {
		var obj = decom.getParentObj(self).eq(0),
			com_id = obj.attr('id'),
			url = window.location.href.split('?')[0],
			url = url.split('#')[0],
			block = jQuery('#' + com_id).find('.decomments-description-block').eq(0).find('.decomments-text-holder'),
			text = block.text(),
			url_into_text,
			uri = '#' + com_id.replace('-', '=');

		uri = encodeURIComponent(uri);
		url += uri;
		url_into_text = url;
		var mn = 140 - url.length
		text = text.substring(0, mn);
		text = text + '...';

		var newWin = window.open('https://twitter.com/share?url=' + url + '&text=' + text, 'Twitter', 'width=500,height=400,resizable=yes,scrollbars=yes,status=yes');
		newWin.focus();
	},
	facebook                    : function (self) {
		var obj = decom.getParentObj(self).eq(0),
			com_id = obj.attr('id'),
			url = window.location.href.split('?')[0],
			url = url.split('#')[0];
		//facebook
		var newWin = window.open('http://www.facebook.com/sharer.php?u=' + url + '?' + com_id.replace('-', '='), 'facebook', 'width=500, height=400');
		newWin.focus();
	},
	vkontakte                   : function (self) {
		var obj = decom.getParentObj(self).eq(0),
			com_id = obj.attr('id'),
			url = window.location.href.split('?')[0],
			url = url.split('#')[0];
		//vkontakte
		var newWin = window.open('http://vkontakte.ru/share.php?url=' + url + '?' + com_id.replace('-', '='), 'vkontakte', 'width=500, height=400');
		newWin.focus();
	},
	google                      : function (self) {
		var obj = decom.getParentObj(self).eq(0),
			com_id = obj.attr('id'),
			url = window.location.href.split('?')[0],
			url = url.split('#')[0];
		var newWin = window.open('http://plus.google.com/share?url=' + url + '?' + com_id.replace('-', '=') + '&text=aaaaaaaa', 'google+', 'width=500, height=400');
		newWin.focus();
	},
	linkedin                    : function (self) {
		var obj = decom.getParentObj(self).eq(0),
			com_id = obj.attr('id'),
			url = window.location.href.split('#')[0],
			url = url.split('#')[0];
		//linkedin
		var newWin = window.open('https://www.linkedin.com/shareArticle?mini=true&url=' + url + '?' + com_id.replace('-', '=') + '&summary=text', 'linkedin', 'width=1000, height=500');
		newWin.focus();
	},
	od                          : function (self) {
		var obj = decom.getParentObj(self).eq(0),
			com_id = obj.attr('id'),
			url = window.location.href.split('#')[0],
			url = url.split('#')[0];
		//odnoklasniki
		var newWin = window.open('http://www.odnoklassniki.ru/dk?st.cmd=addShare&st._surl=' + url, 'Odnoklasniki', 'width=1000, height=500');
		newWin.focus();
	},
	closeModal                  : function () {
		jQuery('#deco_modal_window').remove();
		jQuery('#deco_modal_overlay').remove();
	},
	showModal                   : function (self, type) {
		jQuery('body').append(type);

		if (jQuery('#decomments-add-picture-form').length && jQuery('.decomments-add-image[data-img]').length) {
			jQuery('#decomments-add-picture-form').addClass('added-image');
			jQuery('#decomments-add-picture-form img').attr('src', jQuery('.decomments-add-image').attr('data-img'));
			jQuery('.decomments-button-addfile-cancel').hide();
			jQuery('.decomments-button-addfile-send').css('display', 'inline-block');
		}

		if (jQuery(self).hasClass('addedimg')) {
			var imgsrc = jQuery(self).find('img.hiddenpic').attr('src');
			jQuery('.decom-attachment-full').attr('src', imgsrc);

		}

		if (jQuery('#decomments-add-blockquote-form').length > 0) {
			jQuery('#decomments-add-blockquote-form').find('textarea').focus();
		}

	},
	get_data                    : function () {
		jQuery(document).ready(function () {
			decom.site_url = jQuery('#decomments-form-add-comment').data('site-url');
			decom.admin_ajax = decom.site_url + '/wp-admin/admin-ajax.php';
			decom.post_id = parseInt(jQuery('#decomments-comment-section').data('post_id'));
			decom.user_id = parseInt(jQuery('#decomments-comment-section').data('user_id'));
			decom.is_need_logged = parseInt(jQuery('#decomments-comment-section').data('is_need_logged'));
			decom.lang = jQuery('#decomments-comment-section').data('lang');
			//decom.text_lang_edited = jQuery('#decomments-comment-section').data('text_lang_edited');
			decom.text_lang_delete = jQuery('#decomments-comment-section').data('text_lang_delete');
			decom.text_lang_not_zero = jQuery('#decomments-comment-section').data('text_lang_not_zero');
			decom.text_lang_required = jQuery('#decomments-comment-section').data('text_lang_required');
			decom.text_lang_checked = jQuery('#decomments-comment-section').data('text_lang_checked');
			decom.text_lang_completed = jQuery('#decomments-comment-section').data('text_lang_completed');
			decom.text_lang_items_deleted = jQuery('#decomments-comment-section').data('text_lang_items_deleted');
			decom.text_lang_close = jQuery('#decomments-comment-section').data('text_lang_close');
			decom.text_lang_loading = jQuery('#decomments-comment-section').data('text_lang_loading');
			decom.multiple_vote = parseInt(jQuery('#decomments-comment-section').data('multiple_vote'));
		});
	}
}

jQuery(document).ready(function () {
	decom.get_data();
});

jQuery(document).on('click', '[role="toggle_forgot_password_form"]', function (e) {
	e.preventDefault();
	jQuery(this).parents('.login-form-block').find('.flipper').toggleClass('rotate');
});
jQuery(document).on('click', '.close-modal', function () {

	jQuery('.modal-login').removeClass('open');
});

jQuery(this).keydown(function (eventObject) {
	if (eventObject.which == 27) {

		jQuery('.modal-login').removeClass('open');
	}
});
jQuery(document).on('click touchstart', function (e) {
	if (!jQuery(e.target).parents().hasClass('login-form') && !jQuery(e.target).hasClass('login-form')) {

		jQuery('.modal-login').removeClass('open');
	}

});