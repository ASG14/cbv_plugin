/* Chi Bepoosham — front-end behaviour (vanilla JS, no dependencies). */
(function () {
	'use strict';

	var cfg = window.ChibepooshamTryOn || {};
	var T = cfg.texts || {};
	var ALLOWED = ['image/jpeg', 'image/png', 'image/webp'];
	var MAX_BYTES = cfg.maxBytes || 10 * 1024 * 1024;
	var MAX_SIDE = 1536; // Longest side after client-side downscale.

	function q(root, selector) {
		return root.querySelector(selector);
	}

	/** Shrink big phone photos before upload; falls back to the original file. */
	function downscale(file) {
		return new Promise(function (resolve) {
			var url = URL.createObjectURL(file);
			var img = new Image();

			img.onload = function () {
				URL.revokeObjectURL(url);
				var w = img.naturalWidth;
				var h = img.naturalHeight;
				var scale = Math.min(1, MAX_SIDE / Math.max(w, h));

				if (scale === 1 && file.type === 'image/jpeg') {
					resolve(file);
					return;
				}

				var canvas = document.createElement('canvas');
				canvas.width = Math.round(w * scale);
				canvas.height = Math.round(h * scale);
				var ctx = canvas.getContext('2d');
				ctx.fillStyle = '#ffffff'; // PNG/WEBP transparency -> white.
				ctx.fillRect(0, 0, canvas.width, canvas.height);
				ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
				canvas.toBlob(function (blob) {
					resolve(blob || file);
				}, 'image/jpeg', 0.9);
			};

			img.onerror = function () {
				URL.revokeObjectURL(url);
				resolve(file);
			};

			img.src = url;
		});
	}

	function init(root) {
		var trigger = q(root, '.chibepoosham__trigger');
		var form = q(root, '.chibepoosham__form');
		var input = q(root, '.chibepoosham__file');
		var drop = q(root, '.chibepoosham__drop');
		var dropIntro = q(root, '.chibepoosham__drop-intro');
		var dropPreview = q(root, '.chibepoosham__drop-preview');
		var choose = q(root, '.chibepoosham__choose');
		var submit = q(root, '.chibepoosham__submit');
		var status = q(root, '.chibepoosham__status');
		var result = q(root, '.chibepoosham__result');
		var resultImg = q(root, '.chibepoosham__result-image');
		var resultNote = q(root, '.chibepoosham__note');
		var download = q(root, '.chibepoosham__download');
		var again = q(root, '.chibepoosham__again');

		var chooseLabel = choose.textContent;
		var state = { file: null, previewUrl: null, busy: false };

		function setStatus(message, isError) {
			status.textContent = message || '';
			status.classList.toggle('is-error', !!isError);
		}

		function refresh() {
			submit.disabled = !state.file || state.busy;
			root.classList.toggle('is-loading', state.busy);
			root.setAttribute('aria-busy', state.busy ? 'true' : 'false');
		}

		function setOpen(open) {
			form.hidden = !open;
			trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
		}

		function selectedImageId() {
			var checked = root.querySelector('.chibepoosham__thumb input:checked');
			return checked ? checked.value : '';
		}

		function resetPhoto() {
			if (state.previewUrl) {
				URL.revokeObjectURL(state.previewUrl);
			}
			state.file = null;
			state.previewUrl = null;
			input.value = '';
			dropPreview.hidden = true;
			dropPreview.removeAttribute('src');
			dropIntro.hidden = false;
			drop.classList.remove('has-photo');
			submit.hidden = true;
			choose.textContent = chooseLabel;
			setStatus('');
			refresh();
		}

		function handleFile(file) {
			if (!file) {
				return;
			}
			if (ALLOWED.indexOf(file.type) === -1) {
				setStatus(T.invalidType, true);
				return;
			}
			if (file.size > MAX_BYTES) {
				setStatus(T.tooLarge, true);
				return;
			}

			if (state.previewUrl) {
				URL.revokeObjectURL(state.previewUrl);
			}
			state.file = file;
			state.previewUrl = URL.createObjectURL(file);
			dropPreview.src = state.previewUrl;
			dropPreview.hidden = false;
			dropIntro.hidden = true;
			drop.classList.add('has-photo');
			submit.hidden = false;
			choose.textContent = T.change || chooseLabel;
			setStatus('');
			refresh();
		}

		/** Replace the form with the final image, in the same spot. */
		function showResult(url, note) {
			setOpen(false);
			trigger.hidden = true;
			resultImg.src = url;
			download.href = url;
			resultNote.textContent = note || '';
			resultNote.hidden = !note;
			result.hidden = false;
			setStatus('');
			if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
				result.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
			}
		}

		function run() {
			if (!state.file || state.busy) {
				return;
			}

			state.busy = true;
			setStatus(T.loading, false);
			refresh();

			downscale(state.file)
				.then(function (blob) {
					var body = new FormData();
					body.append('product_id', root.getAttribute('data-product-id'));
					body.append('image_id', selectedImageId());
					body.append('person_photo', blob, 'person.jpg');

					return fetch(cfg.endpoint, { method: 'POST', body: body, credentials: 'same-origin' });
				})
				.then(function (response) {
					return response.json().then(
						function (data) {
							return { ok: response.ok, data: data || {} };
						},
						function () {
							return { ok: false, data: {} };
						}
					);
				})
				.then(function (res) {
					if (res.ok && res.data.image_url) {
						showResult(res.data.image_url);
					} else if (res.ok && res.data.demo) {
						// Service not connected yet: echo the shopper's own photo.
						showResult(state.previewUrl, T.demo);
					} else {
						setStatus(res.data.message || T.error, true);
					}
				})
				.catch(function () {
					setStatus(T.error, true);
				})
				.then(function () {
					state.busy = false;
					refresh();
				});
		}

		trigger.addEventListener('click', function () {
			setOpen(form.hidden);
		});

		input.addEventListener('change', function () {
			handleFile(input.files && input.files[0]);
		});

		// Clicking the drop zone opens the file picker too.
		drop.addEventListener('click', function () {
			input.click();
		});

		// Drag & drop onto the drop zone.
		['dragenter', 'dragover'].forEach(function (type) {
			drop.addEventListener(type, function (event) {
				event.preventDefault();
				drop.classList.add('is-dragover');
			});
		});
		['dragleave', 'drop'].forEach(function (type) {
			drop.addEventListener(type, function (event) {
				event.preventDefault();
				drop.classList.remove('is-dragover');
			});
		});
		drop.addEventListener('drop', function (event) {
			var files = event.dataTransfer && event.dataTransfer.files;
			handleFile(files && files[0]);
		});

		submit.addEventListener('click', run);

		// "Try again": hide the result, clear the photo, reopen the form.
		again.addEventListener('click', function () {
			result.hidden = true;
			trigger.hidden = false;
			resetPhoto();
			setOpen(true);
		});

		refresh();
	}

	function boot() {
		var widgets = document.querySelectorAll('.chibepoosham:not([data-preview])');
		Array.prototype.forEach.call(widgets, init);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();