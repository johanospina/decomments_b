jQuery(document).ready(function ($) {

	$('#decomments-settings-color-field').wpColorPicker();

	if ($("#_enable_embed_links").prop('checked')) {
		$("#max_embed_links_count").addClass('active');
	} else {
		$("#max_embed_links_count").removeClass('active');
	}

	$("#_enable_embed_links").on('change', function () {
		if ($("#_enable_embed_links").prop('checked')) {
			$("#max_embed_links_count").addClass('active');
		} else {
			$("#max_embed_links_count").removeClass('active');
		}
	});
	var spinner = $('.decom-message-box').find('.spinner');
	$('#decom-settings-form').submit(function (e) {
		var form_settings = jQuery(this);
		var settings_a = [];
		$('#message-success').hide();
		$('#message-error').hide();

		//dsp_loader.addClass('active');
		spinner.addClass('active');
		e.preventDefault();
		//var formData = form_settings.serialize();
		var formData;
		//console.log(form_settings.find('input'));
		form_settings.find('input[type=number]').each(function () {
			var input_value = jQuery(this);
			settings_a.push(input_value.attr('name') + "=" + input_value.val());
		});
		form_settings.find('input[type=text]').each(function () {
			var input_value = jQuery(this);
			settings_a.push(input_value.attr('name') + "=" + input_value.val());
		});
		form_settings.find('input[type=hidden]').each(function () {
			var input_value = jQuery(this);
			settings_a.push(input_value.attr('name') + "=" + input_value.val());
		});
		form_settings.find('input:checked').each(function () {
			var input_value = jQuery(this);
			settings_a.push(input_value.attr('name') + "=" + input_value.val());
		});
		form_settings.find('textarea').each(function () {
			var input_value = jQuery(this);
			settings_a.push(input_value.attr('name') + "=" + input_value.val());
		});

		formData = settings_a.join('&');
		jQuery.ajax({
			url     : decomSettings.admin_ajax,
			type    : 'POST',
			dataType: "json",
			//dataType: "html",
			data    : "action=decom_edit_settings&" + encodeURI(formData),
			//mimeType: "multipart/form-data",
			//cache   : false,
			success : function (data) {
				// console.log(data);
				if (data.success) {
					$('#message-success p').html(data.success);
					$('#message-success').show();
					spinner.removeClass('active');
					setTimeout(function () {
						$('#message-success').fadeOut(500, function () {
							$('#message-success').find('p').html('');
						});
					}, 1500);
				}
				else {
					$('#message-error p').html(data.error);
					$('#message-error').show();
					setTimeout(function () {
						$('#message-error').fadeOut(500, function () {
							$('#message-error').find('p').html('');
						});
					}, 1500);
				}
			},
			error   : function (e) {
				console.log(e);
			}
		});
	});
	$('#deco-upload-default-avatar').click(function () {
		if (typeof wp !== 'undefined' && wp.media && wp.media.editor) {
			wp.media.editor.open('decom-avatar-input');
			wp.media.editor.send.attachment = function (a, obj) {
				console.log(obj);
				$('#decom-settings-avatar').attr('src', obj.url);
				$('#decom-avatar-input').attr('value', obj.url);
				$('#decom-settings-avatar').show();
				jQuery('#deco-upload-default-avatar').hide();
				jQuery('#deco-remove-uploaded-avatar').show();
			};
		}
		return false;
	});

	$('#deco-remove-uploaded-avatar').click(function () {
		var url_img = jQuery(this).data('default-avatar');
		jQuery('#deco-remove-uploaded-avatar').hide();
		jQuery('#deco-upload-default-avatar').show();
		$('#decom-settings-avatar').hide();
		$('#decom-settings-avatar').attr('src', '');
		$('#decom-avatar-input').attr('value', '');
		//$('#decom-avatar-input').val(url_img);

		return false;
	});

	if ($('#decom-settings-page').length) {
		var dsp = $('#decom-settings-page'),
			dnav = $('.dsp-nav');

		$('.dsl-item').each(function () {
			$(this).attr('data-top', $(this).offset().top - 110);
		});

		$(document).on('resize', $(window), function () {
			$('.dsl-item').each(function () {
				$(this).attr('data-top', $(this).offset().top - 110);
			});
		});

		dnav.find('li').click(function () {
			var el = $(this);
			$('body,html').stop().animate({scrollTop: $('.dsl-item[data-tab="' + el.attr('data-tab-num') + '"]').offset().top - 109}, 500);
		});

		$(document).on('scroll', $(window), function () {
			var win_s = $(window).scrollTop(),
				start = dsp.offset().top - 32;

			if (win_s > start) {
				dsp.addClass('fixed-po');
			} else {
				dsp.removeClass('fixed-po');
			}


			var starts = $('.dsl-item:first').offset().top - 110,
				ends = $('#wpfooter').offset().top;

			if (win_s > starts) {

				if (win_s > ends) {

					dnav.find('li').removeClass('active');

				} else {

					$('.dsl-item').each(function () {
						var el = $(this),
							num = parseInt(el.attr('data-top')),
							name = el.attr('data-tab');
						if (win_s > num) {
							dnav.find('li').removeClass('active');
							dnav.find('li[data-tab-num="' + name + '"]').addClass('active');
						}
					});

				}

			} else {

				dnav.find('li').removeClass('active');

			}

		});
	}
});


var decom_settings = {

	updateSettings: function () {
		//var arr = [];
		jQuery('input').each(function () {
			//arr
			jQuery(this).val();
		});
	},

	disableMark: function ($this) {
		var name = jQuery($this).attr('name');
		var nameArr = name.split('_');
		name = 'mark_' + nameArr[1] + '_' + nameArr[2];
		if (jQuery($this).is(':checked')) {
			jQuery('.' + name).removeClass('disabled');
			jQuery('[name=' + name + ']').removeAttr('disabled');
		}
		else {
			jQuery('.' + name).addClass('disabled');
			if (jQuery('[name=' + name + ']').is(':checked')) {
				jQuery('[name=' + name + ']').click();
			}
			jQuery('[name=' + name + ']').attr('disabled', 'disabled');
		}
	}

};