<?php
/**
 * Shared form-field helpers for the admin tabs.
 */

defined( 'ABSPATH' ) || exit;

function chibepoosham_field_name( string $key ): string {
	return CHIBEPOOSHAM_OPTION . '[' . $key . ']';
}

function chibepoosham_text_field( array $s, string $key, string $label ): void {
	?>
	<div class="cbp-field">
		<label for="cbp-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
		<input type="text" id="cbp-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( chibepoosham_field_name( $key ) ); ?>" value="<?php echo esc_attr( $s[ $key ] ); ?>" data-cbp-text="<?php echo esc_attr( $key ); ?>">
	</div>
	<?php
}

function chibepoosham_color_field( array $s, string $key, string $label, string $var ): void {
	$defaults = chibepoosham_defaults();
	?>
	<div class="cbp-field cbp-field--color">
		<label for="cbp-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
		<input type="text" class="cbp-color" id="cbp-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( chibepoosham_field_name( $key ) ); ?>" value="<?php echo esc_attr( $s[ $key ] ); ?>" data-default-color="<?php echo esc_attr( $defaults[ $key ] ); ?>" data-cbp-var="<?php echo esc_attr( $var ); ?>">
	</div>
	<?php
}

function chibepoosham_checkbox_field( array $s, string $key, string $label ): void {
	?>
	<label class="cbp-check">
		<input type="checkbox" name="<?php echo esc_attr( chibepoosham_field_name( $key ) ); ?>" value="1" <?php checked( ! empty( $s[ $key ] ) ); ?> data-cbp-toggle="<?php echo esc_attr( $key ); ?>">
		<span><?php echo esc_html( $label ); ?></span>
	</label>
	<?php
}
