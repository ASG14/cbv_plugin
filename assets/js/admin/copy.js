/* Chi Bepoosham — admin "usage" tab: copy shortcode to clipboard. */
(function () {
	'use strict';

	function fallbackCopy(text) {
		var area = document.createElement('textarea');
		area.value = text;
		area.setAttribute('readonly', '');
		area.style.position = 'fixed';
		area.style.opacity = '0';
		document.body.appendChild(area);
		area.select();
		try {
			document.execCommand('copy');
		} catch (e) {
			/* ignore */
		}
		document.body.removeChild(area);
	}

	function bind(button) {
		button.addEventListener('click', function () {
			var target = document.querySelector(button.getAttribute('data-target'));
			var text = target ? target.textContent : '';
			var original = button.textContent;

			function done() {
				button.textContent = 'کپی شد';
				setTimeout(function () {
					button.textContent = original;
				}, 1600);
			}

			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(text).then(done, function () {
					fallbackCopy(text);
					done();
				});
			} else {
				fallbackCopy(text);
				done();
			}
		});
	}

	Array.prototype.forEach.call(document.querySelectorAll('.cbv-copy'), bind);
})();
