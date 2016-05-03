jQuery(function () {
	jQuery('#decom-badge-form-add,#decom-badge-form-edit').submit(function (e) {
		e.preventDefault(); //Prevent Default action.

		//console.log(this);
		var formObj = jQuery(this);
		var formURL = decomSettings.admin_ajax + '?action=decom_badges&f=add_badges';
		var formData = new FormData(this);
		jQuery.ajax({
			url        : formURL,
			type       : 'POST',
			data       : formData,
			mimeType   : "multipart/form-data",
			contentType: false,
			cache      : false,
			processData: false,
			success    : function (data) {
				data = jQuery.parseJSON(data);
				//console.log(typeof data);
				if (data.success)
					window.location.href = data.success;
			},
			error      : function (jqXHR, textStatus, errorThrown) {
			}
		});


	});
});

jQuery(function () {

});

var decomBadges =
{
	remove: function () {
		var checked = jQuery('.decom-badges-datagrid').datagrid('getChecked');
		if (!checked.length) {
			jQuery.messager.alert('', decomLang.checked, 'error');
			return false;
		}
		var ids = new Array();
		jQuery.each(checked, function (i, val) {
			ids.push(val.id);
		});
		jQuery.post(decomSettings.admin_ajax + '?action=decom_badges&f=delete_badges', {badges: ids, actions: 'delete'},
			function (response) {
				if (response.success) {
					jQuery.messager.alert
					(
						'',
						decomLang.completed,
						'info',
						function () {
							for (var i = 0; i <= checked.length - 1; i++) {
								var index = jQuery('.decom-badges-datagrid').datagrid('getRowIndex', checked[i]);
								jQuery('.decom-badges-datagrid').datagrid('deleteRow', index);
							}
							jQuery('.datagrid-header-check input').removeAttr('checked');
						}
					);
				}
				else if (response == 'failed') {
					jQuery.messager.alert('', decomLang.items_deleted, 'error');
				}
			})
	}
}
