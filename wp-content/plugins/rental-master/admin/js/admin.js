(function ($) {
	"use strict";

	$(function () {
		$(".rm-admin-card code").each(function () {
			var $code = $(this);

			if ($code.closest("table").length === 0) {
				return;
			}

			$code.attr("title", "Click to copy shortcode");
			$code.css("cursor", "pointer");
			$code.on("click", function () {
				var text = $code.text();
				if (!navigator.clipboard || !text) {
					return;
				}

				navigator.clipboard.writeText(text);
			});
		});
	});
}(jQuery));
