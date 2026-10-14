/* Chi Bepoosham — admin "style" tab: live preview. */
(function ($) {
	'use strict';

	var $widget = $('#cbp-preview .chibepoosham');

	function setVar(name, value) {
		if ($widget.length && name) {
			$widget[0].style.setProperty(name, value);
		}
	}

	/* Colors */
	$('.cbp-color').each(function () {
		var $input = $(this);
		var cssVar = $input.data('cbpVar');
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
	$('[data-cbp-text]').on('input', function () {
		var key = $(this).data('cbpText');
		$widget.find('[data-cbp-bind="' + key + '"]').text(this.value);
	});

	/* Corner radius */
	$('[data-cbp-radius]').on('input', function () {
		setVar('--cbp-radius', this.value + 'px');
		$('#cbp-radius-out').text(this.value);
	});

	/* Font */
	$('[data-cbp-font]').on('input', function () {
		setVar('--cbp-font', $.trim(this.value) || 'inherit');
	});

	/* Button style */
	$('[data-cbp-button]').on('change', function () {
		$widget.removeClass('chibepoosham--solid chibepoosham--outline').addClass('chibepoosham--' + this.value);
	});

	/* Toggles */
	$('[data-cbp-toggle]').on('change', function () {
		var key = $(this).data('cbpToggle');
		var on = this.checked;

		if (key === 'shadow') {
			$widget.toggleClass('chibepoosham--shadow', on);
		} else if (key === 'show_title') {
			$widget.find('.chibepoosham__title').prop('hidden', !on);
		} else if (key === 'show_description') {
			$widget.find('.chibepoosham__description').prop('hidden', !on);
		}
	});

	/* Preview state: button only / open form / result */
	function setPreviewState(state) {
		var $trigger = $widget.find('.chibepoosham__trigger');

		$trigger.prop('hidden', state === 'result').attr('aria-expanded', state === 'form' ? 'true' : 'false');
		$widget.find('.chibepoosham__form').prop('hidden', state !== 'form');
		$widget.find('.chibepoosham__result').prop('hidden', state !== 'result');
	}

	$('input[name="cbp-preview-state"]').on('change', function () {
		setPreviewState(this.value);
	});
})(jQuery);
