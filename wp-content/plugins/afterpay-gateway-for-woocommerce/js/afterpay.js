var initAfterpayModal = function() {
	jQuery(document).on("click", "a[href='#afterpay-what-is-modal']", function(event) {
		event.preventDefault();
		Afterpay.launchModal(afterpay_js_config.locale);
	});
};

if (typeof Afterpay === 'undefined') {
	window.addEventListener('Afterpay.ready', function() {
		initAfterpayModal();
	});
} else {
	initAfterpayModal();
}
