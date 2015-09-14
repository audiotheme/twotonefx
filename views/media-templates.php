<?php
/**
 * Underscore.js templates.
 *
 * @package   TwotoneFX
 * @since     1.0.0
 * @copyright Copyright (c) 2015 AudioTheme, LLC
 * @license   GPL-2.0+
 */
?>

<script type="text/html" id="tmpl-twotonefx-image-edit-group">
<div class="imgedit-group-top">
	<h3>
		<?php esc_html_e( 'Twotone', 'twotone-fx' ); ?>
		<a href="#" class="dashicons dashicons-editor-help imgedit-help-toggle" onclick="imageEdit.toggleHelp(this);return false;"></a>
	</h3>
	<div class="imgedit-help">
		<ul>
			<li><?php _e( 'The starting color replaces shadows.', 'twotone-fx' ); ?></li>
			<li><?php _e( 'The ending color replaces highlights.', 'twotone-fx' ); ?></li>
		</ul>
	</div>
	<p>
		<strong><?php esc_html_e( 'Starting Color:', 'twotone-fx' ); ?></strong><br>
		<input type="text" name="start" value="{{ data.twotonefxStart }}">
	</p>
	<p>
		<strong><?php esc_html_e( 'Ending Color:', 'twotone-fx' ); ?></strong><br>
		<input type="text" name="end" value="{{ data.twotonefxEnd }}">
	</p>
	<p>
		<button class="button-secondary"><?php esc_html_e( 'Apply', 'twotone-fx' ); ?></button>
	</p>
</div>
</script>
