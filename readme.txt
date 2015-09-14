=== Twotone FX ===
Contributors: audiotheme, bradyvercher, brodyvercher
Tags: duotone, photo, filter, image, media, fx
Requires at least: 4.3
Tested up to: 4.3
Stable tag: trunk
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Apply a duotone effect to photos in the media library.

== Description ==



= Support Policy =

We'll do our best to keep this plugin up to date, fix bugs and implement features when possible, but technical support will only be provided for active AudioTheme customers. If you enjoy this plugin and would like to support its development, you can:

* [Check out AudioTheme](https://audiotheme.com/?utm_source=wordpress.org&utm_medium=link&utm_content=twotonefx-readme&utm_campaign=plugins) and tell your friends!
* Help out on the [support forums](https://wordpress.org/support/plugin/twotonefx).
* Consider [contributing on GitHub](https://github.com/audiotheme/twotonefx).
* [Leave a review](https://wordpress.org/support/view/plugin-reviews/twotonefx#postform) and let everyone know how much you love it.
* [Follow @AudioTheme](https://twitter.com/AudioTheme) on Twitter.


== Installation ==

Install like any other plugin. [Refer to the Codex](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins) if you have any questions.


= Accessing the Image Editor =

Visit the Media Library at <em>Media &rarr; Library</em> in your admin panel.

If you're using the default <strong>Grid Mode</strong>:

1. Click an image to open the <strong>Attachment Details</strong> popup
2. Click the <strong>Edit Image</strong> button below the image
3. Scroll the right sidebar down to the <strong>Twotone Effect</strong> section

If you're using the <strong>List Mode</strong>:

1. Click an image to open the <strong>Edit Attachment</strong> screen
2. Click the <strong>Edit Image</strong> button below the image
3. Find the <strong>Twotone Effect</strong> section

<em>It's also possible to access the image editor in the Media Manager popup when editing any post or page.</em>

= Applying the Twotone Effect =

The <strong>Twotone Effect</strong> section in the image editor consists of two color pickers and a button for applying the filter to the image to preview it before saving your changes.

The <strong>Starting Color</strong> replaces shadows in the image, so choosing a dark color usually produces the best results.

The <strong>Ending Color</strong> replaces highlights in the image. Choose a lighter color that contrasts with the starting color for the best results.

After choosing colors, preview the image by clicking the <strong>Apply</strong> button. If you don't like the way the effect turned out, you can either use the <strong>Undo</strong> button on the image editor toolbar, or select different colors and apply them.

Your changes won't be made permanent until you click the <strong>Save</strong> button.


== Screenshots ==

1. Image editor with a section for adding a duotone effect to a photo.


== Notes ==

= How It works =

Behind the scenes, a gradient map is generated representing values between the starting and ending colors selected for the filter. The photo is then converted to grayscale and each pixel is mapped to one of the colors in the gradient map based on its brightness.

Two methods for editing images are available to support different server environments:

* <em>ImageMagick</em> is the preferred method and provides the best performance.
* A <em>GD Graphics Library</em> implementation is provided as a fallback, however, it can be resource intensive and may exhaust memory on some shared hosts, especially when processing large photos.


== Changelog ==

= 1.0.0 =
* Initial release.
