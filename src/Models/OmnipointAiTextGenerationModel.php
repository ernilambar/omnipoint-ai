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
use WordPress\AiClient\Providers\Http\DTO\Request;
use WordPress\AiClient\Providers\Http\Enums\HttpMethodEnum;
use WordPress\AiClient\Providers\OpenAiCompatibleImplementation\AbstractOpenAiCompatibleTextGenerationModel;

/**
 * Text generation model.
 *
 * @since 1.0.0
 */
class OmnipointAiTextGenerationModel extends AbstractOpenAiCompatibleTextGenerationModel {

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
