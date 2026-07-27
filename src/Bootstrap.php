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
}
