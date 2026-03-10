(function($){
	'use strict';

	function getAjaxUrl() {
		if ( typeof RMAvailability !== 'undefined' && RMAvailability.ajaxUrl ) {
			return RMAvailability.ajaxUrl;
		}
		return typeof ajaxurl !== 'undefined' ? ajaxurl : '';
	}

	function getNonce() {
		return ( typeof RMAvailability !== 'undefined' && RMAvailability.nonce ) ? RMAvailability.nonce : '';
	}

	$(document).on('click', '.updateApartment', function(){
		var $row = $(this).closest('li');
		var id = $row.attr('pid');
		var payload = {
			action: 'update_availability_manager_ajax',
			nonce: getNonce(),
			id: id,
			Rent: $row.find('#Rent').val(),
			Units: $row.find('#Units').val(),
			rentRange: $row.find('#rentRange').val(),
			unitAvail: $row.find('#unitAvail').val(),
			availDate: $row.find('#availDate').val(),
			isFeatured: $row.find('#isfeatured').is(':checked') ? 'on' : ''
		};

		$.post(getAjaxUrl(), payload)
			.done(function(response){
				if ( response && response.success ) {
					if ( window.swal ) {
						swal({ title: 'Success', text: response.data && response.data.message ? response.data.message : 'Updated', type: 'success' });
					}
					return;
				}
				if ( window.swal ) {
					swal({ title: 'Error', text: response && response.data && response.data.message ? response.data.message : 'Update failed', type: 'error' });
				}
			})
			.fail(function(){
				if ( window.swal ) {
					swal({ title: 'Error', text: 'Update failed', type: 'error' });
				}
			});
	});
})(jQuery);
