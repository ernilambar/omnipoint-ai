<?php
/**
 * Plugin Name:       Omnipoint AI
 * Plugin URI:        https://github.com/ernilambar/omnipoint-ai
 * Description:       AI provider for OpenAI-compatible endpoints.
 * Requires at least: 7.0
 * Requires PHP:      7.4
 * Version: 1.0.0
 * Author:            Nilambar Sharma
 * Author URI:        https://nilambar.net
 * License:           GPL-2.0-or-later
 * License URI:       https://spdx.org/licenses/GPL-2.0-or-later.html
 * Text Domain:       omnipoint-ai
 * Domain Path:       /languages
 *
 * @package Nilambar\OmnipointAi
 */

declare( strict_types=1 );

namespace Nilambar\OmnipointAi;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'OMNIPOINT_AI_VERSION', '1.0.0' );
define( 'OMNIPOINT_AI_BASE_NAME', basename( __DIR__ ) );
define( 'OMNIPOINT_AI_BASE_FILEPATH', __FILE__ );
define( 'OMNIPOINT_AI_BASE_FILENAME', plugin_basename( __FILE__ ) );
define( 'OMNIPOINT_AI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'OMNIPOINT_AI_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

add_action(
	'plugins_loaded',
	static function () {
		if ( ! file_exists( OMNIPOINT_AI_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
			return;
		}

		require_once OMNIPOINT_AI_PLUGIN_DIR . 'vendor/autoload.php';

		( new Bootstrap() )->init();
	}
);
