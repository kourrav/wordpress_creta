;(function($) {
	var spinner = null;

	var cartRequiresShipping = function() {
		return $('.requires-shipping--true').length > 0;
	};

	var initAfterpayExpress = function() {
		if ($('.btn-afterpay_express').length && typeof AfterPay != 'undefined') {
			$('.btn-afterpay_express').prop('disabled', false);

			AfterPay.initializeForPopup({
				shippingOptionRequired: cartRequiresShipping(),
				countryCode: afterpay_express_js_config.country_code,
				target: '.btn-afterpay_express',
				buyNow: true,
				pickup: false,
				onCommenceCheckout: function(actions) {
					$('.btn-afterpay_express').prop('disabled', true);

					if($('.buy-backdrop').length) {
						var spinnerOverlay = $('.buy-backdrop').clone();
						spinnerOverlay.find(':contains("Afterpay")').remove();

						if(!spinner) {
							spinner = {
								overlay: spinnerOverlay,
								css: $('style:contains("buy-backdrop")').clone()
							}
						}
					}

					$.ajax({
						url: afterpay_express_js_config.ajaxurl,
						method: 'POST',
						data: {
							action: 'afterpay_express_start',
							nonce: afterpay_express_js_config.ec_start_nonce
						},
						success: function(data){
							if (!data.success) {
								if(data.message) {
									actions.reject(data.message);
								} else {
									actions.reject(AfterPay.CONSTANTS.BAD_RESPONSE);
								}

								if (data.redirectUrl) {
									window.location.href = data.redirectUrl;
								}
							} else {
								actions.resolve(data.token);
							}
						},
						error: function(request, statusText, errorThrown) {
							actions.reject(AfterPay.CONSTANTS.BAD_RESPONSE);
							alert('Something went wrong. Please try again later.');
						}
					});
				},
				onShippingAddressChange: function (data, actions) {
					$.ajax({
							url: afterpay_express_js_config.ajaxurl,
							method: 'POST',
							data: {
								action: 'afterpay_express_change',
								nonce: afterpay_express_js_config.ec_change_nonce,
								address: data
							},
							success: function(options){
								if (options.hasOwnProperty('error')) {
									actions.reject(AfterPay.CONSTANTS.SERVICE_UNAVAILABLE, options.message);
								} else {
									actions.resolve(options);
								}
							},
							error: function(request, statusText, errorThrown) {
								actions.reject(AfterPay.CONSTANTS.BAD_RESPONSE);
							}
					});
				},
				onShippingOptionChange: function (data) {
					$.ajax({
						url: afterpay_express_js_config.ajaxurl,
						method: 'POST',
						data: {
							action: 'afterpay_express_shipping_change',
							shipping: data.id,
							nonce: afterpay_express_js_config.ec_change_shipping_nonce
						}
					})
				},
				onComplete: function (event) {
					if (event.data) {
						if (event.data.status && event.data.status == 'SUCCESS') {
							if (spinner) {
								spinner.overlay.appendTo('body');
								spinner.css.appendTo('head');
							}

							$.ajax({
								url: afterpay_express_js_config.ajaxurl,
								method: 'POST',
								data: {
									action: 'afterpay_express_complete',
									nonce: afterpay_express_js_config.ec_complete_nonce,
									token: event.data.orderToken
								},
								success: function(data){
									$('.btn-afterpay_express').prop('disabled', false);
									if (data.redirectUrl) {
										window.location.href = data.redirectUrl;
									} else {
										spinner.overlay.remove();
										spinner.css.remove();
									}
								},
								error: function(request, statusText, errorThrown) {
									$('.btn-afterpay_express').prop('disabled', false);
									alert('Something went wrong. Please try again later.');

									spinner.overlay.remove();
									spinner.css.remove();
								}
							});
						} else {
							$('.btn-afterpay_express').prop('disabled', false);
						}
					}
				}
			});
		}
	};

	$(function() {
		initAfterpayExpress();
		// needs to be called here again as  when the Woo cart updates via ajax the button needs to have the event re-bound
		$(document.body).on('updated_cart_totals', initAfterpayExpress);
	});
})(jQuery);
