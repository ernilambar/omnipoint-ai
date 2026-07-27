<?php
/**
 * Provider implementation.
 *
 * @package Nilambar\OmnipointAi
 */

declare( strict_types=1 );

namespace Nilambar\OmnipointAi\Provider;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Nilambar\OmnipointAi\Metadata\OmnipointAiModelMetadataDirectory;
use Nilambar\OmnipointAi\Models\OmnipointAiTextGenerationModel;
use Nilambar\OmnipointAi\Settings;
use WordPress\AiClient\Common\Exception\RuntimeException;
use WordPress\AiClient\Providers\ApiBasedImplementation\AbstractApiProvider;
use WordPress\AiClient\Providers\ApiBasedImplementation\ListModelsApiBasedProviderAvailability;
use WordPress\AiClient\Providers\Contracts\ModelMetadataDirectoryInterface;
use WordPress\AiClient\Providers\Contracts\ProviderAvailabilityInterface;
use WordPress\AiClient\Providers\DTO\ProviderMetadata;
use WordPress\AiClient\Providers\Enums\ProviderTypeEnum;
use WordPress\AiClient\Providers\Http\Enums\RequestAuthenticationMethod;
use WordPress\AiClient\Providers\Models\Contracts\ModelInterface;
use WordPress\AiClient\Providers\Models\DTO\ModelMetadata;

/**
 * Provider implementation.
 *
 * @since 1.0.0
 */
class OmnipointAiProvider extends AbstractApiProvider {

	const DEFAULT_BASE_URL = 'https://api.openai.com/v1';

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.0.0
	 */
	protected static function baseUrl(): string {
		$host = getenv( 'OMNIPOINT_AI_BASE_URL' );
		if ( false !== $host && '' !== $host ) {
			return rtrim( $host, '/' );
		}

		$endpoint = Settings::get_endpoint_url();
		if ( '' !== $endpoint ) {
			return rtrim( $endpoint, '/' );
		}

		return self::DEFAULT_BASE_URL;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.0.0
	 *
	 * @param ModelMetadata    $model_metadata    Model metadata.
	 * @param ProviderMetadata $provider_metadata Provider metadata.
	 * @return ModelInterface
	 * @throws RuntimeException When model capabilities are unsupported.
	 */
	protected static function createModel(
		ModelMetadata $model_metadata,
		ProviderMetadata $provider_metadata
	): ModelInterface {
		$capabilities = $model_metadata->getSupportedCapabilities();

		foreach ( $capabilities as $capability ) {
			if ( $capability->isTextGeneration() ) {
				return new OmnipointAiTextGenerationModel( $model_metadata, $provider_metadata );
			}
		}

		throw new RuntimeException(
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			'Unsupported model capabilities: ' . implode( ', ', $capabilities )
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.0.0
	 */
	protected static function createProviderMetadata(): ProviderMetadata {
		return new ProviderMetadata(
			'omnipoint_ai',
			'Omnipoint AI',
			ProviderTypeEnum::cloud(),
			'',
			RequestAuthenticationMethod::apiKey(),
			__( 'Text generation with any OpenAI-compatible API endpoint.', 'omnipoint-ai' ),
			OMNIPOINT_AI_PLUGIN_DIR . 'assets/images/omnipoint-ai.svg'
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.0.0
	 */
	protected static function createProviderAvailability(): ProviderAvailabilityInterface {
		return new ListModelsApiBasedProviderAvailability( static::modelMetadataDirectory() );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.0.0
	 */
	protected static function createModelMetadataDirectory(): ModelMetadataDirectoryInterface {
		return new OmnipointAiModelMetadataDirectory();
	}
}
