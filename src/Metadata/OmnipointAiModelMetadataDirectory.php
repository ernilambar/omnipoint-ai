<?php
/**
 * Model metadata directory.
 *
 * @package Nilambar\OmnipointAi
 */

declare( strict_types=1 );

namespace Nilambar\OmnipointAi\Metadata;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Nilambar\OmnipointAi\Provider\OmnipointAiProvider;
use WordPress\AiClient\Messages\Enums\ModalityEnum;
use WordPress\AiClient\Providers\Http\DTO\Request;
use WordPress\AiClient\Providers\Http\DTO\Response;
use WordPress\AiClient\Providers\Http\Enums\HttpMethodEnum;
use WordPress\AiClient\Providers\Http\Exception\ResponseException;
use WordPress\AiClient\Providers\Models\DTO\ModelMetadata;
use WordPress\AiClient\Providers\Models\DTO\SupportedOption;
use WordPress\AiClient\Providers\Models\Enums\CapabilityEnum;
use WordPress\AiClient\Providers\Models\Enums\OptionEnum;
use WordPress\AiClient\Providers\OpenAiCompatibleImplementation\AbstractOpenAiCompatibleModelMetadataDirectory;

/**
 * Model metadata directory.
 *
 * @since 1.0.0
 *
 * @phpstan-type ModelEntry array{id?: string}
 * @phpstan-type ModelsResponseData array{
 *     data?: list<ModelEntry>,
 *     models?: list<ModelEntry>
 * }
 */
class OmnipointAiModelMetadataDirectory extends AbstractOpenAiCompatibleModelMetadataDirectory {

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.0.0
	 *
	 * @param HttpMethodEnum                     $method  The HTTP method.
	 * @param string                             $path    The API endpoint path, relative to the base URI.
	 * @param array<string, string|list<string>> $headers The request headers.
	 * @param string|array<string, mixed>|null   $data    The request data.
	 * @return Request The request object.
	 */
	protected function createRequest( HttpMethodEnum $method, string $path, array $headers = [], $data = null ): Request {
		return new Request(
			$method,
			OmnipointAiProvider::url( $path ),
			$headers,
			$data
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.0.0
	 *
	 * @param Response $response The response from the API endpoint to list models.
	 * @return list<ModelMetadata>
	 * @throws ResponseException When the response is missing model data.
	 */
	protected function parseResponseToModelMetadataList( Response $response ): array {
		/**
		 * Decoded models response.
		 *
		 * @var ModelsResponseData $response_data
		 */
		$response_data = $response->getData();

		$raw_models = [];
		if ( isset( $response_data['data'] ) && is_array( $response_data['data'] ) ) {
			$raw_models = $response_data['data'];
		} elseif ( isset( $response_data['models'] ) && is_array( $response_data['models'] ) ) {
			$raw_models = $response_data['models'];
		}

		if ( [] === $raw_models ) {
			throw ResponseException::fromMissingData( 'Omnipoint AI', 'data' );
		}

		$options = [
			new SupportedOption( OptionEnum::systemInstruction() ),
			new SupportedOption( OptionEnum::candidateCount() ),
			new SupportedOption( OptionEnum::maxTokens() ),
			new SupportedOption( OptionEnum::temperature() ),
			new SupportedOption( OptionEnum::topP() ),
			new SupportedOption( OptionEnum::stopSequences() ),
			new SupportedOption( OptionEnum::frequencyPenalty() ),
			new SupportedOption( OptionEnum::presencePenalty() ),
			new SupportedOption( OptionEnum::outputMimeType(), [ 'text/plain', 'application/json' ] ),
			new SupportedOption( OptionEnum::outputSchema() ),
			new SupportedOption( OptionEnum::functionDeclarations() ),
			new SupportedOption( OptionEnum::customOptions() ),
			new SupportedOption( OptionEnum::outputModalities(), [ [ ModalityEnum::text() ] ] ),
			new SupportedOption( OptionEnum::inputModalities(), [ [ ModalityEnum::text() ] ] ),
		];

		$models = [];

		foreach ( $raw_models as $model_entry ) {
			if ( ! isset( $model_entry['id'] ) || '' === $model_entry['id'] ) {
				continue;
			}

			$model_id = $model_entry['id'];

			// Skip embedding-only models, which cannot be used for text generation.
			if ( false !== stripos( $model_id, 'embed' ) ) {
				continue;
			}

			$models[ $model_id ] = new ModelMetadata(
				$model_id,
				$model_id,
				[
					CapabilityEnum::textGeneration(),
					CapabilityEnum::chatHistory(),
				],
				$options
			);
		}

		ksort( $models );

		return array_values( $models );
	}
}
