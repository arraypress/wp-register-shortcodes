<?php
/**
 * Shortcodes Registration Helper Functions
 *
 * @package     ArrayPress\WP\Register
 * @copyright   Copyright (c) 2024, ArrayPress Limited
 * @license     GPL2+
 * @version     1.0.0
 * @author      David Sherlock
 */

declare( strict_types=1 );

use ArrayPress\WP\Register\Shortcodes;

if ( ! function_exists( 'register_shortcodes' ) ):
	/**
	 * Helper function to register WordPress shortcodes.
	 *
	 * Example usage:
	 * ```php
	 * $shortcodes = [
	 *     'my_shortcode' => [
	 *         'callback' => 'my_shortcode_callback',
	 *         'attributes' => [
	 *             'title' => 'Default Title',
	 *             'size' => 'large'
	 *         ],
	 *         'description' => 'Displays something cool'
	 *     ]
	 * ];
	 *
	 * // Register with a prefix
	 * register_shortcodes( $shortcodes, 'my_plugin' );
	 * ```
	 *
	 * @since 1.0.0
	 *
	 * @param array  $shortcodes Array of shortcodes to register
	 * @param string $prefix     Optional prefix for shortcode tags
	 *
	 * @return bool True on success, false on failure
	 */
	function register_shortcodes( array $shortcodes, string $prefix = '' ): bool {
		return Shortcodes::register( $shortcodes, $prefix );
	}
endif;

if ( ! function_exists( 'unregister_shortcodes' ) ):
	/**
	 * Helper function to unregister WordPress shortcodes.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $shortcodes Array of shortcodes to unregister
	 * @param string $prefix     Optional prefix used during registration
	 *
	 * @return bool True on success, false on failure
	 */
	function unregister_shortcodes( array $shortcodes, string $prefix = '' ): bool {
		return Shortcodes::unregister( $shortcodes, $prefix );
	}
endif;