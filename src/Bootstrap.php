<?php
/**
 * Bootstrap class.
 *
 * @package Nilambar\OmnipointAi
 */

declare( strict_types=1 );

namespace Nilambar\OmnipointAi;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Nilambar\OmnipointAi\Provider\OmnipointAiProvider;
use WordPress\AiClient\AiClient;
use WordPress\AiClient\Providers\Http\DTO\ApiKeyRequestAuthentication;

/**
 * Bootstrap class.
 *
 * @since 1.0.0
 */
class Bootstrap {

	/**
	 * Initializes the plugin.
	 *
	 * @since 1.0.0
	 */
	public function init(): void {
		add_action( 'init', [ $this, 'register_provider' ], 5 );
		add_action( 'init', [ $this, 'register_fallback_auth' ], 15 );
		add_filter( 'plugin_action_links_' . OMNIPOINT_AI_BASE_FILENAME, [ $this, 'plugin_action_links' ] );
		add_filter( 'http_request_host_is_external', [ $this, 'allow_external_requests' ], 10, 3 );
		add_filter( 'http_allowed_safe_ports', [ $this, 'allow_ports' ] );
		add_filter( 'http_request_args', [ $this, 'extend_timeout' ], 10, 2 );

		( new Settings() )->init();
	}

	/**
	 * Adds a Settings link to the plugin row.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string> $links Existing action links.
	 * @return array<string> Modified action links.
	 */
	public function plugin_action_links( array $links ): array {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			admin_url( 'options-general.php?page=omnipoint-ai' ),
			esc_html__( 'Settings', 'omnipoint-ai' )
		);

		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Registers the provider with the AI Client.
	 *
	 * @since 1.0.0
	 */
	public function register_provider(): void {
		if ( ! class_exists( AiClient::class ) ) {
			return;
		}

		$registry = AiClient::defaultRegistry();

		if ( $registry->hasProvider( OmnipointAiProvider::class ) ) {
			return;
		}

		$registry->registerProvider( OmnipointAiProvider::class );
	}

	/**
	 * Registers fallback authentication for the provider.
	 *
	 * @since 1.0.0
	 */
	public function register_fallback_auth(): void {
		if ( ! class_exists( AiClient::class ) ) {
			return;
		}

		$registry = AiClient::defaultRegistry();

		if ( ! $registry->hasProvider( 'omnipoint_ai' ) ) {
			return;
		}

		$auth = $registry->getProviderRequestAuthentication( 'omnipoint_ai' );
		if ( null !== $auth ) {
			return;
		}

		$api_key = getenv( 'OMNIPOINT_AI_API_KEY' );

		$registry->setProviderRequestAuthentication(
			'omnipoint_ai',
			new ApiKeyRequestAuthentication( false !== $api_key ? $api_key : '' )
		);
	}

	/**
	 * Allows external requests to the configured endpoint.
	 *
	 * @since 1.0.0
	 *
	 * @param bool   $external Whether the request is external.
	 * @param string $host     The host of the request.
	 * @param string $url      The URL of the request.
	 * @return bool Whether the request is allowed.
	 */
	public function allow_external_requests( $external, $host, $url ): bool {
		if ( strpos( $url, OmnipointAiProvider::url() ) !== false ) {
			return true;
		}

		return $external;
	}

	/**
	 * Allows the configured endpoint's port.
	 *
	 * @since 1.0.0
	 *
	 * @param array<int> $ports The ports.
	 * @return array<int> The allowed ports.
	 */
	public function allow_ports( $ports ): array {
		$port = wp_parse_url( OmnipointAiProvider::url(), PHP_URL_PORT );

		if ( ! $port ) {
			return $ports;
		}

		return array_merge( $ports, [ $port ] );
	}

	/**
	 * Extends the timeout for requests to the configured endpoint.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, mixed> $args HTTP request args.
	 * @param string               $url  Request URL.
	 * @return array<string, mixed> Filtered HTTP request args.
	 */
	public function extend_timeout( array $args, string $url ): array {
		if ( strpos( $url, OmnipointAiProvider::url() ) === false ) {
			return $args;
		}

		$existing_timeout = isset( $args['timeout'] ) && is_numeric( $args['timeout'] )
			? (float) $args['timeout']
			: 0.0;

		if ( $existing_timeout < 180.0 ) {
			$args['timeout'] = 180.0;
		}

		return $args;
	}
}
