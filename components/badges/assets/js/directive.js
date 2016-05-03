angular.module('appBadges').
	directive('deuploadimg', function () {
		return {
			restrict: "A",
			require : "?ngModel",
			link    : function (scope, element, attrs, ngModel) {

				element.bind("click", function (e) {
					e.preventDefault();
				});

				element.attr("deuploadimg", true);

				element.on("click", function () {
					if (typeof wp !== 'undefined' && wp.media && wp.media.editor) {
						var img_element = element.parent().prev();
						wp.media.editor.open();
						wp.media.editor.send.attachment = function (a, obj) {
							console.log(obj);
							img_element.src = obj.url;
							ngModel.$setViewValue(obj.url);
						};
					}
				});

			}
		}
	}).directive('bindIf', function () {
		return {
			restrict: 'A',
			require : 'ngModel',

			link: function (scope, element, attrs, ngModel) {
				function parser(value) {
					var show = scope.$eval(attrs.bindIf);
					return show ? value : '';
				}

				ngModel.$parsers.push(parser);
			}
		}
	});