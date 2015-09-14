<?php
/**
 * Main plugin file.
 *
 * @package   TwotoneFX
 * @since     1.0.0
 * @copyright Copyright (c) 2015 AudioTheme, LLC
 * @license   GPL-2.0+
 */

/**
 * Main plugin class.
 *
 * @package TwotoneFX
 * @since   1.0.0
 */
class TwotoneFX_Plugin extends TwotoneFX_AbstractPlugin {
	/**
	 * Register plugin hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_filter( 'wp_image_editors',              array( $this, 'register_image_editor' ) );
		add_filter( 'wp_image_editor_before_change', array( $this, 'process_image' ), 10, 2 );
		add_filter( 'wp_prepare_attachment_for_js',  array( $this, 'prepare_attachment_for_js' ), 10, 3 );
		add_action( 'updated_postmeta',              array( $this, 'maybe_delete_post_meta' ), 10, 4 );

		add_action( 'admin_footer',                  array( $this, 'attachment_edit_screen_footer' ) );
		add_action( 'wp_enqueue_media',              array( $this, 'enqueue_assets' ) );
		add_action( 'print_media_templates',         array( $this, 'print_templates' ) );
	}

	/**
	 * Load and register image editors.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $editors Array of image editors.
	 * @return array
	 */
	public function register_image_editor( $editors ) {
		include( $this->get_path( 'php/class-image-editor-gd.php' ) );
		include( $this->get_path( 'php/class-image-editor-imagick.php' ) );
		include( $this->get_path( 'php/class-image-pixel-gd.php' ) );

		array_unshift( $editors, 'TwotoneFX_Image_Editor_GD' );
		array_unshift( $editors, 'TwotoneFX_Image_Editor_Imagick' );

		return $editors;
	}

	/**
	 * Process an image.
	 *
	 * @since 1.0.0
	 *
	 * @param  WP_Image_Editor $image   An image editor instance.
	 * @param  array           $changes Array of operations to apply to the image.
	 * @return WP_Image_Editor
	 */
	public function process_image( $image, $changes ) {
		// Ensure the editor can apply a duotone effect.
		if ( ! method_exists( $image, 'twotonefx' ) ) {
			return $image;
		}

		foreach ( array_reverse( $changes ) as $operation ) {
			if ( 'twotonefx' !== $operation->type ) {
				continue;
			}

			$start = self::sanitize_hex_color( $operation->start );
			$end   = self::sanitize_hex_color( $operation->end );

			$image->twotonefx( $start, $end );

			if ( isset( $_REQUEST['do'] ) && 'save' === $_REQUEST['do'] ) {
				$post_id = absint( $_REQUEST['postid'] );

				update_post_meta( $post_id, '_twotonefx_start_color', $start );
				update_post_meta( $post_id, '_twotonefx_end_color', $end );
			}
			break;
		}

		return $image;
	}

	/**
	 * Add data to attachemnts for use in JavaScript.
	 *
	 * @since 1.0.0
	 *
	 * @param  array   $response   Attachment data.
	 * @param  WP_Post $attachment Attachment post object.
	 * @param  array   $meta       Attachment meta data.
	 * @return array
	 */
	public function prepare_attachment_for_js( $response, $attachment, $meta ) {
		$start = get_post_meta( $attachment->ID, '_twotonefx_start_color', true );
		$end   = get_post_meta( $attachment->ID, '_twotonefx_end_color', true );

		$response['twotonefxStart'] = empty( $start ) ? $this->get_default_start_color() : $start;
		$response['twotonefxEnd']  = empty( $end ) ? $this->get_default_end_color() : $end;

		return $response;
	}

	/**
	 * Delete TwotoneFX colors when an image is restored to its original state.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $meta_id    ID of updated metadata entry.
	 * @param int    $object_id  Object ID.
	 * @param string $meta_key   Meta key.
	 * @param mixed  $meta_value Meta value.
	 */
	public function maybe_delete_post_meta( $meta_id, $post_id, $meta_key, $meta_value ) {
		if ( '_wp_attachment_backup_sizes' !== $meta_key ) {
			return;
		}

		$meta = maybe_unserialize( $meta_value );
		$file = basename( get_attached_file( $post_id ) );

		if ( isset( $meta['full-orig'] ) && $meta['full-orig']['file'] === $file ) {
			delete_post_meta( $post_id, '_twotonefx_start_color' );
			delete_post_meta( $post_id, '_twotonefx_end_color' );
		}
	}

	/**
	 * Ensure the edit functionality is enqueued on the attachement edit screen.
	 *
	 * @since 1.0.0
	 */
	public function attachment_edit_screen_footer() {
		if ( 'attachment' !== get_current_screen()->id ) {
			return;
		}

		wp_enqueue_media();
		$this->print_templates();

		$post_id = get_post()->ID;
		$start   = get_post_meta( $post_id, '_twotonefx_start_color', true );
		$end     = get_post_meta( $post_id, '_twotonefx_end_color', true );

		wp_localize_script( 'twotonefx-image-edit', '_twotonefxAttachment', array(
			'startColor' => empty( $start ) ? $this->get_default_start_color() : $start,
			'endColor'   => empty( $end ) ? $this->get_default_end_color() : $end,
		) );
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_assets() {
		wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_script(
			'twotonefx-image-edit',
			$this->get_url( 'assets/js/image-edit.js' ),
			array( 'image-edit', 'media-grid', 'wp-backbone', 'wp-color-picker', 'wp-util' ),
			'1.0.0',
			true
		);
	}

	/**
	 * Print Underscore.js templates.
	 *
	 * @since 1.0.0
	 */
	public function print_templates() {
		include_once( $this->get_path( 'views/media-templates.php' ) );
	}

	/**
	 * Sanitizes a hex color.
	 *
	 * Returns either '', a 3 or 6 digit hex color (with #), or nothing.
	 * For sanitizing values without a #, see sanitize_hex_color_no_hash().
	 *
	 * @since 1.0.0
	 *
	 * @param string $color
	 * @return string
	 */
	public static function sanitize_hex_color( $color ) {
		if ( '' === $color ) {
			return '';
		}

		// 3 or 6 hex digits, or the empty string.
		if ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
			return $color;
		}
	}

	/**
	 * Retrieve the default starting color (shadows).
	 *
	 * @since 1.0.0
	 *
	 * @return string Hex color string.
	 */
	protected function get_default_start_color() {
		return apply_filters( 'twotonefx_default_start_color', '#000000' );
	}

	/**
	 * Retrieve the default ending color (highlights).
	 *
	 * @since 1.0.0
	 *
	 * @return string Hex color string.
	 */
	protected function get_default_end_color() {
		return apply_filters( 'twotonefx_default_end_color', '#ffffff' );
	}
}
