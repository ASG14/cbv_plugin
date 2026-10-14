<?php
/**
 * Shared form-field helpers for the admin tabs.
 */

defined( 'ABSPATH' ) || exit;

function cbv_field_name( string $key ): string {
	return CBV_OPTION . '[' . $key . ']';
}

function cbv_text_field( array $s, string $key, string $label ): void {
	?>
	<div class="cbv-field">
		<label for="cbv-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
		<input type="text" id="cbv-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( cbv_field_name( $key ) ); ?>" value="<?php echo esc_attr( $s[ $key ] ); ?>" data-cbv-text="<?php echo esc_attr( $key ); ?>">
	</div>
	<?php
}

function cbv_color_field( array $s, string $key, string $label, string $var ): void {
	$defaults = cbv_defaults();
	?>
	<div class="cbv-field cbv-field--color">
		<label for="cbv-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
		<input type="text" class="cbv-color" id="cbv-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( cbv_field_name( $key ) ); ?>" value="<?php echo esc_attr( $s[ $key ] ); ?>" data-default-color="<?php echo esc_attr( $defaults[ $key ] ); ?>" data-cbv-var="<?php echo esc_attr( $var ); ?>">
	</div>
	<?php
}

function cbv_checkbox_field( array $s, string $key, string $label ): void {
	?>
	<label class="cbv-check">
		<input type="checkbox" name="<?php echo esc_attr( cbv_field_name( $key ) ); ?>" value="1" <?php checked( ! empty( $s[ $key ] ) ); ?> data-cbv-toggle="<?php echo esc_attr( $key ); ?>">
		<span><?php echo esc_html( $label ); ?></span>
	</label>
	<?php
}
