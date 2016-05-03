jQuery(function () {
	jQuery('#decom-btn-submit-notification').click(function () {
		decomAdmin.editNotification();
	});

	jQuery('#decom-remove-badges').click(function () {
		decomBadges.remove()
	});
	jQuery('#show-supported').on('click', function () {
		shortcodes()
	});
	jQuery('[name=output_subscription_comments], [name=output_subscription_rejoin]').on('click', function () {
		decom_settings.disableMark(this)
	});

});

decomAdmin = {
	editNotification: function () {
		jQuery('#message-success').hide();
		jQuery('#message-error').hide();
		jQuery('#form-notification').form('submit', {
			url     : decomSettings.admin_ajax,
			onSubmit: function () {
				return jQuery(this).form('validate');
			},
			success : function (data) {
				data = jQuery.parseJSON(data);
				if (data.success) {
					jQuery('#message-success p').html(data.success);
					jQuery('#message-success').show();
				}
				else {
					jQuery('#message-error p').html(data.error);
					jQuery('#message-error').show();
				}
			},
			error   : function (e) {
				console.log(e);
			}
		});
	},

	editSettings: function () {
		var optionsSettings = {
			url         : decomSettings.admin_ajax,
			beforeSubmit: function () {
				jQuery('#message-success').hide();
				jQuery('#message-error').hide();
			},
			success     : function (data) {
				if (data.success) {
					jQuery('#message-success p').html(data.success);
					jQuery('#message-success').show();
				}
				else {
					jQuery('#message-error p').html(data.error);
					jQuery('#message-error').show();
				}
			},
			error       : function (e) {
				console.log(e);
			}
		};

		jQuery('#decom-settings-form').ajaxForm(optionsSettings);
	},

	addBadges: function () {
		var optionsAddBadges = {
			url    : decomSettings.admin_ajax + '?action=decom_badges&f=add_badges',
			success: function (data) {
				if (data.success) {
					window.location.href = decomSettings.site_url + data.success;
				}
				else {
					jQuery('#message-error').show();
					jQuery('#message-error p').html(data.error);
				}
			}
		};

		jQuery('#decom-badge-form-add').ajaxForm(optionsAddBadges);
		return false;
	},

	editBadges: function () {
		var optionsEditBadges = {
			url    : decomSettings.admin_ajax + '?action=decom_badges&f=edit_badges',
			success: function (data) {
				if (data.success) {
					window.location.href = decomSettings.site_url + data.success;
				}
				else {
					jQuery('#message-error').show();
					jQuery('#message-error p').html(data.error);
				}
			}
		};

		jQuery('#decom-badge-form-edit').ajaxForm(optionsEditBadges);
		return false;
	}


}

function shortcodes() {
	jQuery('#decom-supported-shortcodes a').hide();
	jQuery('#decom_shortcodes').show();
}