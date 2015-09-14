<?php
/**
 * ImageMagick image editor.
 *
 * @package   TwotoneFX
 * @since     1.0.0
 * @copyright Copyright (c) 2015 AudioTheme, LLC
 * @license   GPL-2.0+
 */

/**
 * Class for the ImageMagick image editor.
 *
 * @package TwotoneFX
 * @since   1.0.0
 */
class TwotoneFX_Image_Editor_Imagick extends WP_Image_Editor_Imagick {
	/**
	 * Whether the current environment is configured with required methods.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $args
	 * @return bool
	 */
	public static function test( $args = array() ) {
		if ( ! parent::test( $args ) ) {
			return false;
		}

		$required_methods = array(
			'clutimage',
			'newpseudoimage',
			'transformimagecolorspace',
		);

		if ( array_diff( $required_methods, get_class_methods( 'Imagick' ) ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Apply a duotone effect.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $start Starting hex color (replaces shadows).
	 * @param  string $end   Ending hex color (replaces highlights).
	 * @return $this
	 */
	public function twotonefx( $start, $end ) {
		$this->convert_to_grayscale();
		$this->image->transformImageColorspace( imagick::COLORSPACE_RGB );

		$clut = $this->get_map( $start, $end );
		$this->image->clutImage( $clut );
		unset( $clut );

		return $this;
	}

	/**
	 * Convert the image to grayscale.
	 *
	 * @since 1.0.0
	 *
	 * @return $this
	 */
	public function convert_to_grayscale() {
		#$this->image->modulateImage( 100, 0, 100 );
		#$this->image->setColorspace( imagick::COLORSPACE_GRAY );
		#$this->image->setImageColorspace( imagick::COLORSPACE_GRAY );
		$this->image->transformImageColorspace( imagick::COLORSPACE_GRAY );

		return $this;
	}

	/**
	 * Retrieve a gradient map.
	 *
	 * @since 1.0.0
	 *
	 * @link http://phpimagick.com/Imagick/clutImage
 	 * @link http://www.imagemagick.org/discourse-server/viewtopic.php?t=13181
	 *
	 * @param  string $start Starting hex color.
	 * @param  string $end   Ending hex color.
	 * @return Imagick
	 */
	protected function get_map( $start, $end ) {
		$gradient = sprintf( 'gradient:%s-%s', $start, $end );

		$clut = new Imagick();
		$clut->newPseudoImage( 1, 256, $gradient );

		return $clut;
	}
}
