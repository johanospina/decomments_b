decom_notification = {
	submitForm: function () {
		jQuery('#ff').form('submit', {
			url     : '',
			onSubmit: function () {
				// do some check
				// return false to prevent submit;
			},
			success : function (data) {
				alert(data)
			}
		});
	}
}