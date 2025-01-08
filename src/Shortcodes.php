<?php
/**
 * Shortcodes Registration Manager
 *
 * @package     ArrayPress\WP\Register
 * @copyright   Copyright (c) 2024, ArrayPress Limited
 * @license     GPL2+
 * @version     1.0.0
 */

declare( strict_types=1 );

namespace ArrayPress\WP\Register;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Class Shortcodes
 *
 * Manages WordPress shortcodes registration and management.
 *
 * @since 1.0.0
 */
class Shortcodes {

	/**
	 * Instance of this class.
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Collection of shortcodes to be registered
	 *
	 * @var array
	 */
	private array $shortcodes = [];

	/**
	 * Option prefix for storing shortcode data
	 *
	 * @var string
	 */
	private string $prefix = '';

	/**
	 * Debug mode status
	 *
	 * @var bool
	 */
	private bool $debug = false;

	/**
	 * Get instance of this class.
	 *
	 * @return self Instance of this class.
	 */
	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 *

	 */
	private function __construct() {
		$this->debug = defined( 'WP_DEBUG' ) && WP_DEBUG;
	}

	/**
	 * Set the prefix
	 *
	 * @param string $prefix The prefix to use
	 *
	 * @return self
	 */
	public function set_prefix( string $prefix ): self {
		$this->prefix = $prefix;

		return $this;
	}

	/**
	 * Add shortcodes to be registered
	 *
	 * @param array $shortcodes Array of shortcodes
	 *
	 * @return self
	 */
	public function add_shortcodes( array $shortcodes ): self {
		foreach ( $shortcodes as $tag => $config ) {
			$this->add_shortcode( $tag, $config );
		}

		return $this;
	}

	/**
	 * Add a single shortcode
	 *
	 * @param string $tag    The shortcode tag
	 * @param array  $config Shortcode configuration
	 *
	 * @return self
	 */
	public function add_shortcode( string $tag, array $config ): self {
		if ( ! $this->is_valid_tag( $tag ) ) {
			$this->log( sprintf( 'Invalid shortcode tag: %s', $tag ) );

			return $this;
		}

		if ( ! isset( $config['callback'] ) || ! is_callable( $config['callback'] ) ) {
			$this->log( sprintf( 'Invalid callback for shortcode: %s', $tag ) );

			return $this;
		}

		$this->shortcodes[ $tag ] = wp_parse_args( $config, [
			'callback'    => null,
			'attributes'  => [],
			'description' => ''
		] );

		return $this;
	}

	/**
	 * Install shortcodes
	 *
	 * @return bool
	 */
	public function install(): bool {
		if ( empty( $this->shortcodes ) ) {
			return false;
		}

		foreach ( $this->shortcodes as $tag => $config ) {
			add_shortcode( $this->maybe_prefix_tag( $tag ), function ( $atts = [], $content = null ) use ( $tag ) {
				$config = $this->shortcodes[ $tag ];
				$atts   = shortcode_atts( $config['attributes'], $atts, $tag );

				return call_user_func( $config['callback'], $atts, $content, $tag );
			} );

			$this->log( sprintf( 'Registered shortcode: %s', $tag ) );
		}

		$this->store_installation_flag();

		return true;
	}

	/**
	 * Uninstall shortcodes
	 *
	 * @return bool
	 */
	public function uninstall(): bool {
		foreach ( array_keys( $this->shortcodes ) as $tag ) {
			remove_shortcode( $this->maybe_prefix_tag( $tag ) );
			$this->log( sprintf( 'Removed shortcode: %s', $tag ) );
		}

		$this->delete_installation_flag();

		return true;
	}

	/**
	 * Get all registered shortcodes
	 *
	 * @return array
	 */
	public function get_shortcodes(): array {
		return $this->shortcodes;
	}

	/**
	 * Store installation flag
	 *
	 * @return void
	 */
	protected function store_installation_flag(): void {
		update_option( $this->get_option_key( 'shortcodes_installed' ), true );
	}

	/**
	 * Delete installation flag
	 *
	 * @return void
	 */
	protected function delete_installation_flag(): void {
		delete_option( $this->get_option_key( 'shortcodes_installed' ) );
	}

	/**
	 * Validate a shortcode tag
	 *
	 * @param string $tag Shortcode tag
	 *
	 * @return bool Whether the tag is valid
	 */
	protected function is_valid_tag( string $tag ): bool {
		return (bool) preg_match( '/^[a-z0-9_-]+$/', $tag );
	}

	/**
	 * Maybe prefix shortcode tag
	 *
	 * @param string $tag Shortcode tag
	 *
	 * @return string Possibly prefixed tag
	 */
	protected function maybe_prefix_tag( string $tag ): string {
		return empty( $this->prefix ) ? $tag : "{$this->prefix}_{$tag}";
	}

	/**
	 * Get prefixed option key
	 *
	 * @param string $key Option key
	 *
	 * @return string
	 */
	protected function get_option_key( string $key ): string {
		return empty( $this->prefix ) ? $key : "{$this->prefix}_{$key}";
	}

	/**
	 * Log debug message
	 *
	 * @param string $message Message to log
	 * @param array  $context Optional context
	 *
	 * @return void
	 */
	protected function log( string $message, array $context = [] ): void {
		if ( $this->debug ) {
			$prefix = $this->prefix ? "[{$this->prefix}] " : '';
			error_log( sprintf(
				'%sShortcodes: %s %s',
				$prefix,
				$message,
				$context ? json_encode( $context ) : ''
			) );
		}
	}

	/**
	 * Helper method to register shortcodes
	 *
	 * @param array  $shortcodes Array of shortcodes to register
	 * @param string $prefix     Optional prefix
	 *
	 * @return bool
	 */
	public static function register( array $shortcodes = [], string $prefix = '' ): bool {
		return self::instance()
		           ->set_prefix( $prefix )
		           ->add_shortcodes( $shortcodes )
		           ->install();
	}

	/**
	 * Helper method to unregister shortcodes
	 *
	 * @param array  $shortcodes Array of shortcodes to unregister
	 * @param string $prefix     Optional prefix
	 *
	 * @return bool
	 */
	public static function unregister( array $shortcodes = [], string $prefix = '' ): bool {
		return self::instance()
		           ->set_prefix( $prefix )
		           ->add_shortcodes( $shortcodes )
		           ->uninstall();
	}

}