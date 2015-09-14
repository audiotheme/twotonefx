<?php
/**
 * GD image pixel.
 *
 * @package   TwotoneFX
 * @since     1.0.0
 * @copyright Copyright (c) 2015 AudioTheme, LLC
 * @license   GPL-2.0+
 */

/**
 * Class for a single pixel in an image.
 *
 * @package TwotoneFX
 * @since   1.0.0
 */
class TwotoneFX_Image_Pixel_GD {
	/**
	 * GD image resource.
	 *
	 * @since 1.0.0
	 * @var resource
	 */
	protected $image;

	/**
	 * Position on the x-axis.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	protected $x;

	/**
	 * Position on the y-axis.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	protected $y;

	/**
	 * Class constructor method.
	 *
	 * @since 1.0.0
	 *
	 * @param resource $image GD image resource.
	 * @param int      $x     Position of the pixel on the x-axis.
	 * @param int      $y     Position of the pixel on the y-axis.
	 */
	public function __construct( $image, $x, $y ) {
		$this->image = $image;
		$this->x = $x;
		$this->y = $y;
	}

	/**
	 * Retrieve the pixel's RGB values.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_rgba() {
		$rgb = imagecolorat( $this->image, $this->x, $this->y );
		return array_values( imagecolorsforindex( $this->image, $rgb ) );
	}

	/**
	 * Set the pixel's RGB values.
	 *
	 * @since 1.0.0
	 *
	 * @param int $r Red color index (0 to 255).
	 * @param int $g Green color index (0 to 255).
	 * @param int $b Blue color index (0 to 255).
	 * @return $this
	 */
	public function set_rgb( $r, $g, $b ) {
		$color_id = imagecolorallocate( $this->image, $r, $g, $b );
		imagesetpixel( $this->image, $this->x, $this->y, $color_id );
		return $this;
	}

	/**
	 * Retrieve the pixel's luma.
	 *
	 * @since 1.0.0
	 *
	 * @link https://en.wikipedia.org/wiki/Luma_%28video%29
	 *
	 * @return int Luma value (0 to 255).
	 */
	public function get_luma() {
		$rgba = $this->get_rgba();
		$luma = 0.2126 * $rgba[0] + 0.7152 * $rgba[1] + 0.0722 * $rgba[2];
		return $luma;
	}

	/**
	 * Convert the pixel to grayscale.
	 *
	 * @since 1.0.0
	 *
	 * @return $this
	 */
	protected function to_grayscale() {
		$luma     = $this->get_luma();
		$color_id = imagecolorallocate( $this->image, $luma, $luma, $luma );
		imagesetpixel( $this->image, $this->x, $this->y, $color_id );
		return $this;
	}
}
