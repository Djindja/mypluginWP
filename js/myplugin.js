(function ($) {
	"use strict";

	$(document).ready(function () {
		mypluginFunction();
	});

	function mypluginFunction() {

		var articleButton = $('.single-post article .my-button');
		articleButton.click(function () {

			var thisButton = $(this);

			// if you have this class (it will disable button)
			// you can't add more posts if you click on the button more then once
			if (thisButton.hasClass('disable-button')) {
				return;
			}

			var parent = thisButton.parents('.single-post article');
			var articleID = parent.attr('id');
			var id = articleID.substring(5, articleID.length);

			var ajaxData = {
				action: 'my_plugin_ajax_function',
				PostID: id
			};
			$.ajax({
				type: 'POST',
				data: ajaxData,
				url: myPluginAjaxUrl,
				success: function (data) {
					var response = $.parseJSON(data);
					parent.after(response.html);
					thisButton.addClass('disable-button');
				}
			});
		});
	}

})(jQuery);