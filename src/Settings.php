<?php
/**
 * Settings class.
 *
 * @package Nilambar\OmnipointAi
 */

declare( strict_types=1 );

namespace Nilambar\OmnipointAi;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Exception;
use Nilambar\OmnipointAi\Provider\OmnipointAiProvider;
use WordPress\AiClient\AiClient;

/**
 * Settings class.
 *
 * @since 1.0.0
 */
class Settings {

	const ENDPOINT_OPTION_NAME = 'omnipoint_ai_endpoint_url';
	const MODEL_OPTION_NAME    = 'omnipoint_ai_default_text_model';
	const AJAX_ACTION          = 'omnipoint_ai_get_models';
	const NONCE_KEY            = 'omnipoint_ai_get_models';
	const DEFAULT_ENDPOINT_URL = 'https://api.openai.com/v1';

	/**
	 * Initializes settings hooks.
	 *
	 * @since 1.0.0
	 */
	public function init(): void {
		add_action( 'admin_menu', [ $this, 'add_options_page' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'wp_ajax_' . self::AJAX_ACTION, [ $this, 'ajax_get_models' ] );
	}

	/**
	 * Registers the options page.
	 *
	 * @since 1.0.0
	 */
	public function add_options_page(): void {
		add_options_page(
			_x( 'Omnipoint AI Settings', 'page title', 'omnipoint-ai' ),
			_x( 'Omnipoint AI', 'menu title', 'omnipoint-ai' ),
			'manage_options',
			'omnipoint-ai',
			[ $this, 'render_settings_page' ]
		);
	}

	/**
	 * Registers settings, sections, and fields.
	 *
	 * @since 1.0.0
	 */
	public function register_settings(): void {
		register_setting(
			'omnipoint_ai',
			self::ENDPOINT_OPTION_NAME,
			[
				'type'              => 'string',
				'sanitize_callback' => [ $this, 'sanitize_endpoint_url' ],
				'default'           => self::DEFAULT_ENDPOINT_URL,
			]
		);

		register_setting(
			'omnipoint_ai',
			self::MODEL_OPTION_NAME,
			[
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			]
		);

		add_settings_section(
			'omnipoint_ai_general',
			'',
			'__return_false',
			'omnipoint-ai'
		);

		add_settings_field(
			'endpoint_url',
			__( 'API Endpoint URL', 'omnipoint-ai' ),
			[ $this, 'render_endpoint_url_field' ],
			'omnipoint-ai',
			'omnipoint_ai_general'
		);

		add_settings_field(
			'default_text_model',
			__( 'Default Text Model', 'omnipoint-ai' ),
			[ $this, 'render_default_model_field' ],
			'omnipoint-ai',
			'omnipoint_ai_general'
		);
	}

	/**
	 * Sanitizes the endpoint URL.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Raw value.
	 * @return string Sanitized URL.
	 */
	public function sanitize_endpoint_url( $value ): string {
		$url = trim( (string) $value );

		if ( '' === $url || ! preg_match( '#^https?://#i', $url ) ) {
			return self::DEFAULT_ENDPOINT_URL;
		}

		return rtrim( $url, '/' );
	}

	/**
	 * Enqueues scripts on the settings page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook_suffix Current admin page hook suffix.
	 */
	public function enqueue_scripts( string $hook_suffix ): void {
		if ( 'settings_page_omnipoint-ai' !== $hook_suffix ) {
			return;
		}

		wp_enqueue_script(
			'omnipoint-ai-settings',
			plugin_dir_url( OMNIPOINT_AI_BASE_FILEPATH ) . 'assets/js/settings.js',
			[],
			OMNIPOINT_AI_VERSION,
			true
		);

		wp_localize_script(
			'omnipoint-ai-settings',
			'omnipointAiSettings',
			[
				'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
				'nonce'           => wp_create_nonce( self::NONCE_KEY ),
				'currentModel'    => get_option( self::MODEL_OPTION_NAME, '' ),
				'noOverrideLabel' => __( '&mdash; Default &mdash;', 'omnipoint-ai' ),
				'selectId'        => self::MODEL_OPTION_NAME,
				'statusId'        => 'omnipoint-ai-status',
				'errorLabel'      => __( 'Could not connect to the endpoint.', 'omnipoint-ai' ),
			]
		);
	}

	/**
	 * Renders the API Endpoint URL field.
	 *
	 * @since 1.0.0
	 */
	public function render_endpoint_url_field(): void {
		$value = get_option( self::ENDPOINT_OPTION_NAME, self::DEFAULT_ENDPOINT_URL );
		?>
		<input
			type="url"
			name="<?php echo esc_attr( self::ENDPOINT_OPTION_NAME ); ?>"
			id="<?php echo esc_attr( self::ENDPOINT_OPTION_NAME ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
			class="regular-text"
			placeholder="<?php echo esc_attr( self::DEFAULT_ENDPOINT_URL ); ?>"
		/>
		<?php
	}

	/**
	 * Renders the Default Text Model select field.
	 *
	 * @since 1.0.0
	 */
	public function render_default_model_field(): void {
		?>
		<select
			name="<?php echo esc_attr( self::MODEL_OPTION_NAME ); ?>"
			id="<?php echo esc_attr( self::MODEL_OPTION_NAME ); ?>"
			disabled
		>
			<option value=""><?php esc_html_e( 'Loading…', 'omnipoint-ai' ); ?></option>
		</select>
		<p id="omnipoint-ai-status" class="description"></p>
		<?php
	}

	/**
	 * Renders the settings page.
	 *
	 * @since 1.0.0
	 */
	public function render_settings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'omnipoint_ai' );
				do_settings_sections( 'omnipoint-ai' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Returns the configured API endpoint URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string The endpoint URL.
	 */
	public static function get_endpoint_url(): string {
		return (string) get_option( self::ENDPOINT_OPTION_NAME, self::DEFAULT_ENDPOINT_URL );
	}

	/**
	 * AJAX handler: returns available models for the configured endpoint.
	 *
	 * @since 1.0.0
	 */
	public function ajax_get_models(): void {
		check_ajax_referer( self::NONCE_KEY, 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Insufficient permissions.', 'omnipoint-ai' ), 403 );
		}

		if ( ! class_exists( AiClient::class ) ) {
			wp_send_json_error( __( 'AI Client is not available.', 'omnipoint-ai' ) );
		}

		$registry = AiClient::defaultRegistry();
		if ( ! $registry->hasProvider( OmnipointAiProvider::class ) ) {
			wp_send_json_error( [ 'code' => 'not_configured' ] );
		}

		try {
			$directory       = OmnipointAiProvider::modelMetadataDirectory();
			$models_metadata = $directory->listModelMetadata();

			$model_ids = array_map(
				static function ( $metadata ) {
					return $metadata->getId();
				},
				$models_metadata
			);

			$count = count( $model_ids );

			wp_send_json_success(
				[
					'models'  => $model_ids,
					'message' => sprintf(
						/* translators: %d: number of models. */
						_n( '%d model loaded.', '%d models loaded.', $count, 'omnipoint-ai' ),
						$count
					),
				]
			);
		} catch ( Exception $e ) {
			wp_send_json_error( [ 'code' => 'api_error' ] );
		}
	}
}
