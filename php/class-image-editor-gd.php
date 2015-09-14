<?php
/**
 * GD image editor.
 *
 * @package   TwotoneFX
 * @since     1.0.0
 * @copyright Copyright (c) 2015 AudioTheme, LLC
 * @license   GPL-2.0+
 */

/**
 * Class for the GD image editor.
 *
 * @package TwotoneFX
 * @since   1.0.0
 */
class TwotoneFX_Image_Editor_GD extends WP_Image_Editor_GD {
	/**
	 * Gradient map.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $map;

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
		$this
			->convert_to_grayscale()
			->update_map( $start, $end )
			->each_pixel( array( $this, 'apply_map' ) );

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
		imagefilter( $this->image, IMG_FILTER_GRAYSCALE );
		return $this;
	}

	/**
	 * Apply a callback method to each pixel in the image.
	 *
	 * @since 1.0.0
	 *
	 * @param  callable $callback A callback function.
	 * @return $this
	 */
	public function each_pixel( $callback ) {
		$size = $this->get_size();

		for ( $x = 0; $x < $size['width']; $x++ ) {
			for ( $y = 0; $y < $size['height']; $y++ ) {
				call_user_func( $callback, $this->get_pixel( $x, $y ) );
			}
		}

		return $this;
	}

	/**
	 * Retrieve a gradient map.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $start Starting hex color.
	 * @param  string $end   Ending hex color.
	 * @return array
	 */
	protected function get_map( $start, $end ) {
		$map   = array();
		$start = $this->hex2rgb( $start );
		$end   = $this->hex2rgb( $end );

		// @link http://stackoverflow.com/a/16503313
		for ( $i = 0; $i < 255; $i++ ) {
			$ratio = $i / 255;

			$r = $end[0] * $ratio + $start[0] * ( 1 - $ratio );
			$g = $end[1] * $ratio + $start[1] * ( 1 - $ratio );
			$b = $end[2] * $ratio + $start[2] * ( 1 - $ratio );

			$map[] = array_map( 'floor', array( $r, $g, $b ) );
		}

		return $map;
	}

	/**
	 * Update the current gradient map.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $start Starting hex color.
	 * @param  string $end   Ending hex color.
	 * @return $this
	 */
	protected function update_map( $start, $end ) {
		$this->map = $this->get_map( $start, $end );
		return $this;
	}

	/**
	 * Convert a pixel based on the gradient map.
	 *
	 * @since 1.0.0
	 *
	 * @param TwontoneFX_Image_Pixel_GD $pixel Pixel object.
	 */
	protected function apply_map( $pixel ) {
		$rgb = $this->map[ $pixel->get_luma() ];
		$pixel->set_rgb( $rgb[0], $rgb[1], $rgb[2] );
	}

	/**
	 * Retrieve a pixel object.
	 *
	 * @since 1.0.0
	 *
	 * @param  int $x Position on the x-axis.
	 * @param  int $y Position on the y-axis.
	 * @return TwotoneFX_Image_Pixel_GD
	 */
	protected function get_pixel( $x, $y ) {
		return new TwotoneFX_Image_Pixel_GD( $this->image, $x, $y );
	}

	/**
	 * Convert HEX to RGB.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $color The original color, in 3 or 6-digit hexadecimal form.
	 * @return array Array containing RGB (red, green, and blue) values for the given HEX code, empty array otherwise.
	 */
	protected function hex2rgb( $color ) {
		if ( is_array( $color ) ) {
			return $color;
		}

		$color = trim( $color, '#' );
		if ( strlen( $color ) == 3 ) {
			$r = hexdec( substr( $color, 0, 1 ) . substr( $color, 0, 1 ) );
			$g = hexdec( substr( $color, 1, 1 ) . substr( $color, 1, 1 ) );
			$b = hexdec( substr( $color, 2, 1 ) . substr( $color, 2, 1 ) );
		} else if ( strlen( $color ) == 6 ) {
			$r = hexdec( substr( $color, 0, 2 ) );
			$g = hexdec( substr( $color, 2, 2 ) );
			$b = hexdec( substr( $color, 4, 2 ) );
		} else {
			return array();
		}

		return array( $r, $g, $b );
	}
}
