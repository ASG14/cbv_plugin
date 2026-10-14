/* Chi Bepoosham — admin "style" tab: live preview. */
(function ($) {
	'use strict';

	var $widget = $('#cbv-preview .cbv');

	function setVar(name, value) {
		if ($widget.length && name) {
			$widget[0].style.setProperty(name, value);
		}
	}

	/* Colors */
	$('.cbv-color').each(function () {
		var $input = $(this);
		var cssVar = $input.data('cbvVar');
		var fallback = $input.data('defaultColor');

		$input.wpColorPicker({
			change: function (event, ui) {
				setVar(cssVar, ui.color.toString());
			},
			clear: function () {
				setVar(cssVar, fallback);
			}
		});
	});

	/* Texts */
	$('[data-cbv-text]').on('input', function () {
		var key = $(this).data('cbvText');
		$widget.find('[data-cbv-bind="' + key + '"]').text(this.value);
	});

	/* Corner radius */
	$('[data-cbv-radius]').on('input', function () {
		setVar('--cbv-radius', this.value + 'px');
		$('#cbv-radius-out').text(this.value);
	});

	/* Font */
	$('[data-cbv-font]').on('input', function () {
		setVar('--cbv-font', $.trim(this.value) || 'inherit');
	});

	/* Button style */
	$('[data-cbv-button]').on('change', function () {
		$widget.removeClass('cbv--solid cbv--outline').addClass('cbv--' + this.value);
	});

	/* Toggles */
	$('[data-cbv-toggle]').on('change', function () {
		var key = $(this).data('cbvToggle');
		var on = this.checked;

		if (key === 'shadow') {
			$widget.toggleClass('cbv--shadow', on);
		} else if (key === 'show_title') {
			$widget.find('.cbv__title').prop('hidden', !on);
		} else if (key === 'show_description') {
			$widget.find('.cbv__description').prop('hidden', !on);
		}
	});

	/* Preview state: button only / open form / result */
	function setPreviewState(state) {
		var $trigger = $widget.find('.cbv__trigger');

		$trigger.prop('hidden', state === 'result').attr('aria-expanded', state === 'form' ? 'true' : 'false');
		$widget.find('.cbv__form').prop('hidden', state !== 'form');
		$widget.find('.cbv__result').prop('hidden', state !== 'result');
	}

	$('input[name="cbv-preview-state"]').on('change', function () {
		setPreviewState(this.value);
	});
})(jQuery);
