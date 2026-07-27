<?php
/**
 * Text generation model.
 *
 * @package Nilambar\OmnipointAi
 */

declare( strict_types=1 );

namespace Nilambar\OmnipointAi\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Nilambar\OmnipointAi\Provider\OmnipointAiProvider;
use Nilambar\OmnipointAi\Settings;
use WordPress\AiClient\Providers\Http\DTO\Request;
use WordPress\AiClient\Providers\Http\DTO\Response;
use WordPress\AiClient\Providers\Http\Enums\HttpMethodEnum;
use WordPress\AiClient\Providers\Models\DTO\ModelMetadata;
use WordPress\AiClient\Providers\OpenAiCompatibleImplementation\AbstractOpenAiCompatibleTextGenerationModel;
use WordPress\AiClient\Results\DTO\GenerativeAiResult;

/**
 * Text generation model.
 *
 * @since 1.0.0
 */
class OmnipointAiTextGenerationModel extends AbstractOpenAiCompatibleTextGenerationModel {

	/**
	 * The effective model ID when a settings override is active, null otherwise.
	 *
	 * @var string|null
	 */
	private ?string $effective_model_id = null;

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.0.0
	 *
	 * @param array<\WordPress\AiClient\Messages\DTO\Message> $prompt The prompt messages.
	 * @return array<string, mixed>
	 */
	protected function prepareGenerateTextParams( array $prompt ): array {
		$params = parent::prepareGenerateTextParams( $prompt );

		$default_model = get_option( Settings::MODEL_OPTION_NAME, '' );
		if ( '' !== $default_model && empty( $params['model'] ) ) {
			$params['model']          = $default_model;
			$this->effective_model_id = $default_model;
		}

		return $params;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.0.0
	 *
	 * @param Response $response The HTTP response.
	 * @return GenerativeAiResult
	 */
	protected function parseResponseToGenerativeAiResult( Response $response ): GenerativeAiResult {
		$result = parent::parseResponseToGenerativeAiResult( $response );

		if ( null === $this->effective_model_id ) {
			return $result;
		}

		$additional      = $result->getAdditionalData();
		$actual_model_id = $additional['model'] ?? $this->effective_model_id;

		$original = $this->metadata();
		$metadata = new ModelMetadata(
			$actual_model_id,
			$actual_model_id,
			$original->getSupportedCapabilities(),
			$original->getSupportedOptions()
		);

		return new GenerativeAiResult(
			$result->getId(),
			$result->getCandidates(),
			$result->getTokenUsage(),
			$result->getProviderMetadata(),
			$metadata,
			$result->getAdditionalData()
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.0.0
	 *
	 * @param HttpMethodEnum                     $method  HTTP method.
	 * @param string                             $path    Endpoint path.
	 * @param array<string, string|list<string>> $headers Request headers.
	 * @param string|array<string, mixed>|null   $data    Request data.
	 * @return Request
	 */
	protected function createRequest(
		HttpMethodEnum $method,
		string $path,
		array $headers = [],
		$data = null
	): Request {
		return new Request(
			$method,
			OmnipointAiProvider::url( $path ),
			$headers,
			$data,
			$this->getRequestOptions()
		);
	}
}
