(function () {
	"use strict";

	function debounce(callback, delay) {
		var timer;
		return function () {
			var context = this;
			var args = arguments;
			clearTimeout(timer);
			timer = setTimeout(function () {
				callback.apply(context, args);
			}, delay);
		};
	}

	function renderResults(target, items) {
		if (!target) {
			return;
		}

		if (!items || !items.length) {
			target.innerHTML = "";
			return;
		}

		var html = '<ul class="rm-fast-search-list">';
		items.forEach(function (item) {
			var price = item.price ? " - $" + item.price : "";
			html += '<li><a href="' + item.permalink + '">' + item.title + "</a>" + price + "</li>";
		});
		html += "</ul>";
		target.innerHTML = html;
	}

	function bindFastSearch(form) {
		if (typeof RM_FastSearch === "undefined" || !form) {
			return;
		}

		var status = form.parentNode.querySelector(".rm-fast-search-status");
		var results = form.parentNode.querySelector(".rm-fast-search-results");
		var controller = null;

		var performSearch = debounce(function () {
			if (controller) {
				controller.abort();
			}

			controller = new AbortController();
			var payload = new URLSearchParams({
				action: RM_FastSearch.action || "rm_fast_search",
				nonce: RM_FastSearch.nonce || "",
				name: (form.querySelector('[name="name"]') || {}).value || "",
				location: (form.querySelector('[name="location"]') || {}).value || "",
				rental_category: (form.querySelector('[name="rental_category"]') || {}).value || "",
				property_type: (form.querySelector('[name="property_type"]') || {}).value || "",
				limit: String(RM_FastSearch.defaultLimit || 10)
			});

			if (status) {
				status.textContent = RM_FastSearch.loadingText || "Searching...";
			}

			fetch(RM_FastSearch.ajaxUrl, {
				method: "POST",
				headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
				body: payload.toString(),
				signal: controller.signal
			})
				.then(function (response) {
					return response.json();
				})
				.then(function (response) {
					if (!response || !response.success || !response.data || !response.data.results || !response.data.results.length) {
						if (status) {
							status.textContent = RM_FastSearch.emptyText || "No listings found.";
						}
						renderResults(results, []);
						return;
					}

					if (status) {
						status.textContent = String(response.data.count) + " results";
					}
					renderResults(results, response.data.results);
				})
				.catch(function (error) {
					if (error && error.name === "AbortError") {
						return;
					}
					if (status) {
						status.textContent = RM_FastSearch.emptyText || "No listings found.";
					}
					renderResults(results, []);
				});
		}, 250);

		form.addEventListener("input", performSearch);
		form.addEventListener("change", performSearch);
	}

	document.addEventListener("DOMContentLoaded", function () {
		document.querySelectorAll('form[data-rm-fast-search="1"]').forEach(function (form) {
			bindFastSearch(form);
		});
	});
}());
